<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\FinishedGood;
use App\Models\ProductionRun;
use Illuminate\Http\Request;

class TraceabilityController extends Controller
{
    // Recherche par numéro de lot PF → toutes les commandes qui l'ont reçu
    public function searchByLot(Request $request)
    {
        $lot = $request->input('lot');
        if (!$lot) return response()->json([]);

        // Trouve les produits finis dont le batch_number contient le lot
        $goods = FinishedGood::where('batch_number', 'like', "%{$lot}%")
            ->orWhere('batch_number', $lot)
            ->with(['productionRun.recipe', 'productionRun.ingredients.rawMaterial'])
            ->get();

        $goodIds = $goods->pluck('id');

        // Trouve toutes les commandes qui contiennent ces produits
        $orders = SalesOrder::whereHas('items', fn($q) => $q->whereIn('finished_good_id', $goodIds))
            ->with(['items.finishedGood', 'commercial'])
            ->orderByDesc('order_date')
            ->get();

        return response()->json([
            'lot'            => $lot,
            'finished_goods' => $goods,
            'orders'         => $orders,
            'orders_count'   => $orders->count(),
            'total_qty'      => $orders->flatMap->items
                ->whereIn('finished_good_id', $goodIds->toArray())
                ->sum('quantity'),
        ]);
    }

    // Recherche par client → tous ses lots reçus
    public function searchByClient(Request $request)
    {
        $client = $request->input('client');
        if (!$client) return response()->json([]);

        $orders = SalesOrder::where('client_name', 'like', "%{$client}%")
            ->with(['items.finishedGood.productionRun.recipe', 'commercial'])
            ->orderByDesc('order_date')
            ->get();

        return response()->json($orders);
    }

    // Trace complète d'une commande — aval + amont
    public function orderTrace(SalesOrder $salesOrder)
    {
        $salesOrder->load([
            'items.finishedGood.productionRun.recipe',
            'items.finishedGood.productionRun.ingredients.rawMaterial',
            'items.finishedGood.productionRun.packaging',
            'commercial',
        ]);

        // Pour chaque item → remonte la chaîne complète
        $trace = $salesOrder->items->map(function ($item) {
            $fg  = $item->finishedGood;
            $run = $fg?->productionRun;

            return [
                'item' => [
                    'product'    => $fg?->product_name ?? '—',
                    'batch'      => $fg?->batch_number ?? '—',
                    'lot'        => $run?->lot_number ?? '—',
                    'quantity'   => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'expiry'     => $fg?->expiry_date,
                ],
                'production' => $run ? [
                    'lot_number'    => $run->lot_number,
                    'batch_number'  => $run->batch_number,
                    'recipe'        => $run->recipe?->name,
                    'started_at'    => $run->started_at,
                    'finished_at'   => $run->finished_at,
                    'input_qty_kg'  => $run->input_qty_kg,
                    'output_actual' => $run->output_packets_actual,
                    'status'        => $run->status,
                ] : null,
                'raw_materials' => $run?->ingredients->map(fn($i) => [
                    'name'     => $i->rawMaterial?->name ?? '—',
                    'sku'      => $i->rawMaterial?->sku ?? '—',
                    'quantity' => $i->quantity,
                    'unit'     => $i->unit,
                ]) ?? [],
            ];
        });

        return response()->json([
            'order' => $salesOrder,
            'trace' => $trace,
        ]);
    }
}