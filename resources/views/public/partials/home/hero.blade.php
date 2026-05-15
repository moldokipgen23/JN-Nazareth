
{{-- ══════════════════════════════════════════════════════════════
     1. HERO — Dynamic Banner Slider (falls back to YouTube if no slides)
══════════════════════════════════════════════════════════════ --}}
@php
    $heroTitle    = \App\Helpers\Settings::get('hero_title',    'Welcome to Our Community');
    $heroSubtitle = \App\Helpers\Settings::get('hero_subtitle', 'Connecting people. Sharing stories. Building together. All are welcome — come as you are.');
    $heroCta      = \App\Helpers\Settings::get('hero_cta_text', 'Learn About Us');
    $heroLink     = \App\Helpers\Settings::get('hero_cta_link', route('about'));

    // Extract YouTube video ID from any URL format
    $ytRaw = \App\Helpers\Settings::get('hero_youtube_url', '');
    $ytId  = '';
    if ($ytRaw) {
        if (preg_match('/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/', $ytRaw, $m)) {
            $ytId = $m[1];
        } elseif (preg_match('/^[a-zA-Z0-9_-]{11}$/', trim($ytRaw))) {
            $ytId = trim($ytRaw);
        }
    }
@endphp

@push('styles')
<style>
#hero-slider { position:relative; width:100%; overflow:hidden; height:clamp(520px,88vh,900px); background:#0d1a0f; }
.hero-slide {
    position:absolute; inset:0; opacity:0;
    transition:opacity 1.2s ease, transform 8s linear;
    transform:scale(1.06);
}
.hero-slide.active { opacity:1; transform:scale(1); z-index:1; }
.hero-slide.prev   { opacity:0; transform:scale(1.06); z-index:0; }
.hero-slide img    { width:100%; height:100%; object-fit:cover; }

.slider-dot { width:8px; height:8px; border-radius:50%; background:rgba(255,255,255,.4); cursor:pointer; transition:background .3s, transform .3s; border:none; padding:0; }
.slider-dot.active { background:#fff; transform:scale(1.4); }

.hero-text-in { animation:heroTextIn .9s cubic-bezier(.22,1,.36,1) forwards; opacity:0; transform:translateY(22px); }
@keyframes heroTextIn { to { opacity:1; transform:none; } }
</style>
@endpush

@if($slides->count())
{{-- === BANNER SLIDER === --}}
<section id="hero-slider" aria-label="Hero Banner">

    @foreach($slides as $i => $slide)
    <div class="hero-slide {{ $i === 0 ? 'active' : '' }}" id="slide-{{ $i }}">
        <img src="{{ \App\Helpers\Settings::storageUrl($slide->image) }}"
             alt="{{ $slide->title ?? 'Banner' }}"
             {{ $i === 0 ? '' : 'loading="lazy"' }}>
        {{-- Overlay --}}
        <div style="position:absolute; inset:0;
             background:linear-gradient(to bottom, rgba(0,0,0,.45) 0%, rgba(0,0,0,.2) 40%, rgba(0,0,0,.7) 100%),
             linear-gradient(160deg, rgba(5,30,15,.5) 0%, transparent 60%);"></div>
    </div>
    @endforeach

    {{-- Cross watermark --}}
    <div style="position:absolute;inset:0;z-index:2;display:flex;align-items:center;justify-content:center;pointer-events:none;opacity:.04;">
        <svg width="380" height="380" viewBox="0 0 100 100" fill="white">
            <rect x="45" y="5"  width="10" height="90" rx="3"/>
            <rect x="12" y="32" width="76" height="10" rx="3"/>
        </svg>
    </div>

    {{-- Slide text content --}}
    <div style="position:absolute; inset:0; z-index:3; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:40px 24px;">

        <div style="display:inline-flex; align-items:center; gap:7px; background:rgba(255,255,255,.1); backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,.18); border-radius:50px; padding:7px 18px; margin-bottom:26px; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.14em; color:rgba(255,255,255,.85);">
            <svg width="11" height="11" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
            {{ \App\Helpers\Settings::get('site_tagline', 'Together as One') }}
        </div>

        {{-- Dynamic title per slide --}}
        @foreach($slides as $i => $slide)
        <div class="slide-content" id="slide-content-{{ $i }}" style="{{ $i !== 0 ? 'display:none;' : '' }}">
            <h1 class="{{ $i === 0 ? 'hero-text-in' : '' }}"
                style="font-family:'Playfair Display',Georgia,serif; font-size:clamp(2.4rem,6vw,5rem); font-weight:800; line-height:1.1; color:#fff; text-shadow:0 4px 28px rgba(0,0,0,.55); max-width:800px; margin-bottom:14px; animation-delay:.1s;">
                {{ $slide->title ?: $heroTitle }}
            </h1>
            @if($slide->subtitle)
            <p style="color:rgba(255,255,255,.82); font-size:clamp(.95rem,2vw,1.2rem); max-width:560px; line-height:1.75; margin-bottom:36px; text-shadow:0 1px 10px rgba(0,0,0,.4);">
                {{ $slide->subtitle }}
            </p>
            @endif
            <div style="display:flex; flex-wrap:wrap; gap:14px; justify-content:center;">
                @if($slide->button_text && $slide->button_link)
                <a href="{{ $slide->button_link }}"
                   style="display:inline-flex; align-items:center; gap:9px; background:#fff; color:#0f766e; font-weight:700; font-size:.95rem; padding:13px 30px; border-radius:50px; text-decoration:none; box-shadow:0 6px 24px rgba(0,0,0,.3);">
                    {{ $slide->button_text }}
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                </a>
                @else
                <a href="{{ $heroLink }}"
                   style="display:inline-flex; align-items:center; gap:9px; background:#fff; color:#0f766e; font-weight:700; font-size:.95rem; padding:13px 30px; border-radius:50px; text-decoration:none; box-shadow:0 6px 24px rgba(0,0,0,.3);">
                    {{ $heroCta }}
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                </a>
                @endif
                <a href="{{ route('events') }}"
                   style="display:inline-flex; align-items:center; gap:9px; background:rgba(255,255,255,.12); backdrop-filter:blur(10px); border:1.5px solid rgba(255,255,255,.35); color:#fff; font-weight:600; font-size:.95rem; padding:13px 28px; border-radius:50px; text-decoration:none;">
                    Upcoming Events
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Navigation dots --}}
    @if($slides->count() > 1)
    <div style="position:absolute; bottom:32px; left:50%; transform:translateX(-50%); z-index:4; display:flex; gap:8px; align-items:center;">
        @foreach($slides as $i => $slide)
            <button class="slider-dot {{ $i === 0 ? 'active' : '' }}" id="dot-{{ $i }}" onclick="goToSlide({{ $i }})" aria-label="Slide {{ $i + 1 }}"></button>
        @endforeach
    </div>

    {{-- Prev/Next arrows --}}
    <button onclick="prevSlide()" style="position:absolute; left:20px; top:50%; transform:translateY(-50%); z-index:4; width:46px; height:46px; border-radius:50%; background:rgba(255,255,255,.15); backdrop-filter:blur(6px); border:1px solid rgba(255,255,255,.25); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;" aria-label="Previous slide">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <button onclick="nextSlide()" style="position:absolute; right:20px; top:50%; transform:translateY(-50%); z-index:4; width:46px; height:46px; border-radius:50%; background:rgba(255,255,255,.15); backdrop-filter:blur(6px); border:1px solid rgba(255,255,255,.25); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;" aria-label="Next slide">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
    @endif

    {{-- Bottom fade --}}
    <div style="position:absolute; bottom:0; left:0; right:0; z-index:4; height:100px; background:linear-gradient(to bottom, transparent, var(--cream)); pointer-events:none;"></div>
</section>

@push('scripts')
<script>
(function(){
    const total = {{ $slides->count() }};
    let current = 0, timer = null;

    function goToSlide(n) {
        document.getElementById('slide-' + current).classList.remove('active');
        document.getElementById('slide-content-' + current).style.display = 'none';
        document.getElementById('dot-' + current)?.classList.remove('active');

        current = ((n % total) + total) % total;

        const slide = document.getElementById('slide-' + current);
        slide.classList.add('active');
        const content = document.getElementById('slide-content-' + current);
        content.style.display = '';
        content.querySelectorAll('h1,p').forEach((el, i) => {
            el.style.animation = 'none';
            el.offsetHeight;
            el.style.animation = 'heroTextIn .9s cubic-bezier(.22,1,.36,1) forwards';
            el.style.animationDelay = (i * .12) + 's';
        });
        document.getElementById('dot-' + current)?.classList.add('active');
    }

    function nextSlide() { clearInterval(timer); goToSlide(current + 1); startAuto(); }
    function prevSlide() { clearInterval(timer); goToSlide(current - 1); startAuto(); }
    function startAuto() { timer = setInterval(() => goToSlide(current + 1), 5500); }

    window.goToSlide = goToSlide;
    window.nextSlide = nextSlide;
    window.prevSlide = prevSlide;

    if (total > 1) startAuto();

    // Touch swipe
    let tx = 0;
    const el = document.getElementById('hero-slider');
    el.addEventListener('touchstart', e => { tx = e.touches[0].clientX; }, { passive: true });
    el.addEventListener('touchend', e => {
        const dx = e.changedTouches[0].clientX - tx;
        if (Math.abs(dx) > 50) dx < 0 ? nextSlide() : prevSlide();
    }, { passive: true });
})();
</script>
@endpush

@else
{{-- === STATIC HERO (no slides, no video) === --}}
<section style="position:relative; width:100%; overflow:hidden; height:clamp(520px,85vh,860px); background:linear-gradient(160deg,#05190c 0%,#1b4332 100%);">
    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;opacity:.04;">
        <svg width="420" height="420" viewBox="0 0 100 100" fill="white"><rect x="45" y="5" width="10" height="90" rx="3"/><rect x="12" y="32" width="76" height="10" rx="3"/></svg>
    </div>
    <div style="position:relative; z-index:2; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:40px 24px;">
        <div style="display:inline-flex; align-items:center; gap:7px; background:rgba(255,255,255,.1); backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,.18); border-radius:50px; padding:7px 18px; margin-bottom:26px; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.14em; color:rgba(255,255,255,.85);">
            <svg width="11" height="11" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
            {{ \App\Helpers\Settings::get('site_tagline', 'Together as One') }}
        </div>
        <h1 class="hero-text-in" style="font-family:'Playfair Display',Georgia,serif; font-size:clamp(2.4rem,6vw,5rem); font-weight:800; line-height:1.1; color:#fff; text-shadow:0 4px 28px rgba(0,0,0,.55); max-width:800px; margin-bottom:18px;">{{ $heroTitle }}</h1>
        <p style="color:rgba(255,255,255,.82); font-size:clamp(.95rem,2vw,1.2rem); max-width:560px; line-height:1.75; margin-bottom:40px;">{{ $heroSubtitle }}</p>
        <div style="display:flex; flex-wrap:wrap; gap:14px; justify-content:center;">
            <a href="{{ $heroLink }}" style="display:inline-flex; align-items:center; gap:9px; background:#fff; color:#0f766e; font-weight:700; font-size:.95rem; padding:13px 30px; border-radius:50px; text-decoration:none; box-shadow:0 6px 24px rgba(0,0,0,.3);">{{ $heroCta }}<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></a>
            <a href="{{ route('events') }}" style="display:inline-flex; align-items:center; gap:9px; background:rgba(255,255,255,.12); backdrop-filter:blur(10px); border:1.5px solid rgba(255,255,255,.35); color:#fff; font-weight:600; font-size:.95rem; padding:13px 28px; border-radius:50px; text-decoration:none;">Upcoming Events<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></a>
        </div>
    </div>
    <div style="position:absolute; bottom:0; left:0; right:0; height:100px; background:linear-gradient(to bottom, transparent, var(--cream)); pointer-events:none;"></div>
</section>
@endif

{{-- Village video is now its own draggable section: village-video.blade.php --}}
@if(false)
<section id="village-video-section" style="position:relative; width:100%; overflow:hidden; height:clamp(380px,65vh,700px); background:#060f08; cursor:pointer;" onclick="toggleVillageVideoSound()" title="Click to toggle sound">

    {{-- YouTube background player --}}
    <div style="position:absolute; inset:0; z-index:0; pointer-events:none; overflow:hidden;">
        <div id="yt-village-player" style="position:absolute; top:50%; left:50%; width:177.78vh; min-width:100%; height:56.25vw; min-height:100%; transform:translate(-50%,-50%); opacity:0; transition:opacity 1.2s;"></div>
    </div>

    {{-- Dark cinematic overlay --}}
    <div style="position:absolute; inset:0; z-index:1; pointer-events:none;
         background:linear-gradient(to bottom, rgba(0,0,0,.5) 0%, rgba(0,0,0,.15) 45%, rgba(0,0,0,.65) 100%),
         linear-gradient(160deg, rgba(5,30,15,.5) 0%, transparent 55%);"></div>

    {{-- Cross watermark --}}
    <div style="position:absolute;inset:0;z-index:1;display:flex;align-items:center;justify-content:center;pointer-events:none;opacity:.04;">
        <svg width="340" height="340" viewBox="0 0 100 100" fill="white"><rect x="45" y="5" width="10" height="90" rx="3"/><rect x="12" y="32" width="76" height="10" rx="3"/></svg>
    </div>

    {{-- Content overlay --}}
    <div style="position:relative; z-index:2; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:40px 24px; pointer-events:none;">

        <div style="display:inline-flex; align-items:center; gap:7px; background:rgba(255,255,255,.1); backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,.2); border-radius:50px; padding:6px 16px; margin-bottom:22px; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.14em; color:rgba(255,255,255,.8);">
            <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            Village Showcase
        </div>

        <h2 style="font-family:'Playfair Display',Georgia,serif; font-size:clamp(2rem,5.5vw,4.2rem); font-weight:800; color:#fff; line-height:1.1; text-shadow:0 4px 28px rgba(0,0,0,.6); margin:0 0 16px; max-width:700px;">
            Our Community in Action
        </h2>

        <p style="color:rgba(255,255,255,.75); font-size:clamp(.88rem,1.8vw,1.1rem); max-width:480px; line-height:1.75; margin:0 0 36px; text-shadow:0 1px 12px rgba(0,0,0,.5);">
            A glimpse into our community — the people, the events, and the spirit that binds us.
        </p>

        {{-- Mute/Unmute indicator --}}
        <div id="vv-sound-btn" style="pointer-events:all; display:inline-flex; align-items:center; gap:8px; background:rgba(255,255,255,.12); backdrop-filter:blur(10px); border:1.5px solid rgba(255,255,255,.25); color:#fff; font-weight:600; font-size:.85rem; padding:10px 22px; border-radius:50px; cursor:pointer; transition:background .2s;">
            <svg id="vv-mute-icon" width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02z"/>
            </svg>
            <span id="vv-sound-label">Click to Unmute</span>
        </div>
    </div>

    {{-- Bottom fade --}}
    <div style="position:absolute; bottom:0; left:0; right:0; z-index:3; height:120px; background:linear-gradient(to bottom, transparent, var(--cream)); pointer-events:none;"></div>
</section>

@push('scripts')
<script>
(function() {
    var ytVillagePlayer;
    var isMuted = true;
    var videoId = '{{ $ytId }}';

    function loadYT() {
        if (window.YT && window.YT.Player) { initPlayer(); return; }
        var tag = document.createElement('script');
        tag.src = 'https://www.youtube.com/iframe_api';
        document.head.appendChild(tag);
        window.onYouTubeIframeAPIReady = initPlayer;
    }

    function initPlayer() {
        ytVillagePlayer = new YT.Player('yt-village-player', {
            videoId: videoId,
            playerVars: {
                autoplay: 1, mute: 1, loop: 1, playlist: videoId,
                controls: 0, showinfo: 0, rel: 0, iv_load_policy: 3,
                modestbranding: 1, playsinline: 1, disablekb: 1, fs: 0
            },
            events: {
                onReady: function(e) { e.target.mute(); e.target.playVideo(); },
                onStateChange: function(e) {
                    if (e.data === YT.PlayerState.ENDED) e.target.playVideo();
                    if (e.data === YT.PlayerState.PLAYING) {
                        var el = document.querySelector('#yt-village-player iframe') || document.getElementById('yt-village-player');
                        if (el) el.style.opacity = '1';
                    }
                }
            }
        });
    }

    window.toggleVillageVideoSound = function() {
        if (!ytVillagePlayer || typeof ytVillagePlayer.isMuted !== 'function') return;
        if (isMuted) {
            ytVillagePlayer.unMute(); ytVillagePlayer.setVolume(80);
            isMuted = false;
            document.getElementById('vv-sound-label').textContent = 'Click to Mute';
            document.getElementById('vv-mute-icon').innerHTML = '<path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM19 12c0 2.95-1.67 5.5-4.1 6.78l1.44 1.44C19.33 18.6 21 15.47 21 12c0-3.47-1.67-6.6-4.66-8.22l-1.44 1.44C17.33 6.5 19 9.05 19 12z"/>';
        } else {
            ytVillagePlayer.mute();
            isMuted = true;
            document.getElementById('vv-sound-label').textContent = 'Click to Unmute';
            document.getElementById('vv-mute-icon').innerHTML = '<path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02z"/>';
        }
    };

    // Load when section is visible (IntersectionObserver for performance)
    var section = document.getElementById('village-video-section');
    if ('IntersectionObserver' in window) {
        var obs = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting) { loadYT(); obs.disconnect(); }
        }, { threshold: 0.1 });
        obs.observe(section);
    } else {
        loadYT();
    }
})();
</script>
@endpush
@endif


