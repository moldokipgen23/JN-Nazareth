<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marks', function (Blueprint $table) {
            $table->decimal('theory_marks', 6, 2)->nullable()->after('obtained_marks');
            $table->decimal('assignment_marks', 6, 2)->nullable()->after('theory_marks');
            $table->decimal('total_marks', 6, 2)->nullable()->after('assignment_marks');
        });
    }

    public function down(): void
    {
        Schema::table('marks', function (Blueprint $table) {
            $table->dropColumn(['theory_marks', 'assignment_marks', 'total_marks']);
        });
    }
};
