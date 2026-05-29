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
        <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:14px; padding-bottom:9px; border-bottom:1px solid #f1f5f9;">
            Subjects Taught by {{ $teacher->name }}
        </div>
        <p style="font-size:12px;color:#64748b;margin:0 0 12px;">
            Which subjects does this teacher teach, and in which classes/sections?
        </p>

        <div id="subject-rows" style="display:flex;flex-direction:column;gap:8px;">
            @php
                $rows = $existingSubjectAssignments ?? [];
                $classes = \App\Models\Student::classes();
                $sections = \App\Models\Section::active()->orderBy('sort_order')->orderBy('name')->get();
                $sectionNames = $sections->pluck('name')->unique()->sort()->values();
                if ($sectionNames->isEmpty()) $sectionNames = collect(['A']);
                $subjectsList = \App\Models\Subject::active()->orderBy('sort_order')->orderBy('name')->get();
            @endphp
            @forelse($rows as $i => $row)
            @include('admin.teachers._subject-row', ['i' => $i, 'row' => $row, 'classes' => $classes, 'sectionNames' => $sectionNames, 'subjectsList' => $subjectsList])
            @empty
            @endforelse
        </div>

        <button type="button" onclick="addSubjectRowEdit()" style="background:#f0fdfa;color:#0f766e;border:1.5px dashed #99f6e4;border-radius:8px;padding:8px 16px;font-size:12px;font-weight:700;cursor:pointer;margin-top:10px;">+ Add Subject</button>

        <template id="subject-row-edit-template">
            <div class="subject-row" style="display:grid;grid-template-columns:1.2fr 0.8fr 1.5fr 40px;gap:8px;align-items:center;">
                <select name="subject_assignments[__INDEX__][class]" required style="border:1px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:13px;width:100%;box-sizing:border-box;outline:none;">
                    <option value="">— Class —</option>
                    @foreach($classes as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
                </select>
                <select name="subject_assignments[__INDEX__][section]" required style="border:1px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:13px;width:100%;box-sizing:border-box;outline:none;">
                    @foreach($sectionNames as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach
                </select>
                <select name="subject_assignments[__INDEX__][subject]" required style="border:1px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:13px;width:100%;box-sizing:border-box;outline:none;">
                    <option value="">— Subject —</option>
                    @foreach($subjectsList as $subj)<option value="{{ $subj->name }}">{{ $subj->name }}</option>@endforeach
                </select>
                <button type="button" onclick="this.closest('.subject-row').remove()" style="background:#fee2e2;color:#dc2626;border:none;border-radius:8px;height:38px;cursor:pointer;font-size:18px;font-weight:700;" title="Remove">×</button>
            </div>
        </template>

        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:22px;">
            <a href="{{ route('admin.teachers.index') }}" style="background:#f1f5f9;color:#475569;font-size:13px;font-weight:600;padding:10px 20px;border-radius:9px;text-decoration:none;">Cancel</a>
            <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <input type="hidden" name="update_subjects_only" value="1">
                <button type="submit" style="background:linear-gradient(135deg,#0f766e,#14b8a6);color:#fff;font-size:13px;font-weight:700;padding:10px 24px;border-radius:9px;border:none;cursor:pointer;">Save Assignments</button>
            </form>
        </div>
    </div>
</div>

<script>
let subjectRowIndex = {{ count($rows ?? []) }};
function addSubjectRowEdit() {
    const tpl = document.getElementById('subject-row-edit-template').innerHTML.replace(/__INDEX__/g, subjectRowIndex++);
    document.getElementById('subject-rows').insertAdjacentHTML('beforeend', tpl);
}

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
