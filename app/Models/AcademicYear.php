<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AcademicYear extends Model
{
    protected $fillable = [
        'name',
        'starts_on',
        'ends_on',
        'is_active',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'is_active' => 'boolean',
    ];

    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function promotionRule(): HasOne
    {
        return $this->hasOne(PromotionRule::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function current(): ?self
    {
        return static::active()->first() ?? static::orderByDesc('id')->first();
    }

    public static function activeOrCreate(?string $name = null): self
    {
        $current = static::current();
        if ($current) {
            return $current;
        }

        return static::create([
            'name' => trim((string) ($name ?: '2025-26')),
            'is_active' => true,
        ]);
    }
}
