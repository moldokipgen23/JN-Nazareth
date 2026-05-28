<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\ExamQuestion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionsController extends Controller
{
    public function index(Request $request)
    {
        $year    = AcademicYear::current();
        $examId  = $request->query('exam');
        $class   = $request->query('class');
        $subject = $request->query('subject');
        $status  = $request->query('status');

        $exams = $year ? Exam::forActiveYear()->orderBy('sort_order')->get() : collect();

        // Base query
        $query = ExamQuestion::with(['exam', 'submitter', 'reviewer'])
            ->where('academic_year_id', $year?->id);

        if ($examId)  $query->where('exam_id', $examId);
        if ($class)   $query->where('class', $class);
        if ($subject) $query->where('subject', $subject);
        if ($status)  $query->where('status', $status);

        $questions = $query->orderBy('class')->orderBy('subject')->orderBy('id')->get();

        // Group by exam_id + class + subject
        $groups = $questions->groupBy(fn ($q) => $q->exam_id.'|'.$q->class.'|'.$q->subject)
            ->map(function ($group) {
                $first = $group->first();
                $statusCounts = ['pending' => 0, 'revision_needed' => 0, 'approved' => 0];
                foreach ($group as $q) {
                    $statusCounts[$q->status] = ($statusCounts[$q->status] ?? 0) + 1;
                }
                return [
                    'exam_id'       => $first->exam_id,
                    'exam_name'     => $first->exam?->name ?? '',
                    'class'         => $first->class,
                    'subject'       => $first->subject,
                    'teacher'       => $first->submitter?->name ?? '',
                    'questions'     => $group,
                    'total'         => $group->count(),
                    'status_counts' => $statusCounts,
                ];
            })->values();

        // Distinct classes & subjects for filter dropdowns
        $availableClasses = ExamQuestion::where('academic_year_id', $year?->id)
            ->select('class')->distinct()->pluck('class')->sort()->values();
        $availableSubjects = ExamQuestion::where('academic_year_id', $year?->id)
            ->select('subject')->distinct()->pluck('subject')->sort()->values();

        $stats = [
            'pending'         => ExamQuestion::where('status', 'pending')->count(),
            'revision_needed' => ExamQuestion::where('status', 'revision_needed')->count(),
            'approved'        => ExamQuestion::where('status', 'approved')->count(),
        ];

        return view('admin.questions.index', compact('groups', 'exams', 'year', 'examId', 'class', 'subject', 'status', 'stats', 'availableClasses', 'availableSubjects'));
    }

    public function export(Request $request)
    {
        $year    = AcademicYear::current();
        $examId  = $request->query('exam');
        $class   = $request->query('class');
        $subject = $request->query('subject');
        $status  = $request->query('status');

        $questions = ExamQuestion::with(['exam', 'submitter'])
            ->when($year, fn ($q) => $q->where('academic_year_id', $year->id))
            ->when($examId, fn ($q) => $q->where('exam_id', $examId))
            ->when($class, fn ($q) => $q->where('class', $class))
            ->when($subject, fn ($q) => $q->where('subject', $subject))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('class')->orderBy('subject')->orderBy('id')
            ->get();

        $rows = [['Sl No', 'Exam', 'Class', 'Subject', 'Marks', 'Submitted By', 'Date', 'Status', 'Question Text', 'File', 'Notes', 'Review Notes']];
        foreach ($questions as $i => $q) {
            $rows[] = [
                $i + 1, $q->exam?->name ?? '', $q->class ?? '', $q->subject ?? '',
                $q->marks ?? '', $q->submitter?->name ?? '', $q->created_at?->format('d-m-Y') ?? '',
                ucfirst($q->status ?? 'pending'), $q->question_text ?? '',
                $q->file_name ?? ($q->file_path ? basename($q->file_path) : ''),
                $q->notes ?? '', $q->review_notes ?? '',
            ];
        }

        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(function ($v) {
                $v = (string) $v;
                return (str_contains($v, ',') || str_contains($v, '"') || str_contains($v, "\n"))
                    ? '"' . str_replace('"', '""', $v) . '"'
                    : $v;
            }, $row)) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="exam-questions-export.csv"',
        ]);
    }

    public function approve(ExamQuestion $question)
    {
        $question->update([
            'status' => 'approved', 'reviewed_by' => auth()->id(), 'reviewed_at' => now(),
        ]);
        return back()->with('success', 'Question approved.');
    }

    public function approveGroup(Request $request)
    {
        $ids = $request->input('question_ids', []);
        if (empty($ids)) return back()->with('error', 'No questions selected.');

        ExamQuestion::whereIn('id', $ids)->where('status', 'pending')->update([
            'status' => 'approved', 'reviewed_by' => auth()->id(), 'reviewed_at' => now(),
        ]);
        return back()->with('success', count($ids).' question(s) approved.');
    }

    public function requestRevision(Request $request, ExamQuestion $question)
    {
        $data = $request->validate(['review_notes' => 'required|string|max:2000']);
        $question->update([
            'status' => 'revision_needed', 'review_notes' => $data['review_notes'],
            'reviewed_by' => auth()->id(), 'reviewed_at' => now(),
        ]);
        return back()->with('success', 'Question sent back for revision.');
    }

    public function requestRevisionGroup(Request $request)
    {
        $ids = $request->input('question_ids', []);
        $notes = $request->input('review_notes', '');
        if (empty($ids)) return back()->with('error', 'No questions selected.');
        if (!$notes) return back()->with('error', 'Provide revision notes.');

        ExamQuestion::whereIn('id', $ids)->where('status', 'pending')->update([
            'status' => 'revision_needed', 'review_notes' => $notes,
            'reviewed_by' => auth()->id(), 'reviewed_at' => now(),
        ]);
        return back()->with('success', count($ids).' question(s) sent back for revision.');
    }

    public function destroy(ExamQuestion $question)
    {
        if ($question->file_path) Storage::disk('public')->delete($question->file_path);
        $question->delete();
        return back()->with('success', 'Question deleted.');
    }

    public function exportQuestionPaper(Request $request)
    {
        $year    = AcademicYear::current();
        $examId  = $request->query('exam');
        $class   = $request->query('class');
        $subject = $request->query('subject');

        if (!$year || !$examId || !$class || !$subject) {
            return back()->with('error', 'Select exam, class, and subject.');
        }

        $exam = Exam::find($examId);
        if (!$exam) return back()->with('error', 'Exam not found.');

        $questions = ExamQuestion::where('academic_year_id', $year->id)
            ->where('exam_id', $examId)->where('class', $class)
            ->where('subject', $subject)->where('status', 'approved')
            ->orderBy('id')->get();

        if ($questions->isEmpty()) {
            return back()->with('error', 'No approved questions for this combination.');
        }

        $totalMarks = $questions->sum(fn ($q) => (float) ($q->marks ?? 0));

        $pdf = Pdf::loadView('admin.questions.question-paper-pdf', [
            'year' => $year, 'exam' => $exam, 'class' => $class,
            'subject' => $subject, 'questions' => $questions, 'totalMarks' => $totalMarks,
        ])->setPaper('a4', 'portrait');

        $filename = 'question-paper_'.$exam->name.'_'.$class.'_'.$subject.'.pdf';
        $filename = str_replace(['/', ' '], '_', $filename);
        return $pdf->download($filename);
    }
}
