<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $clients = Client::query()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('phone', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->orderBy('name')
            ->get();

        return response()->json($clients);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type'    => 'required|in:particulier,societe',
            'name'    => 'required|string|max:150',
            'phone'   => 'nullable|string|max:30',
            'email'   => 'nullable|email|max:150',
            'address' => 'nullable|string|max:255',
            'rc'      => 'nullable|string|max:50',
            'nif'     => 'nullable|string|max:50',
            'nis'     => 'nullable|string|max:50',
            'ai'      => 'nullable|string|max:50',
            'notes'   => 'nullable|string',
        ]);

        $client = Client::create($validated);
        return response()->json($client, 201);
    }

    public function show(Client $client): JsonResponse
    {
        return response()->json($client->load('salesOrders'));
    }

    public function update(Request $request, Client $client): JsonResponse
    {
        $validated = $request->validate([
            'type'    => 'sometimes|in:particulier,societe',
            'name'    => 'sometimes|string|max:150',
            'phone'   => 'nullable|string|max:30',
            'email'   => 'nullable|email|max:150',
            'address' => 'nullable|string|max:255',
            'rc'      => 'nullable|string|max:50',
            'nif'     => 'nullable|string|max:50',
            'nis'     => 'nullable|string|max:50',
            'ai'      => 'nullable|string|max:50',
            'notes'   => 'nullable|string',
        ]);

        $client->update($validated);
        return response()->json($client);
    }

    public function destroy(Client $client): JsonResponse
    {
        $client->delete();
        return response()->json(null, 204);
    }
}