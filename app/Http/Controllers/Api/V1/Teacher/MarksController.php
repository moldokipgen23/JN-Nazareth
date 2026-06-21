<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Exam;
use App\Models\ExamSubjectMark;
use App\Models\GradeScale;
use App\Models\Mark;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\SubjectTeacherAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarksController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $year = AcademicYear::current();

        $slots = collect();
        $exams = collect();

        if ($year) {
            if ($user->isAdmin()) {
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

        $classSubjectPairs = ClassSubject::where('academic_year_id', $year->id ?? 0)
            ->get()
            ->map(fn ($cs) => $cs->class . '|' . ($cs->subject?->name ?? ''));

        $slots = $slots->filter(fn ($s) => $classSubjectPairs->contains($s->class . '|' . $s->subject));

        $order = array_flip(Student::classes());
        $slots = $slots->sortBy(fn ($s) => [$order[$s->class] ?? 999, $s->section, $s->subject])->values();

        $slotStatuses = [];
        foreach ($slots as $slot) {
            foreach ($exams as $exam) {
                $s = Mark::subjectStatus($exam->id, $slot->class, $slot->section, $slot->subject, $year->id ?? 0);
                if ($s['state'] === 'not_started') continue;
                $slotStatuses[$slot->class][$slot->section][$slot->subject][$exam->id] = $s;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'year'         => $year ? ['id' => $year->id, 'name' => $year->name] : null,
                'slots'        => $slots,
                'exams'        => $exams->map(fn ($e) => [
                    'id'   => $e->id,
                    'name' => $e->name,
                    'type' => $e->type,
                ]),
                'slotStatuses' => $slotStatuses,
            ],
        ]);
    }

    public function sheet(Request $request): JsonResponse
    {
        $year = $this->requireActiveYear();

        $examId  = $request->query('exam');
        $class   = $request->query('class');
        $section = $request->query('section');
        $subject = $request->query('subject');

        if (! $examId || ! $class || ! $section || ! $subject) {
            return response()->json(['success' => false, 'message' => 'exam, class, section, subject are required.'], 422);
        }

        $exam = Exam::findOrFail($examId);
        $this->authorizeSubject($request, $class, $section, $subject);
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

        [$defaultFull, $defaultPass] = ExamSubjectMark::resolveMarks($exam->id, $class, $subject, $year->id);
        $marksConfigured = ExamSubjectMark::where('exam_id', $exam->id)
            ->where('class', $class)->where('subject', $subject)->exists();

        // Lock detection
        $existingForSlot = Mark::where('exam_id', $exam->id)
            ->where('subject', $subject)->where('class', $class)->where('section', $section)
            ->get(['submitted_at', 'approved_at', 'rejected_at']);

        $anySubmitted = $existingForSlot->contains(fn ($m) => $m->submitted_at !== null);
        $anyApproved  = $existingForSlot->contains(fn ($m) => $m->approved_at !== null);
        $anyRejected  = $existingForSlot->contains(fn ($m) => $m->rejected_at && ! $m->submitted_at);
        $isLocked     = ($anySubmitted || $anyApproved) && ! $anyRejected;

        $rejectionNote = null;
        if ($anyRejected) {
            $rejected = $existingForSlot->first(fn ($m) => $m->rejected_at && ! $m->submitted_at);
            $rejectionNote = $rejected?->rejection_note;
        }

        $enrollmentData = $enrollments->map(function ($e) use ($existing) {
            $mark = $existing->get($e->id);
            return [
                'id'            => $e->id,
                'roll_number'   => $e->roll_number,
                'student_name'  => $e->student?->name ?? '',
                'father_name'   => $e->student?->father_name ?? '',
                'parent_phone'  => $e->student?->parent_phone ?? '',
                'theory'        => $mark?->theory_marks,
                'assignment'    => $mark?->assignment_marks,
                'total'         => $mark?->total_marks,
                'grade'         => $mark?->grade,
                'remarks'       => $mark?->remarks,
                'submitted_at'  => $mark?->submitted_at?->toIso8601String(),
                'approved_at'   => $mark?->approved_at?->toIso8601String(),
                'rejected_at'   => $mark?->rejected_at?->toIso8601String(),
                'rejection_note'=> $mark?->rejection_note,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'exam'            => ['id' => $exam->id, 'name' => $exam->name],
                'class'           => $class,
                'section'         => $section,
                'subject'         => $subject,
                'enrollments'     => $enrollmentData,
                'defaultFull'     => $defaultFull,
                'defaultPass'     => $defaultPass,
                'marksConfigured' => $marksConfigured,
                'isLocked'        => $isLocked,
                'rejectionNote'   => $rejectionNote,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $year = $this->requireActiveYear();

        $data = $request->validate([
            'exam_id'                => 'required|exists:exams,id',
            'class'                  => 'required|string',
            'section'                => 'required|string',
            'subject'                => 'required|string',
            'marks'                  => 'required|array',
            'marks.*.enrollment_id'  => 'required|integer',
            'marks.*.theory'         => 'nullable|numeric|min:0',
            'marks.*.assignment'     => 'nullable|numeric|min:0',
            'marks.*.remarks'        => 'nullable|string|max:500',
            'action'                 => 'nullable|in:draft,submit',
        ]);

        $exam = Exam::find($data['exam_id']);
        $this->authorizeSubject($request, $data['class'], $data['section'], $data['subject']);
        $this->ensureExamInYear($exam, $year);
        $this->ensureSubmissionWindow($exam);

        $user = $request->user();
        if (! $user->isAdmin()) {
            $existingForSlot = Mark::where('exam_id', $exam->id)
                ->where('subject', $data['subject'])->where('class', $data['class'])->where('section', $data['section'])
                ->get(['submitted_at', 'approved_at', 'rejected_at']);

            $anySubmitted = $existingForSlot->contains(fn ($m) => $m->submitted_at !== null);
            $anyApproved  = $existingForSlot->contains(fn ($m) => $m->approved_at !== null);
            $anyRejected  = $existingForSlot->contains(fn ($m) => $m->rejected_at && ! $m->submitted_at);

            if (($anySubmitted || $anyApproved) && ! $anyRejected) {
                return response()->json(['success' => false, 'message' => 'Marks already submitted. Contact admin to revise.'], 409);
            }
        }

        [$fullMarks, $passMarks] = ExamSubjectMark::resolveMarks($exam->id, $data['class'], $data['subject'], $year->id);

        $enrollmentIds = StudentEnrollment::forActiveYear()->active()
            ->where('class', $data['class'])->where('section', $data['section'])
            ->pluck('id')->all();

        $isSubmit = ($data['action'] ?? 'draft') === 'submit';

        $rowEntered = function (?array $row): bool {
            if (!$row) return false;
            foreach (['theory', 'assignment'] as $k) {
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
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot submit — missing marks for: ' . implode(', ', $missing),
                ], 422);
            }
        }

        $saved = 0;
        foreach ($data['marks'] as $row) {
            $enrollmentId = (int) $row['enrollment_id'];
            if (! in_array($enrollmentId, $enrollmentIds, true)) continue;

            $theory     = $row['theory'] ?? null;
            $assignment = $row['assignment'] ?? null;

            $theoryHas     = $theory !== null && $theory !== '';
            $assignmentHas = $assignment !== null && $assignment !== '';

            $total = null;
            if ($theoryHas || $assignmentHas) {
                $total = ((float) ($theoryHas ? $theory : 0)) + ((float) ($assignmentHas ? $assignment : 0));
            }

            if ($total !== null && $total !== '' && (float) $total > (float) $fullMarks) {
                return response()->json([
                    'success' => false,
                    'message' => "Total cannot exceed full marks ({$fullMarks}).",
                ], 422);
            }

            $grade = null;
            if ($total !== null && $total !== '') {
                $pct = ((float) $total / (float) $fullMarks) * 100;
                $gs = GradeScale::gradeFor($pct);
                $grade = $gs?->name;
            }

            $markData = [
                'academic_year_id' => $year->id,
                'class'            => $data['class'],
                'section'          => $data['section'],
                'full_marks'       => $fullMarks,
                'pass_marks'       => $passMarks,
                'theory_marks'     => ($theory === '' || $theory === null) ? null : $theory,
                'assignment_marks' => ($assignment === '' || $assignment === null) ? null : $assignment,
                'total_marks'      => ($total === '' || $total === null) ? null : $total,
                'obtained_marks'   => ($total === '' || $total === null) ? null : $total,
                'grade'            => $grade,
                'remarks'          => $row['remarks'] ?? null,
                'submitted_at'     => $isSubmit ? now() : null,
            ];
            if ($isSubmit) {
                $markData['entered_by']     = $request->user()->id;
                $markData['rejection_note'] = null;
                $markData['rejected_at']    = null;
                $markData['rejected_by']    = null;
            }

            Mark::updateOrCreate(
                ['exam_id' => $exam->id, 'student_enrollment_id' => $enrollmentId, 'subject' => $data['subject']],
                $markData
            );
            $saved++;
        }

        $msg = $isSubmit
            ? "Submitted marks for {$saved} student" . ($saved === 1 ? '' : 's') . '.'
            : "Saved draft for {$saved} student" . ($saved === 1 ? '' : 's') . '.';

        return response()->json([
            'success' => true,
            'message' => $msg,
            'data'    => ['saved' => $saved, 'action' => $data['action'] ?? 'draft'],
        ]);
    }

    protected function requireActiveYear(): AcademicYear
    {
        $year = AcademicYear::current();
        if (! $year) abort(409, 'No active academic year.');
        return $year;
    }

    protected function authorizeSubject(Request $request, string $class, string $section, string $subject): void
    {
        $user = $request->user();
        if ($user->isAdmin()) return;
        if (! $user->teachesSubject($class, $section, $subject)) {
            abort(403, 'You are not the subject teacher for this slot.');
        }
    }

    protected function ensureExamInYear(Exam $exam, AcademicYear $year): void
    {
        if ($exam->academic_year_id !== $year->id) {
            abort(404, 'Exam not in active year.');
        }
    }

    protected function ensureSubmissionWindow(Exam $exam): void
    {
        $now = now();
        if ($exam->submission_starts_on && $now->lt($exam->submission_starts_on)) {
            abort(403, 'Submission not yet started.');
        }
        if ($exam->submission_ends_on && $now->gt($exam->submission_ends_on)) {
            abort(403, 'Submission window closed.');
        }
    }
}
