<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function index(): JsonResponse
    {
        $suppliers = Supplier::where('is_active', true)->get();
        return response()->json($suppliers);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'contact_name'   => 'nullable|string|max:255',
            'email'          => 'nullable|email',
            'phone'          => 'nullable|string|max:20',
            'country'        => 'nullable|string|max:3',
            'lead_time_days' => 'nullable|integer|min:1',
            'notes'          => 'nullable|string',
        ]);

        $supplier = Supplier::create($validated);
        return response()->json($supplier, 201);
    }

    public function show(Supplier $supplier): JsonResponse
    {
        return response()->json($supplier->load('rawMaterials'));
    }

    public function update(Request $request, Supplier $supplier): JsonResponse
    {
        $validated = $request->validate([
            'name'           => 'sometimes|string|max:255',
            'contact_name'   => 'nullable|string|max:255',
            'email'          => 'nullable|email',
            'phone'          => 'nullable|string|max:20',
            'country'        => 'nullable|string|max:3',
            'lead_time_days' => 'nullable|integer|min:1',
            'notes'          => 'nullable|string',
            'is_active'      => 'sometimes|boolean',
        ]);

        $supplier->update($validated);
        return response()->json($supplier);
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $supplier->update(['is_active' => false]);
        return response()->json(null, 204);
    }
}