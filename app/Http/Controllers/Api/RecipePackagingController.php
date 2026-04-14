<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecipePackaging;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RecipePackagingController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(RecipePackaging::with('recipe')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipe_id'                  => 'required|exists:recipes,id',
            'packet_size_g'              => 'required|numeric|min:0.1',
            'packet_label'               => 'required|string|max:255',
            'film_type'                  => 'nullable|string|max:100',
            'film_width_mm'              => 'nullable|numeric|min:0',
            'film_length_mm'             => 'nullable|numeric|min:0',
            'machine_capacity_per_hour'  => 'nullable|numeric|min:0',
            'is_default'                 => 'nullable|boolean',
            'notes'                      => 'nullable|string',
        ]);

        // Un seul défaut par recette
        if (!empty($validated['is_default'])) {
            RecipePackaging::where('recipe_id', $validated['recipe_id'])
                ->update(['is_default' => false]);
        }

        $packaging = RecipePackaging::create($validated);
        return response()->json($packaging, 201);
    }

    public function show(RecipePackaging $recipePackaging): JsonResponse
    {
        return response()->json($recipePackaging->load('recipe'));
    }

    public function update(Request $request, RecipePackaging $recipePackaging): JsonResponse
    {
        $validated = $request->validate([
            'packet_size_g'             => 'sometimes|numeric|min:0.1',
            'packet_label'              => 'sometimes|string|max:255',
            'film_type'                 => 'nullable|string|max:100',
            'film_width_mm'             => 'nullable|numeric|min:0',
            'film_length_mm'            => 'nullable|numeric|min:0',
            'machine_capacity_per_hour' => 'nullable|numeric|min:0',
            'is_default'                => 'nullable|boolean',
            'notes'                     => 'nullable|string',
        ]);

        if (!empty($validated['is_default'])) {
            RecipePackaging::where('recipe_id', $recipePackaging->recipe_id)
                ->where('id', '!=', $recipePackaging->id)
                ->update(['is_default' => false]);
        }

        $recipePackaging->update($validated);
        return response()->json($recipePackaging);
    }

    public function destroy(RecipePackaging $recipePackaging): JsonResponse
    {
        $recipePackaging->delete();
        return response()->json(null, 204);
    }
}