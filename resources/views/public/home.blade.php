@extends('layouts.public')

@section('content')

@push('styles')
<style>
/* ── Mobile responsive global fixes ── */
@media(max-width:768px){
    .hero-text-in { animation-duration:.6s; }
    [style*="grid-template-columns:1fr 1fr"],[style*="grid-template-columns:repeat(2"]{
        grid-template-columns:1fr!important;
    }
    [style*="grid-template-columns:1fr auto"]{
        grid-template-columns:1fr!important;
    }
    [style*="padding:88px 0"],[style*="padding:80px 0"],[style*="padding:72px 0"]{
        padding:48px 0!important;
    }
    [style*="padding:56px 0"],[style*="padding:60px 0"]{
        padding:36px 0!important;
    }
    [style*="font-size:clamp(2rem"],[style*="font-size:clamp(1.8rem"]{
        font-size:1.5rem!important;
    }
}
@media(max-width:480px){
    [style*="display:grid; grid-template-columns:1fr auto"]{
        display:flex!important; flex-direction:column!important; align-items:flex-start!important;
    }
}
</style>
@endpush

@php
    $defaultOrder = ['hero','stats','story-video','about','story','programs','map','gallery','events','blog','videos','location','hall-of-fame','cta'];
    $savedOrder   = json_decode(\App\Helpers\Settings::get('home_section_order',''),true) ?: $defaultOrder;
    // Ensure hero is always first
    $savedOrder = array_unique(array_merge(['hero'], $savedOrder));
@endphp

@foreach($savedOrder as $_sec)
    @includeIf('public.partials.home.' . $_sec)
@endforeach

@endsection
