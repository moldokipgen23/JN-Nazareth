@extends('layouts.admin')
@section('page-title', 'Pages')

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">Pages</h2>
        <p style="font-size:12px; color:#64748b; margin:4px 0 0;">Manage all public-facing pages. Custom pages appear at <code style="font-size:11px; background:#f1f5f9; padding:1px 5px; border-radius:4px;">/page/{slug}</code></p>
    </div>
    <a href="{{ route('admin.pages.create') }}"
       style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; padding:9px 18px; border-radius:10px; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px; box-shadow:0 4px 12px rgba(20,184,166,.3);">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
        New Page
    </a>
</div>

@if(session('success'))
    <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:10px; font-size:13px; font-weight:500; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:#fff1f2; border:1px solid #fecdd3; color:#9f1239; padding:12px 16px; border-radius:10px; font-size:13px; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
@endif

{{-- Stats row --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(120px,1fr)); gap:12px; margin-bottom:20px;">
    @php
        $total     = $pages->count();
        $published = $pages->where('published', true)->count();
        $drafts    = $pages->where('published', false)->count();
        $inNav     = $pages->where('show_in_nav', true)->count();
    @endphp
    @foreach([
        ['label'=>'Total Pages',    'val'=>$total,     'color'=>'#0f766e', 'bg'=>'#f0fdfa'],
        ['label'=>'Published',      'val'=>$published, 'color'=>'#16a34a', 'bg'=>'#f0fdf4'],
        ['label'=>'Drafts',         'val'=>$drafts,    'color'=>'#d97706', 'bg'=>'#fffbeb'],
        ['label'=>'In Navigation',  'val'=>$inNav,     'color'=>'#7c3aed', 'bg'=>'#faf5ff'],
    ] as $stat)
    <div style="background:#fff; border-radius:12px; border:1px solid #f1f5f9; padding:14px 18px; box-shadow:0 1px 4px rgba(0,0,0,.04);">
        <div style="font-size:22px; font-weight:800; color:{{ $stat['color'] }};">{{ $stat['val'] }}</div>
        <div style="font-size:11px; font-weight:600; color:#64748b; margin-top:2px;">{{ $stat['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- Pages table --}}
<div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); overflow:hidden;">
    <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between;">
        <span style="font-size:13px; font-weight:700; color:#0f172a;">All Pages</span>
    </div>

    @if($pages->count())
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="background:#f8fafc; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.06em;">
                    <th style="padding:10px 20px; text-align:left; width:35%;">Title</th>
                    <th style="padding:10px 20px; text-align:left;">URL</th>
                    <th style="padding:10px 20px; text-align:left;">Status</th>
                    <th style="padding:10px 20px; text-align:left;">Navigation</th>
                    <th style="padding:10px 20px; text-align:left;">Updated</th>
                    <th style="padding:10px 20px; text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pages as $page)
                @php $isBuiltIn = in_array($page->slug, ['home', 'about']); @endphp
                <tr style="border-top:1px solid #f8fafc;" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background=''">
                    <td style="padding:13px 20px;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="width:32px; height:32px; border-radius:8px; background:{{ $isBuiltIn ? '#eff6ff' : '#f0fdfa' }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <svg width="14" height="14" fill="none" stroke="{{ $isBuiltIn ? '#3b82f6' : '#0f766e' }}" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <div style="font-weight:600; color:#0f172a;">{{ $page->title }}</div>
                                @if($page->meta_description)
                                    <div style="font-size:11px; color:#94a3b8; margin-top:1px; max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $page->meta_description }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="padding:13px 20px;">
                        <a href="{{ $isBuiltIn ? route($page->slug) : route('page', $page->slug) }}"
                           target="_blank"
                           style="font-size:11px; color:#0d9488; text-decoration:none; font-family:monospace; background:#f0fdfa; padding:3px 8px; border-radius:5px; border:1px solid #99f6e4; display:inline-flex; align-items:center; gap:4px;">
                            /{{ $isBuiltIn ? $page->slug : 'page/' . $page->slug }}
                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </td>
                    <td style="padding:13px 20px;">
                        @if($page->published)
                            <span style="font-size:10px; font-weight:700; padding:3px 10px; border-radius:20px; background:#f0fdf4; color:#16a34a; display:inline-flex; align-items:center; gap:4px;">
                                <span style="width:5px; height:5px; border-radius:50%; background:#16a34a; display:inline-block;"></span>
                                Published
                            </span>
                        @else
                            <span style="font-size:10px; font-weight:700; padding:3px 10px; border-radius:20px; background:#fffbeb; color:#d97706; display:inline-flex; align-items:center; gap:4px;">
                                <span style="width:5px; height:5px; border-radius:50%; background:#d97706; display:inline-block;"></span>
                                Draft
                            </span>
                        @endif
                        @if($isBuiltIn)
                            <span style="font-size:10px; font-weight:700; padding:3px 10px; border-radius:20px; background:#eff6ff; color:#3b82f6; margin-left:4px;">Built-in</span>
                        @endif
                    </td>
                    <td style="padding:13px 20px;">
                        @if($page->show_in_nav)
                            <span style="font-size:10px; font-weight:700; padding:3px 10px; border-radius:20px; background:#faf5ff; color:#7c3aed; display:inline-flex; align-items:center; gap:4px;">
                                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                In Nav
                            </span>
                        @else
                            <span style="font-size:11px; color:#cbd5e1;">—</span>
                        @endif
                    </td>
                    <td style="padding:13px 20px; color:#94a3b8; font-size:12px;">
                        {{ $page->updated_at->format('d M Y') }}
                    </td>
                    <td style="padding:13px 20px;">
                        <div style="display:flex; align-items:center; justify-content:flex-end; gap:6px;">
                            <a href="{{ route('admin.pages.edit', $page) }}"
                               style="background:#f0fdfa; color:#0f766e; padding:5px 12px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </a>
                            @if(!$isBuiltIn)
                            <form method="POST" action="{{ route('admin.pages.destroy', $page) }}"
                                  onsubmit="return confirm('Delete page &quot;{{ addslashes($page->title) }}&quot;? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        style="background:#fff1f2; color:#e11d48; border:none; padding:5px 12px; border-radius:7px; font-size:11px; font-weight:600; cursor:pointer;">
                                    Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="padding:60px 20px; text-align:center;">
        <svg width="48" height="48" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 12px; display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <p style="font-size:14px; font-weight:600; color:#334155; margin:0 0 8px;">No pages yet</p>
        <a href="{{ route('admin.pages.create') }}" style="font-size:13px; color:#0d9488; text-decoration:none; font-weight:600;">Create your first page →</a>
    </div>
    @endif
</div>

@endsection
