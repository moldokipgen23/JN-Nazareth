@extends('layouts.public')
@section('title', 'Gallery')

@push('styles')
<style>
.folder-card { perspective:900px; cursor:pointer; text-decoration:none; display:block; }
.folder-card-inner {
    transition:transform .4s cubic-bezier(.25,.8,.25,1), box-shadow .4s;
    transform-style:preserve-3d;
    border-radius:18px; overflow:hidden;
    background:#fff; box-shadow:0 6px 28px rgba(0,0,0,.1);
}
.folder-card:hover .folder-card-inner {
    transform:rotateY(-7deg) rotateX(4deg) scale(1.04);
    box-shadow:0 22px 64px rgba(0,0,0,.22);
}
.stk { position:absolute; border-radius:12px; overflow:hidden; }

.gal-reveal { opacity:0; transform:translateY(30px); transition:opacity .65s ease, transform .65s ease; }
.gal-reveal.vis { opacity:1; transform:none; }
</style>
@endpush

@section('content')

{{-- ══ Page Hero ══ --}}
<section class="page-hero" style="min-height:280px;">
    <div style="position:relative; z-index:2; max-width:900px; margin:0 auto; padding:72px 24px; text-align:center;">
        <span class="section-label" style="color:rgba(255,255,255,0.6);">Visual Stories</span>
        <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2.4rem,5vw,3.6rem); font-weight:800; color:#fff; margin:14px 0 16px; line-height:1.15;">
            Our Gallery
        </h1>
        <div style="display:flex; align-items:center; justify-content:center; gap:14px; margin-bottom:18px;">
            <span style="height:1px; width:50px; background:rgba(255,255,255,0.3);"></span>
            <svg width="18" height="18" fill="white" opacity="0.5" viewBox="0 0 24 24"><rect x="11" y="2" width="2" height="20" rx="1"/><rect x="3" y="8" width="18" height="2" rx="1"/></svg>
            <span style="height:1px; width:50px; background:rgba(255,255,255,0.3);"></span>
        </div>
        <p style="color:rgba(255,255,255,0.72); font-size:1.05rem; max-width:520px; margin:0 auto; line-height:1.7;">
            Moments of faith, fellowship, and life from {{ \App\Helpers\Settings::get('site_name','our community') }}.
        </p>
    </div>
</section>

{{-- ══ Photo Albums (Folders) ══ --}}
@if($folders->count())
<section style="background:var(--cream); padding:64px 0 40px;" class="gal-reveal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div style="text-align:center; margin-bottom:40px;">
            <span class="section-label">Photo Albums</span>
            <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.8rem,3.5vw,2.5rem); font-weight:800; color:#1c1917; margin:12px 0 0; line-height:1.2;">
                Browse by Album
            </h2>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px,1fr)); gap:26px;">
            @foreach($folders as $folder)
            <a href="{{ route('gallery.folder', $folder) }}" class="folder-card">
                <div class="folder-card-inner">
                    @php
                        $previews = $folder->previewImages;
                        $p0 = $previews->get(0);
                        $p1 = $previews->get(1);
                        $p2 = $previews->get(2);
                        $cover = $folder->cover_image
                            ? \App\Helpers\Settings::storageUrl($folder->cover_image)
                            : ($p0 ? \App\Helpers\Settings::storageUrl($p0->path) : null);
                        $img1  = $p1 ? \App\Helpers\Settings::storageUrl($p1->path) : $cover;
                        $img2  = $p2 ? \App\Helpers\Settings::storageUrl($p2->path) : ($img1 ?? $cover);
                    @endphp
                    <div style="position:relative; padding-top:70%; background:#e8f0ee; overflow:hidden;">

                        {{-- Stack layer 2 (behind) --}}
                        @if($img2)
                        <div class="stk" style="inset:6% 0 0 8%; opacity:.35; transform:rotate(3deg); z-index:1;">
                            <img src="{{ $img2 }}" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                        @endif

                        {{-- Stack layer 1 (mid) --}}
                        @if($img1)
                        <div class="stk" style="inset:3% 0 0 4%; opacity:.65; transform:rotate(1.2deg); z-index:2;">
                            <img src="{{ $img1 }}" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                        @endif

                        {{-- Front cover --}}
                        <div style="position:absolute; inset:0; z-index:3; overflow:hidden;">
                            @if($cover)
                                <img src="{{ $cover }}" alt="{{ $folder->name }}" loading="lazy"
                                     style="width:100%;height:100%;object-fit:cover; transition:transform .4s;"
                                     class="folder-cover-img">
                            @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#c8e6c9,#e8f5e9);">
                                    <svg width="52" height="52" fill="none" stroke="#4caf50" stroke-width="1.5" viewBox="0 0 24 24" opacity=".5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Gradient + Title --}}
                        <div style="position:absolute; inset:0; z-index:4; background:linear-gradient(to top, rgba(0,0,0,.75) 0%, transparent 55%); pointer-events:none;"></div>
                        <div style="position:absolute; bottom:14px; left:16px; right:16px; z-index:5; color:#fff;">
                            <div style="font-size:15px; font-weight:800; text-shadow:0 2px 8px rgba(0,0,0,.5); line-height:1.25;">{{ $folder->name }}</div>
                        </div>

                        {{-- Count badge --}}
                        <div style="position:absolute; top:12px; right:12px; z-index:6; background:rgba(0,0,0,.6); backdrop-filter:blur(6px); color:#fff; font-size:10px; font-weight:700; padding:4px 10px; border-radius:20px; letter-spacing:.04em;">
                            {{ $folder->items_count }} photos
                        </div>
                    </div>

                    @if($folder->description)
                    <div style="padding:12px 14px 14px; font-size:12px; color:#64748b; line-height:1.55; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                        {{ $folder->description }}
                    </div>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($images->count())

{{-- ══ Masonry Gallery Grid ══ --}}
<section style="background:var(--cream); padding:60px 0 80px;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Masonry grid --}}
        <div id="gallery-grid" style="columns:2; column-gap:14px; orphans:1; widows:1;"
             class="sm:columns-3 lg:columns-4">
            @foreach($images as $idx => $image)
            <div class="gallery-item"
                 data-src="{{ \App\Helpers\Settings::storageUrl($image->path) }}"
                 data-caption="{{ $image->title ?? '' }}"
                 data-idx="{{ $idx }}"
                 style="break-inside:avoid; margin-bottom:14px; cursor:pointer; border-radius:12px; overflow:hidden; position:relative; display:block; box-shadow:0 4px 18px rgba(0,0,0,0.1); transition:transform 0.3s, box-shadow 0.3s;"
                 onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 12px 36px rgba(0,0,0,0.22)'; this.querySelector('.gi-overlay').style.opacity='1';"
                 onmouseout="this.style.transform=''; this.style.boxShadow='0 4px 18px rgba(0,0,0,0.1)'; this.querySelector('.gi-overlay').style.opacity='0';">

                <img src="{{ \App\Helpers\Settings::storageUrl($image->path) }}"
                     alt="{{ $image->title ?? \App\Helpers\Settings::get('site_name','Gallery') }}"
                     loading="{{ $idx < 8 ? 'eager' : 'lazy' }}"
                     style="width:100%; height:auto; display:block; border-radius:12px;">

                {{-- Hover overlay --}}
                <div class="gi-overlay" style="position:absolute; inset:0; border-radius:12px; background:linear-gradient(to top, rgba(27,67,50,0.85) 0%, rgba(0,0,0,0.1) 60%); opacity:0; transition:opacity 0.3s; display:flex; flex-direction:column; justify-content:flex-end; padding:16px;">
                    @if($image->title)
                    <p style="color:#fff; font-family:'Playfair Display',serif; font-size:0.9rem; font-weight:600; margin:0 0 4px; text-shadow:0 1px 6px rgba(0,0,0,0.4);">{{ $image->title }}</p>
                    @endif
                    <div style="display:flex; align-items:center; gap:5px; color:rgba(255,255,255,0.7); font-size:0.75rem;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        Click to enlarge
                    </div>
                </div>

                {{-- Number badge --}}
                <div style="position:absolute; top:10px; right:10px; background:rgba(0,0,0,0.45); backdrop-filter:blur(4px); color:#fff; font-size:10px; font-weight:700; padding:3px 8px; border-radius:20px; font-variant-numeric:tabular-nums;">
                    {{ $idx + 1 }}
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($images->hasPages())
        <div style="margin-top:48px; display:flex; justify-content:center;">
            {{ $images->links() }}
        </div>
        @endif
    </div>
</section>

{{-- ══ Lightbox ══ --}}
<div id="lightbox" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.95); backdrop-filter:blur(10px); align-items:center; justify-content:center; flex-direction:column;">

    {{-- Close --}}
    <button id="lb-close" aria-label="Close"
            style="position:absolute; top:20px; right:20px; z-index:10; width:44px; height:44px; border-radius:50%; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s;"
            onmouseover="this.style.background='rgba(255,255,255,0.22)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>

    {{-- Prev --}}
    <button id="lb-prev"
            style="position:absolute; left:16px; top:50%; transform:translateY(-50%); z-index:10; width:52px; height:52px; border-radius:50%; background:rgba(255,255,255,0.10); border:1px solid rgba(255,255,255,0.15); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s;"
            onmouseover="this.style.background='rgba(255,255,255,0.22)'" onmouseout="this.style.background='rgba(255,255,255,0.10)'">
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    </button>

    {{-- Next --}}
    <button id="lb-next"
            style="position:absolute; right:16px; top:50%; transform:translateY(-50%); z-index:10; width:52px; height:52px; border-radius:50%; background:rgba(255,255,255,0.10); border:1px solid rgba(255,255,255,0.15); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s;"
            onmouseover="this.style.background='rgba(255,255,255,0.22)'" onmouseout="this.style.background='rgba(255,255,255,0.10)'">
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
    </button>

    {{-- Image --}}
    <div style="display:flex; flex-direction:column; align-items:center; max-width:92vw; max-height:92vh;">
        <img id="lb-img" src="" alt=""
             style="max-width:90vw; max-height:78vh; width:auto; height:auto; border-radius:12px; box-shadow:0 30px 90px rgba(0,0,0,0.7); object-fit:contain; display:block;">
        <div style="margin-top:16px; text-align:center;">
            <p id="lb-caption" style="color:rgba(255,255,255,0.88); font-family:'Playfair Display',serif; font-size:1rem; font-weight:600; min-height:1.3em;"></p>
            <p id="lb-count" style="color:rgba(255,255,255,0.4); font-size:0.78rem; margin-top:4px; font-variant-numeric:tabular-nums;"></p>
        </div>
    </div>
</div>

@else
{{-- Empty state --}}
<section style="padding:96px 24px; text-align:center; background:var(--cream);">
    <div style="width:96px; height:96px; border-radius:50%; background:#f0ebe2; display:flex; align-items:center; justify-content:center; margin:0 auto 24px;">
        <svg width="44" height="44" fill="none" stroke="#a8a29e" stroke-width="1.5" viewBox="0 0 24 24">
            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
    </div>
    <h3 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#292524; margin-bottom:8px;">No Photos Yet</h3>
    <p style="color:#a8a29e; font-size:0.9rem; max-width:360px; margin:0 auto;">Photos from our church events will appear here. Check back soon!</p>
</section>
@endif

<script>
(function () {
    const items   = Array.from(document.querySelectorAll('.gallery-item'));
    const lb      = document.getElementById('lightbox');
    const lbImg   = document.getElementById('lb-img');
    const lbCap   = document.getElementById('lb-caption');
    const lbCount = document.getElementById('lb-count');
    const lbClose = document.getElementById('lb-close');
    const lbPrev  = document.getElementById('lb-prev');
    const lbNext  = document.getElementById('lb-next');
    let current   = 0;
    const total   = items.length;

    const data = items.map(el => ({
        src: el.dataset.src,
        caption: el.dataset.caption || '',
        idx: parseInt(el.dataset.idx, 10)
    }));

    function show(idx) {
        current = ((idx % total) + total) % total;
        const d = data[current];
        lbImg.style.opacity = '0';
        lbImg.style.transform = 'scale(0.96)';
        setTimeout(() => {
            lbImg.src = d.src;
            lbImg.alt = d.caption;
            lbImg.style.transition = 'opacity 0.3s, transform 0.3s';
            lbImg.style.opacity = '1';
            lbImg.style.transform = 'scale(1)';
        }, 100);
        lbCap.textContent   = d.caption;
        lbCount.textContent = (current + 1) + ' / ' + total;
    }

    function open(idx) {
        show(idx);
        lb.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function close() {
        lb.style.display = 'none';
        document.body.style.overflow = '';
        lbImg.src = '';
    }

    items.forEach(el => {
        el.addEventListener('click', () => open(parseInt(el.dataset.idx, 10)));
    });

    if (lbClose) lbClose.addEventListener('click', close);
    if (lbPrev)  lbPrev.addEventListener('click', () => show(current - 1));
    if (lbNext)  lbNext.addEventListener('click', () => show(current + 1));
    if (lb)      lb.addEventListener('click', e => { if (e.target === lb) close(); });

    document.addEventListener('keydown', e => {
        if (lb.style.display !== 'flex') return;
        if (e.key === 'ArrowLeft')  show(current - 1);
        if (e.key === 'ArrowRight') show(current + 1);
        if (e.key === 'Escape')     close();
    });

    // Touch swipe
    let tx = 0;
    lb.addEventListener('touchstart', e => { tx = e.touches[0].clientX; }, { passive: true });
    lb.addEventListener('touchend', e => {
        const dx = e.changedTouches[0].clientX - tx;
        if (Math.abs(dx) > 40) dx < 0 ? show(current + 1) : show(current - 1);
    }, { passive: true });
})();

// Scroll reveal
const io = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('vis'); io.unobserve(e.target); } });
}, { threshold: 0.1 });
document.querySelectorAll('.gal-reveal').forEach(el => io.observe(el));
</script>

@endsection
