@extends('layouts.admin')
@section('page-title', 'Downloads')

@section('content')
@php
    $catColors = [
        'Notice'         => ['#1d4ed8','#dbeafe'],
        'Circular'       => ['#7c3aed','#ede9fe'],
        'Admission Form' => ['#0f766e','#ccfbf1'],
        'Syllabus'       => ['#c2410c','#ffedd5'],
        'Result'         => ['#be123c','#ffe4e6'],
        'Other'          => ['#475569','#f1f5f9'],
    ];
@endphp

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">Downloads</h2>
        <p style="font-size:12px; color:#64748b; margin:3px 0 0;">Notices, circulars, forms and documents</p>
    </div>
    <a href="{{ route('admin.downloads.create') }}"
       style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:600; padding:10px 18px; border-radius:10px; text-decoration:none; display:flex; align-items:center; gap:7px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Upload File
    </a>
</div>

{{-- Category tabs --}}
<div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:16px;">
    <a href="{{ route('admin.downloads.index') }}"
       style="padding:7px 14px; border-radius:99px; font-size:12px; font-weight:600; text-decoration:none; border:1.5px solid {{ !$category ? '#0f766e' : '#e2e8f0' }}; background:{{ !$category ? '#0f766e' : '#f8fafc' }}; color:{{ !$category ? '#fff' : '#475569' }};">All</a>
    @foreach($categories as $cat)
        <a href="{{ route('admin.downloads.index', ['category' => $cat]) }}"
           style="padding:7px 14px; border-radius:99px; font-size:12px; font-weight:600; text-decoration:none; border:1.5px solid {{ $category === $cat ? '#0f766e' : '#e2e8f0' }}; background:{{ $category === $cat ? '#0f766e' : '#f8fafc' }}; color:{{ $category === $cat ? '#fff' : '#475569' }};">{{ $cat }}</a>
    @endforeach
</div>

<div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; overflow:hidden;">
    @forelse($downloads as $d)
    @php $cc = $catColors[$d->category] ?? $catColors['Other']; @endphp
    <div style="display:flex; align-items:center; gap:12px; padding:13px 18px; border-bottom:1px solid #f8fafc; flex-wrap:wrap;">
        <div style="flex:1; min-width:180px;">
            <div style="font-weight:600; color:#0f172a; font-size:13.5px;">{{ $d->title }}</div>
            <div style="font-size:11px; color:#94a3b8;">{{ strtoupper($d->file_type) }} · {{ $d->file_size }} · {{ $d->created_at->format('d M Y') }}</div>
        </div>
        <span style="background:{{ $cc[1] }}; color:{{ $cc[0] }}; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600;">{{ $d->category }}</span>
        <span style="background:{{ $d->is_published ? '#dcfce7' : '#fee2e2' }}; color:{{ $d->is_published ? '#16a34a' : '#dc2626' }}; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600;">{{ $d->is_published ? 'Published' : 'Hidden' }}</span>
        <div style="display:flex; gap:5px;">
            <a href="{{ \App\Helpers\Settings::storageUrl($d->file_path) }}" target="_blank" style="background:#f0fdfa; color:#0f766e; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none;">Open</a>
            <a href="{{ route('admin.downloads.edit', $d) }}" style="background:#f0f9ff; color:#0369a1; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none;">Edit</a>
            <form method="POST" action="{{ route('admin.downloads.destroy', $d) }}" onsubmit="return confirm('Delete this file?')">
                @csrf @method('DELETE')
                <button type="submit" style="background:#fff1f2; color:#e11d48; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; border:none; cursor:pointer;">Del</button>
            </form>
        </div>
    </div>
    @empty
    <div style="padding:50px 20px; text-align:center;">
        <p style="font-size:14px; font-weight:600; color:#334155; margin:0 0 6px;">No downloads yet</p>
        <a href="{{ route('admin.downloads.create') }}" style="background:#0f766e; color:#fff; padding:9px 18px; border-radius:9px; font-size:13px; font-weight:600; text-decoration:none;">Upload First File</a>
    </div>
    @endforelse

    @if($downloads->hasPages())
    <div style="padding:14px 20px; border-top:1px solid #f1f5f9;">{{ $downloads->links() }}</div>
    @endif
</div>
@endsection
