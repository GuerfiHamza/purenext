<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type', 'title', 'message', 'data',
        'user_id', 'is_global', 'read_at'
    ];

    protected $casts = [
        'data'     => 'array',
        'read_at'  => 'datetime',
        'is_global'=> 'boolean',
    ];

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}