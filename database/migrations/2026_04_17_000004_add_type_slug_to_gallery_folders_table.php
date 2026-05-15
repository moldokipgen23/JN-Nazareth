<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gallery_folders', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->enum('type', ['gallery', 'programs'])->default('gallery')->after('slug');
        });

        Schema::table('gallery', function (Blueprint $table) {
            $table->string('caption')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('gallery_folders', function (Blueprint $table) {
            $table->dropColumn(['slug', 'type']);
        });
        Schema::table('gallery', function (Blueprint $table) {
            $table->dropColumn('caption');
        });
    }
};
