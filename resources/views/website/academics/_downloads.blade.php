{{-- Shared downloadable-file list. Vars: $downloads, $emptyText --}}
@if($downloads->count())
  <div class="adm-list" style="max-width:760px;margin:0 auto;">
    @foreach($downloads as $d)
    <a href="{{ \App\Helpers\Settings::storageUrl($d->file_path) }}" target="_blank" class="adm-item reveal" style="text-decoration:none;">
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
  <p class="empty-note">{{ $emptyText ?? 'No files available at the moment.' }}</p>
@endif
