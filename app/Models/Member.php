<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'name',
        'roll_number',
        'admission_number',
        'aadhar_number',
        'class',
        'section',
        'academic_year',
        'father_name',
        'mother_name',
        'parent_phone',
        'phone',
        'email',
        'address',
        'date_of_birth',
        'admission_date',
        'status',
        'is_active',
        'role',
        'photo',
        'notes',
    ];

    protected $casts = [
        'date_of_birth'  => 'date',
        'admission_date' => 'date',
        'is_active'      => 'boolean',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(MemberFile::class);
    }

    /**
     * Aadhar number with all but the last 4 digits masked — for safer display.
     */
    public function maskedAadhar(): string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->aadhar_number);
        if ($digits === '' || strlen($digits) < 4) {
            return $this->aadhar_number ?? '';
        }

        return 'XXXX XXXX ' . substr($digits, -4);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByClass(Builder $query, string $class): Builder
    {
        return $query->where('class', $class);
    }

    /**
     * Ordered list of school classes (Preparatory → Class X).
     */
    public static function classes(): array
    {
        return [
            'Preparatory', 'LKG', 'UKG',
            'Class I', 'Class II', 'Class III', 'Class IV', 'Class V',
            'Class VI', 'Class VII', 'Class VIII', 'Class IX', 'Class X',
        ];
    }
}
