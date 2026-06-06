<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassTeacherAssignment;
use App\Models\Exam;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\SubjectTeacherAssignment;
use App\Models\Teacher;
use Illuminate\Support\Collection;

class PortalController extends Controller
{
    protected function teacher(): Teacher
    {
        $teacher = auth()->user()?->teacher;

        abort_unless($teacher, 403, 'No teacher profile linked to your account. Ask admin to use "Create Login" on your teacher profile.');

        return $teacher;
    }

    /** Teacher home — quick stats + jump-offs. */
    public function dashboard()
    {
        $teacher = $this->teacher();
        $year    = AcademicYear::current();

        $classes  = $this->classRows($teacher, $year);
        $subjects = $this->subjectRows($teacher, $year);

        $studentCount = $classes->sum('students');

        $now = now()->startOfDay();
        $activeExams = $year
            ? Exam::forActiveYear()->active()
                ->where(function ($q) use ($now) {
                    $q->whereNull('submission_starts_on')
                      ->orWhere('submission_starts_on', '<=', $now);
                })
                ->where(function ($q) use ($now) {
                    $q->whereNull('submission_ends_on')
                      ->orWhere('submission_ends_on', '>=', $now);
                })
                ->orderBy('sort_order')
                ->get()
            : collect();

        return view('teacher.dashboard', [
            'teacher'      => $teacher,
            'year'         => $year,
            'classes'      => $classes,
            'subjects'     => $subjects,
            'studentCount' => $studentCount,
            'activeExams'  => $activeExams,
        ]);
    }

    /** My Classes — class-teacher rows + (read-only) classes derived from subjects. */
    public function classes()
    {
        $teacher = $this->teacher();
        $year    = AcademicYear::current();

        return view('teacher.classes.index', [
            'teacher' => $teacher,
            'year'    => $year,
            'classes' => $this->classRows($teacher, $year),
        ]);
    }

    /** My Subjects — one row per subject/class/section assignment. */
    public function subjects()
    {
        $teacher = $this->teacher();
        $year    = AcademicYear::current();

        return view('teacher.subjects.index', [
            'teacher'  => $teacher,
            'year'     => $year,
            'subjects' => $this->subjectRows($teacher, $year),
        ]);
    }

    // ────────────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────────────

    /**
     * Build a row per class/section the teacher touches.
     *
     * Sources merged (de-duplicated):
     *   • ClassTeacherAssignment  (role = class_teacher)
     *   • SubjectTeacherAssignment (role = subject_teacher, list of subjects)
     *   • legacy teachers.classes JSON (role = legacy)
     *
     * Each row also carries the active-year student count.
     */
    protected function classRows(Teacher $teacher, ?AcademicYear $year): Collection
    {
        $rows = collect();

        // Class-teacher assignments — definitive ownership.
        if ($year) {
            ClassTeacherAssignment::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $year->id)
                ->get(['class', 'section'])
                ->each(function ($a) use (&$rows) {
                    $key = $this->key($a->class, $a->section);
                    $rows->put($key, [
                        'class'    => $a->class,
                        'section'  => $a->section,
                        'is_class_teacher' => true,
                        'subjects' => [],
                    ]);
                });

            // Subject assignments — also expose the class/section + subject list.
            SubjectTeacherAssignment::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $year->id)
                ->get(['class', 'section', 'subject'])
                ->each(function ($a) use (&$rows) {
                    $key = $this->key($a->class, $a->section);
                    $row = $rows->get($key, [
                        'class'    => $a->class,
                        'section'  => $a->section,
                        'is_class_teacher' => false,
                        'subjects' => [],
                    ]);
                    $row['subjects'] = array_values(array_unique(
                        array_merge($row['subjects'], [$a->subject])
                    ));
                    $rows->put($key, $row);
                });
        }

        // Legacy teacher.classes JSON (no section info) — only if not already represented.
        foreach (($teacher->classes ?? []) as $class) {
            if (! $class) {
                continue;
            }
            $touched = $rows->keys()->contains(fn ($k) => str_starts_with($k, $class.'|'));
            if ($touched) {
                continue;
            }
            $rows->put($this->key($class, null), [
                'class'    => $class,
                'section'  => null,
                'is_class_teacher' => false,
                'subjects' => [],
                'legacy'   => true,
            ]);
        }

        // Attach active-year student counts.
        $counts = $year
            ? StudentEnrollment::forActiveYear()->active()
                ->selectRaw('class, section, count(*) as total')
                ->groupBy('class', 'section')
                ->get()
                ->mapWithKeys(fn ($r) => [$this->key($r->class, $r->section) => (int) $r->total])
                ->all()
            : [];

        $order = array_flip(Student::classes());

        return $rows->map(function ($row) use ($counts) {
            $row['students'] = $row['section']
                ? ($counts[$this->key($row['class'], $row['section'])] ?? 0)
                : collect($counts)
                    ->filter(fn ($_, $k) => str_starts_with($k, $row['class'].'|'))
                    ->sum();
            $row['legacy']   = $row['legacy'] ?? false;
            return $row;
        })
        ->sortBy(fn ($r) => [$order[$r['class']] ?? 999, $r['section'] ?? ''])
        ->values();
    }

    /** Per-subject rows for the active year. */
    protected function subjectRows(Teacher $teacher, ?AcademicYear $year): Collection
    {
        if (! $year) {
            return collect();
        }

        $order = array_flip(Student::classes());

        return SubjectTeacherAssignment::where('teacher_id', $teacher->id)
            ->where('academic_year_id', $year->id)
            ->get(['subject', 'class', 'section'])
            ->sortBy(fn ($r) => [$r->subject, $order[$r->class] ?? 999, $r->section ?? ''])
            ->values();
    }

    protected function key(string $class, ?string $section): string
    {
        return $class.'|'.($section ?? '');
    }
}
