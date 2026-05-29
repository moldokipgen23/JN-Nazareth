<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassSubject extends Model
{
    protected $fillable = [
        'class', 'subject_id', 'academic_year_id', 'section',
        'is_optional', 'full_marks', 'pass_marks', 'grade_only',
    ];

    protected $casts = [
        'is_optional' => 'boolean',
        'grade_only'  => 'boolean',
        'full_marks'  => 'decimal:2',
        'pass_marks'  => 'decimal:2',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public static function subjectsForClass(string $class, ?string $section = null, ?int $yearId = null): array
    {
        $yearId ??= AcademicYear::current()?->id;
        if (!$yearId) return [];

        return static::where('academic_year_id', $yearId)
            ->where('class', $class)
            ->when($section, fn ($q) => $q->where(function ($q) use ($section) {
                $q->whereNull('section')->orWhere('section', $section);
            }))
            ->with('subject')
            ->get()
            ->toArray();
    }
}
