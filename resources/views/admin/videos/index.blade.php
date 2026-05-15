@extends('layouts.admin')
@section('page-title', 'Videos')

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">Church Videos</h2>
        <p style="font-size:12px; color:#64748b; margin:4px 0 0;">Manage YouTube videos shown on the homepage — sermons, choir, events.</p>
    </div>
    <span style="background:#f0fdfa; color:#0f766e; font-size:12px; font-weight:600; padding:5px 12px; border-radius:20px; border:1px solid #99f6e4;">
        {{ $videos->count() }} Video{{ $videos->count() != 1 ? 's' : '' }}
    </span>
</div>

@if(session('success'))
    <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:10px; font-size:13px; font-weight:500; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:#fff1f2; border:1px solid #fecdd3; color:#9f1239; padding:12px 16px; border-radius:10px; font-size:13px; margin-bottom:16px;">
        {{ session('error') }}
    </div>
@endif

<div style="display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:start;">

    {{-- ── Video list ── --}}
    <div>
        @if($videos->count())
        <div style="display:flex; flex-direction:column; gap:12px;">
            @foreach($videos as $video)
            @php
                $categoryColors = [
                    'sermon' => ['bg'=>'#eff6ff','text'=>'#1d4ed8'],
                    'choir'  => ['bg'=>'#fdf4ff','text'=>'#7e22ce'],
                    'event'  => ['bg'=>'#fff7ed','text'=>'#c2410c'],
                    'other'  => ['bg'=>'#f8fafc','text'=>'#475569'],
                ];
                $cc = $categoryColors[$video->category] ?? $categoryColors['other'];
            @endphp
            <div style="background:#fff; border-radius:14px; border:1px solid {{ $video->active ? '#f1f5f9' : '#fecdd3' }}; box-shadow:0 1px 6px rgba(0,0,0,.05); padding:14px 16px; display:flex; gap:14px; align-items:flex-start; opacity:{{ $video->active ? '1' : '.6' }};">

                {{-- Thumbnail --}}
                <div style="flex-shrink:0; width:120px; height:68px; border-radius:8px; overflow:hidden; background:#0f172a; position:relative;">
                    @if($video->youtube_id)
                        <img src="{{ $video->thumbnail }}" alt="{{ $video->title }}"
                             style="width:100%; height:100%; object-fit:cover;">
                        <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,.25);">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="white"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    @else
                        <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                            <svg width="24" height="24" fill="none" stroke="#64748b" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.069A1 1 0 0121 8.868V15.13a1 1 0 01-1.447.897L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div style="flex:1; min-width:0;">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px; flex-wrap:wrap;">
                        <span style="font-size:13px; font-weight:700; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:300px;">{{ $video->title }}</span>
                        <span style="font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px; background:{{ $cc['bg'] }}; color:{{ $cc['text'] }}; text-transform:uppercase; letter-spacing:.05em; flex-shrink:0;">{{ $video->category }}</span>
                        @if(!$video->active)
                            <span style="font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px; background:#fff1f2; color:#e11d48;">Hidden</span>
                        @endif
                    </div>
                    @if($video->description)
                        <p style="font-size:12px; color:#64748b; margin:0 0 6px; line-height:1.5; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">{{ $video->description }}</p>
                    @endif
                    <div style="font-size:11px; color:#94a3b8;">
                        Added {{ $video->created_at->diffForHumans() }}
                        @if($video->sort_order > 0) · Order: {{ $video->sort_order }} @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div style="display:flex; flex-direction:column; gap:6px; flex-shrink:0;">
                    <a href="{{ route('admin.videos.edit', $video) }}"
                       style="background:#f0fdfa; color:#0f766e; border:1px solid #99f6e4; padding:5px 12px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none; display:block; text-align:center; margin-bottom:6px;">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.videos.toggle', $video) }}">
                        @csrf
                        <button type="submit"
                                style="background:{{ $video->active ? '#f0fdfa' : '#f8fafc' }}; color:{{ $video->active ? '#0f766e' : '#64748b' }}; border:1px solid {{ $video->active ? '#99f6e4' : '#e2e8f0' }}; padding:5px 12px; border-radius:7px; font-size:11px; font-weight:600; cursor:pointer; white-space:nowrap;">
                            {{ $video->active ? 'Hide' : 'Show' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.videos.destroy', $video) }}"
                          onsubmit="return confirm('Delete this video?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                style="background:#fff1f2; color:#e11d48; border:none; padding:5px 12px; border-radius:7px; font-size:11px; font-weight:600; cursor:pointer; width:100%;">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; padding:60px 20px; text-align:center;">
            <svg width="48" height="48" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 12px; display:block;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.069A1 1 0 0121 8.868V15.13a1 1 0 01-1.447.897L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <p style="font-size:14px; font-weight:600; color:#334155; margin:0 0 6px;">No videos yet</p>
            <p style="font-size:13px; color:#94a3b8; margin:0;">Add a YouTube video using the form on the right.</p>
        </div>
        @endif
    </div>

    {{-- ── Add Video sidebar ── --}}
    <div>
        <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); padding:20px;">
            <div style="font-size:13px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f8fafc;">
                Add New Video
            </div>

            @if($errors->any())
                <div style="background:#fff1f2; border:1px solid #fecdd3; color:#9f1239; padding:10px 12px; border-radius:8px; font-size:12px; margin-bottom:12px;">
                    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.videos.store') }}">
                @csrf

                <div style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">Video Title <span style="color:#e11d48;">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required maxlength="255"
                           placeholder="e.g. Sunday Sermon — April 6"
                           style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; outline:none; box-sizing:border-box;"
                           onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
                </div>

                <div style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">YouTube URL <span style="color:#e11d48;">*</span></label>
                    <input type="url" name="youtube_url" value="{{ old('youtube_url') }}" required
                           placeholder="https://www.youtube.com/watch?v=..."
                           style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; outline:none; box-sizing:border-box;"
                           onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'"
                           oninput="previewThumb(this.value)">
                    <p style="font-size:11px; color:#94a3b8; margin:4px 0 0;">Paste any YouTube link format — watch, share, or embed URL</p>
                    <div id="thumb-preview" style="display:none; margin-top:8px; border-radius:8px; overflow:hidden; background:#0f172a; position:relative;">
                        <img id="thumb-img" src="" alt="" style="width:100%; display:block;">
                        <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center;">
                            <div style="background:rgba(0,0,0,.5); border-radius:50%; width:36px; height:36px; display:flex; align-items:center; justify-content:center;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">Category <span style="color:#e11d48;">*</span></label>
                    <select name="category" required
                            style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; outline:none; box-sizing:border-box; background:#fff;"
                            onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
                        <option value="sermon" {{ old('category') == 'sermon' ? 'selected' : '' }}>Sermon</option>
                        <option value="choir"  {{ old('category') == 'choir'  ? 'selected' : '' }}>Choir</option>
                        <option value="event"  {{ old('category') == 'event'  ? 'selected' : '' }}>Event</option>
                        <option value="other"  {{ old('category') == 'other'  ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">Description <span style="color:#94a3b8; font-weight:400;">(optional)</span></label>
                    <textarea name="description" rows="2" maxlength="500"
                              placeholder="Brief description of the video..."
                              style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; outline:none; box-sizing:border-box; resize:vertical;"
                              onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">{{ old('description') }}</textarea>
                </div>

                <div style="margin-bottom:16px;">
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">Display Order <span style="color:#94a3b8; font-weight:400;">(0 = first)</span></label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" max="999"
                           style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; outline:none; box-sizing:border-box;"
                           onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
                </div>

                <button type="submit"
                        style="width:100%; background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; border:none; padding:10px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:7px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4v8m0 0v4m0-4h4m-4 0H8"/></svg>
                    Add Video
                </button>
            </form>
        </div>

        {{-- Hint card --}}
        <div style="background:#f8fafc; border-radius:12px; border:1px solid #f1f5f9; padding:14px 16px; margin-top:12px; font-size:12px; color:#64748b; line-height:1.6;">
            <div style="font-weight:700; color:#334155; margin-bottom:6px; display:flex; align-items:center; gap:6px;">
                <svg width="13" height="13" fill="#0f766e" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                Supported URL formats
            </div>
            <div style="font-family:monospace; font-size:10.5px; background:#fff; border-radius:6px; padding:8px 10px; border:1px solid #e2e8f0;">
                youtube.com/watch?v=ABC123<br>
                youtu.be/ABC123<br>
                youtube.com/embed/ABC123
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
    var id = extractYoutubeId(url);
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
