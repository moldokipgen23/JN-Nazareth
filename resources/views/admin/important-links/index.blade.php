@extends('layouts.admin')
@section('page-title', 'Important Links')

@section('content')
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">Important Links</h2>
        <p style="font-size:12px; color:#64748b; margin:3px 0 0;">Useful external links and resources</p>
    </div>
    <a href="{{ route('admin.important-links.create') }}"
       style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:600; padding:10px 18px; border-radius:10px; text-decoration:none; display:flex; align-items:center; gap:7px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Add Link
    </a>
</div>

@if(session('success'))
<div style="background:#ecfdf5; border:1px solid #6ee7b7; color:#065f46; border-radius:10px; padding:11px 16px; margin-bottom:18px; font-size:13px; font-weight:600;">
    {{ session('success') }}
</div>
@endif

<div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; overflow:hidden;">
    @forelse($links as $link)
    <div style="display:flex; align-items:center; gap:12px; padding:13px 18px; border-bottom:1px solid #f8fafc; flex-wrap:wrap;">
        <div style="flex:1; min-width:180px;">
            <div style="font-weight:600; color:#0f172a; font-size:13.5px;">{{ $link->title }}</div>
            @if($link->description)
            <div style="font-size:12px; color:#64748b; margin-top:2px;">{{ \Illuminate\Support\Str::limit($link->description, 90) }}</div>
            @endif
            <div style="font-size:11px; color:#94a3b8;">{{ \Illuminate\Support\Str::limit($link->url, 60) }}</div>
        </div>
        <span style="background:{{ $link->is_published ? '#dcfce7' : '#fee2e2' }}; color:{{ $link->is_published ? '#16a34a' : '#dc2626' }}; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600;">{{ $link->is_published ? 'Published' : 'Hidden' }}</span>
        <div style="display:flex; gap:5px;">
            <a href="{{ $link->url }}" target="_blank" rel="noopener" style="background:#f0fdfa; color:#0f766e; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none;">Open</a>
            <a href="{{ route('admin.important-links.edit', $link) }}" style="background:#f0f9ff; color:#0369a1; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none;">Edit</a>
            <form method="POST" action="{{ route('admin.important-links.destroy', $link) }}" onsubmit="return confirm('Delete this link?')">
                @csrf @method('DELETE')
                <button type="submit" style="background:#fff1f2; color:#e11d48; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; border:none; cursor:pointer;">Del</button>
            </form>
        </div>
    </div>
    @empty
    <div style="padding:50px 20px; text-align:center;">
        <p style="font-size:14px; font-weight:600; color:#334155; margin:0 0 6px;">No links yet</p>
        <a href="{{ route('admin.important-links.create') }}" style="background:#0f766e; color:#fff; padding:9px 18px; border-radius:9px; font-size:13px; font-weight:600; text-decoration:none;">Add First Link</a>
    </div>
    @endforelse

    @if($links->hasPages())
    <div style="padding:14px 20px; border-top:1px solid #f1f5f9;">{{ $links->links() }}</div>
    @endif
</div>
@endsection
