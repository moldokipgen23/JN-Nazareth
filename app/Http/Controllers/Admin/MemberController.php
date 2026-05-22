<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $classes = Member::classes();
        $currentClass = $request->input('class');

        $query = Member::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('roll_number', 'like', "%{$search}%")
                  ->orWhere('father_name', 'like', "%{$search}%");
            });
        }

        if ($currentClass && in_array($currentClass, $classes, true)) {
            $query->byClass($currentClass);
        }

        $members = $query->orderByRaw("CASE WHEN roll_number IS NULL THEN 1 ELSE 0 END")
                         ->orderByRaw('CAST(roll_number AS INTEGER)')
                         ->orderBy('name')
                         ->paginate(50)
                         ->withQueryString();

        // Per-class counts for the overview cards / tabs
        $rawCounts = Member::selectRaw('class, count(*) as total')
                           ->groupBy('class')
                           ->pluck('total', 'class')
                           ->toArray();

        $classCounts = [];
        foreach ($classes as $class) {
            $classCounts[$class] = $rawCounts[$class] ?? 0;
        }

        $totalStudents = Member::count();
        $unassigned = Member::whereNull('class')->count();

        return view('admin.members.index', compact(
            'members', 'classes', 'currentClass', 'classCounts', 'totalStudents', 'unassigned'
        ));
    }

    public function create(Request $request)
    {
        $classes = Member::classes();
        $prefillClass = $request->input('class');

        return view('admin.members.create', compact('classes', 'prefillClass'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['status'] = $validated['is_active'] ? 'active' : 'inactive';

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('members/photos', 'public');
        }

        $member = Member::create($validated);

        ActivityLogger::log('student_created', $member, "Created student: {$member->name}");

        return redirect()->route('admin.members.show', $member)
                         ->with('success', 'Student added successfully.');
    }

    public function show(Member $member)
    {
        $member->load('files');

        return view('admin.members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        $classes = Member::classes();

        return view('admin.members.edit', compact('member', 'classes'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate($this->rules($member->id));

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['status'] = $validated['is_active'] ? 'active' : 'inactive';

        if ($request->hasFile('photo')) {
            if ($member->photo) {
                Storage::disk('public')->delete($member->photo);
            }
            $validated['photo'] = $request->file('photo')->store('members/photos', 'public');
        }

        $member->update($validated);

        ActivityLogger::log('student_updated', $member, "Updated student: {$member->name}");

        return redirect()->route('admin.members.show', $member)
                         ->with('success', 'Student updated successfully.');
    }

    public function destroy(Member $member)
    {
        $files = $member->files;
        foreach ($files as $file) {
            Storage::disk('local')->delete($file->path);
        }

        Storage::disk('local')->deleteDirectory("members/{$member->id}");

        if ($member->photo) {
            Storage::disk('public')->delete($member->photo);
        }

        $name = $member->name;
        $member->delete();

        ActivityLogger::log('student_deleted', null, "Deleted student: {$name}");

        return redirect()->route('admin.members.index')
                         ->with('success', 'Student deleted successfully.');
    }

    /** Show the CSV import form. */
    public function importForm()
    {
        $classes = Member::classes();

        return view('admin.members.import', compact('classes'));
    }

    /** Download a ready-to-fill CSV template with sample rows. */
    public function importTemplate()
    {
        $rows = [
            ['name', 'roll_number', 'father_name', 'mother_name', 'parent_phone', 'phone', 'email', 'date_of_birth', 'address', 'class', 'section'],
            ['Aman Singh',      '1', 'Rajesh Singh',   'Sunita Singh',   '9876543210', '',           'aman.singh@example.com',  '2015-04-12', 'Hill Road, Churachandpur',   'Class III', 'A'],
            ['Mary Lalrin',     '2', 'John Lalrin',    'Esther Lalrin',  '9812345678', '9800011122', 'mary.l@example.com',      '2015-08-30', 'New Bazar, Churachandpur',   'Class III', 'A'],
            ['David Haokip',    '3', 'Thang Haokip',   'Niang Haokip',   '9856001234', '',           '',                        '2016-01-05', 'Tuibong, Churachandpur',     'Class III', 'B'],
            ['Priya Devi',      '4', 'Suresh Devi',    'Rita Devi',      '9870098700', '',           'priya.devi@example.com',  '2014-11-22', 'Zenhang, Churachandpur',     'Class IV',  'A'],
            ['Joseph Kipgen',   '5', 'Paul Kipgen',    'Mercy Kipgen',   '9801122334', '',           '',                        '2014-06-18', 'Rengkai, Churachandpur',     'Class IV',  'A'],
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

    /** Process an uploaded CSV and bulk-create students. */
    public function import(Request $request)
    {
        $request->validate([
            'file'          => ['required', 'file', 'mimes:csv,txt', 'max:4096'],
            'class'         => ['nullable', Rule::in(Member::classes())],
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

        // Header row → column index map.
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

            // Class — CSV column wins, else the form default.
            $class = $get($row, 'class') ?: $defaultClass;
            if ($class && ! in_array($class, Member::classes(), true)) {
                $class = null;
            }

            // Date of birth — accept any parseable date.
            $dob = $get($row, 'date_of_birth');
            try {
                $dob = $dob !== '' ? \Illuminate\Support\Carbon::parse($dob)->toDateString() : null;
            } catch (\Throwable $e) {
                $dob = null;
            }

            // Skip duplicate emails to avoid a unique-constraint failure.
            $email = $get($row, 'email');
            if ($email !== '' && Member::where('email', $email)->exists()) {
                $email = null;
            }

            Member::create([
                'name'          => $name,
                'roll_number'   => $get($row, 'roll_number') ?: null,
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
                'status'        => 'active',
            ]);
            $imported++;
        }

        fclose($handle);

        ActivityLogger::log('students_imported', null, "Imported {$imported} student(s) via CSV.");

        $msg = "Imported {$imported} student(s).";
        if ($skipped > 0) {
            $msg .= " Skipped {$skipped} row(s) with no name.";
        }

        return redirect()->route('admin.members.index')->with('success', $msg);
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'roll_number'    => ['nullable', 'string', 'max:20'],
            'class'          => ['nullable', Rule::in(Member::classes())],
            'section'        => ['nullable', 'string', 'max:10'],
            'academic_year'  => ['nullable', 'string', 'max:20'],
            'father_name'    => ['nullable', 'string', 'max:255'],
            'mother_name'    => ['nullable', 'string', 'max:255'],
            'parent_phone'   => ['nullable', 'string', 'max:50'],
            'phone'          => ['nullable', 'string', 'max:50'],
            'email'          => ['nullable', 'email', 'max:255', Rule::unique('members', 'email')->ignore($ignoreId)],
            'address'        => ['nullable', 'string'],
            'date_of_birth'  => ['nullable', 'date'],
            'admission_date' => ['nullable', 'date'],
            'is_active'      => ['nullable', 'boolean'],
            'notes'          => ['nullable', 'string'],
            'photo'          => ['nullable', 'image', 'max:5120'],
        ];
    }
}
