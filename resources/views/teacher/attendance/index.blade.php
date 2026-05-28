@extends('layouts.teacher')
@section('page-title', 'Attendance')

@section('content')

<div style="margin-bottom:16px;">
    <h1 style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">Attendance</h1>
    <div style="font-size:12px;color:#64748b;margin-top:2px;">
        {{ $year ? $year->name : 'No active year' }} &middot; pick a class to mark today's attendance
    </div>
</div>

@if(!$year)
    <div style="background:#fef3c7;border:1px solid #fcd34d;color:#92400e;border-radius:10px;padding:14px 16px;font-size:13px;">
        No academic year is set as active. Ask the admin to create/activate one.
    </div>
@else
    @forelse($rows as $row)
        @php $today = now()->toDateString(); @endphp
        <a href="{{ route('teacher.attendance.mark', ['class' => $row['class'], 'section' => $row['section'], 'date' => $today]) }}"
           style="display:flex;align-items:center;gap:14px;background:#fff;border-radius:12px;padding:16px;margin-bottom:10px;box-shadow:0 1px 3px rgba(15,23,42,.06);text-decoration:none;transition:box-shadow .15s;"
           onmouseover="this.style.boxShadow='0 4px 16px rgba(15,23,42,.1)'" onmouseout="this.style.boxShadow='0 1px 3px rgba(15,23,42,.06)'">
            <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#ccfbf1,#99f6e4);color:#0f766e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">
                {{ strtoupper(substr($row['class'], 0, 2)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:15px;font-weight:600;color:#0f172a;">
                    {{ $row['class'] }} — Section {{ $row['section'] }}
                </div>
                <div style="font-size:12px;color:#64748b;margin-top:4px;">
                    {{ $row['students'] }} {{ Str::plural('student', $row['students']) }}
                    @if($row['marked_today'] > 0)
                        <span class="badge" style="background:#ecfdf5;color:#047857;margin-left:6px;">
                            {{ $row['marked_today'] }} marked today
                        </span>
                    @else
                        <span class="badge" style="background:#fef3c7;color:#92400e;margin-left:6px;">
                            Not marked yet
                        </span>
                    @endif
                </div>
            </div>
            <svg width="16" height="16" fill="none" stroke="#cbd5e1" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    @empty
        <div style="background:#fff;border-radius:14px;padding:48px 24px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
            <div style="font-size:36px;opacity:.3;margin-bottom:10px;">📋</div>
            <div style="font-weight:600;color:#475569;font-size:15px;">No class-teacher assignments</div>
            <div style="font-size:13px;color:#94a3b8;margin-top:6px;">Only the assigned class teacher can mark attendance. Ask the admin to assign you under <em>Teacher Assignments</em>.</div>
        </div>
    @endforelse
@endif

@endsection
