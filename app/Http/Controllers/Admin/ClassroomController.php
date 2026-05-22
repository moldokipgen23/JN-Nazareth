<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;

/**
 * The "Classes" area. Admins see every class; teachers see only the
 * classes they are assigned to. Selecting a class shows its students.
 */
class ClassroomController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $allClasses = Member::classes();
        $visible = $user->hasRole('admin')
            ? $allClasses
            : array_values(array_intersect($allClasses, $user->teachingClasses()));

        $counts = Member::selectRaw('class, count(*) as total')
            ->whereNotNull('class')
            ->groupBy('class')
            ->pluck('total', 'class')
            ->toArray();

        $classes = [];
        foreach ($visible as $class) {
            $classes[$class] = $counts[$class] ?? 0;
        }

        return view('admin.classes.index', compact('classes'));
    }

    public function show(string $class)
    {
        abort_unless(in_array($class, Member::classes(), true), 404);

        $user = auth()->user();
        abort_unless($user->hasRole('admin') || $user->teachesClass($class), 403,
            'You are not assigned to this class.');

        $students = Member::where('class', $class)
            ->orderByRaw('CASE WHEN roll_number IS NULL THEN 1 ELSE 0 END')
            ->orderByRaw('CAST(roll_number AS INTEGER)')
            ->orderBy('name')
            ->get();

        return view('admin.classes.show', compact('class', 'students'));
    }
}
