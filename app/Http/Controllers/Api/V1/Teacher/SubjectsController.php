<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SubjectTeacherAssignment;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user()->teacher;
        abort_unless($teacher, 403, 'No teacher profile linked.');

        $year = AcademicYear::current();

        $order = array_flip(Student::classes());

        $subjects = $year
            ? SubjectTeacherAssignment::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $year->id)
                ->get(['subject', 'class', 'section'])
                ->sortBy(fn ($r) => [$r->subject, $order[$r->class] ?? 999, $r->section ?? ''])
                ->values()
            : collect();

        return response()->json([
            'success' => true,
            'data' => [
                'year'     => $year ? ['id' => $year->id, 'name' => $year->name] : null,
                'subjects' => $subjects,
            ],
        ]);
    }
}
