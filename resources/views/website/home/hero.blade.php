@if(setting('sec_show_hero','1') !== '0')
<section id="hero">
  <div class="hero-bg" @if(setting('hero_image')) style="background-image:url('{{ \App\Helpers\Settings::storageUrl(setting('hero_image')) }}')" @endif></div>
  <div class="hero-overlay"></div>

  <div class="hero-body">
    <div class="hero-inner">
      <img src="{{ $heroLogo }}" alt="{{ $sName }} Logo" class="hero-logo">
      <div class="hero-tag">
        <span class="dot"></span>
        {{ setting('hero_badge_text', 'Govt. Recognised · Est. ' . $estd . ' · ' . $board . ' Approved') }}
        <span class="dot"></span>
      </div>
      <h1 class="hero-title">{{ $sName }}</h1>
      <p class="hero-tagline">"{{ setting('hero_tagline', setting('school_tagline', 'Quality Education for a Better Future')) }}"</p>
      <p class="hero-meta">{{ setting('address_short', 'Khengjang · Churachandpur · Manipur') }} &middot; Preparatory to Class X</p>
      <div class="hero-btns">
        <a href="#admission" class="btn btn-primary"><i class="fas fa-pen-to-square"></i> {{ setting('hero_btn1_text', 'Admission Open') }}</a>
        <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Hello, I would like to inquire about ' . $sName . '.') }}" target="_blank" class="btn btn-ghost">
          <i class="fab fa-whatsapp"></i> {{ setting('hero_btn2_text', 'Contact School') }}
        </a>
      </div>
    </div>
  </div>

  <div class="hero-stats">
    <div class="hero-stats-row container">
      @for($i = 1; $i <= 5; $i++)
        <div class="hs-item">
          <span class="hs-val">{{ setting("hero_stat_{$i}_value", $heroStatD[$i][0]) }}</span>
          <div class="hs-label">{{ setting("hero_stat_{$i}_label", $heroStatD[$i][1]) }}</div>
        </div>
      @endfor
    </div>
  </div>
</section>
@endif
