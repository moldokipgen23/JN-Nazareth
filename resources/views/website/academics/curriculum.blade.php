@extends('layouts.website')
@section('title', setting('acad_curriculum_title', 'Curriculum'))
@section('description', 'Curriculum at ' . setting('school_name') . '.')

@section('content')

@include('website.partials.page-hero', [
  'label'   => '<i class="fas fa-book-open"></i> Academics',
  'heading' => setting('acad_curriculum_title', 'Our Curriculum'),
  'sub'     => setting('acad_curriculum_sub', 'A well-rounded curriculum designed to build knowledge, skills and character.'),
  'crumb'   => 'Curriculum',
])

@php $currItems = acad_curriculum_items(); @endphp

<section class="page-section">
  <div class="container">
    @if(count($currItems))
      <div class="curr-grid reveal">
        @foreach($currItems as $c)
          <div class="curr-card">
            <div class="curr-ic"><i class="{{ $c['icon'] ?? 'fas fa-book' }}"></i></div>
            <div class="curr-title">{{ $c['title'] ?? '' }}</div>
            @if(!empty($c['desc']))
              <p class="curr-desc">{{ $c['desc'] }}</p>
            @endif
          </div>
        @endforeach
      </div>
    @else
      <p class="sec-sub" style="text-align:center;">Curriculum details will be published here soon.</p>
    @endif

    <div class="reveal" style="margin-top:26px;text-align:center;">
      <a href="{{ route('academics') }}" style="color:#0f766e;font-weight:600;text-decoration:none;font-size:14px;">
        <i class="fas fa-arrow-left"></i> Back to Academics
      </a>
    </div>
  </div>
</section>
@endsection
