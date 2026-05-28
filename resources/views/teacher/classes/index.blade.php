@extends('layouts.teacher')
@section('page-title', 'My Classes')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <div>
        <h1 style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">My Classes</h1>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">
            {{ $year ? $year->name : 'No active year' }} &middot; {{ $classes->count() }} {{ Str::plural('class', $classes->count()) }}
        </div>
    </div>
</div>

@forelse($classes as $row)
    @php
        $section = $row['section'] ? ' — Section '.$row['section'] : '';
        $href    = route('admin.classes.show', ['class' => $row['class']]);
    @endphp
    <a href="{{ $href }}" style="display:flex;align-items:center;gap:14px;background:#fff;border-radius:12px;padding:16px;margin-bottom:10px;box-shadow:0 1px 3px rgba(15,23,42,.06);text-decoration:none;transition:box-shadow .15s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(15,23,42,.1)'" onmouseout="this.style.boxShadow='0 1px 3px rgba(15,23,42,.06)'">
        <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#ccfbf1,#99f6e4);color:#0f766e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">
            {{ strtoupper(substr($row['class'], 0, 2)) }}
        </div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:15px;font-weight:600;color:#0f172a;">
                {{ $row['class'] }}{{ $section }}
                @if($row['is_class_teacher'])
                    <span class="badge" style="background:#ecfdf5;color:#047857;margin-left:6px;">Class Teacher</span>
                @elseif(!empty($row['legacy']))
                    <span class="badge" style="background:#f1f5f9;color:#64748b;margin-left:6px;">Legacy</span>
                @endif
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">
                <span>{{ $row['students'] }} {{ Str::plural('student', $row['students']) }}</span>
                @if(count($row['subjects']))
                    <span style="color:#cbd5e1;margin:0 6px;">&middot;</span>
                    <span>Teaching: {{ implode(', ', $row['subjects']) }}</span>
                @endif
            </div>
        </div>
        <svg width="16" height="16" fill="none" stroke="#cbd5e1" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    </a>
@empty
    <div style="background:#fff;border-radius:14px;padding:48px 24px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="font-size:36px;opacity:.3;margin-bottom:10px;">📋</div>
        <div style="font-weight:600;color:#475569;font-size:15px;">No classes assigned</div>
        <div style="font-size:13px;color:#94a3b8;margin-top:6px;">The admin can assign you under <em>Teacher Assignments</em>.</div>
    </div>
@endforelse

@endsection
