@extends('layouts.admin')
@section('page-title', 'Teachers')

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">Teachers</h2>
        <p style="font-size:12px; color:#64748b; margin:3px 0 0;">Staff directory — teachers and the classes they handle (no login)</p>
    </div>
    <a href="{{ route('admin.teachers.create') }}"
       style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:600; padding:10px 18px; border-radius:10px; text-decoration:none; display:flex; align-items:center; gap:7px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Add Teacher
    </a>
</div>

<div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; overflow:hidden;">
    @forelse($teachers as $teacher)
    <div style="display:flex; align-items:center; gap:14px; padding:14px 18px; border-bottom:1px solid #f8fafc; flex-wrap:wrap;">
        <div style="width:40px; height:40px; border-radius:50%; flex-shrink:0; overflow:hidden; background:linear-gradient(135deg,#14b8a6,#0f766e); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:14px;">
            @if($teacher->photo)
                <img src="{{ \App\Helpers\Settings::storageUrl($teacher->photo) }}" style="width:100%; height:100%; object-fit:cover;">
            @else
                {{ strtoupper(substr($teacher->name, 0, 1)) }}
            @endif
        </div>
        <div style="flex:1; min-width:170px;">
            <div style="font-weight:600; color:#0f172a; font-size:13.5px;">{{ $teacher->name }} @unless($teacher->is_active)<span style="font-size:10px;color:#94a3b8;font-weight:600;">(hidden)</span>@endunless</div>
            <div style="font-size:11px; color:#94a3b8;">{{ $teacher->designation ?: 'Teacher' }}@if($teacher->subjects) · {{ $teacher->subjects }}@endif</div>
        </div>
        <div style="display:flex; gap:5px; flex-wrap:wrap; flex:1; min-width:160px;">
            @forelse(($teacher->classes ?? []) as $c)
                <span style="background:#ecfeff; color:#0e7490; padding:2px 9px; border-radius:99px; font-size:11px; font-weight:600;">{{ $c }}</span>
            @empty
                <span style="font-size:11px; color:#cbd5e1;">No classes assigned</span>
            @endforelse
        </div>
        <div style="display:flex; gap:5px;">
            <a href="{{ route('admin.teachers.edit', $teacher) }}" style="background:#f0f9ff; color:#0369a1; padding:5px 12px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none;">Edit</a>
            <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}" onsubmit="return confirm('Delete teacher {{ $teacher->name }}?')">
                @csrf @method('DELETE')
                <button type="submit" style="background:#fff1f2; color:#e11d48; padding:5px 12px; border-radius:7px; font-size:11px; font-weight:600; border:none; cursor:pointer;">Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div style="padding:50px 20px; text-align:center;">
        <p style="font-size:14px; font-weight:600; color:#334155; margin:0 0 6px;">No teachers yet</p>
        <a href="{{ route('admin.teachers.create') }}" style="background:#0f766e; color:#fff; padding:9px 18px; border-radius:9px; font-size:13px; font-weight:600; text-decoration:none;">Add First Teacher</a>
    </div>
    @endforelse
</div>
@endsection
