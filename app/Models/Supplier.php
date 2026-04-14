<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = ['name', 'contact_name', 'email', 'phone', 'country', 'lead_time_days', 'notes', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function rawMaterials(): HasMany
    {
        return $this->hasMany(RawMaterial::class);
    }
}