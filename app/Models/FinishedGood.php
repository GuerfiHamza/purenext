<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinishedGood extends Model
{
    protected $fillable = [
        'product_name', 'brand_id', 'production_run_id', 'batch_number',
        'packet_size_g', 'packet_label', 'quantity_in_stock', 'min_stock_alert',
        'production_date', 'expiry_date'
    ];

    protected $casts = [
        'packet_size_g' => 'decimal:2',
        'production_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function productionRun(): BelongsTo
    {
        return $this->belongsTo(ProductionRun::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(SaleOrderItem::class);
    }

    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'movable');
    }

    public function isLowStock(): bool
    {
        return $this->quantity_in_stock <= $this->min_stock_alert;
    }
}