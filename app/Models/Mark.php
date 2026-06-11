<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mark extends Model
{
    protected $fillable = [
        'academic_year_id',
        'exam_id',
        'student_enrollment_id',
        'class',
        'section',
        'subject',
        'full_marks',
        'pass_marks',
        'theory_marks',
        'assignment_marks',
        'total_marks',
        'obtained_marks',
        'grade',
        'remarks',
        'submitted_at',
        'approved_at',
        'approved_by',
        'rejection_note',
        'rejected_at',
        'rejected_by',
        'entered_by',
    ];

    protected $casts = [
        'full_marks'       => 'decimal:2',
        'pass_marks'       => 'decimal:2',
        'theory_marks'     => 'decimal:2',
        'assignment_marks' => 'decimal:2',
        'total_marks'      => 'decimal:2',
        'obtained_marks'   => 'decimal:2',
        'submitted_at'     => 'datetime',
        'approved_at'      => 'datetime',
        'rejected_at'      => 'datetime',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class, 'student_enrollment_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function scopeForActiveYear(Builder $query): Builder
    {
        $year = AcademicYear::current();

        return $year
            ? $query->where('academic_year_id', $year->id)
            : $query->whereRaw('1 = 0');
    }

    public function status(): string
    {
        if ($this->obtained_marks === null && $this->total_marks === null) {
            return 'ungraded';
        }
        $marks = $this->obtained_marks ?? $this->total_marks;
        return ((float) $marks) >= ((float) $this->pass_marks) ? 'pass' : 'fail';
    }

    public function percentage(): ?float
    {
        $marks = $this->obtained_marks ?? $this->total_marks;
        if ($marks === null || ! $this->full_marks) {
            return null;
        }
        return round(((float) $marks / (float) $this->full_marks) * 100, 2);
    }

    public function computedGrade(): ?string
    {
        $pct = $this->percentage();
        if ($pct === null) {
            return null;
        }
        $grade = GradeScale::gradeFor($pct);
        return $grade?->name;
    }

    public function computedGradePoint(): ?float
    {
        $pct = $this->percentage();
        if ($pct === null) {
            return null;
        }
        $grade = GradeScale::gradeFor($pct);
        return $grade?->grade_point;
    }
}
