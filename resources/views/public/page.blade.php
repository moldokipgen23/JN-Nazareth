@extends('layouts.public')
@section('title', $page->title)

@section('content')

{{-- Page Header --}}
<section class="relative overflow-hidden text-white" style="min-height:200px;">
    <div class="absolute inset-0 brand-gradient"></div>
    <div class="absolute inset-0 opacity-10 pointer-events-none">
        <svg class="w-full h-full" viewBox="0 0 80 40" preserveAspectRatio="xMidYMid slice">
            <defs><pattern id="pg-dots" width="8" height="8" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1.5" fill="white"/></pattern></defs>
            <rect width="80" height="40" fill="url(#pg-dots)" />
        </svg>
    </div>
    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-14 text-center">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight drop-shadow">{{ $page->title }}</h1>
    </div>
</section>

{{-- Page Content --}}
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($page->content)
            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                {!! $page->content !!}
            </div>
        @else
            <p class="text-center text-gray-400 py-10">This page has no content yet.</p>
        @endif
    </div>
</section>

@endsection
