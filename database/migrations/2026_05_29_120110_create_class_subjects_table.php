<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('class')->index();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('section', 20)->nullable()->index();
            $table->boolean('is_optional')->default(false);
            $table->decimal('full_marks', 6, 2)->nullable();
            $table->decimal('pass_marks', 6, 2)->nullable();
            $table->boolean('grade_only')->default(false);
            $table->timestamps();

            $table->unique(['class', 'subject_id', 'academic_year_id', 'section'], 'class_subject_unique');
        });

        // Seed from existing data so nothing breaks
        $year = DB::table('academic_years')->where('is_active', true)->first();
        if ($year) {
            $existing = DB::table('marks')
                ->where('academic_year_id', $year->id)
                ->select('class', 'subject')
                ->distinct()
                ->get()
                ->merge(
                    DB::table('subject_teacher_assignments')
                        ->where('academic_year_id', $year->id)
                        ->select('class', 'subject')
                        ->distinct()
                        ->get()
                )->unique(fn ($i) => $i->class.'|'.$i->subject);

            $subjectNames = DB::table('subjects')->pluck('id', 'name');

            foreach ($existing as $row) {
                $subjectId = $subjectNames[$row->subject] ?? null;
                if ($subjectId) {
                    DB::table('class_subjects')->updateOrInsert(
                        [
                            'class' => $row->class,
                            'subject_id' => $subjectId,
                            'academic_year_id' => $year->id,
                            'section' => null,
                        ],
                        [
                            'is_optional' => false,
                            'grade_only' => false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('class_subjects');
    }
};
