<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StockMovementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = StockMovement::with(['user'])
            ->latest()
            ->when($request->movable_type, fn($q) => $q->where('movable_type', $request->movable_type))
            ->when($request->movable_id, fn($q) => $q->where('movable_id', $request->movable_id))
            ->when($request->type, fn($q) => $q->where('type', $request->type));

        return response()->json($query->paginate(50));
    }

    public function show(StockMovement $stockMovement): JsonResponse
    {
        return response()->json($stockMovement->load(['user', 'movable']));
    }

    // Les autres méthodes sont vides — les mouvements sont créés automatiquement
    public function store(Request $request): JsonResponse { return response()->json(null, 405); }
    public function update(Request $request, StockMovement $stockMovement): JsonResponse { return response()->json(null, 405); }
    public function destroy(StockMovement $stockMovement): JsonResponse { return response()->json(null, 405); }
}