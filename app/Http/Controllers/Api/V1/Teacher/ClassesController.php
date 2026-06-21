<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassTeacherAssignment;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\SubjectTeacherAssignment;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teacher = $this->getTeacher($request);
        $year    = AcademicYear::current();

        $classes = $this->classRows($teacher, $year);

        return response()->json([
            'success' => true,
            'data' => [
                'year'    => $year ? ['id' => $year->id, 'name' => $year->name] : null,
                'classes' => $classes,
            ],
        ]);
    }

    public function show(Request $request, string $class): JsonResponse
    {
        $teacher = $this->getTeacher($request);
        $year    = AcademicYear::current();
        $user    = $request->user();

        if (! $user->isAdmin()) {
            $allowed = $this->classRows($teacher, $year)->pluck('class')->all();
            if (! in_array($class, $allowed, true)) {
                return response()->json(['success' => false, 'message' => 'Class not found.'], 404);
            }
        }

        $sections = collect();
        if ($year) {
            $sections = collect()
                ->merge(ClassTeacherAssignment::where('teacher_id', $teacher->id)
                    ->where('academic_year_id', $year->id)
                    ->where('class', $class)->pluck('section'))
                ->merge(SubjectTeacherAssignment::where('teacher_id', $teacher->id)
                    ->where('academic_year_id', $year->id)
                    ->where('class', $class)->pluck('section'))
                ->filter()->unique()->sort()->values();
        }
        if ($sections->isEmpty()) $sections = collect(['A']);

        $students = $year
            ? StudentEnrollment::forActiveYear()->active()
                ->where('class', $class)
                ->whereIn('section', $sections)
                ->with('student')
                ->orderBy('section')->orderBy('roll_number')
                ->get()
                ->map(fn ($e) => [
                    'id'            => $e->id,
                    'roll_number'   => $e->roll_number,
                    'section'       => $e->section,
                    'student_name'  => $e->student?->name ?? '',
                    'father_name'   => $e->student?->father_name ?? '',
                    'parent_phone'  => $e->student?->parent_phone ?? '',
                ])
            : collect();

        $mySubjects = $year
            ? SubjectTeacherAssignment::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $year->id)
                ->where('class', $class)
                ->pluck('subject')->unique()->values()
            : collect();

        return response()->json([
            'success' => true,
            'data' => [
                'class'      => $class,
                'year'       => $year ? ['id' => $year->id, 'name' => $year->name] : null,
                'sections'   => $sections,
                'students'   => $students,
                'mySubjects' => $mySubjects,
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
            ClassTeacherAssignment::where('teacher_id', $teacher->id)
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
            ? StudentEnrollment::forActiveYear()->active()
                ->selectRaw('class, section, count(*) as total')
                ->groupBy('class', 'section')
                ->get()
                ->mapWithKeys(fn ($r) => [$r->class . '|' . $r->section => (int) $r->total])
                ->all()
            : [];

        $order = array_flip(Student::classes());

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
}
