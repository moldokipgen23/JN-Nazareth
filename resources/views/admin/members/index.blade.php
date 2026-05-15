@extends('layouts.admin')
@section('page-title', 'Members')

@push('styles')
<style>
.mem-desktop { display:none; }
.mem-mobile  { display:block; }
@media (min-width: 640px) {
    .mem-desktop { display:grid !important; }
    .mem-mobile  { display:none !important; }
}
</style>
@endpush

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">Church Members</h2>
        <p style="font-size:12px; color:#64748b; margin:3px 0 0;">{{ $counts['all'] }} total members registered</p>
    </div>
    <a href="{{ route('admin.members.create') }}"
       style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:600; padding:10px 18px; border-radius:10px; text-decoration:none; display:flex; align-items:center; gap:7px; box-shadow:0 4px 12px rgba(20,184,166,.3);">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Add Member
    </a>
</div>

{{-- Status Filter Tabs --}}
<div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:14px;">
    @php
        $currentStatus = request('status');
        $tabs = [
            ''          => ['label' => 'All',       'count' => $counts['all'],       'color' => '#0f766e', 'bg' => '#f0fdfa'],
            'active'    => ['label' => 'Active',    'count' => $counts['active'],    'color' => '#16a34a', 'bg' => '#f0fdf4'],
            'deceased'  => ['label' => 'Deceased',  'count' => $counts['deceased'],  'color' => '#6b7280', 'bg' => '#f9fafb'],
            'moved_out' => ['label' => 'Moved Out', 'count' => $counts['moved_out'], 'color' => '#d97706', 'bg' => '#fffbeb'],
            'inactive'  => ['label' => 'Inactive',  'count' => $counts['inactive'],  'color' => '#dc2626', 'bg' => '#fef2f2'],
        ];
    @endphp
    @foreach($tabs as $value => $tab)
        @php $isActive = $currentStatus === $value; @endphp
        <a href="{{ route('admin.members.index', array_merge(request()->except('status','page'), $value ? ['status'=>$value] : [])) }}"
           style="padding:7px 14px; border-radius:99px; font-size:12px; font-weight:600; text-decoration:none; border:1.5px solid {{ $isActive ? $tab['color'] : '#e2e8f0' }}; background:{{ $isActive ? $tab['color'] : $tab['bg'] }}; color:{{ $isActive ? '#fff' : $tab['color'] }}; display:flex; align-items:center; gap:5px; transition:all .15s;">
            {{ $tab['label'] }}
            <span style="background:{{ $isActive ? 'rgba(255,255,255,.25)' : $tab['color'] }}; color:{{ $isActive ? '#fff' : '#fff' }}; border-radius:99px; font-size:10px; padding:1px 7px; font-weight:700;">{{ $tab['count'] }}</span>
        </a>
    @endforeach
</div>

{{-- Search --}}
<form method="GET" style="margin-bottom:16px; display:flex; gap:10px; flex-wrap:wrap;">
    @if(request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
    @endif
    <input name="search" value="{{ request('search') }}"
           placeholder="Search by name or email…"
           style="flex:1; min-width:200px; padding:10px 14px; border-radius:10px; border:1px solid #e2e8f0; font-size:13px; color:#0f172a; background:#fff; outline:none; max-width:360px;"
           onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
    <button type="submit" style="background:#0f766e; color:#fff; border:none; padding:10px 18px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer;">Search</button>
    @if(request('search') || request('status'))
    <a href="{{ route('admin.members.index') }}" style="background:#f1f5f9; color:#64748b; padding:10px 14px; border-radius:10px; font-size:13px; text-decoration:none; display:flex; align-items:center;">Clear</a>
    @endif
</form>

<div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; overflow:hidden;">

    {{-- Desktop table header --}}
    <div class="mem-desktop" style="grid-template-columns:40px 2fr 1fr 1fr 100px 130px; gap:12px; padding:12px 20px; background:#f8fafc; border-bottom:1px solid #e2e8f0; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.06em;">
        <div>#</div><div>Name</div><div>Phone</div><div>Role</div><div>Status</div><div style="text-align:right;">Actions</div>
    </div>

    @forelse($members as $member)
    @php
        $colors = ['#3b82f6','#8b5cf6','#14b8a6','#f59e0b','#ef4444','#10b981','#f97316'];
        $c = $colors[$loop->index % count($colors)];
        $statusConfig = match($member->status ?? 'active') {
            'active'    => ['label' => 'Active',    'bg' => '#dcfce7', 'color' => '#16a34a'],
            'deceased'  => ['label' => 'Deceased',  'bg' => '#f3f4f6', 'color' => '#6b7280'],
            'moved_out' => ['label' => 'Moved Out', 'bg' => '#fef9c3', 'color' => '#ca8a04'],
            'inactive'  => ['label' => 'Inactive',  'bg' => '#fee2e2', 'color' => '#dc2626'],
            default     => ['label' => 'Active',    'bg' => '#dcfce7', 'color' => '#16a34a'],
        };
    @endphp

    {{-- Desktop row --}}
    <div class="mem-desktop" style="grid-template-columns:40px 2fr 1fr 1fr 100px 130px; gap:12px; padding:13px 20px; border-bottom:1px solid #f8fafc; align-items:center; font-size:13px;" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background=''">
        <div style="color:#94a3b8; font-size:12px;">{{ $members->firstItem() + $loop->index }}</div>
        <div style="display:flex; align-items:center; gap:10px;">
            @if($member->photo)
                <img src="{{ \App\Helpers\Settings::storageUrl($member->photo) }}" alt="{{ $member->name }}"
                     style="width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0;">
            @else
                <div style="width:34px; height:34px; border-radius:50%; background:{{ $c }}; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; color:#fff; flex-shrink:0;">
                    {{ strtoupper(substr($member->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <div style="font-weight:600; color:#0f172a;">{{ $member->name }}</div>
                <div style="font-size:11px; color:#94a3b8;">{{ $member->email ?? '—' }}</div>
            </div>
        </div>
        <div style="color:#475569;">{{ $member->phone ?? '—' }}</div>
        <div>
            <span style="background:#f0fdfa; color:#0f766e; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600;">{{ $member->role ?? 'Member' }}</span>
        </div>
        <div>
            <span style="background:{{ $statusConfig['bg'] }}; color:{{ $statusConfig['color'] }}; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600;">{{ $statusConfig['label'] }}</span>
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
    <div class="mem-mobile" style="padding:14px 16px; border-bottom:1px solid #f1f5f9;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
            @if($member->photo)
                <img src="{{ \App\Helpers\Settings::storageUrl($member->photo) }}" alt="{{ $member->name }}"
                     style="width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0;">
            @else
                <div style="width:40px; height:40px; border-radius:50%; background:{{ $c }}; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:15px; color:#fff; flex-shrink:0;">
                    {{ strtoupper(substr($member->name, 0, 1)) }}
                </div>
            @endif
            <div style="flex:1; min-width:0;">
                <div style="font-weight:700; color:#0f172a; font-size:14px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $member->name }}</div>
                <div style="font-size:11px; color:#94a3b8;">{{ $member->role ?? 'Member' }}</div>
            </div>
            <span style="background:{{ $statusConfig['bg'] }}; color:{{ $statusConfig['color'] }}; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; white-space:nowrap;">{{ $statusConfig['label'] }}</span>
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
        <svg width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 12px; display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        <p style="font-size:14px; font-weight:600; color:#334155; margin:0 0 6px;">No members found</p>
        <p style="font-size:13px; color:#94a3b8; margin:0 0 16px;">{{ request('search') ? 'Try a different search.' : 'Get started by adding the first member.' }}</p>
        <a href="{{ route('admin.members.create') }}" style="background:#0f766e; color:#fff; padding:9px 18px; border-radius:9px; font-size:13px; font-weight:600; text-decoration:none;">Add First Member</a>
    </div>
    @endforelse

    @if($members->hasPages())
    <div style="padding:14px 20px; border-top:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
        <div style="font-size:12px; color:#94a3b8;">Showing {{ $members->firstItem() }}–{{ $members->lastItem() }} of {{ $members->total() }}</div>
        {{ $members->links() }}
    </div>
    @endif
</div>
@endsection
