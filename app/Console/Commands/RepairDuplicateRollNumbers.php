<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use App\Models\StudentEnrollment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairDuplicateRollNumbers extends Command
{
    protected $signature = 'academic:repair-duplicate-rolls {--apply : Save the proposed repairs}';
    protected $description = 'Safely repair duplicate active roll numbers in the current academic year';

    public function handle(): int
    {
        $year = AcademicYear::current();
        if (! $year) {
            $this->error('No active academic year was found.');
            return self::FAILURE;
        }

        $duplicates = StudentEnrollment::query()
            ->where('academic_year_id', $year->id)->where('status', StudentEnrollment::STATUS_ACTIVE)
            ->whereNotNull('roll_number')->where('roll_number', '!=', '')
            ->selectRaw('class, section, roll_number, COUNT(*) as total')
            ->groupBy('class', 'section', 'roll_number')->havingRaw('COUNT(*) > 1')->get();

        if ($duplicates->isEmpty()) {
            $this->info('No duplicate active roll numbers found.');
            return self::SUCCESS;
        }

        $repairs = [];
        $usedByClassSection = [];
        foreach ($duplicates as $duplicate) {
            if (! ctype_digit((string) $duplicate->roll_number)) {
                $this->error("Cannot automatically repair non-numeric roll {$duplicate->roll_number} in {$duplicate->class}/{$duplicate->section}.");
                return self::FAILURE;
            }

            $slot = $duplicate->class . "\0" . $duplicate->section;
            if (! isset($usedByClassSection[$slot])) {
                $usedByClassSection[$slot] = StudentEnrollment::query()
                    ->where('academic_year_id', $year->id)->where('status', StudentEnrollment::STATUS_ACTIVE)
                    ->where('class', $duplicate->class)->where('section', $duplicate->section)
                    ->pluck('roll_number')->filter(fn ($roll) => ctype_digit((string) $roll))
                    ->map(fn ($roll) => (int) $roll)->flip()->all();
            }
            $used =& $usedByClassSection[$slot];

            $rows = StudentEnrollment::with('student')->where('academic_year_id', $year->id)
                ->where('status', StudentEnrollment::STATUS_ACTIVE)->where('class', $duplicate->class)
                ->where('section', $duplicate->section)->where('roll_number', $duplicate->roll_number)
                ->orderBy('enrolled_on')->orderBy('id')->get();

            // Retain the oldest enrollment's roll number. Re-number only later duplicates.
            foreach ($rows->skip(1) as $row) {
                $next = 1;
                while (isset($used[$next])) {
                    $next++;
                }
                $used[$next] = true;
                $repairs[] = [$row, (string) $next];
            }
        }

        foreach ($repairs as [$enrollment, $newRoll]) {
            $this->line("{$enrollment->class}/{$enrollment->section}: {$enrollment->student?->name} — {$enrollment->roll_number} → {$newRoll}");
        }

        if (! $this->option('apply')) {
            $this->warn('Preview only. Run again with --apply to save these changes.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($repairs) {
            foreach ($repairs as [$enrollment, $newRoll]) {
                $enrollment->update(['roll_number' => $newRoll]);
                $enrollment->student?->updateQuietly(['roll_number' => $newRoll]);
            }
        });

        $this->info(count($repairs) . ' duplicate roll number(s) repaired. No students were deleted.');
        return self::SUCCESS;
    }
}
