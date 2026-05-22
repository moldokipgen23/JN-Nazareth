@if(setting('sec_show_about','1') !== '0')
<section id="about">
  <div class="container">
    <div class="about-grid">
      <div class="about-visual reveal">
        <img src="{{ setting('about_emblem') ? \App\Helpers\Settings::storageUrl(setting('about_emblem')) : asset('images/emblem.jpg') }}" alt="School Emblem" class="about-emblem">
        <img src="{{ setting('about_image') ? \App\Helpers\Settings::storageUrl(setting('about_image')) : asset('images/img-awards.png') }}" alt="Students at Award Ceremony" class="about-main-img">
        <div class="about-years-card">
          <span class="about-years-num">{{ setting('about_years_num', '28+') }}</span>
          <span class="about-years-label">{!! nl2br(e(setting('about_years_label', "Years of\nExcellence"))) !!}</span>
        </div>
      </div>

      <div class="reveal">
        <span class="pill"><i class="fas fa-school"></i> {{ setting('about_label', 'About Our School') }}</span>
        <h2 class="sec-title">{{ setting('about_title', 'A Legacy of Quality English Education in Manipur') }}</h2>
        <p class="about-text">{{ setting('about_text_1', $aboutText1D) }}</p>
        <p class="about-text">{{ setting('about_text_2', $aboutText2D) }}</p>

        @for($i = 1; $i <= 3; $i++)
          <div class="about-feat">
            <div class="af-icon"><i class="{{ setting("about_feat_{$i}_icon", $aboutFeatD[$i][0]) }}"></i></div>
            <div>
              <div class="af-title">{{ setting("about_feat_{$i}_title", $aboutFeatD[$i][1]) }}</div>
              <div class="af-desc">{{ setting("about_feat_{$i}_desc", $aboutFeatD[$i][2]) }}</div>
            </div>
          </div>
        @endfor
        <div style="margin-top:8px;"><a href="{{ route('about') }}" class="btn btn-blue-solid"><i class="fas fa-arrow-right"></i> Read More About Us</a></div>
      </div>
    </div>
  </div>
</section>
@endif
