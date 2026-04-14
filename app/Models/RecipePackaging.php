<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipePackaging extends Model
{
    protected $table = 'recipe_packaging';
    protected $fillable = [
        'recipe_id', 'packet_size_g', 'packet_label', 'film_type',
        'film_width_mm', 'film_length_mm', 'machine_capacity_per_hour', 'is_default', 'notes'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'packet_size_g' => 'decimal:2',
        'machine_capacity_per_hour' => 'decimal:2',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}