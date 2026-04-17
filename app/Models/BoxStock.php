<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoxStock extends Model
{
    protected $fillable = [
        'packaging_box_id', 'quantity_in_stock', 'min_stock_alert',
    ];

    public function packagingBox(): BelongsTo
    {
        return $this->belongsTo(PackagingBox::class);
    }
}