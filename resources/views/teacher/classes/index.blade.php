@extends('layouts.teacher')
@section('page-title', 'My Classes')

@section('content')

<div style="margin-bottom:14px;">
    <h1 style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">My Classes</h1>
    <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $year ? $year->name : 'No active year' }} &middot; {{ $classes->count() }} {{ Str::plural('class',$classes->count()) }}</div>
</div>

@forelse($classes as $row)
    @php
        $section = $row['section'] ? ' — Section '.$row['section'] : '';
        $href    = route('teacher.classes.show', ['class' => $row['class']]);
    @endphp
    <a href="{{ $href }}" style="display:flex;align-items:center;gap:12px;background:#fff;border-radius:12px;padding:14px;margin-bottom:8px;box-shadow:0 1px 3px rgba(15,23,42,.06);text-decoration:none;">
        <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#ccfbf1,#99f6e4);color:#0f766e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">{{ strtoupper(substr($row['class'],0,2)) }}</div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:14px;font-weight:600;color:#0f172a;display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                {{ $row['class'] }}{{ $section }}
                @if($row['is_class_teacher'])<span class="badge" style="background:#ecfdf5;color:#047857;">Class Teacher</span>@endif
                @if(!empty($row['legacy']))<span class="badge" style="background:#f1f5f9;color:#64748b;">Legacy</span>@endif
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:3px;">
                <span>{{ $row['students'] }} {{ Str::plural('student',$row['students']) }}</span>
                @if(count($row['subjects']))&middot; Teaching: {{ implode(', ',array_slice($row['subjects'],0,3)) }}@if(count($row['subjects'])>3)+{{ count($row['subjects'])-3 }}@endif @endif
            </div>
        </div>
        <svg width="14" height="14" fill="none" stroke="#cbd5e1" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    </a>
@empty
    <div class="card" style="padding:40px 20px;text-align:center;">
        <div style="font-size:14px;font-weight:600;color:#475569;">No classes assigned</div>
        <div style="font-size:12px;color:#94a3b8;margin-top:4px;">The admin can assign you under <em>Teacher Assignments</em>.</div>
    </div>
@endforelse

@endsection