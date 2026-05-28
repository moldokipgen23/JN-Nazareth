<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function classTeacherAssignments(): HasMany
    {
        return $this->hasMany(ClassTeacherAssignment::class, 'teacher_id');
    }

    public function subjectTeacherAssignments(): HasMany
    {
        return $this->hasMany(SubjectTeacherAssignment::class, 'teacher_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'teacher_id');
    }

    public function yearStatuses(): HasMany
    {
        return $this->hasMany(TeacherYearStatus::class);
    }

    public function statusForYear(AcademicYear $year): ?TeacherYearStatus
    {
        return $this->yearStatuses()->where('academic_year_id', $year->id)->first();
    }

    /** Class names this teacher is assigned to for the current year (any kind). */
    public function teachingClasses(): array
    {
        $year = AcademicYear::current();
        if (!$year || !$this->exists) return [];

        $assigned = collect()
            ->merge($this->classTeacherAssignments()->where('academic_year_id', $year->id)->pluck('class'))
            ->merge($this->subjectTeacherAssignments()->where('academic_year_id', $year->id)->pluck('class'))
            ->all();

        $order = array_flip(\App\Models\Student::classes());
        return collect($assigned)->filter()->unique()
            ->sortBy(fn ($c) => $order[$c] ?? 999)->values()->all();
    }

    /** Main class teacher assignments [{class, section}] for current year. */
    public function mainClassAssignments(): array
    {
        $year = AcademicYear::current();
        if (!$year || !$this->exists) return [];

        $order = array_flip(\App\Models\Student::classes());
        return $this->classTeacherAssignments()
            ->where('academic_year_id', $year->id)
            ->get(['class', 'section'])
            ->sortBy(fn ($a) => $order[$a->class] ?? 999)
            ->map(fn ($a) => ['class' => $a->class, 'section' => $a->section])
            ->values()->all();
    }

    /** Subject teacher assignments [{class, section, subject}] for current year. */
    public function subjectAssignments(): array
    {
        $year = AcademicYear::current();
        if (!$year || !$this->exists) return [];

        $order = array_flip(\App\Models\Student::classes());
        return $this->subjectTeacherAssignments()
            ->where('academic_year_id', $year->id)
            ->get(['class', 'section', 'subject'])
            ->sortBy(fn ($a) => $order[$a->class] ?? 999)
            ->map(fn ($a) => ['class' => $a->class, 'section' => $a->section, 'subject' => $a->subject])
            ->values()->all();
    }

    public function teachesClass(string $class): bool
    {
        return in_array($class, $this->teachingClasses(), true);
    }

    public function isClassTeacherFor(string $class, ?string $section = null): bool
    {
        $year = AcademicYear::current();
        if (! $year) {
            return false;
        }

        return $this->classTeacherAssignments()
            ->where('academic_year_id', $year->id)
            ->where('class', $class)
            ->when($section, fn ($query) => $query->where('section', $section))
            ->exists();
    }

    public function teachesSubject(string $class, string $section, string $subject): bool
    {
        $year = AcademicYear::current();
        if (! $year) {
            return false;
        }

        return $this->subjectTeacherAssignments()
            ->where('academic_year_id', $year->id)
            ->where('class', $class)
            ->where('section', $section)
            ->where('subject', $subject)
            ->exists();
    }
}
