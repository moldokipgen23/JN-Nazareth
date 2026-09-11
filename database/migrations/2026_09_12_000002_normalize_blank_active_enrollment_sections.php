<?php

use App\Models\AcademicYear;
use App\Models\StudentEnrollment;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Older enrollments may have no section, or may accidentally store the
     * class name as the section. Normalize those current active records so
     * class totals and section lists always describe the same students.
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
            ->where(function ($query): void {
                $query->whereNull('section')
                    ->orWhere('section', '')
                    ->orWhereColumn('section', 'class');
            })
            ->orderBy('id')
            ->each(function (StudentEnrollment $enrollment): void {
                $enrollment->updateQuietly(['section' => 'A']);

                if ($enrollment->student) {
                    $enrollment->student->updateQuietly(['section' => 'A']);
                }
            });
    }

    public function down(): void
    {
        // This data normalization is intentionally not reversible.
    }
};
