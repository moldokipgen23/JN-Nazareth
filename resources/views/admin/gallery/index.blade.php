@extends('layouts.admin')
@section('page-title', 'Gallery')

@section('content')

{{-- Header --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">Gallery</h2>
        <p style="font-size:12px; color:#64748b; margin:4px 0 0;">Manage photos shown on the public gallery page.</p>
    </div>
    <span style="background:#f0fdfa; color:#0f766e; font-size:12px; font-weight:600; padding:5px 12px; border-radius:20px; border:1px solid #99f6e4;">
        {{ $images->total() }} Photo{{ $images->total() != 1 ? 's' : '' }}
    </span>
</div>

@if(session('success'))
    <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:10px; font-size:13px; font-weight:500; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div style="background:#fff1f2; border:1px solid #fecdd3; color:#9f1239; padding:12px 16px; border-radius:10px; font-size:13px; margin-bottom:16px;">
        <ul style="margin:0; padding-left:16px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Upload Form --}}
<div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); padding:22px; margin-bottom:24px;">
    <div style="font-size:13px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f8fafc;">
        Upload Images
    </div>
    <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Drop zone --}}
        <div id="drop-zone"
             style="border:2px dashed #e2e8f0; border-radius:12px; padding:28px 20px; text-align:center; cursor:pointer; margin-bottom:14px; transition:border-color .2s;"
             onclick="document.getElementById('gallery-upload').click()"
             ondragover="event.preventDefault(); this.style.borderColor='#14b8a6';"
             ondragleave="this.style.borderColor='#e2e8f0';"
             ondrop="handleDrop(event)">
            <svg width="32" height="32" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 8px; display:block;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p id="drop-label" style="font-size:13px; color:#64748b; margin:0;">
                <strong style="color:#0f766e;">Click to choose</strong> or drag &amp; drop images here
            </p>
            <p style="font-size:11px; color:#94a3b8; margin:4px 0 0;">JPG, PNG, GIF, WebP — max 5 MB each — multiple allowed</p>
        </div>

        <input type="file" id="gallery-upload" name="images[]" multiple accept="image/*" required
               style="display:none;"
               onchange="updateLabel(this)">

        {{-- Preview thumbnails --}}
        <div id="preview-grid" style="display:none; display:flex; flex-wrap:wrap; gap:8px; margin-bottom:14px;"></div>

        {{-- Caption --}}
        <div style="margin-bottom:14px;">
            <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">Caption / Title <span style="color:#94a3b8; font-weight:400;">(optional — applies to all uploaded images)</span></label>
            <input type="text" name="title" value="{{ old('title') }}" maxlength="255"
                   placeholder="e.g. Sunday Worship — April 2026"
                   style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; outline:none; box-sizing:border-box;"
                   onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <button type="submit"
                style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; border:none; padding:10px 24px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Upload Images
        </button>
    </form>
</div>

{{-- Image Grid --}}
@if($images->count())
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(160px,1fr)); gap:12px; margin-bottom:20px;">
    @foreach($images as $image)
    <div style="background:#fff; border-radius:12px; border:1px solid #f1f5f9; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.05); position:relative;" class="gallery-card">

        <div style="position:relative; padding-top:100%; overflow:hidden;">
            <img src="{{ \App\Helpers\Settings::storageUrl($image->path) }}"
                 alt="{{ $image->title ?? 'Gallery image' }}"
                 style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transition:transform .3s;"
                 onmouseover="this.style.transform='scale(1.06)'"
                 onmouseout="this.style.transform='scale(1)'">
        </div>

        @if($image->title)
            <div style="padding:7px 10px; font-size:11px; color:#475569; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                {{ $image->title }}
            </div>
        @endif

        <div style="padding:{{ $image->title ? '0 8px 8px' : '8px' }}; display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:10px; color:#94a3b8;">{{ $image->created_at->format('d M Y') }}</span>
            <form method="POST" action="{{ route('admin.gallery.destroy', $image) }}"
                  onsubmit="return confirm('Delete this image?')">
                @csrf @method('DELETE')
                <button type="submit"
                        style="background:#fff1f2; color:#e11d48; border:none; padding:4px 10px; border-radius:6px; font-size:11px; font-weight:600; cursor:pointer;">
                    Delete
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

<div>{{ $images->links() }}</div>

@else
<div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; padding:60px 20px; text-align:center;">
    <svg width="48" height="48" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 12px; display:block;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <p style="font-size:14px; font-weight:600; color:#334155; margin:0 0 6px;">No images yet</p>
    <p style="font-size:13px; color:#94a3b8; margin:0;">Upload images using the form above to populate the gallery.</p>
</div>
@endif

<script>
function updateLabel(input) {
    const count = input.files.length;
    document.getElementById('drop-label').innerHTML =
        count > 0
            ? '<strong style="color:#0f766e;">' + count + ' file' + (count > 1 ? 's' : '') + ' selected</strong> — ready to upload'
            : '<strong style="color:#0f766e;">Click to choose</strong> or drag &amp; drop images here';

    // Show previews
    const grid = document.getElementById('preview-grid');
    grid.innerHTML = '';
    if (count > 0) {
        grid.style.display = 'flex';
        Array.from(input.files).forEach(function(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.style.cssText = 'width:60px;height:60px;border-radius:8px;overflow:hidden;border:1px solid #e2e8f0;flex-shrink:0;';
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
                div.appendChild(img);
                grid.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    } else {
        grid.style.display = 'none';
    }
}

function handleDrop(e) {
    e.preventDefault();
    document.getElementById('drop-zone').style.borderColor = '#e2e8f0';
    const input = document.getElementById('gallery-upload');
    input.files = e.dataTransfer.files;
    updateLabel(input);
}
</script>

@endsection
