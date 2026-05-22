@extends('layouts.website')
@section('title', 'Contact Us')
@section('description', 'Get in touch with ' . setting('school_name') . ' — address, phone, WhatsApp and location.')

@section('content')
@php $wa = preg_replace('/\D+/', '', setting('whatsapp', '919862880292')); @endphp

@include('website.partials.page-hero', [
  'label'   => '<i class="fas fa-map-location-dot"></i> Find Us',
  'heading' => 'Location & Contact',
  'sub'     => 'Located in Khengjang, Churachandpur — at the heart of the community and easily accessible.',
  'crumb'   => 'Contact',
])

<section class="page-section">
  <div class="container">
    <div class="loc-grid">
      <div class="map-box reveal">
        <iframe src="{{ setting('map_embed_url') ?: 'https://maps.google.com/maps?q=24.388994,93.700615&output=embed&z=16&hl=en' }}"
                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                title="{{ setting('school_name') }} Location"></iframe>
      </div>
      <div class="reveal">
        <div class="loc-card">
          <div class="loc-card-title"><i class="fas fa-map-marker-alt"></i> School Address</div>
          <p class="loc-addr"><strong>{{ setting('school_name') }}</strong><br>{{ setting('contact_address', 'Khengjang, B.P.O. Koite, Churachandpur – 795128, Manipur, India') }}</p>
          <a href="{{ setting('map_directions_url') ?: 'https://maps.app.goo.gl/DfLpg7QV7DaXM37r9' }}" target="_blank" class="loc-dir">
            <i class="fas fa-directions"></i> Get Directions on Google Maps
          </a>
        </div>
        <div class="loc-card">
          <div class="loc-card-title"><i class="fas fa-address-book"></i> Contact Information</div>
          <div class="ct-item">
            <div class="ct-ic"><i class="fab fa-whatsapp"></i></div>
            <div><div class="ct-label">WhatsApp / Phone</div><div class="ct-val"><a href="https://wa.me/{{ $wa }}" target="_blank" style="color:inherit;">{{ setting('contact_phone', '+91 98628 80292') }}</a></div></div>
          </div>
          @if(setting('contact_email'))
          <div class="ct-item">
            <div class="ct-ic"><i class="fas fa-envelope"></i></div>
            <div><div class="ct-label">Email</div><div class="ct-val"><a href="mailto:{{ setting('contact_email') }}" style="color:inherit;">{{ setting('contact_email') }}</a></div></div>
          </div>
          @endif
          <div class="ct-item">
            <div class="ct-ic"><i class="fas fa-user-tie"></i></div>
            <div><div class="ct-label">Principal</div><div class="ct-val">{{ setting('principal_name', 'Ngamboi Kipgen') }}</div></div>
          </div>
          <div class="ct-item">
            <div class="ct-ic"><i class="fas fa-clock"></i></div>
            <div><div class="ct-label">School Hours</div><div class="ct-val">Mon – Fri: {{ setting('school_timing_weekday', '8 AM – 3 PM') }} · Sat: {{ setting('school_timing_saturday', '8 AM – 12 PM') }}</div></div>
          </div>
        </div>
        <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Hello, I want to know more about ' . setting('school_name') . '.') }}" target="_blank" class="btn btn-wa" style="width:100%;justify-content:center;border-radius:12px;padding:14px 20px;">
          <i class="fab fa-whatsapp"></i> Chat with Us on WhatsApp
        </a>
      </div>
    </div>
  </div>
</section>
@endsection
