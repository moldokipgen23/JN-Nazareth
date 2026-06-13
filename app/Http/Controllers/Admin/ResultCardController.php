<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\ClassSubject;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ResultCardController extends Controller
{
    /**
     * Returns the list of subjects a student failed in (below pass_marks)
     * for non-optional subjects. Empty = student is passing.
     */
    private function failedSubjectsFor($enrollment, $marks, $year): array
    {
        $classSubjects = ClassSubject::where('academic_year_id', $year->id)
            ->where('class', $enrollment->class)
            ->when($enrollment->section, fn ($q) => $q->where(function ($q) use ($enrollment) {
                $q->whereNull('section')->orWhere('section', $enrollment->section);
            }))
            ->with('subject')->get();

        $failed = [];
        foreach ($classSubjects as $cs) {
            if ($cs->is_optional) continue;
            $subjName = $cs->subject?->name;
            if (!$subjName) continue;
            $mark = $marks->firstWhere('subject', $subjName);
            if (!$mark || $mark->full_marks <= 0) {
                $failed[] = $subjName;
                continue;
            }
            // Compare in the same unit: pct vs (pass_marks/full_marks)*100
            $passPct = (float) $mark->pass_marks / (float) $mark->full_marks * 100;
            $pct = $mark->percentage();
            if ($pct === null || $pct < $passPct) {
                $failed[] = $subjName;
            }
        }
        return $failed;
    }

    private function requireAllSubjectsSubmitted(Exam $exam, string $class, ?string $section): void
    {
        $year = AcademicYear::current();
        if (!$year) {
            abort(409, 'No active academic year.');
        }

        $classSubjectNames = \App\Models\ClassSubject::where('academic_year_id', $year->id)
            ->where('class', $class)
            ->when($section, fn ($q) => $q->where(function ($q) use ($section) {
                $q->whereNull('section')->orWhere('section', $section);
            }))
            ->with('subject')
            ->get()
            ->pluck('subject.name');

        $subjects = $classSubjectNames->isNotEmpty()
            ? $classSubjectNames
            : Mark::where('academic_year_id', $year->id)
                ->where('exam_id', $exam->id)->where('class', $class)
                ->when($section, fn ($q) => $q->where('section', $section))
                ->select('subject')->distinct()->pluck('subject');

        // Result card / class result is only unlocked when EVERY enrolled student
        // in the class+section has an approved mark for EVERY expected subject.
        // submittedCount-based gates let partially-approved subjects through and
        // silently dropped non-approved students from rankings.
        $enrolled = \App\Models\StudentEnrollment::forActiveYear()->active()
            ->where('class', $class)
            ->when($section, fn ($q) => $q->where('section', $section))
            ->count();

        $pending = [];
        foreach ($subjects as $subj) {
            $approvedCount = Mark::where('academic_year_id', $year->id)
                ->where('exam_id', $exam->id)->where('class', $class)
                ->when($section, fn ($q) => $q->where('section', $section))
                ->where('subject', $subj)
                ->whereNotNull('approved_at')
                ->count();
            if ($enrolled === 0 || $approvedCount < $enrolled) {
                $pending[] = $subj;
            }
        }

        if (!empty($pending)) {
            abort(409, 'Results not yet available. Subjects not fully approved: '.implode(', ', $pending));
        }
    }

    public function download(Student $student, Exam $exam)
    {
        $year = AcademicYear::current();

        $enrollment = StudentEnrollment::where('student_id', $student->id)
            ->where('academic_year_id', $exam->academic_year_id)
            ->first();

        abort_if(! $enrollment, 404, 'Student not enrolled in this academic year.');

        $this->requireAllSubjectsSubmitted($exam, $enrollment->class, $enrollment->section);

        $marks = Mark::where('student_enrollment_id', $enrollment->id)
            ->where('exam_id', $exam->id)
            ->whereNotNull('approved_at')
            ->get();

        $subjects = $marks->map(function ($m) {
            $pct = $m->percentage();
            return [
                'name'       => $m->subject,
                'full'       => $m->full_marks,
                'pass'       => $m->pass_marks,
                'theory'     => $m->theory_marks,
                'assignment' => $m->assignment_marks,
                'total'      => $m->total_marks ?? $m->obtained_marks,
                'pct'        => $pct,
                'grade'      => $m->computedGrade() ?? $m->grade,
                'gp'         => $m->computedGradePoint(),
                'status'     => $m->status(),
            ];
        });

        $pcts = $subjects->pluck('pct')->filter();
        $gps  = $subjects->pluck('gp')->filter();
        $avgPct = $pcts->isNotEmpty() ? round($pcts->avg(), 2) : null;
        $cgpa   = $gps->isNotEmpty() ? round($gps->avg(), 2) : null;

        // Compute class rank — only passing students included in pool
        $rank = null;
        $isPassing = empty($this->failedSubjectsFor($enrollment, $marks, $year));
        if ($avgPct !== null && $isPassing) {
            $marksByStudent = Mark::whereHas('exam', fn($q) => $q->where('id', $exam->id))
                ->whereHas('enrollment', fn($q) => $q->where('class', $enrollment->class)
                    ->where('academic_year_id', $exam->academic_year_id))
                ->whereNotNull('approved_at')
                ->with('enrollment')
                ->get()
                ->groupBy('student_enrollment_id');

            $passingPcts = $marksByStudent->map(function ($ms) use ($year) {
                $enr = $ms->first()->enrollment;
                if (!$enr) return null;
                $failed = $this->failedSubjectsFor($enr, $ms, $year);
                if (!empty($failed)) return null;
                $p = $ms->map(fn($m) => $m->percentage())->filter();
                return $p->isNotEmpty() ? round($p->avg(), 2) : null;
            })->filter()->sortDesc()->values();

            $rank = $passingPcts->search(fn($p) => abs($p - $avgPct) < 0.01);
            if ($rank !== false) $rank++;
            else $rank = null;
        }

        $totalObtained = $subjects->sum('total');
        $totalMax = $subjects->sum('full');

        $attendance = AttendanceRecord::where('student_enrollment_id', $enrollment->id)
            ->where('academic_year_id', $exam->academic_year_id)
            ->get();
        $attSummary = ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];
        foreach ($attendance as $a) {
            $attSummary[$a->status] = ($attSummary[$a->status] ?? 0) + 1;
        }
        $attTotal = array_sum($attSummary);
        $attPct   = $attTotal > 0 ? round(($attSummary['present'] + $attSummary['late'] + $attSummary['excused']) / $attTotal * 100, 1) : null;

        $pdf = Pdf::loadView('admin.students.result-card-pdf', compact(
            'student', 'exam', 'enrollment', 'subjects', 'avgPct', 'cgpa', 'rank',
            'totalObtained', 'totalMax',
            'attSummary', 'attPct', 'attTotal', 'year'
        ));

        $filename = 'result-'.str_replace(' ', '-', $student->name).'-'.$exam->name.'.pdf';

        return $pdf->download($filename);
    }

    public function fullYear(Student $student)
    {
        $year = app('workingYear');
        $enrollment = StudentEnrollment::where('student_id', $student->id)
            ->where('academic_year_id', $year->id)
            ->first();

        abort_if(! $enrollment, 404, 'Student not enrolled in this academic year.');

        // Check submitted_at for all exams
        $exams = Exam::where('academic_year_id', $year->id)
            ->where('is_active', true)
            ->orderBy('starts_on')
            ->get();

        abort_if($exams->isEmpty(), 404, 'No published exams for this academic year.');

        foreach ($exams as $exam) {
            $this->requireAllSubjectsSubmitted($exam, $enrollment->class, $enrollment->section);
        }

        // Aggregate all marks for the year — group by subject
        $allMarks = Mark::where('student_enrollment_id', $enrollment->id)
            ->whereIn('exam_id', $exams->pluck('id'))
            ->whereNotNull('approved_at')
            ->get();

        $subjects = $allMarks->groupBy('subject')->map(function ($ms) {
            $full = $ms->first()->full_marks;
            $pass = $ms->first()->pass_marks;
            $total = $ms->sum(fn($m) => $m->total_marks ?? $m->obtained_marks);
            $pct = $full > 0 ? round($total / ($ms->count() * $full) * 100, 2) : null;
            $gp = $ms->map(fn($m) => $m->computedGradePoint())->filter()->avg();
            $grade = $ms->first()->computedGrade() ?? $ms->first()->grade;

            return [
                'name'   => $ms->first()->subject,
                'full'   => $full,
                'pass'   => $pass,
                'total'  => $total,
                'pct'    => $pct,
                'grade'  => $grade,
                'gp'     => $gp !== null ? round($gp, 2) : null,
                'status' => ($pct !== null && $pct >= $pass) ? 'pass' : 'fail',
            ];
        })->values();

        $pcts = $subjects->pluck('pct')->filter();
        $gps  = $subjects->pluck('gp')->filter();
        $avgPct = $pcts->isNotEmpty() ? round($pcts->avg(), 2) : null;
        $cgpa   = $gps->isNotEmpty() ? round($gps->avg(), 2) : null;

        $totalObtained = $subjects->sum('total');
        $totalMax = $subjects->sum('full');

        // Rank — only passing students (no subject below pass_marks)
        $rank = null;
        $studentFailed = $subjects->contains(fn ($s) => $s['status'] === 'fail');
        if ($avgPct !== null && !$studentFailed) {
            $allPcts = StudentEnrollment::where('class', $enrollment->class)
                ->where('academic_year_id', $year->id)
                ->where('status', 'active')
                ->get()
                ->map(function ($e) use ($exams) {
                    $ms = Mark::where('student_enrollment_id', $e->id)
                        ->whereIn('exam_id', $exams->pluck('id'))
                        ->whereNotNull('approved_at')
                        ->get()
                        ->groupBy('subject');
                    if ($ms->isEmpty()) return null;
                    $hasFail = false;
                    $pctsPerSubject = $ms->map(function ($m) use (&$hasFail) {
                        $full = $m->first()->full_marks;
                        $pass = $m->first()->pass_marks;
                        $total = $m->sum(fn($x) => $x->total_marks ?? $x->obtained_marks);
                        $pct = $full > 0 ? round($total / ($m->count() * $full) * 100, 2) : 0;
                        if ($pct < $pass) $hasFail = true;
                        return $pct;
                    });
                    if ($hasFail) return null;
                    return $pctsPerSubject->isNotEmpty() ? round($pctsPerSubject->avg(), 2) : null;
                })
                ->filter()
                ->sortDesc()
                ->values();
            $rank = $allPcts->search(fn($p) => abs($p - $avgPct) < 0.01);
            if ($rank !== false) $rank++;
            else $rank = null;
        }

        $attendance = AttendanceRecord::where('student_enrollment_id', $enrollment->id)
            ->where('academic_year_id', $year->id)
            ->get();
        $attSummary = ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];
        foreach ($attendance as $a) {
            $attSummary[$a->status] = ($attSummary[$a->status] ?? 0) + 1;
        }
        $attTotal = array_sum($attSummary);
        $attPct   = $attTotal > 0 ? round(($attSummary['present'] + $attSummary['late'] + $attSummary['excused']) / $attTotal * 100, 1) : null;

        $exam = null; // consolidated so no single exam

        $pdf = Pdf::loadView('admin.students.result-card-pdf', compact(
            'student', 'exam', 'enrollment', 'subjects', 'avgPct', 'cgpa', 'rank',
            'totalObtained', 'totalMax',
            'attSummary', 'attPct', 'attTotal', 'year', 'exams'
        ));

        $filename = 'report-card-'.str_replace(' ', '-', $student->name).'-'.$year->name.'.pdf';

        return $pdf->download($filename);
    }
}
