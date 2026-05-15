<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BannerSlide extends Model
{
    protected $table = 'banner_slides';

    protected $fillable = [
        'image',
        'title',
        'subtitle',
        'button_text',
        'button_link',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('active', true)->orderBy('sort_order');
    }
}
