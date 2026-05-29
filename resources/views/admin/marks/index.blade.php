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
    <a href="{{ route('admin.marks.index', ['view' => 'rankings', 'exam' => $examId, 'class' => $class, 'section' => $section]) }}"
       style="padding:10px 20px;font-size:13px;font-weight:700;text-decoration:none;border-bottom:2px solid {{ $view === 'rankings' ? '#0f766e' : 'transparent' }};color:{{ $view === 'rankings' ? '#0f766e' : '#94a3b8' }};margin-bottom:-2px;transition:all .15s;">
        Rankings
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
        <a href="{{ route('admin.marks.export-results', ['exam' => $examId, 'class' => $class, 'section' => $section]) }}" style="background:#0f766e;color:#fff;border:none;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;">
            Export CSV (Ranking)
        </a>
        <a href="{{ route('admin.marks.export-result-cards', ['exam' => $examId, 'class' => $class, 'section' => $section]) }}" style="background:#7c3aed;color:#fff;border:none;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;">
            Download PDFs (ZIP)
        </a>
        <a href="{{ route('admin.marks.gradesheet', ['exam' => $examId, 'class' => $class, 'section' => $section, 'format' => 'pdf']) }}" style="background:#1e3a5f;color:#fff;border:none;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;">
            Gradesheet PDF
        </a>
        <a href="{{ route('admin.marks.gradesheet', ['exam' => $examId, 'class' => $class, 'section' => $section, 'format' => 'csv']) }}" style="background:#fff;color:#1e3a5f;border:1px solid #1e3a5f;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;">
            Gradesheet CSV
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
            @foreach(['total'=>['#f1f5f9','#475569'],'pass'=>['#dcfce7','#15803d'],'fail'=>['#fee2e2','#b91c1c'],'ungraded'=>['#fef3c7','#92400e'],'submitted'=>['#dbeafe','#1d4ed8']] as $key=>$col)
                <div style="background:{{ $col[0] }};color:{{ $col[1] }};border-radius:10px;padding:12px 14px;">
                    <div style="font-size:22px;font-weight:700;line-height:1;">{{ $stats[$key] }}</div>
                    <div style="font-size:11px;font-weight:600;margin-top:4px;text-transform:uppercase;">{{ ucfirst($key) }}</div>
                </div>
            @endforeach
        </div>

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
                        <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Entered By</th>
                        <th style="text-align:right;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Override</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($records as $r)
                    <tr style="border-top:1px solid #f1f5f9;">
                        <td style="padding:10px 14px;color:#475569;">{{ $r->enrollment->roll_number ?: '—' }}</td>
                        <td style="padding:10px 14px;color:#0f172a;font-weight:600;">{{ $r->enrollment->student?->name ?? '—' }}</td>
                        <td style="padding:10px 14px;color:#475569;">{{ $r->theory_marks ?? '—' }}</td>
                        <td style="padding:10px 14px;color:#475569;">{{ $r->assignment_marks ?? '—' }}</td>
                        <td style="padding:10px 14px;font-weight:600;">
                            {{ $r->total_marks ?? $r->obtained_marks ?? '—' }}<span style="color:#94a3b8;font-weight:400;"> / {{ $r->full_marks }}</span>
                        </td>
                        <td style="padding:10px 14px;color:#475569;">{{ $r->percentage() !== null ? $r->percentage().'%' : '—' }}</td>
                        <td style="padding:10px 14px;color:#475569;">{{ $r->grade ?: '—' }}</td>
                        <td style="padding:10px 14px;">
                            @php
                                $s = $r->status();
                                $_bg = ['pass'=>'#dcfce7','fail'=>'#fee2e2','ungraded'=>'#f1f5f9'][$s];
                                $_fg = ['pass'=>'#15803d','fail'=>'#b91c1c','ungraded'=>'#94a3b8'][$s];
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
                        <td style="padding:10px 14px;color:#64748b;font-size:12px;">{{ $r->enteredBy?->name ?? '—' }}</td>
                        <td style="padding:10px 14px;text-align:right;">
                            <div style="display:flex;gap:4px;justify-content:flex-end;align-items:center;flex-wrap:wrap;">
                                <form method="POST" action="{{ route('admin.marks.update', $r) }}" style="display:flex;gap:4px;align-items:center;">
                                    @csrf @method('PUT')
                                    <input type="number" step="0.01" min="0" max="{{ $r->full_marks }}" name="total_marks" value="{{ $r->total_marks ?? $r->obtained_marks }}" style="width:60px;padding:4px 6px;border:1px solid #e2e8f0;border-radius:6px;font-size:11px;text-align:center;">
                                    <input type="text" name="grade" value="{{ $r->grade }}" maxlength="5" placeholder="grade" style="width:40px;padding:4px 6px;border:1px solid #e2e8f0;border-radius:6px;font-size:11px;text-align:center;">
                                    <button type="submit" style="background:#0f766e;color:#fff;border:none;padding:4px 8px;border-radius:6px;font-size:10px;font-weight:600;cursor:pointer;">Save</button>
                                </form>
                                @if($r->submitted_at)
                                <form method="POST" action="{{ route('admin.marks.reset-submission', $r) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" style="background:#fef3c7;color:#92400e;border:none;padding:4px 8px;border-radius:6px;font-size:10px;font-weight:600;cursor:pointer;" onclick="return confirm('Reset submission for this student?')">Reset</button>
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
                <div style="background:{{ $ss->total === $ss->submitted_count ? '#f0fdf4' : '#fef3c7' }};border-radius:8px;padding:8px 12px;">
                    <div style="font-size:12px;font-weight:600;color:#0f172a;">{{ $ss->subject }}</div>
                    <div style="font-size:11px;color:#64748b;">{{ $ss->submitted_count }}/{{ $ss->total }} submitted</div>
                    @if($ss->total === $ss->submitted_count)
                        <span style="font-size:10px;color:#15803d;font-weight:600;">✅ Complete</span>
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
        <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">Pick exam + class + subject above.</div>
    @endif
@elseif($view === 'rankings')
    {{-- Rankings View --}}
    @if(!$examId || !$class)
        <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">Pick exam + class above to view rankings.</div>
    @elseif(!$allSubmitted)
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:36px 20px;text-align:center;">
            <div style="font-size:28px;opacity:.5;margin-bottom:8px;">⏳</div>
            <div style="font-weight:700;color:#92400e;font-size:15px;margin-bottom:6px;">Results not yet available</div>
            <div style="font-size:12px;color:#a16207;margin-bottom:8px;">The following subjects still have unsubmitted marks. Rankings will appear once all teachers submit.</div>
            <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;">
                @foreach($pendingSubjects as $ps)
                    <span style="background:#fee2e2;color:#b91c1c;font-size:11px;font-weight:700;padding:4px 12px;border-radius:99px;">{{ $ps }}</span>
                @endforeach
            </div>
        </div>
    @elseif($rankings->isEmpty() && empty($failRankings))
        <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">No marks found for this combination.</div>
    @else
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
            <span style="font-size:13px;font-weight:700;color:#15803d;">✅ Pass — Ranked ({{ $passRankings->count() }})</span>
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
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Avg %</th>
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">CGPA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($passRankings as $r)
                    <tr style="border-top:1px solid #f1f5f9;">
                        <td style="text-align:center;padding:10px 10px;font-weight:800;color:#0f766e;">#{{ $r['rank'] }}</td>
                        <td style="padding:10px 10px;color:#64748b;">{{ $r['enrollment']->roll_number ?: '—' }}</td>
                        <td style="padding:10px 10px;font-weight:600;color:#0f172a;">{{ $r['enrollment']->student?->name ?? '—' }}</td>
                        @foreach($analyticsSubjects as $subj)
                            @php $sd = $r['subjectData'][$subj] ?? null; @endphp
                            <td style="text-align:center;padding:10px 6px;font-size:12px;">
                                @if($sd)
                                    <span style="font-weight:600;">{{ $sd['pct'] !== null ? $sd['pct'].'%' : '—' }}</span>
                                    <span style="font-size:10px;color:#94a3b8;">{{ $sd['grade'] ?? '' }}</span>
                                @else
                                    <span style="color:#cbd5e1;">—</span>
                                @endif
                            </td>
                        @endforeach
                        <td style="text-align:center;padding:10px 10px;font-weight:700;">{{ $r['avgPct'] !== null ? $r['avgPct'].'%' : '—' }}</td>
                        <td style="text-align:center;padding:10px 10px;font-weight:700;color:#0f766e;">{{ $r['cgpa'] !== null ? number_format($r['cgpa'], 2) : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- FAIL SECTION --}}
        @if(!empty($failRankings))
        <div style="background:#fef2f2;border-radius:12px 12px 0 0;padding:10px 16px;border:1px solid #fecaca;border-bottom:none;">
            <span style="font-size:13px;font-weight:700;color:#b91c1c;">❌ Needs Improvement — No Rank ({{ count($failRankings) }})</span>
        </div>
        <div style="background:#fff;overflow-x:auto;border:1px solid #fecaca;border-top:none;border-radius:0 0 12px 12px;margin-bottom:16px;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead style="background:#fef2f2;">
                    <tr>
                        <th style="text-align:left;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Roll</th>
                        <th style="text-align:left;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Student</th>
                        @foreach($analyticsSubjects as $subj)
                            <th style="text-align:center;padding:10px 6px;font-size:10px;color:#64748b;font-weight:700;text-transform:uppercase;">{{ $subj }}</th>
                        @endforeach
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Avg %</th>
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Failed In</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($failRankings as $r)
                    <tr style="border-top:1px solid #f1f5f9;">
                        <td style="padding:10px 10px;color:#64748b;">{{ $r['enrollment']->roll_number ?: '—' }}</td>
                        <td style="padding:10px 10px;font-weight:600;color:#0f172a;">{{ $r['enrollment']->student?->name ?? '—' }}</td>
                        @foreach($analyticsSubjects as $subj)
                            @php $sd = $r['subjectData'][$subj] ?? null; @endphp
                            <td style="text-align:center;padding:10px 6px;font-size:12px;">
                                @if($sd && $sd['pct'] !== null)
                                    <span style="font-weight:600;">{{ $sd['pct'].'%' }}</span>
                                    <span style="font-size:10px;color:#94a3b8;">{{ $sd['grade'] ?? '' }}</span>
                                @else
                                    <span style="color:#e2e8f0;">—</span>
                                @endif
                            </td>
                        @endforeach
                        <td style="text-align:center;padding:10px 10px;font-weight:700;">{{ $r['avgPct'] !== null ? $r['avgPct'].'%' : '—' }}</td>
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
    @elseif($passRankings->isEmpty() && empty($failRankings))
        <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">No marks found for this combination.</div>
    @else
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
            <div style="background:#f0fdf4;color:#15803d;border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:10px;">
                <div><span style="font-size:20px;font-weight:700;">{{ $passRankings->count() }}</span> <span style="font-size:12px;">Pass</span></div>
            </div>
            <div style="background:#fee2e2;color:#b91c1c;border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:10px;">
                <div><span style="font-size:20px;font-weight:700;">{{ count($failRankings) }}</span> <span style="font-size:12px;">Needs Improvement</span></div>
            </div>
        </div>

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
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Avg %</th>
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">CGPA</th>
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
                        <td style="padding:10px 10px;color:#64748b;">{{ $r['enrollment']->roll_number ?: '—' }}</td>
                        <td style="padding:10px 10px;font-weight:600;color:#0f172a;">{{ $r['enrollment']->student?->name ?? '—' }}</td>
                        @foreach($analyticsSubjects as $subj)
                            @php $sd = $r['subjectData'][$subj] ?? null; @endphp
                            <td style="text-align:center;padding:10px 6px;font-size:12px;">
                                @if($sd)
                                    <span style="font-weight:600;">{{ $sd['pct'] !== null ? $sd['pct'].'%' : '—' }}</span>
                                    <span style="font-size:10px;color:#94a3b8;">{{ $sd['grade'] ?? '' }}</span>
                                @else
                                    <span style="color:#cbd5e1;">—</span>
                                @endif
                            </td>
                        @endforeach
                        <td style="text-align:center;padding:10px 10px;font-weight:700;">{{ $r['avgPct'] !== null ? $r['avgPct'].'%' : '—' }}</td>
                        <td style="text-align:center;padding:10px 10px;font-weight:700;color:#0f766e;">{{ $r['cgpa'] !== null ? number_format($r['cgpa'], 2) : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- FAIL SECTION --}}
        @if(!empty($failRankings))
        <div style="background:#fef2f2;border-radius:12px 12px 0 0;padding:10px 16px;border:1px solid #fecaca;border-bottom:none;">
            <span style="font-size:13px;font-weight:700;color:#b91c1c;">❌ Needs Improvement — No Rank</span>
        </div>
        <div style="background:#fff;overflow-x:auto;border:1px solid #fecaca;border-top:none;border-radius:0 0 12px 12px;margin-bottom:16px;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead style="background:#fef2f2;">
                    <tr>
                        <th style="text-align:left;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Roll</th>
                        <th style="text-align:left;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Student</th>
                        @foreach($analyticsSubjects as $subj)
                            <th style="text-align:center;padding:10px 6px;font-size:10px;color:#64748b;font-weight:700;text-transform:uppercase;">{{ $subj }}</th>
                        @endforeach
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Avg %</th>
                        <th style="text-align:center;padding:10px 10px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Failed In</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($failRankings as $r)
                    <tr style="border-top:1px solid #f1f5f9;">
                        <td style="padding:10px 10px;color:#64748b;">{{ $r['enrollment']->roll_number ?: '—' }}</td>
                        <td style="padding:10px 10px;font-weight:600;color:#0f172a;">{{ $r['enrollment']->student?->name ?? '—' }}</td>
                        @foreach($analyticsSubjects as $subj)
                            @php $sd = $r['subjectData'][$subj] ?? null; @endphp
                            <td style="text-align:center;padding:10px 6px;font-size:12px;">
                                @if($sd && $sd['pct'] !== null)
                                    <span style="font-weight:600;">{{ $sd['pct'].'%' }}</span>
                                    <span style="font-size:10px;color:#94a3b8;">{{ $sd['grade'] ?? '' }}</span>
                                @else
                                    <span style="color:#e2e8f0;">—</span>
                                @endif
                            </td>
                        @endforeach
                        <td style="text-align:center;padding:10px 10px;font-weight:700;">{{ $r['avgPct'] !== null ? $r['avgPct'].'%' : '—' }}</td>
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

        {{-- Bulk download all classes --}}
        @if($examId)
        <div style="background:#fff;border-radius:12px;padding:18px 20px;margin-top:16px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
            <div style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:6px;">Download All Classes</div>
            <div style="font-size:12px;color:#64748b;margin-bottom:12px;">Generate result sheets for every class in this exam. Each class gets its own separate file inside a ZIP.</div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <form method="GET" action="{{ route('admin.marks.bulk-download') }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    <input type="hidden" name="exam" value="{{ $examId }}">
                    <select name="format" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
                        <option value="pdf">PDF</option>
                        <option value="csv">CSV</option>
                    </select>
                    <select name="subject_wise" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
                        <option value="1">Subject-wise (detailed)</option>
                        <option value="0">Ranking only (% + CGPA)</option>
                    </select>
                    <button type="submit" style="background:linear-gradient(135deg,#1e3a5f,#4f46e5);color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                        Download ZIP
                    </button>
                </form>
            </div>
        </div>
        @endif
    @endif
@elseif($view === 'summary')
    {{-- Summary View --}}
    @if(!$examId || !$class || !$section)
        <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">Pick exam + class + section above.</div>
    @else
        @if($submissionStatus->isNotEmpty())
        <div style="background:#fff;border-radius:12px;padding:16px 18px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
            <div style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:12px;">Submission Status</div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;">
                @foreach($submissionStatus as $ss)
                @php
                    $noMarks = $ss->total === 0;
                    $allDone = !$noMarks && $ss->submitted_count === $ss->expected && $ss->total === $ss->expected;
                    $_bg = $noMarks ? '#fef2f2' : ($allDone ? '#f0fdf4' : '#fef3c7');
                    $_border = $noMarks ? '#fecaca' : ($allDone ? '#bbf7d0' : '#fde68a');
                @endphp
                <div style="background:{{ $_bg }};border-radius:10px;padding:12px 14px;border:1px solid {{ $_border }};">
                    <div style="font-size:13px;font-weight:700;color:#0f172a;">{{ $ss->subject }}</div>
                    <div style="font-size:11px;color:#64748b;margin-top:4px;">{{ $ss->submitted_count }}/{{ $ss->expected }} students submitted</div>
                    <div style="margin-top:6px;">
                        @if($noMarks)
                            <span style="font-size:10px;color:#b91c1c;font-weight:600;">❌ Not started</span>
                        @elseif($allDone)
                            <span style="font-size:10px;color:#15803d;font-weight:600;">✅ Complete</span>
                        @else
                            <span style="font-size:10px;color:#92400e;font-weight:600;">⏳ {{ $ss->expected - $ss->submitted_count }} pending</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">
            No marks entered yet for this class. Teachers need to enter marks first.
        </div>
        @endif
    @endif
@endif
@endsection
