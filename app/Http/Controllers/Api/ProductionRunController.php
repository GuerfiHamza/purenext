<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinishedGood;
use App\Models\ProductionRun;
use App\Models\ProductionRunIngredient;
use App\Models\RawMaterial;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class ProductionRunController extends Controller
{
    public function index(): JsonResponse
    {
        $runs = ProductionRun::with(['recipe.brand', 'recipe.packagingOptions', 'packaging', 'operator'])
            ->latest()
            ->paginate(20);

        return response()->json($runs);
    }

    public function show(ProductionRun $productionRun): JsonResponse
    {
        return response()->json($productionRun->load(['recipe.brand', 'recipe.packagingOptions', 'packaging', 'operator', 'ingredients.rawMaterial', 'finishedGood']));
    }

    /**
     * Générer un numéro de lot automatique.
     * Format : LOT-YYYYMMDD-XXX  (ex: LOT-20260414-001)
     */
    public static function generateLotNumber(): string
    {
        $today = now()->format('Ymd');
        $count = ProductionRun::whereDate('created_at', now()->toDateString())->count() + 1;
        return 'LOT-' . $today . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Retourner un numéro de lot auto pour pré-remplir le formulaire.
     */
    public function suggestLotNumber(): JsonResponse
    {
        return response()->json(['lot_number' => self::generateLotNumber()]);
    }

    /**
     * Lancement réel de la production (décrémente le stock MP).
     */
    public function launch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'recipe_packaging_id' => 'required|exists:recipe_packaging,id',
            'input_qty_kg' => 'required|numeric|min:0.001',
            'output_packets_estimated' => 'required|integer|min:1',
            'lot_number' => 'nullable|string|max:50|unique:production_runs,lot_number',
            'notes' => 'nullable|string',
        ]);

        $run = DB::transaction(function () use ($validated) {
            $recipe = \App\Models\Recipe::with('ingredients.rawMaterial')->find($validated['recipe_id']);
            $packaging = \App\Models\RecipePackaging::find($validated['recipe_packaging_id']);

            // Numéro de lot : fourni par l'utilisateur ou auto-généré
            $lotNumber = $validated['lot_number'] ?? self::generateLotNumber();

            // Batch number interne (préfixe marque, conservé pour compatibilité)
            $prefix = strtoupper(substr($recipe->brand->slug ?? 'PN', 0, 4));
            $batchNumber = $prefix . '-' . now()->format('Y') . '-' . str_pad(ProductionRun::whereYear('created_at', now()->year)->count() + 1, 4, '0', STR_PAD_LEFT);

            $data = [
                'batch_number' => $batchNumber,
                'lot_number' => self::generateLotNumber(),
                'recipe_id' => $validated['recipe_id'],
                'recipe_packaging_id' => $validated['recipe_packaging_id'],
                'operator_id' => auth()->id(),
                'input_qty_kg' => $validated['input_qty_kg'],
                'output_packets_estimated' => $validated['output_packets_estimated'],
                'status' => 'in_progress',
                'started_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ];

            $run = ProductionRun::create($data);

            // Décrémenter stock MP + enregistrer mouvements
            foreach ($recipe->ingredients as $ingredient) {
                $consumed = $ingredient->quantity * $validated['input_qty_kg'];
                $rawMaterial = $ingredient->rawMaterial;
                $stockBefore = $rawMaterial->quantity_in_stock;
                $stockAfter = max(0, $stockBefore - $consumed);

                $rawMaterial->update(['quantity_in_stock' => $stockAfter]);

                ProductionRunIngredient::create([
                    'production_run_id' => $run->id,
                    'raw_material_id' => $rawMaterial->id,
                    'quantity_consumed' => $consumed,
                    'unit' => $ingredient->unit,
                ]);

                StockMovement::create([
                    'movable_type' => 'raw_material',
                    'movable_id' => $rawMaterial->id,
                    'type' => 'out',
                    'quantity' => $consumed,
                    'unit' => $ingredient->unit,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reason' => "Lancement production {$lotNumber}",
                    'source_type' => ProductionRun::class,
                    'source_id' => $run->id,
                    'user_id' => auth()->id(),
                ]);
            }

            return $run;
        });

        NotificationService::create('production_started', '🚀 Nouvelle production lancée — ' . $run->lot_number, "La production {$run->lot_number} a démarré avec {$validated['input_qty_kg']} kg d'ingrédients.", ['lot' => $run->lot_number, 'input_kg' => $validated['input_qty_kg']]);
        NotificationService::sendPush('🚀 Nouvelle production lancée', "La production {$run->lot_number} a démarré avec {$validated['input_qty_kg']} kg d'ingrédients.", ['type' => 'production_started', 'lot' => $run->lot_number]);

        return response()->json($run->load(['recipe.brand', 'packaging', 'ingredients.rawMaterial']), 201);
    }

    /**
     * Clôturer une production.
     */
    public function complete(Request $request, ProductionRun $productionRun): JsonResponse
    {
        $validated = $request->validate([
            'output_packets_actual' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $productionRun) {
            $productionRun->update([
                'output_packets_actual' => $validated['output_packets_actual'],
                'status' => 'completed',
                'finished_at' => now(),
                'notes' => $validated['notes'] ?? $productionRun->notes,
            ]);

            $existingGood = FinishedGood::where('brand_id', $productionRun->recipe->brand_id)
                ->where('packet_size_g', $productionRun->packaging->packet_size_g)
                ->where('product_name', $productionRun->recipe->name . ' ' . $productionRun->packaging->packet_label)
                ->first();

            if ($existingGood) {
                $existingGood->update([
                    'quantity_in_stock' => $existingGood->quantity_in_stock + $validated['output_packets_actual'],
                    'expiry_date' => $validated['expiry_date'] ?? $existingGood->expiry_date,
                ]);
            } else {
                FinishedGood::create([
                    'product_name' => $productionRun->recipe->name . ' ' . $productionRun->packaging->packet_label,
                    'brand_id' => $productionRun->recipe->brand_id,
                    'production_run_id' => $productionRun->id,
                    'batch_number' => $productionRun->lot_number ?? $productionRun->batch_number,
                    'packet_size_g' => $productionRun->packaging->packet_size_g,
                    'packet_label' => $productionRun->packaging->packet_label,
                    'quantity_in_stock' => $validated['output_packets_actual'],
                    'production_date' => now()->toDateString(),
                    'expiry_date' => $validated['expiry_date'] ?? null,
                ]);
            }
        });

        NotificationService::create('production_complete', '✅ Production terminée — ' . $productionRun->lot_number, "La production {$productionRun->lot_number} est clôturée avec {$validated['output_packets_actual']} packets.", ['lot' => $productionRun->lot_number, 'packets' => $validated['output_packets_actual']]);
        NotificationService::sendProductionEmail($productionRun->lot_number ?? $productionRun->batch_number, $validated['output_packets_actual'], $productionRun->output_packets_estimated, $productionRun->recipe->name, $productionRun->operator->name ?? 'Système');
        NotificationService::sendPush('✅ Production terminée', "Lot {$productionRun->lot_number} clôturé avec {$validated['output_packets_actual']} packets.", ['type' => 'production_complete']);

        return response()->json($productionRun->fresh()->load(['recipe', 'packaging', 'finishedGood']));
    }

    public function update(Request $request, ProductionRun $productionRun): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:simulated,in_progress,completed,cancelled',
            'lot_number' => 'sometimes|string|max:50|unique:production_runs,lot_number,' . $productionRun->id,
            'notes' => 'nullable|string',
        ]);

        $productionRun->update($validated);
        return response()->json($productionRun);
    }

    public function destroy(ProductionRun $productionRun): JsonResponse
    {
        $productionRun->update(['status' => 'cancelled']);
        return response()->json(null, 204);
    }
}
