@extends('layouts.public')

@section('content')

{{-- Page Header --}}
<section class="bg-indigo-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold mb-3">Blog</h1>
        <p class="text-indigo-200 text-lg">Reflections, announcements, and stories from our community.</p>
        <div class="w-16 h-1 bg-indigo-400 mx-auto rounded-full mt-4"></div>
    </div>
</section>

{{-- Blog Grid --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    @if($blogs->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($blogs as $blog)
                <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition group">
                    <a href="{{ route('blogs.show', $blog->slug ?? $blog->id) }}" class="block">
                        @if($blog->image)
                            <img src="{{ \App\Helpers\Settings::storageUrl($blog->image) }}" alt="{{ $blog->title }}"
                                 class="w-full h-52 object-cover group-hover:opacity-90 transition">
                        @else
                            <div class="w-full h-52 bg-gradient-to-br from-indigo-100 to-indigo-200 flex items-center justify-center">
                                <svg class="w-14 h-14 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M19 20H5a2 2 0 01-2-2V7a2 2 0 012-2h10l4 4v11a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        @endif
                    </a>
                    <div class="p-6">
                        <p class="text-xs text-gray-400 mb-2">
                            {{ \Carbon\Carbon::parse($blog->created_at)->format('F j, Y') }}
                            @if($blog->author)
                                &middot; {{ $blog->author->name }}
                            @endif
                        </p>
                        <h2 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-indigo-600 transition">
                            <a href="{{ route('blogs.show', $blog->slug ?? $blog->id) }}">{{ $blog->title }}</a>
                        </h2>
                        <p class="text-sm text-gray-600 line-clamp-3">
                            {{ Str::limit(strip_tags($blog->content), 150) }}
                        </p>
                        <a href="{{ route('blogs.show', $blog->slug ?? $blog->id) }}"
                           class="inline-block mt-4 text-sm font-semibold text-indigo-600 hover:underline">
                            Read more &rarr;
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $blogs->links() }}
        </div>
    @else
        <div class="text-center py-20 text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M19 20H5a2 2 0 01-2-2V7a2 2 0 012-2h10l4 4v11a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-lg font-medium text-gray-400">No posts published yet.</p>
            <p class="text-sm text-gray-400 mt-1">Check back soon for new content.</p>
        </div>
    @endif
</section>

@endsection
