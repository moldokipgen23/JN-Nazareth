{{-- ══ MAP SECTION ══ --}}
@if(\App\Helpers\Settings::get('sec_show_map', '1'))
@php
    $secTitleMap = \App\Helpers\Settings::get('sec_title_map', 'Find Us');
    $mapSubtitle = \App\Helpers\Settings::get('map_section_subtitle', 'We look forward to welcoming you. Find us at the location below.');
    $mapEmbedUrl = \App\Helpers\Settings::get('map_embed_url', '');
    $contactAddr = \App\Helpers\Settings::get('contact_address');
    $contactPhone= \App\Helpers\Settings::get('contact_phone');
    $contactEmail= \App\Helpers\Settings::get('contact_email');
@endphp
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-12">
            <span class="text-xs font-bold uppercase tracking-widest brand-text mb-3 inline-block">Visit Us</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">{{ $secTitleMap }}</h2>
            @if($mapSubtitle)
            <p class="mt-3 text-gray-500 max-w-xl mx-auto text-base">{{ $mapSubtitle }}</p>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

            <div class="rounded-2xl overflow-hidden shadow-lg border border-gray-100">
                @if($mapEmbedUrl)
                <iframe
                    src="{{ $mapEmbedUrl }}"
                    width="100%" height="380"
                    style="border:0; display:block;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="{{ $secTitleMap }}">
                </iframe>
                @else
                <div style="width:100%;height:380px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;color:#94a3b8;font-size:.85rem;">Map embed not configured &mdash; set it in Admin → Customizer → Sections → Map</div>
                @endif
            </div>

            <div>
                <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wide">Get in Touch</h3>
                <div class="space-y-4 text-sm text-gray-600">
                    @if($contactAddr)
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 shrink-0 brand-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $contactAddr }}</span>
                    </div>
                    @endif
                    @if($contactPhone)
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 brand-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <a href="tel:{{ $contactPhone }}" class="hover:brand-text transition">{{ $contactPhone }}</a>
                    </div>
                    @endif
                    @if($contactEmail)
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 brand-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <a href="mailto:{{ $contactEmail }}" class="hover:brand-text transition">{{ $contactEmail }}</a>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>
@endif
