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
        <select onchange="pickClassSection(this)" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;min-width:180px;">
            <option value="">— pick —</option>
            @foreach($slots as $s)
                <option value="{{ $s->class }}|{{ $s->section }}" {{ $class === $s->class && $section === $s->section ? 'selected' : '' }}>
                    {{ $s->class }} — Sec {{ $s->section }}
                </option>
            @endforeach
        </select>
        <input type="hidden" name="class" value="{{ $class }}">
        <input type="hidden" name="section" value="{{ $section }}">
    </div>
    <script>
        function pickClassSection(sel) {
            var v = sel.value || '';
            var parts = v.split('|');
            var form = sel.closest('form');
            form.querySelector('input[name="class"]').value   = parts[0] || '';
            form.querySelector('input[name="section"]').value = parts[1] || '';
            form.submit();
        }
    </script>
    @if($view === 'daily')
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Date</label>
        <input type="date" name="date" value="{{ $date }}" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
    </div>
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Status</label>
        <select name="approval_status" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
            <option value="">All</option>
            <option value="pending" {{ $approvalStatus === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ $approvalStatus === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ $approvalStatus === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </div>
    @else
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Month</label>
        <input type="month" name="month" value="{{ $month }}" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
    </div>
    @endif
    <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Filter</button>
    @if($view === 'daily' && $class)
        <a href="{{ route('admin.attendance.export', ['class' => $class, 'section' => $section, 'date' => $date]) }}" style="background:#fff;color:#0f766e;border:1px solid #0f766e;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;">Export CSV</a>
    @elseif($view !== 'daily')
        <a href="{{ route('admin.attendance.export', ['class' => $class, 'section' => $section, 'date' => $date]) }}" style="background:#fff;color:#0f766e;border:1px solid #0f766e;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;">Export CSV</a>
    @endif
</form>

{{-- Global pending inbox — shown whenever there are pending records, regardless of class filter --}}
@if($pendingInbox->isNotEmpty())
<div style="background:#fff;border-radius:12px;padding:14px 16px;margin-bottom:14px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px;">
        <div style="font-size:14px;font-weight:700;color:#0f172a;">
            ⏳ Pending Attendance Approvals
            <span style="background:#fef3c7;color:#92400e;font-size:11px;font-weight:700;padding:2px 10px;border-radius:99px;margin-left:6px;">{{ $pendingCount }}</span>
        </div>
        <form method="POST" action="{{ route('admin.attendance.approve-all') }}" style="margin:0;">
            @csrf
            <button type="button" onclick="customConfirm('Approve ALL {{ $pendingCount }} pending attendance records?',()=>this.closest('form').submit())"
                    style="background:#0f766e;color:#fff;border:none;padding:7px 14px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">Approve All</button>
        </form>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:10px;">
        @foreach($pendingInbox as $p)
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 14px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                <span style="font-size:13px;font-weight:700;color:#0f172a;">{{ $p->class }} — Sec {{ $p->section }}</span>
                <span style="font-size:10px;background:#fef3c7;color:#92400e;font-weight:700;padding:2px 8px;border-radius:99px;">{{ $p->student_count }} student{{ $p->student_count !== 1 ? 's' : '' }}</span>
            </div>
            <div style="font-size:11px;color:#64748b;">
                {{ \Carbon\Carbon::parse($p->date)->format('d M Y') }} · marked by {{ $p->marker?->name ?? 'unknown' }}
            </div>
            <div style="font-size:10px;color:#94a3b8;margin:4px 0 8px;">{{ $p->first_marked_at ? \Carbon\Carbon::parse($p->first_marked_at)->diffForHumans() : '' }}</div>
            <div style="display:flex;gap:6px;">
                <a href="{{ route('admin.attendance.index', ['view' => 'daily', 'class' => $p->class, 'section' => $p->section, 'date' => \Carbon\Carbon::parse($p->date)->toDateString(), 'approval_status' => 'pending']) }}"
                   style="flex:1;background:#0f766e;color:#fff;text-align:center;padding:6px 0;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none;">Review</a>
                <form method="POST" action="{{ route('admin.attendance.approve-day') }}" style="flex-shrink:0;margin:0;">
                    @csrf
                    <input type="hidden" name="class" value="{{ $p->class }}">
                    <input type="hidden" name="section" value="{{ $p->section }}">
                    <input type="hidden" name="date" value="{{ \Carbon\Carbon::parse($p->date)->toDateString() }}">
                    <button type="submit" style="background:#dcfce7;color:#15803d;border:1px solid #86efac;padding:6px 12px;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;">Approve</button>
                </form>
                <form method="POST" action="{{ route('admin.attendance.reject-day') }}" style="flex-shrink:0;margin:0;">
                    @csrf
                    <input type="hidden" name="class" value="{{ $p->class }}">
                    <input type="hidden" name="section" value="{{ $p->section }}">
                    <input type="hidden" name="date" value="{{ \Carbon\Carbon::parse($p->date)->toDateString() }}">
                    <button type="button" onclick="customConfirm('Reject this submission?',()=>this.closest('form').submit())" style="background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;padding:6px 12px;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;">Reject</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@if(!$class)
    @if($pendingInbox->isEmpty())
    <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#94a3b8;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        Select a class above to view attendance.
    </div>
    @endif
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

    {{-- Approve/Reject actions for daily view --}}
    @if($class && $records->isNotEmpty() && $records->contains(fn($r) => $r->approval_status === 'pending'))
    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:10px 14px;margin-bottom:12px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
        <span style="font-size:12px;font-weight:700;color:#92400e;">⚠️ Pending approval for {{ $class }} on {{ \Carbon\Carbon::parse($date)->format('d M Y') }}:</span>
        <form method="POST" action="{{ route('admin.attendance.approve-day') }}" style="display:inline;">
            @csrf
            <input type="hidden" name="class" value="{{ $class }}">
            <input type="hidden" name="section" value="{{ $section }}">
            <input type="hidden" name="date" value="{{ $date }}">
            <button type="submit" style="background:#dcfce7;color:#15803d;border:1px solid #86efac;padding:5px 14px;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer;">✅ Approve Day</button>
        </form>
        <form method="POST" action="{{ route('admin.attendance.reject-day') }}" style="display:inline;">
            @csrf
            <input type="hidden" name="class" value="{{ $class }}">
            <input type="hidden" name="section" value="{{ $section }}">
            <input type="hidden" name="date" value="{{ $date }}">
            <button type="submit" style="background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;padding:5px 14px;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer;">✕ Reject Day</button>
        </form>
    </div>
    @endif

    @if($records->isEmpty())
        @if($canBackfill)
            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:14px 16px;margin-bottom:12px;">
                <div style="font-size:13px;font-weight:700;color:#92400e;margin-bottom:4px;">
                    ↩ No attendance recorded for {{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}
                </div>
                <div style="font-size:11px;color:#78350f;">
                    Class teacher missed marking this day. Backfill it below — entries will be marked approved immediately since you are the authority.
                </div>
            </div>

            <form method="POST" action="{{ route('admin.attendance.backfill-day') }}" style="background:#fff;border-radius:12px;padding:0;overflow:hidden;">
                @csrf
                <input type="hidden" name="class" value="{{ $class }}">
                <input type="hidden" name="section" value="{{ $section }}">
                <input type="hidden" name="date" value="{{ $date }}">

                <div style="background:#f8fafc;padding:10px 14px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;border-bottom:1px solid #e2e8f0;">
                    <span style="font-size:11px;color:#64748b;font-weight:600;">Bulk set all to:</span>
                    @foreach(\App\Models\AttendanceRecord::STATUSES as $st)
                        <button type="button" onclick="bulkSet('{{ $st }}')" style="font-size:11px;padding:4px 10px;border:1px solid #e2e8f0;background:#fff;border-radius:6px;font-weight:600;cursor:pointer;color:#475569;text-transform:capitalize;">{{ $st }}</button>
                    @endforeach
                    <span style="flex:1;min-width:120px;font-size:11px;color:#94a3b8;">{{ $backfillEnrollments->count() }} active students</span>
                </div>

                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead style="background:#f8fafc;">
                        <tr>
                            <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Roll</th>
                            <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Student</th>
                            <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Status</th>
                            <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Remarks (optional)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backfillEnrollments as $e)
                            <tr style="border-top:1px solid #f1f5f9;">
                                <td style="padding:8px 14px;color:#475569;">{{ $e->roll_number ?: '—' }}</td>
                                <td style="padding:8px 14px;color:#0f172a;font-weight:600;">{{ $e->student?->name ?? '—' }}</td>
                                <td style="padding:8px 14px;">
                                    <select name="marks[{{ $e->id }}][status]" class="bf-status" style="font-size:12px;padding:5px 8px;border:1px solid #e2e8f0;border-radius:6px;min-width:110px;">
                                        @foreach(\App\Models\AttendanceRecord::STATUSES as $st)
                                            <option value="{{ $st }}" {{ $st === 'present' ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="padding:8px 14px;">
                                    <input type="text" name="marks[{{ $e->id }}][remarks]" maxlength="500" placeholder="—" style="width:100%;font-size:12px;padding:5px 8px;border:1px solid #e2e8f0;border-radius:6px;">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="padding:14px;border-top:1px solid #e2e8f0;background:#f8fafc;display:flex;justify-content:flex-end;">
                    <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:9px 22px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 2px 8px rgba(13,148,136,.3);">Save Backfill</button>
                </div>
            </form>

            <script>
                function bulkSet(status) {
                    document.querySelectorAll('.bf-status').forEach(function(s) { s.value = status; });
                }
            </script>
        @else
            <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">
                No records for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}.
                @if(!$section)
                    <div style="font-size:11px;margin-top:6px;">Pick a class+section above to backfill.</div>
                @endif
            </div>
        @endif
    @else
        <div style="background:#fff;border-radius:12px;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Roll</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Student</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Status</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Marked By</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Approval</th>
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
                            @php
                                $abg = match($r->approval_status) { 'approved' => '#dcfce7', 'pending' => '#fef3c7', 'rejected' => '#fee2e2', default => '#f1f5f9' };
                                $afg = match($r->approval_status) { 'approved' => '#15803d', 'pending' => '#92400e', 'rejected' => '#b91c1c', default => '#64748b' };
                            @endphp
                            <span style="background:{{ $abg }};color:{{ $afg }};padding:2px 8px;border-radius:99px;font-size:10px;font-weight:700;text-transform:uppercase;">{{ $r->approval_status }}</span>
                        </td>
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

    {{-- Bulk Override --}}
    <details style="margin-top:16px;background:#fff8f0;border:1px solid #fde68a;border-radius:12px;padding:12px 16px;">
        <summary style="font-size:13px;font-weight:700;color:#92400e;cursor:pointer;">Bulk Attendance Override</summary>
        <form method="POST" action="{{ route('admin.attendance.bulk') }}" style="margin-top:12px;display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
            @csrf
            <input type="hidden" name="class" value="{{ $class }}">
            <input type="hidden" name="section" value="{{ $section }}">
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Status</label>
                <select name="status" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
                    @foreach(\App\Models\AttendanceRecord::STATUSES as $st)
                        <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Start Date</label>
                <input type="date" name="start_date" required style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
            </div>
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">End Date</label>
                <input type="date" name="end_date" required style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
            </div>
            <button type="submit" style="background:#d97706;color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Apply Bulk</button>
        </form>
    </details>

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