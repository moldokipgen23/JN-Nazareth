<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\Mark;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $workingYear = app('workingYear');
        $classes = Student::classes();
        $currentClass = $request->input('class');
        $selectedSection = $request->input('section');

        $selectedYearId = $workingYear?->id;
        $enrollmentStatus = $request->input('enrollment_status', 'active');

        // Unassigned students view — no_class=1
        if ($request->boolean('no_class')) {
            $query = Student::whereNull('class');
            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('father_name', 'like', "%{$search}%");
                });
            }
            $students = $query->orderBy('name')->paginate(50)->withQueryString();
            $noClass = true;
            return view('admin.students.index', compact(
                'students', 'noClass', 'classes', 'currentClass', 'selectedSection', 'selectedYearId', 'enrollmentStatus'
            ));
        }

        if (!$currentClass) {
            // Class grid view — show count per class
            $classCounts = StudentEnrollment::where('academic_year_id', $selectedYearId)
                ->where('status', 'active')
                ->selectRaw('class, count(*) as total')
                ->groupBy('class')
                ->pluck('total', 'class');

            $totalStudents = $classCounts->sum();
            $unassigned = Student::whereNull('class')->count();

            $selectedSection = null;

            return view('admin.students.index', compact(
                'classCounts', 'totalStudents', 'unassigned',
                'classes', 'currentClass', 'selectedSection', 'selectedYearId', 'enrollmentStatus'
            ));
        }

        // Class selected — get sections for this class
        $sections = Section::active()
            ->where('class', $currentClass)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name');

        $sectionCounts = StudentEnrollment::where('academic_year_id', $selectedYearId)
            ->where('class', $currentClass)
            ->where('status', 'active')
            ->selectRaw('section, count(*) as total')
            ->groupBy('section')
            ->pluck('total', 'section');

        if (!$selectedSection) {
            // Auto-fill: if class has exactly 1 section, go straight to student table
            if ($sections->count() === 1) {
                return redirect()->route('admin.students.index', [
                    'class'   => $currentClass,
                    'section' => $sections->first(),
                ]);
            }

            // Section grid view
            $selectedSection = null;
            return view('admin.students.index', compact(
                'sections', 'sectionCounts', 'selectedSection',
                'classes', 'currentClass', 'selectedYearId', 'enrollmentStatus'
            ));
        }

        // Class + section selected — show student table
        $query = Student::query();

        $query->whereHas('enrollments', function ($q) use ($selectedYearId, $enrollmentStatus) {
            $q->where('academic_year_id', $selectedYearId);
            if ($enrollmentStatus && $enrollmentStatus !== 'all') {
                $q->where('status', $enrollmentStatus);
            }
        });

        $query->where('class', $currentClass);
        $query->where('section', $selectedSection);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('roll_number', 'like', "%{$search}%")
                  ->orWhere('father_name', 'like', "%{$search}%");
            });
        }

        $students = $query->orderByRaw("CASE WHEN roll_number IS NULL THEN 1 ELSE 0 END")
                         ->orderByRaw('CAST(roll_number AS UNSIGNED)')
                         ->orderBy('name')
                         ->paginate(50)
                         ->withQueryString();

        return view('admin.students.index', compact(
            'students',
            'classes', 'currentClass', 'selectedSection', 'sections',
            'selectedYearId', 'enrollmentStatus'
        ));
    }

    public function create(Request $request)
    {
        $classes = Student::classes();
        $prefillClass = $request->input('class');
        $sectionList = Section::active()->orderBy('sort_order')->orderBy('name')->get()
            ->groupBy('class')->map(fn($g) => $g->pluck('name'))->toJson();

        $activeYear = AcademicYear::current();
        $defaultYear = $activeYear?->name ?? \App\Helpers\Settings::get('academic_year', '');

        // Suggest next roll number for selected class
        $nextRoll = null;
        if ($prefillClass && $activeYear) {
            $maxRoll = StudentEnrollment::forActiveYear()
                ->active()
                ->where('class', $prefillClass)
                ->whereNotNull('roll_number')
                ->orderByRaw('CAST(roll_number AS UNSIGNED) DESC')
                ->value('roll_number');
            $nextRoll = $maxRoll ? (int) $maxRoll + 1 : 1;
        }

        return view('admin.students.create', compact(
            'classes', 'prefillClass', 'sectionList', 'activeYear', 'defaultYear', 'nextRoll'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $student = Student::create($validated);

        // Auto-generate admission number: ADM-YYYY-XXXX
        if (! $student->admission_number) {
            $year = now()->format('Y');
            $padded = str_pad((string) $student->id, 4, '0', STR_PAD_LEFT);
            $student->updateQuietly(['admission_number' => "ADM-{$year}-{$padded}"]);
        }

        StudentEnrollment::syncFromStudent($student);

        ActivityLogger::log('student_created', $student, "Created student: {$student->name}");

        return redirect()->route('admin.students.show', $student)
                         ->with('success', 'Student admitted successfully.');
    }

    public function suggestRoll(Request $request)
    {
        $class = $request->input('class');
        $year = AcademicYear::current();
        $nextRoll = 1;

        if ($class && $year) {
            $maxRoll = StudentEnrollment::forActiveYear()
                ->active()
                ->where('class', $class)
                ->whereNotNull('roll_number')
                ->orderByRaw('CAST(roll_number AS UNSIGNED) DESC')
                ->value('roll_number');
            $nextRoll = $maxRoll ? (int) $maxRoll + 1 : 1;
        }

        return response()->json(['roll' => (string) $nextRoll]);
    }

    public function show(Student $student)
    {
        $student->load(['files', 'enrollments.academicYear']);

        return view('admin.students.show', compact('student'));
    }

    public function passport(Student $student)
    {
        $student->load('files', 'enrollments.academicYear');

        $currentYear = AcademicYear::current();
        $currentEnrollment = $currentYear
            ? StudentEnrollment::where('student_id', $student->id)
                ->where('academic_year_id', $currentYear->id)->first()
            : null;

        // Build year-by-year data
        $yearData = collect();
        foreach ($student->enrollments->sortByDesc('academic_year_id') as $enr) {
            $ay = $enr->academicYear;
            if (! $ay) continue;

            // Marks per exam for this year
            $marks = Mark::where('student_enrollment_id', $enr->id)
                ->where('academic_year_id', $ay->id)
                ->with('exam')
                ->get()
                ->groupBy('exam_id');

            $marksByExam = $marks->map(function ($examMarks) {
                $exam = $examMarks->first()->exam;
                $pcts = $examMarks->map(fn($m) => $m->percentage())->filter();
                $gps  = $examMarks->map(fn($m) => $m->computedGradePoint())->filter();
                return [
                    'exam'     => $exam,
                    'subjects' => $examMarks,
                    'avgPct'   => $pcts->isNotEmpty() ? round($pcts->avg(), 2) : null,
                    'avgGp'    => $gps->isNotEmpty() ? round($gps->avg(), 2) : null,
                ];
            })->sortBy(fn($e) => $e['exam']?->sort_order ?? 0)->values();

            $allGps = $marksByExam->pluck('avgGp')->filter();
            $cgpa = $allGps->isNotEmpty() ? round($allGps->avg(), 2) : null;

            // Attendance for this year
            $attSummary = ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];
            $records = AttendanceRecord::where('student_enrollment_id', $enr->id)
                ->where('academic_year_id', $ay->id)
                ->get();
            foreach ($records as $r) {
                $attSummary[$r->status] = ($attSummary[$r->status] ?? 0) + 1;
            }
            $totalDays = array_sum($attSummary);
            $presentDays = $attSummary['present'] + $attSummary['late'] + $attSummary['excused'];
            $attPct = $totalDays > 0 ? round($presentDays / $totalDays * 100, 1) : null;

            $yearData->push([
                'enrollment' => $enr,
                'year'       => $ay,
                'marksByExam' => $marksByExam,
                'cgpa'       => $cgpa,
                'attendance' => $attSummary,
                'attPct'     => $attPct,
            ]);
        }

        return view('admin.students.passport', compact(
            'student', 'currentYear', 'currentEnrollment', 'yearData'
        ));
    }

    public function updateEnrollmentStatus(Request $request, Student $student)
    {
        $data = $request->validate([
            'status'   => ['required', Rule::in(['active','dropped','transferred','graduated'])],
            'left_on'  => ['nullable', 'date'],
            'remarks'  => ['nullable', 'string', 'max:500'],
        ]);

        $year = AcademicYear::current();
        $enrollment = StudentEnrollment::where('student_id', $student->id)
            ->when($year, fn ($q) => $q->where('academic_year_id', $year->id))
            ->latest('id')
            ->first();

        if (! $enrollment) {
            return back()->with('error', 'No enrollment record found for this student.');
        }

        $enrollment->update([
            'status'   => $data['status'],
            'left_on'  => $data['left_on'] ?? null,
            'remarks'  => $data['remarks'] ?? null,
        ]);

        if ($data['status'] !== 'active') {
            $student->update(['is_active' => false]);
        } else {
            $student->update(['is_active' => true]);
        }

        ActivityLogger::log('enrollment_status_changed', $student,
            "Changed enrollment status to {$data['status']} for {$student->name}");

        return back()->with('success', 'Enrollment status updated.');
    }

    public function edit(Student $student)
    {
        $classes = Student::classes();
        $sectionList = Section::active()->orderBy('sort_order')->orderBy('name')->get()
            ->groupBy('class')->map(fn($g) => $g->pluck('name'))->toJson();

        $activeYear = AcademicYear::current();
        $defaultYear = $activeYear?->name ?? \App\Helpers\Settings::get('academic_year', '');

        return view('admin.students.edit', compact(
            'student', 'classes', 'sectionList', 'activeYear', 'defaultYear'
        ));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate($this->rules($student->id));

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $validated['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $student->update($validated);

        StudentEnrollment::syncFromStudent($student);

        ActivityLogger::log('student_updated', $student, "Updated student: {$student->name}");

        return redirect()->route('admin.students.show', $student)
                         ->with('success', 'Student updated.');
    }

    public function destroy(Student $student)
    {
        $files = $student->files;
        foreach ($files as $file) {
            Storage::disk('local')->delete($file->path);
        }

        Storage::disk('local')->deleteDirectory("students/{$student->id}");

        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        $name = $student->name;
        $student->delete();

        ActivityLogger::log('student_deleted', null, "Deleted student: {$name}");

        return redirect()->route('admin.students.index')
                         ->with('success', 'Student deleted successfully.');
    }

    public function importForm()
    {
        $classes = Student::classes();

        return view('admin.students.import', compact('classes'));
    }

    public function importTemplate()
    {
        $rows = [
            ['name', 'roll_number', 'admission_number', 'father_name', 'mother_name', 'parent_phone', 'phone', 'email', 'date_of_birth', 'aadhar_number', 'address', 'class', 'section'],
            ['Aman Singh',      '1', '06', 'Rajesh Singh',   'Sunita Singh',   '9876543210', '',           'aman.singh@example.com',  '2015-04-12', '1234 5678 9012', 'Hill Road, Churachandpur',   'Class III', 'A'],
            ['Mary Lalrin',     '2', '07', 'John Lalrin',    'Esther Lalrin',  '9812345678', '9800011122', 'mary.l@example.com',      '2015-08-30', '2345 6789 0123', 'New Bazar, Churachandpur',   'Class III', 'A'],
            ['David Haokip',    '3', '08', 'Thang Haokip',   'Niang Haokip',   '9856001234', '',           '',                        '2016-01-05', '3456 7890 1234', 'Tuibong, Churachandpur',     'Class III', 'B'],
            ['Priya Devi',      '4', '09', 'Suresh Devi',    'Rita Devi',      '9870098700', '',           'priya.devi@example.com',  '2014-11-22', '4567 8901 2345', 'Zenhang, Churachandpur',     'Class IV',  'A'],
            ['Joseph Kipgen',   '5', '10', 'Paul Kipgen',    'Mercy Kipgen',   '9801122334', '',           '',                        '2014-06-18', '5678 9012 3456', 'Rengkai, Churachandpur',     'Class IV',  'A'],
        ];

        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(function ($v) {
                $v = (string) $v;
                return (str_contains($v, ',') || str_contains($v, '"'))
                    ? '"' . str_replace('"', '""', $v) . '"'
                    : $v;
            }, $row)) . "\r\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student-import-template.csv"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file'          => ['required', 'file', 'mimes:csv,txt', 'max:4096'],
            'class'         => ['nullable', Rule::in(Student::classes())],
            'section'       => ['nullable', 'string', 'max:10'],
            'academic_year' => ['nullable', 'string', 'max:20'],
        ]);

        $defaultClass   = $request->input('class');
        $defaultSection = $request->input('section');
        $defaultYear    = $request->input('academic_year');

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        if ($handle === false) {
            return back()->with('error', 'Could not read the uploaded file.');
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return back()->with('error', 'The file appears to be empty.');
        }
        $map = [];
        foreach ($header as $i => $col) {
            $map[strtolower(trim((string) $col))] = $i;
        }
        if (! isset($map['name'])) {
            fclose($handle);
            return back()->with('error', 'The CSV must have a "name" column.');
        }

        $get = function (array $row, string $key) use ($map) {
            return isset($map[$key]) && isset($row[$map[$key]])
                ? trim((string) $row[$map[$key]])
                : '';
        };

        $imported = 0;
        $skipped  = 0;
        $line     = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;

            $name = $get($row, 'name');
            if ($name === '') {
                $skipped++;
                continue;
            }

            $class = $get($row, 'class') ?: $defaultClass;
            if ($class && ! in_array($class, Student::classes(), true)) {
                $class = null;
            }

            $dob = $get($row, 'date_of_birth');
            try {
                $dob = $dob !== '' ? \Illuminate\Support\Carbon::parse($dob)->toDateString() : null;
            } catch (\Throwable $e) {
                $dob = null;
            }

            $email = $get($row, 'email');
            if ($email !== '' && Student::where('email', $email)->exists()) {
                $email = null;
            }

            $student = Student::create([
                'name'             => $name,
                'roll_number'      => $get($row, 'roll_number') ?: null,
                'admission_number' => $get($row, 'admission_number') ?: null,
                'aadhar_number'    => $get($row, 'aadhar_number') ?: null,
                'class'         => $class ?: null,
                'section'       => ($get($row, 'section') ?: $defaultSection) ?: null,
                'academic_year' => ($get($row, 'academic_year') ?: $defaultYear) ?: null,
                'father_name'   => $get($row, 'father_name') ?: null,
                'mother_name'   => $get($row, 'mother_name') ?: null,
                'parent_phone'  => $get($row, 'parent_phone') ?: null,
                'phone'         => $get($row, 'phone') ?: null,
                'email'         => $email ?: null,
                'address'       => $get($row, 'address') ?: null,
                'date_of_birth' => $dob,
                'is_active'     => true,
            ]);
            StudentEnrollment::syncFromStudent($student);
            $imported++;
        }

        fclose($handle);

        ActivityLogger::log('students_imported', null, "Imported {$imported} student(s) via CSV.");

        $msg = "Imported {$imported} student(s).";
        if ($skipped > 0) {
            $msg .= " Skipped {$skipped} row(s) with no name.";
        }

        return redirect()->route('admin.students.index')->with('success', $msg);
    }

    public function export(Request $request)
    {
        $activeYear = AcademicYear::current();
        $students = Student::query()
            ->when($request->input('class'), fn ($q, $c) => $q->where('class', $c))
            ->orderByRaw("CASE WHEN roll_number IS NULL THEN 1 ELSE 0 END")
            ->orderByRaw('CAST(roll_number AS UNSIGNED)')
            ->orderBy('name')
            ->get();

        $rows = [
            ['Name', 'Roll No', 'Admission No', 'Class', 'Section', 'Father Name', 'Mother Name', 'Phone', 'Email', 'DOB', 'Blood Group', 'Gender', 'Address', 'Status'],
        ];

        foreach ($students as $s) {
            $rows[] = [
                $s->name,
                $s->roll_number ?? '',
                $s->admission_number ?? '',
                $s->class ?? '',
                $s->section ?? '',
                $s->father_name ?? '',
                $s->mother_name ?? '',
                $s->parent_phone ?? $s->phone ?? '',
                $s->email ?? '',
                $s->date_of_birth?->format('d-m-Y') ?? '',
                $s->blood_group ?? '',
                $s->gender ?? '',
                $s->address ?? '',
                $s->is_active ? 'Active' : 'Inactive',
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
            'Content-Disposition' => 'attachment; filename="student-directory.csv"',
        ]);
    }

    public function exportClass(Request $request)
    {
        $classes = Student::classes();
        $activeYear = AcademicYear::current();
        $class = $request->input('class');

        $students = collect();
        if ($class && $activeYear) {
            $enrollments = StudentEnrollment::forActiveYear()
                ->active()
                ->where('class', $class)
                ->with('student.files')
                ->orderByRaw('CAST(roll_number AS UNSIGNED)')
                ->orderBy('roll_number')
                ->get();
            $students = $enrollments->map(fn($e) => $e->student);
        }

        return view('admin.students.export-class', compact(
            'classes', 'activeYear', 'class', 'students'
        ));
    }

    public function exportClassCsv(Request $request)
    {
        $class = $request->input('class');
        abort_unless($class && in_array($class, Student::classes(), true), 404);

        $activeYear = AcademicYear::current();
        $enrollments = StudentEnrollment::forActiveYear()
            ->active()
            ->where('class', $class)
            ->with('student')
            ->orderByRaw('CAST(roll_number AS UNSIGNED)')
            ->orderBy('roll_number')
            ->get();

        $rows = [
            ['Sl No', 'Admission No', 'Name', 'Roll No', 'Class', 'Section', 'Father Name', 'Mother Name', 'Guardian Name', 'Guardian Relation', 'Phone', 'Email', 'DOB', 'Gender', 'Blood Group', 'Address', 'Status'],
        ];

        $sl = 0;
        foreach ($enrollments as $e) {
            $s = $e->student;
            $sl++;
            $rows[] = [
                $sl,
                $s->admission_number ?? '',
                $s->name,
                $e->roll_number ?? $s->roll_number ?? '',
                $e->class,
                $e->section ?? '',
                $s->father_name ?? '',
                $s->mother_name ?? '',
                $s->guardian_name ?? '',
                $s->guardian_relation ?? '',
                $s->parent_phone ?? $s->phone ?? '',
                $s->email ?? '',
                $s->date_of_birth?->format('d-m-Y') ?? '',
                $s->gender ?? '',
                $s->blood_group ?? '',
                $s->address ?? '',
                $e->status === 'active' ? 'Active' : ucfirst($e->status),
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

        $filename = str_replace(' ', '_', $class) . '-students.csv';
        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function downloadClassDocs(Request $request)
    {
        $class = $request->input('class');
        abort_unless($class && in_array($class, Student::classes(), true), 404);

        $activeYear = AcademicYear::current();
        $enrollments = StudentEnrollment::forActiveYear()
            ->active()
            ->where('class', $class)
            ->with('student.files')
            ->orderByRaw('CAST(roll_number AS UNSIGNED)')
            ->orderBy('roll_number')
            ->get();

        $zip = new \ZipArchive();
        $zipName = tempnam(sys_get_temp_dir(), 'docs_');
        if ($zip->open($zipName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Could not create archive.');
        }

        $added = 0;
        foreach ($enrollments as $e) {
            $s = $e->student;
            $folder = str_replace(' ', '_', $e->class) . '/' . ($e->roll_number ?? $s->id) . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $s->name);
            foreach ($s->files as $file) {
                if ($file->file_path && Storage::disk('local')->exists($file->file_path)) {
                    $ext = pathinfo($file->file_path, PATHINFO_EXTENSION);
                    $fname = $file->name ?: 'document';
                    $zip->addFile(Storage::disk('local')->path($file->file_path), $folder . '/' . $fname . ($ext ? '.' . $ext : ''));
                    $added++;
                }
            }
            // Add photo if exists
            if ($s->photo && Storage::disk('public')->exists($s->photo)) {
                $ext = pathinfo($s->photo, PATHINFO_EXTENSION);
                $zip->addFile(Storage::disk('public')->path($s->photo), $folder . '/photo.' . $ext);
                $added++;
            }
        }

        $zip->close();

        if ($added === 0) {
            unlink($zipName);
            return back()->with('error', 'No documents found for this class.');
        }

        $filename = str_replace(' ', '_', $class) . '-documents.zip';
        return response()->download($zipName, $filename)->deleteFileAfterSend(true);
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'name'             => ['required', 'string', 'max:255'],
            'roll_number'      => ['nullable', 'string', 'max:20'],
            'admission_number' => ['nullable', 'string', 'max:30'],
            'aadhar_number'    => ['nullable', 'string', 'max:20'],
            'class'          => ['nullable', Rule::in(Student::classes())],
            'section'        => ['nullable', 'string', 'max:10'],
            'academic_year'  => ['nullable', 'string', 'max:20'],
            'father_name'    => ['nullable', 'string', 'max:255'],
            'mother_name'    => ['nullable', 'string', 'max:255'],
            'guardian_name'  => ['nullable', 'string', 'max:255'],
            'guardian_email' => ['nullable', 'email', 'max:255'],
            'guardian_relation' => ['nullable', 'string', 'max:50'],
            'parent_phone'   => ['nullable', 'string', 'max:50'],
            'phone'          => ['nullable', 'string', 'max:50'],
            'email'          => ['nullable', 'email', 'max:255', Rule::unique('students', 'email')->ignore($ignoreId)],
            'address'        => ['nullable', 'string'],
            'date_of_birth'  => ['nullable', 'date'],
            'gender'         => ['nullable', 'string', 'max:10'],
            'blood_group'    => ['nullable', 'string', 'max:10'],
            'admission_date' => ['nullable', 'date'],
            'is_active'      => ['nullable', 'boolean'],
            'notes'          => ['nullable', 'string'],
            'photo'          => ['nullable', 'image', 'max:102400'],
        ];
    }
}
