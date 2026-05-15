{{-- ══════════════════════════════════════════════════════════════
     6. VIDEOS — SERMONS & CHOIR  (moved up from bottom)
══════════════════════════════════════════════════════════════ --}}
@if(isset($videos) && $videos->count() && \App\Helpers\Settings::get('sec_show_videos', '1'))
@php
  $videoList      = $videos->values();
  $totalVideos    = $videoList->count();
  $secTitleVideos = \App\Helpers\Settings::get('sec_title_videos', 'Sermons & Videos');
@endphp
<section class="py-20" style="background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div style="display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:36px; flex-wrap:wrap; gap:12px;">
            <div>
                <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:#14b8a6; margin-bottom:8px; display:block;">Watch &amp; Listen</span>
                <h2 style="font-size:30px; font-weight:800; color:#ffffff; margin:0; line-height:1.2;">{{ $secTitleVideos }}</h2>
                <p style="font-size:14px; color:#94a3b8; margin:6px 0 0;">{{ \App\Helpers\Settings::get('sec_sub_videos', 'Messages, choir worship, and church events') }}</p>
            </div>
            <a href="{{ route('videos') }}"
               style="display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:600; color:#14b8a6; text-decoration:none; border:1px solid #14b8a640; padding:8px 18px; border-radius:50px; transition:.2s;"
               onmouseover="this.style.background='rgba(20,184,166,.1)'" onmouseout="this.style.background='transparent'">
                View All Videos
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div style="position:relative;">
            <button id="vid-prev" onclick="vidNav(-1)"
                    style="position:absolute; left:-18px; top:50%; transform:translateY(-50%); z-index:10; width:40px; height:40px; border-radius:50%; background:rgba(255,255,255,.12); border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#fff; transition:.2s; backdrop-filter:blur(4px);"
                    onmouseover="this.style.background='rgba(20,184,166,.5)'" onmouseout="this.style.background='rgba(255,255,255,.12)'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            </button>

            <div style="overflow:hidden; border-radius:16px;">
                <div id="vid-track" style="display:flex; gap:16px; transition:transform .4s cubic-bezier(.4,0,.2,1); will-change:transform;">
                    @foreach($videoList as $idx => $video)
                    <div class="vid-card" style="flex:0 0 calc(33.333% - 11px); min-width:0;"
                         data-embed="{{ $video->embed_url }}"
                         data-title="{{ e($video->title) }}"
                         data-desc="{{ e($video->description ?? '') }}"
                         data-cat="{{ $video->category }}">
                        <div onclick="openVideoModal(this.closest('.vid-card'))"
                             style="cursor:pointer; border-radius:14px; overflow:hidden; background:#1e293b; border:1px solid rgba(255,255,255,.06); transition:transform .25s, box-shadow .25s;"
                             onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 20px 40px rgba(0,0,0,.4)'; this.style.borderColor='rgba(20,184,166,.4)';"
                             onmouseout="this.style.transform=''; this.style.boxShadow=''; this.style.borderColor='rgba(255,255,255,.06)';">
                            <div style="position:relative; padding-top:56.25%; background:#0f172a; overflow:hidden;">
                                <img src="{{ $video->thumbnail }}" alt="{{ $video->title }}"
                                     style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transition:transform .4s;"
                                     onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform=''">
                                <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,.7) 0%, rgba(0,0,0,.1) 60%);"></div>
                                <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center;">
                                    <div style="width:52px; height:52px; border-radius:50%; background:rgba(20,184,166,.85); display:flex; align-items:center; justify-content:center; backdrop-filter:blur(4px); box-shadow:0 0 0 8px rgba(20,184,166,.2); transition:.2s;"
                                         onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform=''">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="white" style="margin-left:3px;"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                </div>
                                @php $catBg = match($video->category) { 'sermon'=>'rgba(59,130,246,.9)','choir'=>'rgba(168,85,247,.9)','event'=>'rgba(249,115,22,.9)',default=>'rgba(100,116,139,.9)' }; @endphp
                                <div style="position:absolute; top:10px; left:10px; background:{{ $catBg }}; color:#fff; font-size:9px; font-weight:800; padding:3px 8px; border-radius:20px; text-transform:uppercase; letter-spacing:.06em; backdrop-filter:blur(4px);">{{ $video->category }}</div>
                                <div style="position:absolute; bottom:10px; right:10px; background:rgba(0,0,0,.7); color:#fff; font-size:10px; font-weight:600; padding:2px 7px; border-radius:5px; backdrop-filter:blur(4px);">YouTube</div>
                            </div>
                            <div style="padding:14px 16px 16px;">
                                <h3 style="font-size:14px; font-weight:700; color:#f1f5f9; margin:0 0 6px; line-height:1.4; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">{{ $video->title }}</h3>
                                @if($video->description)
                                    <p style="font-size:12px; color:#64748b; margin:0; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.5;">{{ $video->description }}</p>
                                @endif
                                <div style="margin-top:10px;">
                                    <span style="font-size:10px; color:#14b8a6; font-weight:600; display:flex; align-items:center; gap:4px;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="#14b8a6"><path d="M8 5v14l11-7z"/></svg>
                                        Watch Now
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <button id="vid-next" onclick="vidNav(1)"
                    style="position:absolute; right:-18px; top:50%; transform:translateY(-50%); z-index:10; width:40px; height:40px; border-radius:50%; background:rgba(255,255,255,.12); border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#fff; transition:.2s; backdrop-filter:blur(4px);"
                    onmouseover="this.style.background='rgba(20,184,166,.5)'" onmouseout="this.style.background='rgba(255,255,255,.12)'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

        @if($totalVideos > 1)
        <div id="vid-dots" style="display:flex; justify-content:center; gap:7px; margin-top:24px;">
            @for($d = 0; $d < $totalVideos; $d++)
            <button onclick="vidGoTo({{ $d }})"
                    style="width:8px; height:8px; border-radius:50%; border:none; cursor:pointer; padding:0; transition:.2s; background:{{ $d === 0 ? '#14b8a6' : 'rgba(255,255,255,.25)' }}; transform:{{ $d === 0 ? 'scale(1.3)' : 'scale(1)' }};"
                    data-dot="{{ $d }}"></button>
            @endfor
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
                style="position:absolute; top:-44px; right:0; background:rgba(255,255,255,.15); border:none; width:36px; height:36px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#fff; z-index:10;"
                onmouseover="this.style.background='rgba(255,255,255,.3)'" onmouseout="this.style.background='rgba(255,255,255,.15)'">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div style="position:relative; padding-top:56.25%; background:#000; border-radius:16px; overflow:hidden; box-shadow:0 40px 80px rgba(0,0,0,.6);">
            <iframe id="modal-iframe" src=""
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen
                    style="position:absolute; inset:0; width:100%; height:100%; border:none;"></iframe>
        </div>
        <div style="margin-top:14px; text-align:center;">
            <p id="modal-title" style="color:#f1f5f9; font-size:15px; font-weight:700; margin:0;"></p>
            <p id="modal-desc" style="color:#64748b; font-size:13px; margin:4px 0 0;"></p>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var track   = document.getElementById('vid-track');
    var dots    = document.querySelectorAll('#vid-dots [data-dot]');
    var cards   = track ? track.querySelectorAll('.vid-card') : [];
    var total   = cards.length;
    var current = 0;
    var perView = 3;

    function getPerView() {
        if (window.innerWidth < 640)  return 1;
        if (window.innerWidth < 1024) return 2;
        return 3;
    }
    function getCardWidth() {
        if (!cards.length) return 0;
        return cards[0].offsetWidth + 16;
    }
    function update() {
        perView = getPerView();
        var maxStart = Math.max(0, total - perView);
        if (current > maxStart) current = maxStart;
        track.style.transform = 'translateX(-' + (current * getCardWidth()) + 'px)';
        var pct = perView === 1 ? '100%' : perView === 2 ? 'calc(50% - 8px)' : 'calc(33.333% - 11px)';
        cards.forEach(function(c) { c.style.flexBasis = pct; });
        dots.forEach(function(d, i) {
            d.style.background = i === current ? '#14b8a6' : 'rgba(255,255,255,.25)';
            d.style.transform  = i === current ? 'scale(1.3)' : 'scale(1)';
        });
        var prevBtn = document.getElementById('vid-prev');
        var nextBtn = document.getElementById('vid-next');
        if (prevBtn) prevBtn.style.opacity = current === 0 ? '0.3' : '1';
        if (nextBtn) nextBtn.style.opacity = current >= maxStart ? '0.3' : '1';
    }
    window.vidNav = function(dir) {
        perView = getPerView();
        var maxStart = Math.max(0, total - perView);
        current = Math.min(maxStart, Math.max(0, current + dir));
        update();
    };
    window.vidGoTo = function(idx) { current = idx; update(); };
    window.addEventListener('resize', update);
    update();

    window.openVideoModal = function(card) {
        var url   = card.dataset.embed + '&autoplay=1';
        document.getElementById('modal-iframe').src = url;
        document.getElementById('modal-title').textContent = card.dataset.title;
        document.getElementById('modal-desc').textContent  = card.dataset.desc;
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
})();
</script>
@endpush
@endif

