<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    protected $fillable = [
        'type',
        'reference',
        'documentable_type',
        'documentable_id',
        'data',
        'file_path',
        'generated_by',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getDownloadUrlAttribute(): string
    {
        return $this->file_path
            ? url('storage/' . $this->file_path)
            : '';
    }
}