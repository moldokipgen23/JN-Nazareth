<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    public const STATUS_PRESENT = 'present';
    public const STATUS_ABSENT  = 'absent';
    public const STATUS_LATE    = 'late';
    public const STATUS_EXCUSED = 'excused';

    public const APPROVAL_PENDING = 'pending';
    public const APPROVAL_APPROVED = 'approved';
    public const APPROVAL_REJECTED = 'rejected';

    public const STATUSES = [
        self::STATUS_PRESENT,
        self::STATUS_ABSENT,
        self::STATUS_LATE,
        self::STATUS_EXCUSED,
    ];

    public const APPROVAL_STATUSES = [
        self::APPROVAL_PENDING,
        self::APPROVAL_APPROVED,
        self::APPROVAL_REJECTED,
    ];

    protected $fillable = [
        'academic_year_id',
        'student_enrollment_id',
        'class',
        'section',
        'date',
        'status',
        'marked_by',
        'remarks',
        'approval_status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class, 'student_enrollment_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function marker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('approval_status', self::APPROVAL_PENDING);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approval_status', self::APPROVAL_APPROVED);
    }

    public function scopeForActiveYear(Builder $query): Builder
    {
        $year = AcademicYear::current();

        return $year
            ? $query->where('academic_year_id', $year->id)
            : $query->whereRaw('1 = 0');
    }

    public function scopeForClass(Builder $query, string $class, ?string $section = null): Builder
    {
        $query->where('class', $class);
        if ($section) {
            $query->where('section', $section);
        }
        return $query;
    }

    public function scopeForDate(Builder $query, $date): Builder
    {
        return $query->whereDate('date', $date);
    }

    public static function statusLabel(string $status): string
    {
        return ucfirst($status);
    }
}
