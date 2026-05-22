<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('roll_number')->nullable()->after('name');
            $table->string('class')->nullable()->after('roll_number');
            $table->string('section')->nullable()->default('A')->after('class');
            $table->string('academic_year')->nullable()->after('section');
            $table->string('father_name')->nullable()->after('academic_year');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->string('parent_phone')->nullable()->after('mother_name');
            $table->date('admission_date')->nullable()->after('date_of_birth');
            $table->boolean('is_active')->default(true)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'roll_number', 'class', 'section', 'academic_year',
                'father_name', 'mother_name', 'parent_phone',
                'admission_date', 'is_active',
            ]);
        });
    }
};
