<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. member_files: rename member_id → student_id (only if still exists)
        if (Schema::hasColumn('member_files', 'member_id')) {
            Schema::table('member_files', function (Blueprint $table) {
                $table->dropForeign(['member_id']);
                $table->renameColumn('member_id', 'student_id');
            });
        }

        // 2. student_enrollments: rename member_id → student_id (only if still exists)
        if (Schema::hasColumn('student_enrollments', 'member_id')) {
            Schema::table('student_enrollments', function (Blueprint $table) {
                $table->dropForeign(['member_id']);
                $table->dropUnique('student_enrollments_member_id_academic_year_id_unique');
                $table->renameColumn('member_id', 'student_id');
            });
        }

        // 3. Rename members table if not already done
        if (Schema::hasTable('members') && !Schema::hasTable('students')) {
            Schema::rename('members', 'students');
        }

        // 4. Rename member_files table if not already done
        if (Schema::hasTable('member_files') && !Schema::hasTable('student_files')) {
            Schema::rename('member_files', 'student_files');
        }

        // 5. Re-add foreign keys and unique (only if tables/columns exist)
        if (Schema::hasTable('student_files') && Schema::hasColumn('student_files', 'student_id')) {
            Schema::table('student_files', function (Blueprint $table) {
                $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('student_enrollments') && Schema::hasColumn('student_enrollments', 'student_id')) {
            Schema::table('student_enrollments', function (Blueprint $table) {
                $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
                $table->unique(['student_id', 'academic_year_id']);
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        $studentsExists = Schema::hasTable('students');
        $studentFilesExists = Schema::hasTable('student_files');

        if (Schema::hasColumn('student_files', 'student_id')) {
            Schema::table('student_files', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
                $table->renameColumn('student_id', 'member_id');
            });
        }

        if (Schema::hasColumn('student_enrollments', 'student_id')) {
            Schema::table('student_enrollments', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
                $table->dropUnique('student_enrollments_student_id_academic_year_id_unique');
                $table->renameColumn('student_id', 'member_id');
            });
        }

        if ($studentsExists && !Schema::hasTable('members')) {
            Schema::rename('students', 'members');
        }

        if ($studentFilesExists && !Schema::hasTable('member_files')) {
            Schema::rename('student_files', 'member_files');
        }

        if (Schema::hasTable('member_files') && Schema::hasColumn('member_files', 'member_id')) {
            Schema::table('member_files', function (Blueprint $table) {
                $table->foreign('member_id')->references('id')->on('members')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('student_enrollments') && Schema::hasColumn('student_enrollments', 'member_id')) {
            Schema::table('student_enrollments', function (Blueprint $table) {
                $table->foreign('member_id')->references('id')->on('members')->cascadeOnDelete();
                $table->unique(['member_id', 'academic_year_id']);
            });
        }

        Schema::enableForeignKeyConstraints();
    }
};
