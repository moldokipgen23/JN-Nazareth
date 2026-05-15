@extends('layouts.public')

@section('content')

{{-- Page Header --}}
<section class="bg-indigo-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold mb-3">Upcoming Events</h1>
        <p class="text-indigo-200 text-lg">Join us — everyone is welcome.</p>
        <div class="w-16 h-1 bg-indigo-400 mx-auto rounded-full mt-4"></div>
    </div>
</section>

{{-- Events List --}}
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    @if($events->count())
        <div class="space-y-6">
            @foreach($events as $event)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                    <div class="flex flex-col sm:flex-row">
                        {{-- Date badge --}}
                        <div class="sm:w-32 bg-indigo-600 text-white flex flex-row sm:flex-col items-center justify-center px-4 py-4 sm:py-6 text-center shrink-0 gap-3 sm:gap-0">
                            <span class="text-2xl sm:text-4xl font-bold leading-none">
                                {{ \Carbon\Carbon::parse($event->starts_at)->format('d') }}
                            </span>
                            <span class="text-sm font-semibold uppercase tracking-wider opacity-80">
                                {{ \Carbon\Carbon::parse($event->starts_at)->format('M Y') }}
                            </span>
                        </div>

                        {{-- Event image (if any) --}}
                        @if($event->image)
                            <img src="{{ \App\Helpers\Settings::storageUrl($event->image) }}" alt="{{ $event->title }}"
                                 class="sm:w-48 h-40 sm:h-auto object-cover">
                        @endif

                        {{-- Details --}}
                        <div class="flex-1 p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $event->title }}</h2>

                            <div class="flex flex-wrap gap-4 text-sm text-gray-500 mb-3">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($event->starts_at)->format('l, F j, Y \a\t g:i A') }}
                                    @if($event->ends_at)
                                        &ndash; {{ \Carbon\Carbon::parse($event->ends_at)->format('g:i A') }}
                                    @endif
                                </span>

                                @if($event->location)
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $event->location }}
                                    </span>
                                @endif
                            </div>

                            @if($event->description)
                                <p class="text-sm text-gray-600 leading-relaxed">{{ $event->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-20 text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-lg font-medium text-gray-400">No upcoming events.</p>
            <p class="text-sm text-gray-400 mt-1">Check back soon for new events.</p>
        </div>
    @endif
</section>

@endsection
