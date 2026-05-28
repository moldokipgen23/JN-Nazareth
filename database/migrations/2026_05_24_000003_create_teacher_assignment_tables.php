<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('class')->index();
            $table->string('section', 20)->default('A')->index();
            $table->timestamps();

            $table->unique(['academic_year_id', 'class', 'section'], 'class_teacher_unique_slot');
            $table->index(['teacher_id', 'academic_year_id'], 'class_teacher_teacher_year_idx');
        });

        Schema::create('subject_teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('class')->index();
            $table->string('section', 20)->default('A')->index();
            $table->string('subject')->index();
            $table->timestamps();

            $table->unique(['academic_year_id', 'class', 'section', 'subject'], 'subject_teacher_unique_slot');
            $table->index(['teacher_id', 'academic_year_id'], 'subject_teacher_teacher_year_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_teacher_assignments');
        Schema::dropIfExists('class_teacher_assignments');
    }
};
