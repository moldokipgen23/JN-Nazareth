@extends('layouts.teacher')
@section('page-title', 'Notes & Assignments')
@section('content')

<div style="margin-bottom:16px;">
    <h1 style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">Notes &amp; Assignments</h1>
    <div style="font-size:12px;color:#64748b;margin-top:2px;">Publish class notes and assignments for students</div>
</div>

{{-- Submit form --}}
<div style="background:#fff;border-radius:12px;padding:16px;margin-bottom:16px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:12px;">Publish New</div>
    <form method="POST" action="{{ route('teacher.notes.store') }}" enctype="multipart/form-data">
        @csrf
        @if($errors->any())
            <div style="background:#fef2f2;color:#991b1b;border-radius:8px;padding:10px 12px;font-size:12px;margin-bottom:10px;">{{ $errors->first() }}</div>
        @endif

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;margin-bottom:10px;">
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Type *</label>
                <select name="type" required style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
                    <option value="note">Class Note</option>
                    <option value="assignment">Assignment</option>
                </select>
            </div>
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Class &amp; Section *</label>
                <select name="class" id="slotClass" required onchange="syncSubjects()" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
                    <option value="">— select —</option>
                    @foreach($slots->unique(fn($s)=>$s->class.'|'.$s->section) as $s)
                        <option value="{{ $s->class }}" data-section="{{ $s->section }}">{{ $s->class }} — Sec {{ $s->section }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="section" id="slotSection">
            </div>
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Subject *</label>
                <select name="subject" id="slotSubject" required style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
                    <option value="">— select class first —</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom:10px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Title *</label>
            <input type="text" name="title" required maxlength="255" placeholder="e.g. Chapter 5: Algebra Basics" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
        </div>

        <div style="margin-bottom:10px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Content (optional if uploading a file)</label>
            <textarea name="content" rows="5" placeholder="Type your notes or assignment instructions here..." style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:8px 10px;font-size:13px;resize:vertical;"></textarea>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Upload File (max 20MB)</label>
                <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" style="font-size:13px;">
            </div>
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Due Date (assignments only)</label>
                <input type="date" name="due_date" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
            </div>
        </div>

        <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:10px 20px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">Publish</button>
    </form>
</div>

{{-- My publications --}}
<div style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;">My Publications ({{ $notes->count() }})</div>

@forelse($notes as $n)
<div style="background:#fff;border-radius:12px;padding:14px 16px;margin-bottom:8px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="display:flex;align-items:start;justify-content:space-between;gap:10px;">
        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;background:{{ $n->type==='assignment' ? '#fef3c7' : '#e0e7ff' }};color:{{ $n->type==='assignment' ? '#92400e' : '#4338ca' }};text-transform:uppercase;">{{ $n->type }}</span>
                <span style="font-size:13px;font-weight:600;color:#0f172a;">{{ $n->title }}</span>
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $n->class }} Sec {{ $n->section }} &middot; {{ $n->subject }} &middot; {{ $n->created_at->format('d M Y') }}</div>
            @if($n->content)<div style="font-size:12px;color:#334155;margin-top:6px;white-space:pre-line;">{{ Str::limit($n->content, 150) }}</div>@endif
            @if($n->file_path)
                <a href="{{ Storage::url($n->file_path) }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;margin-top:6px;font-size:12px;font-weight:600;color:#0f766e;">
                    📎 {{ $n->file_name }}
                </a>
            @endif
            @if($n->due_date)
                <div style="font-size:11px;color:#dc2626;margin-top:4px;">Due: {{ $n->due_date->format('d M Y') }}</div>
            @endif
        </div>
        <div style="flex-shrink:0;">
            <form method="POST" action="{{ route('teacher.notes.destroy', $n) }}" onsubmit="return confirm('Delete this {{ $n->type }}?')">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;color:#dc2626;font-size:11px;font-weight:600;cursor:pointer;">Delete</button>
            </form>
        </div>
    </div>
</div>
@empty
<div style="background:#fff;border-radius:12px;padding:36px 24px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="font-size:32px;opacity:.3;margin-bottom:8px;">📝</div>
    <div style="font-weight:600;color:#475569;">No notes or assignments yet</div>
</div>
@endforelse

<script>
const slots = @json($slots);
function syncSubjects() {
    const sel = document.getElementById('slotClass');
    const opt = sel.options[sel.selectedIndex];
    document.getElementById('slotSection').value = opt?.dataset.section || '';
    const cls = opt?.value || '', sec = opt?.dataset.section || '';
    const subSel = document.getElementById('slotSubject');
    subSel.innerHTML = '<option value="">— select —</option>';
    slots.filter(s => s.class === cls && s.section === sec)
         .forEach(s => { const o = document.createElement('option'); o.value = s.subject; o.textContent = s.subject; subSel.appendChild(o); });
}
</script>
@endsection
