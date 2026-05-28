<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\ExamQuestion;
use Illuminate\Http\Request;

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

        $questions = ExamQuestion::with(['exam','submitter'])
            ->when($year,    fn($q) => $q->where('academic_year_id', $year->id))
            ->when($examId,  fn($q) => $q->where('exam_id', $examId))
            ->when($class,   fn($q) => $q->where('class', $class))
            ->when($subject, fn($q) => $q->where('subject', $subject))
            ->when($status,  fn($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->get();

        return view('admin.questions.index', compact('questions','exams','year','examId','class','subject','status'));
    }

    public function export(Request $request)
    {
        $year    = AcademicYear::current();
        $examId  = $request->query('exam');
        $class   = $request->query('class');
        $subject = $request->query('subject');
        $status  = $request->query('status');

        $questions = ExamQuestion::with(['exam','submitter'])
            ->when($year,    fn($q) => $q->where('academic_year_id', $year->id))
            ->when($examId,  fn($q) => $q->where('exam_id', $examId))
            ->when($class,   fn($q) => $q->where('class', $class))
            ->when($subject, fn($q) => $q->where('subject', $subject))
            ->when($status,  fn($q) => $q->where('status', $status))
            ->orderBy('class')->orderBy('subject')->orderByDesc('created_at')
            ->get();

        $rows = [
            ['Sl No', 'Exam', 'Class', 'Section', 'Subject', 'Submitted By', 'Date', 'Status', 'Question Text', 'File', 'Notes'],
        ];

        foreach ($questions as $i => $q) {
            $rows[] = [
                $i + 1,
                $q->exam?->name ?? '',
                $q->class ?? '',
                $q->section ?? '',
                $q->subject ?? '',
                $q->submitter?->name ?? '',
                $q->created_at?->format('d-m-Y') ?? '',
                ucfirst($q->status ?? 'pending'),
                $q->question_text ?? '',
                $q->file_name ?? ($q->file_path ? basename($q->file_path) : ''),
                $q->notes ?? '',
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
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="exam-questions-export.csv"',
        ]);
    }

    public function markReviewed(ExamQuestion $question)
    {
        $question->update(['status' => 'reviewed']);
        return back()->with('success', 'Marked as reviewed.');
    }

    public function destroy(ExamQuestion $question)
    {
        if ($question->file_path) \Storage::disk('public')->delete($question->file_path);
        $question->delete();
        return back()->with('success', 'Question deleted.');
    }
}
