@extends('layouts.teacher')
@section('page-title', 'Mark Attendance')

@push('styles')
<style>
    .att-row { display:flex; align-items:center; gap:12px; background:#fff; border-radius:12px; padding:12px 14px; margin-bottom:8px; box-shadow:0 1px 3px rgba(15,23,42,.06); }
    .att-roll { width:36px; height:36px; border-radius:10px; background:#f1f5f9; color:#475569; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; flex-shrink:0; }
    .att-name { font-size:14px; font-weight:600; color:#0f172a; }
    .att-meta { font-size:11px; color:#94a3b8; margin-top:2px; }
    .att-pills { display:flex; gap:4px; flex-shrink:0; }
    .att-pill { position:relative; }
    .att-pill input { position:absolute; opacity:0; pointer-events:none; }
    .att-pill label {
        display:inline-flex; align-items:center; justify-content:center;
        min-width:38px; padding:6px 10px; border-radius:8px;
        font-size:11px; font-weight:700; cursor:pointer;
        background:#f8fafc; color:#94a3b8; border:1.5px solid transparent;
        transition: all .12s ease; user-select:none;
    }
    .att-pill label:hover { background:#f1f5f9; }
    .att-pill input:checked + label.p { background:#dcfce7; color:#15803d; border-color:#22c55e; }
    .att-pill input:checked + label.a { background:#fee2e2; color:#b91c1c; border-color:#ef4444; }
    .att-pill input:checked + label.l { background:#fef3c7; color:#a16207; border-color:#f59e0b; }
    .att-pill input:checked + label.e { background:#e0e7ff; color:#4338ca; border-color:#6366f1; }

    @media (max-width: 600px) {
        .att-row { flex-wrap:wrap; }
        .att-body { flex:1 1 calc(100% - 50px); min-width:0; }
        .att-pills { flex:1 1 100%; margin-top:8px; justify-content:space-between; }
        .att-pill label { flex:1; }
    }
    .save-bar {
        position:sticky; bottom:0; background:linear-gradient(180deg, transparent, #f0f4f8 30%);
        padding:14px 0 4px; margin-top:12px;
    }
    @media (max-width: 767px) {
        .save-bar { bottom:60px; }
    }
</style>
@endpush

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:14px;flex-wrap:wrap;">
    <div>
        <a href="{{ route('teacher.attendance.index') }}" style="font-size:12px;color:#0f766e;text-decoration:none;font-weight:600;">← Back to classes</a>
        <h1 style="font-size:18px;font-weight:700;color:#0f172a;margin:4px 0 0;">
            {{ $class }} — Section {{ $section }}
        </h1>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $enrollments->count() }} students</div>
    </div>

    <form method="GET" style="display:flex;gap:8px;align-items:center;">
        <label style="font-size:12px;color:#475569;font-weight:600;">Date</label>
        <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
               style="border:1px solid #e2e8f0;border-radius:8px;padding:6px 10px;font-size:13px;background:#fff;">
    </form>
</div>

@if($enrollments->isEmpty())
    <div style="background:#fff;border-radius:14px;padding:48px 24px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="font-size:36px;opacity:.3;margin-bottom:10px;">👥</div>
        <div style="font-weight:600;color:#475569;">No active students in this section.</div>
    </div>
@else
<form method="POST" action="{{ route('teacher.attendance.store', ['class' => $class, 'section' => $section]) }}">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">

    {{-- Quick bulk-mark all --}}
    <div style="background:#fff;border-radius:10px;padding:10px 14px;margin-bottom:12px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <span style="font-size:12px;font-weight:600;color:#475569;">Quick mark all:</span>
        <button type="button" onclick="markAll('present')" style="background:#dcfce7;color:#15803d;border:1px solid #86efac;padding:5px 10px;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;">All Present</button>
        <button type="button" onclick="markAll('absent')" style="background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;padding:5px 10px;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;">All Absent</button>
    </div>

    @foreach($enrollments as $i => $e)
        @php
            $current = $existing[$e->id]->status ?? 'present';
            $remark  = $existing[$e->id]->remarks ?? '';
        @endphp
        <div class="att-row">
            <div class="att-roll">{{ $e->roll_number ?: ($i + 1) }}</div>
            <div class="att-body" style="flex:1;min-width:0;">
                <div class="att-name">{{ $e->student?->name ?? 'Unknown' }}</div>
                <div class="att-meta">
                    @if($e->student?->admission_number)
                        Adm: {{ $e->student->admission_number }}
                    @endif
                    @isset($existing[$e->id])
                        <span style="color:#15803d;">&middot; Saved</span>
                    @endisset
                </div>
            </div>
            <div class="att-pills" data-row="{{ $e->id }}">
                @foreach($statuses as $st)
                    @php
                        $letter = strtoupper(substr($st, 0, 1));
                        $cls    = strtolower(substr($st, 0, 1));
                    @endphp
                    <div class="att-pill">
                        <input type="radio"
                               id="mark-{{ $e->id }}-{{ $st }}"
                               name="marks[{{ $e->id }}][status]"
                               value="{{ $st }}"
                               {{ $current === $st ? 'checked' : '' }}>
                        <label for="mark-{{ $e->id }}-{{ $st }}" class="{{ $cls }}" title="{{ ucfirst($st) }}">{{ $letter }}</label>
                    </div>
                @endforeach
            </div>
            <input type="hidden" name="marks[{{ $e->id }}][remarks]" value="{{ $remark }}">
        </div>
    @endforeach

    <div class="save-bar">
        <button type="submit" style="width:100%;padding:14px;background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;border-radius:12px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(13,148,136,.3);">
            Save Attendance ({{ $enrollments->count() }} students)
        </button>
    </div>
</form>

<script>
function markAll(status) {
    document.querySelectorAll('.att-pills').forEach(group => {
        const id = group.dataset.row;
        const radio = document.getElementById('mark-' + id + '-' + status);
        if (radio) radio.checked = true;
    });
}
</script>
@endif

@endsection
