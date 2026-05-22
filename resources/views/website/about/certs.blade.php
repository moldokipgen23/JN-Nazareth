@php
  $certs = [];
  foreach (cert_items() as $i => $row) {
    if (empty($row['active'])) { continue; }
    $cf = trim((string) ($row['file'] ?? ''));
    if ($cf === '') { continue; }
    $ext = strtolower(pathinfo($cf, PATHINFO_EXTENSION));
    $certs[] = [
      'title' => trim((string) ($row['title'] ?? '')) ?: 'Certificate ' . ($i + 1),
      'url'   => \App\Helpers\Settings::storageUrl($cf),
      'pdf'   => $ext === 'pdf',
    ];
  }
@endphp
@if(count($certs) && setting('sec_show_about_certs', '1') !== '0')
<section class="page-section alt">
  <div class="container">
    <div class="why-header reveal">
      <span class="pill"><i class="fas fa-certificate"></i> {{ setting('about_certs_label', 'Transparency') }}</span>
      <h2 class="sec-title">{{ setting('about_certs_title', 'School Documents & Certificates') }}</h2>
      <p class="sec-sub" style="margin:0 auto;">{{ setting('about_certs_sub', 'Official certificates and safety records of ' . setting('school_name') . ' — displayed openly for parents and the community.') }}</p>
    </div>
    <div class="cert3d-grid">
      @foreach($certs as $cert)
      <figure class="cert3d-item reveal">
        <div class="cert3d-frame" onclick="openCert('{{ $cert['url'] }}', {{ $cert['pdf'] ? 'true' : 'false' }})"
             tabindex="0" role="button" aria-label="View {{ $cert['title'] }}"
             onkeydown="if(event.key==='Enter'){this.click();}">
          <div class="cert3d-mat">
            <div class="cert3d-win">
              @if($cert['pdf'])
                <iframe src="{{ $cert['url'] }}#toolbar=0&navpanes=0&scrollbar=0&view=FitH" class="cert3d-doc" loading="lazy" title="{{ $cert['title'] }}"></iframe>
              @else
                <img src="{{ $cert['url'] }}" alt="{{ $cert['title'] }}" class="cert3d-doc" loading="lazy">
              @endif
            </div>
          </div>
          <span class="cert3d-zoom"><i class="fas fa-expand"></i> View full</span>
        </div>
        <figcaption class="cert3d-caption">{{ $cert['title'] }}</figcaption>
      </figure>
      @endforeach
    </div>
  </div>
</section>

{{-- Certificate viewer lightbox --}}
<div id="certModal" class="cert-modal" onclick="if(event.target===this)closeCert()">
  <button class="cert-close" onclick="closeCert()" aria-label="Close">&times;</button>
  <div class="cert-modal-body" id="certModalBody"></div>
</div>
<script>
  function openCert(url, isPdf){
    var body = document.getElementById('certModalBody');
    body.innerHTML = isPdf
      ? '<iframe src="' + url + '#toolbar=1" class="cert-frame"></iframe>'
      : '<img src="' + url + '" class="cert-full" alt="Certificate">';
    document.getElementById('certModal').classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeCert(){
    document.getElementById('certModal').classList.remove('open');
    document.getElementById('certModalBody').innerHTML = '';
    document.body.style.overflow = '';
  }
  document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeCert(); });
</script>
@endif
