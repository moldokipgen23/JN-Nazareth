{{-- Reusable people-list section. Expects: $pkey, $dLabel, $dTitle, $dSub, $alt (bool) --}}
@php
  $members = [];
  foreach (people_members($pkey) as $row) {
    $nm = trim((string) ($row['name'] ?? ''));
    if ($nm === '') { continue; }
    $ph = trim((string) ($row['photo'] ?? ''));
    $words = preg_split('/\s+/', $nm);
    $initials = strtoupper(mb_substr($words[0], 0, 1) . (count($words) > 1 ? mb_substr(end($words), 0, 1) : ''));
    $members[] = [
      'name'     => $nm,
      'role'     => trim((string) ($row['role'] ?? '')),
      'photo'    => $ph !== '' ? \App\Helpers\Settings::storageUrl($ph) : null,
      'initials' => $initials,
    ];
  }
@endphp
@if(count($members) && setting('sec_show_' . $pkey, '1') !== '0')
<section class="page-section {{ ($alt ?? false) ? 'alt' : '' }}">
  <div class="container">
    <div class="why-header reveal">
      <span class="pill"><i class="fas fa-users"></i> {{ setting($pkey . '_label', $dLabel) }}</span>
      <h2 class="sec-title">{{ setting($pkey . '_title', $dTitle) }}</h2>
      @if(trim((string) setting($pkey . '_sub', $dSub)) !== '')
        <p class="sec-sub" style="margin:0 auto;">{{ setting($pkey . '_sub', $dSub) }}</p>
      @endif
    </div>
    <div class="ppl-grid">
      @foreach($members as $m)
      <div class="ppl-card reveal">
        <div class="ppl-photo">
          @if($m['photo'])
            <img src="{{ $m['photo'] }}" alt="{{ $m['name'] }}" loading="lazy">
          @else
            <span class="ppl-initials">{{ $m['initials'] }}</span>
          @endif
        </div>
        <div class="ppl-name">{{ $m['name'] }}</div>
        @if($m['role'] !== '')<div class="ppl-role">{{ $m['role'] }}</div>@endif
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif
