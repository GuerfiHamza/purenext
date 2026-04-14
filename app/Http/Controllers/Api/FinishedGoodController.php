<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinishedGood;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\StockMovement;
class FinishedGoodController extends Controller
{
    public function adjust(Request $request, FinishedGood $finishedGood): JsonResponse
{
    $validated = $request->validate([
        'type'     => 'required|in:in,out,adjustment',
        'quantity' => 'required|numeric|min:0',
        'reason'   => 'nullable|string|max:255',
    ]);
 
    $stockBefore = $finishedGood->quantity_in_stock;
 
    $stockAfter = match($validated['type']) {
        'in'    => $stockBefore + $validated['quantity'],
        'out' => max(0, $stockBefore - $validated['quantity']),
        'adjustment'    => $validated['quantity'],
    };
 
    $finishedGood->update(['quantity_in_stock' => $stockAfter]);
 
    // Enregistrer le mouvement
    StockMovement::create([
        'movable_type' => 'finished_good',
        'movable_id'   => $finishedGood->id,
        'type'         => 'adjustment',
        'quantity'     => abs($stockAfter - $stockBefore),
        'unit'         => 'pcs',
        'stock_before' => $stockBefore,
        'stock_after'  => $stockAfter,
        'reason'       => $validated['reason'] ?? 'Ajustement manuel',
        'user_id'      => auth()->id(),
    ]);
 
    return response()->json($finishedGood->fresh());
}
    public function index(): JsonResponse
    {
        $goods = FinishedGood::with(['brand', 'productionRun'])
            ->get()
            ->map(fn($g) => array_merge($g->toArray(), [
                'is_low_stock' => $g->isLowStock()
            ]));

        return response()->json($goods);
    }

    public function show(FinishedGood $finishedGood): JsonResponse
    {
        return response()->json(
            $finishedGood->load(['brand', 'productionRun.recipe', 'orderItems.salesOrder'])
        );
    }

    public function update(Request $request, FinishedGood $finishedGood): JsonResponse
    {
        $validated = $request->validate([
            'min_stock_alert' => 'nullable|integer|min:0',
            'expiry_date'     => 'nullable|date',
            'notes'           => 'nullable|string',
        ]);

        $finishedGood->update($validated);
        return response()->json($finishedGood);
    }

    public function destroy(FinishedGood $finishedGood): JsonResponse
    {
        $finishedGood->delete();
        return response()->json(null, 204);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_name'      => 'required|string|max:255',
            'brand_id'          => 'required|exists:brands,id',
            'packet_size_g'     => 'required|numeric|min:0.1',
            'packet_label'      => 'required|string|max:255',
            'quantity_in_stock' => 'required|integer|min:0',
            'production_date'   => 'required|date',
            'expiry_date'       => 'nullable|date|after:production_date',
            'min_stock_alert'   => 'nullable|integer|min:0',
        ]);

        $good = FinishedGood::create($validated);
        return response()->json($good->load('brand'), 201);
    }
}