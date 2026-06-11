<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('page-title', 'Dashboard') — {{ \App\Helpers\Settings::get('site_name', 'Ehlom CMS') }} Admin</title>
    @php
        $adminFavicon = \App\Helpers\Settings::get('favicon')
            ? \App\Helpers\Settings::storageUrl(\App\Helpers\Settings::get('favicon'))
            : asset('images/icon-192.svg');
    @endphp
    <link rel="icon" href="{{ $adminFavicon }}">
    <link rel="apple-touch-icon" href="{{ $adminFavicon }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,sans-serif; }
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
        /* Sidebar section labels */
        .nav-section-label { color:#94a3b8; font-size:10px; font-weight:800; letter-spacing:.1em; padding:6px 6px 3px; text-transform:uppercase; }
        /* Collapsible nav groups */
        .nav-group { border:none; }
        .nav-group summary { list-style:none; cursor:pointer; display:flex; align-items:center; padding:5px 6px; border-radius:7px; user-select:none; }
        .nav-group summary::-webkit-details-marker { display:none; }
        .nav-group summary:hover { background:rgba(255,255,255,.07); }
        .nav-chevron { width:13px; height:13px; color:#94a3b8; transition:transform .2s; flex-shrink:0; }
        details[open] > summary .nav-chevron { transform:rotate(180deg); }
        .nav-group-summary { list-style:none; cursor:pointer; display:flex; align-items:center; padding:5px 6px; border-radius:7px; }
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
        /* ── Global responsive utilities ── */
        .resp-table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
        .resp-table-wrap table { min-width:max-content; }
        .resp-flex { display:flex; flex-wrap:wrap; gap:8px; }
        .resp-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:12px; }
        .resp-stack { display:flex; flex-direction:column; gap:8px; }
        @media (max-width: 640px) {
            .resp-full { width:100% !important; }
            .resp-hide-sm { display:none !important; }
            .resp-stack-sm { grid-template-columns:1fr !important; }
            .main-content { padding:16px 10px !important; }
            .dash-event-side { grid-template-columns:1fr !important; }
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
        <nav style="flex:1; padding:10px 10px 16px; display:flex; flex-direction:column; gap:1px; overflow-y:auto;">
            @php
                $teacherOnly = Auth::user()->isTeacherOnly();
                $isAdmin     = Auth::user()->hasRole('admin');
                $isTeacher   = Auth::user()->hasRole('teacher');
                $activeYear  = \App\Models\AcademicYear::current();

                $pendingAttendance = $activeYear
                    ? \App\Models\AttendanceRecord::forActiveYear()->where('approval_status', 'pending')->count()
                    : 0;
                $pendingQuestions = $activeYear
                    ? \App\Models\ExamQuestion::where('academic_year_id', $activeYear->id)->where('status', 'pending')->count()
                    : 0;
                $pendingMarks = $activeYear
                    ? \App\Models\Mark::where('academic_year_id', $activeYear->id)
                        ->whereNotNull('submitted_at')->whereNull('approved_at')->count()
                    : 0;

                $academicActive = request()->routeIs('admin.academic-years.*','admin.students.*','admin.classes.*','admin.subjects.*','admin.class-subjects.*','admin.folders.*','admin.documents.*');
                $staffActive    = request()->routeIs('admin.teachers.*','admin.teacher-assignments.*');
                $gradebookActive= request()->routeIs('admin.attendance.*','admin.exams.*','admin.marks.*','admin.questions.*','admin.notes.*');
                $settingsActive = request()->routeIs('admin.grade-scales.*','admin.users.*','admin.activity-logs.*');
                $websiteActive  = request()->routeIs('admin.customizer.*','admin.blogs.*','admin.events.*','admin.hall-of-fame.*','admin.gallery-folders.*','admin.videos.*','admin.downloads.*','admin.important-links.*','admin.pages.*','admin.inquiries.*');
            @endphp

            {{-- ═════ DASHBOARD ═════ --}}
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" style="margin-bottom:4px;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Dashboard
            </a>

            {{-- ═════ ACADEMIC ═════ --}}
            @unless($teacherOnly)
            <details class="nav-group" {{ $academicActive ? 'open' : 'open' }}>
                <summary class="nav-group-summary">
                    <span class="nav-section-label" style="margin:0;flex:1;">Academic</span>
                    <svg class="nav-chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div style="padding-top:2px;">
                    @if($isAdmin)
                    <a href="{{ route('admin.academic-years.index') }}" class="sidebar-link {{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Academic Years
                    </a>
                    @endif
                    <a href="{{ route('admin.students.index') }}" class="sidebar-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-2-3.46"/></svg>
                        Students
                    </a>
                    <a href="{{ route('admin.classes.index') }}" class="sidebar-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.42A12 12 0 0112 21a12 12 0 01-6.16-10.42L12 14z"/></svg>
                        Classes &amp; Sections
                    </a>
                    @if($isAdmin)
                    <a href="{{ route('admin.subjects.index') }}" class="sidebar-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13M5.5 4.5h4.5a3 3 0 013 3v11.5a2.5 2.5 0 00-2.5-2.5H5.5v-12zM18.5 4.5h-4.5a3 3 0 00-3 3v11.5a2.5 2.5 0 012.5-2.5h5v-12z"/></svg>
                        Subjects
                    </a>
                    <a href="{{ route('admin.school-holidays.index') }}" class="sidebar-link {{ request()->routeIs('admin.school-holidays.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:18px;height:18px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Holidays
                    </a>
                    @endif
                    <a href="{{ route('admin.folders.index') }}" class="sidebar-link {{ request()->routeIs('admin.folders.*') || request()->routeIs('admin.documents.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                        Documents
                    </a>
                </div>
            </details>
            @endunless

            {{-- ═════ GRADEBOOK ═════ --}}
            @if($isAdmin)
            <details class="nav-group" {{ $gradebookActive ? 'open' : 'open' }}>
                <summary class="nav-group-summary">
                    <span class="nav-section-label" style="margin:0;flex:1;">Gradebook</span>
                    <svg class="nav-chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div style="padding-top:2px;">
                    <a href="{{ route('admin.attendance.index') }}" class="sidebar-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}" style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="display:flex;align-items:center;gap:10px;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:18px;height:18px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Attendance
                        </span>
                        @if($pendingAttendance > 0)
                            <span style="background:#fef3c7;color:#92400e;font-size:10px;font-weight:700;padding:1px 7px;border-radius:99px;">{{ $pendingAttendance }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.exams.index') }}" class="sidebar-link {{ request()->routeIs('admin.exams.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Exams &amp; Terms
                    </a>
                    <a href="{{ route('admin.marks.index') }}" class="sidebar-link {{ request()->routeIs('admin.marks.*') && !request()->routeIs('admin.marks.exam-summary') ? 'active' : '' }}" style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="display:flex;align-items:center;gap:10px;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:18px;height:18px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            Marks
                        </span>
                        @if($pendingMarks > 0)
                            <span style="background:#fef3c7;color:#92400e;font-size:10px;font-weight:700;padding:1px 7px;border-radius:99px;">{{ $pendingMarks }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.marks.exam-summary') }}" class="sidebar-link {{ request()->routeIs('admin.marks.exam-summary') ? 'active' : '' }}" style="display:flex;align-items:center;gap:10px;">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:18px;height:18px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Exam Summary
                    </a>
                    <a href="{{ route('admin.questions.index') }}" class="sidebar-link {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}" style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="display:flex;align-items:center;gap:10px;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:18px;height:18px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M5 8h14M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Questions
                        </span>
                        @if($pendingQuestions > 0)
                            <span style="background:#fef3c7;color:#92400e;font-size:10px;font-weight:700;padding:1px 7px;border-radius:99px;">{{ $pendingQuestions }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.notes.index') }}" class="sidebar-link {{ request()->routeIs('admin.notes.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Notes &amp; Assignments
                    </a>
                </div>
            </details>
            @endif

            {{-- ═════ STAFF ═════ --}}
            @if($isAdmin)
            <details class="nav-group" {{ $staffActive ? 'open' : 'open' }}>
                <summary class="nav-group-summary">
                    <span class="nav-section-label" style="margin:0;flex:1;">Staff</span>
                    <svg class="nav-chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div style="padding-top:2px;">
                    <a href="{{ route('admin.teachers.index') }}" class="sidebar-link {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
                        Teachers
                    </a>
                    <a href="{{ route('admin.teacher-assignments.index') }}" class="sidebar-link {{ request()->routeIs('admin.teacher-assignments.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Assignments
                    </a>
                </div>
            </details>
            @elseif($isTeacher)
            <div class="nav-section-label" style="margin-top:6px;">My Classes</div>
            <a href="{{ route('admin.classes.index') }}" class="sidebar-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.42A12 12 0 0112 21a12 12 0 01-6.16-10.42L12 14z"/></svg>
                My Classes
            </a>
            @endif

            {{-- ═════ SETTINGS ═════ --}}
            @if($isAdmin)
            <details class="nav-group" {{ $settingsActive ? 'open' : '' }}>
                <summary class="nav-group-summary">
                    <span class="nav-section-label" style="margin:0;flex:1;">Settings</span>
                    <svg class="nav-chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div style="padding-top:2px;">
                    <a href="{{ route('admin.grade-scales.index') }}" class="sidebar-link {{ request()->routeIs('admin.grade-scales.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                        Grade Scale
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
                        Users
                    </a>
                    <a href="{{ route('admin.activity-logs.index') }}" class="sidebar-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Activity Logs
                    </a>
                </div>
            </details>
            @endif

            {{-- ═════ WEBSITE ═════ --}}
            @unless($teacherOnly)
            <hr style="border:none;border-top:1px solid rgba(255,255,255,.1);margin:8px 0;">
            <details class="nav-group" {{ $websiteActive ? 'open' : '' }}>
                <summary class="nav-group-summary">
                    <span class="nav-section-label" style="margin:0;flex:1;">Website</span>
                    <svg class="nav-chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div style="padding-top:2px;">
                    @if($isAdmin)
                    <a href="{{ route('admin.customizer.index') }}" class="sidebar-link {{ request()->routeIs('admin.customizer.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        Site Customizer
                    </a>
                    @endif
                    <a href="{{ route('admin.blogs.index') }}" class="sidebar-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V7a2 2 0 012-2h10l4 4v11a2 2 0 01-2 2z"/></svg>
                        News &amp; Notices
                    </a>
                    <a href="{{ route('admin.events.index') }}" class="sidebar-link {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Events
                    </a>
                    @if($isAdmin)
                    <a href="{{ route('admin.hall-of-fame.index') }}" class="sidebar-link {{ request()->routeIs('admin.hall-of-fame.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        Hall of Fame
                    </a>
                    @endif
                    <a href="{{ route('admin.gallery-folders.index', ['type' => 'programs']) }}" class="sidebar-link {{ request()->routeIs('admin.gallery-folders.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h3l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                        Gallery / Albums
                    </a>
                    <a href="{{ route('admin.videos.index') }}" class="sidebar-link {{ request()->routeIs('admin.videos.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.069A1 1 0 0121 8.868V15.13a1 1 0 01-1.447.897L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Videos
                    </a>
                    <a href="{{ route('admin.downloads.index') }}" class="sidebar-link {{ request()->routeIs('admin.downloads.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Downloads
                    </a>
                    <a href="{{ route('admin.important-links.index') }}" class="sidebar-link {{ request()->routeIs('admin.important-links.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5m6.656-2.828a4 4 0 00-5.656 0l-3 3a4 4 0 000 5.656"/></svg>
                        Important Links
                    </a>
                    <a href="{{ route('admin.pages.index') }}" class="sidebar-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Website Pages
                    </a>
                    @php $newInquiries = \App\Models\Inquiry::where('status', 'new')->count(); @endphp
                    <a href="{{ route('admin.inquiries.index') }}" class="sidebar-link {{ request()->routeIs('admin.inquiries.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Inquiries
                        @if($newInquiries > 0)
                            <span style="margin-left:auto;background:#dc2626;color:#fff;border-radius:99px;font-size:10px;padding:1px 7px;font-weight:700;">{{ $newInquiries }}</span>
                        @endif
                    </a>
                </div>
            </details>
            @endunless

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
            <div style="display:flex;align-items:center;gap:12px;flex:1;">
                <button onclick="toggleSidebar()" style="background:none;border:none;cursor:pointer;padding:6px;border-radius:6px;display:flex;align-items:center;color:#64748b;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <div style="font-size:15px;font-weight:700;color:#0f172a;">@yield('page-title', 'Dashboard')</div>
                    <div style="font-size:10px;color:#94a3b8;" class="hidden sm:block">{{ \App\Helpers\Settings::get('site_name', 'Ehlom CMS') }} Management</div>
                </div>
                <div style="flex:1;max-width:320px;margin-left:20px;" class="resp-hide-sm">
                    <form method="GET" action="{{ route('admin.students.index') }}" style="display:flex;">
                        <input type="text" name="search" placeholder="Search students..." value="{{ request('search') }}" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 12px;font-size:13px;background:#f8fafc;outline:none;" onfocus="this.style.borderColor='#0f766e'" onblur="this.style.borderColor='#e2e8f0'">
                    </form>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                @php $allYears = \App\Models\AcademicYear::orderByDesc('id')->get(); @endphp
                @if($allYears->count() > 1)
                <form method="POST" action="{{ route('admin.working-year.switch') }}" style="margin:0;">
                    @csrf
                    <select name="year_id" onchange="this.form.submit()" style="font-size:11px;padding:4px 8px;border-radius:6px;border:1px solid #e2e8f0;background:#f8fafc;color:#334155;cursor:pointer;min-width:150px;">
                        @foreach($allYears as $yr)
                        <option value="{{ $yr->id }}" {{ ($workingYear->id ?? null) === $yr->id ? 'selected' : '' }}>
                            {{ $yr->name }} {{ $yr->is_active ? '(Active)' : '' }}
                        </option>
                        @endforeach
                    </select>
                </form>
                @endif
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

        {{-- Working year banner --}}
        <div style="padding:10px 16px 0;">
            @include('partials.working-year-banner')
        </div>

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
    @if(Auth::user()->hasAnyRole(['admin','teacher']))
    <a href="{{ route('admin.classes.index') }}" class="bottom-nav-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
        Classes
    </a>
    @endif
    @unless($teacherOnly)
    <a href="{{ route('admin.students.index') }}" class="bottom-nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        Students
    </a>
    <a href="{{ route('admin.folders.index') }}" class="bottom-nav-link {{ request()->routeIs('admin.folders.*') || request()->routeIs('admin.documents.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
        Docs
    </a>
    <a href="{{ route('admin.blogs.index') }}" class="bottom-nav-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V7a2 2 0 012-2h10l4 4v11a2 2 0 01-2 2z"/></svg>
        News
    </a>
    @endunless
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

<div id="confirmOverlay" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:9999;align-items:center;justify-content:center;" onclick="if(event.target===this)closeConfirm()">
    <div style="background:#fff;border-radius:14px;padding:28px 32px;max-width:400px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.25);text-align:center;">
        <div style="font-size:40px;margin-bottom:10px;">⚠️</div>
        <div id="confirmMessage" style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:4px;">Are you sure?</div>
        <div style="font-size:12px;color:#64748b;margin-bottom:20px;">This action cannot be undone.</div>
        <div style="display:flex;gap:10px;justify-content:center;">
            <button onclick="closeConfirm()" style="background:#f1f5f9;color:#475569;border:none;padding:9px 22px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Cancel</button>
            <button id="confirmOkBtn" onclick="confirmOk()" style="background:#dc2626;color:#fff;border:none;padding:9px 22px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Confirm</button>
        </div>
    </div>
</div>
<script>
let _confirmCb = null;
function customConfirm(message, cb, okText) {
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmOverlay').style.display = 'flex';
    if (okText) document.getElementById('confirmOkBtn').textContent = okText;
    else document.getElementById('confirmOkBtn').textContent = 'Confirm';
    _confirmCb = cb;
}
function confirmOk() {
    document.getElementById('confirmOverlay').style.display = 'none';
    if (_confirmCb) _confirmCb();
    _confirmCb = null;
}
function closeConfirm() {
    document.getElementById('confirmOverlay').style.display = 'none';
    _confirmCb = null;
}

// Only force a fresh fetch when the browser actually restored the page from
// the back-forward cache (e.g. iOS Safari ignoring Cache-Control: no-store).
// Do NOT reload on every back/forward navigation — that turns normal navigation
// into a flicker and surfaces stale flash messages.
window.addEventListener('pageshow', function (e) {
    if (e.persisted) window.location.reload();
});
</script>
@stack('scripts')
</body>
</html>
