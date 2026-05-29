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
<form method="POST" action="{{ route('admin.exams.marks-config.save', $exam) }}">
    @csrf

    @php $i = 0; @endphp
    @foreach($rows as $class => $classRows)
    <div style="background:#fff;border-radius:12px;padding:16px 18px;margin-bottom:12px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
            <div style="font-size:15px;font-weight:700;color:#0f172a;">{{ $class }}</div>
            <span style="font-size:11px;color:#94a3b8;">{{ $classRows->count() }} subject{{ $classRows->count() === 1 ? '' : 's' }}</span>
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
                               style="width:100%;padding:6px 10px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px;">
                    </td>
                    <td style="padding:8px 10px;">
                        <input type="number" step="0.01" min="0" max="9999" required
                               name="config[{{ $i }}][pass_marks]" value="{{ $pass }}"
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

@endsection
