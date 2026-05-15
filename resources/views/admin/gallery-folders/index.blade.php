@extends('layouts.admin')
@section('page-title', $type === 'programs' ? 'Programmes Folders' : 'Gallery Folders')

@section('content')

{{-- Type Tabs --}}
<div style="display:flex; gap:8px; margin-bottom:20px; background:#f8fafc; padding:4px; border-radius:10px; width:fit-content;">
    <a href="{{ route('admin.gallery-folders.index', ['type' => 'gallery']) }}"
       style="padding:7px 18px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none;
              {{ $type === 'gallery' ? 'background:#0f766e; color:#fff; box-shadow:0 2px 8px rgba(15,118,110,.3);' : 'color:#64748b;' }}">
        Gallery
    </a>
    <a href="{{ route('admin.gallery-folders.index', ['type' => 'programs']) }}"
       style="padding:7px 18px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none;
              {{ $type === 'programs' ? 'background:#0f766e; color:#fff; box-shadow:0 2px 8px rgba(15,118,110,.3);' : 'color:#64748b;' }}">
        Programmes
    </a>
</div>

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">
            {{ $type === 'programs' ? 'Programmes Gallery Folders' : 'Gallery Folders' }}
        </h2>
        <p style="font-size:12px; color:#64748b; margin:4px 0 0;">
            {{ $type === 'programs' ? 'Organise Programmes/NGO photos into albums.' : 'Organise church photos into albums.' }}
            Each folder appears as a 3D card on the public site.
        </p>
    </div>
    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        @if($type === 'gallery')
        <a href="{{ route('admin.gallery.index') }}"
           style="background:#f8fafc; color:#475569; border:1px solid #e2e8f0; padding:7px 16px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none;">
            Unfoldered Images
        </a>
        @endif
        <a href="{{ route('admin.gallery-folders.create', ['type' => $type]) }}"
           style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; border:none; padding:8px 18px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
            New {{ $type === 'programs' ? 'Programmes' : 'Gallery' }} Folder
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:10px; font-size:13px; font-weight:500; margin-bottom:16px;">
        {{ session('success') }}
    </div>
@endif

@if($folders->count())
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px,1fr)); gap:16px;">
    @foreach($folders as $folder)
    <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 2px 8px rgba(0,0,0,.06); overflow:hidden;">

        {{-- Cover --}}
        <a href="{{ route('admin.gallery-folders.show', $folder) }}"
           style="display:block; position:relative; padding-top:65%; background:#f8fafc; overflow:hidden;">
            @if($folder->cover_image)
                <img src="{{ \App\Helpers\Settings::storageUrl($folder->cover_image) }}"
                     alt="{{ $folder->name }}"
                     style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transition:transform .3s;"
                     onmouseover="this.style.transform='scale(1.06)'"
                     onmouseout="this.style.transform=''">
            @else
                <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:#f0fdfa;">
                    <svg width="36" height="36" fill="none" stroke="#99f6e4" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h3l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
                    </svg>
                </div>
            @endif
            <div style="position:absolute; top:8px; left:8px; background:{{ $folder->type === 'programs' ? '#7c3aed' : '#0f766e' }}; color:#fff; font-size:9px; font-weight:700; padding:2px 7px; border-radius:20px; letter-spacing:.05em;">
                {{ strtoupper($folder->type) }}
            </div>
            <div style="position:absolute; bottom:8px; right:8px; background:rgba(0,0,0,.6); color:#fff; font-size:10px; font-weight:600; padding:3px 8px; border-radius:20px;">
                {{ $folder->items_count }} photo{{ $folder->items_count != 1 ? 's' : '' }}
            </div>
        </a>

        {{-- Info --}}
        <div style="padding:12px 14px;">
            <div style="font-size:13px; font-weight:700; color:#0f172a; margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                {{ $folder->name }}
            </div>
            @if($folder->description)
                <div style="font-size:11px; color:#94a3b8; margin-bottom:8px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                    {{ $folder->description }}
                </div>
            @endif

            <div style="display:flex; gap:6px; margin-top:8px;">
                <a href="{{ route('admin.gallery-folders.show', $folder) }}"
                   style="flex:1; text-align:center; background:#f0fdfa; color:#0f766e; border:1px solid #99f6e4; padding:5px 0; border-radius:6px; font-size:11px; font-weight:600; text-decoration:none;">
                    Open
                </a>
                <a href="{{ route('admin.gallery-folders.edit', $folder) }}"
                   style="background:#f8fafc; color:#475569; border:1px solid #e2e8f0; padding:5px 10px; border-radius:6px; font-size:11px; font-weight:600; text-decoration:none;">
                    Edit
                </a>
                <form method="POST" action="{{ route('admin.gallery-folders.destroy', $folder) }}"
                      onsubmit="return confirm('Delete folder and ALL {{ $folder->items_count }} image(s)? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" style="background:#fff1f2; color:#e11d48; border:1px solid #fecdd3; padding:5px 10px; border-radius:6px; font-size:11px; font-weight:600; cursor:pointer;">
                        Del
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; padding:60px 20px; text-align:center;">
    <svg width="48" height="48" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 12px; display:block;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h3l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
    </svg>
    <p style="font-size:14px; font-weight:600; color:#334155; margin:0 0 6px;">No folders yet</p>
    <p style="font-size:13px; color:#94a3b8; margin:0 0 16px;">Create your first {{ $type === 'programs' ? 'Programmes' : 'gallery' }} album.</p>
    <a href="{{ route('admin.gallery-folders.create', ['type' => $type]) }}"
       style="display:inline-flex; align-items:center; gap:6px; background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; padding:9px 20px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
        Create First Folder
    </a>
</div>
@endif

@endsection
