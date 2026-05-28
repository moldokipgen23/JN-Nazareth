@extends('layouts.teacher')
@section('page-title','Questions')
@section('content')

<div style="margin-bottom:16px;">
    <h1 style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">Exam Questions</h1>
    <div style="font-size:12px;color:#64748b;margin-top:2px;">Select a subject to view and add questions. Each subject can have multiple questions.</div>
</div>

@php
    $selectedExamId = request('exam_id', old('exam_id'));
    $selectedClass  = request('class', old('class'));
    $selectedSubject = request('subject', old('subject'));
@endphp

{{-- Subject selector form --}}
<div style="background:#fff;border-radius:12px;padding:16px;margin-bottom:16px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:12px;">Select Subject</div>
    <form method="GET" action="{{ route('teacher.questions.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Exam *</label>
            <select name="exam_id" required style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;min-width:160px;">
                <option value="">— select —</option>
                @foreach($exams as $e)
                    <option value="{{ $e->id }}" {{ $selectedExamId == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Class *</label>
            <select name="class" required style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;min-width:140px;">
                <option value="">— select —</option>
                @foreach($slots->pluck('class')->unique() as $c)
                    <option value="{{ $c }}" {{ $selectedClass === $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Subject *</label>
            <select name="subject" id="filterSubject" required style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;min-width:140px;">
                <option value="">— select class first —</option>
            </select>
        </div>
        <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:8px 20px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Load Questions</button>
    </form>
</div>

<script>
const slots = @json($slots);
const prefillSubject = '{{ $selectedSubject }}';
function syncFilterSubjects() {
    const cls = document.querySelector('select[name="class"]').value;
    const sub = document.getElementById('filterSubject');
    sub.innerHTML = '<option value="">— select —</option>';
    slots.filter(s => s.class === cls).forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.subject;
        opt.textContent = s.subject;
        if (s.subject === prefillSubject) opt.selected = true;
        sub.appendChild(opt);
    });
}
document.addEventListener('DOMContentLoaded', function() {
    const cls = document.querySelector('select[name="class"]');
    if (cls) {
        cls.addEventListener('change', syncFilterSubjects);
        if (cls.value) syncFilterSubjects();
    }
});
</script>

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
        <div style="font-weight:700;color:#0f172a;font-size:15px;margin-bottom:4px;">Select a subject to begin</div>
        <div style="font-size:12px;color:#64748b;">Pick an exam, class, and subject then click "Load Questions".</div>
    </div>
@endif

@endsection
