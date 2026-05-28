@extends('layouts.teacher')
@section('page-title','Questions')
@section('content')

<div style="margin-bottom:16px;">
    <h1 style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">Exam Questions</h1>
    <div style="font-size:12px;color:#64748b;margin-top:2px;">Submit question papers or typed questions for admin review</div>
</div>

{{-- Submit form --}}
@if($exams->isNotEmpty())
<div style="background:#fff;border-radius:12px;padding:16px;margin-bottom:16px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:12px;">Submit Question</div>
    <form method="POST" action="{{ route('teacher.questions.store') }}" enctype="multipart/form-data">
        @csrf
        @if($errors->any())<div style="background:#fef2f2;color:#991b1b;border-radius:8px;padding:10px 12px;font-size:12px;margin-bottom:10px;">{{ $errors->first() }}</div>@endif

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;margin-bottom:10px;">
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Exam *</label>
                <select name="exam_id" required style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
                    <option value="">— select —</option>
                    @foreach($exams as $e)<option value="{{ $e->id }}">{{ $e->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Class &amp; Section *</label>
                <select name="class" id="slotClass" required onchange="syncSubjects()" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
                    <option value="">— select —</option>
                    @foreach($slots->unique(fn($s)=>$s->class.'|'.$s->section) as $s)
                        <option value="{{ $s->class }}" data-section="{{ $s->section }}" {{ request('class') === $s->class && request('section') === $s->section ? 'selected' : '' }}>{{ $s->class }} — Sec {{ $s->section }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="section" id="slotSection" value="{{ request('section', '') }}">
            </div>
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Subject *</label>
                <select name="subject" id="slotSubject" required style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
                    <option value="">— select class first —</option>
                </select>
            </div>
        </div>
        <div style="margin-bottom:10px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Question Text (optional)</label>
            <textarea name="question_text" rows="4" placeholder="Type questions here..." style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:8px 10px;font-size:13px;resize:vertical;"></textarea>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Upload File (PDF/image/doc, max 20MB)</label>
                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="font-size:13px;">
            </div>
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Notes</label>
                <input type="text" name="notes" placeholder="Any note for admin" maxlength="1000" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
            </div>
        </div>
        <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:10px 20px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">Submit Question</button>
    </form>
</div>
@else
<div style="background:#fff1f2;border:1px solid #fecaca;border-radius:12px;padding:16px;margin-bottom:16px;">
    <div style="font-size:13px;font-weight:700;color:#991b1b;margin-bottom:2px;">No Active Exams</div>
    <div style="font-size:12px;color:#b91c1c;">Question submission is only available when the admin has created and activated an exam within the submission window. Check back later.</div>
</div>
@endif

{{-- My submissions --}}
<div style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;">My Submissions ({{ $questions->count() }})</div>

@forelse($questions as $q)
<div style="background:#fff;border-radius:12px;padding:14px 16px;margin-bottom:8px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="display:flex;align-items:start;justify-content:space-between;gap:10px;">
        <div style="flex:1;min-width:0;">
            <div style="font-size:13px;font-weight:600;color:#0f172a;">{{ $q->exam?->name }} — {{ $q->subject }}</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $q->class }} Sec {{ $q->section }} &middot; {{ $q->created_at->format('d M Y') }}</div>
            @if($q->question_text)<div style="font-size:12px;color:#334155;margin-top:6px;white-space:pre-line;">{{ Str::limit($q->question_text, 120) }}</div>@endif
            @if($q->file_path)
                <a href="{{ Storage::url($q->file_path) }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;margin-top:6px;font-size:12px;font-weight:600;color:#0f766e;">
                    📎 {{ $q->file_name }}
                </a>
            @endif
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0;">
            <span style="font-size:10px;font-weight:700;padding:3px 9px;border-radius:99px;background:{{ $q->status==='reviewed' ? '#dcfce7' : '#fef3c7' }};color:{{ $q->status==='reviewed' ? '#15803d' : '#92400e' }};">{{ ucfirst($q->status) }}</span>
            <form method="POST" action="{{ route('teacher.questions.destroy', $q) }}" onsubmit="return confirm('Delete?')">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;color:#dc2626;font-size:11px;font-weight:600;cursor:pointer;">Delete</button>
            </form>
        </div>
    </div>
</div>
@empty
<div style="background:#fff;border-radius:12px;padding:36px 24px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="font-size:32px;opacity:.3;margin-bottom:8px;">📄</div>
    <div style="font-weight:600;color:#475569;">No submissions yet</div>
</div>
@endforelse

<script>
const slots = @json($slots);
const prefillSubject = '{{ request('subject', '') }}';
function syncSubjects() {
    const sel = document.getElementById('slotClass');
    const opt = sel.options[sel.selectedIndex];
    document.getElementById('slotSection').value = opt?.dataset.section || '';
    const cls = opt?.value || '', sec = opt?.dataset.section || '';
    const subSel = document.getElementById('slotSubject');
    subSel.innerHTML = '<option value="">— select —</option>';
    slots.filter(s => s.class === cls && s.section === sec)
         .forEach(s => {
             const o = document.createElement('option');
             o.value = s.subject;
             o.textContent = s.subject;
             if (s.subject === prefillSubject) o.selected = true;
             subSel.appendChild(o);
         });
}
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('slotClass').value) syncSubjects();
});
</script>
@endsection