<?php

namespace App\Services;

use App\Models\Document;
use App\Models\ProductionRun;
use App\Models\Recipe;
use App\Models\SalesOrder;
use App\Models\Supplier;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DocumentService
{
    private ?array $settings = null;

    public function generate(string $type, int $documentableId, int $userId, array $extra = []): Document
    {
        // ── Vérifie si déjà généré ────────────────────────────────
        // Pour bon_commande on ne dédoublonne pas (même fournisseur,
        // commandes différentes à des dates différentes)
        // Facture et BL toujours régénérés (données peuvent changer)
        $alwaysRegenerate = ['facture', 'bon_livraison'];

        if (!in_array($type, ['bon_commande', ...$alwaysRegenerate])) {
            $existing = Document::where('type', $type)->where('documentable_id', $documentableId)->where('documentable_type', $this->resolveDocumentableClass($type))->first();

            if ($existing) {
                if ($existing->file_path && Storage::disk('public')->exists($existing->file_path)) {
                    return $existing;
                }
                $existing->delete();
            }
        } elseif (in_array($type, $alwaysRegenerate)) {
            // Supprimer l'ancien et régénérer
            Document::where('type', $type)
                ->where('documentable_id', $documentableId)
                ->where('documentable_type', $this->resolveDocumentableClass($type))
                ->each(function ($doc) {
                    if ($doc->file_path) {
                        Storage::disk('public')->delete($doc->file_path);
                    }
                    $doc->delete();
                });
        }

        // ── Génération normale ────────────────────────────────────
        [$documentable, $data, $view] = match ($type) {
            'rapport_production' => $this->prepareProductionReport($documentableId),
            'certificat_conformite' => $this->prepareCertificat($documentableId, $extra),
            'fiche_technique' => $this->prepareFicheTechnique($documentableId),
            'facture' => $this->prepareFacture($documentableId),
            'bon_livraison' => $this->prepareBonLivraison($documentableId),
            'bon_commande' => $this->prepareBonCommande($documentableId, $extra),
            default => throw new \InvalidArgumentException("Type inconnu : $type"),
        };

        $reference = $this->generateReference($type);
        $settings = $this->getSettings();

        $pdf = Pdf::loadView("documents.$view", [
            'data' => $data,
            'reference' => $reference,
            'date' => now()->format('d/m/Y'),
            'settings' => $settings,
        ])->setPaper('a4');

        $filename = "documents/{$reference}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        return Document::create([
            'type' => $type,
            'reference' => $reference,
            'documentable_type' => get_class($documentable),
            'documentable_id' => $documentable->id,
            'data' => $data,
            'file_path' => $filename,
            'generated_by' => $userId,
        ]);
    }

    public function generateReception(string $type, \App\Models\RawMaterialReceipt $receipt, int $userId): \App\Models\Document
    {
        $existing = Document::where('type', $type)->where('documentable_type', \App\Models\RawMaterialReceipt::class)->where('documentable_id', $receipt->id)->first();

        if ($existing) {
            if ($existing->file_path && Storage::disk('public')->exists($existing->file_path)) {
                return $existing;
            }
            // Fichier supprimé du disque → supprime l'entrée et régénère
            $existing->delete();
        }

        // ── Génération normale ────────────────────────────────────
        $settings = $this->getSettings();
        $reference = $this->generateReference($type);

        $data = [
            'receipt_number' => $receipt->receipt_number,
            'reception_date' => $receipt->reception_date->format('d/m/Y'),
            'product' => $receipt->rawMaterial->name,
            'sku' => $receipt->rawMaterial->sku ?? '—',
            'supplier_name' => $receipt->supplier_name,
            'supplier_lot' => $receipt->supplier_lot ?? '—',
            'quantity' => $receipt->quantity,
            'unit' => $receipt->unit,
            'unit_cost' => $receipt->unit_cost ? number_format($receipt->unit_cost, 2, ',', ' ') . ' DA' : '—',
            'dluo_date' => $receipt->dluo_date?->format('d/m/Y') ?? '—',
            'temperature' => $receipt->temperature ? $receipt->temperature . ' °C' : '—',
            'humidity' => $receipt->humidity ? $receipt->humidity . ' %' : '—',
            'visual_check' => $receipt->visual_check,
            'smell_check' => $receipt->smell_check,
            'refractometer_brix' => $receipt->refractometer_brix ?? null,
            'refractometer_humidity' => $receipt->refractometer_humidity ?? null,
            'decision' => $receipt->decision,
            'storage_zone' => $receipt->storage_zone ?? '—',
            'storage_location' => $receipt->storage_location ?? '—',
            'notes' => $receipt->notes,
            'received_by' => $receipt->receivedBy->name ?? '—',
        ];

        $view = $type === 'etiquette_stock' ? 'etiquette_stock' : 'fiche_reception';

        $paperSize = $type === 'etiquette_stock' ? [100, 150] : 'a4';
        $orientation = $type === 'etiquette_stock' ? 'landscape' : 'portrait';

        $pdf = Pdf::loadView("documents.$view", [
            'data' => $data,
            'reference' => $reference,
            'date' => now()->format('d/m/Y'),
            'settings' => $settings,
        ]);

        if ($type === 'etiquette_stock') {
            $pdf->setPaper([0, 0, 283.46, 425.2], 'landscape'); // 10x15cm en points
        } else {
            $pdf->setPaper('a4');
        }

        $filename = "documents/{$reference}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        // Ajoute aussi dans generateReference() :
        // 'fiche_reception' => 'REC', 'etiquette_stock' => 'ETQ'

        return \App\Models\Document::create([
            'type' => $type,
            'reference' => $reference,
            'documentable_type' => \App\Models\RawMaterialReceipt::class,
            'documentable_id' => $receipt->id,
            'data' => $data,
            'file_path' => $filename,
            'generated_by' => $userId,
        ]);
    }
    // ── Helper pour résoudre la classe selon le type ──────────────
    private function resolveDocumentableClass(string $type): string
    {
        return match ($type) {
            'rapport_production', 'certificat_conformite' => \App\Models\ProductionRun::class,
            'fiche_technique' => \App\Models\Recipe::class,
            'facture', 'bon_livraison' => \App\Models\SalesOrder::class,
            'bon_commande' => \App\Models\Supplier::class,
            default => throw new \InvalidArgumentException("Type inconnu : $type"),
        };
    }

    // ── Settings ──────────────────────────────────────────────────

    private function getSettings(): array
    {
        if ($this->settings !== null) {
            return $this->settings;
        }

        $defaults = [
            'company_name' => 'PURENEXT SARL',
            'company_address' => 'Alger, Algérie',
            'company_phone' => '+213 549 90 42 01',
            'company_email' => 'contact@purenext.dz',
            'company_website' => 'www.purenext.dz',
            'company_rc' => '',
            'company_nif' => '',
            'company_nis' => '',
            'company_ai' => '',
            'company_rib' => '',
            'company_bank' => '',
            'invoice_tva_rate' => '19',
            'invoice_tva_enabled' => '1',
            'invoice_prefix' => 'FACT',
            'invoice_payment' => 'Paiement à 30 jours.',
            'invoice_notes' => 'Merci pour votre confiance.',
            'delivery_notes' => 'Marchandise voyageant aux risques et périls du destinataire.',
            'po_prefix' => 'BC',
            'po_notes' => 'Veuillez confirmer réception de ce bon de commande.',
        ];

        $rows = Setting::all()->pluck('value', 'key')->toArray();

        $this->settings = array_merge($defaults, $rows);

        return $this->settings;
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function fmtAmount(float $amount): string
    {
        return number_format($amount, 2, ',', ' ');
    }

    // ── Production ────────────────────────────────────────────────

    private function prepareProductionReport(int $id): array
    {
        $run = ProductionRun::with(['recipe', 'operator', 'packaging'])->findOrFail($id);

        $data = [
            'lot_number' => $run->lot_number ?? $run->batch_number,
            'batch_number' => $run->batch_number,
            'recipe' => $run->recipe->name,
            'input_qty_kg' => $run->input_qty_kg . ' kg',
            'output_packets_estimated' => $run->output_packets_estimated . ' unités',
            'output_packets_actual' => $run->output_packets_actual ? $run->output_packets_actual . ' unités' : '—',
            'loss_percentage' => $run->loss_actual_percentage ? $run->loss_actual_percentage . ' %' : '—',
            'packaging' => optional($run->packaging)->packet_label ?? '—',
            'packet_size' => optional($run->packaging)->packet_size_g ? optional($run->packaging)->packet_size_g . ' g' : '—',
            'started_at' => $run->started_at ? Carbon::parse($run->started_at)->format('d/m/Y H:i') : '—',
            'finished_at' => $run->finished_at ? Carbon::parse($run->finished_at)->format('d/m/Y H:i') : '—',
            'operator' => optional($run->operator)->name ?? '—',
            'status' => $run->status,
            'notes' => $run->notes,
        ];

        return [$run, $data, 'rapport_production'];
    }
    private function prepareCertificat(int $id, array $extra = []): array
    {
        $run = ProductionRun::with(['recipe', 'packaging', 'finishedGood'])->findOrFail($id);

        // Chercher le finished_good par batch_number ou lot_number si la relation directe échoue
        $finishedGood = $run->finishedGood ?? \App\Models\FinishedGood::where('batch_number', $run->lot_number)->orWhere('batch_number', $run->batch_number)->first();

        $quantity = match (true) {
            !is_null($run->output_packets_actual) => $run->output_packets_actual . ' unités',
            !is_null($run->output_packets_estimated) => $run->output_packets_estimated . ' unités (estimé)',
            default => $run->input_qty_kg . ' kg',
        };

        $expiryDate = match (true) {
            !empty($extra['expiry_date']) => Carbon::parse($extra['expiry_date'])->format('d/m/Y'),
            !is_null($finishedGood?->expiry_date) => Carbon::parse($finishedGood->expiry_date)->format('d/m/Y'),
            $run->recipe->shelf_life_value && $run->started_at => Carbon::parse($run->started_at)->addDays($run->recipe->shelf_life_value)->format('d/m/Y'),
            default => '—',
        };

        $data = [
            'lot_number' => $run->lot_number ?? $run->batch_number,
            'batch_number' => $run->batch_number,
            'product' => $run->recipe->name,
            'packaging' => optional($run->packaging)->packet_label ?? '—',
            'quantity' => $quantity,
            'production_date' => $run->started_at ? Carbon::parse($run->started_at)->format('d/m/Y') : '—',
            'expiry_date' => $expiryDate,
            'shelf_life' => $run->recipe->shelf_life_value ? "{$run->recipe->shelf_life_value} {$run->recipe->shelf_life_unit}" : '—',
            'operator' => optional($run->operator)->name ?? '—',
        ];

        return [$run, $data, 'certificat_conformite'];
    }

    private function prepareFicheTechnique(int $id): array
    {
        $recipe = Recipe::with('ingredients.rawMaterial')->findOrFail($id);

        $data = [
            'name' => $recipe->name,
            'description' => $recipe->description,
            'shelf_life' => $recipe->shelf_life_value ? "{$recipe->shelf_life_value} {$recipe->shelf_life_unit}" : '—',
            'ingredients' => $recipe->ingredients
                ->map(
                    fn($i) => [
                        'name' => $i->rawMaterial->name,
                        'quantity' => $i->quantity,
                        'unit' => $i->unit,
                    ],
                )
                ->toArray(),
            'yield' => $recipe->yield ?? '—',
        ];

        return [$recipe, $data, 'fiche_technique'];
    }

    // ── Commandes ─────────────────────────────────────────────────

    private function prepareFacture(int $id): array
    {
        \Log::info('prepareFacture id: ' . $id);
        $order = SalesOrder::with(['items.finishedGood', 'items.packagingBox.finishedGood', 'commercial'])->findOrFail($id);
        \Log::info('order_number: ' . $order->order_number);
        // ...
        $settings = $this->getSettings();

        $subtotal = (float) $order->total_amount;
        $tvaEnabled = ($settings['invoice_tva_enabled'] ?? '1') === '1';
        $tvaRate = (float) ($settings['invoice_tva_rate'] ?? 19) / 100;
        $tvaAmount = $tvaEnabled ? $subtotal * $tvaRate : 0;
        $totalTTC = $subtotal + $tvaAmount;
        $prefix = $settings['invoice_prefix'] ?? 'FACT';

        $data = [
            'invoice_number' => "{$prefix}-{$order->order_number}",
            'order_number' => $order->order_number,
            'order_date' => $order->order_date->format('d/m/Y'),
            'delivery_date' => $order->delivery_date?->format('d/m/Y') ?? '—',
            'client_name' => $order->client_name,
            'client_phone' => $order->client_phone ?? '—',
            'client_email' => $order->client_email ?? '—',
            'client_address' => $order->client_address ?? '—',
            'client_type' => $order->client_type ?? 'particulier',
            'client_rc' => $order->client_rc ?? null,
            'client_nif' => $order->client_nif ?? null,
            'client_nis' => $order->client_nis ?? null,
            'client_ai' => $order->client_ai ?? null,
            'commercial' => optional($order->commercial)->name ?? '—',
            'notes' => $order->notes,
            'subtotal' => $this->fmtAmount($subtotal),
            'tva_enabled' => $tvaEnabled,
            'tva_rate' => $settings['invoice_tva_rate'] ?? '19',
            'tva_amount' => $this->fmtAmount($tvaAmount),
            'total_ttc' => $this->fmtAmount($totalTTC),
            'payment_terms' => $settings['invoice_payment'] ?? '',
            'items' => $order->items
                ->map(
                    fn($i) => [
                        'product' => $i->item_type === 'box' ? ($i->packagingBox?->name ?? '—') . ($i->packagingBox?->finishedGood?->product_name ? ' — ' . $i->packagingBox->finishedGood->product_name : '') : optional($i->finishedGood)->product_name ?? '—',
                        'lot' => $i->item_type === 'box' ? $i->packagingBox?->label ?? '—' : optional($i->finishedGood)->batch_number ?? '—',
                        'format' => $i->item_type === 'box' ? $i->packagingBox?->units_per_box . ' unités/boite' : optional($i->finishedGood)->packet_label ?? '—',
                        'quantity' => $i->quantity,
                        'unit_price' => $this->fmtAmount((float) $i->unit_price),
                        'total_price' => $this->fmtAmount((float) $i->quantity * (float) $i->unit_price),
                    ],
                )
                ->toArray(),
        ];

        return [$order, $data, 'facture'];
    }

    private function prepareBonLivraison(int $id): array
    {
        $order = SalesOrder::with(['items.finishedGood', 'items.packagingBox.finishedGood', 'commercial'])->findOrFail($id);
        $settings = $this->getSettings();

        $data = [
            'bl_number' => 'BL-' . $order->order_number,
            'order_number' => $order->order_number,
            'order_date' => $order->order_date->format('d/m/Y'),
            'delivery_date' => $order->delivery_date?->format('d/m/Y') ?? '—',
            'client_name' => $order->client_name,
            'client_phone' => $order->client_phone ?? '—',
            'client_email' => $order->client_email ?? '—',
            'client_address' => $order->client_address ?? '—',
            'client_type' => $order->client_type ?? 'particulier',
            'client_rc' => $order->client_rc ?? null,
            'client_nif' => $order->client_nif ?? null,
            'client_nis' => $order->client_nis ?? null,
            'client_ai' => $order->client_ai ?? null,
            'commercial' => optional($order->commercial)->name ?? '—',
            'notes' => $order->notes,
            'delivery_notes' => $settings['delivery_notes'] ?? '',
            'items' => $order->items
                ->map(
                    fn($i) => [
                        'product' => $i->item_type === 'box' ? ($i->packagingBox?->name ?? '—') . ($i->packagingBox?->finishedGood?->product_name ? ' — ' . $i->packagingBox->finishedGood->product_name : '') : optional($i->finishedGood)->product_name ?? '—',
                        'lot' => $i->item_type === 'box' ? $i->packagingBox?->label ?? '—' : optional($i->finishedGood)->batch_number ?? '—',
                        'format' => $i->item_type === 'box' ? $i->packagingBox?->units_per_box . ' unités/boite' : optional($i->finishedGood)->packet_label ?? '—',
                        'quantity' => $i->quantity,
                    ],
                )
                ->toArray(),
        ];

        return [$order, $data, 'bon_livraison'];
    }

    private function prepareBonCommande(int $id, array $extra): array
    {
        $supplier = Supplier::findOrFail($id);
        $settings = $this->getSettings();

        $items = $extra['items'] ?? [];
        $total = collect($items)->sum(fn($i) => ($i['quantity_needed'] ?? 0) * ($i['unit_price'] ?? 0));

        $data = [
            'po_number' => ($settings['po_prefix'] ?? 'BC') . '-' . now()->format('Ymd') . '-' . str_pad(Document::whereDate('created_at', today())->where('type', 'bon_commande')->count() + 1, 3, '0', STR_PAD_LEFT),
            'supplier_name' => $supplier->name,
            'supplier_phone' => $supplier->phone ?? '—',
            'supplier_email' => $supplier->email ?? '—',
            'supplier_country' => $supplier->country ?? '—',
            'contact_name' => $supplier->contact_name ?? '—',
            'delivery_date' => $extra['delivery_date'] ?? '—',
            'notes' => $extra['notes'] ?? null,
            'po_notes' => $settings['po_notes'] ?? '',
            'has_prices' => collect($items)->contains(fn($i) => !empty($i['unit_price'])),
            'items' => $items,
            'total' => $this->fmtAmount($total),
        ];

        return [$supplier, $data, 'bon_commande'];
    }

    // ── Référence ─────────────────────────────────────────────────

    private function generateReference(string $type): string
    {
        $settings = $this->getSettings();

        $prefix = match ($type) {
            'rapport_production' => 'RPT',
            'certificat_conformite' => 'CRT',
            'fiche_technique' => 'FTK',
            'facture' => $settings['invoice_prefix'] ?? 'FAC',
            'bon_livraison' => 'BDL',
            'bon_commande' => $settings['po_prefix'] ?? 'BDC',
            'fiche_reception' => 'REC', // ← nouveau
            'etiquette_stock' => 'ETQ', // ← nouveau
            default => strtoupper(substr($type, 0, 3)),
        };

        $date = now()->format('Ymd');
        $count = \App\Models\Document::whereDate('created_at', today())->where('type', $type)->count() + 1;

        return sprintf('%s-%s-%03d', $prefix, $date, $count);
    }
}
