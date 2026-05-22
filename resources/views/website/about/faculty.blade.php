@php
  $facTeachers = \App\Models\Teacher::where('is_active', true)
      ->orderBy('sort_order')->orderBy('name')->get();
@endphp
@if($facTeachers->count() && setting('sec_show_about_faculty', '1') !== '0')
<section class="page-section alt">
  <div class="container">
    <div class="why-header reveal">
      <span class="pill"><i class="fas fa-chalkboard-user"></i> {{ setting('about_faculty_label', 'Our People') }}</span>
      <h2 class="sec-title">{{ setting('about_faculty_title', 'Faculty & Staff') }}</h2>
      @if(trim((string) setting('about_faculty_sub', 'Meet the dedicated teachers who guide and inspire our students every day.')) !== '')
        <p class="sec-sub" style="margin:0 auto;">{{ setting('about_faculty_sub', 'Meet the dedicated teachers who guide and inspire our students every day.') }}</p>
      @endif
    </div>
    <div class="ppl-grid">
      @foreach($facTeachers as $t)
      @php
        $words = preg_split('/\s+/', trim($t->name));
        $ini = strtoupper(mb_substr($words[0], 0, 1) . (count($words) > 1 ? mb_substr(end($words), 0, 1) : ''));
      @endphp
      <div class="ppl-card reveal">
        <div class="ppl-photo">
          @if($t->photo)
            <img src="{{ \App\Helpers\Settings::storageUrl($t->photo) }}" alt="{{ $t->name }}" loading="lazy">
          @else
            <span class="ppl-initials">{{ $ini }}</span>
          @endif
        </div>
        <div class="ppl-name">{{ $t->name }}</div>
        <div class="ppl-role">{{ $t->designation ?: 'Teacher' }}</div>
        @if($t->subjects)<div class="ppl-sub">{{ $t->subjects }}</div>@endif
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif
