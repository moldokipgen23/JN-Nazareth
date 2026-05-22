@extends('layouts.website')
@section('title', 'News & Notices')
@section('description', 'Latest news, notices, circulars and downloads from ' . setting('school_name') . '.')

@section('content')
@php use App\Helpers\Settings; @endphp

@include('website.partials.page-hero', [
  'label'   => '<i class="fas fa-newspaper"></i> News &amp; Notices',
  'heading' => 'School News & Notices',
  'sub'     => 'Stay updated with the latest happenings, announcements and important notices from our school.',
  'crumb'   => 'News & Notices',
])

{{-- ===== NEWS POSTS ===== --}}
<section class="page-section">
  <div class="container">
    <div class="why-header reveal">
      <span class="pill"><i class="fas fa-bullhorn"></i> Latest Updates</span>
      <h2 class="sec-title">News & Announcements</h2>
    </div>
    @if($news->count())
      <div class="why-grid">
        @foreach($news as $post)
        <a href="{{ route('news.show', $post->slug) }}" class="why-card reveal" style="display:block;padding:0;overflow:hidden;">
          @if($post->image)
            <div style="height:170px;background:url('{{ Settings::storageUrl($post->image) }}') center/cover;"></div>
          @else
            <div style="height:170px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--blue-600),var(--navy));color:#fff;font-size:2rem;"><i class="fas fa-bullhorn"></i></div>
          @endif
          <div style="padding:22px;">
            <span class="cls-tag" style="text-transform:capitalize;">{{ $post->post_type ?: 'news' }}</span>
            <div class="why-title" style="margin-top:10px;">{{ $post->title }}</div>
            <p class="why-desc">{{ \Illuminate\Support\Str::limit(strip_tags($post->content), 110) }}</p>
            <p style="margin-top:12px;font-size:.78rem;font-weight:700;color:var(--blue-600);">
              {{ optional($post->published_at)->format('d M Y') }} &nbsp;·&nbsp; Read more →
            </p>
          </div>
        </a>
        @endforeach
      </div>
      <div style="margin-top:32px;">{{ $news->links() }}</div>
    @else
      <p class="empty-note">No news posts have been published yet.</p>
    @endif
  </div>
</section>

{{-- ===== DOWNLOADS ===== --}}
<section class="page-section alt">
  <div class="container">
    <div class="why-header reveal">
      <span class="pill"><i class="fas fa-download"></i> Downloads</span>
      <h2 class="sec-title">Notices, Circulars & Forms</h2>
    </div>
    @if($downloads->count())
      <div class="adm-list" style="max-width:760px;margin:0 auto;">
        @foreach($downloads as $d)
        <a href="{{ Settings::storageUrl($d->file_path) }}" target="_blank" class="adm-item reveal" style="text-decoration:none;">
          <div class="adm-item-ic"><i class="fas fa-file-arrow-down"></i></div>
          <div style="flex:1;">
            <div class="adm-item-title">{{ $d->title }}</div>
            <div class="adm-item-desc">{{ $d->category }}@if($d->file_size) · {{ $d->file_size }}@endif</div>
          </div>
          <i class="fas fa-download" style="color:var(--blue-600);"></i>
        </a>
        @endforeach
      </div>
    @else
      <p class="empty-note">No downloads available at the moment.</p>
    @endif
  </div>
</section>
@endsection
