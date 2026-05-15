<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Dashboard') — {{ \App\Helpers\Settings::get('site_name', 'Ehlom CMS') }} Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
        .sidebar-link { display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:8px; font-size:13.5px; font-weight:500; color:#94a3b8; transition:all .15s ease; text-decoration:none; }
        .sidebar-link:hover { background:rgba(255,255,255,.08); color:#fff; }
        .sidebar-link.active { background:linear-gradient(135deg,#0f766e,#0d9488); color:#fff; box-shadow:0 4px 12px rgba(13,148,136,.35); }
        .sidebar-link svg { width:18px; height:18px; shrink:0; }
        .stat-card { border-radius:14px; padding:20px 22px; display:flex; align-items:center; gap:16px; box-shadow:0 2px 12px rgba(0,0,0,.07); }
        .stat-icon { width:56px; height:56px; border-radius:14px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .stat-icon svg { width:26px; height:26px; }
        .badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:99px; font-size:11px; font-weight:600; }
        /* Bottom nav */
        .bottom-nav { display:none; }
        .bottom-nav-link { display:flex; flex-direction:column; align-items:center; gap:3px; padding:8px 0; font-size:10px; font-weight:600; color:#94a3b8; text-decoration:none; flex:1; border-top:2px solid transparent; transition:all .15s; }
        .bottom-nav-link svg { width:22px; height:22px; }
        .bottom-nav-link.active { color:#0f766e; border-top-color:#0f766e; }
        .bottom-nav-link:hover { color:#0f766e; }
        @media (max-width: 767px) {
            #sidebar { transform: translateX(-100%); position:fixed; top:0; left:0; height:100vh; z-index:50; }
            #sidebar.open { transform: translateX(0); }
            .bottom-nav { display:flex; }
            .main-content { padding-bottom: 70px !important; }
        }
    </style>
    @stack('styles')
</head>
<body style="background:#f0f4f8; margin:0;">

<div style="display:flex; height:100vh; overflow:hidden;">

    {{-- ═══════════ SIDEBAR ═══════════ --}}
    <aside id="sidebar" style="width:240px; background:linear-gradient(180deg,#0f172a 0%,#134e4a 100%); display:flex; flex-direction:column; flex-shrink:0; overflow-y:auto; transition:transform .25s ease; z-index:50;">

        {{-- Brand --}}
        <div style="padding:22px 20px 16px; border-bottom:1px solid rgba(255,255,255,.07);">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:40px;height:40px;background:linear-gradient(135deg,#14b8a6,#0d9488);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg width="20" height="20" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/>
                    </svg>
                </div>
                <div>
                    <div style="color:#fff;font-weight:700;font-size:13px;line-height:1.3;">{{ \App\Helpers\Settings::get('site_name', 'Ehlom CMS') }}</div>
                    <div style="color:#5eead4;font-size:11px;">Management System</div>
                </div>
            </div>
        </div>

        {{-- User badge --}}
        <div style="padding:14px 16px; border-bottom:1px solid rgba(255,255,255,.07);">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:34px;height:34px;background:linear-gradient(135deg,#14b8a6,#0f766e);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff;flex-shrink:0;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div style="color:#e2e8f0;font-size:12.5px;font-weight:600;">{{ Auth::user()->name }}</div>
                    <div style="display:flex;align-items:center;gap:5px;">
                        <span style="width:7px;height:7px;background:#22c55e;border-radius:50%;display:inline-block;"></span>
                        <span style="color:#94a3b8;font-size:11px;text-transform:capitalize;">{{ Auth::user()->getRoleNames()->first() ?? 'user' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Nav --}}
        <nav style="flex:1; padding:14px 12px; display:flex; flex-direction:column; gap:2px;">

            <div style="color:#475569;font-size:10px;font-weight:700;letter-spacing:.08em;padding:8px 6px 4px;text-transform:uppercase;">Main</div>

            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Dashboard
            </a>

            <a href="{{ route('admin.members.index') }}" class="sidebar-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-2-3.46"/></svg>
                Members
            </a>

            <a href="{{ route('admin.folders.index') }}" class="sidebar-link {{ request()->routeIs('admin.folders.*') || request()->routeIs('admin.documents.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                Documents
            </a>

            <div style="color:#475569;font-size:10px;font-weight:700;letter-spacing:.08em;padding:12px 6px 4px;text-transform:uppercase;">Content</div>

            <a href="{{ route('admin.blogs.index') }}" class="sidebar-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V7a2 2 0 012-2h10l4 4v11a2 2 0 01-2 2z"/></svg>
                Blog Posts
            </a>

            <a href="{{ route('admin.events.index') }}" class="sidebar-link {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Events
            </a>

            @if(Auth::user()->hasRole('admin'))
            <a href="{{ route('admin.hall-of-fame.index') }}" class="sidebar-link {{ request()->routeIs('admin.hall-of-fame.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                Hall of Fame
            </a>
            @endif

            <a href="{{ route('admin.gallery-folders.index', ['type' => 'gallery']) }}" class="sidebar-link {{ request()->routeIs('admin.gallery-folders.*') && request()->get('type','gallery') === 'gallery' ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h3l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                Gallery Albums
            </a>

            <a href="{{ route('admin.gallery-folders.index', ['type' => 'programs']) }}" class="sidebar-link {{ request()->routeIs('admin.gallery-folders.*') && request()->get('type') === 'programs' ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h3l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                Programme Albums
            </a>

            <a href="{{ route('admin.videos.index') }}" class="sidebar-link {{ request()->routeIs('admin.videos.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.069A1 1 0 0121 8.868V15.13a1 1 0 01-1.447.897L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Videos
            </a>

            @if(Auth::user()->hasRole('admin'))
            <div style="color:#475569;font-size:10px;font-weight:700;letter-spacing:.08em;padding:12px 6px 4px;text-transform:uppercase;">Admin</div>

            <a href="{{ route('admin.customizer.index') }}" class="sidebar-link {{ request()->routeIs('admin.customizer.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                Site Customizer
            </a>

            <a href="{{ route('admin.pages.index') }}" class="sidebar-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Website Pages
            </a>

            <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
                Users
            </a>
            @endif

            <a href="{{ route('admin.activity-logs.index') }}" class="sidebar-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Activity Logs
            </a>
        </nav>

        {{-- Logout --}}
        <div style="padding:12px 16px; border-top:1px solid rgba(255,255,255,.07);">
            <a href="{{ route('home') }}" target="_blank" style="display:flex;align-items:center;gap:8px;color:#94a3b8;font-size:12px;text-decoration:none;padding:7px 10px;border-radius:7px;margin-bottom:4px;" onmouseover="this.style.background='rgba(255,255,255,.06)'" onmouseout="this.style.background='transparent'">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                View Public Site
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="display:flex;align-items:center;gap:8px;color:#f87171;font-size:12px;background:none;border:none;cursor:pointer;padding:7px 10px;border-radius:7px;width:100%;" onmouseover="this.style.background='rgba(248,113,113,.1)'" onmouseout="this.style.background='transparent'">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    {{-- Mobile overlay --}}
    <div id="mob-overlay" onclick="closeSidebar()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:40;"></div>

    {{-- ═══════════ MAIN ═══════════ --}}
    <div style="flex:1; display:flex; flex-direction:column; overflow:hidden;">

        {{-- Top bar --}}
        <header style="background:#fff; border-bottom:1px solid #e2e8f0; height:60px; display:flex; align-items:center; justify-content:space-between; padding:0 16px; flex-shrink:0; box-shadow:0 1px 4px rgba(0,0,0,.04);">
            <div style="display:flex;align-items:center;gap:12px;">
                <button onclick="toggleSidebar()" style="background:none;border:none;cursor:pointer;padding:6px;border-radius:6px;display:flex;align-items:center;color:#64748b;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <div style="font-size:15px;font-weight:700;color:#0f172a;">@yield('page-title', 'Dashboard')</div>
                    <div style="font-size:10px;color:#94a3b8;" class="hidden sm:block">{{ \App\Helpers\Settings::get('site_name', 'Ehlom CMS') }} Management</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="font-size:11px;color:#64748b;" class="hidden sm:block">{{ now()->format('d M Y') }}</span>
                <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:8px;text-decoration:none;background:#f8fafc;border:1px solid #e2e8f0;padding:6px 10px;border-radius:8px;">
                    <div style="width:26px;height:26px;background:linear-gradient(135deg,#14b8a6,#0f766e);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span style="font-size:12px;font-weight:600;color:#334155;" class="hidden sm:block">{{ Auth::user()->name }}</span>
                </a>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success') || session('error'))
        <div style="padding:10px 16px 0;">
            @if(session('success'))
            <div style="display:flex;align-items:center;gap:10px;background:#f0fdf4;border:1px solid #86efac;color:#166534;border-radius:10px;padding:11px 14px;font-size:13px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div style="display:flex;align-items:center;gap:10px;background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;border-radius:10px;padding:11px 14px;font-size:13px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
            @endif
        </div>
        @endif

        {{-- Page content --}}
        <main class="main-content" style="flex:1; overflow-y:auto; padding:20px 16px;">
            @yield('content')
        </main>
    </div>
</div>

{{-- ═══════════ MOBILE BOTTOM NAV ═══════════ --}}
<nav class="bottom-nav" style="position:fixed; bottom:0; left:0; right:0; background:#fff; border-top:1px solid #e2e8f0; z-index:30; box-shadow:0 -2px 12px rgba(0,0,0,.08);">
    <a href="{{ route('admin.dashboard') }}" class="bottom-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        Home
    </a>
    <a href="{{ route('admin.members.index') }}" class="bottom-nav-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        Members
    </a>
    <a href="{{ route('admin.folders.index') }}" class="bottom-nav-link {{ request()->routeIs('admin.folders.*') || request()->routeIs('admin.documents.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
        Docs
    </a>
    <a href="{{ route('admin.gallery.index') }}" class="bottom-nav-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Gallery
    </a>
    <a href="{{ route('admin.blogs.index') }}" class="bottom-nav-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V7a2 2 0 012-2h10l4 4v11a2 2 0 01-2 2z"/></svg>
        Blog
    </a>
</nav>

<script>
function toggleSidebar() {
    const s = document.getElementById('sidebar');
    const o = document.getElementById('mob-overlay');
    if (window.innerWidth < 768) {
        const isOpen = s.classList.contains('open');
        if (isOpen) {
            s.classList.remove('open');
            o.style.display = 'none';
        } else {
            s.classList.add('open');
            o.style.display = 'block';
        }
    } else {
        // Desktop: toggle sidebar visibility
        const hidden = s.style.display === 'none';
        s.style.display = hidden ? 'flex' : 'none';
    }
}
function closeSidebar() {
    const s = document.getElementById('sidebar');
    const o = document.getElementById('mob-overlay');
    s.classList.remove('open');
    o.style.display = 'none';
}
</script>
@stack('scripts')
</body>
</html>
