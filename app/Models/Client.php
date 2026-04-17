<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'type', 'name', 'phone', 'email', 'address',
        'rc', 'nif', 'nis', 'ai', 'notes',
    ];

    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function isSociete(): bool
    {
        return $this->type === 'societe';
    }
}