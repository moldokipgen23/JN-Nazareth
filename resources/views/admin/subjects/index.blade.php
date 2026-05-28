@extends('layouts.admin')
@section('page-title','Subjects')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
    <div>
        <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">Subjects</h1>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">Master subject list — used across assignments, marks &amp; questions</div>
    </div>
    <div style="font-size:12px;color:#94a3b8;">{{ $subjects->count() }} subject(s)</div>
</div>

@if(session('success'))
<div style="background:#dcfce7;color:#15803d;border-radius:10px;padding:10px 16px;margin-bottom:14px;font-size:13px;font-weight:600;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fee2e2;color:#b91c1c;border-radius:10px;padding:10px 16px;margin-bottom:14px;font-size:13px;font-weight:600;">{{ session('error') }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 340px;gap:16px;align-items:start;" class="dash-event-side">

    {{-- Subject list --}}
    <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="padding:10px 16px;background:#f8fafc;border-bottom:1px solid #f1f5f9;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">
            All Subjects
        </div>
        @forelse($subjects as $subject)
        <div style="display:flex;align-items:center;gap:10px;padding:10px 16px;border-bottom:1px solid #f8fafc;">
            <div style="flex:1;min-width:0;">
                {{-- Inline edit --}}
                <form method="POST" action="{{ route('admin.subjects.update', $subject) }}" style="display:flex;gap:6px;align-items:center;">
                    @csrf @method('PUT')
                    <input type="text" name="name" value="{{ $subject->name }}" required
                           style="flex:1;min-width:0;border:1px solid #e2e8f0;border-radius:7px;padding:5px 9px;font-size:13px;font-weight:600;color:#0f172a;">
                    <input type="text" name="code" value="{{ $subject->code }}" placeholder="Code"
                           style="width:70px;border:1px solid #e2e8f0;border-radius:7px;padding:5px 8px;font-size:12px;color:#64748b;">
                    <button type="submit" style="background:#f0fdfa;color:#0f766e;border:1px solid #99f6e4;padding:5px 10px;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;">Save</button>
                </form>
            </div>
            <div style="display:flex;gap:4px;flex-shrink:0;">
                <form method="POST" action="{{ route('admin.subjects.toggle', $subject) }}">
                    @csrf
                    <button type="submit"
                            style="background:{{ $subject->is_active ? '#f0fdf4' : '#f8fafc' }};color:{{ $subject->is_active ? '#15803d' : '#94a3b8' }};border:1px solid {{ $subject->is_active ? '#bbf7d0' : '#e2e8f0' }};padding:4px 10px;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;">
                        {{ $subject->is_active ? 'Active' : 'Inactive' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}"
                      onsubmit="return confirm('Delete {{ addslashes($subject->name) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="background:none;border:none;color:#dc2626;font-size:11px;font-weight:600;cursor:pointer;padding:4px 6px;">✕</button>
                </form>
            </div>
        </div>
        @empty
        <div style="padding:40px;text-align:center;color:#94a3b8;font-size:13px;">No subjects yet. Add one →</div>
        @endforelse
    </div>

    {{-- Add new subject --}}
    <div style="background:#fff;border-radius:12px;padding:16px 18px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:12px;">Add Subject</div>
        <form method="POST" action="{{ route('admin.subjects.store') }}" style="display:flex;flex-direction:column;gap:10px;">
            @csrf
            @error('name')<div style="color:#b91c1c;font-size:12px;">{{ $message }}</div>@enderror
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:3px;">Subject Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Mathematics"
                       style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:8px 11px;font-size:13px;">
            </div>
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:3px;">Short Code</label>
                <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g. MATH"
                       style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:8px 11px;font-size:13px;">
            </div>
            <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:10px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">Add Subject</button>
        </form>

        <div style="margin-top:16px;padding-top:14px;border-top:1px solid #f1f5f9;font-size:11px;color:#94a3b8;line-height:1.6;">
            <strong style="color:#64748b;">Note:</strong> Subject names here become the dropdown options in Teacher Assignments, Marks, and Questions forms. Renaming a subject updates the name only — existing records keep their stored text.
        </div>
    </div>

</div>
@endsection
