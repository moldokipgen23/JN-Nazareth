@extends('layouts.admin')
@section('page-title', 'Site Customizer')

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">Site Customizer</h2>
        <p style="font-size:12px; color:#64748b; margin:3px 0 0;">Manage your site's content, appearance, sections, and SEO</p>
    </div>
    <a href="{{ route('home') }}" target="_blank"
       style="display:inline-flex; align-items:center; gap:7px; background:#f0f9ff; color:#0369a1; border:1px solid #bae6fd; padding:9px 16px; border-radius:10px; font-size:13px; font-weight:600; text-decoration:none;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
        View Site
    </a>
</div>

@if($errors->any())
<div style="background:#fef2f2; border:1px solid #fca5a5; border-radius:10px; padding:12px 16px; margin-bottom:20px;">
    <div style="font-size:13px; font-weight:600; color:#991b1b; margin-bottom:6px;">Please fix the following errors:</div>
    <ul style="margin:0; padding-left:18px;">
        @foreach($errors->all() as $error)
        <li style="font-size:12px; color:#b91c1c; margin-bottom:3px;">{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Tab navigation --}}
<style>
.cust-tabs { display:flex; gap:6px; margin-bottom:20px; background:#fff; padding:6px; border-radius:12px; box-shadow:0 1px 6px rgba(0,0,0,.06); border:1px solid #f1f5f9; overflow-x:auto; -webkit-overflow-scrolling:touch; scrollbar-width:none; flex-wrap:wrap; }
.cust-tabs::-webkit-scrollbar { display:none; }
.cust-tab { padding:8px 14px; border-radius:8px; font-size:12px; font-weight:600; border:none; cursor:pointer; background:transparent; color:#64748b; transition:all .15s; white-space:nowrap; flex-shrink:0; }
@media(max-width:640px){ .cust-tab { padding:7px 10px; font-size:11px; } }

/* Section cards */
.sec-card { border:1px solid #e2e8f0; border-radius:12px; background:#fff; overflow:hidden; transition:box-shadow .15s; }
.sec-card:hover { box-shadow:0 2px 8px rgba(0,0,0,.07); }
/* Form utilities */
.flabel { font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px; }
.finput { border:1px solid #e2e8f0; border-radius:8px; padding:8px 11px; font-size:13px; width:100%; box-sizing:border-box; outline:none; }
.fta    { border:1px solid #e2e8f0; border-radius:8px; padding:8px 11px; font-size:13px; width:100%; outline:none; resize:vertical; box-sizing:border-box; }
.g2     { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.g4     { display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:12px; }
.mb14   { margin-bottom:14px; }
.mb18   { margin-bottom:18px; }
.mb20   { margin-bottom:20px; }
.save-btn    { background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; border:none; border-radius:9px; padding:9px 22px; font-size:13px; font-weight:700; cursor:pointer; }
.manage-link { display:inline-flex; align-items:center; gap:6px; background:#f0fdf4; color:#0f766e; border:1px solid #bbf7d0; padding:8px 14px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none; }
</style>
<div class="cust-tabs">
    <button onclick="switchTab('general')" id="tab-btn-general" class="cust-tab" style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff;">
        General
    </button>
    <button onclick="switchTab('appearance')" id="tab-btn-appearance" class="cust-tab">
        Appearance
    </button>
    <button onclick="switchTab('hero')" id="tab-btn-hero" class="cust-tab">
        Hero &amp; Banner
    </button>
    <button onclick="switchTab('sections')" id="tab-btn-sections" class="cust-tab">
        📋 Sections
    </button>
    <button onclick="switchTab('seo')" id="tab-btn-seo" class="cust-tab">
        🔍 SEO
    </button>
</div>

{{-- ═══════════════════════════════════════════════
     TAB 1 — GENERAL
════════════════════════════════════════════════ --}}
<div id="tab-general" style="display:block;">
    <form method="POST" action="{{ route('admin.customizer.update-general') }}">
        @csrf
        <div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; padding:28px 32px;">

            <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">Site Identity</div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">
                <div>
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Site Name <span style="color:#e11d48;">*</span></label>
                    <input type="text" name="site_name" value="{{ old('site_name', \App\Helpers\Settings::get('site_name', 'My Community Website')) }}"
                           style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                           required maxlength="100">
                </div>
                <div>
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Tagline</label>
                    <input type="text" name="site_tagline" value="{{ old('site_tagline', \App\Helpers\Settings::get('site_tagline')) }}"
                           style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                           maxlength="200" placeholder="A short description of your organisation">
                </div>
            </div>

            <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">Contact Information</div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                <div>
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Email Address</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', \App\Helpers\Settings::get('contact_email')) }}"
                           style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                           maxlength="100" placeholder="hello@example.com">
                </div>
                <div>
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Phone Number</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', \App\Helpers\Settings::get('contact_phone')) }}"
                           style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                           maxlength="50" placeholder="+91 12345 67890">
                </div>
            </div>
            <div style="margin-bottom:20px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Address</label>
                <textarea name="contact_address" rows="2" maxlength="300"
                          style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; resize:vertical; box-sizing:border-box;"
                          placeholder="123 Main Street, Your Town, Country">{{ old('contact_address', \App\Helpers\Settings::get('contact_address')) }}</textarea>
            </div>
            <div style="margin-bottom:24px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Footer Text</label>
                <textarea name="footer_text" rows="2" maxlength="300"
                          style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; resize:vertical; box-sizing:border-box;"
                          placeholder="© 2025 My Community Website. All rights reserved.">{{ old('footer_text', \App\Helpers\Settings::get('footer_text')) }}</textarea>
            </div>

            <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">Social Media Links</div>
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:16px; margin-bottom:28px;">
                @foreach([
                    ['name'=>'social_facebook',  'label'=>'Facebook',   'ph'=>'https://facebook.com/yourpage'],
                    ['name'=>'social_youtube',   'label'=>'YouTube',    'ph'=>'https://youtube.com/yourchannel'],
                    ['name'=>'social_instagram', 'label'=>'Instagram',  'ph'=>'https://instagram.com/yourprofile'],
                    ['name'=>'social_twitter',   'label'=>'Twitter / X','ph'=>'https://x.com/yourprofile'],
                    ['name'=>'social_whatsapp',  'label'=>'WhatsApp',   'ph'=>'+91234567890'],
                ] as $soc)
                <div>
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">{{ $soc['label'] }}</label>
                    <input type="{{ $soc['name'] === 'social_whatsapp' ? 'text' : 'url' }}" name="{{ $soc['name'] }}"
                           value="{{ old($soc['name'], \App\Helpers\Settings::get($soc['name'])) }}"
                           style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                           maxlength="200" placeholder="{{ $soc['ph'] }}">
                </div>
                @endforeach
            </div>

            <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">Media / CDN Settings</div>
            <div style="margin-bottom:24px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">
                    CDN Base URL
                    <span style="font-weight:400; color:#94a3b8; margin-left:6px;">optional — leave blank to use local storage</span>
                </label>
                <input type="url" name="cdn_base_url"
                       value="{{ old('cdn_base_url', \App\Helpers\Settings::get('cdn_base_url')) }}"
                       style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                       placeholder="https://your-zone.b-cdn.net" maxlength="300">
                <p style="font-size:11px; color:#94a3b8; margin:6px 0 0;">Works with Bunny CDN, Cloudflare R2, etc.</p>
            </div>

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:600; padding:11px 24px; border-radius:10px; border:none; cursor:pointer; box-shadow:0 4px 12px rgba(20,184,166,.3); display:flex; align-items:center; gap:8px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    Save General Settings
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ═══════════════════════════════════════════════
     TAB 2 — APPEARANCE
════════════════════════════════════════════════ --}}
<div id="tab-appearance" style="display:none;">
    <form method="POST" action="{{ route('admin.customizer.update-appearance') }}" enctype="multipart/form-data">
        @csrf
        <div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; padding:28px 32px;">

            <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">Brand Colors</div>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:24px; margin-bottom:28px;">
                @include('admin.customizer.partials.color-input', ['name'=>'primary_color',   'label'=>'Primary Color',   'value'=>old('primary_color',   \App\Helpers\Settings::get('primary_color',   '#0f766e'))])
                @include('admin.customizer.partials.color-input', ['name'=>'secondary_color', 'label'=>'Secondary Color', 'value'=>old('secondary_color', \App\Helpers\Settings::get('secondary_color', '#14b8a6'))])
                @include('admin.customizer.partials.color-input', ['name'=>'accent_color',    'label'=>'Accent Color',    'value'=>old('accent_color',    \App\Helpers\Settings::get('accent_color',    '#0d9488'))])
            </div>
            <div style="display:flex; gap:0; border-radius:10px; overflow:hidden; margin-bottom:28px; height:32px; box-shadow:0 2px 6px rgba(0,0,0,.1);">
                <div id="preview-primary"   style="flex:1; background:{{ \App\Helpers\Settings::get('primary_color',   '#0f766e') }};"></div>
                <div id="preview-secondary" style="flex:1; background:{{ \App\Helpers\Settings::get('secondary_color', '#14b8a6') }};"></div>
                <div id="preview-accent"    style="flex:1; background:{{ \App\Helpers\Settings::get('accent_color',    '#0d9488') }};"></div>
            </div>

            <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">Logo</div>
            <div style="display:flex; align-items:center; gap:24px; margin-bottom:28px;">
                @if(\App\Helpers\Settings::get('logo'))
                <div style="flex-shrink:0;">
                    <div style="font-size:11px; font-weight:600; color:#94a3b8; margin-bottom:8px; text-transform:uppercase; letter-spacing:.05em;">Current Logo</div>
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px; display:inline-block;">
                        <img src="{{ \App\Helpers\Settings::storageUrl(\App\Helpers\Settings::get('logo')) }}" alt="Site Logo" style="max-height:60px; max-width:180px; object-fit:contain; display:block;">
                    </div>
                </div>
                @endif
                <div style="flex:1;">
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">{{ \App\Helpers\Settings::get('logo') ? 'Replace Logo' : 'Upload Logo' }}</label>
                    <div style="border:2px dashed #e2e8f0; border-radius:10px; padding:18px; text-align:center; cursor:pointer; transition:border-color .15s;"
                         onclick="document.getElementById('logo-input').click()"
                         onmouseover="this.style.borderColor='#14b8a6'" onmouseout="this.style.borderColor='#e2e8f0'">
                        <svg width="24" height="24" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 8px; display:block;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div id="logo-label" style="font-size:12px; color:#64748b; font-weight:500;">Click to upload logo (PNG, JPG, SVG — max 2 MB)</div>
                    </div>
                    <input type="file" name="logo" id="logo-input" accept="image/*" style="display:none;"
                           onchange="document.getElementById('logo-label').textContent=this.files[0]?.name||'Click to upload logo'">
                </div>
            </div>

            <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">Favicon</div>
            <div style="display:flex; align-items:center; gap:24px; margin-bottom:28px;">
                @if(\App\Helpers\Settings::get('favicon'))
                <div style="flex-shrink:0;">
                    <div style="font-size:11px; font-weight:600; color:#94a3b8; margin-bottom:8px; text-transform:uppercase; letter-spacing:.05em;">Current Favicon</div>
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px; display:inline-flex; align-items:center; justify-content:center; width:52px; height:52px;">
                        <img src="{{ \App\Helpers\Settings::storageUrl(\App\Helpers\Settings::get('favicon')) }}" alt="Favicon" style="width:32px; height:32px; object-fit:contain;">
                    </div>
                </div>
                @endif
                <div style="flex:1;">
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">{{ \App\Helpers\Settings::get('favicon') ? 'Replace Favicon' : 'Upload Favicon' }}</label>
                    <div style="border:2px dashed #e2e8f0; border-radius:10px; padding:18px; text-align:center; cursor:pointer; transition:border-color .15s;"
                         onclick="document.getElementById('favicon-input').click()"
                         onmouseover="this.style.borderColor='#14b8a6'" onmouseout="this.style.borderColor='#e2e8f0'">
                        <div id="favicon-label" style="font-size:12px; color:#64748b; font-weight:500;">Click to upload favicon (ICO or PNG — max 512 KB)</div>
                    </div>
                    <input type="file" name="favicon" id="favicon-input" accept=".ico,.png" style="display:none;"
                           onchange="document.getElementById('favicon-label').textContent=this.files[0]?.name||'Click to upload favicon'">
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:600; padding:11px 24px; border-radius:10px; border:none; cursor:pointer; box-shadow:0 4px 12px rgba(20,184,166,.3); display:flex; align-items:center; gap:8px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    Save Appearance
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ═══════════════════════════════════════════════
     TAB 3 — HERO & BANNER SLIDES
════════════════════════════════════════════════ --}}
<div id="tab-hero" style="display:none;">

    <form method="POST" action="{{ route('admin.customizer.update-hero') }}" style="margin-bottom:24px;">
        @csrf
        <div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; padding:28px 32px;">

            <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">Hero Section Text</div>
            <div style="margin-bottom:16px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Hero Title <span style="color:#e11d48;">*</span></label>
                <input type="text" name="hero_title" value="{{ old('hero_title', \App\Helpers\Settings::get('hero_title', 'Welcome to Our Community')) }}"
                       style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                       required maxlength="200">
            </div>
            <div style="margin-bottom:16px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Hero Subtitle</label>
                <textarea name="hero_subtitle" rows="3" maxlength="400"
                          style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; resize:vertical; box-sizing:border-box;"
                          placeholder="A warm welcome message...">{{ old('hero_subtitle', \App\Helpers\Settings::get('hero_subtitle')) }}</textarea>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:16px;">
                <div>
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">CTA Button Text</label>
                    <input type="text" name="hero_cta_text" value="{{ old('hero_cta_text', \App\Helpers\Settings::get('hero_cta_text', 'Learn More')) }}"
                           style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                           maxlength="80" placeholder="Learn More">
                </div>
                <div>
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">CTA Button Link</label>
                    <input type="text" name="hero_cta_link" value="{{ old('hero_cta_link', \App\Helpers\Settings::get('hero_cta_link', '/about')) }}"
                           style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                           maxlength="200" placeholder="/about">
                </div>
            </div>

            <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">About Preview Text</div>
            <div style="margin-bottom:20px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Short About Text</label>
                <textarea name="about_preview" rows="4" maxlength="500"
                          style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; resize:vertical; box-sizing:border-box;"
                          placeholder="A brief description of your church...">{{ old('about_preview', \App\Helpers\Settings::get('about_preview')) }}</textarea>
                <div style="font-size:11px; color:#94a3b8; margin-top:4px;">Max 500 characters. Shown in the About preview section on the homepage.</div>
            </div>

            <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">🎬 Story Showcase Video</div>
            <div style="margin-bottom:8px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">YouTube Video URL</label>
                <input type="text" name="hero_youtube_url"
                       value="{{ old('hero_youtube_url', \App\Helpers\Settings::get('hero_youtube_url')) }}"
                       style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                       placeholder="https://www.youtube.com/watch?v=... or https://youtu.be/...">
                <div style="font-size:11px; color:#94a3b8; margin-top:4px;">Plays as a muted background video after the hero banner. Leave blank to hide.</div>
            </div>
            @if(\App\Helpers\Settings::get('hero_youtube_url'))
            <div style="display:inline-flex; align-items:center; gap:6px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:6px 12px; font-size:12px; color:#0f766e; font-weight:600; margin-bottom:16px;">
                <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                Video active: {{ \App\Helpers\Settings::get('hero_youtube_url') }}
            </div>
            @endif

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:600; padding:11px 24px; border-radius:10px; border:none; cursor:pointer; box-shadow:0 4px 12px rgba(20,184,166,.3); display:flex; align-items:center; gap:8px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    Save Hero Settings
                </button>
            </div>
        </div>
    </form>

    {{-- Add Banner Slide --}}
    <div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; padding:28px 32px; margin-bottom:24px;">
        <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">Add New Banner Slide</div>
        <form method="POST" action="{{ route('admin.customizer.slides.store') }}" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Slide Image <span style="color:#e11d48;">*</span></label>
                <div style="border:2px dashed #e2e8f0; border-radius:10px; padding:20px; text-align:center; cursor:pointer; transition:border-color .15s;"
                     onclick="document.getElementById('slide-image-input').click()"
                     onmouseover="this.style.borderColor='#14b8a6'" onmouseout="this.style.borderColor='#e2e8f0'">
                    <svg width="28" height="28" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 8px; display:block;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <div id="slide-image-label" style="font-size:12px; color:#64748b; font-weight:500;">Click to select slide image (max 4 MB)</div>
                    <div style="font-size:11px; color:#94a3b8; margin-top:3px;">Recommended: 1600 × 600 px or wider</div>
                </div>
                <input type="file" name="image" id="slide-image-input" accept="image/*" required style="display:none;" onchange="previewSlideImage(this)">
                <div id="slide-image-preview" style="margin-top:10px; display:none;">
                    <img id="slide-preview-img" src="" alt="Preview" style="max-height:120px; border-radius:8px; object-fit:cover; width:100%;">
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Slide Title</label>
                    <input type="text" name="title" style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;" maxlength="150" placeholder="Optional overlay title">
                </div>
                <div>
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Subtitle</label>
                    <input type="text" name="subtitle" style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;" maxlength="300" placeholder="Optional subtitle text">
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
                <div>
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Button Text</label>
                    <input type="text" name="button_text" style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;" maxlength="60" placeholder="e.g. Learn More">
                </div>
                <div>
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Button Link</label>
                    <input type="text" name="button_link" style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;" maxlength="200" placeholder="/about">
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:600; padding:11px 24px; border-radius:10px; border:none; cursor:pointer; box-shadow:0 4px 12px rgba(20,184,166,.3); display:flex; align-items:center; gap:8px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    Add Slide
                </button>
            </div>
        </form>
    </div>

    {{-- Current slides --}}
    <div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; padding:28px 32px;">
        <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between;">
            <span>Current Banner Slides</span>
            <span style="background:#f1f5f9; color:#475569; font-size:11px; font-weight:600; padding:3px 10px; border-radius:99px;">{{ $slides->count() }} {{ Str::plural('slide', $slides->count()) }}</span>
        </div>
        @if($slides->isEmpty())
        <div style="text-align:center; padding:48px 20px;">
            <p style="font-size:14px; font-weight:600; color:#334155; margin:0 0 4px;">No slides yet</p>
            <p style="font-size:12px; color:#94a3b8; margin:0;">Upload your first banner slide above.</p>
        </div>
        @else
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(260px, 1fr)); gap:16px;">
            @foreach($slides as $slide)
            <div style="border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; background:#fafbfc;">
                <div style="position:relative; height:140px; overflow:hidden;">
                    <img src="{{ \App\Helpers\Settings::storageUrl($slide->image) }}" alt="{{ $slide->title ?? 'Slide ' . $loop->iteration }}" style="width:100%; height:100%; object-fit:cover; display:block;">
                    <div style="position:absolute; top:8px; right:8px;">
                        @if($slide->active)
                        <span style="background:rgba(22,163,74,.9); color:#fff; font-size:10px; font-weight:700; padding:3px 8px; border-radius:99px;">Active</span>
                        @else
                        <span style="background:rgba(100,116,139,.85); color:#fff; font-size:10px; font-weight:700; padding:3px 8px; border-radius:99px;">Inactive</span>
                        @endif
                    </div>
                    <div style="position:absolute; top:8px; left:8px;">
                        <span style="background:rgba(15,23,42,.75); color:#fff; font-size:10px; font-weight:700; padding:3px 8px; border-radius:99px;">#{{ $loop->iteration }}</span>
                    </div>
                </div>
                <div style="padding:12px 14px;">
                    <div style="font-size:13px; font-weight:600; color:#0f172a; margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $slide->title ?: '(No title)' }}</div>
                    @if($slide->subtitle)<div style="font-size:11px; color:#64748b; margin-bottom:8px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $slide->subtitle }}</div>
                    @else<div style="margin-bottom:8px;"></div>@endif
                    <div style="display:flex; gap:6px;">
                        <form method="POST" action="{{ route('admin.customizer.slides.toggle', $slide) }}" style="flex:1;">
                            @csrf
                            <button type="submit" style="width:100%; padding:6px 10px; border-radius:7px; font-size:11px; font-weight:600; border:none; cursor:pointer; background:{{ $slide->active ? '#fef9c3' : '#f0fdf4' }}; color:{{ $slide->active ? '#854d0e' : '#15803d' }};">
                                {{ $slide->active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.customizer.slides.destroy', $slide) }}" onsubmit="return confirm('Delete this slide?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="padding:6px 12px; border-radius:7px; font-size:11px; font-weight:600; border:none; cursor:pointer; background:#fff1f2; color:#e11d48;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     TAB 4 — 📋 SECTIONS
════════════════════════════════════════════════ --}}
@php
$allSections = [
    'hero'              => ['label'=>'Hero Banner',             'icon'=>'🖼️',  'pinned'=>true],
    'stats'             => ['label'=>'Statistics Bar',          'icon'=>'📊',  'pinned'=>false],
    'story-video'     => ['label'=>'Story Showcase Video',  'icon'=>'🎥',  'pinned'=>false],
    'about'             => ['label'=>'About the Church',        'icon'=>'⛪',  'pinned'=>false],
    'story'           => ['label'=>'Leadership & Our Story',    'icon'=>'🏘️', 'pinned'=>false],
    'programs'              => ['label'=>'Programmes Community',          'icon'=>'🤝',  'pinned'=>false],
    'map'               => ['label'=>'Story Map',             'icon'=>'🗺️', 'pinned'=>false],
    'gallery'           => ['label'=>'Church Gallery',          'icon'=>'📸',  'pinned'=>false],
    'events'            => ['label'=>'Upcoming Events',         'icon'=>'📅',  'pinned'=>false],
    'blog'              => ['label'=>'Latest Blog Posts',       'icon'=>'✍️',  'pinned'=>false],
    'videos'            => ['label'=>'Sermons & Videos',        'icon'=>'🎬',  'pinned'=>false],
    'location'             => ['label'=>'How to Location',            'icon'=>'📍',  'pinned'=>false],
    'hall-of-fame'      => ['label'=>'Hall of Fame',             'icon'=>'⭐',  'pinned'=>false],
    'cta'               => ['label'=>'Contact / Join CTA',      'icon'=>'✉️',  'pinned'=>false],
];
$defaultOrder = array_keys($allSections);
$currentOrder = json_decode(\App\Helpers\Settings::get('home_section_order',''), true) ?: $defaultOrder;
$currentOrder = array_unique(array_merge($currentOrder, $defaultOrder));
@endphp

<div id="tab-sections" style="display:none;">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
    <p style="font-size:12px;color:#64748b;margin:0;">⠿ Drag to reorder &nbsp;•&nbsp; Toggle on/off &nbsp;•&nbsp; Click row to edit</p>
    <span id="order-save-status" style="font-size:11px;font-weight:500;color:#0f766e;"></span>
</div>

<div id="section-order-list" style="display:flex;flex-direction:column;gap:6px;">

@foreach($currentOrder as $secKey)
@if(!isset($allSections[$secKey]))@continue @endif
@php
    $sec  = $allSections[$secKey];
    $nk   = str_replace('-','_',$secKey);
    $isOn = \App\Helpers\Settings::get('sec_show_'.$nk,'1') === '1';
@endphp
<div data-sec="{{ $secKey }}" class="sec-card">

    {{-- ── Card Header ── --}}
    <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:{{ $sec['pinned']?'#f0fdf4':'#f8fafc' }};{{ !$sec['pinned']?'cursor:pointer;':'' }}"
         @if(!$sec['pinned'])onclick="toggleSecCard('{{ $nk }}')"@endif>

        @if(!$sec['pinned'])
        <div onclick="event.stopPropagation()" title="Drag to reorder"
             style="color:#94a3b8;cursor:grab;flex-shrink:0;padding:2px 4px;line-height:1;"
             onmousedown="event.stopPropagation();document.querySelector('[data-sec=\'{{ $secKey }}\']').setAttribute('draggable','true')"
             onmouseup="document.querySelector('[data-sec=\'{{ $secKey }}\']').setAttribute('draggable','false')">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        </div>
        <label onclick="event.stopPropagation()" style="display:inline-flex;align-items:center;gap:5px;cursor:pointer;flex-shrink:0;">
            <div style="position:relative;width:36px;height:20px;">
                <input type="checkbox" {{ $isOn?'checked':'' }} onclick="event.stopPropagation()"
                       onchange="saveSecVis('{{ $nk }}',this.checked)"
                       style="opacity:0;position:absolute;inset:0;cursor:pointer;margin:0;z-index:1;">
                <div id="tot-{{ $nk }}" style="position:absolute;inset:0;border-radius:50px;background:{{ $isOn?'#0f766e':'#cbd5e1' }};transition:.2s;"></div>
                <div id="toh-{{ $nk }}" style="position:absolute;top:2px;left:2px;width:16px;height:16px;border-radius:50%;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.2);transition:.2s;transform:{{ $isOn?'translateX(16px)':'translateX(0)' }};"></div>
            </div>
            <span id="tol-{{ $nk }}" style="font-size:11px;color:#64748b;min-width:20px;">{{ $isOn?'On':'Off' }}</span>
        </label>
        @else
        <div style="width:6px;flex-shrink:0;"></div>
        <span style="font-size:10px;font-weight:700;padding:2px 9px;border-radius:20px;background:#f0fdf4;color:#0f766e;border:1px solid #bbf7d0;flex-shrink:0;">Fixed</span>
        @endif

        <span style="font-size:16px;flex-shrink:0;">{{ $sec['icon'] }}</span>
        <span style="font-size:13px;font-weight:700;color:#0f172a;flex:1;min-width:0;">{{ $sec['label'] }}</span>

        @if($sec['pinned'])
        <span style="font-size:11px;color:#94a3b8;font-style:italic;white-space:nowrap;">Edit in Hero tab</span>
        @else
        <svg id="chv-{{ $nk }}" width="14" height="14" fill="none" stroke="#94a3b8" stroke-width="2.5" viewBox="0 0 24 24" style="transition:transform .2s;flex-shrink:0;"><path d="M6 9l6 6 6-6"/></svg>
        @endif
    </div>

    @if(!$sec['pinned'])
    <div id="bdy-{{ $nk }}" style="display:none;border-top:1px solid #e2e8f0;padding:22px 24px;">

    @if($secKey === 'stats')
    <form method="POST" action="{{ route('admin.customizer.update-sections') }}">
    @csrf
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:18px;">
        @foreach([1=>'Church Members',2=>'Events Held',3=>'Blog Posts',4=>'Years of Grace'] as $sn=>$sdef)
        <div style="background:#f8fafc;border-radius:9px;padding:13px;border:1px solid #e2e8f0;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#64748b;margin-bottom:9px;">Stat {{ $sn }}</div>
            <label class="flabel">Value</label>
            <input type="text" name="stat_{{ $sn }}_value" class="finput" style="margin-bottom:7px;"
                   value="{{ old('stat_'.$sn.'_value',\App\Helpers\Settings::get('stat_'.$sn.'_value','')) }}" placeholder="e.g. 200+">
            <label class="flabel">Label</label>
            <input type="text" name="stat_{{ $sn }}_label" class="finput"
                   value="{{ old('stat_'.$sn.'_label',\App\Helpers\Settings::get('stat_'.$sn.'_label',$sdef)) }}" placeholder="{{ $sdef }}">
        </div>
        @endforeach
    </div>
    <div style="text-align:right;"><button type="submit" class="save-btn">Save Statistics Bar</button></div>
    </form>

    @elseif($secKey === 'story-video')
    <form method="POST" action="{{ route('admin.customizer.update-sections') }}">
    @csrf
    <div class="g2 mb14">
        <div>
            <label class="flabel">Story Video YouTube URL</label>
            <input type="text" name="story_video_url" class="finput"
                   value="{{ old('story_video_url',\App\Helpers\Settings::get('story_video_url','')) }}" placeholder="https://youtu.be/...">
            <p style="font-size:11px;color:#94a3b8;margin:4px 0 0;">Falls back to Hero YouTube URL if empty.</p>
        </div>
        <div>
            <label class="flabel">Overlay Title (optional)</label>
            <input type="text" name="story_video_title" class="finput" maxlength="150"
                   value="{{ old('story_video_title',\App\Helpers\Settings::get('story_video_title','')) }}" placeholder="e.g. Community Highlights">
        </div>
    </div>
    <div class="mb18">
        <label class="flabel">Overlay Subtitle (optional)</label>
        <input type="text" name="story_video_subtitle" class="finput" maxlength="300"
               value="{{ old('story_video_subtitle',\App\Helpers\Settings::get('story_video_subtitle','')) }}">
    </div>
    <div style="text-align:right;"><button type="submit" class="save-btn">Save Story Video</button></div>
    </form>

    @elseif($secKey === 'about')
    <form method="POST" action="{{ route('admin.customizer.update-sections') }}">
    @csrf
    <div class="g2 mb14">
        <div>
            <label class="flabel">Section Label (small text above heading)</label>
            <input type="text" name="about_section_label" class="finput" maxlength="80"
                   value="{{ old('about_section_label',\App\Helpers\Settings::get('about_section_label','Who We Are')) }}">
        </div>
        <div>
            <label class="flabel">Section Heading</label>
            <input type="text" name="about_section_title" class="finput" maxlength="200"
                   value="{{ old('about_section_title',\App\Helpers\Settings::get('about_section_title','A Church Rooted in Faith & Community')) }}">
        </div>
    </div>
    <div class="mb14">
        <label class="flabel">About Preview Text</label>
        <textarea name="about_preview" class="fta" rows="3" maxlength="1000">{{ old('about_preview',\App\Helpers\Settings::get('about_preview','We are a vibrant community committed to spreading love and serving our neighbours.')) }}</textarea>
    </div>
    <div class="g2 mb18">
        <div>
            <label class="flabel">Button Text</label>
            <input type="text" name="about_btn_text" class="finput" maxlength="80"
                   value="{{ old('about_btn_text',\App\Helpers\Settings::get('about_btn_text','Our Story')) }}">
        </div>
        <div>
            <label class="flabel">Button Link</label>
            <input type="text" name="about_btn_link" class="finput" maxlength="200"
                   value="{{ old('about_btn_link',\App\Helpers\Settings::get('about_btn_link','/about')) }}">
        </div>
    </div>
    <div style="text-align:right;"><button type="submit" class="save-btn">Save About Section</button></div>
    </form>

    @elseif($secKey === 'story')
    <form method="POST" action="{{ route('admin.customizer.update-story') }}" enctype="multipart/form-data">
    @csrf
    <div style="font-size:12px;font-weight:700;color:#374151;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid #f1f5f9;">Leader / Story Leader</div>
    <div class="g2 mb14">
        <div>
            <label class="flabel">Name</label>
            <input type="text" name="leader_name" class="finput" maxlength="150"
                   value="{{ old('leader_name',\App\Helpers\Settings::get('leader_name','')) }}">
        </div>
        <div>
            <label class="flabel">Title / Role</label>
            <input type="text" name="leader_title" class="finput" maxlength="150"
                   value="{{ old('leader_title',\App\Helpers\Settings::get('leader_title','Community Leader')) }}">
        </div>
    </div>
    <div class="mb14">
        <label class="flabel">Bio / Description</label>
        <textarea name="leader_description" class="fta" rows="4" maxlength="1000">{{ old('leader_description',\App\Helpers\Settings::get('leader_description','')) }}</textarea>
    </div>
    <div class="mb20">
        <label class="flabel">Photo</label>
        <div style="display:flex;align-items:flex-start;gap:14px;flex-wrap:wrap;">
            @if(\App\Helpers\Settings::get('leader_photo'))
            <img src="{{ \App\Helpers\Settings::storageUrl(\App\Helpers\Settings::get('leader_photo')) }}" alt="Leader"
                 style="width:72px;height:90px;object-fit:cover;border-radius:9px;border:2px solid #e2e8f0;flex-shrink:0;">
            @endif
            <div style="flex:1;min-width:160px;border:2px dashed #e2e8f0;border-radius:9px;padding:13px;text-align:center;cursor:pointer;"
                 onclick="document.getElementById('chief-photo-input').click()"
                 onmouseover="this.style.borderColor='#14b8a6'" onmouseout="this.style.borderColor='#e2e8f0'">
                <div id="chief-photo-label" style="font-size:12px;color:#64748b;">Click to upload photo (max 3 MB)</div>
            </div>
            <input type="file" name="leader_photo" id="chief-photo-input" accept="image/*" style="display:none;"
                   onchange="document.getElementById('chief-photo-label').textContent=this.files[0]?.name||'Click to upload'">
        </div>
    </div>
    <div style="font-size:12px;font-weight:700;color:#374151;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid #f1f5f9;">Our Story</div>
    <div class="mb14">
        <label class="flabel">Story Section Title</label>
        <input type="text" name="story_title" class="finput" maxlength="200"
               value="{{ old('story_title',\App\Helpers\Settings::get('story_title','Our Story')) }}">
    </div>
    <div class="mb18">
        <label class="flabel">Our Story Content</label>
        <div style="font-size:11px;color:#64748b;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:7px 11px;margin-bottom:6px;">
            💡 Each line break becomes a new paragraph. First letter is styled as a dropcap.
        </div>
        <textarea name="story_content" class="fta" rows="10" maxlength="5000" style="font-family:Georgia,serif;line-height:1.7;">{{ old('story_content',\App\Helpers\Settings::get('story_content','')) }}</textarea>
        <div style="font-size:11px;color:#94a3b8;margin-top:4px;text-align:right;" id="story-char-count">0 / 5000</div>
    </div>
    <div style="text-align:right;"><button type="submit" class="save-btn">Save Story &amp; Leadership</button></div>
    </form>

    @elseif($secKey === 'programs')
    <form method="POST" action="{{ route('admin.customizer.update-sections') }}">
    @csrf
    <div class="mb14">
        <label class="flabel">Section Title</label>
        <input type="text" name="sec_title_programs" class="finput" maxlength="120"
               value="{{ old('sec_title_programs',\App\Helpers\Settings::get('sec_title_programs','Programmes — Story Social Activities')) }}">
    </div>
    <div class="mb18">
        <label class="flabel">Section Subtitle</label>
        <textarea name="sec_sub_programs" class="fta" rows="2" maxlength="400">{{ old('sec_sub_programs',\App\Helpers\Settings::get('sec_sub_programs','A glimpse of our social life, outreach programmes, and community events.')) }}</textarea>
    </div>
    <div class="mb18"><a href="{{ route('admin.gallery-folders.index') }}" class="manage-link">📸 Manage Programmes Albums →</a></div>
    <div style="text-align:right;"><button type="submit" class="save-btn">Save Programmes Section</button></div>
    </form>

    @elseif($secKey === 'map')
    <form method="POST" action="{{ route('admin.customizer.update-sections') }}">
    @csrf
    <div class="mb14">
        <label class="flabel">Section Title</label>
        <input type="text" name="sec_title_map" class="finput" maxlength="120"
               value="{{ old('sec_title_map',\App\Helpers\Settings::get('sec_title_map','Find Us')) }}">
    </div>
    <div class="mb14">
        <label class="flabel">Section Subtitle</label>
        <textarea name="map_section_subtitle" class="fta" rows="2" maxlength="300">{{ old('map_section_subtitle',\App\Helpers\Settings::get('map_section_subtitle','We are located in the heart of the community. Come visit us — you are always welcome.')) }}</textarea>
    </div>
    <div class="mb18">
        <label class="flabel">Google Maps Embed URL</label>
        <textarea name="map_embed_url" class="fta" rows="3" style="font-family:monospace;font-size:12px;">{{ old('map_embed_url',\App\Helpers\Settings::get('map_embed_url','')) }}</textarea>
        <p style="font-size:11px;color:#94a3b8;margin:4px 0 0;">Paste the full <code>src="..."</code> URL from Google Maps → Share → Embed a map.</p>
    </div>
    <div style="text-align:right;"><button type="submit" class="save-btn">Save Map Section</button></div>
    </form>

    @elseif($secKey === 'gallery')
    <form method="POST" action="{{ route('admin.customizer.update-sections') }}">
    @csrf
    <div class="g2 mb14">
        <div>
            <label class="flabel">Section Label</label>
            <input type="text" name="sec_label_gallery" class="finput" maxlength="80"
                   value="{{ old('sec_label_gallery',\App\Helpers\Settings::get('sec_label_gallery','Our Moments')) }}">
        </div>
        <div>
            <label class="flabel">Section Title</label>
            <input type="text" name="sec_title_gallery" class="finput" maxlength="120"
                   value="{{ old('sec_title_gallery',\App\Helpers\Settings::get('sec_title_gallery','Church Gallery')) }}">
        </div>
    </div>
    <div class="mb18">
        <label class="flabel">Section Subtitle</label>
        <input type="text" name="sec_sub_gallery" class="finput" maxlength="300"
               value="{{ old('sec_sub_gallery',\App\Helpers\Settings::get('sec_sub_gallery','')) }}">
    </div>
    <div class="mb18"><a href="{{ route('admin.gallery-folders.index') }}" class="manage-link">📂 Manage Gallery Albums →</a></div>
    <div style="text-align:right;"><button type="submit" class="save-btn">Save Gallery Section</button></div>
    </form>

    @elseif($secKey === 'events')
    <form method="POST" action="{{ route('admin.customizer.update-sections') }}">
    @csrf
    <div class="g2 mb14">
        <div>
            <label class="flabel">Section Label</label>
            <input type="text" name="sec_label_events" class="finput" maxlength="80"
                   value="{{ old('sec_label_events',\App\Helpers\Settings::get('sec_label_events',"What's Happening")) }}">
        </div>
        <div>
            <label class="flabel">Section Title</label>
            <input type="text" name="sec_title_events" class="finput" maxlength="120"
                   value="{{ old('sec_title_events',\App\Helpers\Settings::get('sec_title_events','Upcoming Events')) }}">
        </div>
    </div>
    <div class="mb18">
        <label class="flabel">Section Subtitle</label>
        <input type="text" name="sec_sub_events" class="finput" maxlength="300"
               value="{{ old('sec_sub_events',\App\Helpers\Settings::get('sec_sub_events','')) }}">
    </div>
    <div class="mb18"><a href="{{ route('admin.events.index') }}" class="manage-link">📅 Manage Events →</a></div>
    <div style="text-align:right;"><button type="submit" class="save-btn">Save Events Section</button></div>
    </form>

    @elseif($secKey === 'blog')
    <form method="POST" action="{{ route('admin.customizer.update-sections') }}">
    @csrf
    <div class="g2 mb14">
        <div>
            <label class="flabel">Section Label</label>
            <input type="text" name="sec_label_blog" class="finput" maxlength="80"
                   value="{{ old('sec_label_blog',\App\Helpers\Settings::get('sec_label_blog','From the Church')) }}">
        </div>
        <div>
            <label class="flabel">Section Title</label>
            <input type="text" name="sec_title_blog" class="finput" maxlength="120"
                   value="{{ old('sec_title_blog',\App\Helpers\Settings::get('sec_title_blog','Latest Blog Posts')) }}">
        </div>
    </div>
    <div class="mb18">
        <label class="flabel">Section Subtitle</label>
        <input type="text" name="sec_sub_blog" class="finput" maxlength="300"
               value="{{ old('sec_sub_blog',\App\Helpers\Settings::get('sec_sub_blog','')) }}">
    </div>
    <div class="mb18"><a href="{{ route('admin.blogs.index') }}" class="manage-link">✍️ Manage Blog Posts →</a></div>
    <div style="text-align:right;"><button type="submit" class="save-btn">Save Blog Section</button></div>
    </form>

    @elseif($secKey === 'videos')
    <form method="POST" action="{{ route('admin.customizer.update-sections') }}">
    @csrf
    <div class="mb14">
        <label class="flabel">Section Title</label>
        <input type="text" name="sec_title_videos" class="finput" maxlength="120"
               value="{{ old('sec_title_videos',\App\Helpers\Settings::get('sec_title_videos','Sermons & Videos')) }}">
    </div>
    <div class="mb18">
        <label class="flabel">Section Subtitle</label>
        <input type="text" name="sec_sub_videos" class="finput" maxlength="300"
               value="{{ old('sec_sub_videos',\App\Helpers\Settings::get('sec_sub_videos','Messages, choir worship, and church events')) }}">
    </div>
    <div class="mb18"><a href="{{ route('admin.videos.index') }}" class="manage-link">🎬 Manage Videos →</a></div>
    <div style="text-align:right;"><button type="submit" class="save-btn">Save Videos Section</button></div>
    </form>

    @elseif($secKey === 'location')
    <form method="POST" action="{{ route('admin.customizer.update-location') }}" id="reach-form">
    @csrf
    <div class="mb14">
        <label class="flabel">Google Maps URL (for "Open in Maps" button)</label>
        <input type="text" name="location_map_link" class="finput"
               value="{{ old('location_map_link',\App\Helpers\Settings::get('location_map_link','https://maps.app.goo.gl/XgUkAk4g9oHtpAMq8')) }}"
               placeholder="https://maps.app.goo.gl/...">
    </div>
    <div class="mb14">
        <label class="flabel">Google Maps Embed URL (for the map iframe)</label>
        <input type="text" name="location_embed_url" class="finput"
               value="{{ old('location_embed_url',\App\Helpers\Settings::get('location_embed_url','')) }}"
               placeholder="https://www.google.com/maps/embed?pb=...">
        <p style="font-size:11px;color:#94a3b8;margin:4px 0 0;">In Google Maps → Share → Embed a map → copy the <code>src</code> URL from the iframe code.</p>
    </div>
    <div class="mb14">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:9px;">
            <label class="flabel" style="margin:0;">🛣️ Route Steps</label>
            <button type="button" onclick="addRouteStep()" style="background:#f0fdf4;color:#0f766e;border:1px solid #bbf7d0;border-radius:7px;padding:5px 11px;font-size:12px;font-weight:700;cursor:pointer;">+ Add Stop</button>
        </div>
        <div id="route-steps-list" style="display:flex;flex-direction:column;gap:8px;"></div>
        <input type="hidden" name="location_route_steps" id="location_route_steps_input">
    </div>
    <div class="mb18">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:9px;">
            <label class="flabel" style="margin:0;">📍 Nearby Places</label>
            <button type="button" onclick="addNearbyPlace()" style="background:#f0fdf4;color:#0f766e;border:1px solid #bbf7d0;border-radius:7px;padding:5px 11px;font-size:12px;font-weight:700;cursor:pointer;">+ Add Place</button>
        </div>
        <div id="nearby-places-list" style="display:flex;flex-direction:column;gap:8px;"></div>
        <input type="hidden" name="location_nearby_places" id="location_nearby_places_input">
    </div>
    <div style="text-align:right;">
        <button type="submit" onclick="serializeLocationData()" class="save-btn">Save Location Section</button>
    </div>
    </form>

    @elseif($secKey === 'hall-of-fame')
    <form method="POST" action="{{ route('admin.customizer.update-sections') }}">
    @csrf
    <div class="mb14">
        <label class="flabel">Section Title</label>
        <input type="text" name="sec_title_hall_of_fame" class="finput" maxlength="120"
               value="{{ old('sec_title_hall_of_fame',\App\Helpers\Settings::get('sec_title_hall_of_fame','Hall of Fame')) }}">
    </div>
    <div class="mb18">
        <label class="flabel">Section Subtitle</label>
        <textarea name="sec_sub_hall_of_fame" class="fta" rows="2" maxlength="400">{{ old('sec_sub_hall_of_fame',\App\Helpers\Settings::get('sec_sub_hall_of_fame','Honouring the pioneers and trailblazers who shaped our community.')) }}</textarea>
    </div>
    <div class="mb18">
        <a href="{{ route('admin.hall-of-fame.index') }}" class="manage-link" style="background:#fef3c7;color:#92400e;border-color:#fde68a;">⭐ Manage Hall of Fame Entries →</a>
    </div>
    <div style="text-align:right;"><button type="submit" class="save-btn">Save Hall of Fame</button></div>
    </form>

    @elseif($secKey === 'cta')
    <form method="POST" action="{{ route('admin.customizer.update-sections') }}">
    @csrf
    <div class="g2 mb14">
        <div>
            <label class="flabel">Section Heading</label>
            <input type="text" name="sec_title_cta" class="finput" maxlength="120"
                   value="{{ old('sec_title_cta',\App\Helpers\Settings::get('sec_title_cta','Come Worship With Us')) }}">
        </div>
        <div>
            <label class="flabel">Contact Phone</label>
            <input type="text" name="contact_phone" class="finput" maxlength="50"
                   value="{{ old('contact_phone',\App\Helpers\Settings::get('contact_phone','')) }}">
        </div>
    </div>
    <div class="mb14">
        <label class="flabel">Subtitle / Body Text</label>
        <textarea name="sec_sub_cta" class="fta" rows="2" maxlength="400">{{ old('sec_sub_cta',\App\Helpers\Settings::get('sec_sub_cta','You are always welcome. Come and experience the warmth and fellowship of our community.')) }}</textarea>
    </div>
    <div class="mb14">
        <label class="flabel">Contact Address</label>
        <input type="text" name="contact_address" class="finput" maxlength="300"
               value="{{ old('contact_address',\App\Helpers\Settings::get('contact_address','')) }}">
    </div>
    <div class="g4 mb18">
        <div>
            <label class="flabel">Button 1 Text</label>
            <input type="text" name="cta_btn1_text" class="finput" maxlength="80"
                   value="{{ old('cta_btn1_text',\App\Helpers\Settings::get('cta_btn1_text','Learn About Us')) }}">
        </div>
        <div>
            <label class="flabel">Button 1 Link</label>
            <input type="text" name="cta_btn1_link" class="finput" maxlength="200"
                   value="{{ old('cta_btn1_link',\App\Helpers\Settings::get('cta_btn1_link','/about')) }}">
        </div>
        <div>
            <label class="flabel">Button 2 Text</label>
            <input type="text" name="cta_btn2_text" class="finput" maxlength="80"
                   value="{{ old('cta_btn2_text',\App\Helpers\Settings::get('cta_btn2_text','See Events')) }}">
        </div>
        <div>
            <label class="flabel">Button 2 Link</label>
            <input type="text" name="cta_btn2_link" class="finput" maxlength="200"
                   value="{{ old('cta_btn2_link',\App\Helpers\Settings::get('cta_btn2_link','/events')) }}">
        </div>
    </div>
    <div style="text-align:right;"><button type="submit" class="save-btn">Save CTA Section</button></div>
    </form>
    @endif

    </div>{{-- end body --}}
    @endif

</div>{{-- end card --}}
@endforeach

</div>{{-- end section-order-list --}}
</div>{{-- end tab-sections --}}

{{-- ═══════════════════════════════════════════════
     TAB 5 — 🔍 SEO
════════════════════════════════════════════════ --}}
<div id="tab-seo" style="display:none;">
    <form method="POST" action="{{ route('admin.customizer.update-seo') }}" enctype="multipart/form-data">
        @csrf
        <div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; padding:28px 32px; margin-bottom:20px;">

            <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">
                🔍 Global SEO Settings
            </div>
            <div style="font-size:12px; color:#64748b; margin-bottom:20px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px 14px; line-height:1.6;">
                These settings apply site-wide. Individual pages can override them. Changes here appear in search engine results, social shares, and browser tabs.
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Meta Title (Browser Tab &amp; Search Results)</label>
                <input type="text" name="seo_meta_title"
                       value="{{ old('seo_meta_title', \App\Helpers\Settings::get('seo_meta_title', \App\Helpers\Settings::get('site_name', 'My Community Website'))) }}"
                       style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                       maxlength="200" placeholder="My Community Website — Faith, Hope & Love"
                       oninput="updateSeoPreview()">
                <div style="font-size:11px; color:#94a3b8; margin-top:4px;">Recommended: 50–60 characters for best display in search results.</div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Meta Description</label>
                <textarea name="seo_meta_description" rows="3" maxlength="400"
                          style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; resize:vertical; box-sizing:border-box;"
                          placeholder="A vibrant community committed to faith, fellowship and service. Join us every week."
                          oninput="updateSeoPreview()">{{ old('seo_meta_description', \App\Helpers\Settings::get('seo_meta_description', '')) }}</textarea>
                <div style="font-size:11px; color:#94a3b8; margin-top:4px;">Recommended: 120–160 characters.</div>
            </div>

            {{-- Search result preview --}}
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 18px; margin-bottom:20px;">
                <div style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:10px;">Search Result Preview</div>
                <div id="seo-preview-title" style="font-size:16px; color:#1a0dab; font-weight:400; font-family:Arial,sans-serif; line-height:1.3;">{{ \App\Helpers\Settings::get('seo_meta_title', \App\Helpers\Settings::get('site_name', 'My Community Website')) }}</div>
                <div style="font-size:12px; color:#006621; font-family:Arial,sans-serif; margin:2px 0;">{{ url('/') }}</div>
                <div id="seo-preview-desc" style="font-size:13px; color:#545454; font-family:Arial,sans-serif; line-height:1.5;">{{ \App\Helpers\Settings::get('seo_meta_description', 'A vibrant community committed to faith, fellowship and service.') }}</div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:16px;">
                <div>
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Meta Keywords</label>
                    <input type="text" name="seo_meta_keywords"
                           value="{{ old('seo_meta_keywords', \App\Helpers\Settings::get('seo_meta_keywords', '')) }}"
                           style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                           maxlength="400" placeholder="community, blog, events, gallery, news">
                </div>
                <div>
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Robots Directive</label>
                    <select name="seo_robots" style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box; background:#fff;">
                        @foreach(['index, follow'=>'index, follow (default)', 'noindex, follow'=>'noindex, follow', 'index, nofollow'=>'index, nofollow', 'noindex, nofollow'=>'noindex, nofollow'] as $val => $lbl)
                        <option value="{{ $val }}" {{ \App\Helpers\Settings::get('seo_robots', 'index, follow') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-bottom:24px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Canonical URL <span style="font-weight:400; color:#94a3b8;">(optional)</span></label>
                <input type="url" name="seo_canonical_url"
                       value="{{ old('seo_canonical_url', \App\Helpers\Settings::get('seo_canonical_url', '')) }}"
                       style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                       maxlength="300" placeholder="https://yourdomain.com">
            </div>

            <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">Open Graph (Social Sharing)</div>
            <p style="font-size:12px; color:#64748b; margin:0 0 16px;">Controls how your site looks when shared on Facebook, WhatsApp, etc.</p>

            <div style="margin-bottom:16px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">OG Title</label>
                <input type="text" name="seo_og_title"
                       value="{{ old('seo_og_title', \App\Helpers\Settings::get('seo_og_title', '')) }}"
                       style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                       maxlength="200" placeholder="Leave blank to use Meta Title">
            </div>
            <div style="margin-bottom:16px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">OG Description</label>
                <textarea name="seo_og_description" rows="2" maxlength="400"
                          style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; resize:vertical; box-sizing:border-box;"
                          placeholder="Leave blank to use Meta Description">{{ old('seo_og_description', \App\Helpers\Settings::get('seo_og_description', '')) }}</textarea>
            </div>
            <div style="margin-bottom:24px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">OG Image (1200×630 recommended)</label>
                <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
                    @if(\App\Helpers\Settings::get('seo_og_image'))
                    <img src="{{ \App\Helpers\Settings::storageUrl(\App\Helpers\Settings::get('seo_og_image')) }}" alt="OG Image"
                         style="height:70px; border-radius:8px; border:1px solid #e2e8f0; object-fit:cover;">
                    @endif
                    <div style="flex:1; min-width:180px; border:2px dashed #e2e8f0; border-radius:10px; padding:14px; text-align:center; cursor:pointer;"
                         onclick="document.getElementById('og-image-input').click()"
                         onmouseover="this.style.borderColor='#14b8a6'" onmouseout="this.style.borderColor='#e2e8f0'">
                        <div id="og-image-label" style="font-size:12px; color:#64748b;">{{ \App\Helpers\Settings::get('seo_og_image') ? 'Click to replace OG image' : 'Click to upload OG image' }}</div>
                    </div>
                    <input type="file" name="seo_og_image" id="og-image-input" accept="image/*" style="display:none;"
                           onchange="document.getElementById('og-image-label').textContent=this.files[0]?.name||'Click to upload'">
                </div>
            </div>

            <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">Twitter / X Card</div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">
                <div>
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Twitter Title</label>
                    <input type="text" name="seo_twitter_title"
                           value="{{ old('seo_twitter_title', \App\Helpers\Settings::get('seo_twitter_title', '')) }}"
                           style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                           maxlength="200" placeholder="Leave blank to use OG Title">
                </div>
                <div>
                    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Twitter Description</label>
                    <input type="text" name="seo_twitter_desc"
                           value="{{ old('seo_twitter_desc', \App\Helpers\Settings::get('seo_twitter_desc', '')) }}"
                           style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                           maxlength="400" placeholder="Leave blank to use OG Description">
                </div>
            </div>

            <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">Schema.org</div>
            <div style="margin-bottom:24px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Organization Name (for Schema Markup)</label>
                <input type="text" name="seo_schema_org_name"
                       value="{{ old('seo_schema_org_name', \App\Helpers\Settings::get('seo_schema_org_name', '')) }}"
                       style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                       maxlength="200" placeholder="My Community Website">
            </div>

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:600; padding:11px 24px; border-radius:10px; border:none; cursor:pointer; box-shadow:0 4px 12px rgba(20,184,166,.3); display:flex; align-items:center; gap:8px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    Save SEO Settings
                </button>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
// ── Tab switching ─────────────────────────────────────────────────────────────
const tabs   = ['general','appearance','hero','sections','seo'];
const active = { bg: 'linear-gradient(135deg,#0f766e,#14b8a6)', color: '#fff' };
const idle   = { bg: 'transparent', color: '#64748b' };

function switchTab(name) {
    tabs.forEach(t => {
        document.getElementById('tab-' + t).style.display = t === name ? 'block' : 'none';
        const btn = document.getElementById('tab-btn-' + t);
        if (t === name) {
            btn.style.background = active.bg;
            btn.style.color      = active.color;
            btn.style.boxShadow  = '0 4px 12px rgba(20,184,166,.3)';
            btn.scrollIntoView({ behavior:'smooth', block:'nearest', inline:'center' });
        } else {
            btn.style.background = idle.bg;
            btn.style.color      = idle.color;
            btn.style.boxShadow  = 'none';
        }
    });
    try { sessionStorage.setItem('customizer_tab', name); } catch(e) {}
}

(function() {
    try {
        const saved = sessionStorage.getItem('customizer_tab');
        if (saved && tabs.includes(saved)) { switchTab(saved); return; }
    } catch(e) {}
    switchTab('general');
})();

// ── Color preview strip ───────────────────────────────────────────────────────
function syncPreview(name, elId) {
    const picker = document.querySelector('[name=' + name + ']');
    const prev   = document.getElementById(elId);
    if (!picker || !prev) return;
    function update() { prev.style.background = picker.value; }
    picker.addEventListener('input', update);
    document.getElementById(name + '_hex')?.addEventListener('input', update);
}
syncPreview('primary_color',   'preview-primary');
syncPreview('secondary_color', 'preview-secondary');
syncPreview('accent_color',    'preview-accent');

// ── Story story character counter ──────────────────────────────────────────
(function () {
    const ta = document.querySelector('[name=story_content]');
    const counter = document.getElementById('story-char-count');
    if (!ta || !counter) return;
    function update() { counter.textContent = ta.value.length + ' / 5000'; }
    ta.addEventListener('input', update);
    update();
})();

// ── Section card accordion ────────────────────────────────────────────────────
function toggleSecCard(key) {
    const body = document.getElementById('bdy-' + key);
    const chv  = document.getElementById('chv-' + key);
    if (!body) return;
    const open = body.style.display !== 'none';
    body.style.display = open ? 'none' : 'block';
    if (chv) chv.style.transform = open ? '' : 'rotate(180deg)';
}

// ── Section visibility AJAX toggle ───────────────────────────────────────────
function saveSecVis(key, checked) {
    const track  = document.getElementById('tot-' + key);
    const thumb  = document.getElementById('toh-' + key);
    const lbl    = document.getElementById('tol-' + key);
    const status = document.getElementById('order-save-status');
    if (track) track.style.background     = checked ? '#0f766e' : '#cbd5e1';
    if (thumb) thumb.style.transform      = checked ? 'translateX(16px)' : 'translateX(0)';
    if (lbl)   lbl.textContent            = checked ? 'On' : 'Off';
    if (status) { status.textContent = 'Saving...'; status.style.color = '#64748b'; }
    fetch('{{ route("admin.customizer.toggle-section") }}', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
        body: JSON.stringify({ key: key, value: checked ? 1 : 0 })
    }).then(r => r.json()).then(() => {
        if (status) { status.textContent = 'Saved!'; status.style.color = '#0f766e'; setTimeout(() => status.textContent = '', 2000); }
    }).catch(() => {
        if (status) { status.textContent = 'Error'; status.style.color = '#dc2626'; }
    });
}

// ── Section order drag-and-drop ───────────────────────────────────────────────
(function() {
    const list = document.getElementById('section-order-list');
    if (!list) return;
    let dragSrc = null;

    list.querySelectorAll('[data-sec]').forEach(function(card) {
        if (card.dataset.sec === 'hero') return;
        card.addEventListener('dragstart', function(e) {
            dragSrc = this;
            setTimeout(() => this.style.opacity = '.4', 0);
            e.dataTransfer.effectAllowed = 'move';
        });
        card.addEventListener('dragend', function() {
            this.style.opacity = '1';
            this.setAttribute('draggable', 'false');
            this.style.outline = '';
            saveOrder();
        });
        card.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            if (this !== dragSrc) this.style.outline = '2px dashed #14b8a6';
            return false;
        });
        card.addEventListener('dragleave', function() { this.style.outline = ''; });
        card.addEventListener('drop', function(e) {
            e.stopPropagation();
            this.style.outline = '';
            if (dragSrc && dragSrc !== this) {
                const all    = Array.from(list.querySelectorAll('[data-sec]'));
                const srcIdx = all.indexOf(dragSrc);
                const dstIdx = all.indexOf(this);
                if (srcIdx < dstIdx) list.insertBefore(dragSrc, this.nextSibling);
                else list.insertBefore(dragSrc, this);
            }
            return false;
        });
    });

    function saveOrder() {
        const order  = Array.from(list.querySelectorAll('[data-sec]')).map(el => el.dataset.sec);
        const status = document.getElementById('order-save-status');
        if (status) { status.textContent = 'Saving...'; status.style.color = '#64748b'; }
        fetch('{{ route("admin.customizer.update-section-order") }}', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            body: JSON.stringify({ order: JSON.stringify(order) })
        }).then(r => r.json()).then(() => {
            if (status) { status.textContent = 'Order saved!'; status.style.color = '#0f766e'; setTimeout(() => status.textContent = '', 2000); }
        });
    }
})();

// ── SEO live preview ──────────────────────────────────────────────────────────
function updateSeoPreview() {
    const t = document.querySelector('[name=seo_meta_title]');
    const d = document.querySelector('[name=seo_meta_description]');
    const pt = document.getElementById('seo-preview-title');
    const pd = document.getElementById('seo-preview-desc');
    if (t && pt) pt.textContent = t.value || 'My Community Website';
    if (d && pd) pd.textContent = d.value || '';
}

// ── Location: Route Steps & Nearby Places ───────────────────────────────────────
@php
$_routeSteps = json_decode(\App\Helpers\Settings::get('location_route_steps',''), true) ?: [
    ['place'=>'City Centre',     'note'=>'Starting Point',    'dist'=>'~10 km',  'icon'=>'🏙️', 'color'=>'#2d6a4f'],
    ['place'=>'Main Road',       'note'=>'Take the highway',  'dist'=>'~5 km',   'icon'=>'🚗',  'color'=>'#0369a1'],
    ['place'=>'Local Town',      'note'=>'Turn at the market','dist'=>'~2 km',   'icon'=>'🏘️', 'color'=>'#b45309'],
    ['place'=>'Our Church',      'note'=>'Final Destination', 'dist'=>'Arrived', 'icon'=>'⛪',  'color'=>'#166534'],
];
$_nearbyPlaces = json_decode(\App\Helpers\Settings::get('location_nearby_places',''), true) ?: [
    ['name'=>'Community Park',   'desc'=>'A great place to relax and enjoy nature.',       'link'=>'https://maps.google.com', 'icon'=>'🌳'],
    ['name'=>'Local Market',     'desc'=>'Fresh produce and local goods every weekend.',    'link'=>'https://maps.google.com', 'icon'=>'🛒'],
    ['name'=>'Town Hall',        'desc'=>'Community events and local government offices.',  'link'=>'https://maps.google.com', 'icon'=>'🏛️'],
    ['name'=>'Recreation Centre','desc'=>'Sports facilities and community activities.',     'link'=>'https://maps.google.com', 'icon'=>'🏃'],
];
@endphp
const defaultRouteSteps   = @json($_routeSteps);
const defaultNearbyPlaces = @json($_nearbyPlaces);

function stepRowHtml(s) {
    return `<div class="route-step-row" style="display:grid; grid-template-columns:60px 1fr 1fr 90px 80px 36px; gap:6px; align-items:center; background:#f8fafc; border-radius:10px; padding:8px 12px; border:1px solid #e2e8f0;">
        <input type="text" placeholder="Icon" value="${s.icon||''}" style="border:1px solid #e2e8f0; border-radius:6px; padding:5px 7px; font-size:14px; text-align:center; width:100%; box-sizing:border-box;" data-field="icon">
        <input type="text" placeholder="Place" value="${s.place||''}" style="border:1px solid #e2e8f0; border-radius:6px; padding:5px 7px; font-size:12px; width:100%; box-sizing:border-box;" data-field="place">
        <input type="text" placeholder="Note" value="${s.note||''}" style="border:1px solid #e2e8f0; border-radius:6px; padding:5px 7px; font-size:12px; width:100%; box-sizing:border-box;" data-field="note">
        <input type="text" placeholder="Distance" value="${s.dist||''}" style="border:1px solid #e2e8f0; border-radius:6px; padding:5px 7px; font-size:12px; width:100%; box-sizing:border-box;" data-field="dist">
        <input type="color" value="${s.color||'#2d6a4f'}" style="border:1px solid #e2e8f0; border-radius:6px; padding:1px; height:32px; width:100%; cursor:pointer;" data-field="color">
        <button type="button" onclick="this.closest('.route-step-row').remove()" style="background:#fee2e2; color:#dc2626; border:none; border-radius:6px; padding:5px 8px; cursor:pointer; font-size:14px; font-weight:700;">✕</button>
    </div>`;
}

function placeRowHtml(p) {
    return `<div class="nearby-place-row" style="display:grid; grid-template-columns:60px 1fr 2fr 1fr 36px; gap:6px; align-items:center; background:#f8fafc; border-radius:10px; padding:8px 12px; border:1px solid #e2e8f0;">
        <input type="text" placeholder="Icon" value="${p.icon||''}" style="border:1px solid #e2e8f0; border-radius:6px; padding:5px 7px; font-size:14px; text-align:center; width:100%; box-sizing:border-box;" data-field="icon">
        <input type="text" placeholder="Place name" value="${p.name||''}" style="border:1px solid #e2e8f0; border-radius:6px; padding:5px 7px; font-size:12px; width:100%; box-sizing:border-box;" data-field="name">
        <input type="text" placeholder="Description" value="${p.desc||''}" style="border:1px solid #e2e8f0; border-radius:6px; padding:5px 7px; font-size:12px; width:100%; box-sizing:border-box;" data-field="desc">
        <input type="text" placeholder="Google Maps link" value="${p.link||''}" style="border:1px solid #e2e8f0; border-radius:6px; padding:5px 7px; font-size:11px; width:100%; box-sizing:border-box;" data-field="link">
        <button type="button" onclick="this.closest('.nearby-place-row').remove()" style="background:#fee2e2; color:#dc2626; border:none; border-radius:6px; padding:5px 8px; cursor:pointer; font-size:14px; font-weight:700;">✕</button>
    </div>`;
}

function addRouteStep()  { document.getElementById('route-steps-list').insertAdjacentHTML('beforeend', stepRowHtml({icon:'📍',place:'',note:'',dist:'',color:'#2d6a4f'})); }
function addNearbyPlace(){ document.getElementById('nearby-places-list').insertAdjacentHTML('beforeend', placeRowHtml({icon:'📍',name:'',desc:'',link:''})); }

function serializeLocationData() {
    const steps = Array.from(document.querySelectorAll('.route-step-row')).map(row => ({
        icon:  row.querySelector('[data-field=icon]').value,
        place: row.querySelector('[data-field=place]').value,
        note:  row.querySelector('[data-field=note]').value,
        dist:  row.querySelector('[data-field=dist]').value,
        color: row.querySelector('[data-field=color]').value,
    }));
    const places = Array.from(document.querySelectorAll('.nearby-place-row')).map(row => ({
        icon: row.querySelector('[data-field=icon]').value,
        name: row.querySelector('[data-field=name]').value,
        desc: row.querySelector('[data-field=desc]').value,
        link: row.querySelector('[data-field=link]').value,
    }));
    document.getElementById('location_route_steps_input').value  = JSON.stringify(steps);
    document.getElementById('location_nearby_places_input').value = JSON.stringify(places);
}

(function() {
    const stepsList  = document.getElementById('route-steps-list');
    const placesList = document.getElementById('nearby-places-list');
    if (!stepsList || !placesList) return;
    defaultRouteSteps.forEach(s   => stepsList.insertAdjacentHTML('beforeend', stepRowHtml(s)));
    defaultNearbyPlaces.forEach(p => placesList.insertAdjacentHTML('beforeend', placeRowHtml(p)));
})();

// ── Slide image preview ───────────────────────────────────────────────────────
function previewSlideImage(input) {
    const label   = document.getElementById('slide-image-label');
    const preview = document.getElementById('slide-image-preview');
    const img     = document.getElementById('slide-preview-img');
    if (input.files && input.files[0]) {
        label.textContent = input.files[0].name;
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
