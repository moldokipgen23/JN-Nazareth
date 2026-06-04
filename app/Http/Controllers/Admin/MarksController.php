<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\DivisionRule;
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
            ? Exam::forActiveYear()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get()
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

        // When class is selected, filter subject dropdown to class_subjects only
        $filteredSubjectList = $subjectList;
        if ($class && $year) {
            $classSubjects = \App\Models\ClassSubject::where('academic_year_id', $year->id)
                ->where('class', $class)
                ->when($section, fn ($q) => $q->where(function ($q) use ($section) {
                    $q->whereNull('section')->orWhere('section', $section);
                }))
                ->with('subject')->get()->pluck('subject.name')->sort()->values();
            if ($classSubjects->isNotEmpty()) {
                $filteredSubjectList = $classSubjects;
            }
        }

        $records = collect();
        $stats   = ['total' => 0, 'pass' => 0, 'fail' => 0, 'ungraded' => 0, 'submitted' => 0, 'approved' => 0];

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
                ->filter(fn ($r) => $r->enrollment !== null)
                ->sortBy(fn ($r) => [(int) ($r->enrollment?->roll_number ?: 999999), $r->enrollment?->student?->name ?? ''])
                ->values();

            foreach ($records as $r) {
                $stats['total']++;
                $stats[$r->status()]++;
                if ($r->submitted_at) $stats['submitted']++;
                if ($r->approved_at) $stats['approved']++;
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

            // Check per-subject: every enrolled student must have submitted marks
            $enrolledCount = StudentEnrollment::forActiveYear()->active()
                ->where('class', $class)->where('section', $section)->count();
            $pendingSubjects = [];
            foreach ($allSubjects as $subj) {
                $submittedCount = Mark::where('academic_year_id', $year->id)
                    ->where('exam_id', $examId)->where('class', $class)
                    ->when($section, fn ($q) => $q->where('section', $section))
                    ->where('subject', $subj)
                    ->whereNotNull('submitted_at')
                    ->count();
                $isComplete = $submittedCount >= $enrolledCount && $enrolledCount > 0;
                if (!$isComplete) {
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
                    $row = ['enrollment' => $enrollment, 'subjectData' => [], 'totalPct' => 0, 'totalGp' => 0, 'totalRaw' => 0, 'markedSubjects' => 0, 'submitted' => true];
                    foreach ($analyticsSubjects as $subj) {
                        $mark = $studentMarks->firstWhere('subject', $subj);
                        if ($mark && $mark->total_marks !== null) {
                            $pct = $mark->percentage();
                            $gp  = $mark->computedGradePoint();
                            $raw = $mark->obtained_marks ?? $mark->total_marks;
                            // Pass% = (pass_marks / full_marks) * 100 — keeps comparisons unit-safe when full ≠ 100
                            $passPct = ($mark->full_marks > 0) ? round((float) $mark->pass_marks / (float) $mark->full_marks * 100, 2) : 33;
                            $row['subjectData'][$subj] = ['pct' => $pct, 'grade' => $mark->computedGrade() ?? $mark->grade, 'gp' => $gp, 'passPct' => $passPct, 'raw' => $raw, 'full' => $mark->full_marks];
                            if ($pct !== null) { $row['totalPct'] += $pct; $row['totalGp'] += ($gp ?? 0); $row['totalRaw'] += $raw; $row['markedSubjects']++; }
                            if (!$mark->submitted_at) $row['submitted'] = false;
                        } else { $row['subjectData'][$subj] = null; $row['submitted'] = false; }
                    }
                    $c = $row['markedSubjects'];
                    $row['avgPct'] = $c > 0 ? round($row['totalPct'] / $c, 2) : null;
                    $row['cgpa']     = $c > 0 ? round($row['totalGp'] / $c, 2) : null;
                    $row['division'] = $row['avgPct'] !== null ? DivisionRule::divisionFor($row['avgPct'])?->name : null;
                    $rows[] = $row;
                }

                // Load class_subjects to check optional + pass_marks
                $classSubjects = \App\Models\ClassSubject::where('academic_year_id', $year->id)
                    ->where('class', $class)
                    ->when($section, fn ($q) => $q->where(function ($q) use ($section) {
                        $q->whereNull('section')->orWhere('section', $section);
                    }))
                    ->with('subject')->get();

                // Pass/Fail split: fail if any non-optional subject's pct < its pass percentage
                $passRows = [];
                $failRows = [];
                foreach ($rows as $r) {
                    $failedSubjects = [];
                    foreach ($analyticsSubjects as $subj) {
                        $sd = $r['subjectData'][$subj] ?? null;
                        $cs = $classSubjects->firstWhere('subject.name', $subj);
                        if ($cs && $cs->is_optional) continue; // skip optional subjects
                        // Use the per-mark pass percentage (full/pass_marks scaled correctly per ExamSubjectMark)
                        $passPct = $sd['passPct'] ?? 33;
                        if ($sd === null || $sd['pct'] === null || $sd['pct'] < $passPct) {
                            $failedSubjects[] = $subj;
                        }
                    }
                    if (empty($failedSubjects) && ($r['avgPct'] ?? 0) > 0) {
                        $passRows[] = $r;
                    } else {
                        $r['failedSubjects'] = $failedSubjects;
                        $failRows[] = $r;
                    }
                }

                $rankedPass = collect($passRows)->sortByDesc(fn ($r) => $r['avgPct'] ?? 0)
                    ->values()->map(fn ($r, $i) => array_merge($r, ['rank' => $i + 1]));

                $rankings = $rankedPass;

                // Continuous rank across ALL students: fail ranks continue after pass
                $passCount = $rankedPass->count();
                $rankedFail = collect($failRows)->sortByDesc(fn ($r) => $r['avgPct'] ?? 0)
                    ->values()->map(fn ($r, $i) => array_merge($r, ['rank' => $i + 1 + $passCount]));

                $passRankings = $rankedPass;
                $failRankings = $rankedFail;

                $allRanked = $rankedPass->concat($rankedFail);
                foreach ($analyticsSubjects as $subj) {
                    $pcts = []; $grades = [];
                    foreach ($allRanked as $r) {
                        $sd = $r['subjectData'][$subj] ?? null;
                        if ($sd && $sd['pct'] !== null) { $pcts[] = $sd['pct']; $grades[] = $sd['grade']; }
                    }
                    $cnt = count($pcts);
                    $subjectStats[$subj] = [
                        'avg' => $cnt > 0 ? round(array_sum($pcts) / $cnt, 2) : null,
                        'count' => $cnt, 'gradeDist' => array_count_values(array_filter($grades)),
                        'highest' => $cnt > 0 ? max($pcts) : null,
                        'lowest'  => $cnt > 0 ? min($pcts) : null,
                    ];
                }
            }
        }

        // Submission summary per subject — includes ALL class_subjects even with zero marks
        $submissionStatus = collect();
        if ($year && $examId && $class && $section) {
            $totalStudents = StudentEnrollment::forActiveYear()->active()
                ->where('class', $class)->where('section', $section)->count();

            // Get marks stats per subject
            $subjectsWithMarks = Mark::where('academic_year_id', $year->id)
                ->where('exam_id', $examId)->where('class', $class)->where('section', $section)
                ->select('subject')
                ->selectRaw('COUNT(*) as total')
                ->selectRaw('SUM(CASE WHEN submitted_at IS NOT NULL THEN 1 ELSE 0 END) as submitted_count')
                ->groupBy('subject')->get()->keyBy('subject');

            // Get expected subjects from class_subjects
            $classSubjectNames = \App\Models\ClassSubject::where('academic_year_id', $year->id)
                ->where('class', $class)
                ->when($section, fn ($q) => $q->where(function ($q) use ($section) {
                    $q->whereNull('section')->orWhere('section', $section);
                }))
                ->with('subject')->get()->pluck('subject.name');

            if ($classSubjectNames->isNotEmpty()) {
                foreach ($classSubjectNames as $s) {
                    $subjStats = $subjectsWithMarks->get($s);
                    $submissionStatus->push((object) [
                        'subject' => $s,
                        'total' => $subjStats ? (int) $subjStats->total : 0,
                        'submitted_count' => $subjStats ? (int) $subjStats->submitted_count : 0,
                        'expected' => $totalStudents,
                    ]);
                }
            } else {
                // Fallback: use whatever marks exist
                $submissionStatus = $subjectsWithMarks->map(fn ($s) => (object) [
                    'subject' => $s->subject,
                    'total' => (int) $s->total,
                    'submitted_count' => (int) $s->submitted_count,
                    'expected' => $totalStudents,
                ])->values();
            }
        }

        // Pending approvals: submitted but not yet approved, grouped by (exam, class, section, subject)
        $pendingReviews = collect();
        if ($year) {
            $pendingMarks = Mark::where('academic_year_id', $year->id)
                ->whereNotNull('submitted_at')
                ->whereNull('approved_at')
                ->with('exam')
                ->select('exam_id', 'class', 'section', 'subject')
                ->selectRaw('COUNT(*) as student_count')
                ->selectRaw('MIN(submitted_at) as first_submitted_at')
                ->groupBy('exam_id', 'class', 'section', 'subject')
                ->orderBy('exam_id')
                ->orderBy('class')
                ->orderBy('section')
                ->orderBy('subject')
                ->get();

            if ($examId) {
                $pendingMarks = $pendingMarks->where('exam_id', (int) $examId);
            }

            $pendingReviews = $pendingMarks;
        }

        return view('admin.marks.index', [
            'year'     => $year,
            'exams'    => $exams,
            'slots'    => $slots,
            'subjectList' => $subjectList,
            'filteredSubjectList' => $filteredSubjectList,
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
            'passRankings' => $passRankings ?? collect(),
            'failRankings' => $failRankings ?? collect(),
            'pendingReviews' => $pendingReviews,
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
            ->filter(fn ($r) => $r->enrollment !== null)
            ->sortBy(fn ($r) => [(int) ($r->enrollment?->roll_number ?: 999999), $r->enrollment?->student?->name ?? '']);

        if ($records->isEmpty()) {
            return back()->with('error', 'No marks found for this combination.');
        }

        $examName = Exam::find($examId)?->name ?? 'exam';

        $csv = "Roll No,Student Name,Class,Section,Subject,Theory,Assignment,Total,Full Marks,Pass Marks,Grade,Status\n";
        foreach ($records as $r) {
            $status = $r->status();
            $csv .= implode(',', [
                $r->enrollment?->roll_number ?? '',
                '"'.str_replace('"', '""', $r->enrollment?->student?->name ?? '').'"',
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

        // --- Completion gate: all subjects must be submitted ---
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

        $allSubjects = $classSubjectNames->isNotEmpty()
            ? $classSubjectNames
            : Mark::where('academic_year_id', $year->id)
                ->where('exam_id', $examId)->where('class', $class)
                ->when($section, fn ($q) => $q->where('section', $section))
                ->select('subject')->distinct()->pluck('subject')->sort()->values();

        $enrolledCount = StudentEnrollment::forActiveYear()->active()
            ->where('class', $class)->where('section', $section)->count();

        $pendingSubjects = [];
        foreach ($allSubjects as $subj) {
            $submittedCount = Mark::where('academic_year_id', $year->id)
                ->where('exam_id', $examId)->where('class', $class)
                ->when($section, fn ($q) => $q->where('section', $section))
                ->where('subject', $subj)
                ->whereNotNull('submitted_at')
                ->count();
            $isComplete = $submittedCount >= $enrolledCount && $enrolledCount > 0;
            if (!$isComplete) {
                $pendingSubjects[] = $subj;
            }
        }

        if (!empty($pendingSubjects)) {
            return back()->with('error', 'Cannot export results: '.implode(', ', $pendingSubjects).' still have unsubmitted marks.');
        }

        // --- Build data matching the filter page results view ---
        $enrollments = StudentEnrollment::forActiveYear()->active()
            ->where('class', $class)->when($section, fn ($q) => $q->where('section', $section))
            ->with('student')->orderBy('roll_number')->get();

        $marks = Mark::where('academic_year_id', $year->id)
            ->where('exam_id', $examId)->where('class', $class)
            ->when($section, fn ($q) => $q->where('section', $section))
            ->whereNotNull('submitted_at')
            ->get()->groupBy('student_enrollment_id');

        $analyticsSubjects = $allSubjects;

        $rows = [];
        foreach ($enrollments as $enrollment) {
            $studentMarks = $marks->get($enrollment->id, collect());
            $row = ['enrollment' => $enrollment, 'subjectData' => [], 'totalPct' => 0, 'totalGp' => 0, 'totalRaw' => 0, 'markedSubjects' => 0, 'submitted' => true];
            foreach ($analyticsSubjects as $subj) {
                $mark = $studentMarks->firstWhere('subject', $subj);
                if ($mark && $mark->total_marks !== null) {
                    $pct = $mark->percentage();
                    $gp  = $mark->computedGradePoint();
                    $raw = $mark->obtained_marks ?? $mark->total_marks;
                    $passPct = ($mark->full_marks > 0) ? round((float) $mark->pass_marks / (float) $mark->full_marks * 100, 2) : 33;
                    $row['subjectData'][$subj] = ['pct' => $pct, 'grade' => $mark->computedGrade() ?? $mark->grade, 'gp' => $gp, 'passPct' => $passPct, 'raw' => $raw, 'full' => $mark->full_marks];
                    if ($pct !== null) { $row['totalPct'] += $pct; $row['totalGp'] += ($gp ?? 0); $row['totalRaw'] += $raw; $row['markedSubjects']++; }
                    if (!$mark->submitted_at) $row['submitted'] = false;
                } else { $row['subjectData'][$subj] = null; $row['submitted'] = false; }
            }
            $c = $row['markedSubjects'];
            $row['avgPct'] = $c > 0 ? round($row['totalPct'] / $c, 2) : null;
            $row['cgpa']     = $c > 0 ? round($row['totalGp'] / $c, 2) : null;
            $row['division'] = $row['avgPct'] !== null ? DivisionRule::divisionFor($row['avgPct'])?->name : null;
            $rows[] = $row;
        }

        // Load class_subjects to check optional + pass_marks
        $classSubjects = \App\Models\ClassSubject::where('academic_year_id', $year->id)
            ->where('class', $class)
            ->when($section, fn ($q) => $q->where(function ($q) use ($section) {
                $q->whereNull('section')->orWhere('section', $section);
            }))
            ->with('subject')->get();

        // Pass/Fail split
        $passRows = [];
        $failRows = [];
        foreach ($rows as $r) {
            $failedSubjects = [];
            foreach ($analyticsSubjects as $subj) {
                $sd = $r['subjectData'][$subj] ?? null;
                $cs = $classSubjects->firstWhere('subject.name', $subj);
                if ($cs && $cs->is_optional) continue;
                $passPct = $sd['passPct'] ?? 33;
                if ($sd === null || $sd['pct'] === null || $sd['pct'] < $passPct) {
                    $failedSubjects[] = $subj;
                }
            }
            if (empty($failedSubjects) && ($r['avgPct'] ?? 0) > 0) {
                $passRows[] = $r;
            } else {
                $r['failedSubjects'] = $failedSubjects;
                $failRows[] = $r;
            }
        }

        $rankedPass = collect($passRows)->sortByDesc(fn ($r) => $r['avgPct'] ?? 0)
            ->values()->map(fn ($r, $i) => array_merge($r, ['rank' => $i + 1]));

        $passCount = $rankedPass->count();
        $rankedFail = collect($failRows)->sortByDesc(fn ($r) => $r['avgPct'] ?? 0)
            ->values()->map(fn ($r, $i) => array_merge($r, ['rank' => $i + 1 + $passCount]));

        // --- Build CSV with proper template (school name, exam, class) ---
        $schoolName = \App\Helpers\Settings::get('site_name', 'JN Nazareth School');
        $sectionLabel = $section ? ' - Section '.$section : '';
        $lines = [];

        $lines[] = $schoolName;
        $lines[] = 'Churachandpur, Manipur';
        $lines[] = '';
        $lines[] = 'EXAMINATION RESULT';
        $lines[] = $exam->name.($exam->code ? ' ('.$exam->code.')' : '');
        $lines[] = 'Class: '.$class.$sectionLabel;
        $lines[] = '';

        if ($rankedPass->isNotEmpty()) {
            $lines[] = 'PASS - Ranked';
            $passHeader = ['Rank', 'Roll No', 'Student Name'];
            foreach ($analyticsSubjects as $subj) {
                $passHeader[] = $subj;
            }
            $passHeader[] = 'Total';
            $passHeader[] = 'Avg %';
            $passHeader[] = 'CGPA';
            $passHeader[] = 'Division';
            $lines[] = implode(',', array_map(fn($h) => '"'.$h.'"', $passHeader));
            $lines[] = str_repeat('-', 80);

            foreach ($rankedPass as $r) {
                $line = [$r['rank'], $r['enrollment']?->roll_number ?? '', '"'.str_replace('"', '""', $r['enrollment']?->student?->name ?? '').'"'];
                foreach ($analyticsSubjects as $subj) {
                    $sd = $r['subjectData'][$subj] ?? null;
                    $line[] = $sd && $sd['raw'] !== null ? $sd['raw'] : '';
                }
                $line[] = $r['totalRaw'];
                $line[] = $r['avgPct'] !== null ? $r['avgPct'].'%' : '';
                $line[] = $r['cgpa'] !== null ? number_format($r['cgpa'], 2) : '';
                $line[] = $r['division'] ?? '';
                $lines[] = implode(',', $line);
            }
            $lines[] = '';
        }

        if ($rankedFail->isNotEmpty()) {
            $lines[] = 'NEEDS IMPROVEMENT';
            $failHeader = ['Rank', 'Roll No', 'Student Name'];
            foreach ($analyticsSubjects as $subj) {
                $failHeader[] = $subj;
            }
            $failHeader[] = 'Total';
            $failHeader[] = 'Avg %';
            $failHeader[] = 'Division';
            $failHeader[] = 'Failed In';
            $lines[] = implode(',', array_map(fn($h) => '"'.$h.'"', $failHeader));
            $lines[] = str_repeat('-', 80);

            foreach ($rankedFail as $r) {
                $line = [$r['rank'], $r['enrollment']?->roll_number ?? '', '"'.str_replace('"', '""', $r['enrollment']?->student?->name ?? '').'"'];
                foreach ($analyticsSubjects as $subj) {
                    $sd = $r['subjectData'][$subj] ?? null;
                    $line[] = $sd && $sd['raw'] !== null ? $sd['raw'] : '';
                }
                $line[] = $r['totalRaw'];
                $line[] = $r['avgPct'] !== null ? $r['avgPct'].'%' : '';
                $line[] = $r['division'] ?? '';
                $line[] = !empty($r['failedSubjects']) ? implode(', ', $r['failedSubjects']) : '';
                $lines[] = implode(',', $line);
            }
            $lines[] = '';
        }

        $lines[] = 'Authorised by '.$schoolName.' Administration';
        $lines[] = 'Generated on: '.now()->format('d M Y');
        $csv = implode("\n", $lines)."\n";

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

        // --- Completion gate ---
        $classSubjectNames = \App\Models\ClassSubject::where('academic_year_id', $year->id)
            ->where('class', $class)
            ->when($section, fn ($q) => $q->where(function ($q) use ($section) {
                $q->whereNull('section')->orWhere('section', $section);
            }))
            ->with('subject')->get()->pluck('subject.name')->sort()->values();

        $allSubjects = $classSubjectNames->isNotEmpty()
            ? $classSubjectNames
            : Mark::where('academic_year_id', $year->id)
                ->where('exam_id', $examId)->where('class', $class)
                ->when($section, fn ($q) => $q->where('section', $section))
                ->select('subject')->distinct()->pluck('subject')->sort()->values();

        $enrolledCount = StudentEnrollment::forActiveYear()->active()
            ->where('class', $class)->where('section', $section)->count();

        $pendingSubjects = [];
        foreach ($allSubjects as $subj) {
            $submittedCount = Mark::where('academic_year_id', $year->id)
                ->where('exam_id', $examId)->where('class', $class)
                ->when($section, fn ($q) => $q->where('section', $section))
                ->where('subject', $subj)->whereNotNull('submitted_at')->count();
            if ($submittedCount < $enrolledCount) {
                $pendingSubjects[] = $subj;
            }
        }

        if (!empty($pendingSubjects)) {
            return back()->with('error', 'Result not declared yet. Pending subjects: '.implode(', ', $pendingSubjects));
        }

        // --- Build pass/fail data matching the web page ---
        $analyticsSubjects = $allSubjects;
        $enrollments = StudentEnrollment::forActiveYear()->active()
            ->where('class', $class)->when($section, fn ($q) => $q->where('section', $section))
            ->with('student')->orderBy('roll_number')->get();

        $marks = Mark::where('academic_year_id', $year->id)
            ->where('exam_id', $examId)->where('class', $class)
            ->when($section, fn ($q) => $q->where('section', $section))
            ->whereNotNull('submitted_at')
            ->get()->groupBy('student_enrollment_id');

        $rows = [];
        foreach ($enrollments as $enrollment) {
            $studentMarks = $marks->get($enrollment->id, collect());
            $row = ['enrollment' => $enrollment, 'subjectData' => [], 'totalPct' => 0, 'totalGp' => 0, 'totalRaw' => 0, 'markedSubjects' => 0, 'submitted' => true];
            foreach ($analyticsSubjects as $subj) {
                $mark = $studentMarks->firstWhere('subject', $subj);
                if ($mark && $mark->total_marks !== null) {
                    $pct = $mark->percentage();
                    $gp  = $mark->computedGradePoint();
                    $raw = $mark->obtained_marks ?? $mark->total_marks;
                    $passPct = ($mark->full_marks > 0) ? round((float) $mark->pass_marks / (float) $mark->full_marks * 100, 2) : 33;
                    $row['subjectData'][$subj] = ['pct' => $pct, 'grade' => $mark->computedGrade() ?? $mark->grade, 'gp' => $gp, 'passPct' => $passPct, 'raw' => $raw, 'full' => $mark->full_marks];
                    if ($pct !== null) { $row['totalPct'] += $pct; $row['totalGp'] += ($gp ?? 0); $row['totalRaw'] += $raw; $row['markedSubjects']++; }
                    if (!$mark->submitted_at) $row['submitted'] = false;
                } else { $row['subjectData'][$subj] = null; $row['submitted'] = false; }
            }
            $c = $row['markedSubjects'];
            $row['avgPct'] = $c > 0 ? round($row['totalPct'] / $c, 2) : null;
            $row['cgpa']     = $c > 0 ? round($row['totalGp'] / $c, 2) : null;
            $row['division'] = $row['avgPct'] !== null ? DivisionRule::divisionFor($row['avgPct'])?->name : null;
            $rows[] = $row;
        }

        $classSubjects = \App\Models\ClassSubject::where('academic_year_id', $year->id)
            ->where('class', $class)
            ->when($section, fn ($q) => $q->where(function ($q) use ($section) {
                $q->whereNull('section')->orWhere('section', $section);
            }))
            ->with('subject')->get();

        $passRows = [];
        $failRows = [];
        foreach ($rows as $r) {
            $failedSubjects = [];
            foreach ($analyticsSubjects as $subj) {
                $sd = $r['subjectData'][$subj] ?? null;
                $cs = $classSubjects->firstWhere('subject.name', $subj);
                if ($cs && $cs->is_optional) continue;
                $passPct = $sd['passPct'] ?? 33;
                if ($sd === null || $sd['pct'] === null || $sd['pct'] < $passPct) {
                    $failedSubjects[] = $subj;
                }
            }
            if (empty($failedSubjects) && ($r['avgPct'] ?? 0) > 0) {
                $passRows[] = $r;
            } else {
                $r['failedSubjects'] = $failedSubjects;
                $failRows[] = $r;
            }
        }

        $rankedPass = collect($passRows)->sortByDesc(fn ($r) => $r['avgPct'] ?? 0)
            ->values()->map(fn ($r, $i) => array_merge($r, ['rank' => $i + 1]));

        $passCount = $rankedPass->count();
        $rankedFail = collect($failRows)->sortByDesc(fn ($r) => $r['avgPct'] ?? 0)
            ->values()->map(fn ($r, $i) => array_merge($r, ['rank' => $i + 1 + $passCount]));

        $failCount = $rankedFail->count();

        if ($rankedPass->isEmpty() && $rankedFail->isEmpty()) {
            return back()->with('error', 'No results found for this class.');
        }

        $pdf = Pdf::loadView('admin.marks.class-result-pdf', [
            'year' => $year,
            'exam' => $exam,
            'class' => $class,
            'section' => $section,
            'analyticsSubjects' => $analyticsSubjects,
            'rankedPass' => $rankedPass,
            'rankedFail' => $rankedFail,
            'passCount' => $passCount,
            'failCount' => $failCount,
        ])->setPaper('a4', 'landscape');

        $sectionLabel = $section ? '_Sec-'.$section : '';
        $filename = 'class-result_'.$exam->name.'_'.$class.$sectionLabel.'.pdf';
        $filename = str_replace(['/', ' '], '_', $filename);

        return $pdf->download($filename);
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

        $enrolledCount = StudentEnrollment::forActiveYear()->active()
            ->where('class', $class)->when($section, fn ($q) => $q->where('section', $section))->count();
        $pendingSubj = [];
        foreach ($subjList as $subj) {
            $submittedCount = Mark::where('academic_year_id', $year->id)->where('exam_id', $examId)
                ->where('class', $class)->when($section, fn ($q) => $q->where('section', $section))
                ->where('subject', $subj)->whereNotNull('submitted_at')->count();
            $isComplete = $submittedCount >= $enrolledCount && $enrolledCount > 0;
            if (!$isComplete) { $pendingSubj[] = $subj; }
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
            $row['division'] = $row['avgPct'] !== null ? DivisionRule::divisionFor($row['avgPct'])?->name : null;
            $rows[] = $row;
        }

        $sectionLabel = $section ? '_Sec-'.$section : '';

        if ($format === 'csv') {
            $header = ['Sl No', 'Roll No', 'Student Name'];
            foreach ($subjects as $subj) { $header[] = $subj.' (T/F)'; }
            $header[] = 'Total (Obt/Full)';
            $header[] = 'Avg %';
            $header[] = 'Division';

            $csv = implode(',', array_map(fn($h) => '"'.$h.'"', $header))."\n";
            foreach ($rows as $i => $r) {
                $line = [$i + 1, $r['roll'] ?? '', '"'.str_replace('"', '""', $r['name']).'"'];
                foreach ($subjects as $subj) {
                    $sd = $r['subjectData'][$subj] ?? null;
                    $line[] = $sd ? ($sd['total'] ?? '').'/'.($sd['full'] ?? '') : '';
                }
                $line[] = $r['totalObtained'].'/'.$r['totalFull'];
                $line[] = $r['avgPct'] !== null ? $r['avgPct'].'%' : '';
                $line[] = $r['division'] ?? '';
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
        $mark->update(['submitted_at' => null, 'approved_at' => null, 'approved_by' => null]);
        return back()->with('success', 'Submission reset. Teacher can now edit marks.');
    }

    /**
     * Approve an individual mark record.
     */
    public function approve(Request $request, Mark $mark)
    {
        $mark->update([
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);
        return back()->with('success', 'Mark approved.');
    }

    /**
     * Approve all marks for a subject (exam, class, section, subject).
     */
    public function approveSubject(Request $request)
    {
        $data = $request->validate([
            'exam_id' => 'required|integer|exists:exams,id',
            'class'   => 'required|string',
            'section' => 'required|string',
            'subject' => 'required|string',
        ]);

        $year = AcademicYear::current();
        if (!$year) {
            return back()->with('error', 'No active academic year.');
        }

        $count = Mark::where('academic_year_id', $year->id)
            ->where('exam_id', $data['exam_id'])
            ->where('class', $data['class'])
            ->where('section', $data['section'])
            ->where('subject', $data['subject'])
            ->whereNotNull('submitted_at')
            ->whereNull('approved_at')
            ->update([
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

        return back()->with('success', "Approved {$count} mark(s) for {$data['subject']}.");
    }

    /**
     * Send a mark back for teacher revision (reset submitted_at so teacher can re-edit).
     */
    public function sendBack(Request $request, Mark $mark)
    {
        $mark->update([
            'submitted_at' => null,
            'approved_at' => null,
            'approved_by' => null,
        ]);
        return back()->with('success', 'Mark sent back for revision. Teacher can now edit.');
    }

    /**
     * Send back all marks for a subject (exam, class, section, subject) for teacher revision.
     */
    public function sendBackSubject(Request $request)
    {
        $data = $request->validate([
            'exam_id' => 'required|integer|exists:exams,id',
            'class'   => 'required|string',
            'section' => 'required|string',
            'subject' => 'required|string',
        ]);

        $year = AcademicYear::current();
        if (!$year) {
            return back()->with('error', 'No active academic year.');
        }

        $count = Mark::where('academic_year_id', $year->id)
            ->where('exam_id', $data['exam_id'])
            ->where('class', $data['class'])
            ->where('section', $data['section'])
            ->where('subject', $data['subject'])
            ->whereNotNull('submitted_at')
            ->update([
                'submitted_at' => null,
                'approved_at' => null,
                'approved_by' => null,
            ]);

        return back()->with('success', "Sent back {$count} mark(s) for {$data['subject']}. Teacher can now re-edit.");
    }

    /**
     * Bulk download results for ALL classes in an exam.
     * Produces a ZIP containing one file per class (PDF or CSV).
     * Refuses if any (class, subject) is still pending school-wide.
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

        // School-wide completion gate: every class_subject must have all enrolled students submitted
        $pendingList = [];
        $classSubjectsByClass = \App\Models\ClassSubject::where('academic_year_id', $year->id)
            ->with('subject')->get()->groupBy('class');
        foreach ($classSubjectsByClass as $cls => $csRows) {
            $sections = StudentEnrollment::forActiveYear()->active()
                ->where('class', $cls)->select('section')->distinct()->pluck('section');
            foreach ($sections as $sec) {
                $enrolled = StudentEnrollment::forActiveYear()->active()
                    ->where('class', $cls)->where('section', $sec)->count();
                if ($enrolled === 0) continue;
                foreach ($csRows as $cs) {
                    $subjName = $cs->subject?->name;
                    if (!$subjName) continue;
                    $submitted = Mark::where('academic_year_id', $year->id)
                        ->where('exam_id', $examId)->where('class', $cls)
                        ->where('section', $sec)->where('subject', $subjName)
                        ->whereNotNull('submitted_at')->count();
                    if ($submitted < $enrolled) {
                        $pendingList[] = "$cls–$sec $subjName";
                    }
                }
            }
        }

        if (!empty($pendingList)) {
            $sample = array_slice($pendingList, 0, 5);
            $more = count($pendingList) > 5 ? ' (+'.(count($pendingList) - 5).' more)' : '';
            return back()->with('error', 'School-wide results not yet complete. Pending: '.implode(', ', $sample).$more);
        }

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
        ])->setPaper('a4', 'landscape');

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
        $examId = $exam->id;
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
            $row['division'] = $row['avgPct'] !== null ? DivisionRule::divisionFor($row['avgPct'])?->name : null;
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
        $lines[] = 'Rank,Roll No,Student Name'.($showSubjectWise ? implode('', array_map(fn($s) => ','.$s.' (%)', $classData['subjects'])) : '').',Avg %,CGPA,Division';
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
            $row[] = $r['division'] ?? '';
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
            'entered_by'       => auth()->id(),
        ]);

        return back()->with('success', 'Mark updated.');
    }

    public function examSummary(Request $request)
    {
        $year = AcademicYear::current();
        $examId = $request->query('exam');
        $exams = $year ? Exam::forActiveYear()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get() : collect();

        $classData = collect();
        if ($year && $examId) {
            $classes = Student::classes();

            $approvedQSubjects = \App\Models\ExamQuestion::where('academic_year_id', $year->id)
                ->where('exam_id', $examId)
                ->where('status', 'approved')
                ->select('class', 'subject')->distinct()->get()
                ->groupBy('class')->map(fn ($g) => $g->pluck('subject')->toArray());

            $submittedMSubjects = Mark::where('academic_year_id', $year->id)
                ->where('exam_id', $examId)
                ->whereNotNull('submitted_at')
                ->select('class', 'subject')->distinct()->get()
                ->groupBy('class')->map(fn ($g) => $g->pluck('subject')->toArray());

            foreach ($classes as $c) {
                $expected = \App\Models\ClassSubject::where('academic_year_id', $year->id)
                    ->where('class', $c)->with('subject')->get()->pluck('subject.name');
                if ($expected->isEmpty()) continue;

                $expectedArr = $expected->values()->toArray();
                $qApproved = $approvedQSubjects->get($c, []);
                $mSubmitted = $submittedMSubjects->get($c, []);

                $classData->push([
                    'class' => $c,
                    'expected_count' => count($expectedArr),
                    'questions_done' => count(array_intersect($qApproved, $expectedArr)),
                    'marks_done' => count(array_intersect($mSubmitted, $expectedArr)),
                    'questions_complete' => count(array_intersect($qApproved, $expectedArr)) === count($expectedArr),
                    'marks_complete' => count(array_intersect($mSubmitted, $expectedArr)) === count($expectedArr),
                    'all_complete' => count(array_intersect($qApproved, $expectedArr)) === count($expectedArr)
                        && count(array_intersect($mSubmitted, $expectedArr)) === count($expectedArr),
                    'section' => 'A',
                ]);
            }
        }

        return view('admin.marks.exam-summary', compact('year', 'exams', 'examId', 'classData'));
    }
}
