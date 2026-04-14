<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RawMaterial;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\StockService;

class RawMaterialController extends Controller
{
public function __construct(private StockService $stockService) {}

public function adjustStock(Request $request, RawMaterial $rawMaterial): JsonResponse
{
    $validated = $request->validate([
        'type'     => 'required|in:in,out,adjustment',
        'quantity' => 'required|numeric|min:0.001',
        'reason'   => 'required|string|max:255',
    ]);

    $material = $this->stockService->adjustRawMaterial(
        $rawMaterial,
        $validated['type'],
        $validated['quantity'],
        $validated['reason']
    );

    return response()->json($material);
}
    public function index(): JsonResponse
    {
        $materials = RawMaterial::with(['brand', 'supplier'])
            ->where('is_active', true)
            ->get()
            ->map(fn($m) => array_merge($m->toArray(), [
                'is_low_stock' => $m->isLowStock()
            ]));

        return response()->json($materials);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'sku'                => 'nullable|string|unique:raw_materials',
            'brand_id'           => 'nullable|exists:brands,id',
            'supplier_id'        => 'nullable|exists:suppliers,id',
            'unit'               => 'required|in:kg,g,l,ml,piece,box,m2,m',
            'quantity_in_stock'  => 'nullable|numeric|min:0',
            'min_stock_alert'    => 'nullable|numeric|min:0',
            'cost_per_unit'      => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string',
        ]);

        $material = RawMaterial::create($validated);
        return response()->json($material->load(['brand', 'supplier']), 201);
    }

    public function show(RawMaterial $rawMaterial): JsonResponse
    {
        return response()->json(
            $rawMaterial->load(['brand', 'supplier', 'recipeIngredients.recipe'])
        );
    }

    public function update(Request $request, RawMaterial $rawMaterial): JsonResponse
    {
        $validated = $request->validate([
            'name'            => 'sometimes|string|max:255',
            'sku'             => 'nullable|string|unique:raw_materials,sku,' . $rawMaterial->id,
            'brand_id'        => 'nullable|exists:brands,id',
            'supplier_id'     => 'nullable|exists:suppliers,id',
            'unit'            => 'sometimes|in:kg,g,l,ml,piece,box,m2,m',
            'min_stock_alert' => 'nullable|numeric|min:0',
            'cost_per_unit'   => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
            'is_active'       => 'sometimes|boolean',
        ]);

        $rawMaterial->update($validated);
        return response()->json($rawMaterial);
    }

    public function destroy(RawMaterial $rawMaterial): JsonResponse
    {
        $rawMaterial->update(['is_active' => false]);
        return response()->json(null, 204);
    }

    // Ajustement manuel du stock (inventaire physique)
    public function movements(RawMaterial $rawMaterial): JsonResponse
{
    $movements = StockMovement::where('movable_type', 'raw_material')
        ->where('movable_id', $rawMaterial->id)
        ->with('user:id,name')
        ->orderByDesc('created_at')
        ->paginate(30);
 
    return response()->json($movements);
}
}