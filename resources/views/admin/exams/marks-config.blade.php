@extends('layouts.admin')
@section('page-title', 'Exam Marks Config')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <div>
        <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">Marks Config — {{ $exam->name }}</h1>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">
            Set full marks and pass marks per subject for this exam. Teachers will only see student marks entry — they cannot change these values.
        </div>
    </div>
    <a href="{{ route('admin.exams.index') }}" style="background:#f1f5f9;color:#475569;font-size:12px;font-weight:600;padding:8px 14px;border-radius:8px;text-decoration:none;">← Back to Exams</a>
</div>

@if(session('success'))
<div style="background:#dcfce7;color:#15803d;border-radius:10px;padding:10px 16px;margin-bottom:14px;font-size:13px;font-weight:600;">{{ session('success') }}</div>
@endif

@if($rows->isEmpty())
<div style="background:#fef3c7;border:1px solid #fde68a;border-radius:12px;padding:36px 20px;text-align:center;">
    <div style="font-weight:700;color:#92400e;font-size:14px;">No subjects mapped for any class yet.</div>
    <div style="font-size:12px;color:#92400e;margin-top:6px;">
        Go to <a href="{{ route('admin.class-subjects.index') }}" style="color:#0f766e;font-weight:600;">Subjects → Class Subjects</a> and set up which subjects each class teaches.
    </div>
</div>
@else

{{-- Bulk fill --}}
<div style="background:linear-gradient(135deg,#eef2ff,#fef3c7);border-radius:12px;padding:14px 18px;margin-bottom:14px;display:flex;gap:12px;flex-wrap:wrap;align-items:end;border:1px solid #e0e7ff;">
    <div style="flex:1;min-width:180px;">
        <div style="font-size:14px;font-weight:700;color:#4338ca;margin-bottom:2px;">⚡ Bulk Fill</div>
        <div style="font-size:11px;color:#64748b;">Set every subject across all classes to the same Full/Pass marks in one click. You can still tweak individual rows below.</div>
    </div>
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Full Marks</label>
        <input type="number" id="bulkFull" value="100" step="0.01" min="1" max="9999" style="width:90px;padding:6px 10px;border:1px solid #c7d2fe;border-radius:6px;font-size:13px;">
    </div>
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Pass Marks</label>
        <input type="number" id="bulkPass" value="33" step="0.01" min="0" max="9999" style="width:90px;padding:6px 10px;border:1px solid #c7d2fe;border-radius:6px;font-size:13px;">
    </div>
    <button type="button" onclick="applyBulk()" style="background:#4338ca;color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
        Apply to all
    </button>
</div>

<form method="POST" action="{{ route('admin.exams.marks-config.save', $exam) }}">
    @csrf

    @php $i = 0; @endphp
    @foreach($rows as $class => $classRows)
    <div style="background:#fff;border-radius:12px;padding:16px 18px;margin-bottom:12px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:10px;">
            <div style="font-size:15px;font-weight:700;color:#0f172a;">{{ $class }} <span style="font-size:11px;color:#94a3b8;font-weight:400;">({{ $classRows->count() }} subject{{ $classRows->count() === 1 ? '' : 's' }})</span></div>
            <div style="display:flex;gap:6px;align-items:center;">
                <input type="number" id="bulkFull-{{ $loop->index }}" placeholder="FM" step="0.01" min="1" max="9999" style="width:64px;padding:4px 8px;border:1px solid #e2e8f0;border-radius:6px;font-size:12px;">
                <input type="number" id="bulkPass-{{ $loop->index }}" placeholder="PM" step="0.01" min="0" max="9999" style="width:64px;padding:4px 8px;border:1px solid #e2e8f0;border-radius:6px;font-size:12px;">
                <button type="button" onclick="applyBulkClass({{ $loop->index }}, '{{ $class }}')" style="background:#0f766e;color:#fff;border:none;padding:5px 12px;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;">
                    Apply to {{ $class }}
                </button>
            </div>
        </div>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="text-align:left;padding:8px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Subject</th>
                    <th style="text-align:left;padding:8px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;width:140px;">Full Marks</th>
                    <th style="text-align:left;padding:8px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;width:140px;">Pass Marks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($classRows as $cs)
                @php
                    $subjName = $cs->subject?->name;
                    if (!$subjName) continue;
                    $key = $class.'|'.$subjName;
                    $row = $existing->get($key);
                    $full = $row?->full_marks ?? $cs->full_marks ?? 100;
                    $pass = $row?->pass_marks ?? $cs->pass_marks ?? 33;
                @endphp
                <tr style="border-top:1px solid #f1f5f9;">
                    <td style="padding:8px 10px;font-weight:600;color:#0f172a;">
                        {{ $subjName }}
                        <input type="hidden" name="config[{{ $i }}][class]" value="{{ $class }}">
                        <input type="hidden" name="config[{{ $i }}][subject]" value="{{ $subjName }}">
                    </td>
                    <td style="padding:8px 10px;">
                        <input type="number" step="0.01" min="1" max="9999" required
                               name="config[{{ $i }}][full_marks]" value="{{ $full }}"
                               data-class="{{ $class }}" data-field="full"
                               class="mc-input mc-full"
                               style="width:100%;padding:6px 10px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px;">
                    </td>
                    <td style="padding:8px 10px;">
                        <input type="number" step="0.01" min="0" max="9999" required
                               name="config[{{ $i }}][pass_marks]" value="{{ $pass }}"
                               data-class="{{ $class }}" data-field="pass"
                               class="mc-input mc-pass"
                               style="width:100%;padding:6px 10px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px;">
                    </td>
                </tr>
                @php $i++; @endphp
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach

    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:12px;">
        <a href="{{ route('admin.exams.index') }}" style="background:#f1f5f9;color:#475569;font-size:13px;font-weight:600;padding:10px 20px;border-radius:9px;text-decoration:none;">Cancel</a>
        <button type="submit" style="background:linear-gradient(135deg,#0f766e,#14b8a6);color:#fff;font-size:13px;font-weight:700;padding:10px 24px;border-radius:9px;border:none;cursor:pointer;">
            Save Marks Config
        </button>
    </div>
</form>
@endif

<script>
function applyBulk() {
    const full = document.getElementById('bulkFull').value;
    const pass = document.getElementById('bulkPass').value;
    if (full === '' || pass === '') {
        alert('Enter Full and Pass marks first.');
        return;
    }
    document.querySelectorAll('.mc-full').forEach(i => i.value = full);
    document.querySelectorAll('.mc-pass').forEach(i => i.value = pass);
}
function applyBulkClass(idx, className) {
    const full = document.getElementById('bulkFull-' + idx).value;
    const pass = document.getElementById('bulkPass-' + idx).value;
    if (full === '' && pass === '') {
        alert('Enter at least one value.');
        return;
    }
    document.querySelectorAll('.mc-full[data-class="' + className + '"]').forEach(i => {
        if (full !== '') i.value = full;
    });
    document.querySelectorAll('.mc-pass[data-class="' + className + '"]').forEach(i => {
        if (pass !== '') i.value = pass;
    });
}
</script>

@endsection
