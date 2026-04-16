<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawMaterialReceipt extends Model
{
    protected $fillable = ['receipt_number', 'raw_material_id', 'supplier_id', 'supplier_name', 'supplier_lot', 'quantity', 'unit', 'unit_cost', 'reception_date', 'dluo_date', 'temperature', 'humidity', 'visual_check', 'smell_check', 'refractometer_brix', 'refractometer_humidity', 'decision', 'storage_zone', 'storage_location', 'notes', 'received_by'];

    protected $casts = [
        'reception_date' => 'date',
        'dluo_date' => 'date',
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
    ];

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // Après réception acceptée → met à jour le stock MP
    public function applyToStock(): void
    {
        if ($this->decision === 'accepted' || $this->decision === 'accepted_reserve') {
            $rawMaterial = $this->rawMaterial;
            $stockBefore = (float) $rawMaterial->quantity_in_stock;
            $stockAfter  = $stockBefore + (float) $this->quantity;

            $rawMaterial->increment('quantity_in_stock', $this->quantity);

            StockMovement::create([
                'movable_type' => RawMaterial::class,
                'movable_id'   => $this->raw_material_id,
                'type'         => 'in',
                'quantity'     => $this->quantity,
                'unit'         => $this->unit,
                'stock_before' => $stockBefore,
                'stock_after'  => $stockAfter,
                'reason'       => "Réception {$this->receipt_number} — Lot fournisseur : " . ($this->supplier_lot ?? 'N/A'),
                'source_type'  => RawMaterialReceipt::class,
                'source_id'    => $this->id,
                'user_id'      => $this->received_by,
            ]);
        }
    }
}
