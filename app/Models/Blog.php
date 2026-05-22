<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'post_type',
        'content',
        'image',
        'published',
        'is_featured',
        'published_at',
        'author_id',
    ];

    public const TYPES = ['news', 'notice', 'announcement'];

    protected function casts(): array
    {
        return [
            'published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
