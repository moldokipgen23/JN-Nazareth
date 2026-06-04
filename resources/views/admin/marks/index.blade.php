@extends('layouts.admin')
@section('page-title', 'Marks')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">Marks</h1>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $year ? $year->name : 'No active year' }}</div>
    </div>
</div>

{{-- Tabs --}}
<div style="display:flex;gap:0;margin-bottom:16px;border-bottom:2px solid #e2e8f0;">
    <a href="{{ route('admin.marks.index', ['view' => 'review', 'exam' => $examId, 'class' => $class, 'section' => $section, 'subject' => $subject]) }}"
       style="padding:10px 20px;font-size:13px;font-weight:700;text-decoration:none;border-bottom:2px solid {{ $view === 'review' ? '#0f766e' : 'transparent' }};color:{{ $view === 'review' ? '#0f766e' : '#94a3b8' }};margin-bottom:-2px;transition:all .15s;">
        Per-Subject Review
    </a>
    <a href="{{ route('admin.marks.index', ['view' => 'results', 'exam' => $examId, 'class' => $class, 'section' => $section]) }}"
       style="padding:10px 20px;font-size:13px;font-weight:700;text-decoration:none;border-bottom:2px solid {{ $view === 'results' ? '#0f766e' : 'transparent' }};color:{{ $view === 'results' ? '#0f766e' : '#94a3b8' }};margin-bottom:-2px;transition:all .15s;">
        Results
    </a>
    <a href="{{ route('admin.marks.index', ['view' => 'summary', 'exam' => $examId, 'class' => $class, 'section' => $section]) }}"
       style="padding:10px 20px;font-size:13px;font-weight:700;text-decoration:none;border-bottom:2px solid {{ $view === 'summary' ? '#0f766e' : 'transparent' }};color:{{ $view === 'summary' ? '#0f766e' : '#94a3b8' }};margin-bottom:-2px;transition:all .15s;">
        Summary
    </a>
</div>

<form method="GET" style="background:#fff;border-radius:12px;padding:14px 16px;margin-bottom:16px;display:flex;gap:10px;flex-wrap:wrap;align-items:end;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <input type="hidden" name="view" value="{{ $view }}">
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Exam</label>
        <select name="exam" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;min-width:160px;">
            <option value="">— pick —</option>
            @foreach($exams as $e)
                <option value="{{ $e->id }}" {{ (string) $examId === (string) $e->id ? 'selected' : '' }}>{{ $e->name }}{{ $e->code ? ' ('.$e->code.')' : '' }}</option>
            @endforeach
        </select>
    </div>
    @if($view !== 'summary')
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Class / Section</label>
        <select name="class" onchange="syncSection(this.form)" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;min-width:160px;">
            <option value="">— pick —</option>
            @foreach($slots as $s)
                <option value="{{ $s->class }}" data-section="{{ $s->section }}"
                    {{ $class === $s->class && $section === $s->section ? 'selected' : '' }}>
                    {{ $s->class }} — Sec {{ $s->section }}
                </option>
            @endforeach
        </select>
        <input type="hidden" name="section" value="{{ $section }}">
    </div>
    @endif
    @if($view === 'review')
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Subject</label>
        <select name="subject" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;min-width:140px;">
            <option value="">— pick —</option>
            @foreach($filteredSubjectList as $s)
                <option value="{{ $s }}" {{ $subject === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
    </div>
    @endif
    <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Filter</button>
    @if($view === 'review' && $examId && $class && $subject)
        <a href="{{ route('admin.marks.export', ['exam' => $examId, 'class' => $class, 'section' => $section, 'subject' => $subject]) }}" style="background:#fff;color:#0f766e;border:1px solid #0f766e;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;">Export CSV</a>
    @endif
    @if($view === 'results' && $examId && $class)
        @php
            $_studentCount = \App\Models\StudentEnrollment::forActiveYear()->active()
                ->where('class', $class)->when($section, fn ($q) => $q->where('section', $section))
                ->count();
        @endphp
        <a href="{{ route('admin.marks.export-result-cards', ['exam' => $examId, 'class' => $class, 'section' => $section]) }}" title="{{ $_studentCount }} students — single PDF, all ranked" style="background:#7c3aed;color:#fff;border:none;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;">
            Class Result PDF
        </a>
        <a href="{{ route('admin.marks.export-results', ['exam' => $examId, 'class' => $class, 'section' => $section]) }}" style="background:#fff;color:#0f766e;border:1px solid #0f766e;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;">
            Results CSV
        </a>
    @endif
</form>

<script>
function syncSection(form) {
    const sel = form.querySelector('select[name="class"]');
    const opt = sel.options[sel.selectedIndex];
    form.querySelector('input[name="section"]').value = opt?.dataset.section || '';
}

</script>

@if($view === 'review')
    @if($examId && $class && $section && $subject)
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:10px;margin-bottom:16px;">
            @php
                $statCards = [
                    'total'     => ['#f1f5f9', '#475569'],
                    'pass'      => ['#dcfce7', '#15803d'],
                    'fail'      => ['#fee2e2', '#b91c1c'],
                    'ungraded'  => ['#fef3c7', '#92400e'],
                    'submitted' => ['#dbeafe', '#1d4ed8'],
                    'approved'  => ['#ede9fe', '#6d28d9'],
                ];
            @endphp
            @foreach($statCards as $key => $col)
                <div style="background:{{ $col[0] }};color:{{ $col[1] }};border-radius:10px;padding:12px 14px;">
                    <div style="font-size:22px;font-weight:700;line-height:1;">{{ $stats[$key] ?? 0 }}</div>
                    <div style="font-size:11px;font-weight:600;margin-top:4px;text-transform:uppercase;">{{ ucfirst($key) }}</div>
                </div>
            @endforeach
        </div>

        @php
            $_anyPending = $records->contains(fn($r) => $r->submitted_at && !$r->approved_at);
            $_allApproved = $records->isNotEmpty() && $records->every(fn($r) => $r->approved_at);
            $_anySubmitted = $records->contains(fn($r) => $r->submitted_at);
        @endphp

        @if($_anyPending)
        <div style="background:#f5f3ff;border:1px solid #ddd6fe;border-radius:12px;padding:12px 16px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:13px;font-weight:700;color:#6d28d9;">⏳ Pending Approval</span>
                <span style="font-size:11px;color:#6d28d9;">{{ $records->filter(fn($r) => $r->submitted_at && !$r->approved_at)->count() }} student(s) awaiting approval</span>
            </div>
            <div style="display:flex;gap:6px;">
                <form method="POST" action="{{ route('admin.marks.approve-subject') }}">
                    @csrf
                    <input type="hidden" name="exam_id" value="{{ $examId }}">
                    <input type="hidden" name="class" value="{{ $class }}">
                    <input type="hidden" name="section" value="{{ $section }}">
                    <input type="hidden" name="subject" value="{{ $subject }}">
                    <button type="button" onclick="customConfirm('Approve all pending marks for {{ $subject }}?',()=>this.closest('form').submit())"
                            style="background:linear-gradient(135deg,#6d28d9,#7c3aed);color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">✅ Approve All</button>
                </form>
                <form method="POST" action="{{ route('admin.marks.delete-subject') }}">
                    @csrf
                    <input type="hidden" name="exam_id" value="{{ $examId }}">
                    <input type="hidden" name="class" value="{{ $class }}">
                    <input type="hidden" name="section" value="{{ $section }}">
                    <input type="hidden" name="subject" value="{{ $subject }}">
                    <button type="button" onclick="customConfirm('Delete all pending marks for {{ $subject }}? This cannot be undone.',()=>this.closest('form').submit(),'Delete')"
                            style="background:#fee2e2;color:#b91c1c;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">🗑 Delete All</button>
                </form>
                <form method="POST" action="{{ route('admin.marks.index', ['view' => 'review', 'exam' => $examId, 'class' => $class, 'section' => $section, 'subject' => $subject]) }}" style="display:none;"></form>
            </div>
        </div>
        @elseif($_allApproved)
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:12px 16px;margin-bottom:14px;display:flex;align-items:center;gap:8px;">
            <span style="font-size:13px;font-weight:700;color:#15803d;">✅ All marks approved</span>
        </div>
        @endif

        @if($records->isEmpty())
            <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">No marks entered yet.</div>
        @else
        <div style="background:#fff;border-radius:12px;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Roll</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Student</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Theory</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Assignment</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Total / Full</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">%</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Grade</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Status</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Submitted</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Approved</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Entered By</th>
                        <th style="text-align:right;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($records as $r)
                    @continue(!$r->enrollment)
                    <tr style="border-top:1px solid #f1f5f9;{{ $r->approved_at ? 'background:#f5f3ff;' : '' }}">
                        <td style="padding:10px 14px;color:#475569;">{{ $r->enrollment?->roll_number ?: '—' }}</td>
                        <td style="padding:10px 14px;color:#0f172a;font-weight:600;">{{ $r->enrollment?->student?->name ?? '—' }}</td>
                        <td style="padding:10px 14px;color:#475569;">{{ $r->theory_marks ?? '—' }}</td>
                        <td style="padding:10px 14px;color:#475569;">{{ $r->assignment_marks ?? '—' }}</td>
                        <td style="padding:10px 14px;font-weight:600;">
                            {{ $r->total_marks ?? $r->obtained_marks ?? '—' }}<span style="color:#94a3b8;font-weight:400;"> / {{ $r->full_marks }}</span>
                        </td>
                        <td style="padding:10px 14px;color:#475569;">{{ $r->percentage() !== null ? $r->percentage().'%' : '—' }}</td>
                        <td style="padding:10px 14px;color:#475569;">{{ $r->grade ?: $r->computedGrade() ?: '—' }}</td>
                        <td style="padding:10px 14px;">
                            @php
                                $s = $r->status() ?: 'ungraded';
                                $statusColors = [
                                    'pass'     => ['#dcfce7', '#15803d'],
                                    'fail'     => ['#fee2e2', '#b91c1c'],
                                    'ungraded' => ['#f1f5f9', '#94a3b8'],
                                ];
                                $sc = $statusColors[$s] ?? $statusColors['ungraded'];
                                $_bg = $sc[0];
                                $_fg = $sc[1];
                            @endphp
                            <span style="background:{{ $_bg }};color:{{ $_fg }};padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;text-transform:uppercase;">{{ $s }}</span>
                        </td>
                        <td style="padding:10px 14px;">
                            @if($r->submitted_at)
                                <span style="color:#15803d;font-size:11px;font-weight:600;">✅ {{ $r->submitted_at->format('d M') }}</span>
                            @else
                                <span style="color:#94a3b8;font-size:11px;">—</span>
                            @endif
                        </td>
                        <td style="padding:10px 14px;">
                            @if($r->approved_at)
                                <span style="color:#6d28d9;font-size:11px;font-weight:600;">✅ {{ $r->approved_at->format('d M') }}</span>
                            @elseif($r->submitted_at)
                                <span style="color:#92400e;font-size:11px;font-weight:600;">⏳ Pending</span>
                            @else
                                <span style="color:#94a3b8;font-size:11px;">—</span>
                            @endif
                        </td>
                        <td style="padding:10px 14px;color:#64748b;font-size:12px;">{{ $r->enteredBy?->name ?? '—' }}</td>
                        <td style="padding:10px 14px;text-align:right;white-space:nowrap;">
                            <div style="display:flex;gap:4px;justify-content:flex-end;align-items:center;flex-wrap:nowrap;">
                                @if($r->submitted_at && !$r->approved_at)
                                <form method="POST" action="{{ route('admin.marks.approve', $r) }}" style="display:inline;flex-shrink:0;">
                                    @csrf
                                    <button type="submit" style="background:#6d28d9;color:#fff;border:none;padding:4px 10px;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">Approve</button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('admin.marks.update', $r) }}" style="display:flex;gap:4px;align-items:center;flex-shrink:0;">
                                    @csrf @method('PUT')
                                    <input type="number" step="0.01" min="0" max="{{ $r->full_marks }}" name="total_marks" value="{{ $r->total_marks ?? $r->obtained_marks }}" style="width:58px;padding:4px 6px;border:1px solid #e2e8f0;border-radius:6px;font-size:11px;text-align:center;">
                                    <input type="text" name="grade" value="{{ $r->grade ?: $r->computedGrade() }}" maxlength="5" placeholder="Grd" style="width:38px;padding:4px 4px;border:1px solid #e2e8f0;border-radius:6px;font-size:11px;text-align:center;">
                                    <button type="submit" style="background:#0f766e;color:#fff;border:none;padding:4px 10px;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;flex-shrink:0;">Save</button>
                                </form>
                                @if($r->submitted_at && !$r->approved_at)
                                <form method="POST" action="{{ route('admin.marks.send-back', $r) }}" style="display:inline;flex-shrink:0;">
                                    @csrf
                                    <button type="button" style="background:#fef3c7;color:#92400e;border:none;padding:4px 10px;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;" onclick="customConfirm('Send back for revision? Teacher will be able to re-edit.',()=>this.closest('form').submit())">Edit</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- Submission summary --}}
        @if($submissionStatus->isNotEmpty())
        <div style="background:#fff;border-radius:12px;padding:14px 16px;margin-top:16px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
            <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:8px;">Submission Status per Subject</div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:8px;">
                @foreach($submissionStatus as $ss)
                <div style="background:{{ $ss->total === $ss->approved_count ? '#f0fdf4' : '#fef3c7' }};border-radius:8px;padding:8px 12px;">
                    <div style="font-size:12px;font-weight:600;color:#0f172a;">{{ $ss->subject }}</div>
                    <div style="font-size:11px;color:#64748b;">{{ $ss->approved_count }}/{{ $ss->total }} approved</div>
                    @if($ss->total === $ss->approved_count)
                        <span style="font-size:10px;color:#15803d;font-weight:600;">✅ Approved</span>
                    @else
                        <span style="font-size:10px;color:#92400e;font-weight:600;">⏳ Pending</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif
    @else
        {{-- Pending Approvals — show when no specific subject is selected --}}
        <div style="background:#fff;border-radius:12px;padding:16px 18px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                <div style="font-size:14px;font-weight:700;color:#0f172a;">
                    Pending Approvals
                    @if($pendingReviews->isNotEmpty())
                        <span style="background:#fef3c7;color:#92400e;font-size:11px;font-weight:700;padding:2px 10px;border-radius:99px;margin-left:8px;">{{ $pendingReviews->count() }}</span>
                    @endif
                </div>
                <div style="font-size:11px;color:#64748b;">Submitted marks awaiting admin approval</div>
            </div>
            @if($pendingReviews->isEmpty())
                <div style="text-align:center;padding:24px 12px;color:#94a3b8;">
                    <div style="font-size:28px;opacity:.4;margin-bottom:6px;">✅</div>
                    <div style="font-weight:600;font-size:13px;">No pending approvals</div>
                    <div style="font-size:11px;margin-top:4px;">All submitted marks have been reviewed.</div>
                </div>
            @else
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:10px;">
                    @foreach($pendingReviews as $pr)
                    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                            <span style="font-size:13px;font-weight:700;color:#0f172a;">{{ $pr->subject }}</span>
                            <span style="font-size:10px;background:#fef3c7;color:#92400e;font-weight:700;padding:2px 8px;border-radius:99px;">{{ $pr->student_count }} student{{ $pr->student_count !== 1 ? 's' : '' }}</span>
                        </div>
                        <div style="font-size:11px;color:#64748b;">
                            {{ $pr->exam?->name ?? 'Exam #'.$pr->exam_id }} · {{ $pr->class }} — Sec {{ $pr->section }}
                        </div>
                        <div style="font-size:10px;color:#94a3b8;margin:4px 0 8px;">
                            Submitted {{ $pr->first_submitted_at ? \Carbon\Carbon::parse($pr->first_submitted_at)->diffForHumans() : '' }}
                        </div>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('admin.marks.index', ['view' => 'review', 'exam' => $pr->exam_id, 'class' => $pr->class, 'section' => $pr->section, 'subject' => $pr->subject]) }}"
                               style="flex:1;background:#0f766e;color:#fff;text-align:center;padding:7px 0;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;">Review & Approve</a>
                            <form method="POST" action="{{ route('admin.marks.send-back-subject') }}" style="flex-shrink:0;">
                                @csrf
                                <input type="hidden" name="exam_id" value="{{ $pr->exam_id }}">
                                <input type="hidden" name="class" value="{{ $pr->class }}">
                                <input type="hidden" name="section" value="{{ $pr->section }}">
                                <input type="hidden" name="subject" value="{{ $pr->subject }}">
                                <button type="button" onclick="customConfirm('Send back all marks for {{ $pr->subject }} for revision? Teacher can re-edit.',()=>this.closest('form').submit())"
                                        style="background:#fef3c7;color:#92400e;border:none;padding:7px 12px;border-radius:8px;font-size:11px;font-weight:600;cursor:pointer;">Send Back</button>
                            </form>
                            <form method="POST" action="{{ route('admin.marks.approve-subject') }}" style="flex-shrink:0;">
                                @csrf
                                <input type="hidden" name="exam_id" value="{{ $pr->exam_id }}">
                                <input type="hidden" name="class" value="{{ $pr->class }}">
                                <input type="hidden" name="section" value="{{ $pr->section }}">
                                <input type="hidden" name="subject" value="{{ $pr->subject }}">
                                <button type="button" onclick="customConfirm('Approve all {{ $pr->student_count }} mark(s) for {{ $pr->subject }}?',()=>this.closest('form').submit())"
                                        style="background:#6d28d9;color:#fff;border:none;padding:7px 12px;border-radius:8px;font-size:11px;font-weight:600;cursor:pointer;">Approve All</button>
                            </form>
                            <form method="POST" action="{{ route('admin.marks.delete-subject') }}" style="flex-shrink:0;">
                                @csrf
                                <input type="hidden" name="exam_id" value="{{ $pr->exam_id }}">
                                <input type="hidden" name="class" value="{{ $pr->class }}">
                                <input type="hidden" name="section" value="{{ $pr->section }}">
                                <input type="hidden" name="subject" value="{{ $pr->subject }}">
                                <button type="button" onclick="customConfirm('Delete all {{ $pr->student_count }} mark(s) for {{ $pr->subject }}? This cannot be undone.',()=>this.closest('form').submit(),'Delete')"
                                        style="background:#fee2e2;color:#b91c1c;border:none;padding:7px 12px;border-radius:8px;font-size:11px;font-weight:600;cursor:pointer;">Delete</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if($examId && $class && $section)
        {{-- Also show filter hint if filters are partially set --}}
        <div style="background:#f1f5f9;border-radius:12px;padding:20px 24px;text-align:center;color:#64748b;font-size:13px;">
            Select a subject above to view individual marks for this class.
        </div>
        @else
        <div style="background:#f1f5f9;border-radius:12px;padding:20px 24px;text-align:center;color:#64748b;font-size:13px;">
            Select exam + class above to filter, or review pending submissions listed here.
        </div>
        @endif
    @endif
@elseif($view === 'results')
    {{-- Results View --}}
    @if(!$examId || !$class)
        <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">Pick exam + class above to view results.</div>
    @elseif(!$allSubmitted)
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:36px 20px;text-align:center;">
            <div style="font-size:28px;opacity:.5;margin-bottom:8px;">⏳</div>
            <div style="font-weight:700;color:#92400e;font-size:15px;margin-bottom:6px;">Results not yet available</div>
            <div style="font-size:12px;color:#a16207;margin-bottom:8px;">The following subjects still have unsubmitted marks:</div>
            <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;">
                @foreach($pendingSubjects as $ps)
                    <span style="background:#fee2e2;color:#b91c1c;font-size:11px;font-weight:700;padding:4px 12px;border-radius:99px;">{{ $ps }}</span>
                @endforeach
            </div>
        </div>
    @elseif($passRankings->isEmpty() && $failRankings->isEmpty())
        <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">No marks found for this combination.</div>
    @else
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
            <div style="background:#f0fdf4;color:#15803d;border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:10px;">
                <div><span style="font-size:20px;font-weight:700;">{{ $passRankings->count() }}</span> <span style="font-size:12px;">Pass</span></div>
            </div>
            <div style="background:#fee2e2;color:#b91c1c;border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:10px;">
                <div><span style="font-size:20px;font-weight:700;">{{ $failRankings->count() }}</span> <span style="font-size:12px;">Needs Improvement</span></div>
            </div>
        </div>

        @if(!empty($subjectStats))
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;margin-bottom:16px;">
            @foreach($subjectStats as $subj => $stats)
            <div style="background:#fff;border-radius:10px;padding:12px 14px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
                <div style="font-size:12px;font-weight:700;color:#0f172a;margin-bottom:6px;">{{ $subj }}</div>
                <div style="font-size:11px;color:#64748b;">Avg: <strong>{{ $stats['avg'] !== null ? $stats['avg'].'%' : '—' }}</strong></div>
                <div style="font-size:11px;color:#64748b;">High: <strong style="color:#15803d;">{{ $stats['highest'] !== null ? $stats['highest'].'%' : '—' }}</strong></div>
                <div style="font-size:11px;color:#64748b;">Low: <strong style="color:#b91c1c;">{{ $stats['lowest'] !== null ? $stats['lowest'].'%' : '—' }}</strong></div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- PASS SECTION --}}
        @if($passRankings->isNotEmpty())
        <div style="background:#f0fdf4;border-radius:12px 12px 0 0;padding:10px 16px;border:1px solid #bbf7d0;border-bottom:none;">
            <span style="font-size:13px;font-weight:700;color:#15803d;">✅ Pass — Ranked</span>
        </div>
        <div style="background:#fff;overflow-x:auto;border:1px solid #bbf7d0;border-top:none;border-radius:0 0 12px 12px;margin-bottom:16px;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead style="background:#f0fdf4;">
                    <tr>
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Rank</th>
                        <th style="text-align:left;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Roll</th>
                        <th style="text-align:left;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Student</th>
                        @foreach($analyticsSubjects as $subj)
                            <th style="text-align:center;padding:10px 6px;font-size:10px;color:#64748b;font-weight:700;text-transform:uppercase;">{{ $subj }}</th>
                        @endforeach
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Total</th>
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Avg %</th>
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">CGPA</th>
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Division</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($passRankings as $r)
                    <tr style="border-top:1px solid #f1f5f9;{{ $r['rank'] <= 3 ? 'background:#f0fdf4;' : '' }}">
                        <td style="text-align:center;padding:10px 10px;">
                            @if($r['rank'] === 1)
                                <span style="background:#fbbf24;color:#92400e;border-radius:99px;padding:2px 10px;font-weight:800;font-size:12px;">#{{ $r['rank'] }}</span>
                            @elseif($r['rank'] === 2)
                                <span style="background:#e2e8f0;color:#475569;border-radius:99px;padding:2px 10px;font-weight:800;font-size:12px;">#{{ $r['rank'] }}</span>
                            @elseif($r['rank'] === 3)
                                <span style="background:#fed7aa;color:#9a3412;border-radius:99px;padding:2px 10px;font-weight:800;font-size:12px;">#{{ $r['rank'] }}</span>
                            @else
                                <span style="font-weight:800;color:#64748b;">#{{ $r['rank'] }}</span>
                            @endif
                        </td>
                        <td style="padding:10px 10px;color:#64748b;">{{ $r['enrollment']?->roll_number ?: '—' }}</td>
                        <td style="padding:10px 10px;font-weight:600;color:#0f172a;">{{ $r['enrollment']?->student?->name ?? '—' }}</td>
                        @foreach($analyticsSubjects as $subj)
                            @php $sd = $r['subjectData'][$subj] ?? null; @endphp
                            <td style="text-align:center;padding:10px 6px;font-size:12px;">
                                @if($sd && $sd['pct'] !== null)
                                    <span style="font-weight:600;">{{ $sd['raw'] }}</span>
                                @else
                                    <span style="color:#cbd5e1;">—</span>
                                @endif
                            </td>
                        @endforeach
                        <td style="text-align:center;padding:10px 10px;font-weight:700;">{{ $r['totalRaw'] }}</td>
                        <td style="text-align:center;padding:10px 10px;font-weight:700;">{{ $r['avgPct'] !== null ? $r['avgPct'].'%' : '—' }}</td>
                        <td style="text-align:center;padding:10px 10px;font-weight:700;color:#0f766e;">{{ $r['cgpa'] !== null ? number_format($r['cgpa'], 2) : '—' }}</td>
                        <td style="text-align:center;padding:10px 10px;font-weight:700;color:#475569;">{{ $r['division'] ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- FAIL SECTION --}}
        @if($failRankings->isNotEmpty())
        <div style="background:#fef2f2;border-radius:12px 12px 0 0;padding:10px 16px;border:1px solid #fecaca;border-bottom:none;">
            <span style="font-size:13px;font-weight:700;color:#b91c1c;">❌ Needs Improvement ({{ $failRankings->count() }})</span>
        </div>
        <div style="background:#fff;overflow-x:auto;border:1px solid #fecaca;border-top:none;border-radius:0 0 12px 12px;margin-bottom:16px;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead style="background:#fef2f2;">
                    <tr>
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Rank</th>
                        <th style="text-align:left;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Roll</th>
                        <th style="text-align:left;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Student</th>
                        @foreach($analyticsSubjects as $subj)
                            <th style="text-align:center;padding:10px 6px;font-size:10px;color:#64748b;font-weight:700;text-transform:uppercase;">{{ $subj }}</th>
                        @endforeach
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Total</th>
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Avg %</th>
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Division</th>
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Failed In</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($failRankings as $r)
                    <tr style="border-top:1px solid #f1f5f9;">
                        <td style="text-align:center;padding:10px 10px;"><span style="font-weight:800;color:#b91c1c;">#{{ $r['rank'] }}</span></td>
                        <td style="padding:10px 10px;color:#64748b;">{{ $r['enrollment']?->roll_number ?: '—' }}</td>
                        <td style="padding:10px 10px;font-weight:600;color:#0f172a;">{{ $r['enrollment']?->student?->name ?? '—' }}</td>
                        @foreach($analyticsSubjects as $subj)
                            @php $sd = $r['subjectData'][$subj] ?? null; @endphp
                            <td style="text-align:center;padding:10px 6px;font-size:12px;">
                                @if($sd && $sd['pct'] !== null)
                                    <span style="font-weight:600;">{{ $sd['raw'] }}</span>
                                @else
                                    <span style="color:#e2e8f0;">—</span>
                                @endif
                            </td>
                        @endforeach
                        <td style="text-align:center;padding:10px 10px;font-weight:700;">{{ $r['totalRaw'] }}</td>
                        <td style="text-align:center;padding:10px 10px;font-weight:700;">{{ $r['avgPct'] !== null ? $r['avgPct'].'%' : '—' }}</td>
                        <td style="text-align:center;padding:10px 10px;font-weight:700;color:#475569;">{{ $r['division'] ?? '—' }}</td>
                        <td style="text-align:center;padding:10px 10px;">
                            @if(!empty($r['failedSubjects']))
                                <span style="font-size:10px;color:#b91c1c;font-weight:600;">{{ implode(', ', $r['failedSubjects']) }}</span>
                            @else
                                <span style="color:#94a3b8;">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- School-wide aggregate export --}}
        @if($examId && $year)
        @php
            $_pendingSchool = [];
            $_csByClass = \App\Models\ClassSubject::where('academic_year_id', $year->id)
                ->with('subject')->get()->groupBy('class');
            foreach ($_csByClass as $_cls => $_csRows) {
                $_sections = \App\Models\StudentEnrollment::forActiveYear()->active()
                    ->where('class', $_cls)->select('section')->distinct()->pluck('section');
                foreach ($_sections as $_sec) {
                    $_enrolled = \App\Models\StudentEnrollment::forActiveYear()->active()
                        ->where('class', $_cls)->where('section', $_sec)->count();
                    if ($_enrolled === 0) continue;
                    foreach ($_csRows as $_cs) {
                        $_subj = $_cs->subject?->name;
                        if (!$_subj) continue;
                        $_sub = \App\Models\Mark::where('academic_year_id', $year->id)
                            ->where('exam_id', $examId)->where('class', $_cls)
                            ->where('section', $_sec)->where('subject', $_subj)
                            ->whereNotNull('approved_at')->count();
                        if ($_sub < $_enrolled) {
                            $_pendingSchool[] = $_cls . '-' . $_sec . ' ' . $_subj;
                        }
                    }
                }
            }
            $_schoolReady = empty($_pendingSchool);
        @endphp
        <div style="background:#fff;border-radius:12px;padding:18px 20px;margin-top:16px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
            <div style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:6px;">Export ALL Class Results (School-wide)</div>
            <div style="font-size:12px;color:#64748b;margin-bottom:12px;">
                @if($_schoolReady)
                    All classes complete. Final school-wide results can be exported.
                @else
                    Pending: {{ implode(', ', array_slice($_pendingSchool, 0, 5)) }}{{ count($_pendingSchool) > 5 ? ' (+'.(count($_pendingSchool) - 5).' more)' : '' }}
                @endif
            </div>
            <form method="GET" action="{{ route('admin.marks.bulk-download') }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <input type="hidden" name="exam" value="{{ $examId }}">
                <select name="format" {{ $_schoolReady ? '' : 'disabled' }} style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
                    <option value="pdf">PDF</option>
                    <option value="csv">CSV</option>
                </select>
                <select name="subject_wise" {{ $_schoolReady ? '' : 'disabled' }} style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
                    <option value="1">Subject-wise (detailed)</option>
                    <option value="0">Ranking only (% + CGPA)</option>
                </select>
                <button type="submit" {{ $_schoolReady ? '' : 'disabled' }} title="{{ $_schoolReady ? 'Export every class' : 'School-wide submission incomplete' }}" style="background:{{ $_schoolReady ? 'linear-gradient(135deg,#1e3a5f,#4f46e5)' : '#cbd5e1' }};color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:{{ $_schoolReady ? 'pointer' : 'not-allowed' }};">
                    Export ALL Class Results
                </button>
            </form>
        </div>
        @endif
    @endif
@elseif($view === 'summary')
    {{-- Summary View — ALWAYS school-wide grid --}}
    @if(!$examId || !$year)
        <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">Pick an exam above to see submission status across all classes.</div>
    @else
        <div style="background:#fff;border-radius:12px;padding:12px 16px;margin-bottom:14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;box-shadow:0 1px 3px rgba(0,0,0,.06);">
            <span style="font-size:12px;font-weight:700;color:#64748b;">Bulk Delete:</span>
            <form method="POST" action="{{ route('admin.marks.delete-class') }}" style="display:flex;gap:6px;align-items:center;">
                @csrf
                <input type="hidden" name="exam_id" value="{{ $examId }}">
                <select name="class" required style="border:1px solid #e2e8f0;border-radius:8px;padding:6px 10px;font-size:12px;">
                    <option value="">— pick class —</option>
                    @foreach(\App\Models\Student::classes() as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="customConfirm('Delete ALL marks for this class across all sections and subjects? This cannot be undone.',()=>this.closest('form').submit(),'Delete')"
                        style="background:#fee2e2;color:#b91c1c;border:none;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">🗑 Clear Class</button>
            </form>
        </div>
        @php
            $allClasses = \App\Models\Student::classes();
            $classData = collect();
            foreach ($allClasses as $c) {
                $expected = \App\Models\ClassSubject::where('academic_year_id', $year->id)
                    ->where('class', $c)->with('subject')->get()
                    ->pluck('subject.name')->filter()->values();
                if ($expected->isEmpty()) continue;

                $sections = \App\Models\StudentEnrollment::forActiveYear()->active()
                    ->where('class', $c)->select('section')->distinct()->pluck('section');
                if ($sections->isEmpty()) $sections = collect(['A']);

                foreach ($sections as $sec) {
                    $enrolled = \App\Models\StudentEnrollment::forActiveYear()->active()
                        ->where('class', $c)->where('section', $sec)->count();
                    if ($enrolled === 0) continue;

                    $perSubject = [];
                    foreach ($expected as $subj) {
                        $submittedCount = \App\Models\Mark::where('academic_year_id', $year->id)
                            ->where('exam_id', $examId)->where('class', $c)
                            ->where('section', $sec)->where('subject', $subj)
                            ->whereNotNull('submitted_at')->count();
                        $approvedCount = \App\Models\Mark::where('academic_year_id', $year->id)
                            ->where('exam_id', $examId)->where('class', $c)
                            ->where('section', $sec)->where('subject', $subj)
                            ->whereNotNull('approved_at')->count();
                        $perSubject[$subj] = $submittedCount > 0 && $approvedCount >= $submittedCount;
                    }
                    $doneCount = count(array_filter($perSubject));
                    $totalExp = $expected->count();
                    $classData->push([
                        'class' => $c,
                        'section' => $sec,
                        'expected' => $totalExp,
                        'done' => $doneCount,
                        'enrolled' => $enrolled,
                        'pending' => array_keys(array_filter($perSubject, fn ($v) => !$v)),
                        'complete' => $doneCount >= $totalExp,
                        'subjects' => $perSubject,
                    ]);
                }
            }
        @endphp
        @if($classData->isNotEmpty())
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px;">
            @foreach($classData as $cd)
            <div style="background:#fff;border-radius:12px;padding:16px 18px;box-shadow:0 1px 3px rgba(15,23,42,.06);border:1px solid {{ $cd['complete'] ? '#bbf7d0' : '#e2e8f0' }};">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <span style="font-size:14px;font-weight:700;color:#0f172a;">{{ $cd['class'] }} — Sec {{ $cd['section'] }}</span>
                    <span style="font-size:12px;font-weight:700;color:{{ $cd['complete'] ? '#15803d' : '#92400e' }};">{{ $cd['done'] }}/{{ $cd['expected'] }}</span>
                </div>
                <div style="font-size:10px;color:#64748b;margin-bottom:6px;">{{ $cd['enrolled'] }} students</div>
                @php $pct = $cd['expected'] > 0 ? round($cd['done'] / $cd['expected'] * 100) : 0; @endphp
                <div style="height:6px;background:#f1f5f9;border-radius:99px;overflow:hidden;margin-bottom:8px;">
                    <div style="height:100%;width:{{ $pct }}%;background:{{ $cd['complete'] ? '#22c55e' : '#eab308' }};border-radius:99px;"></div>
                </div>
                @php $sorted = collect($cd['subjects'])->sortKeys(); @endphp
                <div style="display:flex;flex-direction:column;gap:3px;">
                    @foreach($sorted as $subjName => $submitted)
                        <span style="font-size:11px;font-weight:600;color:{{ $submitted ? '#15803d' : '#92400e' }};display:flex;align-items:center;gap:4px;">
                            {{ $submitted ? '✅' : '⏳' }} {{ $subjName }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">
            No class_subjects configured. <a href="{{ route('admin.class-subjects.index') }}" style="color:#0f766e;">Set up class subjects →</a>
        </div>
        @endif
    @endif
@endif
@endsection
