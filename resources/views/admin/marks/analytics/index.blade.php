@php use App\Models\GradeScale; @endphp
@extends('layouts.admin')

@section('title', 'Marks Analytics')

@push('head')
<style>
    .filter-bar { display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap; margin-bottom:24px; padding:16px 20px; background:#fff; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,.06); }
    .filter-bar label { display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:4px; }
    .filter-bar select, .filter-bar button { padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:13px; background:#fff; }
    .filter-bar button { background:#0f766e; color:#fff; border:none; font-weight:600; cursor:pointer; }
    .stat-cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:12px; margin-bottom:24px; }
    .stat-card { background:#fff; border-radius:12px; padding:16px 20px; box-shadow:0 1px 3px rgba(0,0,0,.06); }
    .stat-card .val { font-size:28px; font-weight:800; line-height:1.2; }
    .stat-card .lbl { font-size:11px; font-weight:700; text-transform:uppercase; color:#64748b; margin-top:2px; }
    .analytics-table { width:100%; border-collapse:collapse; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.06); }
    .analytics-table th { background:#f8fafc; padding:10px 12px; font-size:11px; color:#64748b; font-weight:700; text-transform:uppercase; text-align:left; white-space:nowrap; border-bottom:2px solid #e2e8f0; }
    .analytics-table td { padding:10px 12px; font-size:13px; color:#475569; border-bottom:1px solid #f1f5f9; white-space:nowrap; }
    .analytics-table tr:hover td { background:#f8fafc; }
    .grade-badge { display:inline-block; padding:1px 8px; border-radius:99px; font-size:11px; font-weight:700; text-transform:uppercase; }
    .grade-A { background:#dcfce7; color:#15803d; }
    .grade-B { background:#dbeafe; color:#1d4ed8; }
    .grade-C { background:#fef9c3; color:#a16207; }
    .grade-D { background:#ffedd5; color:#c2410c; }
    .grade-F { background:#fee2e2; color:#b91c1c; }
    .rank-badge { display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:50%; font-size:12px; font-weight:800; }
    .rank-1 { background:linear-gradient(135deg,#fbbf24,#f59e0b); color:#fff; }
    .rank-2 { background:linear-gradient(135deg,#94a3b8,#64748b); color:#fff; }
    .rank-3 { background:linear-gradient(135deg,#d97706,#b45309); color:#fff; }
    .rank-other { background:#f1f5f9; color:#475569; }
    .subject-table { width:100%; border-collapse:collapse; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.06); margin-top:24px; }
    .subject-table th { background:#f8fafc; padding:10px 12px; font-size:11px; color:#64748b; font-weight:700; text-transform:uppercase; text-align:left; border-bottom:2px solid #e2e8f0; }
    .subject-table td { padding:10px 12px; font-size:13px; color:#475569; border-bottom:1px solid #f1f5f9; }
    .empty-state { text-align:center; padding:60px 20px; color:#94a3b8; }
    .empty-state svg { width:48px; height:48px; margin-bottom:12px; opacity:.4; }
    .empty-state p { font-size:14px; }
</style>
@endpush

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
    <div>
        <h1 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Marks Analytics</h1>
        <p style="margin:4px 0 0;font-size:13px;color:#64748b;">{{ $year?->title ?? 'No active year' }}</p>
    </div>
</div>

<form class="filter-bar" method="GET">
    <div>
        <label for="class">Class</label>
        <select name="class" id="class" onchange="this.form.submit()">
            <option value="">— Select Class —</option>
            @foreach($classes as $c)
                <option value="{{ $c }}" {{ $selectedClass == $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="exam">Exam / Term</label>
        <select name="exam" id="exam" onchange="this.form.submit()">
            <option value="">— Select Exam —</option>
            @foreach($exams as $e)
                <option value="{{ $e->id }}" {{ $selectedExam == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
            @endforeach
        </select>
    </div>
    @if($selectedClass && $selectedExam)
        <button type="submit">Refresh</button>
    @endif
</form>

@if(!$selectedClass || !$selectedExam)
    <div class="empty-state">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        <p>Select a class and exam to view analytics.</p>
    </div>
@elseif($rankings->isEmpty())
    <div class="empty-state">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <p>No marks found for this class and exam.</p>
    </div>
@else
    @php
        $totalStudents = $rankings->count();
        $passCount = $rankings->filter(fn($r) => $r['cgpa'] !== null && $r['cgpa'] >= 4)->count();
        $avgCgpa = $rankings->avg('cgpa');
    @endphp
    <div class="stat-cards">
        <div class="stat-card">
            <div class="val" style="color:#0f172a;">{{ $totalStudents }}</div>
            <div class="lbl">Students</div>
        </div>
        <div class="stat-card">
            <div class="val" style="color:#0f766e;">{{ $passCount }}</div>
            <div class="lbl">Pass (CGPA ≥ 4.0)</div>
        </div>
        <div class="stat-card">
            <div class="val" style="color:#b91c1c;">{{ $totalStudents - $passCount }}</div>
            <div class="lbl">Below Pass</div>
        </div>
        <div class="stat-card">
            <div class="val" style="color:#2563eb;">{{ $avgCgpa !== null ? number_format($avgCgpa, 2) : '—' }}</div>
            <div class="lbl">Avg CGPA</div>
        </div>
    </div>

    <div style="overflow-x:auto;border-radius:12px;">
        <table class="analytics-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Roll</th>
                    <th>Student</th>
                    @foreach($subjects as $subject)
                        <th colspan="2" style="text-align:center;">{{ $subject }}</th>
                    @endforeach
                    <th style="text-align:center;">Avg %</th>
                    <th style="text-align:center;">CGPA</th>
                </tr>
                <tr style="border-top:0;">
                    <th></th>
                    <th></th>
                    <th></th>
                    @foreach($subjects as $subject)
                        <th style="font-size:10px;text-align:center;padding:4px 8px;">%</th>
                        <th style="font-size:10px;text-align:center;padding:4px 8px;">Grade</th>
                    @endforeach
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($rankings as $row)
                    @php
                        $rank = $row['rank'];
                        $rankClass = $rank <= 3 ? 'rank-'.$rank : 'rank-other';
                    @endphp
                    <tr>
                        <td><span class="rank-badge {{ $rankClass }}">{{ $rank }}</span></td>
                        <td style="color:#64748b;">{{ $row['enrollment']->roll_number ?: '—' }}</td>
                        <td style="color:#0f172a;font-weight:600;">{{ $row['enrollment']->student?->name ?? '—' }}</td>
                        @foreach($subjects as $subject)
                            @php $sd = $row['subjectData'][$subject] ?? null; @endphp
                            @if($sd && $sd['pct'] !== null)
                                <td style="text-align:center;font-weight:600;">{{ $sd['pct'] }}%</td>
                                <td style="text-align:center;">
                                    <span class="grade-badge grade-{{ substr($sd['grade'],0,1) }}">{{ $sd['grade'] }}</span>
                                </td>
                            @else
                                <td style="text-align:center;color:#cbd5e1;">—</td>
                                <td style="text-align:center;color:#cbd5e1;">—</td>
                            @endif
                        @endforeach
                        <td style="text-align:center;font-weight:700;color:#0f172a;">{{ $row['avgPct'] !== null ? $row['avgPct'].'%' : '—' }}</td>
                        <td style="text-align:center;font-weight:700;color:#0f172a;">{{ $row['cgpa'] ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(!$subjectStats->isEmpty())
        <div style="margin-top:32px;">
            <h2 style="font-size:16px;font-weight:700;color:#0f172a;margin:0 0 12px;">Subject-wise Statistics</h2>
            <div style="overflow-x:auto;border-radius:12px;">
                <table class="subject-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th style="text-align:center;">Students</th>
                            <th style="text-align:center;">Average %</th>
                            <th style="text-align:center;">Highest</th>
                            <th style="text-align:center;">Lowest</th>
                            <th style="text-align:center;">Grade Distribution</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjectStats as $subject => $stats)
                            <tr>
                                <td style="font-weight:600;color:#0f172a;">{{ $subject }}</td>
                                <td style="text-align:center;">{{ $stats['count'] }}</td>
                                <td style="text-align:center;font-weight:600;">{{ $stats['avg'] !== null ? $stats['avg'].'%' : '—' }}</td>
                                <td style="text-align:center;font-weight:600;color:#15803d;">{{ $stats['highest'] !== null ? $stats['highest'].'%' : '—' }}</td>
                                <td style="text-align:center;font-weight:600;color:#b91c1c;">{{ $stats['lowest'] !== null ? $stats['lowest'].'%' : '—' }}</td>
                                <td>
                                    @if(!empty($stats['gradeDist']))
                                        @foreach($stats['gradeDist'] as $grade => $count)
                                            <span class="grade-badge grade-{{ substr($grade,0,1) }}" style="margin:1px 2px;">{{ $grade }} ({{ $count }})</span>
                                        @endforeach
                                    @else
                                        <span style="color:#94a3b8;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endif
@endsection
