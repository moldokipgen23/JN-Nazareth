<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\GradeScale;
use App\Models\Mark;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::withCount('enrollments')
            ->orderByDesc('id')
            ->get();

        return view('admin.academic-years.index', compact('years'));
    }

    public function students(AcademicYear $academicYear, Request $request)
    {
        $classes     = Student::classes();
        $currentClass = $request->input('class');
        $search      = $request->input('search');

        $query = StudentEnrollment::with('student')
            ->where('academic_year_id', $academicYear->id);

        if ($currentClass) {
            $query->where('class', $currentClass);
        }

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('roll_number', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->orderBy('class')->orderByRaw('CAST(roll_number AS UNSIGNED)')->orderBy('id')->paginate(60)->withQueryString();

        // Per-class counts for this year
        $classCounts = StudentEnrollment::where('academic_year_id', $academicYear->id)
            ->selectRaw('class, count(*) as total')
            ->groupBy('class')
            ->pluck('total', 'class')
            ->toArray();

        $total = array_sum($classCounts);

        return view('admin.academic-years.students', compact(
            'academicYear', 'enrollments', 'classes', 'classCounts',
            'currentClass', 'search', 'total'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:30|unique:academic_years,name',
            'starts_on' => 'nullable|date',
            'ends_on'   => 'nullable|date|after_or_equal:starts_on',
        ]);

        AcademicYear::create($data + ['is_active' => false]);

        return back()->with('success', "Academic year \"{$data['name']}\" created.");
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:30|unique:academic_years,name,' . $academicYear->id,
            'starts_on' => 'nullable|date',
            'ends_on'   => 'nullable|date|after_or_equal:starts_on',
        ]);

        $academicYear->update($data);

        return back()->with('success', 'Year updated.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->enrollments()->exists()) {
            return back()->with('error', 'Cannot delete a year that has student enrollments.');
        }

        if ($academicYear->is_active) {
            return back()->with('error', 'Cannot delete the active year.');
        }

        $academicYear->delete();

        return back()->with('success', 'Year deleted.');
    }

    public function activate(AcademicYear $academicYear)
    {
        DB::transaction(function () use ($academicYear) {
            AcademicYear::query()->update(['is_active' => false]);
            $academicYear->update(['is_active' => true]);
        });

        return back()->with('success', "\"{$academicYear->name}\" is now the active year.");
    }

    // ── Promote students ─────────────────────────────────────────────────────

    public function promoteForm(AcademicYear $academicYear, Request $request)
    {
        $academicYear->loadMissing('promotionRule');
        $rule = $academicYear->promotionRule;
        $classes = Student::classes();
        $selectedClass = $request->input('class');

        $classMap = array_flip($classes);
        $lastIndex = count($classes) - 1;

        $enrollments = collect();
        $allowedNext = [];

        if ($selectedClass && in_array($selectedClass, $classes, true)) {
            $enrollments = StudentEnrollment::with('student')
                ->where('academic_year_id', $academicYear->id)
                ->where('class', $selectedClass)
                ->where('status', StudentEnrollment::STATUS_ACTIVE)
                ->orderBy('section')
                ->orderByRaw('CAST(roll_number AS UNSIGNED)')
                ->orderBy('student_id')
                ->get();

            $idx = $classMap[$selectedClass] ?? null;
            $nextClass = ($idx !== null && $idx < $lastIndex) ? $classes[$idx + 1] : null;
            $allowedNext[$selectedClass] = array_values(array_filter([$selectedClass, $nextClass]));

            $enrollmentIds = $enrollments->pluck('id');

            // Load marks
            $marksQuery = Mark::whereIn('student_enrollment_id', $enrollmentIds)
                ->where('academic_year_id', $academicYear->id);
            if ($rule?->exam_id) {
                $marksQuery->where('exam_id', $rule->exam_id);
            }
            $marks = $marksQuery->get()->groupBy('student_enrollment_id');

            // Load attendance
            $attendance = AttendanceRecord::whereIn('student_enrollment_id', $enrollmentIds)
                ->where('academic_year_id', $academicYear->id)
                ->get()
                ->groupBy('student_enrollment_id');

            // Double-promote check
            $targetYears = AcademicYear::where('id', '!=', $academicYear->id)
                ->orderByDesc('id')
                ->get();

            $studentIds = $enrollments->pluck('student_id')->unique();
            $existingEnrollments = StudentEnrollment::whereIn('student_id', $studentIds)
                ->whereIn('academic_year_id', $targetYears->pluck('id'))
                ->get()
                ->keyBy('student_id');

            $passThreshold = $rule?->min_pass_percentage
                ?? GradeScale::where('is_active', true)->where('name', 'like', '%D%')->value('min_percent')
                ?? 35;

            foreach ($enrollments as $e) {
                $studentMarks = $marks->get($e->id, collect());
                $studentAttendance = $attendance->get($e->id, collect());
                $simpleSuggest = $nextClass;

                $pcts = $studentMarks->map(fn($m) => $m->percentage())->filter();
                $avgPct = $pcts->isNotEmpty() ? $pcts->avg() : null;
                $gps = $studentMarks->map(fn($m) => $m->computedGradePoint())->filter();
                $avgGp = $gps->isNotEmpty() ? $gps->avg() : null;

                // Attendance %
                $totalDays = $studentAttendance->count();
                $presentDays = $studentAttendance->whereIn('status', ['present', 'late'])->count();
                $attPct = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : null;

                // Subject pass/fail count
                $failCount = $studentMarks->filter(fn($m) => $m->status() === 'fail')->count();
                $allowedFails = $rule?->fail_subjects_allowed ?? 0;

                if ($rule?->use_cgpa && $avgGp !== null) {
                    $eligible = $avgGp >= (float) ($rule->min_cgpa ?? 0);
                } else {
                    $eligible = $avgPct !== null && $avgPct >= $passThreshold && $failCount <= $allowedFails;
                }
                if ($attPct !== null && $rule?->min_attendance_percentage) {
                    $eligible = $eligible && $attPct >= $rule->min_attendance_percentage;
                }

                if ($avgPct !== null && $avgPct < $passThreshold) {
                    $simpleSuggest = $selectedClass;
                }

                // Per-subject breakdown
                $subjectsBreakdown = $studentMarks->map(fn($m) => [
                    'subject'       => $m->subject,
                    'full_marks'    => $m->full_marks,
                    'pass_marks'    => $m->pass_marks,
                    'theory'        => $m->theory_marks,
                    'assignment'    => $m->assignment_marks,
                    'total'         => $m->total_marks ?? $m->obtained_marks,
                    'pct'           => $m->percentage(),
                    'grade'         => $m->computedGrade() ?? $m->grade,
                    'status'        => $m->status(),
                ])->values();

                $e->marks_avg_pct = $avgPct;
                $e->marks_avg_gp = $avgGp;
                $e->attendance_pct = $attPct;
                $e->eligible = $eligible;
                $e->suggested_next = $simpleSuggest;
                $e->fail_count = $failCount;
                $e->subjects_breakdown = $subjectsBreakdown;

                // Double-promote check
                $alreadyPromoted = null;
                $exp = $existingEnrollments->get($e->student_id);
                if ($exp) {
                    $alreadyPromoted = [
                        'target_year_id' => $exp->academic_year_id,
                        'target_year' => $targetYears->firstWhere('id', $exp->academic_year_id)?->name ?? 'Another year',
                    ];
                }
                $e->already_promoted = $alreadyPromoted;
            }

            // Rank within class by CGPA
            $ranked = $enrollments->sortByDesc('marks_avg_gp')->values();
            $rank = 1;
            foreach ($ranked as $e) {
                $e->rank = $e->marks_avg_gp !== null ? $rank : null;
                if ($e->marks_avg_gp !== null) {
                    $rank++;
                }
            }
        }

        $targetYears = AcademicYear::where('id', '!=', $academicYear->id)
            ->orderByDesc('id')
            ->get();

        $sections = Section::active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('class')
            ->map(fn($g) => $g->pluck('name'))
            ->toArray();

        return view('admin.academic-years.promote', compact(
            'academicYear', 'enrollments', 'classes', 'targetYears',
            'rule', 'lastIndex', 'allowedNext',
            'sections', 'selectedClass'
        ));
    }

    public function promote(Request $request, AcademicYear $academicYear)
    {
        $classes = Student::classes();
        $classMap = array_flip($classes);
        $lastIndex = count($classes) - 1;

        if ($request->boolean('bulk')) {
            // Bulk promotion
            $data = $request->validate([
                'enrollment_ids' => 'required|array',
                'enrollment_ids.*' => 'exists:student_enrollments,id',
                'next_class' => 'required|array',
                'section' => 'nullable|array',
                'target_year_id' => 'required|array',
            ]);

            $enrollments = StudentEnrollment::with('student')
                ->whereIn('id', $data['enrollment_ids'])
                ->where('academic_year_id', $academicYear->id)
                ->get();

            $promoted = 0;
            $errors = [];

            DB::transaction(function () use ($enrollments, $data, $classes, $classMap, $lastIndex, $academicYear, &$promoted, &$errors) {
                foreach ($enrollments as $enrollment) {
                    $nextClass = $data['next_class'][$enrollment->id] ?? null;
                    $section = $data['section'][$enrollment->id] ?? null;
                    $targetYearId = $data['target_year_id'][$enrollment->id] ?? null;

                    if (!$nextClass || !$targetYearId) continue;

                    $idx = $classMap[$enrollment->class] ?? null;
                    $allowed = [$enrollment->class];
                    if ($idx !== null && $idx < $lastIndex) {
                        $allowed[] = $classes[$idx + 1];
                    }
                    if (!in_array($nextClass, $allowed, true)) continue;

                    // Skip double-promote
                    if (StudentEnrollment::where('student_id', $enrollment->student_id)
                        ->where('academic_year_id', $targetYearId)
                        ->exists()
                    ) continue;

                    $targetYear = AcademicYear::find($targetYearId);
                    if (!$targetYear) continue;

                    $sec = !empty($section) ? $section : $enrollment->section;

                    StudentEnrollment::updateOrCreate(
                        [
                            'student_id' => $enrollment->student_id,
                            'academic_year_id' => $targetYear->id,
                        ],
                        [
                            'class' => $nextClass,
                            'section' => $sec,
                            'roll_number' => $enrollment->roll_number,
                            'status' => StudentEnrollment::STATUS_ACTIVE,
                            'enrolled_on' => now()->toDateString(),
                            'remarks' => "Promoted from {$enrollment->class} – {$academicYear->name}",
                        ]
                    );
                    $enrollment->student()->update(['class' => $nextClass, 'section' => $sec]);
                    $promoted++;
                }
            });

            $redirect = route('admin.academic-years.promote.form', $academicYear);
            if ($class = $request->input('class')) {
                $redirect .= '?class=' . urlencode($class);
            }

            return redirect($redirect)
                ->with('success', "{$promoted} student(s) promoted successfully.");
        }

        // Single promotion (existing behavior)
        $data = $request->validate([
            'enrollment_id'  => 'required|exists:student_enrollments,id',
            'target_year_id' => 'required|exists:academic_years,id',
            'next_class'     => 'required|string',
            'section'        => 'nullable|string|max:10',
            'notes'          => 'nullable|string|max:500',
        ]);

        $enrollment = StudentEnrollment::findOrFail($data['enrollment_id']);
        if ($enrollment->academic_year_id !== $academicYear->id) {
            return back()->with('error', 'Enrollment does not belong to this academic year.');
        }

        $idx = $classMap[$enrollment->class] ?? null;
        $allowed = [$enrollment->class];
        if ($idx !== null && $idx < $lastIndex) {
            $allowed[] = $classes[$idx + 1];
        }
        if (! in_array($data['next_class'], $allowed, true)) {
            return back()->with('error', 'Invalid next class.');
        }

        if (StudentEnrollment::where('student_id', $enrollment->student_id)
            ->where('academic_year_id', $data['target_year_id'])
            ->exists()
        ) {
            return back()->with('error', "{$enrollment->student->name} is already enrolled in the target year.");
        }

        $targetYear = AcademicYear::findOrFail($data['target_year_id']);
        $section = ! empty($data['section']) ? $data['section'] : $enrollment->section;
        $remarks = $data['notes'] ?: "Promoted from {$enrollment->class} – {$academicYear->name}";

        DB::transaction(function () use ($enrollment, $targetYear, $data, $section, $remarks) {
            StudentEnrollment::updateOrCreate(
                [
                    'student_id'       => $enrollment->student_id,
                    'academic_year_id' => $targetYear->id,
                ],
                [
                    'class'       => $data['next_class'],
                    'section'     => $section,
                    'roll_number' => $enrollment->roll_number,
                    'status'      => StudentEnrollment::STATUS_ACTIVE,
                    'enrolled_on' => now()->toDateString(),
                    'remarks'     => $remarks,
                ]
            );
            $enrollment->student()->update(['class' => $data['next_class'], 'section' => $section]);
        });

        $class = $request->input('class');
        $redirect = route('admin.academic-years.promote.form', $academicYear);
        if ($class) {
            $redirect .= '?class=' . urlencode($class);
        }

        return redirect($redirect)
            ->with('success', "{$enrollment->student->name} promoted to {$data['next_class']}.");
    }

    // ── Assign roll numbers ──────────────────────────────────────────────

    public function assignRollsForm(AcademicYear $academicYear, Request $request)
    {
        $academicYear->loadMissing('promotionRule');
        $classes = Student::classes();
        $selectedClass = $request->input('class');

        $enrollments = collect();
        if ($selectedClass && in_array($selectedClass, $classes, true)) {
            $enrollments = StudentEnrollment::with('student')
                ->where('academic_year_id', $academicYear->id)
                ->where('class', $selectedClass)
                ->where('status', StudentEnrollment::STATUS_ACTIVE)
                ->orderByRaw('CAST(roll_number AS UNSIGNED)')
                ->orderBy('student_id')
                ->get();

            // Assign temp rank based on roll number or name
            $rank = 1;
            foreach ($enrollments as $e) {
                $e->display_rank = $rank++;
            }
        }

        return view('admin.academic-years.assign-rolls', compact(
            'academicYear', 'enrollments', 'classes', 'selectedClass'
        ));
    }

    public function assignRolls(Request $request, AcademicYear $academicYear)
    {
        $data = $request->validate([
            'class' => 'required|string',
            'roll_order' => 'required|array',
            'roll_order.*' => 'exists:student_enrollments,id',
        ]);

        $enrollments = StudentEnrollment::whereIn('id', $data['roll_order'])
            ->where('academic_year_id', $academicYear->id)
            ->where('class', $data['class'])
            ->where('status', StudentEnrollment::STATUS_ACTIVE)
            ->get()
            ->keyBy('id');

        DB::transaction(function () use ($data, $enrollments) {
            foreach ($data['roll_order'] as $index => $id) {
                if ($enrollment = $enrollments->get($id)) {
                    $enrollment->update(['roll_number' => (string) ($index + 1)]);
                }
            }
        });

        $redirect = route('admin.academic-years.assign-rolls', $academicYear);
        if ($class = $request->input('class')) {
            $redirect .= '?class=' . urlencode($class);
        }

        return redirect($redirect)
            ->with('success', 'Roll numbers assigned successfully.');
    }
}
