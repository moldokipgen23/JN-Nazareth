@extends('layouts.teacher')
@section('page-title', 'Marks Sheet')

@push('styles')
<style>
    .mk-row { display:flex; align-items:center; gap:10px; background:#fff; border-radius:10px; padding:10px 12px; margin-bottom:6px; box-shadow:0 1px 3px rgba(15,23,42,.05); }
    .mk-roll { width:32px; height:32px; border-radius:8px; background:#f1f5f9; color:#475569; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12px; flex-shrink:0; }
    .mk-name { font-size:13px; font-weight:600; color:#0f172a; flex:1; min-width:0; }
    .mk-input { width:60px; padding:6px 8px; border:1.5px solid #e2e8f0; border-radius:7px; font-size:13px; font-weight:600; text-align:center; }
    .mk-input:focus { outline:none; border-color:#0f766e; box-shadow:0 0 0 3px rgba(15,118,110,.12); }
    .mk-input-auto { width:60px; padding:6px 8px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13px; font-weight:600; text-align:center; background:#f9fafb; }
    .mk-status { font-size:10px; font-weight:700; padding:3px 7px; border-radius:99px; min-width:38px; text-align:center; }
    .mk-status.pass { background:#dcfce7; color:#15803d; }
    .mk-status.fail { background:#fee2e2; color:#b91c1c; }
    .mk-status.empty { background:#f1f5f9; color:#94a3b8; }
    .mk-grade { font-size:13px; font-weight:800; min-width:30px; text-align:center; }
    @media (max-width:700px) {
        .mk-row { flex-wrap:wrap; }
        .mk-name { flex:1 1 100%; }
        .mk-input-group { flex:1 1 100%; display:flex; gap:6px; align-items:center; margin-top:4px; flex-wrap:wrap; }
    }
    .save-bar { position:sticky; bottom:0; background:linear-gradient(180deg,transparent,#f0f4f8 30%); padding:14px 0 4px; margin-top:12px; }
    @media (max-width:767px) { .save-bar { bottom:60px; } }
</style>
@endpush

@section('content')

<div style="margin-bottom:14px;">
    <a href="{{ route('teacher.marks.index') }}" style="font-size:12px;color:#0f766e;text-decoration:none;font-weight:600;">← Back to subjects</a>
    <h1 style="font-size:18px;font-weight:700;color:#0f172a;margin:4px 0 0;">
        {{ $exam->name }} — {{ $subject }}
    </h1>
    <div style="font-size:12px;color:#64748b;margin-top:2px;">
        {{ $class }} — Section {{ $section }} &middot; {{ $enrollments->count() }} students
    </div>
</div>

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;border-radius:10px;padding:11px 14px;font-size:13px;margin-bottom:12px;">
    {{ $errors->first() }}
</div>
@endif

@if($enrollments->isEmpty())
    <div style="background:#fff;border-radius:14px;padding:48px 24px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="font-size:36px;opacity:.3;margin-bottom:10px;">👥</div>
        <div style="font-weight:600;color:#475569;">No active students in this section.</div>
    </div>
@else
<form method="POST" action="{{ route('teacher.marks.store', ['exam' => $exam->id, 'class' => $class, 'section' => $section, 'subject' => $subject]) }}">
    @csrf

    {{-- Full/pass marks --}}
    <div style="background:#fff;border-radius:10px;padding:12px 14px;margin-bottom:10px;display:flex;gap:14px;flex-wrap:wrap;align-items:end;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Full Marks (Theory)</label>
            <input type="number" name="full_marks" id="fullMarks" value="{{ old('full_marks', $defaultFull) }}" required step="0.01" min="1" max="9999" class="mk-input" style="width:90px;">
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Pass Marks</label>
            <input type="number" name="pass_marks" id="passMarks" value="{{ old('pass_marks', $defaultPass) }}" required step="0.01" min="0" class="mk-input" style="width:90px;">
        </div>
        <div style="flex:1;min-width:140px;font-size:11px;color:#64748b;">
            Enter theory marks and/or assignment marks per student. Total and grade compute automatically.
        </div>
    </div>

    {{-- Column headers --}}
    <div style="display:flex;align-items:center;gap:10px;padding:6px 12px;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">
        <div style="width:32px;flex-shrink:0;"></div>
        <div style="flex:1;min-width:0;">Student</div>
        <div style="width:60px;text-align:center;">Theory</div>
        <div style="width:60px;text-align:center;">Assignment</div>
        <div style="width:60px;text-align:center;">Total</div>
        <div style="width:60px;text-align:center;">Grade</div>
        <div style="width:50px;text-align:center;">Status</div>
    </div>

    @foreach($enrollments as $i => $e)
        @php
            $m            = $existing[$e->id] ?? null;
            $theory       = old("marks.$e->id.theory", $m?->theory_marks);
            $assignment   = old("marks.$e->id.assignment", $m?->assignment_marks);
            $total        = old("marks.$e->id.total", $m?->total_marks);
            $obtained     = old("marks.$e->id.obtained", $m?->obtained_marks);
            $grade        = old("marks.$e->id.grade", $m?->grade ?? $m?->computedGrade());
        @endphp
        <div class="mk-row" data-row="{{ $e->id }}">
            <div class="mk-roll">{{ $e->roll_number ?: ($i + 1) }}</div>
            <div class="mk-name">{{ $e->student?->name ?? 'Unknown' }}</div>
            <div class="mk-input-group" style="display:flex;gap:6px;align-items:center;">
                <input type="number" name="marks[{{ $e->id }}][theory]" value="{{ $theory }}"
                       step="0.01" min="0" placeholder="T"
                       class="mk-input" oninput="recalc(this)">
                <input type="number" name="marks[{{ $e->id }}][assignment]" value="{{ $assignment }}"
                       step="0.01" min="0" placeholder="A"
                       class="mk-input" oninput="recalc(this)">
                <input type="text" name="marks[{{ $e->id }}][total]" value="{{ $total }}" readonly
                       class="mk-input-auto total-display">
                <input type="hidden" name="marks[{{ $e->id }}][grade]" class="grade-input" value="{{ $grade ?? '' }}">
                <span class="mk-grade grade-badge" style="color:{{ $total ? '#0f766e' : '#94a3b8' }};">{{ $grade ?: '—' }}</span>
                <span class="mk-status {{ $total === null || $total === '' ? 'empty' : ((float)$total >= (float)$defaultPass ? 'pass' : 'fail') }}">
                    {{ $total === null || $total === '' ? '—' : ((float)$total >= (float)$defaultPass ? 'PASS' : 'FAIL') }}
                </span>
            </div>
        </div>
    @endforeach

    <div class="save-bar">
        <button type="submit" style="width:100%;padding:14px;background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;border-radius:12px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(13,148,136,.3);">
            Save Marks ({{ $enrollments->count() }} students)
        </button>
    </div>
</form>

<script>
var gradeRanges = @json(\App\Models\GradeScale::active()->orderBy('min_percent')->orderBy('name')->get()->map(fn($g) => ['min' => (float)$g->min_percent, 'max' => (float)$g->max_percent, 'name' => $g->name, 'point' => (float)$g->grade_point])->toArray());

function recalc(input) {
    var row = input.closest('.mk-row');
    var theory = parseFloat(row.querySelector('input[name*="[theory]"]').value) || 0;
    var assignment = parseFloat(row.querySelector('input[name*="[assignment]"]').value) || 0;
    var total = theory + assignment;
    var full = parseFloat(document.getElementById('fullMarks').value) || 0;
    var pass = parseFloat(document.getElementById('passMarks').value) || 0;

    var totalDisplay = row.querySelector('.total-display');
    var gradeInput = row.querySelector('.grade-input');
    var gradeBadge = row.querySelector('.grade-badge');
    var statusEl = row.querySelector('.mk-status');

    totalDisplay.value = total > 0 ? total.toFixed(2) : '';

    // Compute grade from percentage
    var pct = full > 0 ? (total / full) * 100 : 0;
    var grade = '';
    for (var i = gradeRanges.length - 1; i >= 0; i--) {
        var r = gradeRanges[i];
        if (pct >= r.min && pct <= r.max) { grade = r.name; break; }
    }
    gradeInput.value = grade;
    gradeBadge.textContent = grade || '—';
    gradeBadge.style.color = grade ? '#0f766e' : '#94a3b8';

    // Status
    if (total <= 0) { statusEl.className = 'mk-status empty'; statusEl.textContent = '—'; }
    else if (total >= pass) { statusEl.className = 'mk-status pass'; statusEl.textContent = 'PASS'; }
    else { statusEl.className = 'mk-status fail'; statusEl.textContent = 'FAIL'; }

    // Validation highlight
    if (full > 0 && total > full) {
        row.querySelectorAll('.mk-input').forEach(function(el) { el.style.borderColor = '#ef4444'; });
    } else {
        row.querySelectorAll('.mk-input').forEach(function(el) { el.style.borderColor = ''; });
    }
}

document.getElementById('fullMarks').addEventListener('input', function() {
    document.querySelectorAll('.mk-row').forEach(function(row) {
        var inputs = row.querySelectorAll('.mk-input');
        if (inputs.length > 0) recalc(inputs[0]);
    });
});

document.getElementById('passMarks').addEventListener('input', function() {
    document.querySelectorAll('.mk-row').forEach(function(row) {
        var inputs = row.querySelectorAll('.mk-input');
        if (inputs.length > 0) recalc(inputs[0]);
    });
});
</script>
@endif

@endsection
