@extends('layouts.admin')
@section('page-title', 'Class Subjects')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
    <div>
        <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">Class Subjects</h1>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">Assign which subjects each class studies. This controls marks entry, questions, and result generation.</div>
    </div>
</div>

{{-- Tabs --}}
<div style="display:flex;gap:0;margin-bottom:16px;border-bottom:2px solid #e2e8f0;">
    <a href="{{ route('admin.subjects.index') }}"
       style="padding:10px 20px;font-size:13px;font-weight:700;text-decoration:none;border-bottom:2px solid transparent;color:#94a3b8;margin-bottom:-2px;transition:all .15s;">
        Global Subjects
    </a>
    <a href="{{ route('admin.class-subjects.index') }}"
       style="padding:10px 20px;font-size:13px;font-weight:700;text-decoration:none;border-bottom:2px solid #0f766e;color:#0f766e;margin-bottom:-2px;transition:all .15s;">
        Class Subjects
    </a>
</div>

@if(session('success'))
<div style="background:#dcfce7;color:#15803d;border-radius:10px;padding:10px 16px;margin-bottom:14px;font-size:13px;font-weight:600;">{{ session('success') }}</div>
@endif

@if(!$year)
<div style="background:#fef3c7;border:1px solid #fde68a;border-radius:12px;padding:36px 20px;text-align:center;">
    <div style="font-weight:700;color:#92400e;font-size:15px;">No active academic year set.</div>
</div>
@else

@foreach($classes as $class)
    @php $assigned = $classSubjects->get($class, collect())->pluck('subject_id')->toArray(); @endphp
    <div style="background:#fff;border-radius:12px;padding:16px 18px;margin-bottom:12px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <form method="POST" action="{{ route('admin.class-subjects.store') }}">
            @csrf
            <input type="hidden" name="class" value="{{ $class }}">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                <div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $class }}</div>
                <span style="font-size:11px;color:#94a3b8;">{{ count($assigned) }}/{{ $subjects->count() }} subjects</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:6px;">
                @foreach($subjects as $subject)
                <label style="display:flex;align-items:center;gap:6px;padding:6px 10px;border-radius:6px;background:{{ in_array($subject->id, $assigned) ? '#f0fdfa' : '#f8fafc' }};border:1px solid {{ in_array($subject->id, $assigned) ? '#99f6e4' : '#e2e8f0' }};cursor:pointer;font-size:12px;font-weight:{{ in_array($subject->id, $assigned) ? '700' : '400' }};color:#0f172a;">
                    <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}"
                           {{ in_array($subject->id, $assigned) ? 'checked' : '' }}
                           style="accent-color:#0f766e;">
                    <span>{{ $subject->name }}</span>
                    @if($subject->code)
                        <span style="font-size:10px;color:#94a3b8;">({{ $subject->code }})</span>
                    @endif
                </label>
                @endforeach
            </div>
            <div style="margin-top:10px;display:flex;justify-content:flex-end;">
                <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:7px 18px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">Save {{ $class }}</button>
            </div>
        </form>
    </div>
@endforeach

@endif
@endsection
