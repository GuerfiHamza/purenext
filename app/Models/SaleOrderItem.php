<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleOrderItem extends Model
{
protected $fillable = [
    'sales_order_id', 'finished_good_id', 'packaging_box_id',
    'item_type', 'quantity', 'unit_price'
];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function finishedGood(): BelongsTo
    {
        return $this->belongsTo(FinishedGood::class);
    }
    public function packagingBox(): BelongsTo
{
    return $this->belongsTo(PackagingBox::class);
}
}