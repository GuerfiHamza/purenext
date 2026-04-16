<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RecipeController extends Controller
{
    public function index(): JsonResponse
    {
        $recipes = Recipe::with(['brand', 'ingredients.rawMaterial', 'packagingOptions'])
            ->where('is_active', true)
            ->get()
            ->map(function ($r) {
                $r->current_stock = $r->finishedGoods()->sum('quantity_in_stock');
                return $r;
            });

        return response()->json($recipes);
    }
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'version' => 'nullable|string|max:10',
            'yield_unit' => 'required|in:packet,kg,piece,box',
            'yield_qty' => 'required|numeric|min:0.001',
            'loss_percentage' => 'nullable|numeric|min:0|max:100',
            'shelf_life_value' => 'nullable|integer|min:1',
            'shelf_life_unit' => 'nullable|in:days,months',
            'notes' => 'nullable|string',
            'technical_params' => 'nullable|array',
        ]);

        $recipe = Recipe::create($validated);
        return response()->json($recipe->load(['brand', 'ingredients', 'packagingOptions']), 201);
    }

    public function show(Recipe $recipe): JsonResponse
    {
        return response()->json($recipe->load(['brand', 'ingredients.rawMaterial', 'packagingOptions']));
    }

    public function update(Request $request, Recipe $recipe): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'version' => 'nullable|string|max:10',
            'yield_unit' => 'sometimes|in:packet,kg,piece,box',
            'yield_qty' => 'sometimes|numeric|min:0.001',
            'loss_percentage' => 'nullable|numeric|min:0|max:100',
            'shelf_life_value' => 'nullable|integer|min:1',
            'shelf_life_unit' => 'nullable|in:days,months',
            'notes' => 'nullable|string',
            'technical_params' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
        ]);

        $recipe->update($validated);
        return response()->json($recipe->load(['brand', 'ingredients', 'packagingOptions']));
    }

    public function destroy(Recipe $recipe): JsonResponse
    {
        $recipe->update(['is_active' => false]);
        return response()->json(null, 204);
    }

    public function packagingOptions(Recipe $recipe): JsonResponse
    {
        return response()->json($recipe->packagingOptions);
    }
}
