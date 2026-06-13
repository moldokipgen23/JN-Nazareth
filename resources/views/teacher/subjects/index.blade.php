@extends('layouts.teacher')
@section('page-title', 'My Subjects')

@section('content')

<div style="margin-bottom:14px;">
    <h1 style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">My Subjects</h1>
    <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $year ? $year->name : 'No active year' }} &middot; {{ $subjects->count() }} {{ Str::plural('subject',$subjects->count()) }}</div>
</div>

@forelse($subjects as $s)
    <div style="display:flex;align-items:center;gap:12px;background:#fff;border-radius:12px;padding:14px;margin-bottom:8px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#fef3c7,#fde68a);color:#92400e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">{{ strtoupper(substr($s->subject,0,2)) }}</div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:14px;font-weight:600;color:#0f172a;">{{ $s->subject }}</div>
            <div style="font-size:12px;color:#64748b;margin-top:3px;">{{ $s->class }}{{ $s->section?' — Sec '.$s->section:'' }}</div>
        </div>
        <a href="{{ route('teacher.classes.show',['class'=>$s->class]) }}" style="font-size:12px;font-weight:600;color:#0f766e;background:#f0fdf4;border:1px solid #bbf7d0;padding:6px 12px;border-radius:8px;text-decoration:none;white-space:nowrap;">View Class</a>
    </div>
@empty
    <div class="card" style="padding:40px 20px;text-align:center;">
        <div style="font-size:14px;font-weight:600;color:#475569;">No subjects assigned</div>
        <div style="font-size:12px;color:#94a3b8;margin-top:4px;">The admin can assign you under <em>Teacher Assignments</em>.</div>
    </div>
@endforelse

@endsection