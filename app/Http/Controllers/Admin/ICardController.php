<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ICardController extends Controller
{
    public function download(Student $student)
    {
        $enrollment = $student->currentEnrollment;

        $pdf = Pdf::loadView('admin.students.icard-pdf', compact('student', 'enrollment'))
            ->setPaper([0, 0, 340, 540], 'portrait');

        $filename = 'icard-'.str_replace(' ', '-', $student->name).'.pdf';

        return $pdf->download($filename);
    }

    public function batchDownload(Request $request)
    {
        $request->validate([
            'class'   => ['required', 'string'],
            'section' => ['nullable', 'string'],
        ]);

        $workingYear = app('workingYear');

        $query = StudentEnrollment::with(['student', 'academicYear'])
            ->where('academic_year_id', $workingYear->id)
            ->where('status', 'active')
            ->where('class', $request->class);

        if ($request->section) {
            $query->where('section', $request->section);
        }

        $enrollments = $query->orderBy('roll_number')->orderBy('student_id')->get();

        if ($enrollments->isEmpty()) {
            return back()->with('error', 'No students found for the selected criteria.');
        }

        $pdf = Pdf::loadView('admin.students.icard-batch-pdf', compact('enrollments'))
            ->setPaper('a4', 'portrait');

        $sectionLabel = $request->section ? '_Sec-'.$request->section : '';
        $filename = 'id-cards_'.$request->class.$sectionLabel.'.pdf';

        return $pdf->download($filename);
    }
}
