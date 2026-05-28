<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (! Schema::hasColumn('exams', 'submission_starts_on')) {
                $table->date('submission_starts_on')->nullable()->after('ends_on');
            }
            if (! Schema::hasColumn('exams', 'submission_ends_on')) {
                $table->date('submission_ends_on')->nullable()->after('submission_starts_on');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['submission_starts_on', 'submission_ends_on']);
        });
    }
};
