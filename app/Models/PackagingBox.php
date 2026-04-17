<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackagingBox extends Model
{
    protected $fillable = [
        'finished_good_id', 'name', 'units_per_box', 'label', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function finishedGood(): BelongsTo
    {
        return $this->belongsTo(FinishedGood::class);
    }

    public function stock(): HasOne
    {
        return $this->hasOne(BoxStock::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(BoxMovement::class);
    }

    // Helpers
    public function quantityInStock(): int
    {
        return $this->stock?->quantity_in_stock ?? 0;
    }

    public function isLowStock(): bool
    {
        return $this->quantityInStock() <= ($this->stock?->min_stock_alert ?? 0);
    }
}