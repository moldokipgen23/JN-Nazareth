<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamQuestion extends Model
{
    protected $fillable = [
        'academic_year_id', 'exam_id', 'submitted_by',
        'class', 'section', 'subject',
        'question_text', 'file_path', 'file_name', 'notes', 'status',
    ];

    public function exam(): BelongsTo     { return $this->belongsTo(Exam::class); }
    public function academicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class); }
    public function submitter(): BelongsTo { return $this->belongsTo(User::class, 'submitted_by'); }
}
