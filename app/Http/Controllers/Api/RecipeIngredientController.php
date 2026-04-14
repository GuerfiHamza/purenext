<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecipeIngredient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RecipeIngredientController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            RecipeIngredient::with(['recipe', 'rawMaterial'])->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipe_id'       => 'required|exists:recipes,id',
            'raw_material_id' => 'required|exists:raw_materials,id',
            'quantity'        => 'required|numeric|min:0.0001',
            'unit'            => 'required|in:kg,g,l,ml,piece,box,m2,m',
            'order'           => 'nullable|integer|min:1',
            'notes'           => 'nullable|string',
        ]);

        $ingredient = RecipeIngredient::create($validated);
        return response()->json($ingredient->load('rawMaterial'), 201);
    }

    public function show(RecipeIngredient $recipeIngredient): JsonResponse
    {
        return response()->json($recipeIngredient->load(['recipe', 'rawMaterial']));
    }

    public function update(Request $request, RecipeIngredient $recipeIngredient): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'sometimes|numeric|min:0.0001',
            'unit'     => 'sometimes|in:kg,g,l,ml,piece,box,m2,m',
            'order'    => 'nullable|integer|min:1',
            'notes'    => 'nullable|string',
        ]);

        $recipeIngredient->update($validated);
        return response()->json($recipeIngredient);
    }

    public function destroy(RecipeIngredient $recipeIngredient): JsonResponse
    {
        $recipeIngredient->delete();
        return response()->json(null, 204);
    }
}