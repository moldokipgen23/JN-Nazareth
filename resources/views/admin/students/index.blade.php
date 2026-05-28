@extends('layouts.admin')
@section('page-title', 'Students')

@push('styles')
<style>
.stu-desktop { display:none; }
.stu-mobile  { display:block; }
@media (min-width: 640px) {
    .stu-desktop { display:grid !important; }
    .stu-mobile  { display:none !important; }
}
</style>
@endpush

@section('content')

@php
    $breadcrumbs = [];
    if ($currentClass) {
        $breadcrumbs[] = ['label' => 'All Classes', 'url' => route('admin.students.index')];
        $breadcrumbs[] = ['label' => $currentClass, 'url' => route('admin.students.index', ['class' => $currentClass])];
        if ($selectedSection) {
            $breadcrumbs[] = ['label' => 'Section ' . $selectedSection, 'url' => null];
        }
    }
@endphp

{{-- Breadcrumb --}}
@if(!empty($breadcrumbs))
<nav style="display:flex;align-items:center;gap:6px;font-size:12px;margin-bottom:16px;">
    @foreach($breadcrumbs as $i => $crumb)
        @if($i > 0)
            <svg width="12" height="12" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
        @endif
        @if($crumb['url'])
            <a href="{{ $crumb['url'] }}" style="color:#0f766e;text-decoration:none;font-weight:600;">{{ $crumb['label'] }}</a>
        @else
            <span style="color:#334155;font-weight:600;">{{ $crumb['label'] }}</span>
        @endif
    @endforeach
</nav>
@endif

{{-- Header --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">
            @if($currentClass && $selectedSection)
                {{ $currentClass }} — Section {{ $selectedSection }}
            @elseif($currentClass)
                {{ $currentClass }}
            @else
                Students
            @endif
        </h2>
        <p style="font-size:12px; color:#64748b; margin:3px 0 0;">
            @if(isset($classCounts))
                {{ $classCounts->sum() }} students across all classes
            @elseif(isset($sectionCounts))
                {{ $sectionCounts->sum() }} students
            @elseif(isset($students))
                {{ $students->total() }} students
            @endif
        </p>
    </div>
    @if($currentClass && !$selectedSection)
    <div style="display:flex;gap:8px;">
        <a href="{{ route('admin.students.export-class') }}?class={{ urlencode($currentClass) }}"
           style="background:#fff;color:#6366f1;font-size:12px;font-weight:600;padding:10px 16px;border-radius:10px;text-decoration:none;display:flex;align-items:center;gap:7px;border:1px solid #c7d2fe;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export
        </a>
        <a href="{{ route('admin.students.icard.batch', ['class' => $currentClass]) }}"
           style="background:#fff;color:#0f766e;font-size:12px;font-weight:600;padding:10px 16px;border-radius:10px;text-decoration:none;display:flex;align-items:center;gap:7px;border:1px solid #99f6e4;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
            ID Cards
        </a>
        <a href="{{ route('admin.students.create', ['class' => $currentClass]) }}"
           style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:600; padding:10px 18px; border-radius:10px; text-decoration:none; display:flex; align-items:center; gap:7px; box-shadow:0 4px 12px rgba(20,184,166,.3);">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Add Student
        </a>
    </div>
    @endif
</div>

{{-- CLASS GRID VIEW --}}
@if(!$currentClass)
<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(170px,1fr)); gap:12px; margin-bottom:16px;">
    @foreach($classes as $class)
    @php $count = $classCounts[$class] ?? 0; @endphp
    <a href="{{ route('admin.students.index', ['class' => $class]) }}"
       style="text-decoration:none; background:#fff; border:1px solid #f1f5f9; border-radius:14px; padding:16px 18px; box-shadow:0 1px 8px rgba(0,0,0,.05); display:block; transition:all .15s;"
       onmouseover="this.style.borderColor='#14b8a6';this.style.boxShadow='0 4px 16px rgba(20,184,166,.15)'"
       onmouseout="this.style.borderColor='#f1f5f9';this.style.boxShadow='0 1px 8px rgba(0,0,0,.05)'">
        <div style="font-size:13px; font-weight:700; color:#0f172a;">{{ $class }}</div>
        <div style="font-size:22px; font-weight:800; color:#0f766e; margin:6px 0 2px;">{{ $count }}</div>
        <div style="font-size:11px; color:#94a3b8;">student{{ $count == 1 ? '' : 's' }} →</div>
    </a>
    @endforeach
</div>
@if(isset($unassigned) && $unassigned > 0)
<a href="{{ route('admin.students.index', ['no_class' => 1]) }}"
   style="display:inline-flex;align-items:center;gap:6px;font-size:12px;color:#d97706;margin:10px 0 0;font-weight:600;text-decoration:none;padding:6px 12px;background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
    {{ $unassigned }} student(s) have no class — click to view &amp; fix
</a>
@endif
@endif

{{-- SECTION VIEW --}}
@if($currentClass && !$selectedSection && isset($sections))
<div style="margin-bottom:16px;">
    <div style="display:flex; gap:6px; flex-wrap:wrap;">
        @forelse($sections as $section)
        @php $count = $sectionCounts[$section] ?? 0; @endphp
        <a href="{{ route('admin.students.index', ['class' => $currentClass, 'section' => $section]) }}"
           style="text-decoration:none; background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 20px; box-shadow:0 1px 4px rgba(0,0,0,.04); display:flex; align-items:center; gap:12px; min-width:140px; transition:all .15s;"
           onmouseover="this.style.borderColor='#14b8a6';this.style.boxShadow='0 4px 12px rgba(20,184,166,.12)'"
           onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='0 1px 4px rgba(0,0,0,.04)'">
            <div style="width:40px;height:40px;background:#f0fdfa;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span style="font-size:16px;font-weight:800;color:#0f766e;">{{ $section }}</span>
            </div>
            <div>
                <div style="font-size:11px;color:#94a3b8;">Section</div>
                <div style="font-size:20px;font-weight:800;color:#0f172a;">{{ $count }}</div>
                <div style="font-size:10px;color:#94a3b8;">students</div>
            </div>
        </a>
        @empty
        <div style="padding:40px 20px;text-align:center;width:100%;">
            <p style="font-size:14px;font-weight:600;color:#64748b;">No sections configured for {{ $currentClass }}</p>
            <a href="{{ route('admin.sections.index') }}" style="color:#0f766e;font-size:13px;font-weight:600;text-decoration:none;">Manage Classes →</a>
        </div>
        @endforelse
    </div>
</div>
@endif

{{-- UNASSIGNED STUDENTS BANNER --}}
@if(isset($noClass) && $noClass)
<div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:10px;padding:10px 16px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
    <span style="font-size:13px;font-weight:600;color:#92400e;">⚠ Showing students with no class assigned. Edit each student to assign a class.</span>
    <a href="{{ route('admin.students.index') }}" style="font-size:12px;color:#0f766e;font-weight:600;text-decoration:none;">← Back to all students</a>
</div>
@endif

{{-- STUDENT TABLE --}}
@if((($currentClass && $selectedSection) || isset($noClass)) && isset($students))
{{-- Header actions --}}
<div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:14px;">
    <a href="{{ route('admin.students.import.form') }}"
       style="background:#fff; color:#0f766e; font-size:13px; font-weight:600; padding:10px 16px; border-radius:10px; text-decoration:none; display:flex; align-items:center; gap:7px; border:1px solid #99f6e4;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
        Import CSV
    </a>
    <a href="{{ route('admin.students.export', ['class' => $currentClass]) }}"
       style="background:#fff; color:#475569; font-size:13px; font-weight:600; padding:10px 16px; border-radius:10px; text-decoration:none; display:flex; align-items:center; gap:7px; border:1px solid #e2e8f0;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v2a2 2 0 002 2h14a2 2 0 002-2v-2"/></svg>
        Export CSV
    </a>
    <a href="{{ route('admin.students.icard.batch', ['class' => $currentClass, 'section' => $selectedSection]) }}"
       style="background:#fff; color:#0f766e; font-size:13px; font-weight:600; padding:10px 16px; border-radius:10px; text-decoration:none; display:flex; align-items:center; gap:7px; border:1px solid #99f6e4;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
        ID Cards
    </a>
    <a href="{{ route('admin.students.create', ['class' => $currentClass]) }}"
       style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:600; padding:10px 18px; border-radius:10px; text-decoration:none; display:flex; align-items:center; gap:7px; box-shadow:0 4px 12px rgba(20,184,166,.3);">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Add Student
    </a>
</div>

{{-- Status filter + search --}}
<form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:14px;background:#fff;border-radius:10px;padding:10px 14px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <input type="hidden" name="class" value="{{ $currentClass }}">
    <input type="hidden" name="section" value="{{ $selectedSection }}">
    <div style="display:flex;gap:4px;align-items:center;">
        <label style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;">Status:</label>
        @foreach(['active'=>'Active','dropped'=>'Dropped','transferred'=>'Transferred','graduated'=>'Graduated','all'=>'All'] as $val=>$label)
        <a href="{{ request()->fullUrlWithQuery(['enrollment_status'=>$val, 'page'=>null]) }}"
           style="padding:5px 11px;border-radius:99px;font-size:11px;font-weight:600;text-decoration:none;border:1.5px solid {{ $enrollmentStatus===$val ? '#0f766e' : '#e2e8f0' }};background:{{ $enrollmentStatus===$val ? '#0f766e' : '#f8fafc' }};color:{{ $enrollmentStatus===$val ? '#fff' : '#475569' }};">{{ $label }}</a>
        @endforeach
    </div>
    <div style="flex:1;min-width:150px;display:flex;gap:8px;">
        <input name="search" value="{{ request('search') }}"
               placeholder="Search by name, roll no. or father's name…"
               style="flex:1; padding:8px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; color:#0f172a; background:#fff; outline:none;">
        <button type="submit" style="background:#0f766e; color:#fff; border:none; padding:8px 14px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer;">Search</button>
        @if(request('search'))
        <a href="{{ route('admin.students.index', ['class' => $currentClass, 'section' => $selectedSection]) }}" style="background:#f1f5f9; color:#64748b; padding:8px 12px; border-radius:8px; font-size:12px; text-decoration:none; display:flex; align-items:center;">Clear</a>
        @endif
    </div>
</form>

{{-- Student table --}}
<div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; overflow:hidden;">
    <div class="stu-desktop" style="grid-template-columns:60px 2fr 1.4fr 1fr 90px 130px; gap:12px; padding:12px 20px; background:#f8fafc; border-bottom:1px solid #e2e8f0; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.06em;">
        <div>Roll</div><div>Name</div><div>Father's Name</div><div>Phone</div><div>Active</div><div style="text-align:right;">Actions</div>
    </div>

    @forelse($students as $member)
    {{-- Desktop row --}}
    <div class="stu-desktop" style="grid-template-columns:60px 2fr 1.4fr 1fr 90px 130px; gap:12px; padding:13px 20px; border-bottom:1px solid #f8fafc; align-items:center; font-size:13px;">
        <div style="color:#94a3b8; font-weight:600;">{{ $member->roll_number ?? '—' }}</div>
        <div style="display:flex; align-items:center; gap:10px;">
            @if($member->photo)
                <img src="{{ \App\Helpers\Settings::storageUrl($member->photo) }}" style="width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0;">
            @else
                <div style="width:34px;height:34px;border-radius:50%;background:#0f766e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff;flex-shrink:0;">{{ strtoupper(substr($member->name,0,1)) }}</div>
            @endif
            <div>
                <div style="font-weight:600; color:#0f172a;">{{ $member->name }}</div>
                <div style="font-size:11px; color:#94a3b8;">{{ $member->class ?? 'No class' }}{{ $member->section ? ' · Section '.$member->section : '' }}</div>
            </div>
        </div>
        <div style="color:#475569;">{{ $member->father_name ?? '—' }}</div>
        <div style="color:#475569;">{{ $member->parent_phone ?? $member->phone ?? '—' }}</div>
        <div>
            <span style="background:{{ $member->is_active ? '#dcfce7' : '#fee2e2' }}; color:{{ $member->is_active ? '#16a34a' : '#dc2626' }}; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600;">{{ $member->is_active ? 'Yes' : 'No' }}</span>
        </div>
        <div style="display:flex; align-items:center; justify-content:flex-end; gap:5px;">
            <a href="{{ route('admin.students.show', $member) }}" style="background:#f0fdfa; color:#0f766e; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none;">View</a>
            <a href="{{ route('admin.students.edit', $member) }}" style="background:#f0f9ff; color:#0369a1; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none;">Edit</a>
            <form method="POST" action="{{ route('admin.students.destroy', $member) }}" onsubmit="return confirm('Delete {{ addslashes($member->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" style="background:#fff1f2; color:#e11d48; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; border:none; cursor:pointer;">Del</button>
            </form>
        </div>
    </div>

    {{-- Mobile card --}}
    <div class="stu-mobile" style="padding:14px 16px; border-bottom:1px solid #f1f5f9;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
            @if($member->photo)
                <img src="{{ \App\Helpers\Settings::storageUrl($member->photo) }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0;">
            @else
                <div style="width:40px;height:40px;border-radius:50%;background:#0f766e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;color:#fff;flex-shrink:0;">{{ strtoupper(substr($member->name,0,1)) }}</div>
            @endif
            <div style="flex:1; min-width:0;">
                <div style="font-weight:700; color:#0f172a; font-size:14px;">{{ $member->name }}</div>
                <div style="font-size:11px; color:#94a3b8;">Roll {{ $member->roll_number ?? '—' }} · {{ $member->class ?? 'No class' }}</div>
            </div>
            <span style="background:{{ $member->is_active ? '#dcfce7' : '#fee2e2' }}; color:{{ $member->is_active ? '#16a34a' : '#dc2626' }}; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600;">{{ $member->is_active ? 'Active' : 'Inactive' }}</span>
        </div>
        <div style="display:flex; gap:6px;">
            <a href="{{ route('admin.students.show', $member) }}" style="flex:1; background:#f0fdfa; color:#0f766e; padding:7px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none; text-align:center;">View</a>
            <a href="{{ route('admin.students.edit', $member) }}" style="flex:1; background:#f0f9ff; color:#0369a1; padding:7px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none; text-align:center;">Edit</a>
            <form method="POST" action="{{ route('admin.students.destroy', $member) }}" onsubmit="return confirm('Delete {{ addslashes($member->name) }}?')" style="flex:1;">
                @csrf @method('DELETE')
                <button type="submit" style="width:100%; background:#fff1f2; color:#e11d48; padding:7px; border-radius:8px; font-size:12px; font-weight:600; border:none; cursor:pointer;">Delete</button>
            </form>
        </div>
    </div>

    @empty
    <div style="padding:60px 20px; text-align:center;">
        <p style="font-size:14px; font-weight:600; color:#334155; margin:0 0 6px;">No students found</p>
        <p style="font-size:13px; color:#94a3b8; margin:0;">{{ request('search') ? 'Try a different search.' : 'No students enrolled in this section.' }}</p>
    </div>
    @endforelse

    @if(isset($students) && $students->hasPages())
    <div style="padding:14px 20px; border-top:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
        <div style="font-size:12px; color:#94a3b8;">Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }}</div>
        {{ $students->links() }}
    </div>
    @endif
</div>
@endif

@endsection
