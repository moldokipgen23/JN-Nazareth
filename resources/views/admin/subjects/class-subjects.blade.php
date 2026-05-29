@extends('layouts.admin')
@section('page-title', 'Class Subjects')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
    <div>
        <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">Class Subjects</h1>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">Assign which subjects each class studies. Controls marks entry and result generation.</div>
    </div>
</div>

{{-- Tabs --}}
<div style="display:flex;gap:0;margin-bottom:16px;border-bottom:2px solid #e2e8f0;">
    <a href="{{ route('admin.subjects.index') }}"
       style="padding:10px 20px;font-size:13px;font-weight:700;text-decoration:none;border-bottom:2px solid transparent;color:#94a3b8;margin-bottom:-2px;">
        Global Subjects
    </a>
    <a href="{{ route('admin.class-subjects.index') }}"
       style="padding:10px 20px;font-size:13px;font-weight:700;text-decoration:none;border-bottom:2px solid #0f766e;color:#0f766e;margin-bottom:-2px;">
        Class Subjects
    </a>
</div>

@if(session('success'))
<div style="background:#dcfce7;color:#15803d;border-radius:10px;padding:10px 16px;margin-bottom:14px;font-size:13px;font-weight:600;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fee2e2;color:#b91c1c;border-radius:10px;padding:10px 16px;margin-bottom:14px;font-size:13px;font-weight:600;">{{ session('error') }}</div>
@endif

@if(!$year)
<div style="background:#fef3c7;border:1px solid #fde68a;border-radius:12px;padding:36px 20px;text-align:center;">
    <div style="font-weight:700;color:#92400e;font-size:15px;">No active academic year set.</div>
</div>
@else

@foreach($classes as $class)
    @php
        $assigned = $classSubjects->get($class, collect());
        $assignedIds = $assigned->pluck('subject_id')->toArray();
        $otherClasses = collect($classes)->filter(fn($c) => $c !== $class)->values();
    @endphp
    <div style="background:#fff;border-radius:12px;padding:16px 18px;margin-bottom:14px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <form method="POST" action="{{ route('admin.class-subjects.store') }}">
            @csrf
            <input type="hidden" name="class" value="{{ $class }}">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="font-size:15px;font-weight:700;color:#0f172a;">{{ $class }}</div>
                    <span style="font-size:11px;color:#94a3b8;">{{ count($assignedIds) }}/{{ $subjects->count() }} subjects</span>
                </div>
                <div style="display:flex;gap:6px;">
                    @if($otherClasses->isNotEmpty())
                    <select id="copy-source-{{ $loop->index }}" style="border:1px solid #e2e8f0;border-radius:6px;padding:5px 8px;font-size:11px;color:#475569;">
                        <option value="">Copy from…</option>
                        @foreach($otherClasses as $oc)
                        <option value="{{ $oc }}">{{ $oc }}</option>
                        @endforeach
                    </select>
                    <button type="button" onclick="copySubjects('{{ $class }}', {{ $loop->index }})" style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:6px;padding:5px 10px;font-size:11px;font-weight:600;cursor:pointer;">📋 Copy</button>
                    @endif
                    <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:6px 16px;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;">Save</button>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:5px;">
                @foreach($subjects as $subject)
                    @php
                        $cs = $assigned->firstWhere('subject_id', $subject->id);
                        $checked = $cs !== null;
                    @endphp
                    <div style="border-radius:7px;border:1px solid {{ $checked ? '#99f6e4' : '#e2e8f0' }};background:{{ $checked ? '#f0fdfa' : '#fafafa' }};overflow:hidden;">
                        <label style="display:flex;align-items:center;gap:6px;padding:7px 10px;cursor:pointer;font-size:12px;font-weight:{{ $checked ? '700' : '400' }};color:#0f172a;">
                            <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}"
                                   class="cs-cb-{{ $loop->parent->index }}" data-class-index="{{ $loop->parent->index }}" data-subject="{{ $subject->name }}"
                                   {{ $checked ? 'checked' : '' }} onchange="toggleConfig(this, {{ $loop->parent->index }})"
                                   style="accent-color:#0f766e;">
                            <span>{{ $subject->name }}</span>
                            @if($subject->code)
                                <span style="font-size:10px;color:#94a3b8;">({{ $subject->code }})</span>
                            @endif
                        </label>
                        <div id="config-{{ $loop->parent->index }}-{{ $subject->id }}" style="display:{{ $checked ? 'flex' : 'none' }};gap:4px;padding:0 10px 6px;flex-wrap:wrap;">
                            <input type="number" name="full_marks[{{ $subject->id }}]" placeholder="FM" value="{{ old('full_marks.'.$subject->id, $cs->full_marks ?? '') }}"
                                   style="width:50px;border:1px solid #e2e8f0;border-radius:4px;padding:2px 5px;font-size:10px;color:#475569;" title="Full Marks">
                            <input type="number" name="pass_marks[{{ $subject->id }}]" placeholder="PM" value="{{ old('pass_marks.'.$subject->id, $cs->pass_marks ?? '') }}"
                                   style="width:50px;border:1px solid #e2e8f0;border-radius:4px;padding:2px 5px;font-size:10px;color:#475569;" title="Pass Marks">
                            <label style="font-size:10px;color:#64748b;display:flex;align-items:center;gap:2px;cursor:pointer;">
                                <input type="checkbox" name="is_optional[{{ $subject->id }}]" value="1" {{ $cs?->is_optional ? 'checked' : '' }} style="accent-color:#0f766e;"> Opt
                            </label>
                            <label style="font-size:10px;color:#64748b;display:flex;align-items:center;gap:2px;cursor:pointer;">
                                <input type="checkbox" name="grade_only[{{ $subject->id }}]" value="1" {{ $cs?->grade_only ? 'checked' : '' }} style="accent-color:#0f766e;"> Grade
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
    </div>
@endforeach

@endif

<script>
function toggleConfig(cb, idx) {
    const config = document.getElementById('config-' + idx + '-' + cb.value);
    if (config) config.style.display = cb.checked ? 'flex' : 'none';
}

function copySubjects(targetClass, idx) {
    const sel = document.getElementById('copy-source-' + idx);
    const sourceClass = sel.value;
    if (!sourceClass) return;

    const sourceCheckboxes = document.querySelectorAll('.cs-cb-' + idx);
    // We need to find the source class section — by class name stored in data attribute
    // Instead, fetch via AJAX or redirect. Simple approach: reload with copy param.
    const url = '{{ route("admin.class-subjects.copy") }}' + '?from=' + encodeURIComponent(sourceClass) + '&to=' + encodeURIComponent(targetClass);
    window.location.href = url;
}
</script>
@endsection
