@extends('layouts.admin')
@section('page-title', 'Classes & Sections')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
    <div>
        <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">Classes & Sections</h2>
        <p style="font-size:12px;color:#64748b;margin:3px 0 0;">Manage class names and sections. Classes with only one section auto-fill in all dropdowns.</p>
    </div>
</div>

@if(session('success'))
<div style="background:#dcfce7;color:#15803d;border-radius:10px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:600;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fee2e2;color:#b91c1c;border-radius:10px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:600;">{{ session('error') }}</div>
@endif

{{-- Classes grid --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:14px;">
    @foreach($classes as $class)
    @php $classSections = $sections->get($class, collect()); @endphp
    <div style="background:#fff;border-radius:14px;box-shadow:0 1px 8px rgba(0,0,0,.06);border:1px solid #f1f5f9;overflow:hidden;">
        {{-- Class header --}}
        <div style="padding:14px 18px;background:linear-gradient(135deg,#0f766e,#0d9488);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:16px;font-weight:800;color:#fff;">{{ $class }}</div>
                <div style="font-size:11px;color:#ccfbf1;">{{ $classSections->count() }} section(s)</div>
            </div>
            <div style="display:flex;gap:4px;">
                @if($classSections->count() <= 1)
                <span style="background:rgba(255,255,255,.2);color:#fff;padding:2px 8px;border-radius:99px;font-size:9px;font-weight:700;">Auto-fill</span>
                @endif
            </div>
        </div>

        {{-- Sections list --}}
        <div style="padding:12px 18px;">
            @if($classSections->isNotEmpty())
            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px;">
                @foreach($classSections as $section)
                <div style="display:flex;align-items:center;gap:6px;background:#f0fdfa;border:1px solid #99f6e4;border-radius:8px;padding:5px 10px;">
                    <span style="font-weight:700;color:#0f766e;font-size:13px;">{{ $section->name }}</span>
                    <form method="POST" action="{{ route('admin.sections.destroy', $section) }}"
                          onsubmit="return confirm('Delete section {{ $section->name }} for {{ $class }}?')" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none;border:none;color:#94a3b8;cursor:pointer;padding:0;font-size:14px;line-height:1;" title="Delete section">✕</button>
                    </form>
                </div>
                @endforeach
            </div>
            @else
            <div style="font-size:12px;color:#94a3b8;margin-bottom:10px;">No sections defined yet.</div>
            @endif

            {{-- Add section form --}}
            <form method="POST" action="{{ route('admin.sections.store') }}" style="display:flex;gap:6px;">
                @csrf
                <input type="hidden" name="class" value="{{ $class }}">
                <input type="text" name="name" placeholder="e.g. A" maxlength="20" required
                       style="flex:1;border:1px solid #e2e8f0;border-radius:7px;padding:6px 10px;font-size:12px;">
                <button type="submit"
                        style="background:#0f766e;color:#fff;border:none;padding:6px 14px;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;">Add</button>
            </form>
        </div>
    </div>
    @endforeach
</div>

{{-- Add new class form --}}
<div style="background:#fff;border-radius:14px;box-shadow:0 1px 8px rgba(0,0,0,.06);border:1px solid #f1f5f9;padding:18px 20px;margin-top:16px;">
    <h3 style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 10px;">Create New Class</h3>
    <p style="font-size:11px;color:#94a3b8;margin:0 0 12px;">Adding a section for a new class automatically registers the class in the system.</p>
    <form method="POST" action="{{ route('admin.sections.store') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
        @csrf
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:3px;">Class Name *</label>
            <input type="text" name="class" placeholder="e.g. Nursery" required maxlength="30"
                   style="border:1px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;width:160px;">
        </div>
        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:3px;">First Section *</label>
            <input type="text" name="name" placeholder="e.g. A" required maxlength="20"
                   style="border:1px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;width:100px;">
        </div>
        <button type="submit"
                style="background:linear-gradient(135deg,#0f766e,#14b8a6);color:#fff;border:none;padding:9px 20px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">Create Class</button>
    </form>
</div>

@endsection
