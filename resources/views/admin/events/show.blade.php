@extends('layouts.admin')
@section('page-title', $event->title)

@section('content')

<div style="display:flex; align-items:center; gap:6px; font-size:13px; margin-bottom:20px;">
    <a href="{{ route('admin.events.index') }}" style="color:#0d9488; text-decoration:none; font-weight:500;">Events</a>
    <svg width="14" height="14" fill="none" stroke="#cbd5e1" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
    <span style="color:#0f172a; font-weight:700;">{{ $event->title }}</span>
</div>

<div style="display:grid; grid-template-columns:1fr 280px; gap:20px; align-items:start;">

    {{-- Main --}}
    <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); overflow:hidden;">
        @if($event->image)
        <img src="{{ \App\Helpers\Settings::storageUrl($event->image) }}" alt="{{ $event->title }}"
             style="width:100%; height:240px; object-fit:cover;">
        @endif
        <div style="padding:24px;">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
                <h1 style="font-size:20px; font-weight:700; color:#0f172a; margin:0;">{{ $event->title }}</h1>
                @php $past = $event->starts_at->isPast(); @endphp
                <span style="background:{{ $past ? '#f1f5f9' : '#f0fdf4' }}; color:{{ $past ? '#94a3b8' : '#16a34a' }}; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:700;">
                    {{ $past ? 'Past' : 'Upcoming' }}
                </span>
            </div>

            <div style="display:flex; flex-wrap:wrap; gap:16px; margin-bottom:20px; font-size:13px; color:#64748b;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span><strong>Starts:</strong> {{ $event->starts_at->format('D, d M Y · g:i A') }}</span>
                </div>
                @if($event->ends_at)
                <div style="display:flex; align-items:center; gap:6px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span><strong>Ends:</strong> {{ $event->ends_at->format('D, d M Y · g:i A') }}</span>
                </div>
                @endif
                @if($event->location)
                <div style="display:flex; align-items:center; gap:6px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    <span>{{ $event->location }}</span>
                </div>
                @endif
            </div>

            @if($event->description)
            <div style="font-size:14px; color:#374151; line-height:1.7; border-top:1px solid #f1f5f9; padding-top:16px;">
                {!! nl2br(e($event->description)) !!}
            </div>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div style="display:flex; flex-direction:column; gap:12px;">
        <a href="{{ route('admin.events.edit', $event) }}"
           style="display:block; background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; padding:11px 16px; border-radius:10px; font-size:13px; font-weight:600; text-decoration:none; text-align:center;">
            Edit Event
        </a>
        <a href="{{ route('admin.events.index') }}"
           style="display:block; background:#f1f5f9; color:#64748b; padding:11px 16px; border-radius:10px; font-size:13px; font-weight:600; text-decoration:none; text-align:center;">
            ← Back to Events
        </a>
        <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Delete this event?')">
            @csrf @method('DELETE')
            <button type="submit"
                    style="width:100%; background:#fff1f2; color:#e11d48; border:1px solid #fecdd3; padding:11px 16px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer;">
                Delete Event
            </button>
        </form>

        <div style="background:#f8fafc; border-radius:12px; border:1px solid #f1f5f9; padding:14px; font-size:12px; color:#64748b;">
            <div style="font-weight:700; color:#334155; margin-bottom:8px;">Event Info</div>
            <div style="display:flex; justify-content:space-between; padding:4px 0; border-bottom:1px solid #e2e8f0;">
                <span>Created</span><span style="font-weight:600; color:#0f172a;">{{ $event->created_at->format('d M Y') }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:4px 0;">
                <span>Status</span>
                <span style="font-weight:600; color:{{ $event->starts_at->isPast() ? '#94a3b8' : '#16a34a' }};">
                    {{ $event->starts_at->isPast() ? 'Past' : 'Upcoming' }}
                </span>
            </div>
        </div>
    </div>
</div>

@endsection
