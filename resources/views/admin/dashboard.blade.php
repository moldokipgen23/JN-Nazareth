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
.table-row:hover { background:#fafbfc; }
.avatar { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:#fff; flex-shrink:0; }
.action-badge { display:inline-flex; align-items:center; padding:2px 9px; border-radius:99px; font-size:11px; font-weight:600; }
.event-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; margin-top:4px; }
</style>
@endpush

@section('content')

{{-- Welcome bar --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
    <div>
        <h2 style="font-size:20px; font-weight:700; color:#0f172a; margin:0;">
            Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, {{ explode(' ', Auth::user()->name)[0] }} 👋
        </h2>
        <p style="font-size:13px; color:#64748b; margin:4px 0 0;">Here's what's happening at {{ \App\Helpers\Settings::get('site_name', 'your site') }} today.</p>
    </div>
    <a href="{{ route('admin.members.create') }}"
       style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:600; padding:10px 18px; border-radius:10px; text-decoration:none; display:flex; align-items:center; gap:7px; box-shadow:0 4px 12px rgba(20,184,166,.3);">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Add Member
    </a>
</div>

{{-- ══ Stat cards row 1 ══ --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:24px;">

    {{-- Members --}}
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;">
            <svg fill="none" stroke="#3b82f6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-2-3.46"/></svg>
        </div>
        <div>
            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1.1;">{{ number_format($stats['members']) }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:500; margin-top:2px;">Registered Members</div>
        </div>
    </div>

    {{-- Events --}}
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;">
            <svg fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1.1;">{{ number_format($stats['events']) }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:500; margin-top:2px;">Total Events</div>
            <div style="font-size:11px; color:#d97706; font-weight:600; margin-top:3px;">{{ $stats['upcoming'] }} upcoming</div>
        </div>
    </div>

    {{-- Blogs --}}
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;">
            <svg fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V7a2 2 0 012-2h10l4 4v11a2 2 0 01-2 2z"/></svg>
        </div>
        <div>
            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1.1;">{{ number_format($stats['blogs']) }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:500; margin-top:2px;">Blog Posts</div>
            <div style="font-size:11px; color:#16a34a; font-weight:600; margin-top:3px;">{{ $stats['published'] }} published</div>
        </div>
    </div>

    {{-- Documents --}}
    <div class="stat-card">
        <div class="stat-icon" style="background:#fdf4ff;">
            <svg fill="none" stroke="#9333ea" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
        </div>
        <div>
            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1.1;">{{ number_format($stats['documents']) }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:500; margin-top:2px;">Files Stored</div>
            <div style="font-size:11px; color:#9333ea; font-weight:600; margin-top:3px;">{{ $stats['folders'] }} folders</div>
        </div>
    </div>

    {{-- Gallery --}}
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff1f2;">
            <svg fill="none" stroke="#e11d48" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1.1;">{{ number_format($stats['gallery']) }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:500; margin-top:2px;">Gallery Photos</div>
        </div>
    </div>

    {{-- Users --}}
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdfa;">
            <svg fill="none" stroke="#0d9488" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
        </div>
        <div>
            <div style="font-size:26px; font-weight:800; color:#0f172a; line-height:1.1;">{{ number_format($stats['users']) }}</div>
            <div style="font-size:12px; color:#64748b; font-weight:500; margin-top:2px;">System Users</div>
        </div>
    </div>
</div>

{{-- ══ Chart + Upcoming Events ══ --}}
<div style="display:grid; grid-template-columns:1fr 360px; gap:20px; margin-bottom:24px;">

    {{-- Member Growth Chart --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Member Growth (Last 6 Months)</span>
            <span style="font-size:12px; color:#94a3b8;">{{ now()->subMonths(5)->format('M Y') }} — {{ now()->format('M Y') }}</span>
        </div>
        <div style="padding:20px;">
            <canvas id="memberChart" height="120"></canvas>
        </div>
    </div>

    {{-- Upcoming Events --}}
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
                <div style="font-size:11px; color:#94a3b8; margin-top:2px; display:flex; align-items:center; gap:3px;">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $event->location }}
                </div>
                @endif
                <div style="font-size:11px; color:#94a3b8; margin-top:2px;">{{ $event->starts_at->format('g:i A') }}</div>
            </div>
        </div>
        @empty
        <div style="padding:30px 20px; text-align:center; color:#94a3b8; font-size:13px;">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 8px; opacity:.4; display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            No upcoming events
        </div>
        @endforelse
    </div>
</div>

{{-- ══ Recent Members + Activity ══ --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

    {{-- Recent Members --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Recent Members</span>
            <a href="{{ route('admin.members.index') }}" style="font-size:12px; color:#0d9488; text-decoration:none; font-weight:600;">View all</a>
        </div>
        @forelse($recentMembers as $member)
        @php $colors = ['#3b82f6','#8b5cf6','#14b8a6','#f59e0b','#ef4444']; $c = $colors[$loop->index % count($colors)]; @endphp
        <div class="table-row" style="grid-template-columns:auto 1fr auto;">
            <div class="avatar" style="background:{{ $c }}; margin-right:6px;">
                {{ strtoupper(substr($member->name, 0, 1)) }}
            </div>
            <div>
                <div style="font-weight:600; color:#0f172a;">{{ $member->name }}</div>
                <div style="font-size:11px; color:#94a3b8;">{{ $member->role ?? 'Member' }} {{ $member->email ? '· '.$member->email : '' }}</div>
            </div>
            <div style="font-size:11px; color:#94a3b8;">{{ $member->created_at->diffForHumans() }}</div>
        </div>
        @empty
        <div style="padding:30px 20px; text-align:center; color:#94a3b8; font-size:13px;">No members yet.
            <a href="{{ route('admin.members.create') }}" style="color:#0d9488; font-weight:600;">Add first member →</a>
        </div>
        @endforelse
    </div>

    {{-- Recent Activity --}}
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
                    <span class="action-badge" style="background:{{ $bg }}; color:{{ $fg }}; margin-right:5px;">{{ str_replace('_',' ', $log->action) }}</span>
                </div>
                <div style="font-size:11px; color:#94a3b8;">{{ $log->user?->name ?? 'System' }} · {{ $log->ip_address }}</div>
            </div>
            <div style="font-size:11px; color:#94a3b8; white-space:nowrap;">{{ $log->created_at->diffForHumans(null, true) }}</div>
        </div>
        @empty
        <div style="padding:30px 20px; text-align:center; color:#94a3b8; font-size:13px;">No activity logged yet.</div>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labels  = @json(array_keys($months->toArray()));
const data    = @json(array_values($months->toArray()));

const prettyLabels = labels.map(m => {
    const [y, mo] = m.split('-');
    return new Date(y, mo - 1).toLocaleString('default', { month: 'short', year: '2-digit' });
});

new Chart(document.getElementById('memberChart'), {
    type: 'bar',
    data: {
        labels: prettyLabels,
        datasets: [{
            label: 'New Members',
            data: data,
            backgroundColor: 'rgba(13, 148, 136, 0.15)',
            borderColor: '#0d9488',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { backgroundColor: '#0f172a', padding: 10, cornerRadius: 8 }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1, color: '#94a3b8', font: { size: 11 } },
                grid: { color: '#f1f5f9' }
            },
            x: {
                ticks: { color: '#94a3b8', font: { size: 11 } },
                grid: { display: false }
            }
        }
    }
});
</script>
@endpush
