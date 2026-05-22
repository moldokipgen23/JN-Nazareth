@extends('layouts.website')
@section('title', $post->title)
@section('description', \Illuminate\Support\Str::limit(strip_tags($post->content), 150))

@section('content')
@php use App\Helpers\Settings; @endphp

<section class="page-hero">
  <div class="container">
    <span class="pill" style="text-transform:capitalize;"><i class="fas fa-bullhorn"></i> {{ $post->post_type ?: 'News' }}</span>
    <h1>{{ $post->title }}</h1>
    <p>{{ optional($post->published_at)->format('l, d F Y') }}</p>
    <div class="crumbs">
      <a href="{{ route('home') }}">Home</a><span>/</span>
      <a href="{{ route('news') }}">News</a><span>/</span>
      <span style="color:#dbeafe;opacity:1;">{{ \Illuminate\Support\Str::limit($post->title, 40) }}</span>
    </div>
  </div>
</section>

<section class="page-section">
  <div class="container" style="max-width:820px;">
    @if($post->image)
      <img src="{{ Settings::storageUrl($post->image) }}" alt="{{ $post->title }}" style="width:100%;border-radius:16px;margin-bottom:32px;">
    @endif
    <div class="about-text" style="font-size:1.02rem;line-height:1.85;">
      {!! $post->content !!}
    </div>
    <div style="margin-top:40px;">
      <a href="{{ route('news') }}" class="btn btn-blue-solid"><i class="fas fa-arrow-left"></i> Back to News</a>
    </div>
  </div>
</section>

@if($related->count())
<section class="page-section alt">
  <div class="container">
    <div class="why-header reveal">
      <span class="pill"><i class="fas fa-newspaper"></i> More News</span>
      <h2 class="sec-title">Related Posts</h2>
    </div>
    <div class="why-grid">
      @foreach($related as $r)
      <a href="{{ route('news.show', $r->slug) }}" class="why-card reveal" style="display:block;">
        <div class="why-ic"><i class="fas fa-bullhorn"></i></div>
        <div class="why-title">{{ $r->title }}</div>
        <p class="why-desc">{{ \Illuminate\Support\Str::limit(strip_tags($r->content), 100) }}</p>
        <p style="margin-top:12px;font-size:.78rem;font-weight:700;color:var(--blue-600);">
          {{ optional($r->published_at)->format('d M Y') }} &nbsp;·&nbsp; Read more →
        </p>
      </a>
      @endforeach
    </div>
  </div>
</section>
@endif
@endsection
