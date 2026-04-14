<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    public function index(): JsonResponse
    {
        $brands = Brand::where('is_active', true)->get();
        return response()->json($brands);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|unique:brands',
            'color_hex'   => 'nullable|string|size:7',
            'slogan'      => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $brand = Brand::create($validated);
        return response()->json($brand, 201);
    }

    public function show(Brand $brand): JsonResponse
    {
        return response()->json($brand->load(['recipes', 'rawMaterials']));
    }

    public function update(Request $request, Brand $brand): JsonResponse
    {
        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'slug'        => 'sometimes|string|unique:brands,slug,' . $brand->id,
            'color_hex'   => 'nullable|string|size:7',
            'slogan'      => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'sometimes|boolean',
        ]);

        $brand->update($validated);
        return response()->json($brand);
    }

    public function destroy(Brand $brand): JsonResponse
    {
        $brand->update(['is_active' => false]);
        return response()->json(null, 204);
    }
}