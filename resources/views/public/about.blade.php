@extends('layouts.public')
@section('title', 'Our Story')
@php
    $aboutMapLink = \App\Helpers\Settings::get('location_map_link', 'https://maps.google.com');
    $siteName     = \App\Helpers\Settings::get('site_name', 'Our Community');
    $aboutHeroBg  = \App\Helpers\Settings::get('about_hero_image', '');
    $heroBgStyle  = $aboutHeroBg ? 'background-image:url('.asset('storage/'.$aboutHeroBg).')' : 'background:linear-gradient(160deg,#1a3a2e,#2d6a4f)';
@endphp

@push('styles')
<style>
.story-reveal { opacity:0; transform:translateY(32px); transition:opacity .7s ease, transform .7s ease; }
.story-reveal.visible { opacity:1; transform:none; }

.parallax-hero { background-attachment: fixed; background-size: cover; background-position: center; }
@media (max-width: 768px) {
    .parallax-hero { background-attachment: scroll; }
    .about-ministry-grid { grid-template-columns: 1fr !important; }
    .about-cta-btns { flex-direction: column !important; }
    .about-cta-btns a { width: 100% !important; justify-content: center !important; }
}

.story-timeline::before {
    content:''; position:absolute; left:18px; top:0; bottom:0;
    width:2px; background:linear-gradient(to bottom, var(--primary), var(--accent));
    border-radius:2px;
}
</style>
@endpush

@section('content')

{{-- ══ Cinematic Hero ══ --}}
<section class="page-hero parallax-hero" style="min-height:420px; {{ $heroBgStyle }};">
    <div style="position:absolute; inset:0; background:linear-gradient(160deg, rgba(5,25,12,.8) 0%, rgba(27,67,50,.6) 100%);"></div>
    <div style="position:relative; z-index:2; max-width:800px; margin:0 auto; padding:100px 24px 80px; text-align:center;">

        <h1 style="font-family:'Playfair Display',Georgia,serif; font-size:clamp(2.4rem,6vw,4.5rem); font-weight:800; color:#fff; line-height:1.1; margin:0 0 18px; text-shadow:0 4px 24px rgba(0,0,0,.5);">
            Our Story
        </h1>

        <div style="display:flex; align-items:center; justify-content:center; gap:14px; margin-bottom:18px; opacity:.6;">
            <span style="height:1px; width:55px; background:rgba(255,255,255,.5);"></span>
            <svg width="16" height="16" fill="white" viewBox="0 0 24 24"><rect x="11" y="2" width="2" height="20" rx="1"/><rect x="3" y="8" width="18" height="2" rx="1"/></svg>
            <span style="height:1px; width:55px; background:rgba(255,255,255,.5);"></span>
        </div>

        <p style="color:rgba(255,255,255,.8); font-size:1.05rem; max-width:520px; margin:0 auto; line-height:1.75;">
            {{ \App\Helpers\Settings::get('site_tagline', 'Connecting people. Sharing stories. Building together.') }}
        </p>
    </div>
</section>

{{-- ══ Intro Cards ══ --}}
<section style="background:var(--cream); padding:60px 0;" class="story-reveal">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:20px; text-align:center;">
            @foreach([
                ['icon'=>'👥','title'=>\App\Helpers\Settings::get('stat_1_label','Members'),'value'=>\App\Helpers\Settings::get('stat_1_value','500+'),'sub'=>'and growing'],
                ['icon'=>'📅','title'=>\App\Helpers\Settings::get('stat_3_label','Programmes'),'value'=>\App\Helpers\Settings::get('stat_3_value','12'),'sub'=>'every week'],
                ['icon'=>'🗓️','title'=>\App\Helpers\Settings::get('stat_2_label','Years Active'),'value'=>\App\Helpers\Settings::get('stat_2_value','70+'),'sub'=>'of service'],
                ['icon'=>'🏠','title'=>\App\Helpers\Settings::get('stat_4_label','Families'),'value'=>\App\Helpers\Settings::get('stat_4_value','200+'),'sub'=>'served'],
            ] as $card)
            <div style="background:#fff; border-radius:18px; padding:24px 20px; border:1px solid #e7dfd4; box-shadow:0 4px 18px rgba(0,0,0,.06);">
                <div style="font-size:2rem; margin-bottom:10px;">{{ $card['icon'] }}</div>
                <div style="font-size:.6rem; font-weight:800; text-transform:uppercase; letter-spacing:.12em; color:var(--accent); margin-bottom:6px;">{{ $card['title'] }}</div>
                <div style="font-family:'Playfair Display',serif; font-size:1.3rem; font-weight:800; color:#1c1917; line-height:1.2;">{{ $card['value'] }}</div>
                <div style="font-size:.75rem; color:#78716c; margin-top:4px;">{{ $card['sub'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ Dynamic Page Content ══ --}}
@if($page->content)
<section style="background:#fff; padding:72px 0;" class="story-reveal">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div style="text-align:center; margin-bottom:44px;">
            <span class="section-label">The Story</span>
            <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.8rem,4vw,2.6rem); font-weight:800; color:#1c1917; margin:12px 0 0; line-height:1.2;">
                {{ $page->title }}
            </h2>
        </div>
        <div style="font-size:1.05rem; color:#44403c; line-height:1.9; max-width:720px; margin:0 auto;">
            {!! $page->content !!}
        </div>
    </div>
</section>
@endif

{{-- ══ Church Section ══ --}}
<section style="background:var(--cream); padding:80px 0;" class="story-reveal">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:40px; align-items:center;">

            <div>
                <span class="section-label">About Us</span>
                <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.8rem,3.5vw,2.5rem); font-weight:800; color:#1c1917; margin:14px 0 18px; line-height:1.2;">
                    {{ $siteName }}
                </h2>
                <p style="color:#57534e; font-size:1rem; line-height:1.85; margin-bottom:24px;">
                    {{ \App\Helpers\Settings::get('about_preview', 'We are a vibrant community committed to spreading love and serving our neighbours. Founded to serve with the love of God, we stand as a beacon of hope, unity, and service.') }}
                </p>

                <div style="display:flex; align-items:center; gap:6px; margin-bottom:16px;">
                    @for($i=0;$i<5;$i++)
                    <svg width="18" height="18" fill="#facc15" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    @endfor
                    <span style="font-size:.85rem; font-weight:700; color:#1c1917; margin-left:4px;">5.0</span>
                    <span style="font-size:.8rem; color:#94a3b8; margin-left:2px;">· Google Rating</span>
                </div>

                <div class="about-ministry-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:20px;">
                    @foreach(['Programmes','Community Service','Youth Activities','Member Events'] as $ministry)
                    <div style="display:flex; align-items:center; gap:8px; font-size:.85rem; font-weight:600; color:#44403c;">
                        <span style="width:7px; height:7px; border-radius:50%; background:var(--accent); flex-shrink:0;"></span>
                        {{ $ministry }}
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Map of our location --}}
            <div>
                <div style="border-radius:20px; overflow:hidden; box-shadow:0 8px 32px rgba(0,0,0,.14); border:1px solid #e7dfd4;">
                    @if(\App\Helpers\Settings::get('map_embed_url'))
                    <iframe
                        src="{{ \App\Helpers\Settings::get('map_embed_url') }}"
                        width="100%" height="300"
                        style="border:0; display:block; min-height:220px;"
                        allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                    @else
                    <div style="width:100%;height:300px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;color:#94a3b8;font-size:.85rem;">Map embed not configured — set it in Admin → Customizer → Sections → Map</div>
                    @endif
                </div>
                <div style="display:flex; gap:10px; margin-top:14px;">
                    <a href="{{ $aboutMapLink }}" target="_blank" rel="noopener"
                       style="flex:1; display:inline-flex; align-items:center; justify-content:center; gap:7px; background:var(--primary); color:#fff; font-weight:700; font-size:.82rem; padding:11px 16px; border-radius:12px; text-decoration:none;">
                        <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        Open Maps
                    </a>
                    <a href="{{ $aboutMapLink }}" target="_blank" rel="noopener"
                       style="flex:1; display:inline-flex; align-items:center; justify-content:center; gap:7px; background:#fff; color:var(--primary); border:2px solid var(--primary); font-weight:700; font-size:.82rem; padding:11px 16px; border-radius:12px; text-decoration:none;">
                        Get Directions
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══ Community Life Section ══ --}}
<section style="background:#fff; padding:80px 0;" class="story-reveal">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div style="text-align:center; margin-bottom:44px;">
            <span class="section-label">Culture & Identity</span>
            <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.6rem,3vw,2.3rem); font-weight:800; color:#1c1917; margin:12px 0 0;">
                Community Life
            </h2>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px,1fr)); gap:24px;">
            @foreach([
                ['title'=>'Welcome & Belonging','icon'=>'⛪','desc'=>'We bring people together regularly through programmes, gatherings, and shared activities. Everyone is welcome.'],
                ['title'=>'Heritage & Culture','icon'=>'🌿','desc'=>'We honour our heritage and identity. Our community values its unique story and shared traditions.'],
                ['title'=>'Service & Outreach','icon'=>'🤝','desc'=>'From community clean-ups to support for families in need, we serve those around us with care and dedication.'],
                ['title'=>'Youth & Education','icon'=>'📚','desc'=>'Our youth programmes and educational initiatives empower the next generation to grow in leadership, skills, and knowledge.'],
            ] as $item)
            <div style="background:var(--cream); border-radius:18px; padding:26px; border:1px solid #e7dfd4;">
                <div style="font-size:2rem; margin-bottom:14px;">{{ $item['icon'] }}</div>
                <h3 style="font-family:'Playfair Display',serif; font-size:1.1rem; font-weight:700; color:#1c1917; margin:0 0 10px;">{{ $item['title'] }}</h3>
                <p style="font-size:.87rem; color:#57534e; line-height:1.7; margin:0;">{{ $item['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ CTA ══ --}}
<section style="padding:72px 0; background:linear-gradient(160deg,var(--secondary) 0%,var(--primary) 100%);" class="story-reveal">
    <div style="text-align:center; padding:0 24px;">
        <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.8rem,4vw,2.8rem); font-weight:800; color:#fff; margin:0 0 16px;">
            Come Visit Us
        </h2>
        <p style="color:rgba(255,255,255,.8); font-size:1rem; max-width:440px; margin:0 auto 32px; line-height:1.7;">
            {{ \App\Helpers\Settings::get('sec_sub_cta', 'You are always welcome. Come and experience the warmth and fellowship of our community.') }}
        </p>
        <div class="about-cta-btns" style="display:flex; gap:14px; justify-content:center; flex-wrap:wrap;">
            <a href="{{ route('events') }}"
               style="display:inline-flex; align-items:center; gap:8px; background:#fff; color:var(--primary); font-weight:700; font-size:.9rem; padding:12px 28px; border-radius:50px; text-decoration:none; box-shadow:0 4px 18px rgba(0,0,0,.2);">
                Upcoming Events
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </a>
            <a href="{{ route('gallery') }}"
               style="display:inline-flex; align-items:center; gap:8px; background:rgba(255,255,255,.15); backdrop-filter:blur(8px); border:1.5px solid rgba(255,255,255,.3); color:#fff; font-weight:600; font-size:.9rem; padding:12px 26px; border-radius:50px; text-decoration:none;">
                View Gallery
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</section>

@push('scripts')
<script>
const io = new IntersectionObserver(entries => {
    entries.forEach(e => { if(e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); } });
}, { threshold: 0.12 });
document.querySelectorAll('.story-reveal').forEach(el => io.observe(el));
</script>
@endpush

@endsection
