@extends('layouts.website')
@section('title', setting('acad_calendar_title', 'Academic Calendar'))
@section('description', 'Academic calendar for ' . setting('school_name') . '.')

@section('content')

@include('website.partials.page-hero', [
  'label'   => '<i class="fas fa-calendar-days"></i> Academics',
  'heading' => setting('acad_calendar_title', 'Academic Calendar'),
  'sub'     => setting('acad_calendar_sub', 'Important dates, holidays and events for the school year.'),
  'crumb'   => 'Academic Calendar',
])

@php $calImages = acad_calendar_images(); @endphp

<section class="page-section">
  <div class="container">
    @if(count($calImages))
      <div class="cal-grid reveal">
        @foreach($calImages as $img)
          @php $u = \App\Helpers\Settings::storageUrl($img['file']); @endphp
          <figure class="cal-item">
            <div class="cal-frame" onclick="openCalImg('{{ $u }}')" tabindex="0" role="button"
                 aria-label="View calendar" onkeydown="if(event.key==='Enter'){this.click();}">
              <img src="{{ $u }}" alt="{{ $img['caption'] ?? 'Academic calendar' }}" loading="lazy">
              <span class="cal-zoom"><i class="fas fa-expand"></i> View full</span>
            </div>
            @if(!empty($img['caption']))
              <figcaption class="cal-caption">{{ $img['caption'] }}</figcaption>
            @endif
          </figure>
        @endforeach
      </div>
    @else
      <p class="sec-sub" style="text-align:center;">The academic calendar will be published here soon.</p>
    @endif

    <div class="reveal" style="margin-top:26px;text-align:center;">
      <a href="{{ route('academics') }}" style="color:#0f766e;font-weight:600;text-decoration:none;font-size:14px;">
        <i class="fas fa-arrow-left"></i> Back to Academics
      </a>
    </div>
  </div>
</section>

<div id="calModal" class="cert-modal" onclick="if(event.target===this)closeCalImg()">
  <button class="cert-close" onclick="closeCalImg()" aria-label="Close">&times;</button>
  <div class="cert-modal-body" id="calModalBody"></div>
</div>
<script>
  function openCalImg(url){
    document.getElementById('calModalBody').innerHTML = '<img src="' + url + '" class="cert-full" alt="Calendar">';
    document.getElementById('calModal').classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeCalImg(){
    document.getElementById('calModal').classList.remove('open');
    document.getElementById('calModalBody').innerHTML = '';
    document.body.style.overflow = '';
  }
  document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeCalImg(); });
</script>
@endsection
