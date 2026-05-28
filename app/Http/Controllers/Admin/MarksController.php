<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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
        $stats   = ['total' => 0, 'pass' => 0, 'fail' => 0, 'ungraded' => 0];

        // Rankings data
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
            }
        }

        if ($year && $class && $examId) {
            $enrollments = StudentEnrollment::forActiveYear()->active()
                ->where('class', $class)->when($section, fn ($q) => $q->where('section', $section))
                ->with('student')->orderBy('roll_number')->get();

            $marks = Mark::where('academic_year_id', $year->id)
                ->where('exam_id', $examId)->where('class', $class)
                ->when($section, fn ($q) => $q->where('section', $section))
                ->get()->groupBy('student_enrollment_id');

            $analyticsSubjects = Mark::where('academic_year_id', $year->id)
                ->where('exam_id', $examId)->where('class', $class)
                ->when($section, fn ($q) => $q->where('section', $section))
                ->select('subject')->distinct()->pluck('subject')->sort()->values();

            $rows = [];
            foreach ($enrollments as $enrollment) {
                $studentMarks = $marks->get($enrollment->id, collect());
                $row = ['enrollment' => $enrollment, 'subjectData' => [], 'totalPct' => 0, 'totalGp' => 0, 'markedSubjects' => 0];
                foreach ($analyticsSubjects as $subj) {
                    $mark = $studentMarks->firstWhere('subject', $subj);
                    if ($mark && $mark->total_marks !== null) {
                        $pct = $mark->percentage();
                        $gp  = $mark->computedGradePoint();
                        $row['subjectData'][$subj] = ['pct' => $pct, 'grade' => $mark->computedGrade() ?? $mark->grade, 'gp' => $gp];
                        if ($pct !== null) { $row['totalPct'] += $pct; $row['totalGp'] += ($gp ?? 0); $row['markedSubjects']++; }
                    } else { $row['subjectData'][$subj] = null; }
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
}
