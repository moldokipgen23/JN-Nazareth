<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Download extends Model
{
    protected $fillable = [
        'title',
        'category',
        'file_path',
        'file_type',
        'file_size',
        'is_published',
        'sort_order',
        'uploaded_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public const CATEGORIES = [
        'Notice', 'Circular', 'Admission Form', 'Syllabus', 'Textbook', 'Result', 'Other',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
