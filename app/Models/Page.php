<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'content',
        'meta',
        'published',
        'show_in_nav',
        'meta_description',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'meta'        => 'array',
            'published'   => 'boolean',
            'show_in_nav' => 'boolean',
        ];
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    public function scopeInNav($query)
    {
        return $query->where('show_in_nav', true)->where('published', true);
    }
}
