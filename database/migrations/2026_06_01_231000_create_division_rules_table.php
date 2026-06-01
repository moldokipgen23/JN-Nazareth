<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('division_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->decimal('min_percent', 5, 2);
            $table->decimal('max_percent', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('division_rules');
    }
};
