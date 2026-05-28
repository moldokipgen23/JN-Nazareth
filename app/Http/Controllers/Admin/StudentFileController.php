<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentFileController extends Controller
{
    public function store(Request $request, Student $student)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,jpg,jpeg,png',
            ],
        ]);

        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $path = $uploadedFile->storeAs(
            "students/{$student->id}",
            $originalName,
            'local'
        );

        $studentFile = $student->files()->create([
            'filename'    => $originalName,
            'path'        => $path,
            'size'        => $uploadedFile->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        ActivityLogger::log('student_file_uploaded', $studentFile, "Uploaded '{$originalName}' for student: {$student->name}");

        return redirect()->route('admin.students.show', $student)
                         ->with('success', 'File uploaded successfully.');
    }

    public function destroy(StudentFile $studentFile)
    {
        $student = $studentFile->student;

        Storage::disk('local')->delete($studentFile->path);

        $name = $studentFile->filename;
        $studentFile->delete();

        ActivityLogger::log('student_file_deleted', null, "Deleted file '{$name}' from student: {$student->name}");

        return redirect()->route('admin.students.show', $student)
                         ->with('success', 'File deleted successfully.');
    }

    public function download(StudentFile $studentFile)
    {
        if (!Storage::disk('local')->exists($studentFile->path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')->download($studentFile->path, $studentFile->filename);
    }
}
