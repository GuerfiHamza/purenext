<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BoxMovement extends Model
{
    protected $fillable = [
        'packaging_box_id', 'type', 'quantity',
        'stock_before', 'stock_after', 'reason',
        'source_type', 'source_id', 'user_id',
    ];

    public function packagingBox(): BelongsTo
    {
        return $this->belongsTo(PackagingBox::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}