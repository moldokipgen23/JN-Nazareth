<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('address');
            $table->enum('status', ['active', 'deceased', 'moved_out', 'inactive'])
                  ->default('active')->after('date_of_birth');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['date_of_birth', 'status']);
        });
    }
};
