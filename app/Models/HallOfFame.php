<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HallOfFame extends Model
{
    protected $table = 'hall_of_fames';

    protected $fillable = [
        'name',
        'achievement_title',
        'description',
        'photo',
        'year',
        'video_url',
        'external_link',
        'featured',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'featured'   => 'boolean',
        'active'     => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
