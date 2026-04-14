<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{
    protected $fillable = [
        'name', 'sku', 'brand_id', 'supplier_id', 'unit',
        'quantity_in_stock', 'min_stock_alert', 'cost_per_unit', 'notes', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'quantity_in_stock' => 'decimal:3',
        'min_stock_alert' => 'decimal:3',
        'cost_per_unit' => 'decimal:2',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->morphMany(StockMovement::class, 'movable');
    }

    public function isLowStock(): bool
    {
        return $this->quantity_in_stock <= $this->min_stock_alert;
    }
}