<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinishedGood;
use App\Models\ProductionRun;
use App\Models\RawMaterial;
use App\Models\SalesOrder;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        // KPIs
        $totalRawMaterials     = RawMaterial::where('is_active', true)->count();
        $totalFinishedGoods    = FinishedGood::sum('quantity_in_stock');
        $productionsInProgress = ProductionRun::where('status', 'in_progress')->count();
        $pendingOrders         = SalesOrder::whereIn('status', ['pending', 'confirmed'])->count();

        // Alertes stock bas MP
        $lowRawMaterials = RawMaterial::where('is_active', true)
            ->whereColumn('quantity_in_stock', '<=', 'min_stock_alert')
            ->with('supplier')
            ->get();

        // Alertes stock bas PF
        $lowFinishedGoods = FinishedGood::whereColumn('quantity_in_stock', '<=', 'min_stock_alert')
            ->with('brand')
            ->get();

        // Productions récentes
        $recentProductions = ProductionRun::with(['recipe.brand', 'packaging'])
            ->latest()
            ->take(5)
            ->get();

        // Commandes récentes
        $recentOrders = SalesOrder::with('items')
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'kpis' => [
                'total_raw_materials'      => $totalRawMaterials,
                'total_finished_goods'     => $totalFinishedGoods,
                'productions_in_progress'  => $productionsInProgress,
                'pending_orders'           => $pendingOrders,
                'low_stock_alerts'         => $lowRawMaterials->count() + $lowFinishedGoods->count(),
            ],
            'low_stock' => [
                'raw_materials'  => $lowRawMaterials,
                'finished_goods' => $lowFinishedGoods,
            ],
            'recent_productions' => $recentProductions,
            'recent_orders'      => $recentOrders,
        ]);
    }
    public function charts(): JsonResponse
{
    // Productions par mois (12 derniers mois)
    $productionsByMonth = ProductionRun::where('status', 'completed')
        ->where('started_at', '>=', now()->subMonths(12))
        ->selectRaw('DATE_FORMAT(started_at, "%Y-%m") as month, COUNT(*) as total, SUM(output_packets_actual) as packets')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    // CA par mois (12 derniers mois)
    $revenueByMonth = SalesOrder::where('status', 'delivered')
        ->where('order_date', '>=', now()->subMonths(12))
        ->selectRaw('DATE_FORMAT(order_date, "%Y-%m") as month, SUM(total_amount) as revenue, COUNT(*) as orders')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    // Stock MP par marque
    $stockByBrand = \App\Models\Brand::withSum(
        ['rawMaterials' => fn($q) => $q->where('is_active', true)],
        'quantity_in_stock'
    )->get()->map(fn($b) => [
        'name'  => $b->name,
        'stock' => round($b->raw_materials_sum_quantity_in_stock ?? 0, 2),
        'color' => $b->color_hex ?? '#16a34a',
    ]);

    // Top produits finis par stock
    $topFinishedGoods = \App\Models\FinishedGood::with('brand')
        ->orderByDesc('quantity_in_stock')
        ->take(5)
        ->get()
        ->map(fn($g) => [
            'name'     => $g->product_name,
            'stock'    => $g->quantity_in_stock,
            'brand'    => $g->brand?->name,
            'color'    => $g->brand?->color_hex ?? '#16a34a',
        ]);

    return response()->json([
        'productions_by_month' => $productionsByMonth,
        'revenue_by_month'     => $revenueByMonth,
        'stock_by_brand'       => $stockByBrand,
        'top_finished_goods'   => $topFinishedGoods,
    ]);
}
}