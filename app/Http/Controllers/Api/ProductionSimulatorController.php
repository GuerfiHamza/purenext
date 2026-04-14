<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\RecipePackaging;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ProductionSimulatorService;

class ProductionSimulatorController extends Controller
{
    public function __construct(private ProductionSimulatorService $simulator) {}

    public function simulate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipe_id'           => 'required|exists:recipes,id',
            'recipe_packaging_id' => 'required|exists:recipe_packaging,id',
            'input_qty_kg'        => 'required|numeric|min:0.001',
        ]);

        $result = $this->simulator->simulate(
            $validated['recipe_id'],
            $validated['recipe_packaging_id'],
            $validated['input_qty_kg']
        );

        return response()->json($result);
    }
}