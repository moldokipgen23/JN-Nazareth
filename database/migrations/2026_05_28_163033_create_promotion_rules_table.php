<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotion_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('min_pass_percentage', 5, 2)->default(40.00);
            $table->decimal('min_attendance_percentage', 5, 2)->default(75.00);
            $table->boolean('use_cgpa')->default(false);
            $table->decimal('min_cgpa', 3, 2)->nullable();
            $table->json('fail_subjects_allowed')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_rules');
    }
};
