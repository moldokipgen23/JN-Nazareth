@extends('layouts.website')
@section('title', 'Academics')
@section('description', 'Classes offered at ' . setting('school_name') . ' — a structured English medium journey from Preparatory to Class X.')

@section('content')

@include('website.partials.page-hero', [
  'label'   => '<i class="fas fa-book-open"></i> Academic Programme',
  'heading' => 'Academics & Classes We Offer',
  'sub'     => 'A structured, English medium journey from early childhood through board-level high school education.',
  'crumb'   => 'Academics',
])

{{-- ===== ACADEMICS FEATURE GRID ===== --}}
@php
  $acadFeatures = [
    ['fas fa-door-open',         'Admission',              route('admission')],
    ['fas fa-clipboard-check',   'Admission Requirements', route('admission')],
    ['fas fa-indian-rupee-sign', 'Fee Structure',          route('academics.fee')],
    ['fas fa-calendar-days',     'Academic Calendar',      route('academics.calendar')],
    ['fas fa-clock',             'School Timing',          route('academics.timing')],
    ['fas fa-book-open',         'Curriculum',             route('academics.curriculum')],
    ['fas fa-book',              'Prescribed Textbooks',   route('academics.textbooks')],
    ['fas fa-chart-simple',      'Results',                route('academics.results')],
  ];
@endphp
<section class="page-section">
  <div class="container">
    <div class="hl-header reveal">
      <span class="pill"><i class="fas fa-graduation-cap"></i> {{ setting('acad_feat_label', 'Explore Academics') }}</span>
      <h2 class="sec-title">{{ setting('acad_feat_title', 'Academics at a Glance') }}</h2>
      <p class="sec-sub">{{ setting('acad_feat_sub', 'Everything you need — admissions, fees, timing, curriculum, textbooks and results.') }}</p>
    </div>
    <div class="acad-feat-grid reveal">
      @foreach($acadFeatures as [$fIcon, $fLabel, $fUrl])
      <a href="{{ $fUrl }}" class="acad-feat">
        <span class="acad-feat-ic"><i class="{{ $fIcon }}"></i></span>
        <span class="acad-feat-name">{{ $fLabel }}</span>
        <i class="fas fa-arrow-right acad-feat-go"></i>
      </a>
      @endforeach
    </div>
  </div>
</section>

@php
  $on = fn($k) => setting('sec_show_' . $k, '1') !== '0';

  // Section order from Site Customizer → Academic (drag to reorder).
  $acadDefault = ['acad_highlights', 'acad_classes', 'acad_fee', 'acad_timing'];
  $acadOrder = json_decode(setting('academic_section_order', ''), true);
  if (! is_array($acadOrder) || ! $acadOrder) { $acadOrder = $acadDefault; }
  $acadOrder = array_values(array_intersect($acadOrder, $acadDefault));
  foreach ($acadDefault as $k) { if (! in_array($k, $acadOrder, true)) { $acadOrder[] = $k; } }

  // Collapse to page blocks — fee + timing share one "info" block.
  $acadBlocks = [];
  foreach ($acadOrder as $k) {
      $b = in_array($k, ['acad_fee', 'acad_timing'], true) ? 'info' : $k;
      if (! in_array($b, $acadBlocks, true)) { $acadBlocks[] = $b; }
  }
@endphp

@foreach($acadBlocks as $block)
@switch($block)

{{-- ===== HIGHLIGHTS ===== --}}
@case('acad_highlights')
@if($on('acad_highlights'))
<section id="highlights" class="page-section">
  <div class="container">
    <div class="hl-header reveal">
      <span class="pill">{{ setting('acad_hl_label', 'School Highlights') }}</span>
      <h2 class="sec-title">{{ setting('acad_hl_title', 'What Makes Us Stand Out') }}</h2>
      <p class="sec-sub">{{ setting('acad_hl_sub', 'Delivering quality education with discipline, English excellence, and a nurturing environment for every child.') }}</p>
    </div>
    <div class="hl-grid">
      @php
        $hlD = [
          1 => ['fas fa-school','Preparatory-X','Complete School Journey from Early Years to High School'],
          2 => ['fas fa-chalkboard-user','Qualified','Trained & Experienced Teachers Committed to Every Student'],
          3 => ['fas fa-lightbulb','Smart','Modern & Engaging Learning Environment for Better Understanding'],
          4 => ['fas fa-globe','English','100% English Medium Instruction Across All Classes'],
          5 => ['fas fa-medal','Board ✓','Approved High School Under ' . setting('school_board', 'Manipur Board') . ' of Secondary Education'],
        ];
      @endphp
      @for($i = 1; $i <= 5; $i++)
      <div class="hl-card reveal">
        <div class="hl-icon"><i class="{{ setting("acad_hl_{$i}_icon", $hlD[$i][0]) }}"></i></div>
        <span class="hl-val">{{ setting("acad_hl_{$i}_value", $hlD[$i][1]) }}</span>
        <span class="hl-label">{{ setting("acad_hl_{$i}_label", $hlD[$i][2]) }}</span>
      </div>
      @endfor
    </div>
  </div>
</section>
@endif
@break

{{-- ===== CLASSES ===== --}}
@case('acad_classes')
@if($on('acad_classes'))
<section class="page-section alt">
  <div class="container">
    <div class="cls-header reveal">
      <span class="pill"><i class="fas fa-book-open"></i> {{ setting('acad_cls_label', 'Academic Programme') }}</span>
      <h2 class="sec-title">{{ setting('acad_cls_title', 'Classes We Offer') }}</h2>
      <p class="sec-sub" style="margin:0 auto;">{{ setting('acad_cls_sub', 'A structured, English medium journey from early childhood through board-level high school education.') }}</p>
    </div>
    <div class="cls-grid">
      @php
        $board = setting('school_board', 'Manipur Board');
        $clsD = [
          1 => ['🌱','Preparatory','Early Years','Play-based learning, English familiarisation, and foundational skills in a safe, nurturing, and joyful classroom setting.','Ages 3 – 4'],
          2 => ['🔤','LKG','(KG-1)','Building strong reading, writing, and numeracy foundations through structured English activities, stories, and interactive lessons.','Ages 4 – 5'],
          3 => ['🔤','UKG','(KG-2)','Building strong reading, writing, and numeracy foundations through structured English activities, stories, and interactive lessons.','Ages 5 – 6'],
          4 => ['📘','Primary','Class I – V','A comprehensive curriculum — English, Mathematics, Science, and Social Studies — building a strong academic base with curiosity and confidence.','Ages 6 – 11'],
          5 => ['📗','Middle School','Class VI – VIII','Deepening knowledge across all subjects with structured learning, preparing students for the rigor of high school education.','Ages 11 – 14'],
          6 => ['🏆','High School','Class IX – X','Board-approved Class 9 & 10 under the ' . $board . ' — rigorous preparation for board exams and future academic success.', $board],
        ];
      @endphp
      @for($i = 1; $i <= 6; $i++)
      <div class="cls-card reveal">
        <div class="cls-card-top">
          <div class="cls-emoji" @if($i === 6) style="background:linear-gradient(135deg,#f59e0b,#b45309);box-shadow:0 4px 18px rgba(245,158,11,.42);" @endif>{{ setting("acad_cls_{$i}_emoji", $clsD[$i][0]) }}</div>
          <div class="cls-name">{{ setting("acad_cls_{$i}_name", $clsD[$i][1]) }}</div>
          <div class="cls-range" @if($i === 6) style="color:#b45309;" @endif>{{ setting("acad_cls_{$i}_range", $clsD[$i][2]) }}</div>
        </div>
        <div class="cls-card-body">
          <p class="cls-desc">{{ setting("acad_cls_{$i}_desc", $clsD[$i][3]) }}</p>
          <span class="cls-tag" @if($i === 6) style="background:#fffbeb;color:#92400e;border-color:#fde68a;" @endif>{{ setting("acad_cls_{$i}_tag", $clsD[$i][4]) }}</span>
        </div>
      </div>
      @endfor
    </div>
    <div style="text-align:center;margin-top:38px;" class="reveal">
      <a href="{{ route('admission') }}" class="btn btn-blue-solid"><i class="fas fa-pen-to-square"></i> Apply for Admission</a>
    </div>
  </div>
</section>
@endif
@break

{{-- ===== ACADEMIC INFO (Fee + Timing) ===== --}}
@case('info')
@if($on('acad_fee') || $on('acad_timing'))
<section class="page-section">
  <div class="container">
    <div class="hl-header reveal">
      <span class="pill">Academic Information</span>
      <h2 class="sec-title">Fees &amp; Timing</h2>
    </div>
    <div class="reveal" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px;max-width:760px;margin:0 auto;">
      @if($on('acad_fee'))
      <a href="{{ route('academics.fee') }}" style="text-decoration:none;background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:28px;display:flex;align-items:center;gap:16px;box-shadow:0 4px 20px rgba(0,0,0,.05);">
        <div style="width:52px;height:52px;flex-shrink:0;border-radius:14px;background:#ecfdf5;color:#0f766e;display:flex;align-items:center;justify-content:center;font-size:22px;"><i class="fas fa-receipt"></i></div>
        <div>
          <div style="font-size:16px;font-weight:700;color:#0f172a;">Fee Structure</div>
          <div style="font-size:13px;color:#64748b;">Affordable fee information</div>
        </div>
      </a>
      @endif
      @if($on('acad_timing'))
      <a href="{{ route('academics.timing') }}" style="text-decoration:none;background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:28px;display:flex;align-items:center;gap:16px;box-shadow:0 4px 20px rgba(0,0,0,.05);">
        <div style="width:52px;height:52px;flex-shrink:0;border-radius:14px;background:#eff6ff;color:#1d4ed8;display:flex;align-items:center;justify-content:center;font-size:22px;"><i class="fas fa-clock"></i></div>
        <div>
          <div style="font-size:16px;font-weight:700;color:#0f172a;">School Timing</div>
          <div style="font-size:13px;color:#64748b;">Daily school hours</div>
        </div>
      </a>
      @endif
    </div>
  </div>
</section>
@endif
@break

@endswitch
@endforeach
@endsection
