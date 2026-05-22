<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Teacher directory record — a staff member, NOT a login account.
 * Teachers who need CMS access have a separate User account (teacher role).
 */
class Teacher extends Model
{
    protected $fillable = [
        'name',
        'designation',
        'subjects',
        'phone',
        'email',
        'photo',
        'classes',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'classes'   => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
