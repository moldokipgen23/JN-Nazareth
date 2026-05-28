<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('class')->nullable()->index();
            $table->string('section', 20)->nullable()->index();
            $table->string('roll_number', 30)->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->date('enrolled_on')->nullable();
            $table->date('left_on')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['member_id', 'academic_year_id']);
            $table->index(['academic_year_id', 'class', 'section', 'status'], 'enrollments_year_class_section_status_idx');
        });

        $fallbackYearId = DB::table('academic_years')->where('is_active', true)->value('id')
            ?? DB::table('academic_years')->value('id');

        $yearIdsByName = DB::table('academic_years')->pluck('id', 'name')->all();

        DB::table('members')
            ->whereNotNull('class')
            ->orderBy('id')
            ->chunkById(200, function ($members) use (&$yearIdsByName, $fallbackYearId) {
                foreach ($members as $member) {
                    $yearName = trim((string) ($member->academic_year ?: ''));
                    $yearId = $yearName !== '' ? ($yearIdsByName[$yearName] ?? null) : null;

                    if (! $yearId && $yearName !== '') {
                        $yearId = DB::table('academic_years')->insertGetId([
                            'name' => $yearName,
                            'starts_on' => null,
                            'ends_on' => null,
                            'is_active' => false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $yearIdsByName[$yearName] = $yearId;
                    }

                    $yearId = $yearId ?: $fallbackYearId;
                    if (! $yearId) {
                        continue;
                    }

                    DB::table('student_enrollments')->updateOrInsert(
                        [
                            'member_id' => $member->id,
                            'academic_year_id' => $yearId,
                        ],
                        [
                            'class' => $member->class,
                            'section' => $member->section ?: 'A',
                            'roll_number' => $member->roll_number,
                            'status' => ($member->is_active ?? true) ? 'active' : 'dropped',
                            'enrolled_on' => $member->admission_date,
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
