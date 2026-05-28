<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'phone', 'designation', 'photo', 'assigned_classes', 'teacher_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'assigned_classes' => 'array',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function uploadedFiles(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class, 'author_id');
    }

    /** Class names this teacher is assigned to for the current year. */
    public function teachingClasses(): array
    {
        return $this->teacher?->teachingClasses() ?? ($this->assigned_classes ?? []);
    }

    public function teachesClass(string $class): bool
    {
        return in_array($class, $this->teachingClasses(), true);
    }

    public function isClassTeacherFor(string $class, ?string $section = null): bool
    {
        return $this->teacher?->isClassTeacherFor($class, $section) ?? false;
    }

    public function teachesSubject(string $class, string $section, string $subject): bool
    {
        return $this->teacher?->teachesSubject($class, $section, $subject) ?? false;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    /** True for accounts whose only admin-area role is teacher. */
    public function isTeacherOnly(): bool
    {
        return $this->hasRole('teacher') && ! $this->hasAnyRole(['admin', 'staff']);
    }
}
