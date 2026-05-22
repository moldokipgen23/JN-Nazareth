@extends('layouts.website')
@section('title', 'Prescribed Textbooks')
@section('description', 'Prescribed textbook and syllabus list for ' . setting('school_name') . '.')

@section('content')

@include('website.partials.page-hero', [
  'label'   => '<i class="fas fa-book"></i> Academics',
  'heading' => 'Prescribed Textbooks',
  'sub'     => 'Download the prescribed textbook and syllabus lists for each class.',
  'crumb'   => 'Prescribed Textbooks',
])

<section class="page-section">
  <div class="container">
    <div class="hl-header reveal">
      <span class="pill"><i class="fas fa-download"></i> Textbooks &amp; Syllabus</span>
      <h2 class="sec-title">Textbook Lists</h2>
    </div>

    @include('website.academics._downloads', [
      'downloads' => $downloads,
      'emptyText' => 'Textbook lists will be published here soon.',
    ])

    <div class="reveal" style="margin-top:26px;text-align:center;">
      <a href="{{ route('academics') }}" style="color:#0f766e;font-weight:600;text-decoration:none;font-size:14px;">
        <i class="fas fa-arrow-left"></i> Back to Academics
      </a>
    </div>
  </div>
</section>
@endsection
