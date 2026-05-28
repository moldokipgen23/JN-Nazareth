<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoteAssignment extends Model
{
    protected $fillable = [
        'academic_year_id', 'submitted_by',
        'class', 'section', 'subject',
        'title', 'content', 'file_path', 'file_name',
        'type', 'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function scopeForActiveYear(Builder $query): Builder
    {
        $year = AcademicYear::current();
        return $year
            ? $query->where('academic_year_id', $year->id)
            : $query->whereRaw('1 = 0');
    }

    // NoteAssignment has no status field — pending() always returns empty set
    public function scopePending(Builder $query): Builder
    {
        return $query->whereRaw('1 = 0');
    }
}
