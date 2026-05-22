@if(setting('sec_show_about_intro', '1') !== '0')
@php
  $sName = setting('school_name', 'J.N. Nazareth English School');
  $estd  = setting('school_established', '1996');
  $board = setting('school_board', 'Manipur Board');
  $aboutText1D = $sName . ', established in ' . $estd . ' at Khengjang, Churachandpur, stands as one of the most trusted and reputed English medium schools in the region.';
  $aboutText2D = 'Our school offers a complete educational journey from Preparatory through Class 10, guided by qualified teachers, a disciplined environment, and a strong focus on English communication, academic integrity, and character development.';
  $aboutFeatD = [
    1 => ['fas fa-graduation-cap', $board . ' Approved High School', 'Recently approved for Class 10 under the Board of Secondary Education, Manipur — a landmark achievement recognising our commitment to quality education.'],
    2 => ['fas fa-language', '100% English Medium — Preparatory to Class X', 'All subjects are taught in English, building strong language skills, communication confidence, and academic readiness from the very first year.'],
    3 => ['fas fa-shield-halved', 'Discipline, Values & Character Formation', 'We cultivate not just academic excellence but responsible, ethical, and well-rounded individuals prepared for life and for a better Manipur.'],
  ];
@endphp
<section class="page-section">
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
      </div>
    </div>
  </div>
</section>
@endif
