@extends('layouts.admin')
@section('page-title', 'Dashboard')

@push('styles')
<style>
.stat-card { background:#fff; border-radius:14px; padding:20px; display:flex; align-items:center; gap:16px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; }
.stat-icon { width:54px; height:54px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.stat-icon svg { width:24px; height:24px; }
.card { background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; overflow:hidden; }
.card-header { padding:16px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; }
.card-title { font-size:14px; font-weight:700; color:#0f172a; }
.table-row { display:grid; padding:11px 20px; border-bottom:1px solid #f8fafc; align-items:center; font-size:13px; }
.table-row:last-child { border-bottom:none; }
.avatar { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:#fff; flex-shrink:0; }
.action-badge { display:inline-flex; align-items:center; padding:2px 9px; border-radius:99px; font-size:11px; font-weight:600; }
.qa-btn { display:flex; align-items:center; gap:8px; padding:11px 16px; border-radius:10px; font-size:13px; font-weight:600; text-decoration:none; }
@media (max-width: 640px) {
    .dash-two-col { grid-template-columns:1fr !important; }
    .dash-event-side { grid-template-columns:1fr !important; }
}
</style>
@endpush

@section('content')

{{-- Welcome bar --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; flex-wrap:wrap; gap:10px;">
    <div>
        <h2 style="font-size:20px; font-weight:700; color:#0f172a; margin:0;">
            Welcome to {{ \App\Helpers\Settings::get('school_name', 'JN Nazareth School') }} CMS
        </h2>
        <p style="font-size:13px; color:#64748b; margin:4px 0 0;">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ explode(' ', Auth::user()->name)[0] }} — here's an overview of your school.</p>
    </div>
</div>

{{-- Quick actions --}}
<div style="display:flex; gap:10px; flex-wrap:wrap; margin:18px 0 24px;">
    <a href="{{ route('admin.students.create') }}" class="qa-btn" style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff;">+ Add Student</a>
    <a href="{{ route('admin.blogs.create') }}" class="qa-btn" style="background:#eff6ff; color:#2563eb;">+ Add News</a>
    <a href="{{ route('admin.downloads.create') }}" class="qa-btn" style="background:#f0fdf4; color:#16a34a;">+ Upload Download</a>
    <a href="{{ route('admin.inquiries.index') }}" class="qa-btn" style="background:{{ $stats['new_inquiries'] > 0 ? '#fee2e2' : '#f1f5f9' }}; color:{{ $stats['new_inquiries'] > 0 ? '#dc2626' : '#64748b' }};">
        View Inquiries @if($stats['new_inquiries'] > 0)({{ $stats['new_inquiries'] }} new)@endif
    </a>
</div>

{{-- Stat cards --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:24px;">

    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;">
            <svg fill="none" stroke="#3b82f6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-2-3.46"/></svg>
        </div>
        <div>
            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1.1;">{{ number_format($stats['active_students']) }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:500; margin-top:2px;">Active Students</div>
            <div style="font-size:11px; color:#3b82f6; font-weight:600; margin-top:3px;">{{ $stats['total_students'] }} total on record</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;">
            <svg fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V7a2 2 0 012-2h10l4 4v11a2 2 0 01-2 2z"/></svg>
        </div>
        <div>
            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1.1;">{{ number_format($stats['news']) }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:500; margin-top:2px;">Published News &amp; Notices</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:#fff1f2;">
            <svg fill="none" stroke="#e11d48" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1.1;">{{ number_format($stats['gallery']) }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:500; margin-top:2px;">Gallery Albums</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:#fdf4ff;">
            <svg fill="none" stroke="#9333ea" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        </div>
        <div>
            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1.1;">{{ number_format($stats['downloads']) }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:500; margin-top:2px;">Downloads</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;">
            <svg fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1.1;">{{ number_format($stats['events']) }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:500; margin-top:2px;">Events</div>
            <div style="font-size:11px; color:#d97706; font-weight:600; margin-top:3px;">{{ $stats['upcoming'] }} upcoming</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:{{ $stats['new_inquiries'] > 0 ? '#fee2e2' : '#f0fdfa' }};">
            <svg fill="none" stroke="{{ $stats['new_inquiries'] > 0 ? '#dc2626' : '#0d9488' }}" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1.1;">{{ number_format($stats['new_inquiries']) }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:500; margin-top:2px;">New Inquiries</div>
        </div>
    </div>
</div>

{{-- Students by class + Upcoming events --}}
<div class="dash-event-side" style="display:grid; grid-template-columns:1fr 360px; gap:20px; margin-bottom:24px;">

    <div class="card">
        <div class="card-header">
            <span class="card-title">Students by Class</span>
            <a href="{{ route('admin.students.index') }}" style="font-size:12px; color:#0d9488; text-decoration:none; font-weight:600;">View all</a>
        </div>
        <div style="padding:14px 20px; display:grid; grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); gap:10px;">
            @foreach($classCounts as $class => $count)
            <a href="{{ route('admin.students.index', ['class' => $class]) }}"
               style="text-decoration:none; background:#f8fafc; border:1px solid #f1f5f9; border-radius:10px; padding:10px 12px;">
                <div style="font-size:12px; font-weight:600; color:#475569;">{{ $class }}</div>
                <div style="font-size:18px; font-weight:800; color:#0f766e; margin-top:2px;">{{ $count }}</div>
            </a>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Upcoming Events</span>
            <a href="{{ route('admin.events.index') }}" style="font-size:12px; color:#0d9488; text-decoration:none; font-weight:600;">View all</a>
        </div>
        @forelse($upcomingEvents as $event)
        <div style="display:flex; gap:14px; padding:14px 20px; border-bottom:1px solid #f8fafc; align-items:flex-start;">
            <div style="background:#f0fdfa; border-radius:10px; padding:8px 10px; text-align:center; flex-shrink:0; min-width:44px;">
                <div style="font-size:18px; font-weight:800; color:#0f766e; line-height:1;">{{ $event->starts_at->format('d') }}</div>
                <div style="font-size:10px; color:#0d9488; font-weight:700; text-transform:uppercase;">{{ $event->starts_at->format('M') }}</div>
            </div>
            <div>
                <div style="font-size:13px; font-weight:600; color:#0f172a;">{{ $event->title }}</div>
                @if($event->location)
                <div style="font-size:11px; color:#94a3b8; margin-top:2px;">{{ $event->location }}</div>
                @endif
                <div style="font-size:11px; color:#94a3b8; margin-top:2px;">{{ $event->starts_at->format('g:i A') }}</div>
            </div>
        </div>
        @empty
        <div style="padding:30px 20px; text-align:center; color:#94a3b8; font-size:13px;">No upcoming events</div>
        @endforelse
    </div>
</div>

{{-- Recent students + activity --}}
<div class="dash-two-col" style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

    <div class="card">
        <div class="card-header">
            <span class="card-title">Recently Added Students</span>
            <a href="{{ route('admin.students.index') }}" style="font-size:12px; color:#0d9488; text-decoration:none; font-weight:600;">View all</a>
        </div>
        @forelse($recentStudents as $student)
        @php $colors = ['#3b82f6','#8b5cf6','#14b8a6','#f59e0b','#ef4444']; $c = $colors[$loop->index % count($colors)]; @endphp
        <div class="table-row" style="grid-template-columns:auto 1fr auto;">
            <div class="avatar" style="background:{{ $c }}; margin-right:6px;">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
            <div>
                <div style="font-weight:600; color:#0f172a;">{{ $student->name }}</div>
                <div style="font-size:11px; color:#94a3b8;">{{ $student->class ?? 'No class' }}{{ $student->roll_number ? ' · Roll '.$student->roll_number : '' }}</div>
            </div>
            <div style="font-size:11px; color:#94a3b8;">{{ $student->created_at->diffForHumans() }}</div>
        </div>
        @empty
        <div style="padding:30px 20px; text-align:center; color:#94a3b8; font-size:13px;">No students yet.
            <a href="{{ route('admin.students.create') }}" style="color:#0d9488; font-weight:600;">Add first student →</a>
        </div>
        @endforelse
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Recent Activity</span>
            <a href="{{ route('admin.activity-logs.index') }}" style="font-size:12px; color:#0d9488; text-decoration:none; font-weight:600;">View all</a>
        </div>
        @forelse($recentActivity as $log)
        @php
            $actionColors = [
                'uploaded' => ['#dcfce7','#16a34a'],
                'deleted'  => ['#fee2e2','#dc2626'],
                'created'  => ['#dbeafe','#2563eb'],
                'updated'  => ['#fef9c3','#ca8a04'],
                'login'    => ['#f0fdf4','#16a34a'],
            ];
            $key = collect(array_keys($actionColors))->first(fn($k) => str_contains($log->action, $k));
            [$bg, $fg] = $actionColors[$key] ?? ['#f1f5f9','#64748b'];
        @endphp
        <div class="table-row" style="grid-template-columns:auto 1fr auto;">
            <div class="avatar" style="background:#f1f5f9; color:#64748b; font-size:13px; margin-right:8px; width:28px; height:28px;">
                {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
            </div>
            <div>
                <div style="font-size:12.5px; font-weight:600; color:#334155;">
                    <span class="action-badge" style="background:{{ $bg }}; color:{{ $fg }};">{{ str_replace('_',' ', $log->action) }}</span>
                </div>
                <div style="font-size:11px; color:#94a3b8;">{{ $log->user?->name ?? 'System' }}</div>
            </div>
            <div style="font-size:11px; color:#94a3b8; white-space:nowrap;">{{ $log->created_at->diffForHumans(null, true) }}</div>
        </div>
        @empty
        <div style="padding:30px 20px; text-align:center; color:#94a3b8; font-size:13px;">No activity logged yet.</div>
        @endforelse
    </div>
</div>

@endsection
