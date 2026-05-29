<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f766e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Teacher Portal">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <title>@yield('page-title', 'Teacher') — {{ \App\Helpers\Settings::get('site_name', 'Ehlom CMS') }}</title>
    @php
        $favicon = \App\Helpers\Settings::get('favicon')
            ? \App\Helpers\Settings::storageUrl(\App\Helpers\Settings::get('favicon'))
            : asset('icon-192.svg');
    @endphp
    <link rel="icon" href="{{ $favicon }}">
    <link rel="apple-touch-icon" href="{{ $favicon }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *,*::before,*::after{box-sizing:border-box}
        body{margin:0;font-family:'Inter',sans-serif;background:#f0f4f8;-webkit-tap-highlight-color:transparent;-webkit-font-smoothing:antialiased}
        ::-webkit-scrollbar{width:4px;height:4px}
        ::-webkit-scrollbar-track{background:#f1f5f9}
        ::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:99px}

        .badge{display:inline-flex;align-items:center;padding:2px 10px;border-radius:99px;font-size:10px;font-weight:700;text-transform:uppercase}
        .card{background:#fff;border-radius:14px;box-shadow:0 1px 4px rgba(15,23,42,.06);overflow:hidden}
        .card-header{padding:14px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
        .card-title{font-size:14px;font-weight:700;color:#0f172a}
        .stat-card{background:#fff;border-radius:14px;padding:16px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 4px rgba(15,23,42,.06)}
        .stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
        .stat-icon svg{width:22px;height:22px}
        .mini-pill{display:inline-flex;align-items:center;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600}
        .tap-target{min-height:44px;display:flex;align-items:center}

        .sidebar-link{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:8px;font-size:13.5px;font-weight:500;color:#94a3b8;transition:all .15s;text-decoration:none}
        .sidebar-link:hover{background:rgba(255,255,255,.08);color:#fff}
        .sidebar-link.active{background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;box-shadow:0 4px 12px rgba(13,148,136,.35)}
        .sidebar-link svg{width:18px;height:18px}

        #sidebar{width:240px;background:linear-gradient(180deg,#0f172a,#134e4a);display:flex;flex-direction:column;flex-shrink:0;overflow-y:auto;position:fixed;top:0;left:0;height:100vh;z-index:50;transform:translateX(-100%);transition:transform .25s ease}
        #sidebar.open{transform:translateX(0)}

        .bottom-nav{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #e2e8f0;z-index:40;display:flex;box-shadow:0 -2px 12px rgba(0,0,0,.08);padding-bottom:env(safe-area-inset-bottom,0)}
        .bottom-nav-link{display:flex;flex-direction:column;align-items:center;gap:2px;padding:6px 0 4px;font-size:10px;font-weight:600;color:#94a3b8;text-decoration:none;flex:1;border-top:2px solid transparent;transition:all .12s;min-height:52px;justify-content:center}
        .bottom-nav-link svg{width:22px;height:22px;stroke:currentColor;fill:none;stroke-width:2}
        .bottom-nav-link.active{color:#0f766e;border-top-color:#0f766e}
        .bottom-nav-link.more-btn{background:none;border:none;cursor:pointer;font-family:inherit;padding:6px 0 4px;flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;font-size:10px;font-weight:600;color:#94a3b8;border-top:2px solid transparent;min-height:52px;justify-content:center}
        .bottom-nav-link.more-btn.active{color:#0f766e;border-top-color:#0f766e}

        .action-overlay{position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:60;opacity:0;pointer-events:none;transition:opacity .2s}
        .action-overlay.open{opacity:1;pointer-events:auto}
        .action-sheet{position:fixed;bottom:0;left:0;right:0;background:#fff;border-radius:16px 16px 0 0;z-index:61;transform:translateY(100%);transition:transform .3s cubic-bezier(.32,.72,0,1);box-shadow:0 -8px 40px rgba(0,0,0,.15);padding-bottom:env(safe-area-inset-bottom,0)}
        .action-sheet.open{transform:translateY(0)}
        .action-sheet-handle{width:36px;height:4px;background:#e2e8f0;border-radius:99px;margin:10px auto 4px}
        .action-sheet-item{display:flex;align-items:center;gap:14px;padding:14px 20px;font-size:15px;font-weight:600;color:#0f172a;text-decoration:none;border-bottom:1px solid #f8fafc;min-height:52px}
        .action-sheet-item:last-child{border-bottom:none}
        .action-sheet-item svg{width:22px;height:22px;flex-shrink:0;color:#64748b}
        .action-sheet-item.danger{color:#dc2626}
        .action-sheet-item.danger svg{color:#dc2626}

        .main-wrap{display:flex;min-height:100vh}
        .main-area{flex:1;display:flex;flex-direction:column;min-width:0}
        .top-bar{background:#fff;border-bottom:1px solid #e2e8f0;height:52px;display:flex;align-items:center;justify-content:space-between;padding:0 12px;flex-shrink:0;position:sticky;top:0;z-index:30}
        .top-bar-title{font-size:15px;font-weight:700;color:#0f172a;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .top-bar-sub{font-size:10px;color:#94a3b8}
        .main-content{flex:1;overflow-y:auto;padding:14px 12px 80px}

        @media (min-width:768px){
            #sidebar{transform:translateX(0)}
            .bottom-nav{display:none}
            .main-content{padding:20px 24px 20px 264px}
            .top-bar{padding:0 20px 0 264px}
            .top-bar{height:56px}
        }

        @media (max-width:767px){
            .main-content{padding:12px 12px 76px}
            .top-bar{height:48px}
            .top-bar-sub{display:none}
            .resp-hide-mob{display:none!important}
        }

        .flash{position:fixed;top:12px;left:12px;right:12px;z-index:100;pointer-events:none}
        .flash>div{pointer-events:auto;border-radius:12px;padding:12px 16px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:10px;box-shadow:0 4px 20px rgba(0,0,0,.12);animation:slideDown .3s ease}
        @keyframes slideDown{from{opacity:0;transform:translateY(-20px)}to{opacity:1;transform:translateY(0)}}
    </style>
    @stack('styles')
</head>
<body>

{{-- ═══ SIDEBAR (desktop) ═══ --}}
<aside id="sidebar">
    <div style="padding:22px 20px 16px;border-bottom:1px solid rgba(255,255,255,.07)">
        <div style="display:flex;align-items:center;gap:12px">
            <div style="width:40px;height:40px;background:linear-gradient(135deg,#14b8a6,#0d9488);border-radius:10px;display:flex;align-items:center;justify-content:center">
                <svg width="20" height="20" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.42A12 12 0 0112 21a12 12 0 01-6.16-10.42L12 14z"/></svg>
            </div>
            <div>
                <div style="color:#fff;font-weight:700;font-size:13px;line-height:1.3">{{ \App\Helpers\Settings::get('site_name', 'Ehlom CMS') }}</div>
                <div style="color:#5eead4;font-size:11px">Teacher Portal</div>
            </div>
        </div>
    </div>
    <div style="padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.07)">
        <div style="display:flex;align-items:center;gap:10px">
            <div style="width:34px;height:34px;background:linear-gradient(135deg,#14b8a6,#0f766e);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff;flex-shrink:0">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
            <div>
                <div style="color:#e2e8f0;font-size:12.5px;font-weight:600">{{ Auth::user()->name }}</div>
                <div style="display:flex;align-items:center;gap:5px">
                    <span style="width:7px;height:7px;background:#22c55e;border-radius:50%;display:inline-block"></span>
                    @php $y = \App\Models\AcademicYear::current(); @endphp
                    <span style="color:#94a3b8;font-size:11px">{{ $y ? $y->name : 'Teacher' }}</span>
                </div>
            </div>
        </div>
    </div>
    <nav style="flex:1;padding:14px 12px;display:flex;flex-direction:column;gap:2px">
        <div style="color:#475569;font-size:10px;font-weight:700;letter-spacing:.08em;padding:8px 6px 4px;text-transform:uppercase">Teacher</div>
        <a href="{{ route('teacher.dashboard') }}" class="sidebar-link {{ request()->routeIs('teacher.dashboard')?'active':'' }}"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Dashboard</a>
        <a href="{{ route('teacher.classes') }}" class="sidebar-link {{ request()->routeIs('teacher.classes')?'active':'' }}"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.42A12 12 0 0112 21a12 12 0 01-6.16-10.42L12 14z"/></svg>My Classes</a>
        <a href="{{ route('teacher.subjects') }}" class="sidebar-link {{ request()->routeIs('teacher.subjects')?'active':'' }}"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13M5.5 4.5h4.5a3 3 0 013 3v11.5a2.5 2.5 0 00-2.5-2.5H5.5v-12zM18.5 4.5h-4.5a3 3 0 00-3 3v11.5a2.5 2.5 0 012.5-2.5h5v-12z"/></svg>My Subjects</a>
        <div style="color:#475569;font-size:10px;font-weight:700;letter-spacing:.08em;padding:12px 6px 4px;text-transform:uppercase">Daily Work</div>
        <a href="{{ route('teacher.attendance.index') }}" class="sidebar-link {{ request()->routeIs('teacher.attendance.*')?'active':'' }}"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>Attendance</a>
        <a href="{{ route('teacher.marks.index') }}" class="sidebar-link {{ request()->routeIs('teacher.marks.*')?'active':'' }}"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>Marks Entry</a>
        <a href="{{ route('teacher.questions.index') }}" class="sidebar-link {{ request()->routeIs('teacher.questions.*')?'active':'' }}"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M5 8h14M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>Questions</a>
        <a href="{{ route('teacher.notes.index') }}" class="sidebar-link {{ request()->routeIs('teacher.notes.*')?'active':'' }}"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>Notes &amp; Assignments</a>
        @if(Auth::user()->hasRole('admin'))
        <div style="color:#475569;font-size:10px;font-weight:700;letter-spacing:.08em;padding:12px 6px 4px;text-transform:uppercase">Admin</div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>Switch to Admin</a>
        @endif
    </nav>
    <div style="padding:12px 16px;border-top:1px solid rgba(255,255,255,.07)">
        <a href="{{ route('home') }}" target="_blank" style="display:flex;align-items:center;gap:8px;color:#94a3b8;font-size:12px;text-decoration:none;padding:7px 10px;border-radius:7px;margin-bottom:4px" onmouseover="this.style.background='rgba(255,255,255,.06)'" onmouseout="this.style.background='transparent'"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>View Public Site</a>
        <form method="POST" action="{{ route('teacher.logout') }}">
            @csrf
            <button type="submit" style="display:flex;align-items:center;gap:8px;color:#f87171;font-size:12px;background:none;border:none;cursor:pointer;padding:7px 10px;border-radius:7px;width:100%" onmouseover="this.style.background='rgba(248,113,113,.1)'" onmouseout="this.style.background='transparent'"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>Sign Out</button>
        </form>
    </div>
</aside>
<div id="mob-overlay" onclick="closeSidebar()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:45"></div>

{{-- ═══ MAIN AREA ═══ --}}
<div class="main-wrap">
    <div class="main-area">

        {{-- Top bar --}}
        <header class="top-bar">
            <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0">
                <button onclick="toggleSidebar()" style="background:none;border:none;cursor:pointer;padding:6px;border-radius:6px;display:flex;align-items:center;color:#64748b;flex-shrink:0">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div style="min-width:0">
                    <div class="top-bar-title">@yield('page-title','Dashboard')</div>
                    <div class="top-bar-sub">{{ \App\Helpers\Settings::get('site_name','Ehlom CMS') }} · Teacher</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:6px">
                <span style="font-size:11px;color:#94a3b8" class="resp-hide-mob">{{ now()->format('d M') }}</span>
                <a href="{{ route('teacher.profile.edit') }}" style="display:flex;align-items:center;gap:6px;text-decoration:none;background:#f8fafc;border:1px solid #e2e8f0;padding:5px 8px;border-radius:8px">
                    <div style="width:26px;height:26px;background:linear-gradient(135deg,#14b8a6,#0f766e);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
                    <span style="font-size:12px;font-weight:600;color:#334155" class="resp-hide-mob">{{ Auth::user()->name }}</span>
                </a>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success')||session('error'))
        <div class="flash">
            @if(session('success'))<div style="background:#f0fdf4;border:1px solid #86efac;color:#166534">{{ session('success') }}</div>@endif
            @if(session('error'))<div style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b">{{ session('error') }}</div>@endif
        </div>
        @endif

        {{-- Page content --}}
        <main class="main-content">
            @yield('content')
        </main>
    </div>
</div>

{{-- ═══ MOBILE BOTTOM NAV ═══ --}}
@php
    $isHome = request()->routeIs('teacher.dashboard');
    $isClasses = request()->routeIs('teacher.classes')||request()->routeIs('teacher.subjects');
    $isMarks = request()->routeIs('teacher.marks.*');
    $isAttendance = request()->routeIs('teacher.attendance.*');
    $isOther = !$isHome && !$isClasses && !$isMarks && !$isAttendance;
@endphp

<nav class="bottom-nav" x-data="{more:false}" x-init="()=>{if($el)window.bottomNav=function(v){more=v}}">
    <a href="{{ route('teacher.dashboard') }}" class="bottom-nav-link {{ $isHome?'active':'' }}">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        Home
    </a>
    <a href="{{ route('teacher.classes') }}" class="bottom-nav-link {{ $isClasses?'active':'' }}">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.42A12 12 0 0112 21a12 12 0 01-6.16-10.42L12 14z"/></svg>
        Classes
    </a>
    <a href="{{ route('teacher.marks.index') }}" class="bottom-nav-link {{ $isMarks?'active':'' }}">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
        Marks
    </a>
    <a href="{{ route('teacher.attendance.index') }}" class="bottom-nav-link {{ $isAttendance?'active':'' }}">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        Attendance
    </a>
    <button class="bottom-nav-link more-btn" :class="{active:more||{{ $isOther?'true':'false' }}}" @click="more=true">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5" fill="currentColor"/><circle cx="12" cy="12" r="1.5" fill="currentColor"/><circle cx="12" cy="19" r="1.5" fill="currentColor"/></svg>
        More
    </button>
</nav>

{{-- ═══ ACTION SHEET ═══ --}}
<div class="action-overlay" :class="more?'open':''" @click="more=false"></div>
<div class="action-sheet" :class="more?'open':''" x-data x-show="more" x-cloak x-transition>
    <div class="action-sheet-handle"></div>
    <div style="padding:4px 0">
        <a href="{{ route('teacher.subjects') }}" class="action-sheet-item" @click="more=false">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13M5.5 4.5h4.5a3 3 0 013 3v11.5a2.5 2.5 0 00-2.5-2.5H5.5v-12zM18.5 4.5h-4.5a3 3 0 00-3 3v11.5a2.5 2.5 0 012.5-2.5h5v-12z"/></svg>
            My Subjects
        </a>
        <a href="{{ route('teacher.questions.index') }}" class="action-sheet-item" @click="more=false">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M5 8h14M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Questions
        </a>
        <a href="{{ route('teacher.notes.index') }}" class="action-sheet-item" @click="more=false">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Notes &amp; Assignments
        </a>
        <a href="{{ route('teacher.profile.edit') }}" class="action-sheet-item" @click="more=false">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Profile
        </a>
        @if(Auth::user()->hasRole('admin'))
        <a href="{{ route('admin.dashboard') }}" class="action-sheet-item" @click="more=false">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
            Switch to Admin
        </a>
        @endif
        <form method="POST" action="{{ route('teacher.logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="action-sheet-item danger" style="background:none;border:none;cursor:pointer;width:100%;text-align:left;font-family:inherit">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Sign Out
            </button>
        </form>
    </div>
</div>

{{-- ═══ SCRIPTS ═══ --}}
<script>
function toggleSidebar(){var s=document.getElementById('sidebar'),o=document.getElementById('mob-overlay');if(window.innerWidth<768){s.classList.toggle('open');o.style.display=s.classList.contains('open')?'block':'none'}else{s.style.display=s.style.display==='none'?'flex':'none'}}
function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('mob-overlay').style.display='none'}
if('serviceWorker'in navigator){window.addEventListener('load',function(){navigator.serviceWorker.register('/sw.js').then(function(r){console.log('SW registered:',r.scope)},function(e){console.log('SW failed:',e)})})}
</script>

@stack('scripts')
</body>
</html>
