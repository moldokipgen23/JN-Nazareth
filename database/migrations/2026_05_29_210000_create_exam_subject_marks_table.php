<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_subject_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->string('class')->index();
            $table->string('subject');
            $table->decimal('full_marks', 6, 2)->default(100);
            $table->decimal('pass_marks', 6, 2)->default(33);
            $table->timestamps();

            $table->unique(['exam_id', 'class', 'subject'], 'esm_exam_class_subject_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_subject_marks');
    }
};
