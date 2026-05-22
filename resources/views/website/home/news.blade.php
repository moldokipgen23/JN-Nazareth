@if($latestNews->count() && setting('sec_show_news','1') !== '0')
<section id="news" style="background:var(--gray-50);">
  <div class="container">
    <div class="why-header reveal">
      <span class="pill"><i class="fas fa-newspaper"></i> {{ setting('news_label', 'News & Notices') }}</span>
      <h2 class="sec-title">{{ setting('news_title', 'Latest School News') }}</h2>
    </div>
    <div class="why-grid">
      @foreach($latestNews as $news)
      <a href="{{ route('news.show', $news->slug) }}" class="why-card reveal" style="display:block;">
        <div class="why-ic"><i class="fas fa-bullhorn"></i></div>
        <div class="why-title">{{ $news->title }}</div>
        <p class="why-desc">{{ \Illuminate\Support\Str::limit(strip_tags($news->content), 120) }}</p>
        <p style="margin-top:12px;font-size:.78rem;font-weight:700;color:var(--blue-600);">
          {{ optional($news->published_at)->format('d M Y') }} &nbsp;·&nbsp; Read more →
        </p>
      </a>
      @endforeach
    </div>
    <div style="text-align:center;margin-top:32px;" class="reveal">
      <a href="{{ route('news') }}" class="btn btn-blue-solid"><i class="fas fa-newspaper"></i> View All News &amp; Notices</a>
    </div>
  </div>
</section>
@endif
