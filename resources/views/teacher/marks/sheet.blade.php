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

@php
    $isSubmitted = $existing->first()?->submitted_at !== null;
    $allSubmitted = $existing->isNotEmpty() && $existing->every(fn($m) => $m->submitted_at !== null);
@endphp

@if($enrollments->isEmpty())
    <div style="background:#fff;border-radius:14px;padding:48px 24px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="font-size:36px;opacity:.3;margin-bottom:10px;">👥</div>
        <div style="font-weight:600;color:#475569;">No active students in this section.</div>
    </div>
@elseif($allSubmitted && !auth()->user()->isAdmin())
    {{-- Read-only view for submitted marks --}}
    <div style="background:#fef3c7;border:1px solid #fcd34d;color:#92400e;border-radius:10px;padding:11px 14px;font-size:13px;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        Marks have been submitted and locked. Contact admin to make changes.
    </div>

    <div style="background:#fff;border-radius:10px;padding:12px 14px;margin-bottom:10px;display:flex;gap:14px;flex-wrap:wrap;align-items:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div><span style="font-size:11px;font-weight:600;color:#64748b;">Full Marks:</span> <strong>{{ $defaultFull }}</strong></div>
        <div><span style="font-size:11px;font-weight:600;color:#64748b;">Pass Marks:</span> <strong>{{ $defaultPass }}</strong></div>
        <div style="margin-left:auto;"><span style="font-size:11px;color:#64748b;">Submitted {{ $existing->first()?->submitted_at?->diffForHumans() }}</span></div>
    </div>

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
        @php $m = $existing[$e->id] ?? null; @endphp
        <div class="mk-row" style="opacity:.85;">
            <div class="mk-roll">{{ $e->roll_number ?: ($i + 1) }}</div>
            <div class="mk-name">{{ $e->student?->name ?? 'Unknown' }}</div>
            <div style="width:60px;text-align:center;font-size:13px;font-weight:600;">{{ $m?->theory_marks ?? '—' }}</div>
            <div style="width:60px;text-align:center;font-size:13px;font-weight:600;">{{ $m?->assignment_marks ?? '—' }}</div>
            <div style="width:60px;text-align:center;font-size:13px;font-weight:700;">{{ $m?->total_marks ?? '—' }}</div>
            <div style="width:60px;text-align:center;font-size:13px;font-weight:800;color:#0f766e;">{{ $m?->grade ?? '—' }}</div>
            <div style="width:50px;text-align:center;">
                @php $s = $m?->status() ?? 'ungraded'; @endphp
                <span class="mk-status {{ $s }}">{{ $s === 'ungraded' ? '—' : strtoupper($s) }}</span>
            </div>
        </div>
    @endforeach
@else
<form method="POST" action="{{ route('teacher.marks.store', ['exam' => $exam->id, 'class' => $class, 'section' => $section, 'subject' => $subject]) }}">
    @csrf

    @if(!($marksConfigured ?? true))
    <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:12px 14px;margin-bottom:10px;color:#92400e;font-size:12px;font-weight:600;">
        ⚠️ Admin has not set the marks config for this exam yet. Using fallback values ({{ $defaultFull }} / {{ $defaultPass }}). Please ask admin to set Marks Config before submitting to avoid wrong calculations.
    </div>
    @endif

    {{-- Full/pass marks (set by admin — read-only here) --}}
    <div style="background:#fff;border-radius:10px;padding:12px 14px;margin-bottom:10px;display:flex;gap:14px;flex-wrap:wrap;align-items:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="background:#eef2ff;color:#4338ca;padding:6px 12px;border-radius:8px;font-size:12px;font-weight:700;">
            Full Marks: {{ $defaultFull }}
        </div>
        <div style="background:#eef2ff;color:#4338ca;padding:6px 12px;border-radius:8px;font-size:12px;font-weight:700;">
            Pass Marks: {{ $defaultPass }}
        </div>
        <input type="hidden" id="fullMarks" value="{{ $defaultFull }}">
        <input type="hidden" id="passMarks" value="{{ $defaultPass }}">
        <div style="flex:1;min-width:140px;font-size:11px;color:#64748b;">
            Marks config is set by admin. Enter each student's theory and/or assignment marks — total and grade compute automatically.
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
            <div class="mk-name" style="cursor:pointer;" onclick="showStd({{ $e->student?->id ?? 'null' }}, '{{ addslashes($e->student?->name ?? 'Unknown') }}', '{{ addslashes($e->student?->father_name ?? '') }}', '{{ addslashes($e->student?->mother_name ?? '') }}', '{{ $e->student?->parent_phone ?? '' }}', '{{ addslashes($e->student?->address ?? '') }}')">{{ $e->student?->name ?? 'Unknown' }}</div>
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
        <div style="display:flex;gap:10px;">
            <button type="submit" name="action" value="draft" style="flex:1;padding:14px;background:#fff;color:#0f766e;border:2px solid #0f766e;border-radius:12px;font-size:14px;font-weight:700;cursor:pointer;">
                Save as Draft
            </button>
            <button type="submit" name="action" value="submit" style="flex:1;padding:14px;background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;border-radius:12px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(13,148,136,.3);"
                    onclick="return confirm('Submit marks for all students? This will lock further editing.')">
                Submit &amp; Lock
            </button>
        </div>
    </div>
</form>
@endif

<div id="studentModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:100;align-items:center;justify-content:center;" onclick="if(event.target===this)closeStd()">
    <div style="background:#fff;border-radius:16px;max-width:400px;width:90%;padding:24px;box-shadow:0 20px 60px rgba(0,0,0,.3);position:relative;">
        <button onclick="closeStd()" style="position:absolute;top:12px;right:12px;background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8;">✕</button>
        <div id="studentModalContent"></div>
    </div>
</div>

@php
    $gradeRangesJs = \App\Models\GradeScale::active()
        ->orderBy('min_percent')->orderBy('name')->get()
        ->map(fn($g) => [
            'min' => (float) $g->min_percent,
            'max' => (float) $g->max_percent,
            'name' => $g->name,
            'point' => (float) $g->grade_point,
        ])->toArray();
@endphp
<script>
var gradeRanges = {!! json_encode($gradeRangesJs) !!};

function showStd(id, name, father, mother, phone, address) {
    if (!id) return;
    document.getElementById('studentModalContent').innerHTML = `
        <div style="text-align:center;margin-bottom:16px;">
            <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#0f766e,#14b8a6);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;font-size:22px;font-weight:800;color:#fff;">${name[0]}</div>
            <div style="font-size:16px;font-weight:700;color:#0f172a;">${name}</div>
        </div>
        <div style="display:grid;gap:10px;">
            ${father ? `<div style="display:flex;justify-content:space-between;padding:8px 12px;background:#f8fafc;border-radius:8px;"><span style="color:#64748b;font-size:12px;">Father</span><span style="font-weight:600;font-size:13px;color:#0f172a;">${father}</span></div>` : ''}
            ${mother ? `<div style="display:flex;justify-content:space-between;padding:8px 12px;background:#f8fafc;border-radius:8px;"><span style="color:#64748b;font-size:12px;">Mother</span><span style="font-weight:600;font-size:13px;color:#0f172a;">${mother}</span></div>` : ''}
            ${phone ? `<div style="display:flex;justify-content:space-between;padding:8px 12px;background:#f8fafc;border-radius:8px;"><span style="color:#64748b;font-size:12px;">Parent Phone</span><span style="font-weight:600;font-size:13px;color:#0f172a;">${phone}</span></div>` : ''}
            ${address ? `<div style="display:flex;justify-content:space-between;padding:8px 12px;background:#f8fafc;border-radius:8px;"><span style="color:#64748b;font-size:12px;">Address</span><span style="font-weight:600;font-size:13px;color:#0f172a;">${address}</span></div>` : ''}
        </div>
    `;
    document.getElementById('studentModal').style.display = 'flex';
}
function closeStd() {
    document.getElementById('studentModal').style.display = 'none';
}

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

var _fm = document.getElementById('fullMarks');
var _pm = document.getElementById('passMarks');
if (_fm) _fm.addEventListener('input', function() {
    document.querySelectorAll('.mk-row').forEach(function(row) {
        var inputs = row.querySelectorAll('.mk-input');
        if (inputs.length > 0) recalc(inputs[0]);
    });
});
if (_pm) _pm.addEventListener('input', function() {
    document.querySelectorAll('.mk-row').forEach(function(row) {
        var inputs = row.querySelectorAll('.mk-input');
        if (inputs.length > 0) recalc(inputs[0]);
    });
});
</script>

@endsection
