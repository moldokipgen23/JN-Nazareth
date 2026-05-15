<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hall_of_fames', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('achievement_title', 200);
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->string('year', 10)->nullable();
            $table->string('video_url', 300)->nullable();
            $table->string('external_link', 300)->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hall_of_fames');
    }
};
