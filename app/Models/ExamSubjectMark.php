<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamSubjectMark extends Model
{
    protected $fillable = ['exam_id', 'class', 'subject', 'full_marks', 'pass_marks'];

    protected $casts = [
        'full_marks' => 'decimal:2',
        'pass_marks' => 'decimal:2',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Resolve full_marks/pass_marks for a (exam, class, subject) trio.
     * Lookup order:
     *   1. exam_subject_marks row (admin set per exam)
     *   2. class_subjects row (class default override)
     *   3. fallback 100 / 33
     */
    public static function resolveMarks(int $examId, string $class, string $subject, ?int $academicYearId = null): array
    {
        $row = static::where('exam_id', $examId)
            ->where('class', $class)
            ->where('subject', $subject)
            ->first();

        if ($row) {
            return [(float) $row->full_marks, (float) $row->pass_marks];
        }

        // Fall back to class_subjects (per-class override) — needs the year + subject id
        if ($academicYearId) {
            $cs = ClassSubject::where('academic_year_id', $academicYearId)
                ->where('class', $class)
                ->whereHas('subject', fn ($q) => $q->where('name', $subject))
                ->first();

            if ($cs && $cs->full_marks !== null) {
                return [(float) $cs->full_marks, (float) ($cs->pass_marks ?? 33)];
            }
        }

        return [100.0, 33.0];
    }
}
