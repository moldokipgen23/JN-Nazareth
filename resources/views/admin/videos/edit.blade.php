@extends('layouts.admin')
@section('page-title', 'Edit Video')

@section('content')

<div style="display:flex; align-items:center; gap:12px; margin-bottom:20px;">
    <a href="{{ route('admin.videos.index') }}"
       style="display:flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:8px; background:#f8fafc; border:1px solid #e2e8f0; color:#64748b; text-decoration:none;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">Edit Video</h2>
        <p style="font-size:12px; color:#64748b; margin:3px 0 0;">{{ $video->title }}</p>
    </div>
</div>

@if(session('success'))
    <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:10px; font-size:13px; font-weight:500; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
@endif

<div style="display:grid; grid-template-columns:1fr 300px; gap:20px; align-items:start;">

    {{-- Form --}}
    <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); padding:24px;">

        @if($errors->any())
            <div style="background:#fff1f2; border:1px solid #fecdd3; color:#9f1239; padding:10px 12px; border-radius:8px; font-size:12px; margin-bottom:16px;">
                @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.videos.update', $video) }}">
            @csrf @method('PUT')

            <div style="margin-bottom:16px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">Video Title <span style="color:#e11d48;">*</span></label>
                <input type="text" name="title" value="{{ old('title', $video->title) }}" required maxlength="255"
                       style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; outline:none; box-sizing:border-box;"
                       onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">YouTube URL <span style="color:#e11d48;">*</span></label>
                <input type="url" name="youtube_url" value="{{ old('youtube_url', $video->youtube_url) }}" required
                       style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; outline:none; box-sizing:border-box;"
                       onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'"
                       oninput="previewThumb(this.value)">
                <div id="thumb-preview" style="{{ $video->youtube_id ? '' : 'display:none;' }} margin-top:10px; border-radius:10px; overflow:hidden; background:#0f172a; position:relative; max-width:320px;">
                    <img id="thumb-img" src="{{ $video->thumbnail ?? '' }}" alt="" style="width:100%; display:block;">
                    <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center;">
                        <div style="background:rgba(0,0,0,.5); border-radius:50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="white"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">Category <span style="color:#e11d48;">*</span></label>
                    <select name="category" required
                            style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; outline:none; box-sizing:border-box; background:#fff;"
                            onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
                        @foreach(['sermon','choir','event','other'] as $cat)
                        <option value="{{ $cat }}" {{ old('category', $video->category) === $cat ? 'selected' : '' }}>
                            {{ ucfirst($cat) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">Display Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $video->sort_order) }}" min="0" max="999"
                           style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; outline:none; box-sizing:border-box;"
                           onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">Description</label>
                <textarea name="description" rows="3" maxlength="500"
                          style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; outline:none; box-sizing:border-box; resize:vertical;"
                          onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">{{ old('description', $video->description) }}</textarea>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit"
                        style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; border:none; padding:10px 24px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    Save Changes
                </button>
                <a href="{{ route('admin.videos.index') }}"
                   style="background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; padding:10px 20px; border-radius:10px; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center;">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    {{-- Preview sidebar --}}
    <div>
        <div style="background:#0f172a; border-radius:14px; border:1px solid #1e293b; padding:16px;">
            <div style="font-size:12px; font-weight:700; color:#94a3b8; margin-bottom:12px; text-transform:uppercase; letter-spacing:.06em;">Current Preview</div>
            @if($video->youtube_id)
            <div style="position:relative; padding-top:56.25%; border-radius:10px; overflow:hidden; background:#000; margin-bottom:12px;">
                <img src="{{ $video->thumbnail }}" alt="{{ $video->title }}"
                     style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;">
                <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,.3);">
                    <div style="width:44px; height:44px; border-radius:50%; background:rgba(20,184,166,.85); display:flex; align-items:center; justify-content:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="white" style="margin-left:3px;"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                </div>
            </div>
            <a href="{{ $video->youtube_url }}" target="_blank"
               style="display:flex; align-items:center; gap:6px; font-size:12px; color:#14b8a6; text-decoration:none; margin-bottom:8px;">
                <svg width="13" height="13" fill="#ef4444" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                Open on YouTube
            </a>
            @endif
            <div style="font-size:11px; color:#475569; line-height:1.6;">
                <div style="margin-bottom:4px;"><span style="color:#94a3b8;">Status:</span> {{ $video->active ? '✅ Visible' : '🔴 Hidden' }}</div>
                <div style="margin-bottom:4px;"><span style="color:#94a3b8;">Category:</span> {{ ucfirst($video->category) }}</div>
                <div><span style="color:#94a3b8;">Added:</span> {{ $video->created_at->format('d M Y') }}</div>
            </div>
        </div>
    </div>
</div>

<script>
function extractYoutubeId(url) {
    var m;
    if ((m = url.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/))) return m[1];
    if ((m = url.match(/(?:v=|\/embed\/|\/v\/)([a-zA-Z0-9_-]{11})/))) return m[1];
    return null;
}
function previewThumb(url) {
    var id  = extractYoutubeId(url);
    var box = document.getElementById('thumb-preview');
    var img = document.getElementById('thumb-img');
    if (id) {
        img.src = 'https://img.youtube.com/vi/' + id + '/hqdefault.jpg';
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
}
</script>

@endsection
