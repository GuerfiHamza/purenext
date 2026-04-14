<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionRunIngredient extends Model
{
    protected $fillable = ['production_run_id', 'raw_material_id', 'quantity_consumed', 'unit'];

    protected $casts = ['quantity_consumed' => 'decimal:4'];

    public function productionRun(): BelongsTo
    {
        return $this->belongsTo(ProductionRun::class);
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }
}