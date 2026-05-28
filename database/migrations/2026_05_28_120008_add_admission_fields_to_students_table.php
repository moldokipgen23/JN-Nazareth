<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'gender')) {
                $table->string('gender', 10)->nullable()->after('date_of_birth');
            }
            if (! Schema::hasColumn('students', 'blood_group')) {
                $table->string('blood_group', 10)->nullable()->after('gender');
            }
            if (! Schema::hasColumn('students', 'guardian_name')) {
                $table->string('guardian_name')->nullable()->after('mother_name');
            }
            if (! Schema::hasColumn('students', 'guardian_email')) {
                $table->string('guardian_email')->nullable()->after('guardian_name');
            }
            if (! Schema::hasColumn('students', 'guardian_relation')) {
                $table->string('guardian_relation', 50)->nullable()->after('guardian_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['gender', 'blood_group', 'guardian_name', 'guardian_email', 'guardian_relation']);
        });
    }
};
