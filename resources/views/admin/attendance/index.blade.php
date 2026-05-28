@extends('layouts.admin')
@section('title', 'Attendance')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <div>
        <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">Attendance</h1>
        <p style="font-size:12px;color:#64748b;margin:2px 0 0;">{{ $year ? $year->name : 'No active year' }}</p>
    </div>
</div>

{{-- Tabs --}}
<div style="display:flex;gap:0;margin-bottom:16px;border-bottom:2px solid #e2e8f0;">
    <a href="{{ route('admin.attendance.index', ['view' => 'daily', 'class' => $class, 'section' => $section, 'date' => $date]) }}"
       style="padding:10px 20px;font-size:13px;font-weight:700;text-decoration:none;border-bottom:2px solid {{ $view === 'daily' ? '#0f766e' : 'transparent' }};color:{{ $view === 'daily' ? '#0f766e' : '#94a3b8' }};margin-bottom:-2px;transition:all .15s;">
        Daily View
    </a>
    <a href="{{ route('admin.attendance.index', ['view' => 'analytics', 'class' => $class, 'section' => $section, 'month' => $month]) }}"
       style="padding:10px 20px;font-size:13px;font-weight:700;text-decoration:none;border-bottom:2px solid {{ $view === 'analytics' ? '#0f766e' : 'transparent' }};color:{{ $view === 'analytics' ? '#0f766e' : '#94a3b8' }};margin-bottom:-2px;transition:all .15s;">
        Monthly Analytics
    </a>
</div>

{{-- Filter bar --}}
<form method="GET" style="background:#fff;border-radius:12px;padding:14px 16px;margin-bottom:16px;display:flex;gap:10px;flex-wrap:wrap;align-items:end;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <input type="hidden" name="view" value="{{ $view }}">
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Class</label>
        <select name="class" onchange="this.form.submit()" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;min-width:160px;">
            <option value="">— pick —</option>
            @foreach($slots as $s)
                <option value="{{ $s->class }}" {{ $class === $s->class && $section === $s->section ? 'selected' : '' }}>
                    {{ $s->class }} — Sec {{ $s->section }}
                </option>
            @endforeach
        </select>
    </div>
    @if($view === 'daily')
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Date</label>
        <input type="date" name="date" value="{{ $date }}" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
    </div>
    @else
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Month</label>
        <input type="month" name="month" value="{{ $month }}" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
    </div>
    @endif
    <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Filter</button>
</form>

@if(!$class)
    <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#94a3b8;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        Select a class above to view attendance.
    </div>
@elseif($view === 'daily')
    {{-- Daily View --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:10px;margin-bottom:16px;">
        @foreach($summary as $st => $count)
            @php
                $bg = match($st) { 'present' => '#dcfce7', 'absent' => '#fee2e2', 'late' => '#fef3c7', 'excused' => '#e0e7ff' };
                $fg = match($st) { 'present' => '#15803d', 'absent' => '#b91c1c', 'late' => '#a16207', 'excused' => '#4338ca' };
            @endphp
            <div style="background:{{ $bg }};color:{{ $fg }};border-radius:10px;padding:12px 14px;">
                <div style="font-size:22px;font-weight:700;line-height:1;">{{ $count }}</div>
                <div style="font-size:11px;font-weight:600;margin-top:4px;text-transform:uppercase;">{{ ucfirst($st) }}</div>
            </div>
        @endforeach
    </div>

    @if($records->isEmpty())
        <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">
            No records for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}.
        </div>
    @else
        <div style="background:#fff;border-radius:12px;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Roll</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Student</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Status</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Marked By</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Override</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($records as $r)
                    <tr style="border-top:1px solid #f1f5f9;">
                        <td style="padding:10px 14px;color:#475569;">{{ $r->enrollment->roll_number ?: '—' }}</td>
                        <td style="padding:10px 14px;color:#0f172a;font-weight:600;">{{ $r->enrollment->student?->name ?? '—' }}</td>
                        <td style="padding:10px 14px;">
                            @php
                                $bg = match($r->status) { 'present' => '#dcfce7', 'absent' => '#fee2e2', 'late' => '#fef3c7', 'excused' => '#e0e7ff' };
                                $fg = match($r->status) { 'present' => '#15803d', 'absent' => '#b91c1c', 'late' => '#a16207', 'excused' => '#4338ca' };
                            @endphp
                            <span style="background:{{ $bg }};color:{{ $fg }};padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;text-transform:uppercase;">{{ $r->status }}</span>
                        </td>
                        <td style="padding:10px 14px;color:#64748b;font-size:12px;">{{ $r->marker?->name ?? '—' }}</td>
                        <td style="padding:10px 14px;">
                            <form method="POST" action="{{ route('admin.attendance.update', $r) }}" style="display:flex;gap:6px;align-items:center;">
                                @csrf @method('PUT')
                                <select name="status" style="font-size:12px;padding:4px 8px;border:1px solid #e2e8f0;border-radius:6px;">
                                    @foreach(\App\Models\AttendanceRecord::STATUSES as $st)
                                        <option value="{{ $st }}" {{ $r->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" style="background:#0f766e;color:#fff;border:none;padding:5px 10px;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">Save</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

@else
    {{-- Analytics View --}}
    @if($studentStats->isEmpty())
        <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#94a3b8;">
            No attendance records for {{ $class }} in {{ $monthStart->format('F Y') }}.
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:24px;">
            <div style="background:#fff;border-radius:12px;padding:16px 20px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
                <div style="font-size:28px;font-weight:800;line-height:1.2;color:#0f172a;">{{ $studentStats->count() }}</div>
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#64748b;margin-top:2px;">Students</div>
            </div>
            <div style="background:#fff;border-radius:12px;padding:16px 20px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
                <div style="font-size:28px;font-weight:800;line-height:1.2;color:#0f766e;">{{ $totalDays }}</div>
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#64748b;margin-top:2px;">School Days</div>
            </div>
            <div style="background:#fff;border-radius:12px;padding:16px 20px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
                <div style="font-size:28px;font-weight:800;line-height:1.2;color:#2563eb;">{{ $classAvgPct !== null ? $classAvgPct.'%' : '—' }}</div>
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#64748b;margin-top:2px;">Avg Attendance</div>
            </div>
        </div>

        @if($monthlyTrend->isNotEmpty())
        <div style="margin-bottom:24px;">
            <h3 style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 10px;">Monthly Trend</h3>
            <div style="background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.06);overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead style="background:#f8fafc;">
                        <tr>
                            <th style="text-align:left;padding:10px 12px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Month</th>
                            <th style="text-align:center;padding:10px 12px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">P</th>
                            <th style="text-align:center;padding:10px 12px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">A</th>
                            <th style="text-align:center;padding:10px 12px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">L</th>
                            <th style="text-align:center;padding:10px 12px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">E</th>
                            <th style="text-align:center;padding:10px 12px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Total</th>
                            <th style="text-align:center;padding:10px 12px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyTrend as $trend)
                        <tr style="border-top:1px solid #f1f5f9;">
                            <td style="padding:10px 12px;font-weight:600;color:#0f172a;">{{ $trend['label'] }}</td>
                            <td style="text-align:center;padding:10px 12px;"><span style="display:inline-block;padding:1px 8px;border-radius:99px;font-size:11px;font-weight:700;background:#dcfce7;color:#15803d;">{{ $trend['present'] }}</span></td>
                            <td style="text-align:center;padding:10px 12px;"><span style="display:inline-block;padding:1px 8px;border-radius:99px;font-size:11px;font-weight:700;background:#fee2e2;color:#b91c1c;">{{ $trend['absent'] }}</span></td>
                            <td style="text-align:center;padding:10px 12px;"><span style="display:inline-block;padding:1px 8px;border-radius:99px;font-size:11px;font-weight:700;background:#fef3c7;color:#a16207;">{{ $trend['late'] }}</span></td>
                            <td style="text-align:center;padding:10px 12px;"><span style="display:inline-block;padding:1px 8px;border-radius:99px;font-size:11px;font-weight:700;background:#e0e7ff;color:#4338ca;">{{ $trend['excused'] }}</span></td>
                            <td style="text-align:center;padding:10px 12px;">{{ $trend['total'] }}</td>
                            <td style="text-align:center;padding:10px 12px;font-weight:700;">{{ $trend['pct'] !== null ? $trend['pct'].'%' : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div>
            <h3 style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 10px;">Per-Student Breakdown</h3>
            <div style="background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.06);overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead style="background:#f8fafc;">
                        <tr>
                            <th style="text-align:left;padding:10px 12px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Roll</th>
                            <th style="text-align:left;padding:10px 12px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Student</th>
                            <th style="text-align:center;padding:10px 12px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">P</th>
                            <th style="text-align:center;padding:10px 12px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">A</th>
                            <th style="text-align:center;padding:10px 12px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">L</th>
                            <th style="text-align:center;padding:10px 12px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">E</th>
                            <th style="text-align:center;padding:10px 12px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Marked</th>
                            <th style="text-align:center;padding:10px 12px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentStats as $row)
                        <tr style="border-top:1px solid #f1f5f9;">
                            <td style="padding:10px 12px;color:#64748b;">{{ $row['enrollment']->roll_number ?: '—' }}</td>
                            <td style="padding:10px 12px;color:#0f172a;font-weight:600;">{{ $row['enrollment']->student?->name ?? '—' }}</td>
                            <td style="text-align:center;padding:10px 12px;"><span style="display:inline-block;padding:1px 8px;border-radius:99px;font-size:11px;font-weight:700;background:#dcfce7;color:#15803d;">{{ $row['present'] }}</span></td>
                            <td style="text-align:center;padding:10px 12px;"><span style="display:inline-block;padding:1px 8px;border-radius:99px;font-size:11px;font-weight:700;background:#fee2e2;color:#b91c1c;">{{ $row['absent'] }}</span></td>
                            <td style="text-align:center;padding:10px 12px;"><span style="display:inline-block;padding:1px 8px;border-radius:99px;font-size:11px;font-weight:700;background:#fef3c7;color:#a16207;">{{ $row['late'] }}</span></td>
                            <td style="text-align:center;padding:10px 12px;"><span style="display:inline-block;padding:1px 8px;border-radius:99px;font-size:11px;font-weight:700;background:#e0e7ff;color:#4338ca;">{{ $row['excused'] }}</span></td>
                            <td style="text-align:center;padding:10px 12px;">{{ $row['marked'] }}</td>
                            <td style="text-align:center;padding:10px 12px;font-weight:700;">{{ $row['pct'] !== null ? $row['pct'].'%' : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endif
@endsection