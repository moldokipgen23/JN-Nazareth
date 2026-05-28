<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // Rename member_files.member_id → student_id
        Schema::table('member_files', function ($table) {
            $table->renameColumn('member_id', 'student_id');
        });

        // Rename student_enrollments.member_id → student_id
        Schema::table('student_enrollments', function ($table) {
            $table->dropUnique('student_enrollments_member_id_academic_year_id_unique');
            $table->renameColumn('member_id', 'student_id');
        });

        // Rename tables
        Schema::rename('members', 'students');
        Schema::rename('member_files', 'student_files');

        // Re-add foreign keys and unique
        Schema::table('student_files', function ($table) {
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });
        Schema::table('student_enrollments', function ($table) {
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->unique(['student_id', 'academic_year_id']);
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('student_files', function ($table) {
            $table->renameColumn('student_id', 'member_id');
        });
        Schema::table('student_enrollments', function ($table) {
            $table->dropUnique('student_enrollments_student_id_academic_year_id_unique');
            $table->renameColumn('student_id', 'member_id');
        });

        Schema::rename('students', 'members');
        Schema::rename('student_files', 'member_files');

        Schema::table('member_files', function ($table) {
            $table->foreign('member_id')->references('id')->on('members')->cascadeOnDelete();
        });
        Schema::table('student_enrollments', function ($table) {
            $table->foreign('member_id')->references('id')->on('members')->cascadeOnDelete();
            $table->unique(['member_id', 'academic_year_id']);
        });

        Schema::enableForeignKeyConstraints();
    }
};
