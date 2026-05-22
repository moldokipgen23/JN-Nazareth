@extends('layouts.website')
@section('title', 'Fee Structure')
@section('description', 'Fee structure information for ' . setting('school_name') . '.')

@section('content')

@include('website.partials.page-hero', [
  'label'   => '<i class="fas fa-receipt"></i> Academics',
  'heading' => 'Fee Structure',
  'sub'     => 'Quality education at an affordable cost.',
  'crumb'   => 'Fee Structure',
])

@php
  $feeRows = [];
  for ($i = 1; $i <= 13; $i++) {
    $cn        = trim((string) setting("acad_fee_{$i}_class"));
    $admission = trim((string) setting("acad_fee_{$i}_admission"));
    $tuition   = trim((string) setting("acad_fee_{$i}_tuition"));
    if ($cn !== '' && ($admission !== '' || $tuition !== '')) {
      $feeRows[] = ['class' => $cn, 'admission' => $admission, 'tuition' => $tuition];
    }
  }
@endphp

<section class="page-section">
  <div class="container">
    <div class="hl-header reveal">
      <span class="pill">{{ setting('acad_fee_title', 'Fee Information') }}</span>
      <h2 class="sec-title">Transparent &amp; Affordable Fees</h2>
      <p class="sec-sub">Tap any class card to flip it and view the one-time admission fee and the monthly tuition fee.</p>
    </div>

    @if(count($feeRows))
    <div class="fee-grid reveal">
      @foreach($feeRows as $row)
      <div class="fee-card" onclick="this.classList.toggle('flipped')" role="button" tabindex="0"
           onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.classList.toggle('flipped');}">
        <div class="fee-card-inner">

          {{-- Front --}}
          <div class="fee-face fee-front">
            <div class="fee-emoji">{{ mb_strtoupper(mb_substr($row['class'], 0, 1)) }}</div>
            <div class="fee-class">{{ $row['class'] }}</div>
            <span class="fee-hint"><i class="fas fa-rotate"></i> Tap to view fees</span>
          </div>

          {{-- Back --}}
          <div class="fee-face fee-back">
            <div class="fee-back-class">{{ $row['class'] }}</div>
            @if($row['admission'] !== '')
            <div class="fee-line">
              <span class="fee-line-label"><i class="fas fa-id-card"></i> Admission (one-time)</span>
              <span class="fee-line-val">{{ $row['admission'] }}</span>
            </div>
            @endif
            @if($row['tuition'] !== '')
            <div class="fee-line">
              <span class="fee-line-label"><i class="fas fa-calendar-day"></i> Monthly Tuition</span>
              <span class="fee-line-val">{{ $row['tuition'] }}</span>
            </div>
            @endif
            <span class="fee-hint"><i class="fas fa-rotate-left"></i> Tap to flip back</span>
          </div>

        </div>
      </div>
      @endforeach
    </div>
    @endif

    @php $feeNote = trim((string) setting('acad_fee_text', 'We offer quality education at an affordable cost. Contact us on WhatsApp or visit the school for detailed fee information.')); @endphp
    @if($feeNote !== '')
    <div class="reveal" style="max-width:760px;margin:28px auto 0;background:#f0fdfa;border:1px solid #99f6e4;border-radius:14px;padding:18px 22px;">
      <p style="color:#0f766e;line-height:1.7;font-size:14.5px;margin:0;white-space:pre-line;">
        <i class="fas fa-circle-info"></i> {{ $feeNote }}
      </p>
    </div>
    @endif

    <div class="reveal" style="text-align:center;margin-top:26px;">
      <a href="https://wa.me/{{ setting('whatsapp', '919862880292') }}?text={{ rawurlencode('Hello, I would like to know the fee structure at ' . setting('school_name') . '.') }}"
         target="_blank" class="btn btn-blue-solid">
        <i class="fab fa-whatsapp"></i> Ask Fee Details on WhatsApp
      </a>
    </div>

    <div class="reveal" style="margin-top:18px;text-align:center;">
      <a href="{{ route('academics') }}" style="color:#0f766e;font-weight:600;text-decoration:none;font-size:14px;">
        <i class="fas fa-arrow-left"></i> Back to Academics
      </a>
    </div>
  </div>
</section>
@endsection
