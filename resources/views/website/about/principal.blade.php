@if(setting('sec_show_about_principal', '1') !== '0')
@php $estd = setting('school_established', '1996'); $sName = setting('school_name'); @endphp
<section class="page-section">
  <div class="container">
    <div class="pr-grid">
      <div class="pr-img-col reveal">
        <div class="pr-frame">
          <img src="{{ setting('principal_photo') ? \App\Helpers\Settings::storageUrl(setting('principal_photo')) : asset('images/principal.png') }}" alt="Principal {{ setting('principal_name') }}" class="pr-img">
          <div class="pr-badge">
            <strong>{{ setting('principal_name', 'Ngamboi Kipgen') }}</strong>
            <span>Principal</span>
          </div>
        </div>
      </div>
      <div class="reveal">
        <span class="pill"><i class="fas fa-quote-left"></i> {{ setting('principal_label', "Principal's Message") }}</span>
        <div class="pr-qmark">"</div>
        <p class="pr-quote">{{ setting('principal_quote', 'Education is not the filling of a pail, but the lighting of a fire. At ' . $sName . ', we believe every child carries within them immense potential — and our role is to kindle that spark.') }}</p>
        @php $pm = setting('principal_message'); @endphp
        <p class="pr-text">{!! $pm ? nl2br(e($pm)) : 'Since our establishment in ' . e($estd) . ', our school has stood as a beacon of quality education in the Khengjang community of Churachandpur, Manipur.' !!}</p>
        <p class="pr-sig">— {{ setting('principal_name', 'Ngamboi Kipgen') }}, Principal</p>
      </div>
    </div>
  </div>
</section>
@endif
