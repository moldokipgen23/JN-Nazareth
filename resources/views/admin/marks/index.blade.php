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
            @foreach($subjectList as $s)
                <option value="{{ $s }}" {{ $subject === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
    </div>
    @endif
    <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Filter</button>
    @if($view === 'review' && $examId && $class && $subject)
        <a href="{{ route('admin.marks.export', ['exam' => $examId, 'class' => $class, 'section' => $section, 'subject' => $subject]) }}" style="background:#fff;color:#0f766e;border:1px solid #0f766e;padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;">Export CSV</a>
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
            @foreach(['total'=>['#f1f5f9','#475569'],'pass'=>['#dcfce7','#15803d'],'fail'=>['#fee2e2','#b91c1c'],'ungraded'=>['#fef3c7','#92400e']] as $key=>$col)
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
                        <td style="padding:10px 14px;color:#64748b;font-size:12px;">{{ $r->enteredBy?->name ?? '—' }}</td>
                        <td style="padding:10px 14px;text-align:right;">
                            <form method="POST" action="{{ route('admin.marks.update', $r) }}" style="display:flex;gap:6px;justify-content:flex-end;align-items:center;">
                                @csrf @method('PUT')
                                <input type="number" step="0.01" min="0" max="{{ $r->full_marks }}" name="total_marks" value="{{ $r->total_marks ?? $r->obtained_marks }}" style="width:70px;padding:5px 8px;border:1px solid #e2e8f0;border-radius:6px;font-size:12px;text-align:center;">
                                <input type="text" name="grade" value="{{ $r->grade }}" maxlength="5" placeholder="grade" style="width:50px;padding:5px 8px;border:1px solid #e2e8f0;border-radius:6px;font-size:12px;text-align:center;">
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
        <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">Pick exam + class + subject above.</div>
    @endif
@else
    {{-- Rankings View --}}
    @if(!$examId || !$class)
        <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">Pick exam + class above to view rankings.</div>
    @elseif($rankings->isEmpty())
        <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">No marks found for this combination.</div>
    @else
        {{-- Subject stats --}}
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

        <div style="background:#fff;border-radius:12px;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead style="background:#f8fafc;">
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
                    @foreach($rankings as $r)
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
@endif
@endsection