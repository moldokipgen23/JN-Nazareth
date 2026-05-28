@extends('layouts.admin')
@section('page-title', 'Teacher Assignments')

@section('content')
@php
    $input = 'width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px; font-size:13px; background:#fff;';
    $card = 'background:#fff; border:1px solid #e5e7eb; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.05);';
    $cb = 'width:16px;height:16px;accent-color:#2563eb;flex-shrink:0;';
@endphp

<div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:18px;">
    <div>
        <h2 style="font-size:20px; font-weight:800; color:#0f172a; margin:0;">Teacher Assignments</h2>
        <p style="font-size:12px; color:#64748b; margin:4px 0 0;">
            Active year:
            <strong style="color:#0f766e;">{{ $activeYear?->name ?? 'Not set' }}</strong>
            @if($filterTeacher)
                &nbsp;·&nbsp; Filtered: <strong style="color:#0f766e;">{{ $filterTeacher->name }}</strong>
            @endif
        </p>
    </div>
</div>

{{-- Filter by teacher --}}
<div style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.05); padding:14px 18px; margin-bottom:18px;">
    <form method="GET" action="{{ route('admin.teacher-assignments.index') }}" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <label style="font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.4px;">Filter by Teacher:</label>
        <select name="teacher_id" style="border:1px solid #d1d5db; border-radius:10px; padding:9px 12px; font-size:13px; min-width:240px;">
            <option value="">— All teachers —</option>
            @foreach($teachers as $t)
                <option value="{{ $t->id }}" {{ (string)$filterTeacherId === (string)$t->id ? 'selected' : '' }}>{{ $t->name }}</option>
            @endforeach
        </select>
        <button type="submit" style="background:#0f766e; color:#fff; border:none; border-radius:10px; padding:9px 20px; font-size:13px; font-weight:700; cursor:pointer;">Apply</button>
        @if($filterTeacherId)
            <a href="{{ route('admin.teacher-assignments.index') }}" style="background:#f1f5f9; color:#475569; padding:9px 16px; border-radius:10px; font-size:12px; font-weight:600; text-decoration:none;">Clear filter</a>
            <a href="{{ route('admin.teachers.edit', $filterTeacherId) }}" style="margin-left:auto; background:#eff6ff; color:#1d4ed8; padding:9px 16px; border-radius:10px; font-size:12px; font-weight:700; text-decoration:none;">
                Edit {{ $filterTeacher?->name ?? 'teacher' }} →
            </a>
        @endif
    </form>
</div>

@if(! $activeYear)
    <div style="{{ $card }} padding:22px; color:#92400e; background:#fffbeb;">
        Create an academic year before assigning teachers.
    </div>
@elseif($teachers->isEmpty())
    <div style="{{ $card }} padding:22px; color:#475569;">
        No teachers in the directory. <a href="{{ route('admin.teachers.create') }}" style="color:#0f766e;font-weight:700;">Add a teacher first</a>.
    </div>
@elseif($subjects->isEmpty())
    <div style="{{ $card }} padding:22px; color:#475569;">
        No subjects yet. <a href="{{ route('admin.subjects.index') }}" style="color:#0f766e;font-weight:700;">Add subjects first</a>.
    </div>
@else
<div style="margin-bottom:18px;">
    <div style="{{ $card }} padding:18px; max-width:520px;">
        <h3 style="font-size:15px; font-weight:800; color:#0f172a; margin:0 0 12px;">Assign Subject Teacher</h3>
        <form method="POST" action="{{ route('admin.teacher-assignments.subject.store') }}">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr 90px; gap:10px; margin-bottom:12px;" class="resp-stack-sm">
                <select name="teacher_id" required style="{{ $input }}">
                    <option value="">Teacher</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ (string)$filterTeacherId === (string)$t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
                <select name="class" id="assign-class" required style="{{ $input }}" onchange="filterSections(this.value)">
                    <option value="">Class</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
                <select name="section" id="assign-section" required style="{{ $input }}">
                    <option value="">Section</option>
                </select>
            </div>
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:8px; margin-bottom:12px;">
                @foreach($subjects as $s)
                <label style="display:flex; align-items:center; gap:8px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:9px 11px; font-size:12.5px; cursor:pointer;">
                    <input type="checkbox" name="subjects[]" value="{{ $s->name }}" style="{{ $cb }}">
                    {{ $s->name }}{{ $s->code ? ' ('.$s->code.')' : '' }}
                </label>
                @endforeach
            </div>
            <button type="submit" style="background:#2563eb; color:#fff; border:none; border-radius:10px; padding:10px 14px; font-size:13px; font-weight:700; cursor:pointer;">Save Assignments</button>
        </form>
    </div>
</div>

{{-- Manage Sections --}}
<div style="margin-bottom:18px;">
    <details style="{{ $card }} max-width:520px;">
        <summary style="font-size:13px; font-weight:800; color:#0f172a; padding:14px 16px; cursor:pointer;">Manage Sections</summary>
        <div style="padding:0 16px 16px;">
            <form method="POST" action="{{ route('admin.sections.store') }}" style="display:flex; gap:10px; margin-bottom:10px;">
                @csrf
                <select name="class" required style="{{ $input }}+'flex:1;'">
                    <option value="">Class</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
                <input name="name" placeholder="Section name" required maxlength="20" style="{{ $input }}+'flex:1;'">
                <button type="submit" style="background:#0f766e; color:#fff; border:none; border-radius:10px; padding:10px 14px; font-size:13px; font-weight:700; cursor:pointer; white-space:nowrap;">+ Add</button>
            </form>
            @php $groups = $sections->groupBy('class'); @endphp
            @foreach($groups as $class => $secs)
                <div style="margin-bottom:8px;">
                    <strong style="font-size:12px; color:#475569;">{{ $class }}</strong>
                    <div style="display:flex; flex-wrap:wrap; gap:6px; margin-top:4px;">
                        @foreach($secs as $sec)
                            <span style="display:inline-flex; align-items:center; gap:6px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:5px 10px; font-size:12px;">
                                {{ $sec->name }}
                                <form method="POST" action="{{ route('admin.sections.destroy', $sec) }}" style="display:inline;" onsubmit="return confirm('Delete section {{ $sec->name }} for {{ $class }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none; border:none; color:#e11d48; cursor:pointer; font-size:13px; padding:0; line-height:1;">&times;</button>
                                </form>
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </details>
</div>
@endif

<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:16px;">
    <div style="{{ $card }} overflow:hidden;">
        <div style="padding:14px 16px; border-bottom:1px solid #f1f5f9; font-weight:800; color:#0f172a;">Class Teachers</div>
        <p style="padding:8px 16px 0; font-size:11px; color:#94a3b8; margin:0;">Set on each teacher's profile.</p>
        @forelse($classAssignments as $assignment)
            <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; padding:13px 16px; border-bottom:1px solid #f8fafc;">
                <div>
                    <div style="font-size:13px; font-weight:700; color:#0f172a;">{{ $assignment->class }} · Section {{ $assignment->section }}</div>
                    <div style="font-size:12px; color:#64748b;">{{ $assignment->teacher?->name ?? 'Teacher removed' }}</div>
                </div>
                <form method="POST" action="{{ route('admin.teacher-assignments.class.destroy', $assignment) }}" onsubmit="return confirm('Remove this class teacher assignment?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background:#fff1f2; color:#e11d48; border:none; border-radius:8px; padding:7px 10px; font-size:12px; font-weight:700; cursor:pointer;">Remove</button>
                </form>
            </div>
        @empty
            <div style="padding:22px 16px; color:#94a3b8; font-size:13px;">
                @if($filterTeacher)
                    {{ $filterTeacher->name }} is not a class teacher in this year.
                @else
                    No class teacher assignments yet.
                @endif
            </div>
        @endforelse
    </div>

    <div style="{{ $card }} overflow:hidden;">
        <div style="padding:14px 16px; border-bottom:1px solid #f1f5f9; font-weight:800; color:#0f172a;">Subject Teachers</div>
        @forelse($subjectAssignments as $assignment)
            <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; padding:13px 16px; border-bottom:1px solid #f8fafc;">
                <div>
                    <div style="font-size:13px; font-weight:700; color:#0f172a;">{{ $assignment->subject }} · {{ $assignment->class }} {{ $assignment->section }}</div>
                    <div style="font-size:12px; color:#64748b;">{{ $assignment->teacher?->name ?? 'Teacher removed' }}</div>
                </div>
                <form method="POST" action="{{ route('admin.teacher-assignments.subject.destroy', $assignment) }}" onsubmit="return confirm('Remove this subject teacher assignment?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background:#fff1f2; color:#e11d48; border:none; border-radius:8px; padding:7px 10px; font-size:12px; font-weight:700; cursor:pointer;">Remove</button>
                </form>
            </div>
        @empty
            <div style="padding:22px 16px; color:#94a3b8; font-size:13px;">
                @if($filterTeacher)
                    {{ $filterTeacher->name }} has no subject assignments in this year.
                @else
                    No subject teacher assignments yet.
                @endif
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
var sectionData = {!! $sectionList !!};

function filterSections(className) {
    var sel = document.getElementById('assign-section');
    sel.innerHTML = '<option value="">Section</option>';
    if (!className || !sectionData[className]) return;
    sectionData[className].forEach(function(s) {
        var opt = document.createElement('option');
        opt.value = s;
        opt.textContent = s;
        sel.appendChild(opt);
    });
}
</script>
@endpush
