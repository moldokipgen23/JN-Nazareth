@extends('layouts.admin')
@section('page-title', 'Events')

@section('content')
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">Events</h2>
        <p style="font-size:12px; color:#64748b; margin:3px 0 0;">{{ $events->total() }} total events</p>
    </div>
    <a href="{{ route('admin.events.create') }}"
       style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:600; padding:10px 18px; border-radius:10px; text-decoration:none; display:flex; align-items:center; gap:7px; box-shadow:0 4px 12px rgba(20,184,166,.3);">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        New Event
    </a>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:16px;">
    @forelse($events as $event)
    @php $past = $event->starts_at->isPast(); @endphp
    <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.05); overflow:hidden; opacity:{{ $past ? '.65' : '1' }};">
        @if($event->image)
        <img src="{{ \App\Helpers\Settings::storageUrl($event->image) }}" alt="" style="width:100%; height:140px; object-fit:cover;">
        @else
        <div style="width:100%; height:100px; background:linear-gradient(135deg,#f0fdfa,#ccfbf1); display:flex; align-items:center; justify-content:center;">
            <svg width="36" height="36" fill="none" stroke="#0d9488" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:.4;"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        @endif
        <div style="padding:16px;">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin-bottom:8px;">
                <div style="font-size:14px; font-weight:700; color:#0f172a;">{{ $event->title }}</div>
                <span style="background:{{ $past ? '#f1f5f9' : '#f0fdf4' }}; color:{{ $past ? '#94a3b8' : '#16a34a' }}; padding:3px 9px; border-radius:99px; font-size:11px; font-weight:600; white-space:nowrap; flex-shrink:0;">
                    {{ $past ? 'Past' : 'Upcoming' }}
                </span>
            </div>
            <div style="font-size:12px; color:#64748b; margin-bottom:4px; display:flex; align-items:center; gap:5px;">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ $event->starts_at->format('D, d M Y · g:i A') }}
            </div>
            @if($event->location)
            <div style="font-size:12px; color:#64748b; display:flex; align-items:center; gap:5px;">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                {{ $event->location }}
            </div>
            @endif
            <div style="display:flex; gap:6px; margin-top:14px; padding-top:12px; border-top:1px solid #f1f5f9;">
                <a href="{{ route('admin.events.edit', $event) }}" style="flex:1; background:#f0f9ff; color:#0369a1; padding:7px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none; text-align:center;">Edit</a>
                <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Delete this event?')" style="flex:1;">
                    @csrf @method('DELETE')
                    <button type="submit" style="width:100%; background:#fff1f2; color:#e11d48; padding:7px; border-radius:8px; font-size:12px; font-weight:600; border:none; cursor:pointer;">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1; padding:60px 20px; text-align:center; background:#fff; border-radius:14px; border:1px solid #f1f5f9;">
        <svg width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 12px; display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <p style="font-size:14px; font-weight:600; color:#334155; margin:0 0 6px;">No events yet</p>
        <a href="{{ route('admin.events.create') }}" style="background:#0f766e; color:#fff; padding:9px 18px; border-radius:9px; font-size:13px; font-weight:600; text-decoration:none;">Create First Event</a>
    </div>
    @endforelse
</div>

@if($events->hasPages())
<div style="margin-top:16px;">{{ $events->links() }}</div>
@endif
@endsection
