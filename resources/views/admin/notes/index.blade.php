@extends('layouts.admin')
@section('page-title', 'Notes & Assignments')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <div>
        <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">Notes &amp; Assignments</h1>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">
            {{ $year ? $year->name : 'No active year' }}
        </div>
    </div>
</div>

{{-- Filter bar --}}
<form method="GET" style="background:#fff;border-radius:12px;padding:14px 16px;margin-bottom:16px;display:flex;gap:10px;flex-wrap:wrap;align-items:end;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Class / Section</label>
        <select name="class" onchange="syncSection(this.form)" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;min-width:160px;">
            <option value="">— all —</option>
            @foreach($slots as $s)
                <option value="{{ $s->class }}" data-section="{{ $s->section }}"
                    {{ $class === $s->class && $section === $s->section ? 'selected' : '' }}>
                    {{ $s->class }} — Sec {{ $s->section }}
                </option>
            @endforeach
        </select>
        <input type="hidden" name="section" value="{{ $section }}">
    </div>
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Type</label>
        <select name="type" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
            <option value="">— all —</option>
            <option value="note" {{ $type === 'note' ? 'selected' : '' }}>Class Note</option>
            <option value="assignment" {{ $type === 'assignment' ? 'selected' : '' }}>Assignment</option>
        </select>
    </div>
    <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Filter</button>
</form>

<script>
function syncSection(form) {
    const sel = form.querySelector('select[name="class"]');
    const opt = sel.options[sel.selectedIndex];
    form.querySelector('input[name="section"]').value = opt?.dataset.section || '';
}
</script>

{{-- List --}}
@if($notes->isEmpty())
    <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        No notes or assignments found.
    </div>
@else
    <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.04em;">Type</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.04em;">Title</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.04em;">Class</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.04em;">Subject</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.04em;">Teacher</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.04em;">Date</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.04em;">File</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.04em;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($notes as $n)
                <tr style="border-top:1px solid #f1f5f9;">
                    <td style="padding:10px 14px;">
                        <span style="display:inline-block;font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;background:{{ $n->type==='assignment' ? '#fef3c7' : '#e0e7ff' }};color:{{ $n->type==='assignment' ? '#92400e' : '#4338ca' }};text-transform:uppercase;">{{ $n->type }}</span>
                    </td>
                    <td style="padding:10px 14px;color:#0f172a;font-weight:600;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $n->title }}">
                        {{ $n->title }}
                        @if($n->due_date)
                            <span style="display:block;font-size:10px;color:#dc2626;font-weight:500;margin-top:2px;">Due {{ $n->due_date->format('d M') }}</span>
                        @endif
                    </td>
                    <td style="padding:10px 14px;color:#475569;">{{ $n->class }} {{ $n->section }}</td>
                    <td style="padding:10px 14px;color:#475569;">{{ $n->subject }}</td>
                    <td style="padding:10px 14px;color:#475569;">{{ $n->submitter?->name ?? '—' }}</td>
                    <td style="padding:10px 14px;color:#64748b;font-size:12px;">{{ $n->created_at->format('d M Y') }}</td>
                    <td style="padding:10px 14px;">
                        @if($n->file_path)
                            <a href="{{ Storage::url($n->file_path) }}" target="_blank" style="font-size:12px;font-weight:600;color:#0f766e;text-decoration:none;">📎 {{ $n->file_name }}</a>
                        @else
                            <span style="color:#94a3b8;">—</span>
                        @endif
                    </td>
                    <td style="padding:10px 14px;">
                        <form method="POST" action="{{ route('admin.notes.destroy', $n) }}" onsubmit="return confirm('Delete this {{ $n->type }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#dc2626;font-size:11px;font-weight:600;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
