<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentEnrollment extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_DROPPED = 'dropped';
    public const STATUS_TRANSFERRED = 'transferred';
    public const STATUS_GRADUATED = 'graduated';

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'class',
        'section',
        'roll_number',
        'status',
        'enrolled_on',
        'left_on',
        'remarks',
    ];

    protected $casts = [
        'enrolled_on' => 'date',
        'left_on' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeForActiveYear(Builder $query): Builder
    {
        $year = AcademicYear::current();

        return $year
            ? $query->where('academic_year_id', $year->id)
            : $query->whereRaw('1 = 0');
    }

    public static function syncFromStudent(Student $student): ?self
    {
        if (! $student->class) {
            return null;
        }

        $year = AcademicYear::activeOrCreate($student->academic_year);

        return static::updateOrCreate(
            [
                'student_id' => $student->id,
                'academic_year_id' => $year->id,
            ],
            [
                'class' => $student->class,
                'section' => $student->section ?: 'A',
                'roll_number' => $student->roll_number,
                'status' => $student->is_active ? self::STATUS_ACTIVE : self::STATUS_DROPPED,
                'enrolled_on' => $student->admission_date,
            ]
        );
    }
}
