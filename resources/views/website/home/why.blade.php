@if(setting('sec_show_why','1') !== '0')
<section id="why">
  <div class="container">
    <div class="why-header reveal">
      <span class="pill"><i class="fas fa-award"></i> {{ setting('why_label', 'Why Choose Us') }}</span>
      <h2 class="sec-title">{{ setting('why_title', 'The ' . $sName . ' Advantage') }}</h2>
      <p class="sec-sub" style="margin:0 auto;">{{ setting('why_sub', 'We combine academic rigour with holistic development, preparing every student for a bright and confident future.') }}</p>
    </div>
    <div class="why-grid">
      @for($i = 1; $i <= 6; $i++)
        <div class="why-card reveal">
          <div class="why-ic"><i class="{{ setting("why_{$i}_icon", $whyD[$i][0]) }}"></i></div>
          <div class="why-title">{{ setting("why_{$i}_title", $whyD[$i][1]) }}</div>
          <p class="why-desc">{{ setting("why_{$i}_desc", $whyD[$i][2]) }}</p>
        </div>
      @endfor
    </div>
  </div>
</section>
@endif
