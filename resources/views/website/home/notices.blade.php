@if($notices->count() && setting('sec_show_notices','1') !== '0')
<section id="notices" style="background:var(--gray-50);">
  <div class="container">
    <div class="why-header reveal">
      <span class="pill"><i class="fas fa-bell"></i> {{ setting('notices_label', 'Notices & Circulars') }}</span>
      <h2 class="sec-title">{{ setting('notices_title', 'Latest Notices & Circulars') }}</h2>
      <p class="sec-sub" style="margin:0 auto;">{{ setting('notices_sub', 'Important notices, circulars and downloadable forms for parents and students.') }}</p>
    </div>
    <div class="adm-list" style="max-width:760px;margin:0 auto;">
      @foreach($notices as $d)
      <a href="{{ \App\Helpers\Settings::storageUrl($d->file_path) }}" target="_blank" rel="noopener" class="adm-item reveal" style="text-decoration:none;">
        <div class="adm-item-ic"><i class="fas fa-file-arrow-down"></i></div>
        <div style="flex:1;">
          <div class="adm-item-title">{{ $d->title }}</div>
          <div class="adm-item-desc">{{ $d->category }}@if($d->file_size) · {{ $d->file_size }}@endif</div>
        </div>
        <i class="fas fa-download" style="color:var(--blue-600);"></i>
      </a>
      @endforeach
    </div>
    <div style="text-align:center;margin-top:32px;" class="reveal">
      <a href="{{ route('news') }}" class="btn btn-blue-solid"><i class="fas fa-bell"></i> View All Notices</a>
    </div>
  </div>
</section>
@endif
