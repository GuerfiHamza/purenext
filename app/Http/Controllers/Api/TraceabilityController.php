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
        if (!$lot) {
            return response()->json([]);
        }

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
            'lot' => $lot,
            'finished_goods' => $goods,
            'orders' => $orders,
            'orders_count' => $orders->count(),
            'total_qty' => $orders->flatMap->items->whereIn('finished_good_id', $goodIds->toArray())->sum('quantity'),
        ]);
    }

    // Recherche par client → tous ses lots reçus
    public function searchByClient(Request $request)
    {
        $client = $request->input('client');
        if (!$client) {
            return response()->json([]);
        }

        $orders = SalesOrder::where('client_name', 'like', "%{$client}%")
            ->with(['items.finishedGood.productionRun.recipe', 'commercial'])
            ->orderByDesc('order_date')
            ->get();

        return response()->json($orders);
    }

    // Trace complète d'une commande — aval + amont
    public function orderTrace(SalesOrder $salesOrder)
    {
        $salesOrder->load(['items.finishedGood.productionRun.recipe', 'items.finishedGood.productionRun.ingredients.rawMaterial', 'items.finishedGood.productionRun.packaging', 'commercial']);

        // Pour chaque item → remonte la chaîne complète
        $trace = $salesOrder->items->map(function ($item) {
            $fg = $item->finishedGood;
            $run = $fg?->productionRun;

            return [
                'item' => [
                    'product' => $fg?->product_name ?? '—',
                    'batch' => $fg?->batch_number ?? '—',
                    'lot' => $run?->lot_number ?? '—',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'expiry' => $fg?->expiry_date,
                ],
                'production' => $run
                    ? [
                        'lot_number' => $run->lot_number,
                        'batch_number' => $run->batch_number,
                        'recipe' => $run->recipe?->name,
                        'started_at' => $run->started_at,
                        'finished_at' => $run->finished_at,
                        'input_qty_kg' => $run->input_qty_kg,
                        'output_actual' => $run->output_packets_actual,
                        'status' => $run->status,
                    ]
                    : null,
                // Dans orderTrace() et searchByLot(), remplace la partie raw_materials :

                'raw_materials' =>
                    $run?->ingredients->map(function ($i) use ($run) {
                        $rm = $i->rawMaterial;

                        // Trouve les réceptions de cette MP proches de la date de production
                        // (dans les 90 jours avant la production — stock FIFO probable)
                        $receipts = $rm
                            ? \App\Models\RawMaterialReceipt::where('raw_material_id', $rm->id)
                                ->where('decision', '!=', 'refused')
                                ->where('reception_date', '<=', $run->started_at ?? now())
                                ->where('reception_date', '>=', \Carbon\Carbon::parse($run->started_at ?? now())->subDays(90))
                                ->orderByDesc('reception_date')
                                ->get(['receipt_number', 'supplier_name', 'supplier_lot', 'reception_date', 'quantity', 'unit', 'dluo_date'])
                            : collect();

                        return [
                            'name' => $rm?->name ?? '—',
                            'sku' => $rm?->sku ?? '—',
                            'quantity' => $i->quantity,
                            'unit' => $i->unit,
                            'receipts' => $receipts
                                ->map(
                                    fn($r) => [
                                        'receipt_number' => $r->receipt_number,
                                        'supplier_name' => $r->supplier_name,
                                        'supplier_lot' => $r->supplier_lot ?? '—',
                                        'reception_date' => \Carbon\Carbon::parse($r->reception_date)->format('d/m/Y'),
                                        'quantity' => $r->quantity,
                                        'unit' => $r->unit,
                                        'dluo_date' => $r->dluo_date ? \Carbon\Carbon::parse($r->dluo_date)->format('d/m/Y') : '—',
                                    ],
                                )
                                ->toArray(),
                        ];
                    }) ?? [],
            ];
        });

        return response()->json([
            'order' => $salesOrder,
            'trace' => $trace,
        ]);
    }
}
