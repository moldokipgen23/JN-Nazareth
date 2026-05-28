<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GradeScale extends Model
{
    protected $fillable = ['name', 'min_percent', 'max_percent', 'grade_point', 'is_active'];

    protected $casts = [
        'min_percent'  => 'decimal:2',
        'max_percent'  => 'decimal:2',
        'grade_point'  => 'decimal:2',
        'is_active'    => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function gradeFor(float $percentage): ?self
    {
        return static::active()
            ->where('min_percent', '<=', $percentage)
            ->where('max_percent', '>=', $percentage)
            ->orderByDesc('grade_point')
            ->first();
    }

    public static function defaultScale(): array
    {
        return [
            ['name' => 'A+', 'min_percent' => 90, 'max_percent' => 100, 'grade_point' => 4.00],
            ['name' => 'A',  'min_percent' => 80, 'max_percent' => 89.99, 'grade_point' => 3.50],
            ['name' => 'B+', 'min_percent' => 70, 'max_percent' => 79.99, 'grade_point' => 3.00],
            ['name' => 'B',  'min_percent' => 60, 'max_percent' => 69.99, 'grade_point' => 2.50],
            ['name' => 'C+', 'min_percent' => 50, 'max_percent' => 59.99, 'grade_point' => 2.00],
            ['name' => 'C',  'min_percent' => 40, 'max_percent' => 49.99, 'grade_point' => 1.50],
            ['name' => 'D',  'min_percent' => 33, 'max_percent' => 39.99, 'grade_point' => 1.00],
            ['name' => 'F',  'min_percent' => 0,  'max_percent' => 32.99, 'grade_point' => 0.00],
        ];
    }
}
