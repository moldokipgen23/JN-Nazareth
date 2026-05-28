@extends('layouts.admin')
@section('page-title', 'Blog Posts')

@section('content')
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">Blog Posts</h2>
        <p style="font-size:12px; color:#64748b; margin:3px 0 0;">{{ $blogs->total() }} posts total</p>
    </div>
    <a href="{{ route('admin.blogs.create') }}"
       style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:600; padding:10px 18px; border-radius:10px; text-decoration:none; display:flex; align-items:center; gap:7px; box-shadow:0 4px 12px rgba(20,184,166,.3);">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        New Post
    </a>
</div>

<div class="resp-table-wrap" style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9;">
    <div style="display:grid; grid-template-columns:2fr 1fr 120px 130px; gap:12px; padding:12px 20px; background:#f8fafc; border-bottom:1px solid #e2e8f0; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.06em;">
        <div>Title</div><div>Author</div><div>Status</div><div style="text-align:right;">Actions</div>
    </div>

    @forelse($blogs as $blog)
    <div style="display:grid; grid-template-columns:2fr 1fr 120px 130px; gap:12px; padding:14px 20px; border-bottom:1px solid #f8fafc; align-items:center; font-size:13px;" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background=''">
        <div>
            <div style="font-weight:600; color:#0f172a;">{{ $blog->title }}</div>
            <div style="font-size:11px; color:#94a3b8; margin-top:2px;">{{ $blog->created_at->format('d M Y') }} · /blogs/{{ $blog->slug }}</div>
        </div>
        <div style="font-size:12px; color:#475569;">{{ $blog->author?->name ?? '—' }}</div>
        <div>
            @if($blog->published)
            <span style="background:#f0fdf4; color:#16a34a; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600;">Published</span>
            @else
            <span style="background:#f8fafc; color:#94a3b8; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600;">Draft</span>
            @endif
        </div>
        <div style="display:flex; align-items:center; justify-content:flex-end; gap:5px;">
            <a href="{{ route('admin.blogs.edit', $blog) }}" style="background:#f0f9ff; color:#0369a1; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none;">Edit</a>
            <form method="POST" action="{{ route('admin.blogs.toggle-publish', $blog) }}">
                @csrf
                <button type="submit" style="background:{{ $blog->published ? '#fef9c3' : '#f0fdf4' }}; color:{{ $blog->published ? '#854d0e' : '#15803d' }}; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; border:none; cursor:pointer;">
                    {{ $blog->published ? 'Unpublish' : 'Publish' }}
                </button>
            </form>
            <form method="POST" action="{{ route('admin.blogs.destroy', $blog) }}" onsubmit="return confirm('Delete this post?')">
                @csrf @method('DELETE')
                <button type="submit" style="background:#fff1f2; color:#e11d48; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; border:none; cursor:pointer;">Del</button>
            </form>
        </div>
    </div>
    @empty
    <div style="padding:60px 20px; text-align:center;">
        <svg width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 12px; display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V7a2 2 0 012-2h10l4 4v11a2 2 0 01-2 2z"/></svg>
        <p style="font-size:14px; font-weight:600; color:#334155; margin:0 0 6px;">No posts yet</p>
        <a href="{{ route('admin.blogs.create') }}" style="background:#0f766e; color:#fff; padding:9px 18px; border-radius:9px; font-size:13px; font-weight:600; text-decoration:none;">Write First Post</a>
    </div>
    @endforelse

    @if($blogs->hasPages())
    <div style="padding:14px 20px; border-top:1px solid #f1f5f9;">{{ $blogs->links() }}</div>
    @endif
</div>
@endsection
