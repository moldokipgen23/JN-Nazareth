@extends('layouts.website')
@section('title', 'Results')
@section('description', 'Examination results and notices for ' . setting('school_name') . '.')

@section('content')

@include('website.partials.page-hero', [
  'label'   => '<i class="fas fa-chart-simple"></i> Academics',
  'heading' => 'Results',
  'sub'     => 'Download examination results and result notices.',
  'crumb'   => 'Results',
])

<section class="page-section">
  <div class="container">
    <div class="hl-header reveal">
      <span class="pill"><i class="fas fa-download"></i> Examination Results</span>
      <h2 class="sec-title">Result Downloads</h2>
    </div>

    @include('website.academics._downloads', [
      'downloads' => $downloads,
      'emptyText' => 'Results will be published here soon.',
    ])

    <div class="reveal" style="margin-top:26px;text-align:center;">
      <a href="{{ route('academics') }}" style="color:#0f766e;font-weight:600;text-decoration:none;font-size:14px;">
        <i class="fas fa-arrow-left"></i> Back to Academics
      </a>
    </div>
  </div>
</section>
@endsection
