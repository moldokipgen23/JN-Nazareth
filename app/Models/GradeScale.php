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
            ['name' => 'A1', 'min_percent' => 91.00, 'max_percent' => 100.00, 'grade_point' => 10.00],
            ['name' => 'A2', 'min_percent' => 81.00, 'max_percent' => 90.99,  'grade_point' => 9.00],
            ['name' => 'B1', 'min_percent' => 71.00, 'max_percent' => 80.99,  'grade_point' => 8.00],
            ['name' => 'B2', 'min_percent' => 61.00, 'max_percent' => 70.99,  'grade_point' => 7.00],
            ['name' => 'C1', 'min_percent' => 51.00, 'max_percent' => 60.99,  'grade_point' => 6.00],
            ['name' => 'C2', 'min_percent' => 41.00, 'max_percent' => 50.99,  'grade_point' => 5.00],
            ['name' => 'D',  'min_percent' => 33.00, 'max_percent' => 40.99,  'grade_point' => 4.00],
            ['name' => 'E',  'min_percent' => 0.00,  'max_percent' => 32.99,  'grade_point' => 0.00],
        ];
    }
}
