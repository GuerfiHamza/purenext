<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinishedGood;
use App\Models\SaleOrderItem;
use App\Models\SalesOrder;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    public function index(): JsonResponse
{
    $orders = SalesOrder::with(['commercial', 'items.finishedGood.brand', 'items.packagingBox'])
        ->latest()
        ->paginate(20);

    return response()->json($orders);
}
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:150',
            'client_phone' => 'nullable|string|max:30',
            'client_email' => 'nullable|email|max:150',
            'client_address' => 'nullable|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'client_type' => 'nullable|in:particulier,societe',
            'client_rc' => 'nullable|string|max:50',
            'client_nif' => 'nullable|string|max:50',
            'client_nis' => 'nullable|string|max:50',
            'client_ai' => 'nullable|string|max:50',
            'order_date' => 'required|date',
            'delivery_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|min:1',
            'items.*.item_type' => 'required|in:packet,box',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $order = DB::transaction(function () use ($validated, $request) {
            $orderNumber = 'CMD-' . now()->format('Y') . '-' . str_pad(SalesOrder::whereYear('created_at', now()->year)->count() + 1, 4, '0', STR_PAD_LEFT);

            $order = SalesOrder::create([
                'order_number' => $orderNumber,
                'client_id' => $validated['client_id'] ?? null,
                'client_type' => $validated['client_type'] ?? 'particulier',
                'client_name' => $request->input('client_name'),
                'client_phone' => $request->input('client_phone'),
                'client_email' => $request->input('client_email'),
                'client_address' => $request->input('client_address'),
                'client_rc' => $validated['client_rc'] ?? null,
                'client_nif' => $validated['client_nif'] ?? null,
                'client_nis' => $validated['client_nis'] ?? null,
                'client_ai' => $validated['client_ai'] ?? null,
                'order_date' => $validated['order_date'],
                'delivery_date' => $validated['delivery_date'] ?? null,
                'commercial_id' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $itemType = $item['item_type'] ?? 'packet';
                SaleOrderItem::create([
                    'sales_order_id' => $order->id,
                    'finished_good_id' => $itemType === 'packet' ? $item['product_id'] : null,
                    'packaging_box_id' => $itemType === 'box' ? $item['product_id'] : null,
                    'item_type' => $itemType,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }
            $order->recalculateTotal();
            return $order;
        });

        return response()->json($order->load('items.finishedGood'), 201);
    }

    public function show(SalesOrder $salesOrder): JsonResponse
    {
return response()->json($salesOrder->load(['commercial', 'items.finishedGood.brand', 'items.packagingBox']));
    }

    public function update(Request $request, SalesOrder $salesOrder): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,confirmed,preparing,shipped,delivered,cancelled',
            'delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $salesOrder->update($validated);
        return response()->json($salesOrder);
    }

    // Confirmer la livraison → décrémenter le stock PF
    public function deliver(SalesOrder $salesOrder): JsonResponse
    {
        if ($salesOrder->status !== 'shipped') {
            return response()->json(['message' => 'La commande doit être en statut expédiée.'], 422);
        }

        DB::transaction(function () use ($salesOrder) {
            foreach ($salesOrder->items as $item) {
                if ($item->item_type === 'box') {
                    $stock = \App\Models\BoxStock::where('packaging_box_id', $item->packaging_box_id)->first();
                    if (!$stock) {
                        continue;
                    }
                    $before = $stock->quantity_in_stock;
                    $after = max(0, $before - $item->quantity);
                    $stock->update(['quantity_in_stock' => $after]);

                    \App\Models\BoxMovement::create([
                        'packaging_box_id' => $item->packaging_box_id,
                        'type' => 'out',
                        'quantity' => $item->quantity,
                        'stock_before' => $before,
                        'stock_after' => $after,
                        'reason' => "Livraison commande {$salesOrder->order_number}",
                        'source_type' => SalesOrder::class,
                        'source_id' => $salesOrder->id,
                        'user_id' => auth()->id(),
                    ]);
                } else {
                    $good = FinishedGood::find($item->finished_good_id);
                    if (!$good) {
                        continue;
                    }
                    $stockBefore = $good->quantity_in_stock;
                    $stockAfter = max(0, $stockBefore - $item->quantity);
                    $good->update(['quantity_in_stock' => $stockAfter]);

                    StockMovement::create([
                        'movable_type' => 'finished_good',
                        'movable_id' => $good->id,
                        'type' => 'out',
                        'quantity' => $item->quantity,
                        'unit' => 'piece',
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'reason' => "Livraison commande {$salesOrder->order_number}",
                        'source_type' => SalesOrder::class,
                        'source_id' => $salesOrder->id,
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            $salesOrder->update(['status' => 'delivered']);
        });

        return response()->json($salesOrder->fresh()->load('items.finishedGood'));
    }

    public function destroy(SalesOrder $salesOrder): JsonResponse
    {
        $salesOrder->update(['status' => 'cancelled']);
        return response()->json(null, 204);
    }
}
