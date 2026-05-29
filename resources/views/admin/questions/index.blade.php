@extends('layouts.admin')
@section('page-title','Questions')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <div>
        <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">Exam Questions</h1>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">Click a summary card to filter, or use dropdowns below. Click <strong>View</strong> to see all questions for a subject.</div>
    </div>
    <div style="display:flex;gap:8px;">
        @if($examId && $class && $subject)
        <a href="{{ route('admin.questions.question-paper', ['exam' => $examId, 'class' => $class, 'subject' => $subject]) }}"
           style="background:#1e3a5f;color:#fff;font-size:12px;font-weight:600;padding:8px 14px;border-radius:8px;text-decoration:none;display:flex;align-items:center;gap:6px;">
            Download Question Paper
        </a>
        @endif
        @if(isset($groups) && $groups->isNotEmpty())
        <a href="{{ route('admin.questions.export') }}?{{ http_build_query(request()->query()) }}"
           style="background:#fff;color:#6366f1;font-size:12px;font-weight:600;padding:8px 14px;border-radius:8px;text-decoration:none;display:flex;align-items:center;gap:6px;border:1px solid #c7d2fe;">
            Export CSV
        </a>
        @endif
    </div>
</div>

{{-- Stats cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:10px;margin-bottom:16px;">
    <div style="background:#f1f5f9;color:#1e293b;border-radius:10px;padding:12px 14px;">
        <div style="font-size:22px;font-weight:700;line-height:1;">{{ $stats['total'] ?? 0 }}</div>
        <div style="font-size:11px;font-weight:600;margin-top:4px;text-transform:uppercase;">Total</div>
    </div>
    @foreach(['pending'=>['#fef3c7','#92400e'],'revision_needed'=>['#fee2e2','#b91c1c'],'approved'=>['#dcfce7','#15803d']] as $key=>$col)
        <div style="background:{{ $col[0] }};color:{{ $col[1] }};border-radius:10px;padding:12px 14px;">
            <div style="font-size:22px;font-weight:700;line-height:1;">{{ $stats[$key] ?? 0 }}</div>
            <div style="font-size:11px;font-weight:600;margin-top:4px;text-transform:uppercase;">{{ str_replace('_', ' ', $key) }}</div>
        </div>
    @endforeach
</div>

{{-- Progress Summary (class_subjects based) --}}
@if($classProgress->isNotEmpty())
<div style="margin-bottom:16px;">
    <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:8px;">Progress per Class@if($progressExamId && $exams->firstWhere('id', $progressExamId)) — {{ $exams->firstWhere('id', $progressExamId)->name }}@endif</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:10px;">
        @foreach($classProgress as $cp)
        @php
            $allDone = $cp['approved_count'] === $cp['expected_count'];
            $pct = $cp['expected_count'] > 0 ? round($cp['approved_count'] / $cp['expected_count'] * 100) : 0;
        @endphp
        <div style="background:#fff;border-radius:10px;padding:12px 14px;box-shadow:0 1px 3px rgba(15,23,42,.06);border:1px solid {{ $allDone ? '#bbf7d0' : '#fde68a' }};">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <span style="font-size:13px;font-weight:700;color:#0f172a;">{{ $cp['class'] }}</span>
                <span style="font-size:11px;font-weight:600;color:{{ $allDone ? '#15803d' : '#92400e' }};">{{ $cp['approved_count'] }}/{{ $cp['expected_count'] }}</span>
            </div>
            <div style="height:6px;background:#f1f5f9;border-radius:99px;overflow:hidden;margin-bottom:8px;">
                <div style="height:100%;width:{{ $pct }}%;background:{{ $allDone ? '#22c55e' : '#eab308' }};border-radius:99px;transition:width .3s;"></div>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                @foreach($cp['expected'] as $subj)
                    @php $done = in_array($subj, $cp['approved']); @endphp
                    <span style="font-size:9px;font-weight:600;padding:2px 8px;border-radius:99px;background:{{ $done ? '#dcfce7' : '#fef3c7' }};color:{{ $done ? '#15803d' : '#92400e' }};">{{ $subj }}</span>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@elseif($groups->isNotEmpty())
<div style="margin-bottom:16px;">
    <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:8px;">Submission Summary (no class_subjects configured)</div>
    @php $byExam = $groups->groupBy('exam_name'); @endphp
    @foreach($byExam as $examName => $examGroups)
    <div style="background:#fff;border-radius:10px;padding:12px 14px;margin-bottom:8px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="font-size:12px;font-weight:700;color:#1e3a5f;margin-bottom:6px;">{{ $examName }}</div>
        <div style="display:flex;flex-wrap:wrap;gap:6px;">
            @foreach($examGroups as $g)
            <button type="button" class="summary-card"
                data-exam="{{ $g['exam_id'] }}"
                data-class="{{ $g['class'] }}"
                data-subject="{{ $g['subject'] }}"
                onclick="applyFilter(this)"
                style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:8px 12px;cursor:pointer;text-align:left;min-width:180px;flex:1 0 auto;transition:all .15s;"
                onmouseover="this.style.borderColor='#0f766e';this.style.background='#f0fdfa'"
                onmouseout="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc'">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#0f172a;">{{ $g['subject'] }}</div>
                        <div style="font-size:11px;color:#64748b;">{{ $g['class'] }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:16px;font-weight:700;color:#0f172a;">{{ $g['total'] }}</div>
                        <div style="font-size:9px;color:#94a3b8;text-transform:uppercase;">submitted</div>
                    </div>
                </div>
                <div style="display:flex;gap:4px;margin-top:5px;">
                    @php $sc = $g['status_counts']; @endphp
                    @if(($sc['approved'] ?? 0) > 0)
                        <span style="font-size:9px;font-weight:700;padding:1px 6px;border-radius:99px;background:#dcfce7;color:#15803d;">{{ $sc['approved'] }} approved</span>
                    @endif
                    @if(($sc['pending'] ?? 0) > 0)
                        <span style="font-size:9px;font-weight:700;padding:1px 6px;border-radius:99px;background:#fef3c7;color:#92400e;">{{ $sc['pending'] }} pending</span>
                    @endif
                    @if(($sc['revision_needed'] ?? 0) > 0)
                        <span style="font-size:9px;font-weight:700;padding:1px 6px;border-radius:99px;background:#fee2e2;color:#b91c1c;">{{ $sc['revision_needed'] }} revision</span>
                    @endif
                </div>
            </button>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endif

<form method="GET" class="filter-form" style="background:#fff;border-radius:12px;padding:12px 16px;margin-bottom:16px;display:flex;gap:10px;flex-wrap:wrap;align-items:end;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Exam</label>
        <select name="exam" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
            <option value="">All</option>
            @foreach($exams as $e)<option value="{{ $e->id }}" {{ $examId==$e->id?'selected':'' }}>{{ $e->name }}</option>@endforeach
        </select>
    </div>
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Class</label>
        <select name="class" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;min-width:130px;">
            <option value="">All</option>
            @foreach($availableClasses as $c)
                <option value="{{ $c }}" {{ $class === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Subject</label>
        <select name="subject" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;min-width:130px;">
            <option value="">All</option>
            @foreach($availableSubjects as $s)
                <option value="{{ $s }}" {{ $subject === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Status</label>
        <select name="status" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
            <option value="">All</option>
            <option value="pending" {{ $status==='pending'?'selected':'' }}>Pending</option>
            <option value="revision_needed" {{ $status==='revision_needed'?'selected':'' }}>Revision Needed</option>
            <option value="approved" {{ $status==='approved'?'selected':'' }}>Approved</option>
        </select>
    </div>
    <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Filter</button>
    @if($examId || $class || $subject || $status)
    <a href="{{ route('admin.questions.index') }}" style="background:#fff;color:#64748b;border:1px solid #e2e8f0;padding:8px 14px;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:4px;">Clear</a>
    @endif
</form>

@if($groups->isEmpty())
<div style="background:#fff;border-radius:12px;padding:48px 24px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="font-weight:600;color:#475569;">No questions match the selected filters.</div>
</div>
@else
<div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Subject</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Class</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Teacher</th>
                <th style="text-align:center;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Questions</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Status</th>
                <th style="text-align:right;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($groups as $g)
            @php $groupId = 'g'.md5($g['exam_id'].$g['class'].$g['subject']); @endphp
            <tr style="border-top:1px solid #e2e8f0;">
                <td style="padding:12px 14px;font-weight:700;color:#0f172a;">{{ $g['subject'] }}</td>
                <td style="padding:12px 14px;color:#475569;">{{ $g['class'] }}</td>
                <td style="padding:12px 14px;color:#475569;font-size:12px;">{{ $g['teacher'] }}</td>
                <td style="padding:12px 14px;text-align:center;font-weight:700;">{{ $g['total'] }}</td>
                <td style="padding:12px 14px;">
                    <div style="display:flex;gap:4px;flex-wrap:wrap;">
                        @foreach(['approved'=>'#dcfce7;#15803d','pending'=>'#fef3c7;#92400e','revision_needed'=>'#fee2e2;#b91c1c'] as $st=>$cl)
                            @php $c = explode(';', $cl); @endphp
                            @if(($g['status_counts'][$st] ?? 0) > 0)
                                <span style="font-size:10px;font-weight:700;padding:2px 7px;border-radius:99px;background:{{ $c[0] }};color:{{ $c[1] }};">
                                    {{ $g['status_counts'][$st] }} {{ ucfirst(str_replace('_', ' ', $st)) }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </td>
                <td style="padding:12px 14px;text-align:right;">
                    <button onclick="toggleGroup('{{ $groupId }}')" style="background:#f1f5f9;color:#0f172a;border:none;padding:5px 12px;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">
                        View Questions ▼
                    </button>
                </td>
            </tr>
            {{-- Expanded detail row --}}
            <tr id="{{ $groupId }}" style="display:none;">
                <td colspan="6" style="padding:0;background:#f8fafc;">
                    <div style="padding:12px 20px;border-top:1px solid #e2e8f0;">
                        {{-- Bulk actions --}}
                        @if($g['status_counts']['pending'] > 0)
                        <div style="margin-bottom:10px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                            <span style="font-size:12px;font-weight:600;color:#475569;">Bulk Actions:</span>
                            <form method="POST" action="{{ route('admin.questions.bulk-approve') }}" style="display:inline;">
                                @csrf
                                @foreach($g['questions'] as $q)
                                    @if($q->status === 'pending')
                                        <input type="hidden" name="question_ids[]" value="{{ $q->id }}">
                                    @endif
                                @endforeach
                                <button type="submit" style="background:#dcfce7;color:#15803d;border:1px solid #86efac;padding:4px 12px;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;">
                                    ✅ Approve All Pending
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.questions.bulk-revision') }}" style="display:flex;gap:6px;align-items:center;">
                                @csrf
                                @foreach($g['questions'] as $q)
                                    @if($q->status === 'pending')
                                        <input type="hidden" name="question_ids[]" value="{{ $q->id }}">
                                    @endif
                                @endforeach
                                <input type="text" name="review_notes" placeholder="Reason for revision..." required style="border:1px solid #e2e8f0;border-radius:6px;padding:4px 8px;font-size:11px;min-width:180px;">
                                <button type="submit" style="background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;padding:4px 12px;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;">
                                    Send All Back
                                </button>
                            </form>
                        </div>
                        @endif

                        {{-- Individual questions --}}
                        @foreach($g['questions'] as $i => $q)
                        <div style="background:#fff;border-radius:8px;padding:10px 14px;margin-bottom:6px;border:1px solid #e2e8f0;{{ $q->status === 'revision_needed' ? 'border-left:3px solid #b91c1c;' : '' }}">
                            <div style="display:flex;align-items:start;justify-content:space-between;gap:8px;">
                                <div style="flex:1;">
                                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                                        <span style="font-weight:700;color:#1e3a5f;font-size:12px;">Q{{ $i + 1 }}.</span>
                                        <span style="font-size:11px;font-weight:700;background:#f1f5f9;padding:1px 7px;border-radius:4px;">{{ $q->marks ?? '—' }} mark{{ $q->marks != 1 ? 's' : '' }}</span>
                                        @php
                                            $_sc = ['pending'=>['#fef3c7','#92400e'],'revision_needed'=>['#fee2e2','#b91c1c'],'approved'=>['#dcfce7','#15803d']];
                                            $_c = $_sc[$q->status] ?? ['#f1f5f9','#64748b'];
                                        @endphp
                                        <span style="font-size:9px;font-weight:700;padding:2px 7px;border-radius:99px;background:{{ $_c[0] }};color:{{ $_c[1] }};">{{ str_replace('_', ' ', ucfirst($q->status)) }}</span>
                                    </div>
                                    @if($q->question_text)
                                        <div style="font-size:12px;color:#334155;margin-top:4px;white-space:pre-line;">{{ $q->question_text }}</div>
                                    @endif
                                    @if($q->file_path)
                                        <a href="{{ Storage::url($q->file_path) }}" target="_blank" style="font-size:11px;font-weight:600;color:#0f766e;">📎 {{ $q->file_name }}</a>
                                    @endif
                                    @if($q->review_notes && $q->status === 'revision_needed')
                                        <div style="font-size:11px;color:#b91c1c;background:#fee2e2;padding:4px 8px;border-radius:4px;margin-top:4px;">Note: {{ $q->review_notes }}</div>
                                    @endif
                                </div>
                                <div style="display:flex;gap:4px;flex-shrink:0;align-items:center;">
                                    @if($q->status === 'pending')
                                    <form method="POST" action="{{ route('admin.questions.approve', $q) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" style="background:#dcfce7;color:#15803d;border:none;padding:3px 8px;border-radius:4px;font-size:10px;font-weight:700;cursor:pointer;">Approve</button>
                                    </form>
                                    <button type="button" onclick="showRevForm('{{ $q->id }}')" style="background:#fee2e2;color:#b91c1c;border:none;padding:3px 8px;border-radius:4px;font-size:10px;font-weight:700;cursor:pointer;">Send Back</button>
                                    @endif
                                    <form method="POST" action="{{ route('admin.questions.destroy', $q) }}" onsubmit="return confirm('Delete?')" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" style="background:none;border:none;color:#dc2626;font-size:10px;font-weight:600;cursor:pointer;">🗑️</button>
                                    </form>
                                </div>
                            </div>
                            {{-- Inline revision form --}}
                            @if($q->status === 'pending')
                            <form id="rev-form-{{ $q->id }}" method="POST" action="{{ route('admin.questions.revision', $q) }}" style="display:none;margin-top:8px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:8px;">
                                @csrf
                                <textarea name="review_notes" rows="2" placeholder="What needs to change?" style="width:100%;border:1px solid #e2e8f0;border-radius:4px;padding:4px 8px;font-size:11px;" required></textarea>
                                <div style="display:flex;gap:6px;margin-top:4px;">
                                    <button type="submit" style="background:#b91c1c;color:#fff;border:none;padding:3px 10px;border-radius:4px;font-size:10px;font-weight:600;cursor:pointer;">Send</button>
                                    <button type="button" onclick="document.getElementById('rev-form-{{ $q->id }}').style.display='none'" style="background:none;border:none;color:#64748b;font-size:10px;cursor:pointer;">Cancel</button>
                                </div>
                            </form>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

<script>
function toggleGroup(id) {
    var row = document.getElementById(id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
function showRevForm(id) {
    var f = document.getElementById('rev-form-' + id);
    if (f) f.style.display = 'block';
}
function applyFilter(btn) {
    document.querySelector('select[name="exam"]').value = btn.dataset.exam;
    document.querySelector('select[name="class"]').value = btn.dataset.class;
    document.querySelector('select[name="subject"]').value = btn.dataset.subject;
    document.querySelector('select[name="status"]').value = '';
    document.querySelector('.filter-form').submit();
}
</script>
@endsection
