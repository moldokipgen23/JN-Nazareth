@extends('layouts.public')
@section('title', 'Videos')

@section('content')

{{-- Hero --}}
<section class="relative overflow-hidden text-white" style="min-height:220px; background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);">
    <div class="absolute inset-0 opacity-10 pointer-events-none">
        <svg class="w-full h-full" viewBox="0 0 80 40" preserveAspectRatio="xMidYMid slice">
            <defs><pattern id="vid-dots" width="8" height="8" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1.5" fill="white"/></pattern></defs>
            <rect width="80" height="40" fill="url(#vid-dots)"/>
        </svg>
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full mb-4" style="background:rgba(20,184,166,.2);">
            <svg class="w-7 h-7 text-teal-400" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
        </div>
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight">Sermons &amp; Videos</h1>
        <p class="mt-3 text-gray-400 text-lg max-w-xl mx-auto">Messages, choir worship, and highlights from church life.</p>
        @if($videos->total())
            <div class="mt-4 inline-flex items-center gap-2 text-sm text-teal-400 font-medium">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                {{ $videos->total() }} video{{ $videos->total() != 1 ? 's' : '' }}
            </div>
        @endif
    </div>
</section>

{{-- Video Grid --}}
<section class="py-16" style="background:#f8fafc;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($videos->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($videos as $video)
            @php
              $catBg = match($video->category) { 'sermon'=>'rgba(59,130,246,.9)','choir'=>'rgba(168,85,247,.9)','event'=>'rgba(249,115,22,.9)',default=>'rgba(100,116,139,.9)' };
            @endphp
            <div class="group cursor-pointer rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                 style="background:#fff;"
                 onclick="openVideoModal('{{ $video->embed_url }}', '{{ addslashes($video->title) }}', '{{ addslashes($video->description ?? '') }}')">

                {{-- Thumbnail --}}
                <div style="position:relative; padding-top:56.25%; overflow:hidden; background:#0f172a;">
                    <img src="{{ $video->thumbnail }}" alt="{{ $video->title }}"
                         class="group-hover:scale-105 transition-transform duration-500"
                         style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;">
                    <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,.5), transparent); display:flex; align-items:center; justify-content:center;">
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                             style="width:52px; height:52px; border-radius:50%; background:rgba(20,184,166,.9); display:flex; align-items:center; justify-content:center; box-shadow:0 0 0 8px rgba(20,184,166,.2);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="white" style="margin-left:3px;"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                    <div style="position:absolute; top:10px; left:10px; background:{{ $catBg }}; color:#fff; font-size:9px; font-weight:800; padding:3px 8px; border-radius:20px; text-transform:uppercase; letter-spacing:.06em;">
                        {{ $video->category }}
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-4">
                    <h3 class="font-bold text-gray-900 text-sm leading-snug mb-1 line-clamp-2">{{ $video->title }}</h3>
                    @if($video->description)
                        <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">{{ $video->description }}</p>
                    @endif
                    <div class="mt-3 flex items-center gap-1.5 text-xs font-semibold" style="color:#0d9488;">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        Watch Now
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($videos->hasPages())
            <div class="mt-10 flex justify-center">{{ $videos->links() }}</div>
        @endif

        @else
        <div class="text-center py-20">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-5">
                <svg class="w-10 h-10 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-700 mb-2">No Videos Yet</h3>
            <p class="text-gray-400 text-sm">Check back soon for sermons and church videos.</p>
        </div>
        @endif

    </div>
</section>

{{-- Video Modal --}}
<div id="video-modal"
     style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; background:rgba(0,0,0,.88); backdrop-filter:blur(8px);"
     onclick="closeVideoModal(event)">
    <div style="position:relative; width:90vw; max-width:900px; margin:auto;" onclick="event.stopPropagation()">
        <button onclick="closeVideoModal()"
                style="position:absolute; top:-44px; right:0; background:rgba(255,255,255,.15); border:none; width:36px; height:36px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#fff;"
                onmouseover="this.style.background='rgba(255,255,255,.3)'" onmouseout="this.style.background='rgba(255,255,255,.15)'">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div style="position:relative; padding-top:56.25%; background:#000; border-radius:16px; overflow:hidden; box-shadow:0 40px 80px rgba(0,0,0,.6);">
            <iframe id="modal-iframe" src="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen
                    style="position:absolute; inset:0; width:100%; height:100%; border:none;"></iframe>
        </div>
        <div style="margin-top:12px; text-align:center;">
            <p id="modal-title" style="color:#f1f5f9; font-size:15px; font-weight:700; margin:0;"></p>
            <p id="modal-desc" style="color:#64748b; font-size:13px; margin:4px 0 0;"></p>
        </div>
    </div>
</div>

@push('scripts')
<script>
window.openVideoModal = function(embedUrl, title, desc) {
    document.getElementById('modal-iframe').src  = embedUrl + '&autoplay=1';
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-desc').textContent  = desc;
    document.getElementById('video-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
};
window.closeVideoModal = function(e) {
    if (e && e.target !== document.getElementById('video-modal')) return;
    document.getElementById('modal-iframe').src = '';
    document.getElementById('video-modal').style.display = 'none';
    document.body.style.overflow = '';
};
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeVideoModal();
});
</script>
@endpush

@endsection
