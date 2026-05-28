<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamQuestion extends Model
{
    protected $fillable = [
        'academic_year_id', 'exam_id', 'submitted_by', 'reviewed_by',
        'class', 'section', 'subject', 'marks',
        'question_text', 'file_path', 'file_name', 'notes', 'review_notes',
        'status', 'reviewed_at',
    ];

    protected $casts = [
        'marks'       => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function exam(): BelongsTo         { return $this->belongsTo(Exam::class); }
    public function academicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class); }
    public function submitter(): BelongsTo    { return $this->belongsTo(User::class, 'submitted_by'); }
    public function reviewer(): BelongsTo     { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function scopeForActiveYear(Builder $query): Builder
    {
        $year = AcademicYear::current();
        return $year
            ? $query->where('academic_year_id', $year->id)
            : $query->whereRaw('1 = 0');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeRevisionNeeded(Builder $query): Builder
    {
        return $query->where('status', 'revision_needed');
    }
}
