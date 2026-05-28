<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = [
        'academic_year_id',
        'name',
        'type',
        'code',
        'starts_on',
        'ends_on',
        'submission_starts_on',
        'submission_ends_on',
        'sort_order',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'starts_on'           => 'date',
        'ends_on'             => 'date',
        'submission_starts_on' => 'date',
        'submission_ends_on'   => 'date',
        'is_active'           => 'boolean',
    ];

    public const TYPES = ['Formative', 'Summative', 'Final', 'Custom'];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForActiveYear(Builder $query): Builder
    {
        $year = AcademicYear::current();

        return $year
            ? $query->where('academic_year_id', $year->id)
            : $query->whereRaw('1 = 0');
    }
}
