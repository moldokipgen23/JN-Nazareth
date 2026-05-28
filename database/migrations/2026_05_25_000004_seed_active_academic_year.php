<?php

use App\Models\AcademicYear;
use App\Models\StudentEnrollment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        AcademicYear::activeOrCreate('2025-26');

        // The table may be called "members" (pre-rename) or "students" (post-rename).
        $table = Schema::hasTable('members') ? 'members' : (Schema::hasTable('students') ? 'students' : null);

        if ($table === null) {
            return;
        }

        DB::table($table)->whereNotNull('class')->orderBy('id')->each(function ($row) {
            $year = AcademicYear::activeOrCreate($row->academic_year ?? date('Y').'-'.(date('y') + 1));

            StudentEnrollment::updateOrCreate(
                [
                    'student_id'       => $row->id,
                    'academic_year_id' => $year->id,
                ],
                [
                    'class'       => $row->class,
                    'section'     => $row->section ?: 'A',
                    'roll_number' => $row->roll_number ?? null,
                    'status'      => ($row->is_active ?? true) ? StudentEnrollment::STATUS_ACTIVE : StudentEnrollment::STATUS_DROPPED,
                    'enrolled_on' => $row->admission_date ?? now()->toDateString(),
                ]
            );
        });
    }

    public function down(): void
    {
        // Intentionally blank — this migration only upserts data.
    }
};
