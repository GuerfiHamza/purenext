<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $fillable = [
        'name', 'brand_id', 'version', 'yield_unit', 'yield_qty',
        'loss_percentage', 'notes', 'technical_params', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'technical_params' => 'array',
        'loss_percentage' => 'decimal:2',
        'yield_qty' => 'decimal:3',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class)->orderBy('order');
    }

    public function packagingOptions(): HasMany
    {
        return $this->hasMany(RecipePackaging::class);
    }

    public function defaultPackaging()
    {
        return $this->packagingOptions()->where('is_default', true)->first();
    }

    public function productionRuns(): HasMany
    {
        return $this->hasMany(ProductionRun::class);
    }
    public function finishedGoods(): HasManyThrough
{
    return $this->hasManyThrough(FinishedGood::class, ProductionRun::class);
}
}