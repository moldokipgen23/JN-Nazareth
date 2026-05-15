<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramItem extends Model
{
    protected $table = 'program_items';

    protected $fillable = [
        'path', 'caption', 'title', 'description',
        'category', 'activity_date', 'sort_order', 'active',
    ];

    protected $casts = [
        'active'        => 'boolean',
        'activity_date' => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
