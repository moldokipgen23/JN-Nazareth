@extends('layouts.admin')
@section('page-title', $class)

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
    <div>
        <div style="display:flex;align-items:center;gap:10px;">
            <a href="{{ route('admin.classes.index') }}"
               style="padding:6px;border-radius:8px;color:#94a3b8;display:flex;text-decoration:none;"
               onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">{{ $class }}</h2>
                <p style="font-size:12px;color:#64748b;margin:3px 0 0;">
                    {{ $sectionNames->count() }} section(s)
                    @if($activeYear)
                        <span style="color:#0f766e;font-weight:700;">· {{ $activeYear->name }}</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('admin.students.index', ['class' => $class]) }}"
           style="background:#f0fdfa;color:#0f766e;border:1px solid #99f6e4;padding:8px 16px;border-radius:9px;font-size:12px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:6px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            View Students
        </a>
    </div>
</div>

@if(session('success'))
<div style="background:#dcfce7;color:#15803d;border-radius:10px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:600;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fee2e2;color:#b91c1c;border-radius:10px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:600;">{{ session('error') }}</div>
@endif

{{-- Sections grid --}}
@if($sectionNames->isNotEmpty())
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin-bottom:16px;">
    @foreach($sectionNames as $section)
    @php $count = $sectionCounts[$section->name] ?? 0; @endphp
    <a href="{{ route('admin.students.index', ['class' => $class, 'section' => $section->name]) }}"
       style="text-decoration:none;background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px;display:block;box-shadow:0 1px 8px rgba(0,0,0,.05);transition:all .15s;"
       onmouseover="this.style.borderColor='#14b8a6';this.style.boxShadow='0 4px 16px rgba(20,184,166,.15)'"
       onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='0 1px 8px rgba(0,0,0,.05)'">
        <div style="width:48px;height:48px;background:linear-gradient(135deg,#0f766e,#14b8a6);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
            <span style="font-size:20px;font-weight:800;color:#fff;">{{ $section->name }}</span>
        </div>
        <div style="font-size:13px;font-weight:700;color:#0f172a;">Section {{ $section->name }}</div>
        <div style="font-size:22px;font-weight:800;color:#0f766e;margin:4px 0 2px;">{{ $count }}</div>
        <div style="font-size:11px;color:#94a3b8;">student{{ $count == 1 ? '' : 's' }} →</div>
    </a>
    @endforeach
</div>
@else
<div style="background:#fff;border-radius:14px;padding:40px 20px;text-align:center;box-shadow:0 1px 8px rgba(0,0,0,.06);border:1px solid #f1f5f9;margin-bottom:16px;">
    <p style="font-size:14px;font-weight:600;color:#64748b;margin:0;">No sections configured for {{ $class }}</p>
    <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">Add a section below to start organizing students.</p>
</div>
@endif

{{-- Add section form (admin only) --}}
@if($isAdmin)
<div style="background:#fff;border-radius:14px;box-shadow:0 1px 8px rgba(0,0,0,.06);border:1px solid #f1f5f9;padding:16px 20px;">
    <h3 style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 10px;">Add Section</h3>
    <form method="POST" action="{{ route('admin.sections.store') }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
        @csrf
        <input type="hidden" name="class" value="{{ $class }}">
        <input type="text" name="name" placeholder="Section name (e.g. A)" maxlength="20" required
               style="border:1px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;width:160px;">
        <button type="submit"
                style="background:linear-gradient(135deg,#0f766e,#14b8a6);color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">
            + Add Section
        </button>
    </form>

    {{-- Section list with delete --}}
    @if($sectionNames->isNotEmpty())
    <div style="margin-top:12px;padding-top:12px;border-top:1px solid #f1f5f9;">
        <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:6px;">Existing Sections</div>
        <div style="display:flex;flex-wrap:wrap;gap:6px;">
            @foreach($sectionNames as $section)
            <div style="display:flex;align-items:center;gap:6px;background:#f0fdfa;border:1px solid #99f6e4;border-radius:8px;padding:6px 12px;">
                <span style="font-weight:700;color:#0f766e;font-size:13px;">{{ $section->name }}</span>
                <form method="POST" action="{{ route('admin.sections.destroy', $section) }}"
                      onsubmit="return confirm('Delete section {{ $section->name }}?')" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" style="background:none;border:none;color:#94a3b8;cursor:pointer;padding:0;font-size:14px;line-height:1;" title="Delete section">✕</button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif

@endsection
