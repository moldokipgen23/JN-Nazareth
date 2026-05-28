<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionRule extends Model
{
    protected $fillable = [
        'academic_year_id',
        'exam_id',
        'min_pass_percentage',
        'min_attendance_percentage',
        'use_cgpa',
        'min_cgpa',
        'fail_subjects_allowed',
    ];

    protected $casts = [
        'min_pass_percentage' => 'decimal:2',
        'min_attendance_percentage' => 'decimal:2',
        'use_cgpa' => 'boolean',
        'min_cgpa' => 'decimal:2',
        'fail_subjects_allowed' => 'array',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}
