@extends('layouts.admin')
@section('title', 'Passport — '.$student->name)
@section('content')
<div class="flex items-center justify-between gap-4 mb-6 flex-wrap no-print">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.students.show', $student) }}"
           class="p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Student Passport</h1>
    </div>
    <div class="flex gap-2 flex-wrap items-center">
        <a href="{{ route('admin.students.icard', $student) }}"
           class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition">
            ID Card
        </a>
        <button onclick="window.print()"
                class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition">
            Print
        </button>
    </div>
</div>

{{-- Header Card --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-5 text-white flex items-center gap-5 flex-wrap">
        @if($student->photo)
            <img src="{{ \App\Helpers\Settings::storageUrl($student->photo) }}"
                 class="w-16 h-16 rounded-full border-2 border-white/40 object-cover shrink-0">
        @else
            <div class="w-16 h-16 rounded-full border-2 border-white/40 bg-white/20 flex items-center justify-center text-2xl font-bold shrink-0">
                {{ strtoupper(substr($student->name, 0, 1)) }}
            </div>
        @endif
        <div class="flex-1 min-w-0">
            <h2 class="text-xl font-bold">{{ $student->name }}</h2>
            <div class="text-sm text-white/80 mt-0.5">
                @if($currentEnrollment)
                    {{ $currentEnrollment->class }} · Section {{ $currentEnrollment->section }}
                    @if($currentEnrollment->roll_number) · Roll {{ $currentEnrollment->roll_number }} @endif
                @else
                    {{ $student->class ?? 'No class' }}
                @endif
            </div>
        </div>
        @php $currentCgpa = $yearData->first()['cgpa'] ?? null; @endphp
        @if($currentCgpa !== null)
        <div class="text-right shrink-0">
            <div class="text-3xl font-bold leading-none">{{ number_format($currentCgpa, 2) }}</div>
            <div class="text-xs text-white/70 uppercase tracking-wider mt-0.5">CGPA ({{ $currentYear->name ?? '' }})</div>
        </div>
        @endif
    </div>
    <div class="px-6 py-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-4 text-sm">
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Admission No.</dt>
                <dd class="text-gray-800 font-medium">{{ $student->admission_number ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Date of Birth</dt>
                <dd class="text-gray-800 font-medium">{{ $student->date_of_birth?->format('d M Y') ?? '—' }}@if($student->date_of_birth) <span class="text-gray-400 font-normal">({{ $student->date_of_birth->age }} yrs)</span>@endif</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Father</dt>
                <dd class="text-gray-800 font-medium">{{ $student->father_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Mother</dt>
                <dd class="text-gray-800 font-medium">{{ $student->mother_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Phone</dt>
                <dd class="text-gray-800 font-medium">{{ $student->parent_phone ?? $student->phone ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Blood Group</dt>
                <dd class="text-gray-800 font-medium">{{ $student->blood_group ?? '—' }}</dd>
            </div>
            <div class="sm:col-span-2 md:col-span-3">
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Address</dt>
                <dd class="text-gray-800 font-medium">{{ $student->address ?? '—' }}</dd>
            </div>
        </div>
    </div>
</div>

{{-- Year-by-Year Cards --}}
@if($yearData->isNotEmpty())
<div class="space-y-4">
    @foreach($yearData as $i => $yd)
        @php
            $enr = $yd['enrollment'];
            $ay = $yd['year'];
            $isCurrent = $currentYear && $ay->id === $currentYear->id;
            $expanded = $loop->first;
            $cardId = 'year-card-'.$ay->id;
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <button type="button" onclick="toggleYear({{ $ay->id }})"
                    class="w-full px-6 py-4 flex items-center justify-between gap-4 text-left hover:bg-gray-50 transition {{ $isCurrent ? 'border-l-4 border-teal-500' : '' }}">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="text-lg font-bold text-gray-900">{{ $ay->name }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $enr->status === 'active' ? 'bg-green-100 text-green-700' : ($enr->status === 'graduated' ? 'bg-purple-100 text-purple-700' : ($enr->status === 'transferred' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-600')) }}">
                        {{ ucfirst($enr->status) }}
                    </span>
                    <span class="text-sm text-gray-500">{{ $enr->class }} @if($enr->section)· Section {{ $enr->section }} @endif @if($enr->roll_number)· Roll {{ $enr->roll_number }} @endif</span>
                </div>
                <div class="flex items-center gap-4 shrink-0">
                    @if($yd['cgpa'] !== null)
                        <span class="text-sm font-semibold text-gray-700">CGPA: {{ number_format($yd['cgpa'], 2) }}</span>
                    @endif
                    @if($yd['attPct'] !== null)
                        <span class="text-sm font-semibold {{ $yd['attPct'] >= 75 ? 'text-green-600' : 'text-red-500' }}">Att: {{ $yd['attPct'] }}%</span>
                    @endif
                    @if($yd['marksByExam']->isNotEmpty())
                        <span class="text-xs text-gray-400">{{ $yd['marksByExam']->count() }} exam(s)</span>
                    @endif
                    <svg id="icon-{{ $ay->id }}" class="w-5 h-5 text-gray-400 transition-transform {{ $expanded ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                </div>
            </button>

            <div id="body-{{ $ay->id }}" class="{{ $expanded ? '' : 'hidden' }} border-t border-gray-100">
                {{-- Quick Stats --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 px-6 py-4 bg-gray-50/50">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
                        <div class="text-2xl font-bold text-teal-600">{{ $yd['marksByExam']->count() }}</div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-1">Exams Taken</div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
                        <div class="text-2xl font-bold {{ $yd['attPct'] !== null ? ($yd['attPct'] >= 75 ? 'text-blue-600' : 'text-red-500') : 'text-gray-400' }}">{{ $yd['attPct'] !== null ? $yd['attPct'].'%' : '—' }}</div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-1">Attendance</div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $yd['attendance']['present'] + $yd['attendance']['late'] + $yd['attendance']['excused'] }}</div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-1">Days Present</div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
                        <div class="text-2xl font-bold text-red-600">{{ $yd['attendance']['absent'] }}</div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-1">Days Absent</div>
                    </div>
                </div>

                {{-- Marks Per Exam --}}
                @if($yd['marksByExam']->isNotEmpty())
                <div class="px-6 py-4 space-y-6">
                    @foreach($yd['marksByExam'] as $examData)
                        @php $exam = $examData['exam']; @endphp
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-800">{{ $exam->name }}</h4>
                                <span class="text-xs text-gray-500">
                                    Avg: <strong class="text-gray-800">{{ $examData['avgPct'] ?? '—' }}%</strong>
                                    @if($examData['avgGp']) · GP: <strong class="text-gray-800">{{ $examData['avgGp'] }}</strong>@endif
                                </span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                                            <th class="px-3 py-2">Subject</th>
                                            <th class="px-3 py-2 text-center">Full</th>
                                            <th class="px-3 py-2 text-center">Theory</th>
                                            <th class="px-3 py-2 text-center">Assign</th>
                                            <th class="px-3 py-2 text-center">Total</th>
                                            <th class="px-3 py-2 text-center">%</th>
                                            <th class="px-3 py-2 text-center">Grade</th>
                                            <th class="px-3 py-2 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($examData['subjects'] as $mark)
                                            @php $pct = $mark->percentage(); @endphp
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-3 py-2 font-medium text-gray-800">{{ $mark->subject }}</td>
                                                <td class="px-3 py-2 text-center text-gray-600">{{ $mark->full_marks }}</td>
                                                <td class="px-3 py-2 text-center text-gray-600">{{ $mark->theory_marks ?? '—' }}</td>
                                                <td class="px-3 py-2 text-center text-gray-600">{{ $mark->assignment_marks ?? '—' }}</td>
                                                <td class="px-3 py-2 text-center font-medium">{{ $mark->total_marks ?? $mark->obtained_marks ?? '—' }}</td>
                                                <td class="px-3 py-2 text-center font-medium">{{ $pct !== null ? $pct.'%' : '—' }}</td>
                                                <td class="px-3 py-2 text-center">
                                                    @php $g = $mark->computedGrade() ?? $mark->grade; @endphp
                                                    @if($g)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">{{ $g }}</span>
                                                    @else — @endif
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    @php $s = $mark->status(); @endphp
                                                    @if($s === 'pass')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">PASS</span>
                                                    @elseif($s === 'fail')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-600">FAIL</span>
                                                    @else
                                                        <span class="text-gray-400">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
                @else
                <div class="px-6 py-8 text-center text-gray-400 text-sm">No examination records for this year.</div>
                @endif

                {{-- Attendance Detail --}}
                <div class="px-6 py-4 border-t border-gray-100">
                    <div class="grid grid-cols-4 gap-3">
                        <div class="bg-green-50 rounded-xl p-3 text-center">
                            <div class="text-xl font-bold text-green-700">{{ $yd['attendance']['present'] }}</div>
                            <div class="text-xs font-semibold text-green-600 uppercase tracking-wider mt-0.5">Present</div>
                        </div>
                        <div class="bg-red-50 rounded-xl p-3 text-center">
                            <div class="text-xl font-bold text-red-600">{{ $yd['attendance']['absent'] }}</div>
                            <div class="text-xs font-semibold text-red-600 uppercase tracking-wider mt-0.5">Absent</div>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-3 text-center">
                            <div class="text-xl font-bold text-amber-700">{{ $yd['attendance']['late'] }}</div>
                            <div class="text-xs font-semibold text-amber-600 uppercase tracking-wider mt-0.5">Late</div>
                        </div>
                        <div class="bg-indigo-50 rounded-xl p-3 text-center">
                            <div class="text-xl font-bold text-indigo-700">{{ $yd['attendance']['excused'] }}</div>
                            <div class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mt-0.5">Excused</div>
                        </div>
                    </div>
                </div>

                {{-- Download links --}}
                <div class="px-6 py-3 border-t border-gray-100 flex gap-2 flex-wrap bg-gray-50/50">
                    <a href="{{ route('admin.students.icard', $student) }}"
                       class="px-3 py-1.5 text-xs font-semibold bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition">
                        ID Card
                    </a>
                    @if($yd['marksByExam']->isNotEmpty())
                        @foreach($yd['marksByExam'] as $examData)
                            @php $exam = $examData['exam']; @endphp
                            @if($exam)
                            <a href="{{ route('admin.students.result-card', ['student' => $student, 'exam' => $exam->id]) }}"
                               class="px-3 py-1.5 text-xs font-semibold bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">
                                {{ $exam->name }} Report
                            </a>
                            @endif
                        @endforeach
                        @if($yd['marksByExam']->isNotEmpty())
                        <a href="{{ route('admin.students.result-card', ['student' => $student, 'exam' => 'full-year']) }}"
                           class="px-3 py-1.5 text-xs font-semibold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            Full Year Report
                        </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@else
<div class="text-center py-12 text-gray-400">
    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
    <p class="text-sm font-medium">No academic history found.</p>
    <p class="text-xs mt-1">Enroll this student in an academic year to build their passport.</p>
</div>
@endif

<script>
function toggleYear(id) {
    var body = document.getElementById('body-' + id);
    var icon = document.getElementById('icon-' + id);
    if (body.classList.contains('hidden')) {
        body.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        body.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}
</script>

<style>
@media print {
    .no-print { display:none !important; }
    body { background:#fff; }
    .bg-white { border:1px solid #e2e8f0 !important; break-inside:avoid; box-shadow:none !important; }
    .bg-gradient-to-r { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .bg-green-50, .bg-red-50, .bg-amber-50, .bg-indigo-50 { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .hidden { display:block !important; }
}
</style>
@endsection