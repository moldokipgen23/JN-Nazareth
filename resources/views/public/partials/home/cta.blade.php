{{-- ══════════════════════════════════════════════════════════════
     10. CONTACT / JOIN CTA BANNER
══════════════════════════════════════════════════════════════ --}}
@if(\App\Helpers\Settings::get('sec_show_cta', '1'))
@php
    $ctaAddr   = \App\Helpers\Settings::get('contact_address', '');
    $ctaPhone  = \App\Helpers\Settings::get('contact_phone');
    $ctaTitle  = \App\Helpers\Settings::get('sec_title_cta', 'Come Worship With Us');
    $ctaSub    = \App\Helpers\Settings::get('sec_sub_cta', 'You are always welcome. Come and experience the warmth and fellowship of our community.');
    $btn1Text  = \App\Helpers\Settings::get('cta_btn1_text', 'Learn About Us');
    $btn1Link  = \App\Helpers\Settings::get('cta_btn1_link', '/about');
    $btn2Text  = \App\Helpers\Settings::get('cta_btn2_text', 'See Events');
    $btn2Link  = \App\Helpers\Settings::get('cta_btn2_link', '/events');
@endphp
<section style="position:relative; overflow:hidden; background:var(--secondary);">
    @if(\App\Helpers\Settings::get('cta_bg_image'))<div style="position:absolute; inset:0; background-image:url('{{ asset('storage/'.\App\Helpers\Settings::get('cta_bg_image')) }}'); background-size:cover; background-position:center; opacity:0.18;"></div>@endif
    <div style="position:absolute; inset:0; background:linear-gradient(160deg, var(--secondary) 0%, var(--primary) 100%); opacity:0.9;"></div>

    <div style="position:relative; z-index:10; max-width:760px; margin:0 auto; padding:80px 24px; text-align:center;">
        <div style="margin-bottom:20px;">
            <svg width="36" height="36" fill="rgba(255,255,255,0.5)" viewBox="0 0 24 24"><rect x="11" y="2" width="2" height="20" rx="1"/><rect x="3" y="8" width="18" height="2" rx="1"/></svg>
        </div>
        <h2 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,4vw,3rem); font-weight:800; color:#fff; line-height:1.2; margin-bottom:16px;">
            {{ $ctaTitle }}
        </h2>
        <p style="color:rgba(255,255,255,0.78); font-size:1.05rem; line-height:1.7; margin-bottom:36px;">
            {{ $ctaSub }}
        </p>
        @if($ctaAddr)
        <p style="display:inline-flex; align-items:center; gap:7px; color:rgba(255,255,255,0.65); font-size:0.88rem; margin-bottom:32px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
            {{ $ctaAddr }}
        </p>
        @endif
        <div style="display:flex; flex-wrap:wrap; gap:14px; justify-content:center;">
            @if($btn1Text && $btn1Link)
            <a href="{{ $btn1Link }}"
               style="background:#fff; color:var(--primary); font-weight:700; padding:13px 30px; border-radius:50px; font-size:0.95rem; text-decoration:none; box-shadow:0 4px 18px rgba(0,0,0,0.25);">
                {{ $btn1Text }}
            </a>
            @endif
            @if($btn2Text && $btn2Link)
            <a href="{{ $btn2Link }}"
               style="border:2px solid rgba(255,255,255,0.6); color:#fff; font-weight:600; padding:13px 30px; border-radius:50px; font-size:0.95rem; text-decoration:none;">
                {{ $btn2Text }}
            </a>
            @endif
        </div>
    </div>
</section>
@endif
