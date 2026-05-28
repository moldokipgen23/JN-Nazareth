@extends('layouts.teacher')
@section('page-title', 'Dashboard')

@section('content')

{{-- Stat cards — same style as admin --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:14px; margin-bottom:24px;">

    <div class="stat-card" style="background:#fff;">
        <div class="stat-icon" style="background:linear-gradient(135deg,#ccfbf1,#99f6e4);">
            <svg fill="none" stroke="#0f766e" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.42A12 12 0 0112 21a12 12 0 01-6.16-10.42L12 14z"/></svg>
        </div>
        <div>
            <div style="font-size:26px;font-weight:700;color:#0f172a;line-height:1;">{{ $classes->count() }}</div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">Classes</div>
        </div>
    </div>

    <div class="stat-card" style="background:#fff;">
        <div class="stat-icon" style="background:linear-gradient(135deg,#fef3c7,#fde68a);">
            <svg fill="none" stroke="#92400e" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13M5.5 4.5h4.5a3 3 0 013 3v11.5a2.5 2.5 0 00-2.5-2.5H5.5v-12zM18.5 4.5h-4.5a3 3 0 00-3 3v11.5a2.5 2.5 0 012.5-2.5h5v-12z"/></svg>
        </div>
        <div>
            <div style="font-size:26px;font-weight:700;color:#0f172a;line-height:1;">{{ $subjects->count() }}</div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">Subjects</div>
        </div>
    </div>

    <div class="stat-card" style="background:#fff;">
        <div class="stat-icon" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);">
            <svg fill="none" stroke="#1d4ed8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-2-3.46"/></svg>
        </div>
        <div>
            <div style="font-size:26px;font-weight:700;color:#0f172a;line-height:1;">{{ $studentCount }}</div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">Students</div>
        </div>
    </div>

    @if($year)
    <div class="stat-card" style="background:linear-gradient(135deg,#0f766e,#0d9488); color:#fff;">
        <div class="stat-icon" style="background:rgba(255,255,255,.15);">
            <svg fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <div style="font-size:15px;font-weight:700;line-height:1.2;">{{ $year->name }}</div>
            <div style="font-size:11px;opacity:.85;margin-top:4px;">Academic Year</div>
        </div>
    </div>
    @endif

</div>

{{-- My Classes --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
    <h2 style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">My Classes</h2>
    <a href="{{ route('teacher.classes') }}" style="font-size:12px;font-weight:600;color:#0f766e;text-decoration:none;">View all →</a>
</div>

@forelse($classes->take(5) as $row)
    @php
        $section = $row['section'] ? ' — Sec '.$row['section'] : '';
        $href    = route('admin.classes.show', ['class' => $row['class']]);
    @endphp
    <a href="{{ $href }}" style="display:flex;align-items:center;gap:14px;background:#fff;border-radius:12px;padding:14px 16px;margin-bottom:8px;box-shadow:0 1px 3px rgba(15,23,42,.06);text-decoration:none;transition:box-shadow .15s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(15,23,42,.1)'" onmouseout="this.style.boxShadow='0 1px 3px rgba(15,23,42,.06)'">
        <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,#ccfbf1,#99f6e4);color:#0f766e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">
            {{ strtoupper(substr($row['class'], 0, 2)) }}
        </div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:14px;font-weight:600;color:#0f172a;">
                {{ $row['class'] }}{{ $section }}
                @if($row['is_class_teacher'])
                    <span class="badge" style="background:#ecfdf5;color:#047857;margin-left:6px;">Class Teacher</span>
                @endif
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:2px;">
                {{ $row['students'] }} {{ Str::plural('student', $row['students']) }}
                @if(count($row['subjects']))
                    &middot; {{ implode(', ', $row['subjects']) }}
                @endif
            </div>
        </div>
        <svg width="16" height="16" fill="none" stroke="#cbd5e1" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    </a>
@empty
    <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="font-size:32px;opacity:.3;margin-bottom:8px;">📋</div>
        <div style="font-weight:600;color:#475569;">No classes assigned yet</div>
        <div style="font-size:12px;color:#94a3b8;margin-top:4px;">Ask the admin to assign you under Teacher Assignments.</div>
    </div>
@endforelse

{{-- My Subjects --}}
@if($subjects->isNotEmpty())
<div style="display:flex;align-items:center;justify-content:space-between;margin:22px 0 12px;">
    <h2 style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">My Subjects</h2>
    <a href="{{ route('teacher.subjects') }}" style="font-size:12px;font-weight:600;color:#0f766e;text-decoration:none;">View all →</a>
</div>

@foreach($subjects->take(5) as $s)
    <div style="display:flex;align-items:center;gap:14px;background:#fff;border-radius:12px;padding:14px 16px;margin-bottom:8px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,#fef3c7,#fde68a);color:#92400e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">
            {{ strtoupper(substr($s->subject, 0, 2)) }}
        </div>
        <div style="flex:1;">
            <div style="font-size:14px;font-weight:600;color:#0f172a;">{{ $s->subject }}</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $s->class }}{{ $s->section ? ' — Sec '.$s->section : '' }}</div>
        </div>
        @if($activeExams->isNotEmpty())
        <a href="{{ route('teacher.questions.index') }}?subject={{ urlencode($s->subject) }}&class={{ urlencode($s->class) }}&section={{ urlencode($s->section ?? '') }}"
           style="background:#f0fdfa;color:#0f766e;padding:6px 12px;border-radius:8px;font-size:11px;font-weight:600;text-decoration:none;white-space:nowrap;">
            Submit Q
        </a>
        @endif
    </div>
@endforeach
@endif

@endsection
