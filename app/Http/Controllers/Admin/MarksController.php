<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\GradeScale;
use App\Models\Mark;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use App\Models\AttendanceRecord;

class MarksController extends Controller
{
    public function index(Request $request)
    {
        $year = AcademicYear::current();
        $view = $request->query('view', 'review');

        $examId  = $request->query('exam');
        $class   = $request->query('class');
        $section = $request->query('section');
        $subject = $request->query('subject');

        $exams = $year
            ? Exam::forActiveYear()->orderBy('sort_order')->orderBy('name')->get()
            : collect();

        $slots = $year
            ? StudentEnrollment::forActiveYear()->active()
                ->select('class', 'section')->groupBy('class', 'section')->get()
            : collect();

        $order = array_flip(Student::classes());
        $slots = $slots->sortBy(fn ($s) => [$order[$s->class] ?? 999, $s->section])->values();

        $subjectList = $year
            ? Mark::forActiveYear()->select('subject')->distinct()->pluck('subject')
                ->merge(\App\Models\SubjectTeacherAssignment::where('academic_year_id', $year->id)->pluck('subject'))
                ->unique()->sort()->values()
            : collect();

        $records = collect();
        $stats   = ['total' => 0, 'pass' => 0, 'fail' => 0, 'ungraded' => 0, 'submitted' => 0];

        $rankings   = collect();
        $analyticsSubjects = collect();
        $subjectStats = [];

        if ($year && $examId && $class && $section && $subject) {
            $records = Mark::where('exam_id', $examId)
                ->where('class', $class)
                ->where('section', $section)
                ->where('subject', $subject)
                ->with(['enrollment.student', 'enteredBy'])
                ->get()
                ->sortBy(fn ($r) => [(int) ($r->enrollment->roll_number ?: 999999), $r->enrollment?->student?->name ?? ''])
                ->values();

            foreach ($records as $r) {
                $stats['total']++;
                $stats[$r->status()]++;
                if ($r->submitted_at) $stats['submitted']++;
            }
        }

        if ($year && $class && $examId) {
            $enrollments = StudentEnrollment::forActiveYear()->active()
                ->where('class', $class)->when($section, fn ($q) => $q->where('section', $section))
                ->with('student')->orderBy('roll_number')->get();

            // Get expected subjects for this class from class_subjects
            $classSubjectNames = \App\Models\ClassSubject::where('academic_year_id', $year->id)
                ->where('class', $class)
                ->when($section, fn ($q) => $q->where(function ($q) use ($section) {
                    $q->whereNull('section')->orWhere('section', $section);
                }))
                ->with('subject')
                ->get()
                ->pluck('subject.name')
                ->sort()
                ->values();

            // Fallback: if no class_subjects setup yet, derive from marks table
            $allSubjects = $classSubjectNames->isNotEmpty()
                ? $classSubjectNames
                : Mark::where('academic_year_id', $year->id)
                    ->where('exam_id', $examId)->where('class', $class)
                    ->when($section, fn ($q) => $q->where('section', $section))
                    ->select('subject')->distinct()->pluck('subject')->sort()->values();

            // Check per-subject: are ALL marks submitted for this subject?
            $pendingSubjects = [];
            foreach ($allSubjects as $subj) {
                $hasDraft = Mark::where('academic_year_id', $year->id)
                    ->where('exam_id', $examId)->where('class', $class)
                    ->when($section, fn ($q) => $q->where('section', $section))
                    ->where('subject', $subj)
                    ->whereNull('submitted_at')
                    ->exists();
                if ($hasDraft) {
                    $pendingSubjects[] = $subj;
                }
            }

            $allSubmitted = empty($pendingSubjects);

            // Only build rankings/results if all subjects are fully submitted
            $analyticsSubjects = collect();
            $rows = [];
            $rankings = collect();
            $subjectStats = [];

            if ($allSubmitted) {
                $marks = Mark::where('academic_year_id', $year->id)
                    ->where('exam_id', $examId)->where('class', $class)
                    ->when($section, fn ($q) => $q->where('section', $section))
                    ->whereNotNull('submitted_at')
                    ->get()->groupBy('student_enrollment_id');

                $analyticsSubjects = $allSubjects;

                foreach ($enrollments as $enrollment) {
                    $studentMarks = $marks->get($enrollment->id, collect());
                    $row = ['enrollment' => $enrollment, 'subjectData' => [], 'totalPct' => 0, 'totalGp' => 0, 'markedSubjects' => 0, 'submitted' => true];
                    foreach ($analyticsSubjects as $subj) {
                        $mark = $studentMarks->firstWhere('subject', $subj);
                        if ($mark && $mark->total_marks !== null) {
                            $pct = $mark->percentage();
                            $gp  = $mark->computedGradePoint();
                            $row['subjectData'][$subj] = ['pct' => $pct, 'grade' => $mark->computedGrade() ?? $mark->grade, 'gp' => $gp];
                            if ($pct !== null) { $row['totalPct'] += $pct; $row['totalGp'] += ($gp ?? 0); $row['markedSubjects']++; }
                            if (!$mark->submitted_at) $row['submitted'] = false;
                        } else { $row['subjectData'][$subj] = null; $row['submitted'] = false; }
                    }
                    $c = $row['markedSubjects'];
                    $row['avgPct'] = $c > 0 ? round($row['totalPct'] / $c, 2) : null;
                    $row['cgpa']   = $c > 0 ? round($row['totalGp'] / $c, 2) : null;
                    $rows[] = $row;
                }

                $rankings = collect($rows)->sortByDesc(fn ($r) => $r['cgpa'] ?? 0)
                    ->values()->map(fn ($r, $i) => array_merge($r, ['rank' => $i + 1]));

                foreach ($analyticsSubjects as $subj) {
                    $pcts = []; $grades = [];
                    foreach ($rankings as $r) {
                        $sd = $r['subjectData'][$subj] ?? null;
                        if ($sd && $sd['pct'] !== null) { $pcts[] = $sd['pct']; $grades[] = $sd['grade']; }
                    }
                    $cnt = count($pcts);
                    $subjectStats[$subj] = [
                        'avg' => $cnt > 0 ? round(array_sum($pcts) / $cnt, 2) : null,
                        'count' => $cnt, 'gradeDist' => array_count_values($grades),
                        'highest' => $cnt > 0 ? max($pcts) : null,
                        'lowest'  => $cnt > 0 ? min($pcts) : null,
                    ];
                }
            }
        }

        // Submission summary per subject (for review tab) — only counts students with marks
        $submissionStatus = collect();
        if ($year && $examId && $class && $section) {
            $totalStudents = StudentEnrollment::forActiveYear()->active()
                ->where('class', $class)->where('section', $section)->count();
            $subjectsWithMarks = Mark::where('academic_year_id', $year->id)
                ->where('exam_id', $examId)->where('class', $class)->where('section', $section)
                ->select('subject')
                ->selectRaw('COUNT(*) as total')
                ->selectRaw('SUM(CASE WHEN submitted_at IS NOT NULL THEN 1 ELSE 0 END) as submitted_count')
                ->groupBy('subject')->get();
            $submissionStatus = $subjectsWithMarks->map(fn ($s) => [
                'subject' => $s->subject,
                'total' => $s->total,
                'submitted_count' => $s->submitted_count,
                'expected' => $totalStudents,
            ]);
        }

        return view('admin.marks.index', [
            'year'     => $year,
            'exams'    => $exams,
            'slots'    => $slots,
            'subjectList' => $subjectList,
            'examId'   => $examId,
            'class'    => $class,
            'section'  => $section,
            'subject'  => $subject,
            'records'  => $records,
            'stats'    => $stats,
            'view'     => $view,
            'rankings' => $rankings,
            'analyticsSubjects' => $analyticsSubjects,
            'subjectStats' => $subjectStats,
            'submissionStatus' => $submissionStatus,
            'allSubmitted' => $allSubmitted ?? false,
            'pendingSubjects' => $pendingSubjects ?? [],
        ]);
    }

    public function exportCsv(Request $request)
    {
        $year = AcademicYear::current();
        $examId  = $request->query('exam');
        $class   = $request->query('class');
        $section = $request->query('section');
        $subject = $request->query('subject');

        if (!$year || !$examId || !$class || !$subject) {
            return back()->with('error', 'Select exam, class, and subject to export.');
        }

        $records = Mark::where('academic_year_id', $year->id)
            ->where('exam_id', $examId)
            ->where('class', $class)
            ->when($section, fn ($q) => $q->where('section', $section))
            ->where('subject', $subject)
            ->with(['enrollment.student'])
            ->get()
            ->sortBy(fn ($r) => [(int) ($r->enrollment->roll_number ?: 999999), $r->enrollment?->student?->name ?? '']);

        $examName = Exam::find($examId)?->name ?? 'exam';

        $csv = "Roll No,Student Name,Class,Section,Subject,Theory,Assignment,Total,Full Marks,Pass Marks,Grade,Status\n";
        foreach ($records as $r) {
            $status = $r->status();
            $csv .= implode(',', [
                $r->enrollment->roll_number ?? '',
                '"'.str_replace('"', '""', $r->enrollment->student?->name ?? '').'"',
                $r->class,
                $r->section,
                $r->subject,
                $r->theory_marks ?? '',
                $r->assignment_marks ?? '',
                $r->total_marks ?? '',
                $r->full_marks,
                $r->pass_marks,
                $r->grade ?? '',
                $status,
            ])."\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="marks-'.$examName.'-'.$class.'-'.$subject.'.csv"',
        ]);
    }

    public function exportCsvResults(Request $request)
    {
        $year = AcademicYear::current();
        $examId = $request->query('exam');
        $class  = $request->query('class');
        $section = $request->query('section');

        if (!$year || !$examId || !$class) {
            return back()->with('error', 'Select exam and class to export results.');
        }

        $exam = Exam::find($examId);
        if (!$exam) return back()->with('error', 'Exam not found.');

        $enrollments = StudentEnrollment::forActiveYear()->active()
            ->where('class', $class)->when($section, fn ($q) => $q->where('section', $section))
            ->with('student')->orderBy('roll_number')->get();

        $marks = Mark::where('academic_year_id', $year->id)
            ->where('exam_id', $examId)->where('class', $class)
            ->when($section, fn ($q) => $q->where('section', $section))
            ->get()->groupBy('student_enrollment_id');

        $subjects = Mark::where('academic_year_id', $year->id)
            ->where('exam_id', $examId)->where('class', $class)
            ->when($section, fn ($q) => $q->where('section', $section))
            ->select('subject')->distinct()->pluck('subject')->sort()->values();

        // Build rows with ranking
        $rows = [];
        foreach ($enrollments as $enrollment) {
            $studentMarks = $marks->get($enrollment->id, collect());
            $row = ['roll' => $enrollment->roll_number, 'name' => $enrollment->student?->name ?? '', 'subjects' => [], 'totalPct' => 0, 'count' => 0];
            foreach ($subjects as $subj) {
                $m = $studentMarks->firstWhere('subject', $subj);
                $pct = $m?->percentage();
                $row['subjects'][$subj] = [
                    'total' => $m?->total_marks ?? '',
                    'full'  => $m?->full_marks ?? '',
                    'pct'   => $pct,
                    'grade' => $m?->grade ?? '',
                    'gp'    => $m?->computedGradePoint(),
                ];
                if ($pct !== null) { $row['totalPct'] += $pct; $row['count']++; }
            }
            $row['avgPct'] = $row['count'] > 0 ? round($row['totalPct'] / $row['count'], 2) : null;
            $rows[] = $row;
        }

        // Sort by avgPct desc for ranking
        usort($rows, fn ($a, $b) => ($b['avgPct'] ?? 0) <=> ($a['avgPct'] ?? 0));

        // Build CSV
        $header = ['Rank', 'Roll No', 'Student Name'];
        foreach ($subjects as $subj) {
            $header[] = $subj.' (Total)';
            $header[] = $subj.' (%)';
            $header[] = $subj.' (Grade)';
        }
        $header[] = 'Avg %';
        $header[] = 'CGPA';

        $csv = implode(',', array_map(fn($h) => '"'.$h.'"', $header))."\n";
        foreach ($rows as $i => $row) {
            $line = [$i + 1, $row['roll'] ?? '', '"'.str_replace('"', '""', $row['name']).'"'];
            foreach ($subjects as $subj) {
                $s = $row['subjects'][$subj] ?? [];
                $line[] = $s['total'] ?? '';
                $line[] = $s['pct'] !== null ? $s['pct'].'%' : '';
                $line[] = $s['grade'] ?? '';
            }
            $line[] = $row['avgPct'] !== null ? $row['avgPct'].'%' : '';
            // Compute CGPA (avg of grade points)
            $gps = [];
            foreach ($row['subjects'] as $s) { if ($s['gp'] !== null) $gps[] = $s['gp']; }
            $line[] = count($gps) > 0 ? number_format(array_sum($gps) / count($gps), 2) : '';
            $csv .= implode(',', $line)."\n";
        }

        $sectionLabel = $section ? '_Sec-'.$section : '';
        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="results-'.$exam->name.'-'.$class.$sectionLabel.'.csv"',
        ]);
    }

    public function exportAllResultCards(Request $request)
    {
        $year = AcademicYear::current();
        $examId = $request->query('exam');
        $class  = $request->query('class');
        $section = $request->query('section');

        if (!$year || !$examId || !$class) {
            return back()->with('error', 'Select exam and class to export.');
        }

        $exam = Exam::find($examId);
        if (!$exam) return back()->with('error', 'Exam not found.');

        $enrollments = StudentEnrollment::forActiveYear()->active()
            ->where('class', $class)->when($section, fn ($q) => $q->where('section', $section))
            ->with('student')->orderBy('roll_number')->get();

        if ($enrollments->isEmpty()) {
            return back()->with('error', 'No students found.');
        }

        $zip = new ZipArchive();
        $zipName = tempnam(sys_get_temp_dir(), 'result_cards_');
        if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Could not create archive.');
        }

        $subjects = Mark::where('academic_year_id', $year->id)
            ->where('exam_id', $examId)->where('class', $class)
            ->when($section, fn ($q) => $q->where('section', $section))
            ->whereNotNull('submitted_at')
            ->select('subject')->distinct()->pluck('subject')->sort()->values();

        $marks = Mark::where('academic_year_id', $year->id)
            ->where('exam_id', $examId)->where('class', $class)
            ->when($section, fn ($q) => $q->where('section', $section))
            ->whereNotNull('submitted_at')
            ->get()->groupBy('student_enrollment_id');

        $attendance = \App\Models\AttendanceRecord::where('academic_year_id', $year->id)
            ->where('class', $class)
            ->when($section, fn ($q) => $q->where('section', $section))
            ->get()->groupBy('student_enrollment_id');

        $gradeScale = GradeScale::active()->orderBy('min_percent')->get();

        foreach ($enrollments as $enrollment) {
            $student = $enrollment->student;
            $studentMarks = $marks->get($enrollment->id, collect());

            $subjectData = [];
            $totalObtained = 0; $totalFull = 0;
            foreach ($subjects as $subj) {
                $m = $studentMarks->firstWhere('subject', $subj);
                $pct = $m?->percentage();
                $gp = $m?->computedGradePoint();
                $subjectData[] = [
                    'name' => $subj,
                    'full_marks' => $m?->full_marks ?? 0,
                    'pass_marks' => $m?->pass_marks ?? 0,
                    'theory' => $m?->theory_marks,
                    'assignment' => $m?->assignment_marks,
                    'total' => $m?->total_marks,
                    'pct' => $pct,
                    'grade' => $m?->computedGrade() ?? $m?->grade ?? '',
                    'gp' => $gp,
                    'status' => $m?->status() ?? 'ungraded',
                ];
                if ($m?->total_marks) $totalObtained += (float) $m->total_marks;
                if ($m?->full_marks) $totalFull += (float) $m->full_marks;
            }

            $gps = array_filter(array_column($subjectData, 'gp'));
            $cgpa = count($gps) > 0 ? round(array_sum($gps) / count($gps), 2) : null;

            // Ranking
            $allEnr = StudentEnrollment::forActiveYear()->active()
                ->where('class', $class)->when($section, fn ($q) => $q->where('section', $section))
                ->pluck('id');
            $allMarks = Mark::where('academic_year_id', $year->id)
                ->where('exam_id', $examId)->where('class', $class)
                ->when($section, fn ($q) => $q->where('section', $section))
                ->get()->groupBy('student_enrollment_id');
            $ranked = [];
            foreach ($allEnr as $eid) {
                $sm = $allMarks->get($eid, collect());
                $gps2 = [];
                foreach ($subjects as $subj) {
                    $mm = $sm->firstWhere('subject', $subj);
                    if ($mm) { $gp2 = $mm->computedGradePoint(); if ($gp2 !== null) $gps2[] = $gp2; }
                }
                $ranked[$eid] = count($gps2) > 0 ? round(array_sum($gps2) / count($gps2), 2) : 0;
            }
            arsort($ranked);
            $rank = array_search($enrollment->id, array_keys($ranked)) + 1;

            $attRecs = $attendance->get($enrollment->id, collect());
            $attSummary = ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];
            foreach ($attRecs as $a) { $attSummary[$a->status]++; }
            $totalDays = array_sum($attSummary);
            $presentDays = $attSummary['present'] + $attSummary['late'] + $attSummary['excused'];
            $attPct = $totalDays > 0 ? round($presentDays / $totalDays * 100, 1) : null;

            $pdf = Pdf::loadView('admin.students.result-card-pdf', [
                'student' => $student,
                'enrollment' => $enrollment,
                'exam' => $exam,
                'subjectData' => $subjectData,
                'totalObtained' => $totalObtained,
                'totalFull' => $totalFull,
                'cgpa' => $cgpa,
                'rank' => $rank,
                'totalStudents' => count($allEnr),
                'attSummary' => $attSummary,
                'attPct' => $attPct,
            ])->setPaper('a4', 'portrait');

            $filename = 'result-'.str_replace(['/', ' '], '-', $student->name).'.pdf';
            $zip->addFromString($filename, $pdf->output());
        }

        $zip->close();

        $sectionLabel = $section ? '_Sec-'.$section : '';
        $zipFilename = 'result-cards_'.$exam->name.'_'.$class.$sectionLabel.'.zip';
        return Response::download($zipName, $zipFilename)->deleteFileAfterSend(true);
    }

    public function gradesheet(Request $request)
    {
        $year = AcademicYear::current();
        $examId = $request->query('exam');
        $class  = $request->query('class');
        $section = $request->query('section');
        $format = $request->query('format', 'pdf');

        if (!$year || !$examId || !$class) {
            return back()->with('error', 'Select exam and class.');
        }

        $exam = Exam::find($examId);
        if (!$exam) return back()->with('error', 'Exam not found.');

        // Verify all subjects submitted before export — use class_subjects if available
        $expectedSubjects = \App\Models\ClassSubject::where('academic_year_id', $year->id)
            ->where('class', $class)
            ->when($section, fn ($q) => $q->where(function ($q) use ($section) {
                $q->whereNull('section')->orWhere('section', $section);
            }))
            ->with('subject')->get()->pluck('subject.name');

        $subjList = $expectedSubjects->isNotEmpty()
            ? $expectedSubjects
            : Mark::where('academic_year_id', $year->id)
                ->where('exam_id', $examId)->where('class', $class)
                ->when($section, fn ($q) => $q->where('section', $section))
                ->select('subject')->distinct()->pluck('subject');

        $pendingSubj = [];
        foreach ($subjList as $subj) {
            if (Mark::where('academic_year_id', $year->id)->where('exam_id', $examId)
                ->where('class', $class)->when($section, fn ($q) => $q->where('section', $section))
                ->where('subject', $subj)->whereNull('submitted_at')->exists()
            ) { $pendingSubj[] = $subj; }
        }
        if ($pendingSubj) {
            return back()->with('error', 'Cannot export results: '.implode(', ', $pendingSubj).' still have unsubmitted marks.');
        }

        $enrollments = StudentEnrollment::forActiveYear()->active()
            ->where('class', $class)->when($section, fn ($q) => $q->where('section', $section))
            ->with('student')->orderBy('roll_number')->get();

        $marks = Mark::where('academic_year_id', $year->id)
            ->where('exam_id', $examId)->where('class', $class)
            ->when($section, fn ($q) => $q->where('section', $section))
            ->whereNotNull('submitted_at')
            ->get()->groupBy('student_enrollment_id');

        $subjects = Mark::where('academic_year_id', $year->id)
            ->where('exam_id', $examId)->where('class', $class)
            ->when($section, fn ($q) => $q->where('section', $section))
            ->whereNotNull('submitted_at')
            ->select('subject')->distinct()->pluck('subject')->sort()->values();

        // Build rows with ranking
        $rows = [];
        foreach ($enrollments as $enrollment) {
            $studentMarks = $marks->get($enrollment->id, collect());
            $row = [
                'roll' => $enrollment->roll_number,
                'name' => $enrollment->student?->name ?? '',
                'subjectData' => [],
                'totalObtained' => 0, 'totalFull' => 0,
            ];
            foreach ($subjects as $subj) {
                $m = $studentMarks->firstWhere('subject', $subj);
                $total = $m ? ($m->total_marks ?? $m->obtained_marks) : null;
                $full  = $m?->full_marks ?? 0;
                $pct   = $m?->percentage();
                $row['subjectData'][$subj] = ['total' => $total, 'full' => $full, 'pct' => $pct];
                if ($total !== null) { $row['totalObtained'] += (float) $total; $row['totalFull'] += (float) $full; }
            }
            $row['avgPct'] = $row['totalFull'] > 0 ? round(($row['totalObtained'] / $row['totalFull']) * 100, 2) : null;
            $rows[] = $row;
        }

        $sectionLabel = $section ? '_Sec-'.$section : '';

        if ($format === 'csv') {
            $header = ['Sl No', 'Roll No', 'Student Name'];
            foreach ($subjects as $subj) { $header[] = $subj.' (T/F)'; }
            $header[] = 'Total (Obt/Full)';
            $header[] = 'Avg %';

            $csv = implode(',', array_map(fn($h) => '"'.$h.'"', $header))."\n";
            foreach ($rows as $i => $r) {
                $line = [$i + 1, $r['roll'] ?? '', '"'.str_replace('"', '""', $r['name']).'"'];
                foreach ($subjects as $subj) {
                    $sd = $r['subjectData'][$subj] ?? null;
                    $line[] = $sd ? ($sd['total'] ?? '').'/'.($sd['full'] ?? '') : '';
                }
                $line[] = $r['totalObtained'].'/'.$r['totalFull'];
                $line[] = $r['avgPct'] !== null ? $r['avgPct'].'%' : '';
                $csv .= implode(',', $line)."\n";
            }

            return Response::make($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="gradesheet_'.$exam->name.'_'.$class.$sectionLabel.'.csv"',
            ]);
        }

        $pdf = Pdf::loadView('admin.marks.gradesheet-pdf', [
            'year' => $year, 'exam' => $exam, 'class' => $class,
            'section' => $section, 'subjects' => $subjects, 'rows' => $rows,
        ])->setPaper('a4', 'landscape');

        $filename = 'gradesheet_'.$exam->name.'_'.$class.$sectionLabel.'.pdf';
        $filename = str_replace(['/', ' '], '_', $filename);
        return $pdf->download($filename);
    }

    public function resetSubmission(Request $request, Mark $mark)
    {
        $mark->update(['submitted_at' => null]);
        return back()->with('success', 'Submission reset. Teacher can now edit marks.');
    }

    /**
     * Bulk download results for ALL classes in an exam.
     * Produces a ZIP containing one file per class (PDF or CSV).
     */
    public function bulkDownload(Request $request)
    {
        $year = AcademicYear::current();
        $examId = $request->query('exam');
        $format = $request->query('format', 'pdf'); // pdf or csv
        $showSubjectWise = $request->boolean('subject_wise', true);

        if (!$year || !$examId) {
            return back()->with('error', 'Select an exam.');
        }

        $exam = Exam::find($examId);
        if (!$exam) return back()->with('error', 'Exam not found.');

        // Get all classes that have submitted marks for this exam
        $classSections = Mark::where('academic_year_id', $year->id)
            ->where('exam_id', $examId)
            ->whereNotNull('submitted_at')
            ->select('class', 'section')
            ->distinct()
            ->get()
            ->sortBy(fn ($r) => array_flip(Student::classes())[$r->class] ?? 999);

        if ($classSections->isEmpty()) {
            return back()->with('error', 'No marks found for this exam.');
        }

        $zip = new ZipArchive();
        $zipName = tempnam(sys_get_temp_dir(), 'bulk_results_');
        if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Could not create archive.');
        }

        $added = 0;

        foreach ($classSections as $cs) {
            $classData = $this->buildClassResultData($year, $exam, $cs->class, $cs->section);

            if ($format === 'csv') {
                $csv = $this->buildClassResultCsv($exam, $cs->class, $cs->section, $classData, $showSubjectWise);
                $filename = 'result-'.$exam->name.'-'.$cs->class.($cs->section ? '_Sec-'.$cs->section : '').'.csv';
                $filename = str_replace(['/', ' '], '_', $filename);
                $zip->addFromString($filename, $csv);
                $added++;
            } else {
                $pdf = Pdf::loadView('admin.marks.result-pdf', [
                    'year' => $year,
                    'exam' => $exam,
                    'classResults' => [$classData],
                    'class' => $cs->class,
                    'section' => $cs->section,
                    'showSubjectWise' => $showSubjectWise,
                ])->setPaper('a4', 'portrait');

                $filename = 'result-'.$exam->name.'-'.$cs->class.($cs->section ? '_Sec-'.$cs->section : '').'.pdf';
                $filename = str_replace(['/', ' '], '_', $filename);
                $zip->addFromString($filename, $pdf->output());
                $added++;
            }
        }

        $zip->close();

        $ext = $format;
        $label = $showSubjectWise ? 'Subject-wise' : 'Ranking-only';
        $zipFilename = 'bulk-results_'.$exam->name.'_'.$label.'_'.$ext.'.zip';
        $zipFilename = str_replace(['/', ' '], '_', $zipFilename);

        return Response::download($zipName, $zipFilename)->deleteFileAfterSend(true);
    }

    /**
     * Build result data array for a single class+section.
     */
    private function buildClassResultData($year, $exam, $class, $section): array
    {
        $enrollments = StudentEnrollment::forActiveYear()->active()
            ->where('class', $class)->when($section, fn ($q) => $q->where('section', $section))
            ->with('student')->orderBy('roll_number')->get();

        $marks = Mark::where('academic_year_id', $year->id)
            ->where('exam_id', $examId)->where('class', $class)
            ->when($section, fn ($q) => $q->where('section', $section))
            ->whereNotNull('submitted_at')
            ->get()->groupBy('student_enrollment_id');

        $subjects = Mark::where('academic_year_id', $year->id)
            ->where('exam_id', $examId)->where('class', $class)
            ->when($section, fn ($q) => $q->where('section', $section))
            ->whereNotNull('submitted_at')
            ->select('subject')->distinct()->pluck('subject')->sort()->values();

        $rows = [];
        foreach ($enrollments as $enrollment) {
            $studentMarks = $marks->get($enrollment->id, collect());
            $row = [
                'roll' => $enrollment->roll_number,
                'name' => $enrollment->student?->name ?? '',
                'subjectData' => [],
                'totalPct' => 0, 'totalGp' => 0, 'markedSubjects' => 0,
            ];
            foreach ($subjects as $subj) {
                $m = $studentMarks->firstWhere('subject', $subj);
                if ($m && $m->total_marks !== null) {
                    $pct = $m->percentage();
                    $gp  = $m->computedGradePoint();
                    $row['subjectData'][$subj] = ['pct' => $pct, 'grade' => $m->computedGrade() ?? $m->grade, 'gp' => $gp];
                    if ($pct !== null) { $row['totalPct'] += $pct; $row['totalGp'] += ($gp ?? 0); $row['markedSubjects']++; }
                } else {
                    $row['subjectData'][$subj] = null;
                }
            }
            $c = $row['markedSubjects'];
            $row['avgPct'] = $c > 0 ? round($row['totalPct'] / $c, 2) : null;
            $row['cgpa']   = $c > 0 ? round($row['totalGp'] / $c, 2) : null;
            $rows[] = $row;
        }

        $ranked = collect($rows)->sortByDesc(fn ($r) => $r['cgpa'] ?? 0)
            ->values()->map(fn ($r, $i) => array_merge($r, ['rank' => $i + 1]))->toArray();

        return [
            'class' => $class,
            'section' => $section,
            'total' => count($ranked),
            'subjects' => $subjects->toArray(),
            'rankings' => $ranked,
        ];
    }

    /**
     * Build CSV string for a single class result.
     */
    private function buildClassResultCsv($exam, $class, $section, array $classData, bool $showSubjectWise): string
    {
        $schoolName = \App\Helpers\Settings::get('site_name', 'JN Nazareth School');
        $lines = [];

        $lines[] = $schoolName;
        $lines[] = 'Churachandpur, Manipur';
        $lines[] = '';
        $lines[] = 'EXAMINATION RESULT';
        $lines[] = $exam->name.($exam->code ? ' ('.$exam->code.')' : '');
        $lines[] = 'Class: '.$class.($section ? ' - Section '.$section : '');
        $lines[] = '';
        $lines[] = 'Rank,Roll No,Student Name'.($showSubjectWise ? implode('', array_map(fn($s) => ','.$s.' (%)', $classData['subjects'])) : '').',Avg %,CGPA';
        $lines[] = str_repeat('-', 80);

        foreach ($classData['rankings'] as $r) {
            $row = [$r['rank'], $r['roll'] ?? '', '"'.str_replace('"', '""', $r['name']).'"'];
            if ($showSubjectWise) {
                foreach ($classData['subjects'] as $subj) {
                    $sd = $r['subjectData'][$subj] ?? null;
                    $row[] = $sd && $sd['pct'] !== null ? $sd['pct'].'%' : '';
                }
            }
            $row[] = $r['avgPct'] !== null ? $r['avgPct'].'%' : '';
            $row[] = $r['cgpa'] !== null ? number_format($r['cgpa'], 2) : '';
            $lines[] = implode(',', $row);
        }

        $lines[] = '';
        $lines[] = 'Authorised by '.$schoolName.' Administration';
        $lines[] = 'Generated on: '.now()->format('d M Y');

        return implode("\n", $lines)."\n";
    }

    public function update(Request $request, Mark $mark)
    {
        $data = $request->validate([
            'theory_marks'     => 'nullable|numeric|min:0',
            'assignment_marks' => 'nullable|numeric|min:0',
            'total_marks'      => 'nullable|numeric|min:0',
            'obtained_marks'   => 'nullable|numeric|min:0|max:9999',
            'grade'            => 'nullable|string|max:5',
            'remarks'          => 'nullable|string|max:500',
        ]);

        if (isset($data['total_marks']) && $data['total_marks'] !== null
            && (float) $data['total_marks'] > (float) $mark->full_marks) {
            return back()->with('error', "Total marks cannot exceed full marks ({$mark->full_marks}).");
        }

        $mark->update([
            'theory_marks'     => $data['theory_marks'] ?? null,
            'assignment_marks' => $data['assignment_marks'] ?? null,
            'total_marks'      => $data['total_marks'] ?? null,
            'obtained_marks'   => $data['total_marks'] ?? $data['obtained_marks'] ?? null,
            'grade'            => $data['grade'] ?? $mark->computedGrade(),
            'remarks'          => $data['remarks'] ?? null,
            'submitted_at'     => null, // Reset submission on admin override
            'entered_by'       => auth()->id(),
        ]);

        return back()->with('success', 'Mark updated and submission reset.');
    }
}
