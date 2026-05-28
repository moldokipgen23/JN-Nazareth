@extends('layouts.admin')
@section('page-title', $student->name)

@section('content')
@php
    $waDigits = preg_replace('/\D+/', '', $student->parent_phone ?? $student->phone ?? '');
    if (strlen($waDigits) === 10) { $waDigits = '91' . $waDigits; }
    $activeTab = request('tab', 'personal');
@endphp
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.students.index', $student->class ? ['class'=>$student->class] : []) }}"
               class="p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-xl font-bold text-gray-900">{{ $student->name }}</h2>
        </div>
        <div class="flex gap-2 flex-wrap">
            @if($waDigits)
            <a href="https://wa.me/{{ $waDigits }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                WhatsApp Parent
            </a>
            @endif
            <a href="{{ route('admin.students.edit', $student) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition">
                Edit
            </a>
            <form method="POST" action="{{ route('admin.students.destroy', $student) }}"
                  onsubmit="return confirm('Delete {{ addslashes($student->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 border border-red-300 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition">
                    Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="display:flex;gap:0;border-bottom:2px solid #e2e8f0;margin-bottom:0;">
        <a href="{{ route('admin.students.show', ['student' => $student, 'tab' => 'personal']) }}"
           style="padding:10px 20px;font-size:13px;font-weight:700;text-decoration:none;border-bottom:2px solid {{ $activeTab === 'personal' ? '#0f766e' : 'transparent' }};color:{{ $activeTab === 'personal' ? '#0f766e' : '#94a3b8' }};margin-bottom:-2px;transition:all .15s;">
            Personal
        </a>
        <a href="{{ route('admin.students.show', ['student' => $student, 'tab' => 'enrollment']) }}"
           style="padding:10px 20px;font-size:13px;font-weight:700;text-decoration:none;border-bottom:2px solid {{ $activeTab === 'enrollment' ? '#0f766e' : 'transparent' }};color:{{ $activeTab === 'enrollment' ? '#0f766e' : '#94a3b8' }};margin-bottom:-2px;transition:all .15s;">
            Enrollment
        </a>
        <a href="{{ route('admin.students.show', ['student' => $student, 'tab' => 'marks']) }}"
           style="padding:10px 20px;font-size:13px;font-weight:700;text-decoration:none;border-bottom:2px solid {{ $activeTab === 'marks' ? '#0f766e' : 'transparent' }};color:{{ $activeTab === 'marks' ? '#0f766e' : '#94a3b8' }};margin-bottom:-2px;transition:all .15s;">
            Marks
        </a>
        <a href="{{ route('admin.students.show', ['student' => $student, 'tab' => 'attendance']) }}"
           style="padding:10px 20px;font-size:13px;font-weight:700;text-decoration:none;border-bottom:2px solid {{ $activeTab === 'attendance' ? '#0f766e' : 'transparent' }};color:{{ $activeTab === 'attendance' ? '#0f766e' : '#94a3b8' }};margin-bottom:-2px;transition:all .15s;">
            Attendance
        </a>
        <a href="{{ route('admin.students.show', ['student' => $student, 'tab' => 'documents']) }}"
           style="padding:10px 20px;font-size:13px;font-weight:700;text-decoration:none;border-bottom:2px solid {{ $activeTab === 'documents' ? '#0f766e' : 'transparent' }};color:{{ $activeTab === 'documents' ? '#0f766e' : '#94a3b8' }};margin-bottom:-2px;transition:all .15s;">
            Documents
        </a>
    </div>

    @if($activeTab === 'personal')
    {{-- Personal Info --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-teal-600 to-teal-700 h-24"></div>
        <div class="px-6 pb-6">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-12 mb-6">
                @if($student->photo)
                    <img src="{{ \App\Helpers\Settings::storageUrl($student->photo) }}"
                         style="width:80px;height:80px;border-radius:9999px;border:4px solid #fff;object-fit:cover;box-shadow:0 1px 2px 0 rgba(0,0,0,.05);flex-shrink:0;">
                @else
                    <div style="width:80px;height:80px;border-radius:9999px;border:4px solid #fff;background:#ccfbf1;display:flex;align-items:center;justify-content:center;color:#0f766e;font-weight:700;font-size:28px;box-shadow:0 1px 2px 0 rgba(0,0,0,.05);flex-shrink:0;">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                @endif
                <div class="pb-1 flex flex-wrap items-center gap-2">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $student->name }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold mt-1 bg-blue-100 text-blue-800">
                            {{ $student->class ?? 'No class' }}@if($student->roll_number) · Roll {{ $student->roll_number }}@endif
                        </span>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $student->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ $student->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Father's Name</dt>
                    <dd class="text-gray-800">{{ $student->father_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Mother's Name</dt>
                    <dd class="text-gray-800">{{ $student->mother_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Parent Phone</dt>
                    <dd class="text-gray-800">{{ $student->parent_phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Section</dt>
                    <dd class="text-gray-800">{{ $student->section ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Date of Birth</dt>
                    <dd class="text-gray-800">
                        @if($student->date_of_birth)
                            {{ $student->date_of_birth->format('d M Y') }}
                            <span class="text-gray-400 text-xs ml-1">({{ $student->date_of_birth->age }} yrs)</span>
                        @else — @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Admission Date</dt>
                    <dd class="text-gray-800">{{ $student->admission_date?->format('d M Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Admission No.</dt>
                    <dd class="text-gray-800">{{ $student->admission_number ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Aadhar No.</dt>
                    <dd class="text-gray-800">
                        @if($student->aadhar_number)
                            <span data-aadhar-masked>{{ $student->maskedAadhar() }}</span>
                            <span data-aadhar-full hidden>{{ $student->aadhar_number }}</span>
                            <button type="button" class="ml-2 text-xs text-teal-600 hover:underline"
                                    onclick="(function(b){var d=b.parentNode;var m=d.querySelector('[data-aadhar-masked]'),f=d.querySelector('[data-aadhar-full]');var s=f.hidden;f.hidden=!s;m.hidden=s;b.textContent=s?'Hide':'Show';})(this)">Show</button>
                        @else — @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Academic Year</dt>
                    <dd class="text-gray-800">{{ $student->academic_year ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email</dt>
                    <dd class="text-gray-800">{{ $student->email ?? '—' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Address</dt>
                    <dd class="text-gray-800">{{ $student->address ?? '—' }}</dd>
                </div>
                @if($student->notes)
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Notes</dt>
                    <dd class="text-gray-700 leading-relaxed">{{ $student->notes }}</dd>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    @if($activeTab === 'enrollment')
    {{-- Enrollment History + Change Status --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Enrollment History</h3>
            <button type="button" onclick="document.getElementById('status-form').classList.toggle('hidden')"
                    class="text-xs font-semibold text-teal-700 bg-teal-50 hover:bg-teal-100 px-3 py-1.5 rounded-lg transition">
                Change Status
            </button>
        </div>

        <div id="status-form" class="hidden px-6 py-4 bg-amber-50 border-b border-amber-100">
            <form method="POST" action="{{ route('admin.students.enrollment-status', $student) }}"
                  class="flex flex-wrap gap-3 items-end">
                @csrf @method('PATCH')
                @if(session('error'))<div class="w-full text-xs text-red-600 font-medium">{{ session('error') }}</div>@endif
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">New Status</label>
                    <select name="status" required class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        @foreach(['active'=>'Active','dropped'=>'Dropped','transferred'=>'Transferred','graduated'=>'Graduated'] as $val=>$lbl)
                            <option value="{{ $val }}" {{ ($student->currentEnrollment?->status ?? 'active') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Leaving Date</label>
                    <input type="date" name="left_on" value="{{ $student->currentEnrollment?->left_on?->format('Y-m-d') }}"
                           class="border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Remarks</label>
                    <input type="text" name="remarks" value="{{ $student->currentEnrollment?->remarks }}" maxlength="500"
                           placeholder="Reason / note…"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <button type="submit" class="px-4 py-2 bg-amber-600 text-white text-sm font-semibold rounded-lg hover:bg-amber-700 transition">
                    Save Status
                </button>
            </form>
        </div>

        @if($student->enrollments->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                        <th class="px-6 py-3">Year</th>
                        <th class="px-6 py-3">Class</th>
                        <th class="px-6 py-3">Section</th>
                        <th class="px-6 py-3">Roll</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 hidden sm:table-cell">Enrolled</th>
                        <th class="px-6 py-3 hidden sm:table-cell">Left</th>
                        <th class="px-6 py-3 hidden md:table-cell">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($student->enrollments->sortByDesc('id') as $enr)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3 font-semibold text-gray-800">{{ $enr->academicYear?->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-700">{{ $enr->class }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $enr->section ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $enr->roll_number ?? '—' }}</td>
                        <td class="px-6 py-3">
                            @php
                                $colours = ['active'=>'bg-green-100 text-green-700','dropped'=>'bg-red-100 text-red-600','transferred'=>'bg-blue-100 text-blue-700','graduated'=>'bg-purple-100 text-purple-700'];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $colours[$enr->status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($enr->status) }}</span>
                        </td>
                        <td class="px-6 py-3 text-gray-500 hidden sm:table-cell">{{ $enr->enrolled_on?->format('d M Y') ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-500 hidden sm:table-cell">{{ $enr->left_on?->format('d M Y') ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-400 text-xs hidden md:table-cell">{{ $enr->remarks ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8 text-gray-400 text-sm">No enrollment records found.</div>
        @endif
    </div>
    @endif

    @if($activeTab === 'marks')
    {{-- Marks Summary --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Marks</h3>
            <div class="flex gap-2">
                @if($student->currentEnrollment)
                    @php $exams = \App\Models\Exam::where('academic_year_id', $student->currentEnrollment->academic_year_id)->where('is_active', true)->orderBy('starts_on')->get(); @endphp
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition">
                            Result Card <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-1 w-52 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                            @foreach($exams as $exam)
                            <a href="{{ route('admin.students.result-card', ['student' => $student, 'exam' => $exam]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ $exam->name }}</a>
                            @endforeach
                            <a href="{{ route('admin.students.report-card.full-year', $student) }}" class="block px-4 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-50 rounded-b-lg border-t border-gray-100">Full Year Report</a>
                        </div>
                    </div>
                @endif
                <a href="{{ route('admin.students.passport', $student) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">Passport</a>
            </div>
        </div>
        @php
            $yearEnrollments = $student->enrollments->sortByDesc('academic_year_id');
        @endphp
        @forelse($yearEnrollments as $enr)
            @php
                $ay = $enr->academicYear;
                $marks = \App\Models\Mark::where('student_enrollment_id', $enr->id)->where('academic_year_id', $ay->id)->with('exam')->get()->groupBy('exam_id');
            @endphp
            @if($marks->isNotEmpty())
            <div class="px-6 py-3 border-b border-gray-100">
                <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:8px;">{{ $ay?->name ?? 'Unknown Year' }}</div>
                @foreach($marks as $examId => $examMarks)
                    @php $exam = $examMarks->first()->exam; @endphp
                    <div style="margin-bottom:10px;">
                        <div style="font-size:12px;font-weight:600;color:#475569;margin-bottom:4px;">{{ $exam?->name ?? 'Exam' }}</div>
                        <table style="width:100%;border-collapse:collapse;font-size:12px;">
                            <thead><tr style="background:#f8fafc;"><th style="padding:4px 8px;text-align:left;">Subject</th><th style="padding:4px 8px;text-align:center;">Obtained</th><th style="padding:4px 8px;text-align:center;">Full</th><th style="padding:4px 8px;text-align:center;">%</th><th style="padding:4px 8px;text-align:center;">Grade</th></tr></thead>
                            <tbody>
                                @foreach($examMarks as $m)
                                <tr style="border-top:1px solid #f1f5f9;">
                                    <td style="padding:4px 8px;">{{ $m->subject }}</td>
                                    <td style="padding:4px 8px;text-align:center;">{{ $m->total_marks ?? $m->obtained_marks ?? '—' }}</td>
                                    <td style="padding:4px 8px;text-align:center;">{{ $m->full_marks }}</td>
                                    <td style="padding:4px 8px;text-align:center;font-weight:600;">{{ $m->percentage() !== null ? $m->percentage().'%' : '—' }}</td>
                                    <td style="padding:4px 8px;text-align:center;">{{ $m->grade ?? '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
            @endif
        @empty
            <div class="text-center py-10 text-gray-400 text-sm">No marks found.</div>
        @endforelse
    </div>
    @endif

    @if($activeTab === 'attendance')
    {{-- Attendance Summary --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Attendance</h3>
        </div>
        @php
            $yearEnrollments = $student->enrollments->sortByDesc('academic_year_id');
        @endphp
        @forelse($yearEnrollments as $enr)
            @php
                $ay = $enr->academicYear;
                $records = \App\Models\AttendanceRecord::where('student_enrollment_id', $enr->id)->where('academic_year_id', $ay->id)->get();
                $summary = ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];
                foreach ($records as $r) { $summary[$r->status] = ($summary[$r->status] ?? 0) + 1; }
                $totalDays = array_sum($summary);
                $presentDays = $summary['present'] + $summary['late'] + $summary['excused'];
                $attPct = $totalDays > 0 ? round($presentDays / $totalDays * 100, 1) : null;
            @endphp
            <div class="px-6 py-3 border-b border-gray-100">
                <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:8px;">{{ $ay?->name ?? 'Unknown Year' }}</div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(80px,1fr));gap:8px;">
                    <div style="background:#dcfce7;border-radius:8px;padding:8px;text-align:center;"><div style="font-size:18px;font-weight:700;color:#15803d;">{{ $summary['present'] }}</div><div style="font-size:10px;color:#15803d;font-weight:600;">Present</div></div>
                    <div style="background:#fee2e2;border-radius:8px;padding:8px;text-align:center;"><div style="font-size:18px;font-weight:700;color:#b91c1c;">{{ $summary['absent'] }}</div><div style="font-size:10px;color:#b91c1c;font-weight:600;">Absent</div></div>
                    <div style="background:#fef3c7;border-radius:8px;padding:8px;text-align:center;"><div style="font-size:18px;font-weight:700;color:#a16207;">{{ $summary['late'] }}</div><div style="font-size:10px;color:#a16207;font-weight:600;">Late</div></div>
                    <div style="background:#e0e7ff;border-radius:8px;padding:8px;text-align:center;"><div style="font-size:18px;font-weight:700;color:#4338ca;">{{ $summary['excused'] }}</div><div style="font-size:10px;color:#4338ca;font-weight:600;">Excused</div></div>
                    <div style="background:#f1f5f9;border-radius:8px;padding:8px;text-align:center;"><div style="font-size:18px;font-weight:700;color:#0f172a;">{{ $attPct !== null ? $attPct.'%' : '—' }}</div><div style="font-size:10px;color:#64748b;font-weight:600;">Attendance</div></div>
                </div>
                @if($records->count() > 0 && $records->count() <= 31)
                <div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:10px;">
                    @foreach($records->sortByDesc('date') as $r)
                        @php
                            $dotBg = match($r->status) { 'present'=>'#15803d', 'absent'=>'#dc2626', 'late'=>'#d97706', 'excused'=>'#6366f1' };
                            $dotTitle = $r->date.' - '.ucfirst($r->status);
                        @endphp
                        <span title="{{ $dotTitle }}" style="width:24px;height:24px;border-radius:6px;background:{{ $dotBg }};display:inline-flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;color:#fff;">{{ substr($r->date, 8, 2) }}</span>
                    @endforeach
                </div>
                @endif
            </div>
        @empty
            <div class="text-center py-10 text-gray-400 text-sm">No attendance records found.</div>
        @endforelse
    </div>
    @endif

    @if($activeTab === 'documents')
    {{-- Documents --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Documents</h3>
            <a href="{{ route('admin.students.icard', $student) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 transition">ID Card</a>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
            <form method="POST" action="{{ route('admin.student-files.store', $student) }}" enctype="multipart/form-data"
                  class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                @csrf
                <div class="flex-1">
                    <label for="file" class="block text-xs font-medium text-gray-600 mb-1">Upload a file</label>
                    <input type="file" name="file" id="file" required
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 transition">
                    @error('file')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition shrink-0">
                    Upload
                </button>
            </form>
        </div>

        @if(isset($student->files) && $student->files->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                            <th class="px-6 py-3">File Name</th>
                            <th class="px-6 py-3">Size</th>
                            <th class="px-6 py-3 hidden sm:table-cell">Date</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($student->files as $file)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3 font-medium text-gray-800">
                                    <span class="truncate max-w-[180px]">{{ $file->filename ?? basename($file->path) }}</span>
                                </td>
                                <td class="px-6 py-3 text-gray-500">
                                    {{ isset($file->size) ? number_format($file->size / 1024, 1) . ' KB' : '—' }}
                                </td>
                                <td class="px-6 py-3 text-gray-500 hidden sm:table-cell">{{ $file->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.student-files.download', $file) }}"
                                           class="px-3 py-1.5 text-xs font-medium text-teal-700 bg-teal-50 hover:bg-teal-100 rounded-lg transition">Download</a>
                                        <form method="POST" action="{{ route('admin.student-files.destroy', $file)}}"
                                              onsubmit="return confirm('Delete this file?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-10 text-gray-400 text-sm">
                No documents uploaded for this student yet.
            </div>
        @endif
    </div>
    @endif
</div>
@endsection
