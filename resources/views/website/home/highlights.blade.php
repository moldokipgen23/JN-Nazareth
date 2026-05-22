@if(setting('sec_show_highlights','1') !== '0')
<section id="highlights">
  <div class="container">
    <div class="hl-header reveal">
      <span class="pill">{{ setting('highlights_label', 'School Highlights') }}</span>
      <h2 class="sec-title">{{ setting('highlights_title', 'What Makes Us Stand Out') }}</h2>
      <p class="sec-sub">{{ setting('highlights_sub', 'Delivering quality education with discipline, English excellence, and a nurturing environment for every child.') }}</p>
    </div>
    <div class="hl-grid">
      @for($i = 1; $i <= 5; $i++)
        <div class="hl-card reveal">
          <div class="hl-icon"><i class="{{ setting("hl_{$i}_icon", $hlD[$i][0]) }}"></i></div>
          <span class="hl-val">{{ setting("hl_{$i}_value", $hlD[$i][1]) }}</span>
          <span class="hl-label">{{ setting("hl_{$i}_label", $hlD[$i][2]) }}</span>
        </div>
      @endfor
    </div>
  </div>
</section>
@endif
