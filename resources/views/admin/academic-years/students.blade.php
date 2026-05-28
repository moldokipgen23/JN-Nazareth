@extends('layouts.admin')
@section('page-title', $academicYear->name . ' — Students')
@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
    <div style="display:flex;align-items:center;gap:10px;">
        <div>
            <div style="display:flex;align-items:center;gap:8px;">
                <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">{{ $academicYear->name }}</h1>
                @if($academicYear->is_active)
                    <span style="background:#dcfce7;color:#15803d;font-size:10px;font-weight:700;padding:3px 10px;border-radius:99px;">Active Year</span>
                @endif
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:2px;">
                {{ $total }} student(s) enrolled
                @if($academicYear->starts_on) · {{ $academicYear->starts_on->format('d M Y') }} – {{ $academicYear->ends_on?->format('d M Y') ?? '?' }} @endif
            </div>
        </div>
    </div>
    <div style="display:flex;gap:8px;">
        @if($academicYear->is_active)
        <a href="{{ route('admin.academic-years.promote.form', $academicYear) }}"
           style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;padding:9px 16px;border-radius:9px;font-size:12px;font-weight:700;text-decoration:none;">
            Promote Students →
        </a>
        @endif
        <a href="{{ route('admin.academic-years.index') }}"
           style="background:#f1f5f9;color:#475569;padding:9px 14px;border-radius:9px;font-size:12px;font-weight:600;text-decoration:none;">
            Manage Years
        </a>
    </div>
</div>

{{-- Class filter tabs --}}
<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:14px;">
    <a href="{{ route('admin.academic-years.students', $academicYear) }}"
       style="padding:6px 13px;border-radius:99px;font-size:12px;font-weight:600;text-decoration:none;border:1.5px solid {{ !$currentClass ? '#0f766e' : '#e2e8f0' }};background:{{ !$currentClass ? '#0f766e' : '#f8fafc' }};color:{{ !$currentClass ? '#fff' : '#475569' }};">
        All <span style="background:{{ !$currentClass ? 'rgba(255,255,255,.25)' : '#cbd5e1' }};color:#fff;border-radius:99px;font-size:10px;padding:1px 6px;font-weight:700;">{{ $total }}</span>
    </a>
    @foreach($classes as $class)
        @if(($classCounts[$class] ?? 0) > 0)
        @php $isActive = $currentClass === $class; @endphp
        <a href="{{ route('admin.academic-years.students', [$academicYear, 'class' => $class]) }}"
           style="padding:6px 13px;border-radius:99px;font-size:12px;font-weight:600;text-decoration:none;border:1.5px solid {{ $isActive ? '#0f766e' : '#e2e8f0' }};background:{{ $isActive ? '#0f766e' : '#f8fafc' }};color:{{ $isActive ? '#fff' : '#475569' }};">
            {{ $class }}
            <span style="background:{{ $isActive ? 'rgba(255,255,255,.25)' : '#cbd5e1' }};color:#fff;border-radius:99px;font-size:10px;padding:1px 6px;font-weight:700;">{{ $classCounts[$class] }}</span>
        </a>
        @endif
    @endforeach
</div>

{{-- Search --}}
<form method="GET" style="margin-bottom:14px;display:flex;gap:8px;flex-wrap:wrap;">
    @if($currentClass)<input type="hidden" name="class" value="{{ $currentClass }}">@endif
    <input name="search" value="{{ $search }}" placeholder="Search name or roll no…"
           style="flex:1;min-width:180px;max-width:320px;padding:9px 13px;border-radius:9px;border:1px solid #e2e8f0;font-size:13px;background:#fff;">
    <button type="submit" style="background:#0f766e;color:#fff;border:none;padding:9px 16px;border-radius:9px;font-size:13px;font-weight:600;cursor:pointer;">Search</button>
    @if($search)
    <a href="{{ route('admin.academic-years.students', array_filter(['academicYear'=>$academicYear->id,'class'=>$currentClass])) }}"
       style="background:#f1f5f9;color:#64748b;padding:9px 13px;border-radius:9px;font-size:13px;text-decoration:none;">Clear</a>
    @endif
</form>

@if($enrollments->isEmpty())
<div style="background:#fff;border-radius:12px;padding:48px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="font-size:32px;opacity:.3;margin-bottom:8px;">🎓</div>
    <div style="font-weight:600;color:#475569;">No students found{{ $currentClass ? ' in '.$currentClass : '' }}.</div>
</div>
@else

{{-- When no class filter: show overview cards first --}}
@if(!$currentClass && !$search)
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px;margin-bottom:16px;">
    @foreach($classes as $class)
    @if(($classCounts[$class] ?? 0) > 0)
    <a href="{{ route('admin.academic-years.students', [$academicYear, 'class' => $class]) }}"
       style="text-decoration:none;background:#fff;border:1px solid #f1f5f9;border-radius:12px;padding:14px 16px;box-shadow:0 1px 6px rgba(0,0,0,.05);display:block;">
        <div style="font-size:12px;font-weight:700;color:#0f172a;">{{ $class }}</div>
        <div style="font-size:22px;font-weight:800;color:#0f766e;margin:4px 0 2px;">{{ $classCounts[$class] }}</div>
        <div style="font-size:10px;color:#94a3b8;">students</div>
    </a>
    @endif
    @endforeach
</div>
@endif

{{-- Student table --}}
<div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="text-align:left;padding:10px 16px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Roll</th>
                <th style="text-align:left;padding:10px 16px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Name</th>
                <th style="text-align:left;padding:10px 16px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Class</th>
                <th style="text-align:left;padding:10px 16px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Section</th>
                <th style="text-align:left;padding:10px 16px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Status</th>
                <th style="text-align:right;padding:10px 16px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($enrollments as $enr)
        @php
            $s = $enr->student;
            $statusColour = ['active'=>'#dcfce7|#15803d','dropped'=>'#fee2e2|#b91c1c','transferred'=>'#dbeafe|#1d4ed8','graduated'=>'#f3e8ff|#7e22ce'][$enr->status] ?? '#f1f5f9|#475569';
            [$bg,$fg] = explode('|', $statusColour);
        @endphp
        <tr style="border-top:1px solid #f1f5f9;">
            <td style="padding:10px 16px;color:#94a3b8;font-weight:600;">{{ $enr->roll_number ?? '—' }}</td>
            <td style="padding:10px 16px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    @if($s?->photo)
                        <img src="{{ \App\Helpers\Settings::storageUrl($s->photo) }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                    @else
                        <div style="width:30px;height:30px;border-radius:50%;background:#0f766e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:#fff;flex-shrink:0;">{{ strtoupper(substr($s?->name ?? '?', 0, 1)) }}</div>
                    @endif
                    <div>
                        <div style="font-weight:600;color:#0f172a;">{{ $s?->name ?? '—' }}</div>
                        @if($s?->father_name)<div style="font-size:11px;color:#94a3b8;">{{ $s->father_name }}</div>@endif
                    </div>
                </div>
            </td>
            <td style="padding:10px 16px;color:#475569;">{{ $enr->class }}</td>
            <td style="padding:10px 16px;color:#475569;">{{ $enr->section ?? '—' }}</td>
            <td style="padding:10px 16px;">
                <span style="background:{{ $bg }};color:{{ $fg }};font-size:10px;font-weight:700;padding:3px 9px;border-radius:99px;">{{ ucfirst($enr->status) }}</span>
            </td>
            <td style="padding:10px 16px;text-align:right;">
                @if($s)
                <a href="{{ route('admin.students.show', $s) }}"
                   style="background:#f0fdfa;color:#0f766e;padding:5px 10px;border-radius:7px;font-size:11px;font-weight:600;text-decoration:none;">View</a>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

    @if($enrollments->hasPages())
    <div style="padding:12px 16px;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <div style="font-size:12px;color:#94a3b8;">Showing {{ $enrollments->firstItem() }}–{{ $enrollments->lastItem() }} of {{ $enrollments->total() }}</div>
        {{ $enrollments->links() }}
    </div>
    @endif
</div>
@endif

@endsection
