@extends('layouts.admin')
@section('page-title', 'Students')

@push('styles')
<style>
.stu-desktop { display:none; }
.stu-mobile  { display:block; }
@media (min-width: 640px) {
    .stu-desktop { display:grid !important; }
    .stu-mobile  { display:none !important; }
}
</style>
@endpush

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">Students</h2>
        <p style="font-size:12px; color:#64748b; margin:3px 0 0;">{{ $totalStudents }} students across all classes</p>
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <a href="{{ route('admin.members.import.form') }}"
           style="background:#fff; color:#0f766e; font-size:13px; font-weight:600; padding:10px 16px; border-radius:10px; text-decoration:none; display:flex; align-items:center; gap:7px; border:1px solid #99f6e4;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
            Import CSV
        </a>
        <a href="{{ route('admin.members.create', $currentClass ? ['class' => $currentClass] : []) }}"
           style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:600; padding:10px 18px; border-radius:10px; text-decoration:none; display:flex; align-items:center; gap:7px; box-shadow:0 4px 12px rgba(20,184,166,.3);">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Add Student
        </a>
    </div>
</div>

{{-- Class filter tabs --}}
<div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:16px;">
    <a href="{{ route('admin.members.index') }}"
       style="padding:7px 14px; border-radius:99px; font-size:12px; font-weight:600; text-decoration:none; border:1.5px solid {{ !$currentClass ? '#0f766e' : '#e2e8f0' }}; background:{{ !$currentClass ? '#0f766e' : '#f8fafc' }}; color:{{ !$currentClass ? '#fff' : '#0f766e' }}; display:flex; align-items:center; gap:5px;">
        All
        <span style="background:{{ !$currentClass ? 'rgba(255,255,255,.25)' : '#0f766e' }}; color:#fff; border-radius:99px; font-size:10px; padding:1px 7px; font-weight:700;">{{ $totalStudents }}</span>
    </a>
    @foreach($classes as $class)
        @php $isActive = $currentClass === $class; @endphp
        <a href="{{ route('admin.members.index', ['class' => $class]) }}"
           style="padding:7px 14px; border-radius:99px; font-size:12px; font-weight:600; text-decoration:none; border:1.5px solid {{ $isActive ? '#0f766e' : '#e2e8f0' }}; background:{{ $isActive ? '#0f766e' : '#f8fafc' }}; color:{{ $isActive ? '#fff' : '#475569' }}; display:flex; align-items:center; gap:5px;">
            {{ $class }}
            <span style="background:{{ $isActive ? 'rgba(255,255,255,.25)' : '#cbd5e1' }}; color:#fff; border-radius:99px; font-size:10px; padding:1px 7px; font-weight:700;">{{ $classCounts[$class] }}</span>
        </a>
    @endforeach
</div>

{{-- Search --}}
<form method="GET" style="margin-bottom:16px; display:flex; gap:10px; flex-wrap:wrap;">
    @if($currentClass)<input type="hidden" name="class" value="{{ $currentClass }}">@endif
    <input name="search" value="{{ request('search') }}"
           placeholder="Search by name, roll no. or father's name…"
           style="flex:1; min-width:200px; max-width:380px; padding:10px 14px; border-radius:10px; border:1px solid #e2e8f0; font-size:13px; color:#0f172a; background:#fff; outline:none;">
    <button type="submit" style="background:#0f766e; color:#fff; border:none; padding:10px 18px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer;">Search</button>
    @if(request('search'))
    <a href="{{ route('admin.members.index', $currentClass ? ['class'=>$currentClass] : []) }}" style="background:#f1f5f9; color:#64748b; padding:10px 14px; border-radius:10px; font-size:13px; text-decoration:none; display:flex; align-items:center;">Clear</a>
    @endif
</form>

{{-- Overview cards when no class selected and not searching --}}
@if(!$currentClass && !request('search'))
<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(170px,1fr)); gap:12px; margin-bottom:8px;">
    @foreach($classes as $class)
    <a href="{{ route('admin.members.index', ['class' => $class]) }}"
       style="text-decoration:none; background:#fff; border:1px solid #f1f5f9; border-radius:14px; padding:16px 18px; box-shadow:0 1px 8px rgba(0,0,0,.05); display:block;">
        <div style="font-size:13px; font-weight:700; color:#0f172a;">{{ $class }}</div>
        <div style="font-size:22px; font-weight:800; color:#0f766e; margin:6px 0 2px;">{{ $classCounts[$class] }}</div>
        <div style="font-size:11px; color:#94a3b8;">student{{ $classCounts[$class] == 1 ? '' : 's' }} &nbsp;·&nbsp; View list →</div>
    </a>
    @endforeach
</div>
@if($unassigned > 0)
<p style="font-size:12px; color:#d97706; margin:10px 0 0;">{{ $unassigned }} student(s) have no class assigned — edit them to set a class.</p>
@endif
@else

{{-- Student table --}}
<div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; overflow:hidden;">
    <div class="stu-desktop" style="grid-template-columns:60px 2fr 1.4fr 1fr 90px 130px; gap:12px; padding:12px 20px; background:#f8fafc; border-bottom:1px solid #e2e8f0; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.06em;">
        <div>Roll</div><div>Name</div><div>Father's Name</div><div>Phone</div><div>Active</div><div style="text-align:right;">Actions</div>
    </div>

    @forelse($members as $member)
    {{-- Desktop row --}}
    <div class="stu-desktop" style="grid-template-columns:60px 2fr 1.4fr 1fr 90px 130px; gap:12px; padding:13px 20px; border-bottom:1px solid #f8fafc; align-items:center; font-size:13px;">
        <div style="color:#94a3b8; font-weight:600;">{{ $member->roll_number ?? '—' }}</div>
        <div style="display:flex; align-items:center; gap:10px;">
            @if($member->photo)
                <img src="{{ \App\Helpers\Settings::storageUrl($member->photo) }}" style="width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0;">
            @else
                <div style="width:34px;height:34px;border-radius:50%;background:#0f766e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff;flex-shrink:0;">{{ strtoupper(substr($member->name,0,1)) }}</div>
            @endif
            <div>
                <div style="font-weight:600; color:#0f172a;">{{ $member->name }}</div>
                <div style="font-size:11px; color:#94a3b8;">{{ $member->class ?? 'No class' }}</div>
            </div>
        </div>
        <div style="color:#475569;">{{ $member->father_name ?? '—' }}</div>
        <div style="color:#475569;">{{ $member->parent_phone ?? $member->phone ?? '—' }}</div>
        <div>
            <span style="background:{{ $member->is_active ? '#dcfce7' : '#fee2e2' }}; color:{{ $member->is_active ? '#16a34a' : '#dc2626' }}; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600;">{{ $member->is_active ? 'Yes' : 'No' }}</span>
        </div>
        <div style="display:flex; align-items:center; justify-content:flex-end; gap:5px;">
            <a href="{{ route('admin.members.show', $member) }}" style="background:#f0fdfa; color:#0f766e; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none;">View</a>
            <a href="{{ route('admin.members.edit', $member) }}" style="background:#f0f9ff; color:#0369a1; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none;">Edit</a>
            <form method="POST" action="{{ route('admin.members.destroy', $member) }}" onsubmit="return confirm('Delete {{ addslashes($member->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" style="background:#fff1f2; color:#e11d48; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; border:none; cursor:pointer;">Del</button>
            </form>
        </div>
    </div>

    {{-- Mobile card --}}
    <div class="stu-mobile" style="padding:14px 16px; border-bottom:1px solid #f1f5f9;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
            @if($member->photo)
                <img src="{{ \App\Helpers\Settings::storageUrl($member->photo) }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0;">
            @else
                <div style="width:40px;height:40px;border-radius:50%;background:#0f766e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;color:#fff;flex-shrink:0;">{{ strtoupper(substr($member->name,0,1)) }}</div>
            @endif
            <div style="flex:1; min-width:0;">
                <div style="font-weight:700; color:#0f172a; font-size:14px;">{{ $member->name }}</div>
                <div style="font-size:11px; color:#94a3b8;">Roll {{ $member->roll_number ?? '—' }} · {{ $member->class ?? 'No class' }}</div>
            </div>
            <span style="background:{{ $member->is_active ? '#dcfce7' : '#fee2e2' }}; color:{{ $member->is_active ? '#16a34a' : '#dc2626' }}; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600;">{{ $member->is_active ? 'Active' : 'Inactive' }}</span>
        </div>
        <div style="display:flex; gap:6px;">
            <a href="{{ route('admin.members.show', $member) }}" style="flex:1; background:#f0fdfa; color:#0f766e; padding:7px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none; text-align:center;">View</a>
            <a href="{{ route('admin.members.edit', $member) }}" style="flex:1; background:#f0f9ff; color:#0369a1; padding:7px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none; text-align:center;">Edit</a>
            <form method="POST" action="{{ route('admin.members.destroy', $member) }}" onsubmit="return confirm('Delete {{ addslashes($member->name) }}?')" style="flex:1;">
                @csrf @method('DELETE')
                <button type="submit" style="width:100%; background:#fff1f2; color:#e11d48; padding:7px; border-radius:8px; font-size:12px; font-weight:600; border:none; cursor:pointer;">Delete</button>
            </form>
        </div>
    </div>

    @empty
    <div style="padding:60px 20px; text-align:center;">
        <p style="font-size:14px; font-weight:600; color:#334155; margin:0 0 6px;">No students found</p>
        <p style="font-size:13px; color:#94a3b8; margin:0 0 16px;">{{ request('search') ? 'Try a different search.' : 'Add the first student to this class.' }}</p>
        <a href="{{ route('admin.members.create', $currentClass ? ['class'=>$currentClass] : []) }}" style="background:#0f766e; color:#fff; padding:9px 18px; border-radius:9px; font-size:13px; font-weight:600; text-decoration:none;">Add Student</a>
    </div>
    @endforelse

    @if($members->hasPages())
    <div style="padding:14px 20px; border-top:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
        <div style="font-size:12px; color:#94a3b8;">Showing {{ $members->firstItem() }}–{{ $members->lastItem() }} of {{ $members->total() }}</div>
        {{ $members->links() }}
    </div>
    @endif
</div>
@endif
@endsection
