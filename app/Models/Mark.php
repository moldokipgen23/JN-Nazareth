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

    /**
     * Canonical per-subject status. Single source of truth used by:
     *   - Teacher portal subject list (status pills)
     *   - Admin /admin/marks "Submission Status per Subject" panel
     *   - Admin /admin/marks?view=summary class cards (each subject)
     *
     * Same rule everywhere — a subject is "approved" only when every
     * enrolled student in that class+section has an approved mark.
     *
     * Returns:
     *   state           : 'approved' | 'pending' | 'rejected' | 'partial' | 'draft' | 'not_started'
     *   label           : short human label e.g. "12 awaiting approval"
     *   icon            : single-char emoji for the pill
     *   color, bg       : hex colours for the pill
     *   enrolled        : active students in class+section
     *   entered/submitted/approved/rejected/pending/missing : counts
     */
    public static function subjectStatus(int $examId, string $class, string $section, string $subject, int $yearId): array
    {
        $enrolled = StudentEnrollment::forActiveYear()->active()
            ->where('class', $class)->where('section', $section)
            ->count();

        $agg = self::where('academic_year_id', $yearId)
            ->where('exam_id', $examId)
            ->where('class', $class)->where('section', $section)
            ->where('subject', $subject)
            ->selectRaw('
                SUM(CASE WHEN total_marks IS NOT NULL THEN 1 ELSE 0 END) as entered,
                SUM(CASE WHEN submitted_at IS NOT NULL THEN 1 ELSE 0 END) as submitted,
                SUM(CASE WHEN approved_at IS NOT NULL THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN rejected_at IS NOT NULL AND submitted_at IS NULL THEN 1 ELSE 0 END) as rejected
            ')->first();

        $entered   = (int) ($agg->entered   ?? 0);
        $submitted = (int) ($agg->submitted ?? 0);
        $approved  = (int) ($agg->approved  ?? 0);
        $rejected  = (int) ($agg->rejected  ?? 0);
        $pending   = max(0, $submitted - $approved);
        $missing   = max(0, $enrolled - $entered);

        if ($rejected > 0) {
            $state = 'rejected';
            $label = $rejected.' sent back';
        } elseif ($enrolled > 0 && $approved >= $enrolled) {
            $state = 'approved';
            $label = 'Approved';
        } elseif ($pending > 0) {
            $state = 'pending';
            $label = $pending.' awaiting approval';
        } elseif ($approved > 0 && $missing > 0) {
            $state = 'partial';
            $label = $missing.' student'.($missing === 1 ? '' : 's').' missing';
        } elseif ($entered > 0 && $submitted === 0) {
            $state = 'draft';
            $label = $entered.' draft'.($entered === 1 ? '' : 's');
        } else {
            $state = 'not_started';
            $label = 'Not started';
        }

        $styles = [
            'approved'    => ['#15803d', '#f0fdf4', '✅'],
            'pending'     => ['#1d4ed8', '#dbeafe', '⏳'],
            'rejected'    => ['#b91c1c', '#fee2e2', '↩'],
            'partial'     => ['#92400e', '#fef3c7', '⚠️'],
            'draft'       => ['#9a3412', '#ffedd5', '📝'],
            'not_started' => ['#64748b', '#f1f5f9', '○'],
        ];
        [$color, $bg, $icon] = $styles[$state];

        return compact(
            'state', 'label', 'icon', 'color', 'bg',
            'enrolled', 'entered', 'submitted', 'approved', 'rejected', 'pending', 'missing'
        );
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
