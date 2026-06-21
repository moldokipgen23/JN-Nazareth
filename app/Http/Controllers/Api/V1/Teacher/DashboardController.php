<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\GradeScale;
use App\Models\SubjectTeacherAssignment;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teacher = $this->getTeacher($request);
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

        $gradeScale = GradeScale::active()->orderByDesc('grade_point')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'teacher' => [
                    'id'   => $teacher->id,
                    'name' => $teacher->name,
                    'designation' => $teacher->designation,
                    'photo' => $teacher->photo ? asset('storage/' . $teacher->photo) : null,
                ],
                'year' => $year ? [
                    'id'   => $year->id,
                    'name' => $year->name,
                ] : null,
                'classes'      => $classes,
                'subjects'     => $subjects,
                'studentCount' => $studentCount,
                'activeExams'  => $activeExams->map(fn ($e) => [
                    'id'   => $e->id,
                    'name' => $e->name,
                    'type' => $e->type,
                ]),
                'gradeScale' => $gradeScale->map(fn ($g) => [
                    'name'         => $g->name,
                    'min_percent'  => $g->min_percent,
                    'max_percent'  => $g->max_percent,
                    'grade_point'  => $g->grade_point,
                ]),
            ],
        ]);
    }

    protected function getTeacher(Request $request): Teacher
    {
        $teacher = $request->user()->teacher;
        abort_unless($teacher, 403, 'No teacher profile linked.');
        return $teacher;
    }

    protected function classRows(Teacher $teacher, ?AcademicYear $year): \Illuminate\Support\Collection
    {
        $rows = collect();

        if ($year) {
            \App\Models\ClassTeacherAssignment::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $year->id)
                ->get(['class', 'section'])
                ->each(function ($a) use (&$rows) {
                    $key = $a->class . '|' . $a->section;
                    $rows->put($key, [
                        'class'    => $a->class,
                        'section'  => $a->section,
                        'is_class_teacher' => true,
                        'subjects' => [],
                    ]);
                });

            SubjectTeacherAssignment::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $year->id)
                ->get(['class', 'section', 'subject'])
                ->each(function ($a) use (&$rows) {
                    $key = $a->class . '|' . $a->section;
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

        foreach (($teacher->classes ?? []) as $class) {
            if (! $class) continue;
            $touched = $rows->keys()->contains(fn ($k) => str_starts_with($k, $class . '|'));
            if ($touched) continue;
            $rows->put($class . '|', [
                'class'    => $class,
                'section'  => null,
                'is_class_teacher' => false,
                'subjects' => [],
                'legacy'   => true,
            ]);
        }

        $counts = $year
            ? \App\Models\StudentEnrollment::forActiveYear()->active()
                ->selectRaw('class, section, count(*) as total')
                ->groupBy('class', 'section')
                ->get()
                ->mapWithKeys(fn ($r) => [$r->class . '|' . $r->section => (int) $r->total])
                ->all()
            : [];

        $order = array_flip(\App\Models\Student::classes());

        return $rows->map(function ($row) use ($counts) {
            $row['students'] = $row['section']
                ? ($counts[$row['class'] . '|' . $row['section']] ?? 0)
                : collect($counts)
                    ->filter(fn ($_, $k) => str_starts_with($k, $row['class'] . '|'))
                    ->sum();
            $row['legacy'] = $row['legacy'] ?? false;
            return $row;
        })
        ->sortBy(fn ($r) => [$order[$r['class']] ?? 999, $r['section'] ?? ''])
        ->values();
    }

    protected function subjectRows(Teacher $teacher, ?AcademicYear $year): \Illuminate\Support\Collection
    {
        if (! $year) return collect();

        $order = array_flip(\App\Models\Student::classes());

        return SubjectTeacherAssignment::where('teacher_id', $teacher->id)
            ->where('academic_year_id', $year->id)
            ->get(['subject', 'class', 'section'])
            ->sortBy(fn ($r) => [$r->subject, $order[$r->class] ?? 999, $r->section ?? ''])
            ->values();
    }
}
