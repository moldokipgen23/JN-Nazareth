@extends('layouts.website')
@section('title', 'Student Life')
@section('description', 'Student life at ' . setting('school_name') . ' — programmes, events, activities and videos.')

@section('content')
@php
  use App\Helpers\Settings;

  $showAlbums = setting('sec_show_sl_albums', '1') !== '0';
  $showVideos = setting('sec_show_sl_videos', '1') !== '0';

  // Section order from Site Customizer → Student Life (drag to reorder).
  $slDefault = ['sl_albums', 'sl_videos'];
  $slOrder = json_decode(setting('studentlife_section_order', ''), true);
  if (! is_array($slOrder) || ! $slOrder) { $slOrder = $slDefault; }
  $slOrder = array_values(array_intersect($slOrder, $slDefault));
  foreach ($slDefault as $k) { if (! in_array($k, $slOrder, true)) { $slOrder[] = $k; } }
@endphp

@include('website.partials.page-hero', [
  'label'   => '<i class="fas fa-children"></i> ' . setting('sl_hero_label', 'Student Life'),
  'heading' => setting('sl_hero_title', 'Life Beyond the Classroom'),
  'sub'     => setting('sl_hero_sub', 'Programmes, events, activities and memorable moments that shape our students every day.'),
  'crumb'   => 'Student Life',
])

@foreach($slOrder as $slBlock)
@switch($slBlock)

{{-- ===== ALBUMS ===== --}}
@case('sl_albums')
@if($showAlbums)
<section class="page-section">
  <div class="container">
    <div class="gal-header reveal">
      <span class="pill"><i class="fas fa-folder-open"></i> {{ setting('sl_albums_label', 'Photo Albums') }}</span>
      <h2 class="sec-title">{{ setting('sl_albums_title', 'Programmes & Activities') }}</h2>
      <p class="sec-sub">{{ setting('sl_albums_sub', 'Photo albums from our school programmes, events and student activities.') }}</p>
    </div>
    @if($folders->count())
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
    @else
      <p class="empty-note">No albums have been added yet. Please check back soon.</p>
    @endif
  </div>
</section>
@endif
@break

{{-- ===== VIDEOS ===== --}}
@case('sl_videos')
@if($showVideos)
<section class="page-section alt">
  <div class="container">
    <div class="gal-header reveal">
      <span class="pill"><i class="fas fa-play"></i> {{ setting('sl_videos_label', 'Videos') }}</span>
      <h2 class="sec-title">{{ setting('sl_videos_title', 'Watch Our School in Action') }}</h2>
      <p class="sec-sub">{{ setting('sl_videos_sub', 'Highlights, performances and moments from school life.') }}</p>
    </div>
    @if($videos->count())
    <div class="why-grid">
      @foreach($videos as $video)
        @if($video->youtube_id)
        <a href="{{ $video->youtube_url }}" target="_blank" rel="noopener" class="why-card reveal" style="display:block;padding:0;overflow:hidden;">
          <div style="position:relative;height:180px;background:url('{{ $video->thumbnail }}') center/cover;">
            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(15,23,42,.32);">
              <span style="width:56px;height:56px;border-radius:50%;background:#dc2626;color:#fff;display:flex;align-items:center;justify-content:center;font-size:20px;box-shadow:0 4px 18px rgba(0,0,0,.35);">
                <i class="fas fa-play" style="margin-left:3px;"></i>
              </span>
            </div>
          </div>
          <div style="padding:20px;">
            <div class="why-title">{{ $video->title }}</div>
            @if($video->description)
              <p class="why-desc">{{ \Illuminate\Support\Str::limit($video->description, 90) }}</p>
            @endif
          </div>
        </a>
        @endif
      @endforeach
    </div>
    @else
      <p class="empty-note">No videos have been added yet. Please check back soon.</p>
    @endif
  </div>
</section>
@endif
@break

@endswitch
@endforeach
@endsection
