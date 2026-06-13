@extends('layouts.teacher')
@section('page-title', 'Dashboard')

@section('content')

{{-- Quick action cards (mobile-friendly, one row) --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:18px;">
    <a href="{{ route('teacher.attendance.index') }}" style="display:flex;flex-direction:column;align-items:center;gap:6px;background:linear-gradient(135deg,#0f766e,#0d9488);border-radius:14px;padding:16px 8px;text-decoration:none;text-align:center;min-height:80px;justify-content:center;">
        <svg width="26" height="26" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <span style="font-size:11px;font-weight:700;color:#fff;">Attendance</span>
    </a>
    <a href="{{ route('teacher.marks.index') }}" style="display:flex;flex-direction:column;align-items:center;gap:6px;background:linear-gradient(135deg,#0369a1,#0284c7);border-radius:14px;padding:16px 8px;text-decoration:none;text-align:center;min-height:80px;justify-content:center;">
        <svg width="26" height="26" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
        <span style="font-size:11px;font-weight:700;color:#fff;">Marks</span>
    </a>
    <a href="{{ route('teacher.classes') }}" style="display:flex;flex-direction:column;align-items:center;gap:6px;background:linear-gradient(135deg,#7c3aed,#8b5cf6);border-radius:14px;padding:16px 8px;text-decoration:none;text-align:center;min-height:80px;justify-content:center;">
        <svg width="26" height="26" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.42A12 12 0 0112 21a12 12 0 01-6.16-10.42L12 14z"/></svg>
        <span style="font-size:11px;font-weight:700;color:#fff;">Classes</span>
    </a>
</div>

{{-- Welcome card --}}
<div class="card" style="margin-bottom:14px;">
    <div style="padding:16px;display:flex;align-items:center;gap:14px;">
        <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#0f766e,#14b8a6);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:#fff;flex-shrink:0;">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
        <div style="min-width:0;flex:1;">
            <div style="font-size:16px;font-weight:700;color:#0f172a;">{{ \App\Helpers\Settings::get('school_name','School') }}</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $year ? $year->name : 'No active year' }} &middot; {{ $classes->count() }} {{ Str::plural('class',$classes->count()) }}</div>
        </div>
        @if($year)
        <div style="background:#f0fdf4;padding:6px 12px;border-radius:8px;text-align:center;flex-shrink:0;">
            <div style="font-size:16px;font-weight:800;color:#15803d;">{{ $studentCount }}</div>
            <div style="font-size:9px;font-weight:700;color:#15803d;text-transform:uppercase;">Students</div>
        </div>
        @endif
    </div>
</div>

{{-- Stat cards --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:18px;">
    <div class="stat-card" style="padding:12px;flex-direction:column;gap:6px;text-align:center;">
        <div style="font-size:22px;font-weight:800;color:#0f766e;line-height:1;">{{ $classes->count() }}</div>
        <div style="font-size:11px;color:#64748b;font-weight:600;">Classes</div>
    </div>
    <div class="stat-card" style="padding:12px;flex-direction:column;gap:6px;text-align:center;">
        <div style="font-size:22px;font-weight:800;color:#92400e;line-height:1;">{{ $subjects->count() }}</div>
        <div style="font-size:11px;color:#64748b;font-weight:600;">Subjects</div>
    </div>
    <div class="stat-card" style="padding:12px;flex-direction:column;gap:6px;text-align:center;">
        <div style="font-size:22px;font-weight:800;color:#1d4ed8;line-height:1;">{{ $studentCount }}</div>
        <div style="font-size:11px;color:#64748b;font-weight:600;">Students</div>
    </div>
</div>

{{-- My Classes --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
    <h2 style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">My Classes</h2>
    @if($classes->count()>5)<a href="{{ route('teacher.classes') }}" style="font-size:12px;font-weight:600;color:#0f766e;text-decoration:none;">View all</a>@endif
</div>

@forelse($classes->take(5) as $row)
    @php
        $section = $row['section'] ? ' — Sec '.$row['section'] : '';
        $href    = route('teacher.classes.show', ['class' => $row['class']]);
    @endphp
    <a href="{{ $href }}" style="display:flex;align-items:center;gap:12px;background:#fff;border-radius:12px;padding:13px 14px;margin-bottom:8px;box-shadow:0 1px 3px rgba(15,23,42,.06);text-decoration:none;">
        <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,#ccfbf1,#99f6e4);color:#0f766e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">{{ strtoupper(substr($row['class'],0,2)) }}</div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:14px;font-weight:600;color:#0f172a;display:flex;align-items:center;gap:6px;">
                {{ $row['class'] }}{{ $section }}
                @if($row['is_class_teacher'])<span class="badge" style="background:#ecfdf5;color:#047857;">CT</span>@endif
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:2px;">
                {{ $row['students'] }} {{ Str::plural('student',$row['students']) }}
                @if(count($row['subjects']))&middot; {{ implode(', ',array_slice($row['subjects'],0,2)) }}@if(count($row['subjects'])>2)+{{ count($row['subjects'])-2 }}@endif @endif
            </div>
        </div>
        <svg width="14" height="14" fill="none" stroke="#cbd5e1" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    </a>
@empty
    <div class="card" style="padding:32px 20px;text-align:center;">
        <div style="font-size:14px;font-weight:600;color:#475569;">No classes assigned yet</div>
        <div style="font-size:12px;color:#94a3b8;margin-top:4px;">Ask the admin to assign you.</div>
    </div>
@endforelse

{{-- My Subjects --}}
@if($subjects->isNotEmpty())
<div style="display:flex;align-items:center;justify-content:space-between;margin:18px 0 10px;">
    <h2 style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">My Subjects</h2>
    @if($subjects->count()>5)<a href="{{ route('teacher.subjects') }}" style="font-size:12px;font-weight:600;color:#0f766e;text-decoration:none;">View all</a>@endif
</div>

@foreach($subjects->take(5) as $s)
    <div style="display:flex;align-items:center;gap:12px;background:#fff;border-radius:12px;padding:13px 14px;margin-bottom:8px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,#fef3c7,#fde68a);color:#92400e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">{{ strtoupper(substr($s->subject,0,2)) }}</div>
        <div style="flex:1;">
            <div style="font-size:14px;font-weight:600;color:#0f172a;">{{ $s->subject }}</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $s->class }}{{ $s->section?' — Sec '.$s->section:'' }}</div>
        </div>
        @if($activeExams->isNotEmpty())
        <a href="{{ route('teacher.questions.index') }}?subject={{ urlencode($s->subject) }}&class={{ urlencode($s->class) }}&section={{ urlencode($s->section??'') }}" style="background:#f0fdfa;color:#0f766e;padding:6px 12px;border-radius:8px;font-size:11px;font-weight:600;text-decoration:none;white-space:nowrap;">Submit Q</a>
        @endif
    </div>
@endforeach
@endif

{{-- Quick links for pending items --}}
@php
    $pendingCount = \App\Models\ExamQuestion::forActiveYear()->pending()->count() + \App\Models\NoteAssignment::forActiveYear()->pending()->count();
@endphp
@if($pendingCount>0)
<div style="margin-top:18px;">
    <a href="{{ route('teacher.questions.index') }}" style="display:flex;align-items:center;gap:10px;background:#fff8f0;border:1px solid #fde68a;border-radius:12px;padding:12px 14px;text-decoration:none;">
        <div style="width:36px;height:36px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;font-size:16px;">⏳</div>
        <div style="flex:1;">
            <div style="font-size:13px;font-weight:600;color:#92400e;">{{ $pendingCount }} pending review{{ $pendingCount!==1?'s':'' }}</div>
            <div style="font-size:11px;color:#a16207;">Questions and notes awaiting admin approval</div>
        </div>
        <svg width="14" height="14" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    </a>
</div>
@endif

@endsection