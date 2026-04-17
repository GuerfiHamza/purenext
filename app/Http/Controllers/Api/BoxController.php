<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BoxMovement;
use App\Models\BoxStock;
use App\Models\FinishedGood;
use App\Models\PackagingBox;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoxController extends Controller
{
    // ──────────────────────────────────────────
    // PACKAGING BOXES CRUD
    // ──────────────────────────────────────────

    /**
     * Liste toutes les boites avec stock + produit fini.
     */
    public function index(): JsonResponse
    {
        $boxes = PackagingBox::with(['finishedGood.brand', 'stock'])
            ->where('is_active', true)
            ->get()
            ->map(fn($box) => $this->formatBox($box));

        return response()->json($boxes);
    }

    /**
     * Créer une définition de boite pour un produit fini.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'finished_good_id' => 'required|exists:finished_goods,id',
            'name'             => 'required|string|max:100',
            'units_per_box'    => 'required|integer|min:1',
            'label'            => 'nullable|string|max:50',
            'min_stock_alert'  => 'nullable|integer|min:0',
        ]);

        $box = DB::transaction(function () use ($validated) {
            $box = PackagingBox::create([
                'finished_good_id' => $validated['finished_good_id'],
                'name'             => $validated['name'],
                'units_per_box'    => $validated['units_per_box'],
                'label'            => $validated['label'] ?? null,
                'is_active'        => true,
            ]);

            // Créer le stock à 0
            BoxStock::create([
                'packaging_box_id' => $box->id,
                'quantity_in_stock' => 0,
                'min_stock_alert'   => $validated['min_stock_alert'] ?? 10,
            ]);

            return $box;
        });

        return response()->json(
            $this->formatBox($box->load(['finishedGood.brand', 'stock'])),
            201
        );
    }

    /**
     * Détail d'une boite.
     */
    public function show(PackagingBox $packagingBox): JsonResponse
    {
        return response()->json(
            $this->formatBox($packagingBox->load(['finishedGood.brand', 'stock']))
        );
    }

    /**
     * Modifier une boite (nom, label, units_per_box, min_stock_alert).
     */
    public function update(Request $request, PackagingBox $packagingBox): JsonResponse
    {
        $validated = $request->validate([
            'name'            => 'sometimes|string|max:100',
            'units_per_box'   => 'sometimes|integer|min:1',
            'label'           => 'nullable|string|max:50',
            'min_stock_alert' => 'nullable|integer|min:0',
            'is_active'       => 'sometimes|boolean',
        ]);

        DB::transaction(function () use ($validated, $packagingBox) {
            $packagingBox->update(array_filter([
                'name'          => $validated['name'] ?? null,
                'units_per_box' => $validated['units_per_box'] ?? null,
                'label'         => $validated['label'] ?? null,
                'is_active'     => $validated['is_active'] ?? null,
            ], fn($v) => !is_null($v)));

            if (isset($validated['min_stock_alert'])) {
                $packagingBox->stock()->update(['min_stock_alert' => $validated['min_stock_alert']]);
            }
        });

        return response()->json(
            $this->formatBox($packagingBox->fresh(['finishedGood.brand', 'stock']))
        );
    }

    /**
     * Supprimer (soft-delete via is_active = false).
     * Suppression physique uniquement si stock = 0 et aucun mouvement.
     */
    public function destroy(PackagingBox $packagingBox): JsonResponse
    {
        $hasStock     = ($packagingBox->stock?->quantity_in_stock ?? 0) > 0;
        $hasMovements = $packagingBox->movements()->exists();

        if ($hasStock || $hasMovements) {
            // Soft-delete : désactiver seulement
            $packagingBox->update(['is_active' => false]);
            return response()->json(['message' => 'Boite désactivée (stock ou historique existant).']);
        }

        $packagingBox->stock()->delete();
        $packagingBox->delete();

        return response()->json(null, 204);
    }

    // ──────────────────────────────────────────
    // CONDITIONNEMENT : packets → boites
    // ──────────────────────────────────────────

    /**
     * Conditionner des packets en boites.
     *
     * body: { packaging_box_id, boxes_to_produce, reason? }
     *
     * Calcule packets nécessaires = boxes_to_produce × units_per_box,
     * décrémente FinishedGood.quantity_in_stock,
     * incrémente BoxStock.quantity_in_stock,
     * enregistre BoxMovement (in) + StockMovement sur FinishedGood (out).
     */
    public function pack(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'packaging_box_id' => 'required|exists:packaging_boxes,id',
            'boxes_to_produce' => 'required|integer|min:1',
            'reason'           => 'nullable|string|max:255',
        ]);

        $result = DB::transaction(function () use ($validated) {
            $box          = PackagingBox::with(['finishedGood', 'stock'])->findOrFail($validated['packaging_box_id']);
            $boxesCount   = $validated['boxes_to_produce'];
            $packetsNeeded = $boxesCount * $box->units_per_box;
            $finishedGood  = $box->finishedGood;
            $stock         = $box->stock;

            // Vérifier stock packets suffisant
            if ($finishedGood->quantity_in_stock < $packetsNeeded) {
                abort(422, "Stock insuffisant : {$finishedGood->quantity_in_stock} packets disponibles, {$packetsNeeded} requis.");
            }

            // Décrémenter packets (FinishedGood)
            $fgStockBefore = $finishedGood->quantity_in_stock;
            $fgStockAfter  = $fgStockBefore - $packetsNeeded;
            $finishedGood->update(['quantity_in_stock' => $fgStockAfter]);

            // StockMovement sur le produit fini
            \App\Models\StockMovement::create([
                'movable_type' => 'finished_good',
                'movable_id'   => $finishedGood->id,
                'type'         => 'out',
                'quantity'     => $packetsNeeded,
                'unit'         => 'piece',
                'stock_before' => $fgStockBefore,
                'stock_after'  => $fgStockAfter,
                'reason'       => "Conditionnement → {$boxesCount} boites ({$box->name})",
                'source_type'  => PackagingBox::class,
                'source_id'    => $box->id,
                'user_id'      => auth()->id(),
            ]);

            // Incrémenter stock boites
            $boxStockBefore = $stock->quantity_in_stock;
            $boxStockAfter  = $boxStockBefore + $boxesCount;
            $stock->update(['quantity_in_stock' => $boxStockAfter]);

            // BoxMovement (in)
            BoxMovement::create([
                'packaging_box_id' => $box->id,
                'type'             => 'in',
                'quantity'         => $boxesCount,
                'stock_before'     => $boxStockBefore,
                'stock_after'      => $boxStockAfter,
                'reason'           => $validated['reason'] ?? "Conditionnement {$packetsNeeded} packets",
                'source_type'      => FinishedGood::class,
                'source_id'        => $finishedGood->id,
                'user_id'          => auth()->id(),
            ]);

            return [
                'box'             => $this->formatBox($box->fresh(['finishedGood.brand', 'stock'])),
                'packets_consumed' => $packetsNeeded,
                'boxes_produced'   => $boxesCount,
                'packets_remaining' => $fgStockAfter,
            ];
        });

        return response()->json($result);
    }

    // ──────────────────────────────────────────
    // HISTORIQUE MOUVEMENTS
    // ──────────────────────────────────────────

    /**
     * Historique des mouvements d'une boite.
     */
    public function movements(PackagingBox $packagingBox): JsonResponse
    {
        $movements = $packagingBox->movements()
            ->with('user')
            ->latest()
            ->paginate(30);

        return response()->json($movements);
    }

    /**
     * Tous les mouvements toutes boites confondues.
     */
    public function allMovements(): JsonResponse
    {
        $movements = BoxMovement::with(['packagingBox.finishedGood', 'user'])
            ->latest()
            ->paginate(50);

        return response()->json($movements);
    }

    // ──────────────────────────────────────────
    // SORTIE BOITES (vente)
    // ──────────────────────────────────────────

    /**
     * Décrémenter manuellement le stock boites (utilisé par SalesOrder ou sortie manuelle).
     *
     * body: { packaging_box_id, quantity, reason? }
     */
    public function out(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'packaging_box_id' => 'required|exists:packaging_boxes,id',
            'quantity'         => 'required|integer|min:1',
            'reason'           => 'nullable|string|max:255',
        ]);

        $result = DB::transaction(function () use ($validated) {
            $box   = PackagingBox::with('stock')->findOrFail($validated['packaging_box_id']);
            $stock = $box->stock;

            if ($stock->quantity_in_stock < $validated['quantity']) {
                abort(422, "Stock insuffisant : {$stock->quantity_in_stock} boites disponibles.");
            }

            $before = $stock->quantity_in_stock;
            $after  = $before - $validated['quantity'];
            $stock->update(['quantity_in_stock' => $after]);

            BoxMovement::create([
                'packaging_box_id' => $box->id,
                'type'             => 'out',
                'quantity'         => $validated['quantity'],
                'stock_before'     => $before,
                'stock_after'      => $after,
                'reason'           => $validated['reason'] ?? 'Sortie manuelle',
                'user_id'          => auth()->id(),
            ]);

            return ['stock_after' => $after, 'quantity_out' => $validated['quantity']];
        });

        return response()->json($result);
    }

    // ──────────────────────────────────────────
    // HELPER
    // ──────────────────────────────────────────

    private function formatBox(PackagingBox $box): array
    {
        return [
            'id'                => $box->id,
            'name'              => $box->name,
            'label'             => $box->label,
            'units_per_box'     => $box->units_per_box,
            'is_active'         => $box->is_active,
            'quantity_in_stock' => $box->stock?->quantity_in_stock ?? 0,
            'min_stock_alert'   => $box->stock?->min_stock_alert ?? 0,
            'is_low_stock'      => $box->isLowStock(),
            'finished_good'     => $box->finishedGood ? [
                'id'                => $box->finishedGood->id,
                'product_name'      => $box->finishedGood->product_name,
                'packet_size_g'     => $box->finishedGood->packet_size_g,
                'packet_label'      => $box->finishedGood->packet_label,
                'quantity_in_stock' => $box->finishedGood->quantity_in_stock,
                'brand'             => $box->finishedGood->brand?->name,
            ] : null,
        ];
    }
}