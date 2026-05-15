@extends('layouts.admin')
@section('page-title', $folder->name)

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px;">
            <a href="{{ route('admin.gallery-folders.index', ['type' => $folder->type]) }}" style="color:#64748b; text-decoration:none; font-size:12px; display:flex; align-items:center; gap:4px;">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                {{ $folder->type === 'programs' ? 'Programme Folders' : 'Gallery Folders' }}
            </a>
            <span style="color:#cbd5e1; font-size:12px;">/</span>
            <span style="font-size:12px; color:#0f172a; font-weight:600;">{{ $folder->name }}</span>
        </div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">{{ $folder->name }}</h2>
        @if($folder->description)
            <p style="font-size:12px; color:#64748b; margin:3px 0 0;">{{ $folder->description }}</p>
        @endif
    </div>
    <div style="display:flex; gap:8px;">
        <span style="background:#f0fdfa; color:#0f766e; font-size:12px; font-weight:600; padding:5px 12px; border-radius:20px; border:1px solid #99f6e4;">
            {{ $images->total() }} Photo{{ $images->total() != 1 ? 's' : '' }}
        </span>
        <a href="{{ route('admin.gallery-folders.edit', $folder) }}"
           style="background:#f8fafc; color:#475569; border:1px solid #e2e8f0; padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none;">
            Edit Folder
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:10px; font-size:13px; font-weight:500; margin-bottom:16px;">
        {{ session('success') }}
    </div>
@endif

{{-- Upload Form --}}
<div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); padding:22px; margin-bottom:24px;">
    <div style="font-size:13px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f8fafc;">
        Upload to "{{ $folder->name }}"
    </div>
    <form method="POST" action="{{ route('admin.gallery-folders.images.store', $folder) }}" enctype="multipart/form-data">
        @csrf
        @if($errors->any())
            <div style="background:#fff1f2; border:1px solid #fecdd3; color:#9f1239; padding:10px 14px; border-radius:8px; font-size:12px; margin-bottom:12px;">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif

        <div id="drop-zone"
             style="border:2px dashed #e2e8f0; border-radius:12px; padding:24px; text-align:center; cursor:pointer; margin-bottom:12px; transition:border-color .2s;"
             onclick="document.getElementById('folder-upload').click()"
             ondragover="event.preventDefault(); this.style.borderColor='#14b8a6';"
             ondragleave="this.style.borderColor='#e2e8f0';"
             ondrop="handleDrop(event)">
            <svg width="28" height="28" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 6px; display:block;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p id="drop-label" style="font-size:12px; color:#64748b; margin:0;">
                <strong style="color:#0f766e;">Click to choose</strong> or drag &amp; drop images
            </p>
        </div>

        <input type="file" id="folder-upload" name="images[]" multiple accept="image/*" required
               style="display:none;" onchange="updateLabel(this)">
        <div id="preview-grid" style="display:none; flex-wrap:wrap; gap:6px; margin-bottom:12px;"></div>

        <div style="display:flex; gap:10px; align-items:flex-end;">
            <div style="flex:1;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:4px;">Caption <span style="color:#94a3b8; font-weight:400;">(optional)</span></label>
                <input type="text" name="caption" value="{{ old('caption') }}" maxlength="255"
                       placeholder="e.g. Opening Ceremony"
                       style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:12px; box-sizing:border-box;"
                       onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <button type="submit"
                    style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; border:none; padding:9px 20px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; white-space:nowrap;">
                Upload
            </button>
        </div>
    </form>
</div>

{{-- Image Grid --}}
@if($images->count())
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(150px,1fr)); gap:10px; margin-bottom:20px;">
    @foreach($images as $image)
    <div style="background:#fff; border-radius:10px; border:1px solid #f1f5f9; overflow:hidden; position:relative;">
        <div style="position:relative; padding-top:100%; overflow:hidden;">
            <img src="{{ \App\Helpers\Settings::storageUrl($image->path) }}"
                 alt="{{ $image->title ?? 'Photo' }}"
                 style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transition:transform .3s;"
                 onmouseover="this.style.transform='scale(1.06)'"
                 onmouseout="this.style.transform=''">
        </div>
        <div style="padding:6px 8px; display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:10px; color:#94a3b8;">{{ $image->created_at->format('d M Y') }}</span>
            <form method="POST" action="{{ route('admin.gallery-folders.images.destroy', $image) }}"
                  onsubmit="return confirm('Delete this image?')">
                @csrf @method('DELETE')
                <button type="submit"
                        style="background:#fff1f2; color:#e11d48; border:none; padding:3px 8px; border-radius:5px; font-size:10px; font-weight:600; cursor:pointer;">
                    Del
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
<div>{{ $images->links() }}</div>
@else
<div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; padding:48px 20px; text-align:center;">
    <p style="font-size:13px; color:#94a3b8; margin:0;">No images in this folder yet. Upload some above.</p>
</div>
@endif

<script>
function updateLabel(input) {
    const count = input.files.length;
    document.getElementById('drop-label').innerHTML =
        count > 0 ? '<strong style="color:#0f766e;">' + count + ' file(s) selected</strong>' : '<strong style="color:#0f766e;">Click to choose</strong> or drag &amp; drop images';
    const grid = document.getElementById('preview-grid');
    grid.innerHTML = '';
    if (count > 0) {
        grid.style.display = 'flex';
        Array.from(input.files).forEach(function(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.style.cssText = 'width:50px;height:50px;border-radius:6px;overflow:hidden;border:1px solid #e2e8f0;flex-shrink:0;';
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
                div.appendChild(img);
                grid.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    } else { grid.style.display = 'none'; }
}
function handleDrop(e) {
    e.preventDefault();
    document.getElementById('drop-zone').style.borderColor = '#e2e8f0';
    const input = document.getElementById('folder-upload');
    input.files = e.dataTransfer.files;
    updateLabel(input);
}
</script>

@endsection
