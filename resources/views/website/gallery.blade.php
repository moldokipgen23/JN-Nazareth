@extends('layouts.website')
@section('title', 'Gallery')
@section('description', 'Photo gallery of ' . setting('school_name') . ' — events, sports, students and campus life.')

@section('content')
@php
  use App\Helpers\Settings;
  $lbItems = [];
  foreach ($images as $img) {
      $lbItems[] = ['src' => Settings::storageUrl($img->path), 'cap' => $img->caption ?: $img->title ?: 'School Gallery'];
  }
@endphp

@include('website.partials.page-hero', [
  'label'   => '<i class="fas fa-images"></i> School Gallery',
  'heading' => 'Life at ' . setting('school_name'),
  'sub'     => 'A glimpse into our vibrant school life — events, sports, students, and campus moments captured through the years.',
  'crumb'   => 'Gallery',
])

{{-- ===== ALBUMS ===== --}}
@if($folders->count())
<section class="page-section">
  <div class="container">
    <div class="gal-header reveal">
      <span class="pill"><i class="fas fa-folder-open"></i> Photo Albums</span>
      <h2 class="sec-title">Browse by Album</h2>
    </div>
    <div class="why-grid">
      @foreach($folders as $folder)
        @php
          $cover = $folder->cover_image
            ? Settings::storageUrl($folder->cover_image)
            : ($folder->previewImages->first() ? Settings::storageUrl($folder->previewImages->first()->path) : asset('images/img-campus.png'));
        @endphp
        <a href="{{ route('gallery.folder', $folder) }}" class="why-card reveal" style="display:block;padding:0;overflow:hidden;">
          <div style="height:180px;background:url('{{ $cover }}') center/cover;"></div>
          <div style="padding:20px;">
            <div class="why-title">{{ $folder->name }}</div>
            <p class="why-desc">{{ $folder->items_count }} {{ \Illuminate\Support\Str::plural('photo', $folder->items_count) }}</p>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- ===== ALL PHOTOS ===== --}}
<section class="page-section alt">
  <div class="container">
    <div class="gal-header reveal">
      <span class="pill"><i class="fas fa-camera"></i> All Photos</span>
      <h2 class="sec-title">Recent Moments</h2>
    </div>
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
      <p class="empty-note">No photos have been added yet. Please check back soon.</p>
    @endif
  </div>
</section>

@include('website.partials.lightbox')
@endsection

@push('scripts')
<script>const lbImages = @json($lbItems);</script>
@include('website.partials.lightbox-js')
@endpush
