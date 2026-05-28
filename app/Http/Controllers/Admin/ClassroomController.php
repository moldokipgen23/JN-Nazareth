<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;

/**
 * The "Classes" area. Admins see every class; teachers see only the
 * classes they are assigned to. Selecting a class shows its students.
 */
class ClassroomController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $activeYear = AcademicYear::current();
        $isAdmin = $user->hasRole('admin');

        $allClasses = Student::classes();
        $visible = $isAdmin
            ? $allClasses
            : array_values(array_intersect($allClasses, $user->teachingClasses()));

        $counts = StudentEnrollment::query()
            ->forActiveYear()
            ->active()
            ->whereNotNull('class')
            ->groupBy('class')
            ->selectRaw('class, count(*) as total')
            ->pluck('total', 'class')
            ->toArray();

        $classes = [];
        foreach ($visible as $class) {
            $classes[$class] = $counts[$class] ?? 0;
        }

        return view('admin.classes.index', compact('classes', 'activeYear', 'isAdmin'));
    }

    public function show(string $class)
    {
        abort_unless(in_array($class, Student::classes(), true), 404);

        $user = auth()->user();
        abort_unless($user->hasRole('admin') || $user->teachesClass($class), 403,
            'You are not assigned to this class.');

        $activeYear = AcademicYear::current();
        $isAdmin = $user->hasRole('admin');

        // Sections for this class with student counts
        $sectionNames = Section::active()
            ->where('class', $class)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $sectionCounts = StudentEnrollment::forActiveYear()
            ->active()
            ->where('class', $class)
            ->whereNotNull('section')
            ->selectRaw('section, count(*) as total')
            ->groupBy('section')
            ->pluck('total', 'section')
            ->toArray();

        return view('admin.classes.show', compact(
            'class', 'sectionNames', 'sectionCounts', 'activeYear', 'isAdmin'
        ));
    }
}
