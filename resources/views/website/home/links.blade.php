@if($importantLinks->count() && setting('sec_show_links','1') !== '0')
<section id="links">
  <div class="container">
    <div class="why-header reveal">
      <span class="pill"><i class="fas fa-link"></i> {{ setting('links_label', 'Important Links') }}</span>
      <h2 class="sec-title">{{ setting('links_title', 'Quick & Useful Links') }}</h2>
      <p class="sec-sub" style="margin:0 auto;">{{ setting('links_sub', 'Helpful resources and official links for parents, students and visitors.') }}</p>
    </div>
    <div class="why-grid">
      @foreach($importantLinks as $link)
      <a href="{{ $link->url }}" target="_blank" rel="noopener" class="why-card reveal" style="display:block;">
        <div class="why-ic"><i class="fas fa-up-right-from-square"></i></div>
        <div class="why-title">{{ $link->title }}</div>
        @if($link->description)
          <p class="why-desc">{{ $link->description }}</p>
        @endif
        <p style="margin-top:12px;font-size:.78rem;font-weight:700;color:var(--blue-600);">Open link →</p>
      </a>
      @endforeach
    </div>
  </div>
</section>
@endif
