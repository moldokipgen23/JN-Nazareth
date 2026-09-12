<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use App\Models\StudentEnrollment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuditAcademicData extends Command
{
    protected $signature = 'academic:audit-data {--year= : Academic-year ID (defaults to the current year)}';
    protected $description = 'Read-only audit for class, section, roster, and roll-number integrity';

    public function handle(): int
    {
        $year = $this->option('year')
            ? AcademicYear::find($this->option('year'))
            : AcademicYear::current();

        if (! $year) {
            $this->error('No academic year was found. No data was changed.');
            return self::FAILURE;
        }

        $problems = 0;
        $this->info("Read-only audit: {$year->name}");

        $invalidEnrollments = DB::table('student_enrollments as e')
            ->leftJoin('sections as s', function ($join) {
                $join->on('s.class', '=', 'e.class')->on('s.name', '=', 'e.section')->where('s.is_active', '=', true);
            })
            ->where('e.academic_year_id', $year->id)->whereNotNull('e.class')->whereNull('s.id')
            ->select('e.id', 'e.student_id', 'e.class', 'e.section')->get();
        $problems += $this->report('Enrollments with an invalid section', $invalidEnrollments, fn ($r) => "Enrollment #{$r->id}: {$r->class} / " . ($r->section ?: '[blank]'));

        $profileMismatches = StudentEnrollment::query()->with('student')
            ->where('academic_year_id', $year->id)->where('status', StudentEnrollment::STATUS_ACTIVE)->get()
            ->filter(fn ($e) => ! $e->student || $e->student->class !== $e->class || $e->student->section !== $e->section || (string) $e->student->roll_number !== (string) $e->roll_number);
        $problems += $this->report('Student profile/enrollment mismatches', $profileMismatches, fn ($e) => "Student #{$e->student_id}: {$e->class}/{$e->section}, roll {$e->roll_number}");

        $duplicateRolls = DB::table('student_enrollments')
            ->where('academic_year_id', $year->id)->where('status', StudentEnrollment::STATUS_ACTIVE)
            ->whereNotNull('roll_number')->where('roll_number', '!=', '')
            ->selectRaw('class, section, roll_number, COUNT(*) as total')
            ->groupBy('class', 'section', 'roll_number')->havingRaw('COUNT(*) > 1')->get();
        $problems += $this->report('Duplicate active roll numbers', $duplicateRolls, fn ($r) => "{$r->class}/{$r->section}: roll {$r->roll_number} ({$r->total} students)");

        foreach (['class_teacher_assignments', 'subject_teacher_assignments', 'marks', 'note_assignments'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            $invalid = DB::table("{$table} as r")
                ->leftJoin('sections as s', function ($join) {
                    $join->on('s.class', '=', 'r.class')->on('s.name', '=', 'r.section')->where('s.is_active', '=', true);
                })
                ->where('r.academic_year_id', $year->id)->whereNotNull('r.class')->whereNotNull('r.section')->whereNull('s.id')
                ->select('r.id', 'r.class', 'r.section')->get();
            $problems += $this->report("Invalid class/section in {$table}", $invalid, fn ($r) => "#{$r->id}: {$r->class}/{$r->section}");
        }

        $this->newLine();
        if ($problems === 0) {
            $this->info('PASS: no integrity problems found. No data was changed.');
            return self::SUCCESS;
        }

        $this->error("FAIL: {$problems} problem(s) found. No data was changed.");
        return self::FAILURE;
    }

    private function report(string $title, iterable $rows, callable $format): int
    {
        $rows = collect($rows);
        if ($rows->isEmpty()) {
            $this->line("PASS: {$title}");
            return 0;
        }

        $this->warn("FAIL: {$title} ({$rows->count()})");
        foreach ($rows->take(20) as $row) {
            $this->line('  - ' . $format($row));
        }
        if ($rows->count() > 20) {
            $this->line('  - Additional rows omitted from display.');
        }

        return $rows->count();
    }
}
