<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    protected $fillable = [
    'order_number', 'client_id', 'client_type', 'client_name', 'client_phone', 'client_email',
    'client_address', 'client_rc', 'client_nif', 'client_nis', 'client_ai',
    'order_date', 'delivery_date', 'status',
    'total_amount', 'commercial_id', 'notes'
];

    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function commercial(): BelongsTo
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleOrderItem::class);
    }

    public function recalculateTotal(): void
    {
        $this->total_amount = $this->items()->sum('total_price');
        $this->save();
    }
    public function client(): BelongsTo
{
    return $this->belongsTo(Client::class);
}
}