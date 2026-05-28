@extends('layouts.teacher')
@section('page-title', 'Attendance')

@section('content')

<div style="margin-bottom:14px;">
    <h1 style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">Attendance</h1>
    <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $year ? $year->name : 'No active year' }} — pick a class to mark today's attendance</div>
</div>

@if(!$year)
    <div style="background:#fef3c7;border:1px solid #fcd34d;color:#92400e;border-radius:10px;padding:14px 16px;font-size:13px;">No academic year is set as active. Ask the admin to create/activate one.</div>
@else
    @forelse($rows as $row)
        <a href="{{ route('teacher.attendance.mark',['class'=>$row['class'],'section'=>$row['section']]) }}" style="display:flex;align-items:center;gap:12px;background:#fff;border-radius:12px;padding:14px;margin-bottom:8px;box-shadow:0 1px 3px rgba(15,23,42,.06);text-decoration:none;">
            <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#ccfbf1,#99f6e4);color:#0f766e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">{{ strtoupper(substr($row['class'],0,2)) }}</div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:14px;font-weight:600;color:#0f172a;">{{ $row['class'] }} — Section {{ $row['section'] }}</div>
                <div style="font-size:12px;color:#64748b;margin-top:3px;">
                    {{ $row['students'] }} {{ Str::plural('student',$row['students']) }}
                    @if($row['marked_today']>0)<span class="badge" style="background:#dcfce7;color:#15803d;margin-left:6px;">✓ Submitted ({{ $row['marked_today'] }})</span>@else<span class="badge" style="background:#fef3c7;color:#92400e;margin-left:6px;">Not marked yet</span>@endif
                </div>
            </div>
            <svg width="14" height="14" fill="none" stroke="#cbd5e1" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    @empty
        <div class="card" style="padding:40px 20px;text-align:center;">
            <div style="font-size:14px;font-weight:600;color:#475569;">No class-teacher assignments</div>
            <div style="font-size:12px;color:#94a3b8;margin-top:4px;">Only the assigned class teacher can mark attendance. Ask admin to assign you.</div>
        </div>
    @endforelse
@endif

@endsection