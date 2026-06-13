<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\ExamSubjectMark;
use App\Models\Mark;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\SubjectTeacherAssignment;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MarksController extends Controller
{
    /** List subject/class/section slots the teacher can enter marks for × active exams. */
    public function index()
    {
        $user = auth()->user();
        $year = AcademicYear::current();

        $slots = collect();
        $exams = collect();

        if ($year) {
            if ($user->isAdmin()) {
                // Admin: every distinct subject/class/section that has an assignment.
                $slots = SubjectTeacherAssignment::where('academic_year_id', $year->id)
                    ->select('class', 'section', 'subject')
                    ->groupBy('class', 'section', 'subject')
                    ->get();
            } else {
                $slots = SubjectTeacherAssignment::where('teacher_id', ($user->teacher?->id ?? $user->id))
                    ->where('academic_year_id', $year->id)
                    ->get(['class', 'section', 'subject']);
            }

            $exams = Exam::forActiveYear()->active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }

        // Filter by class_subjects: only show slots where class+subject exists in class_subjects
        $classSubjectPairs = \App\Models\ClassSubject::where('academic_year_id', $year->id)
            ->get()
            ->map(fn ($cs) => $cs->class . '|' . ($cs->subject?->name ?? ''));

        $slots = $slots->filter(fn ($s) => $classSubjectPairs->contains($s->class . '|' . $s->subject));

        $order = array_flip(Student::classes());
        $slots = $slots->sortBy(fn ($s) => [$order[$s->class] ?? 999, $s->section, $s->subject])->values();

        $slotStatuses = [];
        $teacherId = auth()->id();
        foreach ($slots as $slot) {
            foreach ($exams as $exam) {
                $marks = Mark::where('academic_year_id', $year->id)
                    ->where('exam_id', $exam->id)
                    ->where('class', $slot->class)
                    ->where('section', $slot->section)
                    ->where('subject', $slot->subject)
                    ->get();
                if ($marks->isEmpty()) continue;
                $anySubmitted = $marks->contains(fn ($m) => $m->submitted_at);
                $anyRejected  = $marks->contains(fn ($m) => $m->rejected_at && !$m->submitted_at);
                $allApproved  = $anySubmitted && $marks->every(fn ($m) => $m->approved_at);
                // "Revised" means admin edited a row that is not yet approved.
                // Once approved, the source of edits no longer matters — show approved.
                $hasRevised = !$allApproved && $marks->contains(
                    fn ($m) => $m->entered_by && $m->entered_by !== $teacherId && !$m->approved_at
                );

                $status = $anyRejected   ? 'rejected'
                        : ($allApproved  ? 'approved'
                        : ($anySubmitted ? 'pending'
                        : ($hasRevised   ? 'revised'
                        : 'draft')));
                $slotStatuses[$slot->class][$slot->section][$slot->subject][$exam->id] = $status;
            }
        }

        return view('teacher.marks.index', [
            'year'  => $year,
            'slots' => $slots,
            'exams' => $exams,
            'slotStatuses' => $slotStatuses,
        ]);
    }

    /** Mark-entry sheet for one exam × subject × class × section. */
    public function sheet(Request $request, Exam $exam, string $class, string $section, string $subject)
    {
        $year = $this->requireActiveYear();
        $this->authorizeSubject($class, $section, $subject);
        $this->ensureExamInYear($exam, $year);
        $this->ensureSubmissionWindow($exam);

        $enrollments = StudentEnrollment::forActiveYear()->active()
            ->where('class', $class)->where('section', $section)
            ->with('student')
            ->get()
            ->sortBy(fn ($e) => [(int) $e->roll_number ?: 999999, $e->student?->name ?? ''])
            ->values();

        $existing = Mark::where('exam_id', $exam->id)
            ->where('subject', $subject)
            ->whereIn('student_enrollment_id', $enrollments->pluck('id'))
            ->get()
            ->keyBy('student_enrollment_id');

        // Marks config is admin-controlled per (exam, class, subject) — never teacher-editable.
        [$defaultFull, $defaultPass] = ExamSubjectMark::resolveMarks($exam->id, $class, $subject, $year->id);
        $marksConfigured = ExamSubjectMark::where('exam_id', $exam->id)
            ->where('class', $class)->where('subject', $subject)->exists();

        return view('teacher.marks.sheet', [
            'year'        => $year,
            'exam'        => $exam,
            'class'       => $class,
            'section'     => $section,
            'subject'     => $subject,
            'enrollments' => $enrollments,
            'existing'    => $existing,
            'defaultFull' => $defaultFull,
            'defaultPass' => $defaultPass,
            'marksConfigured' => $marksConfigured,
        ]);
    }

    /** Bulk-save marks for one exam × subject × class × section. */
    public function store(Request $request, Exam $exam, string $class, string $section, string $subject)
    {
        $year = $this->requireActiveYear();
        $this->authorizeSubject($class, $section, $subject);
        $this->ensureExamInYear($exam, $year);
        $this->ensureSubmissionWindow($exam);

        // Block editing if already submitted/approved AND not currently in a
        // sent-back state. Admin can always edit.
        if (!auth()->user()->isAdmin()) {
            $existingForSlot = Mark::where('exam_id', $exam->id)
                ->where('subject', $subject)->where('class', $class)->where('section', $section)
                ->get(['submitted_at', 'approved_at', 'rejected_at']);

            $anySubmitted = $existingForSlot->contains(fn ($m) => $m->submitted_at !== null);
            $anyApproved  = $existingForSlot->contains(fn ($m) => $m->approved_at !== null);
            $anyRejected  = $existingForSlot->contains(fn ($m) => $m->rejected_at && !$m->submitted_at);

            if (($anySubmitted || $anyApproved) && !$anyRejected) {
                return back()->with('error', 'Marks have already been submitted for this subject. Contact admin if you need to revise.');
            }
        }

        $data = $request->validate([
            'marks'                  => 'required|array',
            'marks.*.theory'         => 'nullable|numeric|min:0',
            'marks.*.assignment'     => 'nullable|numeric|min:0',
            'marks.*.total'          => 'nullable|numeric|min:0',
            'marks.*.grade'          => 'nullable|string|max:5',
            'marks.*.remarks'        => 'nullable|string|max:500',
            'action'                 => 'nullable|in:draft,submit',
        ]);

        // Marks config comes from admin-set ExamSubjectMark — teacher cannot override.
        [$fullMarks, $passMarks] = ExamSubjectMark::resolveMarks($exam->id, $class, $subject, $year->id);
        $data['full_marks'] = $fullMarks;
        $data['pass_marks'] = $passMarks;

        $enrollmentIds = StudentEnrollment::forActiveYear()->active()
            ->where('class', $class)->where('section', $section)
            ->pluck('id')->all();

        $isSubmit = $request->input('action') === 'submit';

        // A row is considered "entered" when any of theory / assignment / total
        // has a value — including the literal 0 (a student scoring 0 or absent
        // must be recordable). Empty across all three means truly missing.
        $rowEntered = function (?array $row): bool {
            if (!$row) return false;
            foreach (['theory', 'assignment', 'total'] as $k) {
                $v = $row[$k] ?? null;
                if ($v !== null && $v !== '') return true;
            }
            return false;
        };

        if ($isSubmit) {
            $missing = [];
            foreach ($enrollmentIds as $eid) {
                if (!$rowEntered($data['marks'][$eid] ?? null)) {
                    $student = StudentEnrollment::find($eid)?->student?->name ?? "ID #{$eid}";
                    $missing[] = $student;
                }
            }
            if (!empty($missing)) {
                return back()->with('error', 'Cannot submit — these students have no marks (use the Absent toggle to mark as 0): '.implode(', ', $missing))
                    ->withInput();
            }
        }

        $saved = 0;
        foreach ($data['marks'] as $enrollmentId => $row) {
            $enrollmentId = (int) $enrollmentId;
            if (! in_array($enrollmentId, $enrollmentIds, true)) {
                continue;
            }

            $theory     = $row['theory'] ?? null;
            $assignment = $row['assignment'] ?? null;
            $total      = $row['total'] ?? null;

            $theoryHas     = $theory !== null && $theory !== '';
            $assignmentHas = $assignment !== null && $assignment !== '';
            $totalHas      = $total !== null && $total !== '';

            // If any component is entered, authoritatively recompute total = theory + assignment
            // (treat missing components as 0). This handles single-component exams, 0-scores,
            // and ensures the saved total always matches the inputs.
            if ($theoryHas || $assignmentHas) {
                $total = ((float) ($theoryHas ? $theory : 0)) + ((float) ($assignmentHas ? $assignment : 0));
            }
            // If only total is provided (assignment column not used), keep what was sent.
            // If nothing is provided, total stays null and the row stays a draft skip.

            if ($total !== null && $total !== '' && (float) $total > (float) $data['full_marks']) {
                return back()->withErrors([
                    'marks' => "Total marks cannot exceed full marks ({$data['full_marks']}).",
                ])->withInput();
            }

            $grade = $row['grade'] ?? null;
            if ($total && ! $grade) {
                $pct = ((float) $total / (float) $data['full_marks']) * 100;
                $gs = \App\Models\GradeScale::gradeFor($pct);
                $grade = $gs?->name;
            }

            $markData = [
                'academic_year_id' => $year->id,
                'class'            => $class,
                'section'          => $section,
                'full_marks'       => $data['full_marks'],
                'pass_marks'       => $data['pass_marks'],
                'theory_marks'     => ($theory === '' ? null : $theory),
                'assignment_marks' => ($assignment === '' ? null : $assignment),
                'total_marks'      => ($total === '' ? null : $total),
                'obtained_marks'   => ($total === '' ? null : $total),
                'grade'            => $grade,
                'remarks'          => $row['remarks'] ?? null,
                'submitted_at'     => $isSubmit ? now() : null,
            ];
            if ($isSubmit) {
                $markData['entered_by']     = auth()->id();
                $markData['rejection_note'] = null;
                $markData['rejected_at']    = null;
                $markData['rejected_by']    = null;
            }
            Mark::updateOrCreate(
                ['exam_id' => $exam->id, 'student_enrollment_id' => $enrollmentId, 'subject' => $subject],
                $markData
            );
            $saved++;
        }

        $msg = $isSubmit
            ? "Submitted marks for {$saved} student".($saved === 1 ? '' : 's').'.'
            : "Saved draft for {$saved} student".($saved === 1 ? '' : 's').'.';

        return redirect()
            ->route('teacher.marks.sheet', [
                'exam' => $exam->id, 'class' => $class, 'section' => $section, 'subject' => $subject,
            ])
            ->with('success', $msg);
    }

    // ────────────────────────────────────────────────────────────────────

    protected function requireActiveYear(): AcademicYear
    {
        $year = AcademicYear::current();
        if (! $year) {
            throw new HttpException(409, 'No active academic year is set.');
        }
        return $year;
    }

    protected function authorizeSubject(string $class, string $section, string $subject): void
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return;
        }
        if (! $user->teachesSubject($class, $section, $subject)) {
            abort(403, 'You are not the subject teacher for this slot.');
        }
    }

    protected function ensureExamInYear(Exam $exam, AcademicYear $year): void
    {
        if ($exam->academic_year_id !== $year->id) {
            abort(404, 'This exam is not in the active academic year.');
        }
    }

    protected function ensureSubmissionWindow(Exam $exam): void
    {
        $now = now();
        if ($exam->submission_starts_on && $now->lt($exam->submission_starts_on)) {
            abort(403, 'Marks submission has not yet started.');
        }
        if ($exam->submission_ends_on && $now->gt($exam->submission_ends_on)) {
            abort(403, 'Marks submission window has closed.');
        }
    }
}
