<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['class', 'name', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function namesForClass(string $class): array
    {
        return static::active()
            ->where('class', $class)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
    }
}
