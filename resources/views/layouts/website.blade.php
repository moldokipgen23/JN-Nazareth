<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @php
    $seoDesc  = trim((string) setting('seo_meta_description'))
                ?: setting('school_tagline', 'Quality Education for a Better Future') . ' — ' . setting('school_name');
    $ogLogo   = setting('school_logo') ? \App\Helpers\Settings::storageUrl(setting('school_logo')) : asset('images/logo.png');
    $ogImage  = setting('seo_og_image') ? \App\Helpers\Settings::storageUrl(setting('seo_og_image')) : $ogLogo;
    $favicon  = setting('favicon') ? \App\Helpers\Settings::storageUrl(setting('favicon')) : $ogLogo;
    $gVerify  = trim((string) setting('seo_google_verification'));
  @endphp
  <meta name="description" content="@yield('description', $seoDesc)">
  @if(trim((string) setting('seo_meta_keywords')) !== '')
  <meta name="keywords" content="{{ setting('seo_meta_keywords') }}">
  @endif
  @if($gVerify !== '')
  <meta name="google-site-verification" content="{{ $gVerify }}">
  @endif
  <meta property="og:title" content="@yield('title', setting('school_name'))">
  <meta property="og:description" content="@yield('description', $seoDesc)">
  <meta property="og:image" content="{{ $ogImage }}">
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="@yield('title', setting('school_name'))">
  <meta name="twitter:description" content="@yield('description', $seoDesc)">
  <meta name="twitter:image" content="{{ $ogImage }}">
  <link rel="canonical" href="{{ url()->current() }}">
  <title>@yield('title', setting('school_name')) | Khengjang, Churachandpur, Manipur</title>

  <link rel="icon" href="{{ $favicon }}">
  <link rel="apple-touch-icon" href="{{ $favicon }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/website.css') }}">
  @stack('styles')
</head>
<body>

@php
  $schoolName = setting('school_name', 'J.N. Nazareth English School');
  $wa         = preg_replace('/\D+/', '', setting('whatsapp', '919862880292'));
  $navLogo    = setting('school_logo') ? \App\Helpers\Settings::storageUrl(setting('school_logo')) : asset('images/logo.png');
@endphp

  {{-- ===== TOP TICKER ===== --}}
  @if(setting('sec_show_ticker', '1') !== '0')
  <div class="ticker">
    <div class="ticker-inner">
      <span class="ticker-badge">NEW</span>
      <span>{{ setting('ticker_text', 'Admissions Open for the New Session — Enquire Now') }}</span>
      <span style="opacity:.5">·</span>
      <span>Admissions Open &nbsp;—&nbsp;
        <a href="{{ route('home') }}#admission" style="color:#93c5fd;font-weight:700;text-decoration:underline;">Apply Now</a>
      </span>
    </div>
  </div>
  @endif

  {{-- ===== NAVBAR ===== --}}
  <nav id="nav" class="top">
    <div class="container">
      <div class="nav-wrap">
        <a href="{{ route('home') }}" class="nav-logo">
          <img src="{{ $navLogo }}" alt="{{ $schoolName }} Logo">
          <div class="nav-logo-text">
            <strong>{{ $schoolName }}</strong>
            <span>Khengjang, Churachandpur &middot; Est. {{ setting('school_established', '1996') }}</span>
          </div>
        </a>

        @php $rn = request()->route()?->getName(); @endphp
        <div class="nav-links">
          <a href="{{ route('home') }}" @class(['active' => $rn === 'home'])>Home</a>
          <a href="{{ route('about') }}" @class(['active' => $rn === 'about'])>About</a>
          <a href="{{ route('academics') }}" @class(['active' => in_array($rn, ['academics','academics.fee','academics.timing'])])>Academics</a>
          @if(setting('sec_show_student_life_page', '1') !== '0')
            <a href="{{ route('student-life') }}" @class(['active' => in_array($rn, ['student-life','gallery.folder'])])>Student Life</a>
          @endif
          <a href="{{ route('news') }}" @class(['active' => in_array($rn, ['news','news.show'])])>News</a>
          <a href="{{ route('contact') }}" @class(['active' => $rn === 'contact'])>Contact</a>
          @if(setting('sec_show_admission_page', '1') !== '0')
            <a href="{{ route('admission') }}" class="nav-admit">Admission Open</a>
          @endif
        </div>

        <div class="hamburger" id="ham" onclick="toggleNav()">
          <span></span><span></span><span></span>
        </div>
      </div>
    </div>
  </nav>

  {{-- ===== MOBILE NAV ===== --}}
  <div class="mobile-nav" id="mobileNav">
    <a href="{{ route('home') }}"      onclick="closeNav()"><i class="fas fa-house"></i>Home</a>
    <a href="{{ route('about') }}"     onclick="closeNav()"><i class="fas fa-school"></i>About School</a>
    <a href="{{ route('academics') }}" onclick="closeNav()"><i class="fas fa-book"></i>Academics</a>
    @if(setting('sec_show_admission_page', '1') !== '0')
      <a href="{{ route('admission') }}" onclick="closeNav()"><i class="fas fa-pen"></i>Admission</a>
    @endif
    @if(setting('sec_show_student_life_page', '1') !== '0')
      <a href="{{ route('student-life') }}" onclick="closeNav()"><i class="fas fa-children"></i>Student Life</a>
    @endif
    <a href="{{ route('news') }}"      onclick="closeNav()"><i class="fas fa-newspaper"></i>News &amp; Notices</a>
    <a href="{{ route('contact') }}"   onclick="closeNav()"><i class="fas fa-map-marker-alt"></i>Contact Us</a>

    {{-- Social links at the bottom of the mobile menu --}}
    <div class="mnav-social">
      @if(setting('social_facebook'))<a href="{{ setting('social_facebook') }}" target="_blank" rel="noopener" class="msoc msoc-fb" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>@endif
      @if(setting('social_instagram'))<a href="{{ setting('social_instagram') }}" target="_blank" rel="noopener" class="msoc msoc-ig" aria-label="Instagram"><i class="fab fa-instagram"></i></a>@endif
      <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener" class="msoc msoc-wa" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
      @if(setting('social_youtube'))<a href="{{ setting('social_youtube') }}" target="_blank" rel="noopener" class="msoc msoc-yt" aria-label="YouTube"><i class="fab fa-youtube"></i></a>@endif
    </div>
  </div>

  @yield('content')

  {{-- ===== FOOTER ===== --}}
  <footer>
    <div class="container">
      <div class="ft-grid">
        <div class="ft-brand">
          <img src="{{ $navLogo }}" alt="{{ $schoolName }} Logo">
          <div class="ft-brand-name">{{ $schoolName }}</div>
          <p class="ft-desc">One of the finest English medium schools in Churachandpur, Manipur — offering quality education, strong values, and disciplined learning from Preparatory to Class 10 since {{ setting('school_established', '1996') }}.</p>
        </div>

        <div>
          <div class="ft-col-title">Quick Links</div>
          <div class="ft-links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('about') }}">About School</a>
            @if(setting('sec_show_admission_page', '1') !== '0')
              <a href="{{ route('admission') }}">Admission</a>
            @endif
            @if(setting('sec_show_student_life_page', '1') !== '0')
              <a href="{{ route('student-life') }}">Student Life</a>
            @endif
            <a href="{{ route('news') }}">News &amp; Notices</a>
            <a href="{{ route('hall-of-fame') }}">Hall of Fame</a>
          </div>
        </div>

        <div>
          <div class="ft-col-title">Academics</div>
          <div class="ft-links">
            <a href="{{ route('academics') }}">Classes &amp; Programme</a>
            <a href="{{ route('academics.fee') }}">Fee Structure</a>
            <a href="{{ route('academics.timing') }}">School Timing</a>
            <a href="{{ route('academics.calendar') }}">Academic Calendar</a>
            <a href="{{ route('academics.curriculum') }}">Curriculum</a>
            <a href="{{ route('academics.results') }}">Results</a>
          </div>
        </div>

        <div>
          <div class="ft-col-title">Contact Us</div>
          <div class="ft-ct-item">
            <i class="fas fa-map-marker-alt"></i>
            <span>{{ setting('contact_address', 'Khengjang, B.P.O. Koite, Churachandpur – 795128, Manipur, India') }}</span>
          </div>
          <div class="ft-ct-item">
            <i class="fab fa-whatsapp"></i>
            <a href="https://wa.me/{{ $wa }}" style="color:inherit;">{{ setting('contact_phone', '+91 98628 80292') }}</a>
          </div>
          <div class="ft-ct-item">
            <i class="fas fa-user-tie"></i>
            <span>Principal: {{ setting('principal_name', 'Ngamboi Kipgen') }}</span>
          </div>
          <div class="ft-ct-item">
            <i class="fas fa-clock"></i>
            <span>Mon – Fri: {{ setting('school_timing_weekday', '8:00 AM – 3:00 PM') }}</span>
          </div>
          <div class="ft-ct-item">
            <i class="fas fa-graduation-cap"></i>
            <span>Preparatory to Class X &middot; English Medium</span>
          </div>
        </div>
      </div>

      <div class="ft-bottom">
        <p class="ft-copy">&copy; {{ date('Y') }} {{ $schoolName }}, Khengjang, Churachandpur, Manipur. All rights reserved.</p>
      </div>
    </div>

    {{-- ===== SOCIAL CHANNELS — bottom-most ===== --}}
    <div class="ft-social-bar">
      <span class="ft-social-label">Connect with us</span>
      <div class="ft-social-icons">
        @if(setting('social_facebook'))
        <a href="{{ setting('social_facebook') }}" target="_blank" rel="noopener" class="fsoc fsoc-fb" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
        @endif
        @if(setting('social_instagram'))
        <a href="{{ setting('social_instagram') }}" target="_blank" rel="noopener" class="fsoc fsoc-ig" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        @endif
        <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener" class="fsoc fsoc-wa" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
        @if(setting('social_youtube'))
        <a href="{{ setting('social_youtube') }}" target="_blank" rel="noopener" class="fsoc fsoc-yt" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
        @endif
      </div>
    </div>

    {{-- ===== EHLOM CREDIT — bottom-most ===== --}}
    <div class="ft-credit-bar">
      <a href="https://ehlom.com" target="_blank" rel="noopener" class="ft-credit">
        <img src="{{ asset('images/ehlom.png') }}" alt="Ehlom Digital Logo">
        <div class="ft-credit-label">
          <span>Managed &amp; Designed by <strong>Ehlom Digital</strong></span>
        </div>
      </a>
    </div>
  </footer>

  {{-- ===== FLOATING WHATSAPP ===== --}}
  <div class="wa-float">
    <span class="wa-tooltip">Chat with School</span>
    <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Hello, I would like to know more about ' . $schoolName . '.') }}"
       class="wa-btn" target="_blank" aria-label="Chat on WhatsApp">
      <div class="wa-pulse"></div>
      <i class="fab fa-whatsapp"></i>
    </a>
  </div>

  <script>
    /* --- Navbar scroll state + ticker offset --- */
    const nav      = document.getElementById('nav');
    const tickerEl = document.querySelector('.ticker');

    function getTickerVisible() {
      if (!tickerEl) return 0;
      return Math.max(0, tickerEl.offsetHeight - window.scrollY);
    }
    function setNav() {
      const offset = getTickerVisible();
      nav.style.top = offset + 'px';
      const menu = document.getElementById('mobileNav');
      if (menu.classList.contains('open')) {
        menu.style.top = (offset + nav.offsetHeight) + 'px';
      }
      if (window.scrollY > 56) {
        nav.classList.remove('top'); nav.classList.add('solid');
      } else {
        nav.classList.add('top'); nav.classList.remove('solid');
      }
    }
    window.addEventListener('scroll', setNav, { passive: true });
    setNav();

    /* --- Hamburger menu --- */
    function toggleNav() {
      const ham  = document.getElementById('ham');
      const menu = document.getElementById('mobileNav');
      ham.classList.toggle('open');
      menu.classList.toggle('open');
      menu.style.top = (getTickerVisible() + nav.offsetHeight) + 'px';
      document.body.style.overflow = menu.classList.contains('open') ? 'hidden' : '';
    }
    function closeNav() {
      document.getElementById('ham').classList.remove('open');
      document.getElementById('mobileNav').classList.remove('open');
      document.body.style.overflow = '';
    }

    /* --- Scroll-reveal (with fail-safe so content is never stuck hidden) --- */
    (function () {
      const reveals = document.querySelectorAll('.reveal');
      const showAll = () => reveals.forEach(el => el.classList.add('show'));

      if (!('IntersectionObserver' in window)) { showAll(); return; }

      const revealObs = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          const siblings = Array.from(entry.target.parentElement?.children || []);
          const idx = Math.max(0, siblings.indexOf(entry.target));
          setTimeout(() => entry.target.classList.add('show'), idx * 70);
          revealObs.unobserve(entry.target);
        });
      }, { threshold: 0, rootMargin: '0px 0px -40px 0px' });

      reveals.forEach(el => revealObs.observe(el));

      /* Fail-safe: anything still hidden 1.5s after load gets shown anyway */
      window.addEventListener('load', () => {
        setTimeout(() => reveals.forEach(el => {
          if (!el.classList.contains('show')) el.classList.add('show');
        }), 1500);
      });
    })();

    /* --- Smooth anchor scroll --- */
    document.querySelectorAll('a[href*="#"]').forEach(a => {
      a.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        const hash = href.includes('#') ? '#' + href.split('#')[1] : '';
        if (!hash || hash === '#') return;
        const target = document.querySelector(hash);
        if (!target) return;
        e.preventDefault();
        closeNav();
        const headerH = getTickerVisible() + nav.offsetHeight + 8;
        window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY - headerH, behavior: 'smooth' });
      });
    });
  </script>
  @stack('scripts')
</body>
</html>
