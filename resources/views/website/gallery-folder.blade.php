@extends('layouts.website')
@section('title', $galleryFolder->name)
@section('description', $galleryFolder->description ?: ('Photos from ' . $galleryFolder->name . ' at ' . setting('school_name')))

@section('content')
@php
  use App\Helpers\Settings;
  $lbItems = [];
  foreach ($images as $img) {
      $lbItems[] = ['src' => Settings::storageUrl($img->path), 'cap' => $img->caption ?: $img->title ?: $galleryFolder->name];
  }
@endphp

<section class="page-hero">
  <div class="container">
    <span class="pill"><i class="fas fa-folder-open"></i> Photo Album</span>
    <h1>{{ $galleryFolder->name }}</h1>
    @if($galleryFolder->description)<p>{{ $galleryFolder->description }}</p>@endif
    <div class="crumbs">
      <a href="{{ route('home') }}">Home</a><span>/</span>
      <a href="{{ route('student-life') }}">Student Life</a><span>/</span>
      <span style="color:#dbeafe;opacity:1;">{{ $galleryFolder->name }}</span>
    </div>
  </div>
</section>

<section class="page-section">
  <div class="container">
    @if(count($lbItems))
      <div class="gal-grid">
        @foreach($lbItems as $i => $g)
          <div class="gal-item reveal {{ $i % 7 === 0 ? 'tall' : ($i % 7 === 3 ? 'wide' : '') }}" onclick="openLB({{ $i }})">
            <img src="{{ $g['src'] }}" alt="{{ $g['cap'] }}" loading="lazy">
            <div class="gal-overlay">
              <span class="gal-cap">{{ $g['cap'] }}</span>
              <span class="gal-zoom"><i class="fas fa-expand"></i></span>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <p class="empty-note">This album has no photos yet.</p>
    @endif
    <div style="text-align:center;margin-top:32px;" class="reveal">
      <a href="{{ route('student-life') }}" class="btn btn-blue-solid"><i class="fas fa-arrow-left"></i> Back to Student Life</a>
    </div>
  </div>
</section>

@include('website.partials.lightbox')
@endsection

@push('scripts')
<script>const lbImages = @json($lbItems);</script>
@include('website.partials.lightbox-js')
@endpush
