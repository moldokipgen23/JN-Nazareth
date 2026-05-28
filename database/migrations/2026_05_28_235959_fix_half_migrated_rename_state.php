<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        $studentEnrollmentsHasMemberId = Schema::hasColumn('student_enrollments', 'member_id');
        $studentEnrollmentsHasStudentId = Schema::hasColumn('student_enrollments', 'student_id');
        $membersExists = Schema::hasTable('members');
        $studentsExists = Schema::hasTable('students');
        $memberFilesExists = Schema::hasTable('member_files');
        $studentFilesExists = Schema::hasTable('student_files');

        // 1. student_enrollments: rename member_id → student_id if needed
        if ($studentEnrollmentsHasMemberId) {
            Schema::table('student_enrollments', function (Blueprint $table) {
                $table->dropUnique('student_enrollments_member_id_academic_year_id_unique');
                $table->renameColumn('member_id', 'student_id');
            });
        }

        // 2. Rename tables
        if ($membersExists && !$studentsExists) {
            Schema::rename('members', 'students');
        }
        if ($memberFilesExists && !$studentFilesExists) {
            Schema::rename('member_files', 'student_files');
        }

        // 3. Re-add foreign keys and unique constraint
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
        // Reversing this fixer is not practical — the original migration covers it
    }
};
