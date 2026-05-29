@extends('layouts.admin')
@section('page-title', 'Edit Teacher')

@section('content')
<h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0 0 4px;">Edit Teacher</h2>
<p style="font-size:12px; color:#64748b; margin:0 0 18px;">Update {{ $teacher->name }}'s record.</p>

@if($errors->any())
<div style="background:#fef2f2; border:1px solid #fca5a5; border-radius:10px; padding:11px 16px; margin-bottom:16px;">
    <ul style="margin:0; padding-left:18px;">
        @foreach($errors->all() as $e)<li style="font-size:12px; color:#b91c1c;">{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

{{-- Tabs --}}
<div style="display:flex;gap:0;margin-bottom:16px;border-bottom:2px solid #e2e8f0;">
    <button type="button" id="tab1-btn" onclick="switchTab(1)"
            style="padding:10px 20px;font-size:13px;font-weight:700;background:none;border:none;border-bottom:2px solid #0f766e;color:#0f766e;margin-bottom:-2px;cursor:pointer;">
        Profile &amp; Class Role
    </button>
    <button type="button" id="tab2-btn" onclick="switchTab(2)"
            style="padding:10px 20px;font-size:13px;font-weight:700;background:none;border:none;border-bottom:2px solid transparent;color:#94a3b8;margin-bottom:-2px;cursor:pointer;">
        Subject Assignments
    </button>
</div>

{{-- Tab 1: Profile + Class Teacher --}}
<div id="tab1">
    <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('admin.teachers._form', ['hideSubjects' => true])
    </form>
</div>

{{-- Tab 2: Subject Assignments --}}
<div id="tab2" style="display:none;">
    <div style="background:#fff;border-radius:14px;box-shadow:0 1px 8px rgba(0,0,0,.06);border:1px solid #f1f5f9;padding:24px 28px;">
        <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <input type="hidden" name="update_subjects_only" value="1">

            <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:14px; padding-bottom:9px; border-bottom:1px solid #f1f5f9;">
                Subjects Taught by {{ $teacher->name }}
            </div>
            <p style="font-size:12px;color:#64748b;margin:0 0 12px;">
                Which subjects does this teacher teach, and in which classes/sections?
            </p>

@php
    $allClasses = \App\Models\Student::classes();
    $allSections = \App\Models\Section::active()->orderBy('sort_order')->orderBy('name')->get();
    $allSectionNames = $allSections->pluck('name')->unique()->sort()->values();
    if ($allSectionNames->isEmpty()) $allSectionNames = collect(['A']);
    $allSubjects = \App\Models\Subject::active()->orderBy('sort_order')->orderBy('name')->get();
    $assignmentsByClass = collect();
    if ($teacher) {
        foreach ($teacher->subjectAssignments() as $a) {
            $assignmentsByClass->push(['class' => $a['class'], 'section' => $a['section'], 'subject' => $a['subject']]);
        }
    }
    $assignedMap = [];
    $sectionMap = [];
    foreach ($assignmentsByClass as $a) {
        $assignedMap[$a['class']][] = $a['subject'];
        $sectionMap[$a['class']] = $a['section'];
    }
@endphp

<div style="display:flex;flex-direction:column;gap:10px;">
    @foreach($allClasses as $class)
    <div style="background:#fff;border-radius:10px;padding:14px 16px;border:1px solid #e2e8f0;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
            <div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $class }}</div>
            <div style="display:flex;align-items:center;gap:6px;">
                <span style="font-size:11px;color:#94a3b8;">Section:</span>
                <select name="section[{{ $class }}]" style="border:1px solid #e2e8f0;border-radius:6px;padding:4px 8px;font-size:12px;">
                    @foreach($allSectionNames as $sn)
                        <option value="{{ $sn }}" {{ ($sectionMap[$class] ?? 'A') === $sn ? 'selected' : '' }}>{{ $sn }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:5px;">
            @foreach($allSubjects as $subject)
            @php $checked = in_array($subject->name, $assignedMap[$class] ?? []); @endphp
            <label style="display:flex;align-items:center;gap:6px;padding:5px 10px;border-radius:6px;background:{{ $checked ? '#f0fdfa' : '#f8fafc' }};border:1px solid {{ $checked ? '#99f6e4' : '#e2e8f0' }};cursor:pointer;font-size:12px;font-weight:{{ $checked ? '700' : '400' }};color:#0f172a;">
                <input type="checkbox" name="subjects[{{ $class }}][]" value="{{ $subject->name }}"
                       {{ $checked ? 'checked' : '' }} style="accent-color:#0f766e;">
                <span>{{ $subject->name }}</span>
            </label>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:22px;">
                <a href="{{ route('admin.teachers.index') }}" style="background:#f1f5f9;color:#475569;font-size:13px;font-weight:600;padding:10px 20px;border-radius:9px;text-decoration:none;">Cancel</a>
                <button type="submit" style="background:linear-gradient(135deg,#0f766e,#14b8a6);color:#fff;font-size:13px;font-weight:700;padding:10px 24px;border-radius:9px;border:none;cursor:pointer;">Save Assignments</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(n) {
    document.getElementById('tab1').style.display = n === 1 ? 'block' : 'none';
    document.getElementById('tab2').style.display = n === 2 ? 'block' : 'none';
    const btn1 = document.getElementById('tab1-btn');
    const btn2 = document.getElementById('tab2-btn');
    btn1.style.borderBottomColor = n === 1 ? '#0f766e' : 'transparent';
    btn1.style.color = n === 1 ? '#0f766e' : '#94a3b8';
    btn2.style.borderBottomColor = n === 2 ? '#0f766e' : 'transparent';
    btn2.style.color = n === 2 ? '#0f766e' : '#94a3b8';
}
</script>
@endsection
