@extends('layouts.admin')
@section('page-title', 'Teachers')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <div>
        <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">Teachers</h2>
        <p style="font-size:12px;color:#64748b;margin:3px 0 0;">{{ $teachers->count() }} teacher{{ $teachers->count() !== 1 ? 's' : '' }}</p>
    </div>
    <a href="{{ route('admin.teachers.create') }}"
       style="background:linear-gradient(135deg,#0f766e,#14b8a6);color:#fff;font-size:13px;font-weight:600;padding:10px 18px;border-radius:10px;text-decoration:none;display:flex;align-items:center;gap:7px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Add Teacher
    </a>
</div>

@if(session('generated_password'))
<div style="background:#ecfdf5;border:1px solid #6ee7b7;border-radius:10px;padding:12px 16px;margin-bottom:16px;">
    <p style="font-size:13px;font-weight:700;color:#065f46;margin:0 0 2px;">Login created</p>
    <p style="font-size:12px;color:#047857;margin:0;">Temp password: <code style="background:#d1fae5;padding:2px 8px;border-radius:5px;font-weight:700;">{{ session('generated_password') }}</code></p>
</div>
@endif

@if($teachers->isNotEmpty())
@foreach($teachers as $teacher)
@php $loginUser = $teacher->users()->first(); @endphp
<div style="background:#fff;border:1px solid #eef2f6;border-radius:10px;padding:12px 14px;margin-bottom:8px;box-shadow:0 1px 3px rgba(0,0,0,.03);display:flex;align-items:center;gap:12px;flex-wrap:nowrap;">
    <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;overflow:hidden;background:linear-gradient(135deg,#14b8a6,#0f766e);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;">
        @if($teacher->photo)
            <img src="{{ \App\Helpers\Settings::storageUrl($teacher->photo) }}" style="width:100%;height:100%;object-fit:cover;">
        @else
            {{ strtoupper(substr($teacher->name, 0, 1)) }}
        @endif
    </div>
    <div style="flex:1;min-width:0;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        <div style="min-width:0;">
            <div style="font-weight:600;color:#0f172a;font-size:13px;display:flex;align-items:center;gap:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                {{ $teacher->name }}
                @if(!$teacher->is_active)
                    <span style="font-size:9px;background:#f1f5f9;color:#94a3b8;padding:1px 6px;border-radius:99px;font-weight:600;">Inactive</span>
                @endif
                @if($loginUser)
                    <span style="font-size:9px;background:#ecfdf5;color:#059669;padding:1px 6px;border-radius:99px;font-weight:700;">Login</span>
                @else
                    <span style="font-size:9px;background:#f8fafc;color:#94a3b8;padding:1px 6px;border-radius:99px;font-weight:600;">No login</span>
                @endif
            </div>
            <div style="font-size:11px;color:#94a3b8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px;">
                {{ $teacher->designation ?: 'Teacher' }}@if($teacher->subjects) · {{ $teacher->subjects }}@endif
            </div>
        </div>
        @php $classes = $teacher->classes ?? []; @endphp
        @if(!empty($classes))
            <div style="display:flex;flex-wrap:wrap;gap:3px;">
                @foreach(array_slice($classes, 0, 3) as $c)
                    <span style="background:#f0fdfa;color:#0f766e;padding:1px 7px;border-radius:99px;font-size:10px;font-weight:600;">{{ $c }}</span>
                @endforeach
                @if(count($classes) > 3)
                    <span style="font-size:10px;color:#94a3b8;">+{{ count($classes) - 3 }}</span>
                @endif
            </div>
        @endif
    </div>
    <div style="display:flex;gap:4px;flex-shrink:0;">
        <a href="{{ route('admin.teachers.show', $teacher) }}"
           style="background:#f0fdfa;color:#0f766e;padding:5px 10px;border-radius:6px;font-size:10px;font-weight:600;text-decoration:none;white-space:nowrap;">View</a>
        <a href="{{ route('admin.teachers.edit', $teacher) }}"
           style="background:#f0f9ff;color:#0369a1;padding:5px 10px;border-radius:6px;font-size:10px;font-weight:600;text-decoration:none;white-space:nowrap;">Edit</a>
        @if($loginUser)
            <form method="POST" action="{{ route('admin.teachers.unlink-user', [$teacher, $loginUser]) }}" style="display:inline;">
                @csrf
                <button type="submit" style="background:#f8fafc;color:#64748b;padding:5px 10px;border-radius:6px;font-size:10px;font-weight:600;border:none;cursor:pointer;white-space:nowrap;">Unlink</button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.teachers.create-login', $teacher) }}" style="display:inline;">
                @csrf
                <button type="submit" style="background:#ecfdf5;color:#059669;padding:5px 10px;border-radius:6px;font-size:10px;font-weight:600;border:none;cursor:pointer;white-space:nowrap;">Login</button>
            </form>
        @endif
        <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}" onsubmit="return confirm('Delete {{ $teacher->name }}?')" style="display:inline;">
            @csrf @method('DELETE')
            <button type="submit" style="background:#fff1f2;color:#e11d48;padding:5px 10px;border-radius:6px;font-size:10px;font-weight:600;border:none;cursor:pointer;white-space:nowrap;">Del</button>
        </form>
    </div>
</div>
@endforeach
@else
<div style="background:#fff;border-radius:10px;border:1px solid #eef2f6;padding:40px 20px;text-align:center;">
    <p style="font-size:14px;font-weight:600;color:#334155;margin:0 0 8px;">No teachers yet</p>
    <a href="{{ route('admin.teachers.create') }}" style="background:#0f766e;color:#fff;padding:9px 18px;border-radius:9px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:7px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Add First Teacher
    </a>
</div>
@endif
@endsection