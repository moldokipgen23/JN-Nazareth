<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_items', function (Blueprint $table) {
            $table->string('title')->nullable()->after('caption');
            $table->text('description')->nullable()->after('title');
            $table->string('category')->default('general')->after('description');
            $table->date('activity_date')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('program_items', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'category', 'activity_date']);
        });
    }
};
