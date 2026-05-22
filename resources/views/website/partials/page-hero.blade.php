{{-- Reusable page banner. Vars: $label, $heading, $sub, $crumb --}}
<section class="page-hero">
  <div class="container">
    @isset($label)<span class="pill">{!! $label !!}</span>@endisset
    <h1>{{ $heading }}</h1>
    @isset($sub)<p>{{ $sub }}</p>@endisset
    <div class="crumbs">
      <a href="{{ route('home') }}">Home</a>
      <span>/</span>
      <span style="color:#dbeafe;opacity:1;">{{ $crumb ?? $heading }}</span>
    </div>
  </div>
</section>
