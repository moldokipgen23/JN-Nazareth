<?php

use App\Models\AcademicYear;
use App\Models\StudentEnrollment;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Bring the denormalized current student profile fields into line with
     * the active academic year's enrollment records.
     */
    public function up(): void
    {
        $year = AcademicYear::current();

        if (! $year) {
            return;
        }

        StudentEnrollment::query()
            ->where('academic_year_id', $year->id)
            ->where('status', StudentEnrollment::STATUS_ACTIVE)
            ->whereNotNull('class')
            ->orderBy('id')
            ->each(function (StudentEnrollment $enrollment): void {
                $student = $enrollment->student;

                if (! $student) {
                    return;
                }

                $student->updateQuietly([
                    'class'       => $enrollment->class,
                    'section'     => $enrollment->section ?: 'A',
                    'roll_number' => $enrollment->roll_number,
                    'is_active'   => true,
                ]);
            });
    }

    public function down(): void
    {
        // This data repair is intentionally not reversible.
    }
};
