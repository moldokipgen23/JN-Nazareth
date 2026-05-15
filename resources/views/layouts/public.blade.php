<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteName     = \App\Helpers\Settings::get('site_name', 'Ehlom Blog CMS');
        $seoTitle     = \App\Helpers\Settings::get('seo_meta_title', $siteName);
        $seoDesc      = \App\Helpers\Settings::get('seo_meta_description', \App\Helpers\Settings::get('site_tagline', 'A welcoming community where everyone belongs.'));
        $seoKeywords  = \App\Helpers\Settings::get('seo_meta_keywords', 'community, blog, news, events, gallery');
        $seoRobots    = \App\Helpers\Settings::get('seo_robots', 'index, follow');
        $seoCanonical = \App\Helpers\Settings::get('seo_canonical_url');
        $ogTitle      = \App\Helpers\Settings::get('seo_og_title', $seoTitle);
        $ogDesc       = \App\Helpers\Settings::get('seo_og_description', $seoDesc);
        $ogImage      = \App\Helpers\Settings::get('seo_og_image');
        $twitterTitle = \App\Helpers\Settings::get('seo_twitter_title', $ogTitle);
        $twitterDesc  = \App\Helpers\Settings::get('seo_twitter_desc', $ogDesc);
        $schemaName   = \App\Helpers\Settings::get('seo_schema_org_name', $siteName);
        $favicon      = \App\Helpers\Settings::get('favicon');
        $pageTitle    = isset($title) ? $title . ' — ' . $siteName : $seoTitle;
    @endphp

    <title>{{ $pageTitle }}</title>

    <!-- Core SEO meta -->
    <meta name="description" content="{{ $seoDesc }}">
    @if($seoKeywords)<meta name="keywords" content="{{ $seoKeywords }}">@endif
    <meta name="robots" content="{{ $seoRobots }}">
    @if($seoCanonical)<link rel="canonical" href="{{ $seoCanonical }}">@endif

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDesc }}">
    @if($ogImage)<meta property="og:image" content="{{ \App\Helpers\Settings::storageUrl($ogImage) }}">@endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $twitterTitle }}">
    <meta name="twitter:description" content="{{ $twitterDesc }}">
    @if($ogImage)<meta name="twitter:image" content="{{ \App\Helpers\Settings::storageUrl($ogImage) }}">@endif

    <!-- Schema.org (Organization) -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "Organization",
      "name": "{{ $schemaName }}",
      "url": "{{ url('/') }}",
      "description": "{{ $seoDesc }}",
      @if(\App\Helpers\Settings::get('contact_address'))"address": {
        "@@type": "PostalAddress",
        "streetAddress": "{{ addslashes(\App\Helpers\Settings::get('contact_address', '')) }}"
      }@endif
    }
    </script>

    <!-- Favicon -->
    <link rel="icon" href="{{ $favicon ? \App\Helpers\Settings::storageUrl($favicon) : '/favicon.ico' }}">

    <!-- Fonts: Playfair Display (headings) + Inter (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Dynamic Brand CSS Variables -->
    @php
        $primaryColor   = \App\Helpers\Settings::get('primary_color', '#2d6a4f');
        $secondaryColor = \App\Helpers\Settings::get('secondary_color', '#1b4332');
    @endphp
    <style>
        :root {
            --primary:   {{ $primaryColor }};
            --secondary: {{ $secondaryColor }};
            --accent:    #c8873a;
            --cream:     #fdf8f0;
            --stone:     #78716c;
        }

        *, body { font-family: 'Inter', sans-serif; }
        h1,h2,h3,h4,.font-serif { font-family: 'Playfair Display', Georgia, serif; }

        /* Utility classes */
        .brand-bg        { background-color: var(--primary); }
        .brand-bg-sec    { background-color: var(--secondary); }
        .brand-text      { color: var(--primary); }
        .brand-border    { border-color: var(--primary); }
        .brand-hover:hover { color: var(--primary); }
        .brand-btn {
            background-color: var(--primary);
            color: #ffffff;
            transition: background-color 0.2s, opacity 0.2s;
        }
        .brand-btn:hover { background-color: var(--secondary); }
        .brand-btn-outline {
            border: 2px solid var(--primary);
            color: var(--primary);
            transition: background-color 0.2s, color 0.2s;
        }
        .brand-btn-outline:hover {
            background-color: var(--primary);
            color: #ffffff;
        }
        .nav-active {
            color: var(--primary);
            font-weight: 600;
        }
        .nav-link { color: #44403c; transition: color 0.15s; }
        .nav-link:hover { color: var(--primary); }

        /* Gradient helpers */
        .brand-gradient {
            background: linear-gradient(160deg, var(--secondary) 0%, var(--primary) 100%);
        }
        .brand-gradient-dark {
            background: linear-gradient(160deg, #0d2b1e 0%, var(--secondary) 100%);
        }

        /* Village-style section dividers */
        .section-label {
            font-family: 'Inter', sans-serif;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--accent);
        }

        /* Leaf/cross accent line */
        .accent-line {
            display: inline-block;
            width: 40px;
            height: 2px;
            background: var(--accent);
            border-radius: 2px;
            vertical-align: middle;
            margin: 0 10px;
        }

        /* Page hero backgrounds */
        .page-hero {
            background: linear-gradient(160deg, var(--secondary) 0%, var(--primary) 100%);
            position: relative;
            overflow: hidden;
        }
        .page-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased text-stone-800" style="background-color: var(--cream);">

    <!-- ══ Navigation ══ -->
    <header class="sticky top-0 z-50" style="background: rgba(253,248,240,0.97); backdrop-filter: blur(8px); border-bottom: 1px solid #e7dfd4; box-shadow: 0 1px 12px rgba(0,0,0,0.06);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <!-- Logo / Church Name -->
                @php
                    $logo = \App\Helpers\Settings::get('logo');
                    $siteName = \App\Helpers\Settings::get('site_name', 'Ehlom Blog CMS');
                    $navPages = \App\Models\Page::inNav()->orderBy('title')->get();
                @endphp
                <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                    @if($logo)
                        <img src="{{ \App\Helpers\Settings::storageUrl($logo) }}"
                             alt="{{ $siteName }}"
                             class="h-9 w-auto object-contain">
                    @else
                        <span class="inline-flex items-center gap-2 font-bold tracking-tight brand-text" style="font-family:'Playfair Display',Georgia,serif;">
                            <svg class="w-7 h-7 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 12l9-9 9 9M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/>
                            </svg>
                            <span style="font-size:1.1rem; font-weight:800; line-height:1.2;">{{ $siteName }}</span>
                        </span>
                    @endif
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center gap-7 text-sm font-medium">
                    <a href="{{ route('home') }}"
                       class="{{ request()->routeIs('home') ? 'nav-active' : 'nav-link' }} transition">Home</a>
                    <a href="{{ route('about') }}"
                       class="{{ request()->routeIs('about') ? 'nav-active' : 'nav-link' }} transition">Our Story</a>
                    <a href="{{ route('blogs') }}"
                       class="{{ request()->routeIs('blogs*') ? 'nav-active' : 'nav-link' }} transition">Blog</a>
                    <a href="{{ route('events') }}"
                       class="{{ request()->routeIs('events*') ? 'nav-active' : 'nav-link' }} transition">Events</a>
                    <a href="{{ route('gallery') }}"
                       class="{{ request()->routeIs('gallery*') ? 'nav-active' : 'nav-link' }} transition">Gallery</a>
                    <a href="{{ route('videos') }}"
                       class="{{ request()->routeIs('videos*') ? 'nav-active' : 'nav-link' }} transition">Videos</a>
                    @foreach($navPages as $navPage)
                        <a href="{{ route('page', $navPage->slug) }}"
                           class="{{ request()->is('page/' . $navPage->slug) ? 'nav-active' : 'nav-link' }} transition">{{ $navPage->title }}</a>
                    @endforeach
                </nav>

                <!-- Mobile Hamburger -->
                <button id="mobile-menu-btn"
                        aria-label="Toggle navigation"
                        class="md:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100 transition">
                    <svg id="hamburger-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white shadow-md">
            <div class="px-4 py-3 space-y-1 text-sm font-medium">
                <a href="{{ route('home') }}"
                   class="block py-2.5 px-3 rounded-lg {{ request()->routeIs('home') ? 'brand-text font-semibold bg-gray-50' : 'text-gray-700 hover:bg-gray-50' }} transition">Home</a>
                <a href="{{ route('about') }}"
                   class="block py-2.5 px-3 rounded-lg {{ request()->routeIs('about') ? 'brand-text font-semibold bg-gray-50' : 'text-gray-700 hover:bg-gray-50' }} transition">Our Story</a>
                <a href="{{ route('blogs') }}"
                   class="block py-2.5 px-3 rounded-lg {{ request()->routeIs('blogs*') ? 'brand-text font-semibold bg-gray-50' : 'text-gray-700 hover:bg-gray-50' }} transition">Blog</a>
                <a href="{{ route('events') }}"
                   class="block py-2.5 px-3 rounded-lg {{ request()->routeIs('events*') ? 'brand-text font-semibold bg-gray-50' : 'text-gray-700 hover:bg-gray-50' }} transition">Events</a>
                <a href="{{ route('gallery') }}"
                   class="block py-2.5 px-3 rounded-lg {{ request()->routeIs('gallery*') ? 'brand-text font-semibold bg-gray-50' : 'text-gray-700 hover:bg-gray-50' }} transition">Gallery</a>
                <a href="{{ route('videos') }}"
                   class="block py-2.5 px-3 rounded-lg {{ request()->routeIs('videos*') ? 'brand-text font-semibold bg-gray-50' : 'text-gray-700 hover:bg-gray-50' }} transition">Videos</a>
                @foreach($navPages as $navPage)
                    <a href="{{ route('page', $navPage->slug) }}"
                       class="block py-2.5 px-3 rounded-lg {{ request()->is('page/' . $navPage->slug) ? 'brand-text font-semibold bg-gray-50' : 'text-gray-700 hover:bg-gray-50' }} transition">{{ $navPage->title }}</a>
                @endforeach
            </div>
        </div>
    </header>

    <!-- ══ Main Content ══ -->
    <main>
        @yield('content')
    </main>

    <!-- ══ Footer ══ -->
    @php
        $footerName         = \App\Helpers\Settings::get('site_name', 'Ehlom Blog CMS');
        $footerTagline      = \App\Helpers\Settings::get('site_tagline', 'A welcoming community where everyone belongs.');
        $footerText         = \App\Helpers\Settings::get('footer_text', '© ' . date('Y') . ' ' . $footerName . '. All rights reserved.');
        $footerLinksTitle   = \App\Helpers\Settings::get('footer_quick_links_title', 'Quick Links');
        $footerContactTitle = \App\Helpers\Settings::get('footer_contact_title', 'Contact Us');
        $footerShowCredit   = \App\Helpers\Settings::get('footer_show_credit', '1');
        $footerCreditText   = \App\Helpers\Settings::get('footer_credit_text', 'Designed by Ehlom Digital');
        $contactEmail  = \App\Helpers\Settings::get('contact_email');
        $contactPhone  = \App\Helpers\Settings::get('contact_phone');
        $contactAddr   = \App\Helpers\Settings::get('contact_address');
        $facebook      = \App\Helpers\Settings::get('social_facebook');
        $youtube       = \App\Helpers\Settings::get('social_youtube');
        $instagram     = \App\Helpers\Settings::get('social_instagram');
        $twitter       = \App\Helpers\Settings::get('social_twitter');
        $whatsapp      = \App\Helpers\Settings::get('social_whatsapp');
        $hasSocial     = $facebook || $youtube || $instagram || $twitter || $whatsapp;
    @endphp
    <footer style="background:#111810; color:#a8a29e; font-family:'Inter',sans-serif;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

                <!-- Church Identity -->
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        @if($logo)
                            <img src="{{ \App\Helpers\Settings::storageUrl($logo) }}"
                                 alt="{{ $footerName }}" class="h-8 w-auto object-contain brightness-200">
                        @else
                            <svg class="w-6 h-6 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 12l9-9 9 9M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/>
                            </svg>
                        @endif
                        <span class="text-white font-bold text-lg">{{ $footerName }}</span>
                    </div>
                    <p class="text-sm leading-relaxed text-gray-400 max-w-xs">{{ $footerTagline }}</p>

                    @if($hasSocial)
                        <div class="flex items-center gap-3 mt-6">
                            @if($facebook)
                                <a href="{{ $facebook }}" target="_blank" rel="noopener" aria-label="Facebook"
                                   class="w-9 h-9 rounded-full flex items-center justify-center bg-white/10 hover:bg-blue-600 transition-all duration-200 text-gray-300 hover:text-white">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.563 9.876v-6.988h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.888h-2.33v6.988A10.003 10.003 0 0 0 22 12z"/></svg>
                                </a>
                            @endif
                            @if($youtube)
                                <a href="{{ $youtube }}" target="_blank" rel="noopener" aria-label="YouTube"
                                   class="w-9 h-9 rounded-full flex items-center justify-center bg-white/10 hover:bg-red-600 transition-all duration-200 text-gray-300 hover:text-white">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                </a>
                            @endif
                            @if($instagram)
                                <a href="{{ $instagram }}" target="_blank" rel="noopener" aria-label="Instagram"
                                   class="w-9 h-9 rounded-full flex items-center justify-center bg-white/10 hover:bg-pink-600 transition-all duration-200 text-gray-300 hover:text-white">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
                                </a>
                            @endif
                            @if($twitter)
                                <a href="{{ $twitter }}" target="_blank" rel="noopener" aria-label="Twitter / X"
                                   class="w-9 h-9 rounded-full flex items-center justify-center bg-white/10 hover:bg-gray-700 transition-all duration-200 text-gray-300 hover:text-white">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </a>
                            @endif
                            @if($whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $whatsapp) }}" target="_blank" rel="noopener" aria-label="WhatsApp"
                                   class="w-9 h-9 rounded-full flex items-center justify-center bg-white/10 hover:bg-green-600 transition-all duration-200 text-gray-300 hover:text-white">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-white font-semibold text-xs uppercase tracking-widest mb-4">{{ $footerLinksTitle }}</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('home') }}"    class="hover:text-white transition">Home</a></li>
                        <li><a href="{{ route('about') }}"   class="hover:text-white transition">Our Story</a></li>
                        <li><a href="{{ route('blogs') }}"   class="hover:text-white transition">Blog</a></li>
                        <li><a href="{{ route('events') }}"  class="hover:text-white transition">Events</a></li>
                        <li><a href="{{ route('gallery') }}" class="hover:text-white transition">Gallery</a></li>
                        <li><a href="{{ route('videos') }}"  class="hover:text-white transition">Videos</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="text-white font-semibold text-xs uppercase tracking-widest mb-4">{{ $footerContactTitle }}</h3>
                    <ul class="space-y-3 text-sm text-gray-400">
                        @if($contactAddr)
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 mt-0.5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>{{ $contactAddr }}</span>
                            </li>
                        @endif
                        @if($contactPhone)
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <a href="tel:{{ $contactPhone }}" class="hover:text-white transition">{{ $contactPhone }}</a>
                            </li>
                        @endif
                        @if($contactEmail)
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <a href="mailto:{{ $contactEmail }}" class="hover:text-white transition">{{ $contactEmail }}</a>
                            </li>
                        @endif
                        @if(!$contactAddr && !$contactPhone && !$contactEmail)
                            <li class="text-gray-500 italic text-xs">Contact details coming soon.</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Bottom bar -->
            <div style="border-top:1px solid rgba(255,255,255,0.08); margin-top:40px; padding-top:24px; display:flex; flex-direction:column; align-items:center; gap:12px; font-size:0.75rem; color:#6b7280; text-align:center;">
                <span>{{ $footerText }}</span>
                @if($footerShowCredit)
                <a href="https://ehlom.com" target="_blank" rel="noopener"
                   style="display:inline-flex; align-items:center; gap:8px; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); border-radius:50px; padding:6px 14px 6px 8px; text-decoration:none; transition:all .25s; color:#9ca3af;"
                   onmouseover="this.style.background='rgba(20,184,166,.15)'; this.style.borderColor='rgba(20,184,166,.4)'; this.style.color='#fff';"
                   onmouseout="this.style.background='rgba(255,255,255,.06)'; this.style.borderColor='rgba(255,255,255,.12)'; this.style.color='#9ca3af';">
                    @if(file_exists(public_path('images/ehlom-logo.png')))
                        <img src="/images/ehlom-logo.png" alt="Ehlom Digital" style="height:20px; width:auto; border-radius:4px; opacity:.85;">
                    @else
                        <span style="width:22px; height:22px; border-radius:6px; background:linear-gradient(135deg,#0f766e,#14b8a6); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="11" height="11" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        </span>
                    @endif
                    <span style="font-size:.72rem; line-height:1.3;">{{ $footerCreditText }}</span>
                </a>
                @endif
            </div>
        </div>
    </footer>

    <!-- Global scroll-reveal & lazy-load -->
    <script>
    (function(){
        // Scroll reveal
        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver(entries => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.add('visible');
                        io.unobserve(e.target);
                    }
                });
            }, { threshold: 0.1 });
            document.querySelectorAll('.scroll-reveal').forEach(el => io.observe(el));
        } else {
            document.querySelectorAll('.scroll-reveal').forEach(el => el.classList.add('visible'));
        }
    })();
    </script>

    <style>
    .scroll-reveal { opacity:0; transform:translateY(28px); transition:opacity .65s ease, transform .65s ease; }
    .scroll-reveal.visible { opacity:1; transform:none; }
    .scroll-reveal-left { opacity:0; transform:translateX(-28px); transition:opacity .65s ease, transform .65s ease; }
    .scroll-reveal-left.visible { opacity:1; transform:none; }
    .scroll-reveal-right { opacity:0; transform:translateX(28px); transition:opacity .65s ease, transform .65s ease; }
    .scroll-reveal-right.visible { opacity:1; transform:none; }
    </style>

    <!-- Mobile menu toggle -->
    <script>
        (function () {
            var btn  = document.getElementById('mobile-menu-btn');
            var menu = document.getElementById('mobile-menu');
            var ham  = document.getElementById('hamburger-icon');
            var cls  = document.getElementById('close-icon');
            btn.addEventListener('click', function () {
                var open = !menu.classList.contains('hidden');
                menu.classList.toggle('hidden', open);
                ham.classList.toggle('hidden', !open);
                cls.classList.toggle('hidden', open);
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
