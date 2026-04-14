<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductionRun extends Model
{
    protected $fillable = [
        'batch_number', 'recipe_id', 'recipe_packaging_id', 'operator_id',
        'input_qty_kg', 'output_packets_estimated', 'output_packets_actual',
        'loss_actual_percentage', 'status', 'started_at', 'finished_at', 'notes'
    ];

    protected $casts = [
        'input_qty_kg' => 'decimal:3',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function packaging(): BelongsTo
    {
        return $this->belongsTo(RecipePackaging::class, 'recipe_packaging_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(ProductionRunIngredient::class);
    }

    public function finishedGood(): HasOne
    {
        return $this->hasOne(FinishedGood::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}