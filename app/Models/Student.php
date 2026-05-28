<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Student extends Model
{
    protected $table = 'students';

    protected $fillable = [
        'name',
        'class',
        'section',
        'roll_number',
        'phone',
        'email',
        'address',
        'photo',
        'academic_year',
        'father_name',
        'mother_name',
        'guardian_name',
        'guardian_email',
        'guardian_relation',
        'guardian_phone',
        'date_of_birth',
        'gender',
        'blood_group',
        'admission_number',
        'aadhar_number',
        'admission_date',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(StudentFile::class, 'student_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class, 'student_id');
    }

    public function currentEnrollment()
    {
        return $this->hasOne(StudentEnrollment::class, 'student_id')
            ->where('academic_year_id', AcademicYear::current()?->id);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function classes(): array
    {
        return Cache::remember('member_classes', 3600, function () {
            $default = [
                'Preparatory', 'LKG', 'UKG',
                'Class I', 'Class II', 'Class III', 'Class IV', 'Class V',
                'Class VI', 'Class VII', 'Class VIII', 'Class IX', 'Class X',
            ];

            $custom = static::select('class')
                ->whereNotNull('class')
                ->distinct()
                ->orderBy('class')
                ->pluck('class')
                ->toArray();

            // Also include classes that have sections defined (pre-created classes)
            $sectionClasses = \App\Models\Section::select('class')
                ->distinct()
                ->pluck('class')
                ->toArray();

            $all = array_values(array_unique(array_merge($default, $custom, $sectionClasses)));

            $order = array_flip($default);
            usort($all, function ($a, $b) use ($order) {
                $ia = $order[$a] ?? 999;
                $ib = $order[$b] ?? 999;
                if ($ia !== $ib) return $ia <=> $ib;
                return strcmp($a, $b);
            });

            return $all;
        });
    }
}
