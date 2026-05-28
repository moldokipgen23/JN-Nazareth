@extends('layouts.teacher')
@section('page-title', 'My Subjects')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <div>
        <h1 style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">My Subjects</h1>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">
            {{ $year ? $year->name : 'No active year' }} &middot; {{ $subjects->count() }} {{ Str::plural('subject', $subjects->count()) }}
        </div>
    </div>
</div>

@forelse($subjects as $s)
    <div style="display:flex;align-items:center;gap:14px;background:#fff;border-radius:12px;padding:16px;margin-bottom:10px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#fef3c7,#fde68a);color:#92400e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">
            {{ strtoupper(substr($s->subject, 0, 2)) }}
        </div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:15px;font-weight:600;color:#0f172a;">{{ $s->subject }}</div>
            <div style="font-size:12px;color:#64748b;margin-top:4px;">
                {{ $s->class }}{{ $s->section ? ' — Section '.$s->section : '' }}
            </div>
        </div>
        <a href="{{ route('admin.classes.show', ['class' => $s->class]) }}" style="display:flex;align-items:center;gap:4px;font-size:12px;font-weight:600;color:#0f766e;background:#f0fdf4;border:1px solid #bbf7d0;padding:6px 12px;border-radius:8px;text-decoration:none;white-space:nowrap;">
            View Class
        </a>
    </div>
@empty
    <div style="background:#fff;border-radius:14px;padding:48px 24px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="font-size:36px;opacity:.3;margin-bottom:10px;">📚</div>
        <div style="font-weight:600;color:#475569;font-size:15px;">No subjects assigned</div>
        <div style="font-size:13px;color:#94a3b8;margin-top:6px;">The admin can assign you subjects under <em>Teacher Assignments</em>.</div>
    </div>
@endforelse

@endsection
