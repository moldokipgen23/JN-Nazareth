@php
    $logo     = \App\Helpers\Settings::get('logo');
    $siteName = \App\Helpers\Settings::get('site_name', 'Ehlom CMS');
@endphp
@if($logo)
    <img src="{{ \App\Helpers\Settings::storageUrl($logo) }}" alt="{{ $siteName }}" style="height:36px; width:auto; object-fit:contain;">
@else
    <div style="display:flex; align-items:center; gap:8px;">
        <div style="width:32px; height:32px; background:linear-gradient(135deg,#14b8a6,#0d9488); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/>
            </svg>
        </div>
        <span style="font-size:14px; font-weight:700; color:#0f172a; white-space:nowrap;">{{ $siteName }}</span>
    </div>
@endif
