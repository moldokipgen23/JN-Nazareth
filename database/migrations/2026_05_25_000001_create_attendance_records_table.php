<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('student_enrollment_id')->constrained('student_enrollments')->cascadeOnDelete();
            $table->string('class')->index();
            $table->string('section', 20)->default('A')->index();
            $table->date('date')->index();
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('remarks', 500)->nullable();
            $table->timestamps();

            // One attendance row per student per day.
            $table->unique(['student_enrollment_id', 'date'], 'attendance_student_date_unique');
            $table->index(['academic_year_id', 'class', 'section', 'date'], 'attendance_class_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
