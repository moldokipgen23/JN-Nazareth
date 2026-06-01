<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DivisionRule extends Model
{
    protected $fillable = ['name', 'min_percent', 'max_percent', 'is_active'];

    protected $casts = [
        'min_percent' => 'decimal:2',
        'max_percent' => 'decimal:2',
        'is_active'   => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function divisionFor(float $percentage): ?self
    {
        return static::active()
            ->where('min_percent', '<=', $percentage)
            ->where('max_percent', '>=', $percentage)
            ->first();
    }
}
