<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (!Schema::hasColumn('members', 'admission_number')) {
                $table->string('admission_number')->nullable()->after('roll_number');
            }
            if (!Schema::hasColumn('members', 'aadhar_number')) {
                $table->string('aadhar_number')->nullable()->after('date_of_birth');
            }
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['admission_number', 'aadhar_number']);
        });
    }
};
