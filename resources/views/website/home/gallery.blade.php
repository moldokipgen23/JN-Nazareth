@if(setting('sec_show_gallery','1') !== '0')
<section id="gallery">
  <div class="container">
    <div class="gal-header reveal">
      <span class="pill"><i class="fas fa-images"></i> {{ setting('gallery_label', 'School Gallery') }}</span>
      <h2 class="sec-title">{{ setting('gallery_title', 'Life at ' . $sName) }}</h2>
      <p class="sec-sub" style="margin:0 auto;">{{ setting('gallery_sub', 'A glimpse into our vibrant school life — events, sports, students, and campus moments captured through the years.') }}</p>
    </div>
    <div class="gal-grid">
      @foreach($galItems as $i => $g)
        <div class="gal-item reveal {{ $i === 0 ? 'tall' : ($i === 3 ? 'wide' : '') }}" onclick="openLB({{ $i }})">
          <img src="{{ $g['src'] }}" alt="{{ $g['cap'] }}" loading="lazy">
          <div class="gal-overlay">
            <span class="gal-cap">{{ $g['cap'] }}</span>
            <span class="gal-zoom"><i class="fas fa-expand"></i></span>
          </div>
        </div>
      @endforeach
    </div>
    <div style="text-align:center;margin-top:32px;" class="reveal">
      <a href="{{ route('student-life') }}" class="btn btn-blue-solid"><i class="fas fa-images"></i> View Full Gallery</a>
    </div>
  </div>
</section>
@endif
