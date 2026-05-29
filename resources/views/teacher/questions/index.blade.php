@extends('layouts.teacher')
@section('page-title','Questions')
@section('content')

<div style="margin-bottom:16px;">
    <h1 style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">Exam Questions</h1>
    <div style="font-size:12px;color:#64748b;margin-top:2px;">Select a subject below to view and add questions.</div>
</div>

@php
    $selectedExamId = request('exam_id', old('exam_id'));
    $selectedClass  = request('class', old('class'));
    $selectedSubject = request('subject', old('subject'));
@endphp

{{-- Subject selector — clickable cards with tick --}}
<div style="margin-bottom:16px;">
    @if($exams->isEmpty() && $slots->isEmpty())
    <div style="background:#fff;border-radius:12px;padding:48px 24px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="font-size:32px;opacity:.3;margin-bottom:8px;">📝</div>
        <div style="font-weight:700;color:#0f172a;font-size:15px;margin-bottom:4px;">No active exams or subjects assigned yet</div>
        <div style="font-size:12px;color:#64748b;">Contact your school administration if you believe this is an error.</div>
    </div>
    @else
    @foreach($exams as $exam)
    <div style="background:#fff;border-radius:12px;padding:12px 14px;margin-bottom:8px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="font-size:13px;font-weight:700;color:#1e3a5f;margin-bottom:8px;">{{ $exam->name }}</div>
        <div style="display:flex;flex-wrap:wrap;gap:6px;">
            @php $examSlots = $slots->unique(fn($s) => $s->class.'|'.$s->subject); @endphp
            @foreach($examSlots as $slot)
                @php
                    $isActive = $selectedExamId == $exam->id && $selectedClass === $slot->class && $selectedSubject === $slot->subject;
                    $url = route('teacher.questions.index', ['exam_id' => $exam->id, 'class' => $slot->class, 'subject' => $slot->subject]);
                @endphp
                <a href="{{ $url }}"
                   style="display:flex;align-items:center;gap:8px;text-decoration:none;padding:8px 12px;border-radius:8px;border:1.5px solid {{ $isActive ? '#0f766e' : '#e2e8f0' }};background:{{ $isActive ? '#f0fdfa' : '#fff' }};transition:all .15s;min-width:140px;flex:1 0 auto;"
                   onmouseover="this.style.borderColor='#0f766e';this.style.background='#f0fdfa'"
                   onmouseout="this.style.borderColor='{{ $isActive ? '#0f766e' : '#e2e8f0' }}';this.style.background='{{ $isActive ? '#f0fdfa' : '#fff' }}'">
                    <div style="width:18px;height:18px;border-radius:4px;border:2px solid {{ $isActive ? '#0f766e' : '#cbd5e1' }};display:flex;align-items:center;justify-content:center;background:{{ $isActive ? '#0f766e' : 'transparent' }};flex-shrink:0;">
                        @if($isActive)
                            <svg width="12" height="12" fill="none" stroke="#fff" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#0f172a;">{{ $slot->subject }}</div>
                        <div style="font-size:11px;color:#64748b;">{{ $slot->class }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endforeach
    @endif
</div>

@if($selectedExamId && $selectedClass && $selectedSubject)
    {{-- Add question form --}}
    <div style="background:#fff;border-radius:12px;padding:16px;margin-bottom:16px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:8px;">
            Add Question to {{ $selectedSubject }} — {{ $selectedClass }}
        </div>
        <form method="POST" action="{{ route('teacher.questions.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="exam_id" value="{{ $selectedExamId }}">
            <input type="hidden" name="class" value="{{ $selectedClass }}">
            <input type="hidden" name="subject" value="{{ $selectedSubject }}">
            <input type="hidden" name="redirect_params" value="exam_id={{ $selectedExamId }}&class={{ urlencode($selectedClass) }}&subject={{ urlencode($selectedSubject) }}">

            @if($errors->any())
            <div style="background:#fef2f2;color:#991b1b;border-radius:8px;padding:10px 12px;font-size:12px;margin-bottom:10px;">{{ $errors->first() }}</div>
            @endif

            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
                <div style="flex:1;min-width:100px;">
                    <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Marks *</label>
                    <input type="number" name="marks" step="0.01" min="0" max="999" required
                           value="{{ old('marks') }}"
                           style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
                </div>
                <div style="flex:1;min-width:140px;">
                    <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Upload File (optional)</label>
                    <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="font-size:13px;">
                </div>
                <div style="flex:1;min-width:100px;">
                    <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Notes (optional)</label>
                    <input type="text" name="notes" placeholder="Note for admin" maxlength="1000" value="{{ old('notes') }}"
                           style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
                </div>
            </div>
            <div style="margin-bottom:10px;">
                <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Question Text</label>
                <textarea name="question_text" rows="2" placeholder="Type the question here..." style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:8px 10px;font-size:13px;resize:vertical;">{{ old('question_text') }}</textarea>
            </div>
            <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">Add Question</button>
        </form>
    </div>

    {{-- Questions list for this subject --}}
    @php $subjectQuestions = $questions->where('subject', $selectedSubject); @endphp
    <div style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;">
        Questions for {{ $selectedSubject }} ({{ $subjectQuestions->count() }})
    </div>

    @forelse($subjectQuestions as $i => $q)
    <div style="background:#fff;border-radius:12px;padding:14px 16px;margin-bottom:8px;box-shadow:0 1px 3px rgba(15,23,42,.06);{{ $q->status === 'revision_needed' ? 'border-left:3px solid #b91c1c;' : '' }}">
        <div style="display:flex;align-items:start;justify-content:space-between;gap:10px;">
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <span style="font-size:13px;font-weight:700;color:#1e3a5f;">Q{{ $i + 1 }}.</span>
                    <span style="font-size:11px;font-weight:700;color:#0f172a;background:#f1f5f9;padding:2px 8px;border-radius:4px;">{{ $q->marks ?? '—' }} mark{{ $q->marks != 1 ? 's' : '' }}</span>
                    <span style="font-size:11px;color:#64748b;">{{ $q->created_at->format('d M Y') }}</span>
                </div>
                @if($q->question_text)
                    <div style="font-size:13px;color:#0f172a;margin-top:6px;white-space:pre-line;">{{ $q->question_text }}</div>
                @endif
                @if($q->file_path)
                    <a href="{{ Storage::url($q->file_path) }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;margin-top:6px;font-size:12px;font-weight:600;color:#0f766e;">📎 {{ $q->file_name }}</a>
                @endif
                @if($q->review_notes && $q->status === 'revision_needed')
                    <div style="margin-top:8px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:8px 10px;">
                        <div style="font-size:11px;font-weight:700;color:#b91c1c;">✏️ Revision requested by {{ $q->reviewer?->name ?? 'Admin' }}</div>
                        <div style="font-size:11px;color:#991b1b;margin-top:4px;">{{ $q->review_notes }}</div>
                    </div>
                    <form method="POST" action="{{ route('teacher.questions.update', $q) }}" enctype="multipart/form-data" style="margin-top:10px;padding-top:10px;border-top:1px solid #e2e8f0;">
                        @csrf
                        <div style="font-size:12px;font-weight:600;color:#0f172a;margin-bottom:6px;">Revise Question</div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:8px;">
                            <div>
                                <label style="display:block;font-size:10px;font-weight:600;color:#64748b;margin-bottom:2px;">Marks</label>
                                <input type="number" name="marks" step="0.01" min="0" value="{{ $q->marks }}" style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px 8px;font-size:12px;">
                            </div>
                            <div>
                                <label style="display:block;font-size:10px;font-weight:600;color:#64748b;margin-bottom:2px;">File (optional)</label>
                                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="font-size:12px;">
                            </div>
                        </div>
                        <textarea name="question_text" rows="2" placeholder="Revised question text..." style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:6px 8px;font-size:12px;resize:vertical;">{{ $q->question_text }}</textarea>
                        <div style="margin-top:6px;display:flex;gap:6px;">
                            <button type="submit" style="background:#0f766e;color:#fff;border:none;padding:6px 14px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">Re-submit</button>
                        </div>
                    </form>
                @endif
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0;">
                @php
                    $statusColors = ['pending'=>['#fef3c7','#92400e'],'revision_needed'=>['#fee2e2','#b91c1c'],'approved'=>['#dcfce7','#15803d']];
                    $sc = $statusColors[$q->status] ?? ['#f1f5f9','#64748b'];
                @endphp
                <span style="font-size:10px;font-weight:700;padding:3px 9px;border-radius:99px;background:{{ $sc[0] }};color:{{ $sc[1] }};">{{ str_replace('_', ' ', ucfirst($q->status)) }}</span>
                @if($q->status !== 'revision_needed')
                <form method="POST" action="{{ route('teacher.questions.destroy', $q) }}" onsubmit="return confirm('Delete this question?')">
                    @csrf @method('DELETE')
                    <input type="hidden" name="exam_id" value="{{ $selectedExamId }}">
                    <input type="hidden" name="class" value="{{ $selectedClass }}">
                    <input type="hidden" name="subject" value="{{ $selectedSubject }}">
                    <button type="submit" style="background:none;border:none;color:#dc2626;font-size:11px;font-weight:600;cursor:pointer;">Delete</button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div style="background:#fff;border-radius:12px;padding:36px 24px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="font-weight:600;color:#475569;">No questions yet for {{ $selectedSubject }}. Add one above.</div>
    </div>
    @endforelse

@else
    <div style="background:#fff;border-radius:12px;padding:48px 24px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="font-size:32px;opacity:.3;margin-bottom:8px;">📝</div>
        <div style="font-weight:700;color:#0f172a;font-size:15px;margin-bottom:4px;">Select a subject above to begin</div>
        <div style="font-size:12px;color:#64748b;">Click any subject card to view questions and add new ones.</div>
    </div>
@endif

@endsection
