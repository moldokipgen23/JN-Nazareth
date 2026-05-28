@extends('layouts.admin')
@section('page-title', $student->name)

@section('content')
@php
    $waDigits = preg_replace('/\D+/', '', $student->parent_phone ?? $student->phone ?? '');
    if (strlen($waDigits) === 10) { $waDigits = '91' . $waDigits; }
@endphp
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.students.index', $student->class ? ['class'=>$student->class] : []) }}"
               class="p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-xl font-bold text-gray-900">Student Profile</h2>
        </div>
        <div class="flex gap-2 flex-wrap">
            @if($waDigits)
            <a href="https://wa.me/{{ $waDigits }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                WhatsApp Parent
            </a>
            @endif
            <a href="{{ route('admin.students.icard', $student) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 transition">
                ID Card
            </a>
            @if($student->currentEnrollment)
                @php
                    $exams = \App\Models\Exam::where('academic_year_id', $student->currentEnrollment->academic_year_id)
                        ->where('status', 'published')
                        ->orderBy('held_on')->get();
                @endphp
                <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition">
                    Result Card
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" @click.outside="open = false"
                     class="absolute right-0 mt-1 w-52 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                    @if($exams->isNotEmpty())
                    @foreach($exams as $exam)
                    <a href="{{ route('admin.students.result-card', ['student' => $student, 'exam' => $exam]) }}"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 first:rounded-t-lg">
                        {{ $exam->name }}
                    </a>
                    @endforeach
                    @endif
                    <a href="{{ route('admin.students.report-card.full-year', $student) }}"
                       class="block px-4 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-50 {{ $exams->isNotEmpty() ? '' : 'first:rounded-t-lg' }} last:rounded-b-lg border-t {{ $exams->isNotEmpty() ? 'border-gray-100' : '' }}">
                        Full Year Report
                    </a>
                </div>
            </div>
            @endif
            <a href="{{ route('admin.students.passport', $student) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                Passport
            </a>
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

    {{-- Profile Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-teal-600 to-teal-700 h-24"></div>
        <div class="px-6 pb-6">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-12 mb-6">
                @if($student->photo)
                    <img src="{{ \App\Helpers\Settings::storageUrl($student->photo) }}"
                         class="w-20 h-20 rounded-full border-4 border-white object-cover shadow shrink-0">
                @else
                    <div class="w-20 h-20 rounded-full border-4 border-white bg-teal-100 flex items-center justify-center text-teal-700 font-bold text-2xl shadow shrink-0">
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

    {{-- Enrollment History + Change Status --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Enrollment History</h3>
            <button type="button" onclick="document.getElementById('status-form').classList.toggle('hidden')"
                    class="text-xs font-semibold text-teal-700 bg-teal-50 hover:bg-teal-100 px-3 py-1.5 rounded-lg transition">
                Change Status
            </button>
        </div>

        {{-- Change status inline form --}}
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

    {{-- Documents --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Documents</h3>
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
                                    <span class="truncate max-w-[180px]">{{ $file->original_name ?? basename($file->path) }}</span>
                                </td>
                                <td class="px-6 py-3 text-gray-500">
                                    {{ isset($file->size) ? number_format($file->size / 1024, 1) . ' KB' : '—' }}
                                </td>
                                <td class="px-6 py-3 text-gray-500 hidden sm:table-cell">{{ $file->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.student-files.download', $file) }}"
                                           class="px-3 py-1.5 text-xs font-medium text-teal-700 bg-teal-50 hover:bg-teal-100 rounded-lg transition">Download</a>
                                        <form method="POST" action="{{ route('admin.student-files.destroy', $file) }}"
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
</div>
@endsection
