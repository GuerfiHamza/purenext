<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RawMaterialReceipt;
use App\Models\RawMaterial;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RawMaterialReceiptController extends Controller
{
    public function __construct(private DocumentService $documentService) {}

    public function index(Request $request)
    {
        $receipts = RawMaterialReceipt::with(['rawMaterial', 'supplier', 'receivedBy'])
            ->when($request->raw_material_id, fn($q) => $q->where('raw_material_id', $request->raw_material_id))
            ->when($request->decision, fn($q) => $q->where('decision', $request->decision))
            ->when($request->date_from, fn($q) => $q->whereDate('reception_date', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('reception_date', '<=', $request->date_to))
            ->orderByDesc('reception_date')
            ->paginate(20);

        return response()->json($receipts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'raw_material_id'       => 'required|exists:raw_materials,id',
            'supplier_id'           => 'nullable|exists:suppliers,id',
            'supplier_name'         => 'required|string|max:255',
            'supplier_lot'          => 'nullable|string|max:100',
            'quantity'              => 'required|numeric|min:0.001',
            'unit'                  => 'required|string',
            'unit_cost'             => 'nullable|numeric|min:0',
            'reception_date'        => 'required|date',
            'dluo_date'             => 'nullable|date',
            'temperature'           => 'nullable|numeric',
            'humidity'              => 'nullable|numeric|min:0|max:100',
            'visual_check'          => 'required|in:conforme,non_conforme',
            'smell_check'           => 'required|in:conforme,non_conforme',
            'refractometer_brix'    => 'nullable|numeric',
            'refractometer_humidity'=> 'nullable|numeric',
            'decision'              => 'required|in:accepted,refused,accepted_reserve',
            'storage_zone'          => 'nullable|string|max:50',
            'storage_location'      => 'nullable|string|max:50',
            'notes'                 => 'nullable|string',
        ]);

        $receipt = DB::transaction(function () use ($request) {
            $number = $this->generateReceiptNumber();

            $receipt = RawMaterialReceipt::create([
                ...$request->validated(),
                'receipt_number' => $number,
                'received_by'    => $request->user()->id,
            ]);

            // Mise à jour stock si accepté
            $receipt->applyToStock();

            return $receipt->load(['rawMaterial', 'supplier', 'receivedBy']);
        });

        return response()->json($receipt, 201);
    }

    public function show(RawMaterialReceipt $rawMaterialReceipt)
    {
        return response()->json($rawMaterialReceipt->load(['rawMaterial', 'supplier', 'receivedBy']));
    }

    // Génère la fiche A4 + étiquette stock via DocumentService
    public function generateDocuments(RawMaterialReceipt $rawMaterialReceipt, Request $request)
    {
        $type = $request->input('type', 'fiche_reception'); // fiche_reception | etiquette_stock

        $doc = $this->documentService->generateReception(
            $type,
            $rawMaterialReceipt,
            $request->user()->id
        );

        return response()->json([
            'message'  => 'Document généré.',
            'document' => $doc,
            'url'      => url('storage/' . $doc->file_path),
        ]);
    }

    private function generateReceiptNumber(): string
    {
        $date  = now()->format('Ymd');
        $count = RawMaterialReceipt::whereDate('created_at', today())->count() + 1;
        return sprintf('REC-%s-%03d', $date, $count);
    }
}