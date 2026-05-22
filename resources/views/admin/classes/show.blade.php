@extends('layouts.admin')
@section('page-title', $class)

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">{{ $class }}</h2>
        <p style="font-size:12px; color:#64748b; margin:3px 0 0;">{{ $students->count() }} {{ Str::plural('student', $students->count()) }} in this class</p>
    </div>
    <a href="{{ route('admin.classes.index') }}" style="background:#f1f5f9; color:#475569; font-size:12px; font-weight:600; padding:8px 16px; border-radius:9px; text-decoration:none;">← All Classes</a>
</div>

<div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; overflow:hidden;">
    @forelse($students as $student)
    <div style="display:flex; align-items:center; gap:14px; padding:13px 18px; border-bottom:1px solid #f8fafc;">
        <div style="width:38px; height:38px; border-radius:50%; flex-shrink:0; overflow:hidden; background:#e2e8f0; display:flex; align-items:center; justify-content:center; color:#64748b; font-weight:700; font-size:13px;">
            @if($student->photo)
                <img src="{{ \App\Helpers\Settings::storageUrl($student->photo) }}" style="width:100%; height:100%; object-fit:cover;">
            @else
                {{ strtoupper(substr($student->name, 0, 1)) }}
            @endif
        </div>
        <div style="width:54px; flex-shrink:0;">
            <span style="font-size:11px; color:#94a3b8;">Roll</span>
            <div style="font-size:13px; font-weight:700; color:#0f172a;">{{ $student->roll_number ?: '—' }}</div>
        </div>
        <div style="width:54px; flex-shrink:0;">
            <span style="font-size:11px; color:#94a3b8;">Section</span>
            <div style="font-size:13px; font-weight:700; color:#0f172a;">{{ $student->section ?: '—' }}</div>
        </div>
        <div style="flex:1; min-width:140px;">
            <div style="font-weight:600; color:#0f172a; font-size:13.5px;">{{ $student->name }}</div>
            <div style="font-size:11px; color:#94a3b8;">
                {{ $student->father_name ? 'Father: ' . $student->father_name : '' }}
                {{ $student->parent_phone ? ' · ' . $student->parent_phone : '' }}
            </div>
        </div>
        <span style="background:{{ $student->is_active ? '#dcfce7' : '#fee2e2' }}; color:{{ $student->is_active ? '#16a34a' : '#dc2626' }}; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600;">{{ $student->is_active ? 'Active' : 'Inactive' }}</span>
    </div>
    @empty
    <div style="padding:50px 20px; text-align:center;">
        <p style="font-size:14px; font-weight:600; color:#334155; margin:0;">No students in this class</p>
        <p style="font-size:12px; color:#94a3b8; margin:5px 0 0;">Add students from the Students module.</p>
    </div>
    @endforelse
</div>
@endsection
