@extends('layouts.admin')
@section('page-title', 'Classes')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
    <div>
        <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">Classes</h2>
        <p style="font-size:12px;color:#64748b;margin:3px 0 0;">
            {{ $isAdmin ? 'Every class. Select one to view its students.' : 'The classes you are assigned to teach. Select one to view its students.' }}
            @if($activeYear)
                <span style="color:#0f766e;font-weight:700;">Active year: {{ $activeYear->name }}</span>
            @endif
        </p>
    </div>
    @if($isAdmin)
    <div style="display:flex;align-items:center;gap:8px;">
        <form method="POST" action="{{ route('admin.sections.store') }}" style="display:flex;gap:6px;align-items:center;">
            @csrf
            <input type="text" name="class" placeholder="New class name" maxlength="30" required
                   style="border:1px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;width:150px;">
            <input type="text" name="name" placeholder="Section" maxlength="20" required
                   style="border:1px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;width:80px;">
            <button type="submit"
                    style="background:linear-gradient(135deg,#0f766e,#14b8a6);color:#fff;border:none;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">+ Add Class</button>
        </form>
    </div>
    @endif
</div>

@if(empty($classes))
<div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; padding:50px 20px; text-align:center;">
    <p style="font-size:14px; font-weight:600; color:#334155; margin:0 0 4px;">No classes assigned</p>
    <p style="font-size:12px; color:#94a3b8; margin:0;">An administrator has not assigned you to any class yet.</p>
</div>
@else
<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(210px,1fr)); gap:14px;">
    @foreach($classes as $class => $count)
    <a href="{{ route('admin.classes.show', ['class' => $class]) }}"
       style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:20px; text-decoration:none; box-shadow:0 1px 8px rgba(0,0,0,.05);">
        <div style="width:44px; height:44px; border-radius:11px; background:linear-gradient(135deg,#0f766e,#14b8a6); display:flex; align-items:center; justify-content:center; margin-bottom:12px;">
            <svg width="22" height="22" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.42A12 12 0 0112 21a12 12 0 01-6.16-10.42L12 14z"/></svg>
        </div>
        <div style="font-size:15px; font-weight:700; color:#0f172a;">{{ $class }}</div>
        <div style="font-size:12px; color:#64748b; margin-top:3px;">{{ $count }} {{ Str::plural('student', $count) }}</div>
    </a>
    @endforeach
</div>
@endif
@endsection
