<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('published')->default(true)->after('content');
            $table->boolean('show_in_nav')->default(false)->after('published');
            $table->string('meta_description')->nullable()->after('show_in_nav');
            $table->foreignId('updated_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['published', 'show_in_nav', 'meta_description']);
        });
    }
};
