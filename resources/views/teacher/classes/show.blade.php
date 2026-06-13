@extends('layouts.teacher')
@section('page-title', 'Class — '.$class)

@section('content')

<div style="margin-bottom:14px;">
    <div style="display:flex;align-items:center;gap:10px;">
        <a href="{{ route('teacher.classes') }}" style="color:#64748b;font-size:12px;text-decoration:none;">← My Classes</a>
    </div>
    <h1 style="font-size:18px;font-weight:700;color:#0f172a;margin:6px 0 0;">{{ $class }}</h1>
    <div style="font-size:12px;color:#64748b;margin-top:2px;">
        {{ $year ? $year->name : 'No active year' }} ·
        {{ $students->count() }} {{ Str::plural('student', $students->count()) }} ·
        Section{{ $sections->count() === 1 ? '' : 's' }} {{ $sections->implode(', ') }}
    </div>
</div>

{{-- Subjects I teach in this class --}}
@if($mySubjects->isNotEmpty())
<div style="background:#fff;border-radius:12px;padding:12px 14px;margin-bottom:12px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;">Subjects you teach here</div>
    <div style="display:flex;flex-wrap:wrap;gap:6px;">
        @foreach($mySubjects as $s)
            <span style="font-size:12px;background:#f0fdf4;color:#0f766e;border:1px solid #bbf7d0;padding:4px 10px;border-radius:99px;font-weight:600;">{{ $s }}</span>
        @endforeach
    </div>
</div>
@endif

{{-- Quick actions --}}
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
    @foreach($sections as $sec)
        <a href="{{ route('teacher.attendance.mark', ['class' => $class, 'section' => $sec]) }}"
           style="flex:1;min-width:160px;background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;padding:10px 14px;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;text-align:center;box-shadow:0 2px 8px rgba(13,148,136,.25);">
            Mark Attendance — Sec {{ $sec }}
        </a>
    @endforeach
    <a href="{{ route('teacher.marks.index') }}"
       style="flex:1;min-width:160px;background:#fff;color:#0f766e;border:2px solid #0f766e;padding:9px 14px;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;text-align:center;">
        Marks Entry
    </a>
</div>

{{-- Student roster --}}
<div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="padding:12px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:13px;font-weight:700;color:#0f172a;">Students</span>
        <span style="font-size:11px;color:#94a3b8;">Read-only · contact admin for changes</span>
    </div>

    @if($students->isEmpty())
        <div style="padding:30px 16px;text-align:center;color:#94a3b8;font-size:13px;">No active students enrolled in this class.</div>
    @else
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="text-align:left;padding:8px 14px;font-size:10px;color:#64748b;font-weight:700;text-transform:uppercase;">Roll</th>
                    <th style="text-align:left;padding:8px 14px;font-size:10px;color:#64748b;font-weight:700;text-transform:uppercase;">Student</th>
                    @if($sections->count() > 1)
                        <th style="text-align:left;padding:8px 14px;font-size:10px;color:#64748b;font-weight:700;text-transform:uppercase;">Sec</th>
                    @endif
                    <th style="text-align:left;padding:8px 14px;font-size:10px;color:#64748b;font-weight:700;text-transform:uppercase;">Parent contact</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $e)
                    <tr style="border-top:1px solid #f1f5f9;">
                        <td style="padding:8px 14px;color:#475569;">{{ $e->roll_number ?: '—' }}</td>
                        <td style="padding:8px 14px;color:#0f172a;font-weight:600;">{{ $e->student?->name ?? '—' }}</td>
                        @if($sections->count() > 1)
                            <td style="padding:8px 14px;color:#64748b;">{{ $e->section }}</td>
                        @endif
                        <td style="padding:8px 14px;color:#64748b;font-size:12px;">
                            {{ $e->student?->parent_phone ?? '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection
