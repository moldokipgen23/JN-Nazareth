<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('designation')->nullable()->after('phone');
            $table->string('photo')->nullable()->after('designation');
            // List of class names a teacher is assigned to, e.g. ["Class I","Class II"]
            $table->json('assigned_classes')->nullable()->after('photo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'designation', 'photo', 'assigned_classes']);
        });
    }
};
