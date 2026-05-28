<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Link users → teachers
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('teacher_id')->nullable()->after('id')->constrained('teachers')->nullOnDelete();
        });

        // 2. class_teacher_assignments: switch FK users → teachers
        Schema::table('class_teacher_assignments', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
        });
        if (Schema::hasIndex('class_teacher_assignments', 'class_teacher_teacher_year_idx')) {
            Schema::table('class_teacher_assignments', function (Blueprint $table) {
                $table->dropIndex('class_teacher_teacher_year_idx');
            });
        }
        Schema::table('class_teacher_assignments', function (Blueprint $table) {
            // rename old column out of the way
            $table->renameColumn('teacher_id', 'user_id');
        });
        Schema::table('class_teacher_assignments', function (Blueprint $table) {
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->index(['teacher_id', 'academic_year_id'], 'class_teacher_teacher_year_idx');
        });
        Schema::table('class_teacher_assignments', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        // 3. subject_teacher_assignments: switch FK users → teachers
        Schema::table('subject_teacher_assignments', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
        });
        if (Schema::hasIndex('subject_teacher_assignments', 'subject_teacher_teacher_year_idx')) {
            Schema::table('subject_teacher_assignments', function (Blueprint $table) {
                $table->dropIndex('subject_teacher_teacher_year_idx');
            });
        }
        Schema::table('subject_teacher_assignments', function (Blueprint $table) {
            $table->renameColumn('teacher_id', 'user_id');
        });
        Schema::table('subject_teacher_assignments', function (Blueprint $table) {
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->index(['teacher_id', 'academic_year_id'], 'subject_teacher_teacher_year_idx');
        });
        Schema::table('subject_teacher_assignments', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        // Reverse class_teacher_assignments
        Schema::table('class_teacher_assignments', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropIndex('class_teacher_teacher_year_idx');
            $table->dropColumn('teacher_id');
        });
        Schema::table('class_teacher_assignments', function (Blueprint $table) {
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
        });

        // Reverse subject_teacher_assignments
        Schema::table('subject_teacher_assignments', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropIndex('subject_teacher_teacher_year_idx');
            $table->dropColumn('teacher_id');
        });
        Schema::table('subject_teacher_assignments', function (Blueprint $table) {
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
        });

        // Reverse users
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
        });

        Schema::enableForeignKeyConstraints();
    }
};
