<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\FinishedGood;
use App\Models\ProductionRun;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\StockMovement;
use App\Models\RawMaterialReceipt;

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
public function monthlyReport(Request $request)
{
    $month = $request->input('month', now()->format('Y-m')); // ex: "2026-04"
    $start = Carbon::parse($month . '-01')->startOfMonth();
    $end   = Carbon::parse($month . '-01')->endOfMonth();

    // --- Lots produits ce mois ---
    $productions = ProductionRun::with(['recipe'])
        ->whereBetween('started_at', [$start, $end])
        ->whereNotNull('lot_number')
        ->get();

    $lots = $productions->map(function ($p) {
        return [
            'lot'        => $p->lot_number,
            'recipe'     => $p->recipe?->name,
            'quantity'   => $p->quantity,
            'unit'       => $p->unit,
            'date'       => $p->started_at?->format('Y-m-d'),
            'status'     => $p->status,
        ];
    });

    // --- MP utilisées ce mois ---
    $rawMovements = StockMovement::with('movable')
        ->where('type', 'out')
        ->where('source_type', ProductionRun::class)
        ->whereBetween('created_at', [$start, $end])
        ->get()
        ->groupBy('movable_id')
        ->map(function ($mvts) {
            $first = $mvts->first();
            return [
                'raw_material' => $first->movable?->name ?? 'Inconnu',
                'total_used'   => $mvts->sum('quantity'),
                'unit'         => $first->unit,
                'movements'    => $mvts->count(),
            ];
        })->values();

    // --- Commandes livrées ce mois ---
    $orders = SalesOrder::with('client')
        ->where('status', 'delivered')
        ->whereBetween('delivered_at', [$start, $end])
        ->get();

    $deliveries = $orders->map(fn($o) => [
        'order_ref' => $o->reference,
        'client'    => $o->client?->name,
        'amount'    => $o->total_amount,
        'date'      => $o->delivered_at?->format('Y-m-d'),
        'lot'       => $o->lot_number,
    ]);

    // --- Clients uniques ---
    $uniqueClients = $orders->pluck('client.name')->filter()->unique()->values();

    // --- Alertes ---
    $alerts = [];

    // Lots sans commande livrée associée
    $deliveredLots = $orders->pluck('lot_number')->filter()->unique();
    $untracedLots = $lots->filter(fn($l) => !$deliveredLots->contains($l['lot']));
    foreach ($untracedLots as $l) {
        $alerts[] = [
            'type'    => 'lot_non_trace',
            'level'   => 'warning',
            'message' => "Lot {$l['lot']} ({$l['recipe']}) produit le {$l['date']} sans commande livrée associée.",
            'lot'     => $l['lot'],
        ];
    }

    // Réceptions MP sans lot fournisseur
    $receptions = RawMaterialReceipt::whereBetween('reception_date', [$start, $end])
        ->whereNull('supplier_lot')
        ->get();
    foreach ($receptions as $r) {
        $alerts[] = [
            'type'    => 'reception_sans_lot',
            'level'   => 'error',
            'message' => "Réception {$r->receipt_number} sans lot fournisseur (MP: {$r->supplier_name}).",
            'receipt' => $r->receipt_number,
        ];
    }

    // Productions sans lot
    $noLot = ProductionRun::whereBetween('started_at', [$start, $end])
        ->whereNull('lot_number')
        ->get();
    foreach ($noLot as $p) {
        $alerts[] = [
            'type'    => 'production_sans_lot',
            'level'   => 'error',
            'message' => "Production #{$p->id} ({$p->recipe?->name}) sans numéro de lot.",
        ];
    }

    return response()->json([
        'month'           => $month,
        'lots_produced'   => $lots,
        'raw_materials'   => $rawMovements,
        'deliveries'      => $deliveries,
        'unique_clients'  => $uniqueClients,
        'alerts'          => $alerts,
        'summary' => [
            'total_lots'       => $lots->count(),
            'total_deliveries' => $deliveries->count(),
            'total_clients'    => $uniqueClients->count(),
            'total_alerts'     => count($alerts),
            'alert_errors'     => collect($alerts)->where('level', 'error')->count(),
            'alert_warnings'   => collect($alerts)->where('level', 'warning')->count(),
        ],
    ]);
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
                                ->when($run->started_at, function ($q) use ($run) {
                                    // Si started_at existe → fenêtre 180j avant + 30j après (réception après démarrage possible)
                                    $q->where('reception_date', '<=', \Carbon\Carbon::parse($run->started_at)->addDays(30))->where('reception_date', '>=', \Carbon\Carbon::parse($run->started_at)->subDays(180));
                                })
                                // Si started_at null → toutes les réceptions de cette MP
                                ->orderByDesc('reception_date')
                                ->limit(5)
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
