@extends('layouts.public')

@section('content')

{{-- Hero image --}}
@if($blog->image)
    <div class="w-full h-72 sm:h-96 overflow-hidden bg-gray-200">
        <img src="{{ \App\Helpers\Settings::storageUrl($blog->image) }}" alt="{{ $blog->title }}"
             class="w-full h-full object-cover">
    </div>
@else
    <div class="w-full h-40 bg-gradient-to-br from-indigo-800 to-indigo-900"></div>
@endif

{{-- Article --}}
<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Back link --}}
    <a href="{{ route('blogs') }}"
       class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:underline mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Blog
    </a>

    <article class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-12">

        {{-- Meta --}}
        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 mb-4">
            <span>{{ \Carbon\Carbon::parse($blog->created_at)->format('F j, Y') }}</span>
            @if($blog->author)
                <span>&middot;</span>
                <span class="font-medium text-gray-700">{{ $blog->author->name }}</span>
            @endif
            @if(!$blog->published)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                    Draft
                </span>
            @endif
        </div>

        {{-- Title --}}
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-8 leading-tight">
            {{ $blog->title }}
        </h1>

        {{-- Content --}}
        <div class="prose prose-indigo prose-lg max-w-none text-gray-700 leading-relaxed">
            {!! $blog->content !!}
        </div>
    </article>

    {{-- Navigation --}}
    <div class="mt-8 text-center">
        <a href="{{ route('blogs') }}"
           class="inline-block bg-indigo-600 text-white font-semibold px-6 py-2.5 rounded-full hover:bg-indigo-700 transition text-sm">
            &larr; All Posts
        </a>
    </div>
</section>

@endsection
