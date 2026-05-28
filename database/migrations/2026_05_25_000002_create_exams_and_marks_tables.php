<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('name');                  // First Term, Unit Test 1, Final Exam...
            $table->string('code', 40)->nullable();  // optional short code
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['academic_year_id', 'name'], 'exam_year_name_unique');
            $table->index(['academic_year_id', 'is_active'], 'exam_year_active_idx');
        });

        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('student_enrollment_id')->constrained('student_enrollments')->cascadeOnDelete();
            $table->string('class')->index();
            $table->string('section', 20)->default('A')->index();
            $table->string('subject')->index();
            $table->decimal('full_marks', 6, 2)->default(100);
            $table->decimal('pass_marks', 6, 2)->default(33);
            $table->decimal('obtained_marks', 6, 2)->nullable();
            $table->string('grade', 5)->nullable();
            $table->string('remarks', 500)->nullable();
            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // One mark per (exam, student, subject).
            $table->unique(['exam_id', 'student_enrollment_id', 'subject'], 'marks_exam_student_subject_unique');
            $table->index(['academic_year_id', 'class', 'section', 'subject'], 'marks_class_subject_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marks');
        Schema::dropIfExists('exams');
    }
};
