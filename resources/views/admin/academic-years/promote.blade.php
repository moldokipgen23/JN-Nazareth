@extends('layouts.admin')
@section('page-title','Promote Students')
@section('content')

<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
    <a href="{{ route('admin.academic-years.index') }}"
       style="padding:6px;border-radius:8px;color:#64748b;display:flex;align-items:center;">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">Promote Students</h1>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">From <strong>{{ $academicYear->name }}</strong></div>
    </div>
</div>

{{-- Class selector --}}
<div style="background:#fff;border-radius:12px;padding:14px 18px;margin-bottom:16px;box-shadow:0 1px 3px rgba(15,23,42,.06);display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
    <label style="font-size:13px;font-weight:600;color:#0f172a;">Class:</label>
    <form method="GET" action="{{ route('admin.academic-years.promote.form', $academicYear) }}" style="display:flex;gap:10px;align-items:center;">
        <select name="class" onchange="this.form.submit()" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 12px;font-size:13px;min-width:160px;">
            <option value="">— Select class —</option>
            @foreach($classes as $c)
                <option value="{{ $c }}" {{ $selectedClass === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
    </form>
    @if($selectedClass)
    <span style="font-size:12px;color:#64748b;">{{ $enrollments->count() }} active student(s)</span>
    @endif
</div>

{{-- Promotion rules (always visible when no class selected) --}}
@if(!$selectedClass)
<div style="background:#fff;border-radius:12px;padding:14px 18px;margin-bottom:16px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="display:flex;align-items:center;gap:10px;">
        <svg width="18" height="18" fill="none" stroke="#0f766e" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span style="font-size:13px;font-weight:600;color:#475569;">Select a class above to promote students, or configure promotion rules below.</span>
    </div>
</div>

@include('admin.promotion-rules.edit')

<div style="background:#fff;border-radius:12px;padding:48px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="font-size:36px;opacity:.3;margin-bottom:10px;">🎓</div>
    <div style="font-weight:600;color:#475569;">Select a class above to start promoting students.</div>
</div>

@elseif($enrollments->isEmpty())
<div style="background:#fff;border-radius:12px;padding:48px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="font-size:36px;opacity:.3;margin-bottom:10px;">📭</div>
    <div style="font-weight:600;color:#475569;">No active students in {{ $selectedClass }} for {{ $academicYear->name }}.</div>
</div>

@else
@php
    $isGraduating = is_null($enrollments->first()->suggested_next);
    $hasMarks = $enrollments->first()->marks_avg_pct !== null;
    $eligibleCount = $enrollments->where('eligible', true)->count();
    $promotedCount = $enrollments->where(fn($e) => $e->already_promoted !== null)->count();
    $availableCount = $enrollments->count() - $promotedCount;
@endphp

{{-- Promotion rules summary --}}
@if($rule)
<div style="background:#f0fdfa;border:1px solid #99f6e4;border-radius:12px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;flex-wrap:wrap;gap:10px;font-size:12px;color:#0f766e;">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span><strong>Rules:</strong> Min {{ $rule->min_pass_percentage }}% marks</span>
    @if($rule->min_attendance_percentage)<span>· Min {{ $rule->min_attendance_percentage }}% attendance</span>@endif
    @if($rule->fail_subjects_allowed > 0)<span>· Allow {{ $rule->fail_subjects_allowed }} fail(s)</span>@endif
    @if($rule->use_cgpa)<span>· Min CGPA {{ $rule->min_cgpa }}</span>@endif
    @if($rule->exam)<span>· Eval: {{ $rule->exam->name }}</span>@endif
</div>
@endif

<div style="background:#fff;border-radius:12px;overflow-x:auto;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="padding:12px 16px;background:#f8fafc;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <span style="font-size:13px;font-weight:700;color:#0f172a;">
                {{ $selectedClass }}
                @if($isGraduating)
                    <span style="color:#b91c1c;font-weight:700;margin-left:8px;">(Graduating)</span>
                @else
                    <span style="color:#0f766e;font-weight:600;margin-left:8px;">→ {{ $enrollments->first()->suggested_next }}</span>
                @endif
            </span>
            <span style="font-size:11px;color:#64748b;">
                {{ $eligibleCount }} eligible · {{ $availableCount }} available
            </span>
        </div>
        <div style="display:flex;gap:8px;">
            @if($hasMarks && !$isGraduating)
            <button type="button" onclick="selectAllEligible()"
                    style="background:#dbeafe;border:none;padding:5px 12px;border-radius:6px;font-size:11px;font-weight:600;color:#1d4ed8;cursor:pointer;">
                Select All Eligible
            </button>
            <button type="button" onclick="promoteSelected()"
                    style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:5px 12px;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;">
                Promote Selected
            </button>
            @endif
        </div>
    </div>

    <form id="bulk-promote-form" method="POST" action="{{ route('admin.academic-years.promote', $academicYear) }}">
        @csrf
        <input type="hidden" name="class" value="{{ $selectedClass }}">
        <input type="hidden" name="bulk" value="1">

        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#fafafa;">
                <tr>
                    <th style="width:32px;padding:8px 8px;"><input type="checkbox" id="select-all" onchange="toggleAllCheckboxes(this)"></th>
                    <th style="text-align:left;padding:8px 8px;font-size:11px;color:#64748b;font-weight:700;">Rank</th>
                    <th style="text-align:left;padding:8px 10px;font-size:11px;color:#64748b;font-weight:700;">Student</th>
                    <th style="text-align:left;padding:8px 8px;font-size:11px;color:#64748b;font-weight:700;">%</th>
                    <th style="text-align:left;padding:8px 8px;font-size:11px;color:#64748b;font-weight:700;">CGPA</th>
                    <th style="text-align:left;padding:8px 8px;font-size:11px;color:#64748b;font-weight:700;">Att.</th>
                    <th style="text-align:left;padding:8px 8px;font-size:11px;color:#64748b;font-weight:700;">Status</th>
                    <th style="text-align:left;padding:8px 10px;font-size:11px;color:#64748b;font-weight:700;">Next Class</th>
                    <th style="text-align:left;padding:8px 10px;font-size:11px;color:#64748b;font-weight:700;">Section</th>
                    <th style="text-align:right;padding:8px 10px;font-size:11px;color:#64748b;font-weight:700;">Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach($enrollments as $enrollment)
            @php
                $promoted = $enrollment->already_promoted;
                $eligible = $enrollment->eligible;
            @endphp
            <tr style="border-top:1px solid #f1f5f9;{{ $promoted ? 'opacity:.6;' : '' }}" {{ $promoted ? 'data-promoted' : '' }}>
                <td style="padding:8px 8px;">
                    @if(!$promoted)
                    <input type="checkbox" name="enrollment_ids[]" value="{{ $enrollment->id }}"
                           class="enr-checkbox" {{ $eligible ? 'data-eligible' : '' }}
                           style="width:16px;height:16px;accent-color:#0f766e;">
                    @endif
                </td>
                <td style="padding:8px 8px;font-weight:700;color:{{ $enrollment->rank ? '#0f172a' : '#94a3b8' }};">
                    {{ $enrollment->rank ?? '—' }}
                </td>
                <td style="padding:8px 10px;font-weight:600;color:#0f172a;white-space:nowrap;">
                    <div style="display:flex;align-items:center;gap:6px;">
                        @if($enrollment->subjects_breakdown->isNotEmpty())
                        <button type="button" onclick="toggleMarks({{ $enrollment->id }})"
                                style="background:none;border:none;cursor:pointer;padding:2px;color:#94a3b8;font-size:14px;line-height:1;"
                                title="View subject marks">➕</button>
                        @endif
                        {{ $enrollment->student?->name }}
                    </div>
                    <div style="font-size:11px;color:#94a3b8;">Roll {{ $enrollment->roll_number ?? '—' }} · Sec {{ $enrollment->section ?: '—' }}</div>
                </td>
                <td style="padding:8px 8px;white-space:nowrap;">
                    @if($enrollment->marks_avg_pct !== null)
                        <span style="font-weight:600;color:{{ $enrollment->marks_avg_pct >= ($rule->min_pass_percentage ?? 40) ? '#15803d' : '#b91c1c' }};">
                            {{ $enrollment->marks_avg_pct }}%
                        </span>
                    @else
                        <span style="color:#94a3b8;">—</span>
                    @endif
                </td>
                <td style="padding:8px 8px;">
                    @if($enrollment->marks_avg_gp !== null)
                        <span style="font-weight:600;">{{ number_format($enrollment->marks_avg_gp, 2) }}</span>
                    @else
                        <span style="color:#94a3b8;">—</span>
                    @endif
                </td>
                <td style="padding:8px 8px;">
                    @if($enrollment->attendance_pct !== null)
                        <span style="color:{{ $enrollment->attendance_pct >= ($rule->min_attendance_percentage ?? 75) ? '#15803d' : '#b91c1c' }};">
                            {{ $enrollment->attendance_pct }}%
                        </span>
                    @else
                        <span style="color:#94a3b8;">—</span>
                    @endif
                </td>
                <td style="padding:8px 8px;">
                    @if($promoted)
                        <span style="display:inline-block;padding:2px 8px;border-radius:99px;font-size:10px;font-weight:700;background:#dbeafe;color:#1d4ed8;white-space:nowrap;">
                            In {{ $promoted['target_year'] }}
                        </span>
                    @elseif($eligible)
                        <span style="display:inline-block;padding:2px 8px;border-radius:99px;font-size:10px;font-weight:700;background:#dcfce7;color:#15803d;">Eligible</span>
                    @elseif($enrollment->marks_avg_pct !== null || $enrollment->attendance_pct !== null)
                        <span style="display:inline-block;padding:2px 8px;border-radius:99px;font-size:10px;font-weight:700;background:#fee2e2;color:#b91c1c;">Below Threshold</span>
                    @else
                        <span style="color:#94a3b8;font-size:11px;">No data</span>
                    @endif
                </td>
                <td style="padding:8px 10px;">
                    @if($promoted)
                        <span style="color:#94a3b8;font-size:12px;">—</span>
                    @elseif($isGraduating)
                        <span style="color:#b91c1c;font-weight:600;">Graduated</span>
                        <input type="hidden" name="next_class[{{ $enrollment->id }}]" value="{{ $selectedClass }}">
                    @else
                    <select name="next_class[{{ $enrollment->id }}]" class="next-class-sel"
                            style="border:1px solid #e2e8f0;border-radius:7px;padding:5px 6px;font-size:12px;min-width:90px;">
                        @php $allowedOpts = $allowedNext[$selectedClass] ?? [$selectedClass]; @endphp
                        @foreach($allowedOpts as $opt)
                            <option value="{{ $opt }}" {{ $opt === $enrollment->suggested_next ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                    @endif
                </td>
                <td style="padding:8px 10px;">
                    @if($promoted)
                        <span style="color:#94a3b8;font-size:12px;">—</span>
                    @else
                    @php $classSections = $sections[$selectedClass] ?? []; @endphp
                    @if(count($classSections) === 1)
                        {{-- Auto-fill single section --}}
                        <span style="font-weight:600;color:#0f766e;font-size:13px;">{{ $classSections[0] }}</span>
                        <input type="hidden" name="section[{{ $enrollment->id }}]" value="{{ $classSections[0] }}">
                    @else
                    <select name="section[{{ $enrollment->id }}]"
                            style="border:1px solid #e2e8f0;border-radius:7px;padding:5px 6px;font-size:12px;min-width:65px;">
                        <option value="">Same</option>
                        @foreach($classSections as $sec)
                            <option value="{{ $sec }}" {{ $enrollment->section === $sec ? 'selected' : '' }}>{{ $sec }}</option>
                        @endforeach
                    </select>
                    @endif
                    @endif
                </td>
                <td style="padding:8px 10px;text-align:right;white-space:nowrap;">
                    @if(!$promoted)
                    <select name="target_year_id[{{ $enrollment->id }}]" class="target-year-select"
                            style="border:1px solid #e2e8f0;border-radius:7px;padding:5px 6px;font-size:11px;min-width:90px;">
                        @foreach($targetYears as $ty)
                            <option value="{{ $ty->id }}">{{ $ty->name }}</option>
                        @endforeach
                    </select>
                    @endif
                </td>
            </tr>
            {{-- Expandable marks detail row --}}
            @if($enrollment->subjects_breakdown->isNotEmpty())
            <tr id="marks-row-{{ $enrollment->id }}" style="display:none;">
                <td colspan="10" style="padding:8px 16px 12px 50px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <table style="width:100%;border-collapse:collapse;font-size:11px;">
                        <thead>
                            <tr>
                                <th style="text-align:left;padding:4px 8px;color:#64748b;font-weight:700;">Subject</th>
                                <th style="text-align:center;padding:4px 8px;color:#64748b;font-weight:700;">Max</th>
                                <th style="text-align:center;padding:4px 8px;color:#64748b;font-weight:700;">Theory</th>
                                <th style="text-align:center;padding:4px 8px;color:#64748b;font-weight:700;">Assignment</th>
                                <th style="text-align:center;padding:4px 8px;color:#64748b;font-weight:700;">Total</th>
                                <th style="text-align:center;padding:4px 8px;color:#64748b;font-weight:700;">%</th>
                                <th style="text-align:center;padding:4px 8px;color:#64748b;font-weight:700;">Grade</th>
                                <th style="text-align:center;padding:4px 8px;color:#64748b;font-weight:700;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollment->subjects_breakdown as $sb)
                            <tr style="border-top:1px solid #f1f5f9;">
                                <td style="padding:4px 8px;font-weight:600;color:#0f172a;">{{ $sb['subject'] }}</td>
                                <td style="padding:4px 8px;text-align:center;color:#475569;">{{ $sb['full_marks'] }}</td>
                                <td style="padding:4px 8px;text-align:center;color:#475569;">{{ $sb['theory'] ?? '—' }}</td>
                                <td style="padding:4px 8px;text-align:center;color:#475569;">{{ $sb['assignment'] ?? '—' }}</td>
                                <td style="padding:4px 8px;text-align:center;font-weight:700;">{{ $sb['total'] ?? '—' }}</td>
                                <td style="padding:4px 8px;text-align:center;font-weight:600;color:{{ $sb['status'] === 'pass' ? '#15803d' : ($sb['status'] === 'fail' ? '#b91c1c' : '#475569') }};">
                                    {{ $sb['pct'] !== null ? $sb['pct'].'%' : '—' }}
                                </td>
                                <td style="padding:4px 8px;text-align:center;">{{ $sb['grade'] ?? '—' }}</td>
                                <td style="padding:4px 8px;text-align:center;">
                                    @if($sb['status'] === 'pass')
                                        <span style="color:#15803d;font-weight:600;">PASS</span>
                                    @elseif($sb['status'] === 'fail')
                                        <span style="color:#b91c1c;font-weight:600;">FAIL</span>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
            @endif
            @endforeach
            <tr style="background:#f8fafc;border-top:2px solid #e2e8f0;">
                <td colspan="10" style="padding:10px 14px;font-size:12px;color:#64748b;">
                    <strong>{{ $promotedCount }}</strong> already promoted ·
                    <strong>{{ $eligibleCount }}</strong> eligible ·
                    <strong>{{ $availableCount }}</strong> available to promote
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>

<script>
function toggleAllCheckboxes(master) {
    document.querySelectorAll('.enr-checkbox').forEach(function(cb) {
        cb.checked = master.checked;
    });
}

function selectAllEligible() {
    var count = 0;
    document.querySelectorAll('.enr-checkbox[data-eligible]').forEach(function(cb) {
        cb.checked = true;
        count++;
    });
    if (count > 0) {
        alert('Selected ' + count + ' eligible student(s). Click "Promote Selected" to proceed.');
    } else {
        alert('No eligible students found.');
    }
}

function toggleMarks(id) {
    var row = document.getElementById('marks-row-' + id);
    if (row) {
        row.style.display = row.style.display === 'none' ? '' : 'none';
    }
}

function promoteSelected() {
    var checked = document.querySelectorAll('.enr-checkbox:checked').length;
    if (checked === 0) {
        alert('Select at least one student to promote.');
        return;
    }
    if (!confirm('Promote ' + checked + ' student(s)?')) return;

    // Disable unchecked checkboxes so they aren't submitted
    document.querySelectorAll('.enr-checkbox').forEach(function(cb) {
        if (!cb.checked) cb.disabled = true;
    });

    document.getElementById('bulk-promote-form').submit();
}
</script>
@endif

@endsection
