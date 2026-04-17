<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DocumentService;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function __construct(private DocumentService $service) {}

    public function index(Request $request)
    {
        $docs = Document::with('generatedBy')->when($request->type, fn($q) => $q->where('type', $request->type))->orderByDesc('created_at')->paginate(20);

        return response()->json($docs);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:rapport_production,certificat_conformite,fiche_technique,facture,bon_livraison,bon_commande',
            'documentable_id' => 'required|integer',
            'items' => 'required_if:type,bon_commande|array',
            'items.*.name' => 'required_if:type,bon_commande|string',
            'items.*.quantity_needed' => 'required_if:type,bon_commande|numeric|min:0',
            'items.*.unit' => 'required_if:type,bon_commande|string',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'delivery_date' => 'nullable|string',
            'expiry_date' => 'nullable|date',  
            'notes' => 'nullable|string',
        ]);

        $extra = $request->only(['items', 'delivery_date', 'notes', 'expiry_date']);

        $wasExisting = false;

        // Vérifie avant de générer si le doc existe déjà
        if ($request->type !== 'bon_commande') {
            $wasExisting = Document::where('type', $request->type)->where('documentable_id', $request->documentable_id)->exists();
        }

        $doc = $this->service->generate($request->type, $request->documentable_id, $request->user()->id, $extra);

        return response()->json(
            [
                'message' => $wasExisting ? 'Document existant retourné.' : 'Document généré avec succès.',
                'document' => $doc,
                'url' => url('storage/' . $doc->file_path),
                'existing' => $wasExisting,
            ],
            $wasExisting ? 200 : 201,
        );
    }

    public function show(Document $document)
    {
        return response()->json($document->load('generatedBy'));
    }

    public function download(Document $document)
    {
        abort_unless(Storage::disk('public')->exists($document->file_path), 404, 'Fichier introuvable.');

        return Storage::disk('public')->download($document->file_path, "{$document->reference}.pdf");
    }

    public function destroy(Document $document)
    {
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();

        return response()->json(['message' => 'Document supprimé.']);
    }
}
