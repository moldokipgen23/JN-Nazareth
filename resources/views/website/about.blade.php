@extends('layouts.website')
@section('title', 'About Us')
@section('description', 'Learn about ' . setting('school_name') . ' — a legacy of quality English education in Churachandpur, Manipur.')

@section('content')

@include('website.partials.page-hero', [
  'label'   => '<i class="fas fa-school"></i> About Our School',
  'heading' => 'About ' . setting('school_name', 'J.N. Nazareth English School'),
  'sub'     => setting('about_title', 'A Legacy of Quality English Education in Manipur'),
  'crumb'   => 'About Us',
])

@php
  // Section order — set in Site Customizer → About Us (drag to reorder).
  $aboutDefault = ['about_intro', 'about_principal', 'about_administration', 'about_faculty', 'about_smc', 'about_pta', 'about_certs'];
  $aboutOrder = json_decode(setting('about_section_order', ''), true);
  if (! is_array($aboutOrder) || ! $aboutOrder) { $aboutOrder = $aboutDefault; }
  $aboutOrder = array_values(array_intersect($aboutOrder, $aboutDefault));
  foreach ($aboutDefault as $s) { if (! in_array($s, $aboutOrder, true)) { $aboutOrder[] = $s; } }
@endphp

@foreach($aboutOrder as $section)
  @includeIf('website.about.' . str_replace('about_', '', $section))
@endforeach

@endsection
