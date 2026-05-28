@extends('layouts.admin')
@section('page-title','Questions')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <div>
        <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">Exam Questions</h1>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">Review &amp; download question papers submitted by teachers</div>
    </div>
    @if($questions->isNotEmpty())
    <a href="{{ route('admin.questions.export') }}?{{ http_build_query(request()->query()) }}"
       style="background:#fff;color:#6366f1;font-size:12px;font-weight:600;padding:8px 14px;border-radius:8px;text-decoration:none;display:flex;align-items:center;gap:6px;border:1px solid #c7d2fe;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export CSV
    </a>
    @endif
</div>

<form method="GET" style="background:#fff;border-radius:12px;padding:12px 16px;margin-bottom:16px;display:flex;gap:10px;flex-wrap:wrap;align-items:end;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Exam</label>
        <select name="exam" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
            <option value="">All</option>
            @foreach($exams as $e)<option value="{{ $e->id }}" {{ $examId==$e->id?'selected':'' }}>{{ $e->name }}</option>@endforeach
        </select>
    </div>
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Class</label>
        <input type="text" name="class" value="{{ $class ?? '' }}" placeholder="e.g. Class V" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;width:120px;">
    </div>
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Subject</label>
        <input type="text" name="subject" value="{{ $subject ?? '' }}" placeholder="e.g. Mathematics" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;width:130px;">
    </div>
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Status</label>
        <select name="status" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
            <option value="">All</option>
            <option value="pending" {{ $status==='pending'?'selected':'' }}>Pending</option>
            <option value="reviewed" {{ $status==='reviewed'?'selected':'' }}>Reviewed</option>
        </select>
    </div>
    <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Filter</button>
</form>

@if($questions->isEmpty())
<div style="background:#fff;border-radius:12px;padding:48px 24px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="font-size:36px;opacity:.3;margin-bottom:10px;">📄</div>
    <div style="font-weight:600;color:#475569;">No questions submitted yet.</div>
</div>
@else
<div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Exam</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Class / Subject</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Teacher</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Content</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Status</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($questions as $q)
        <tr style="border-top:1px solid #f1f5f9;">
            <td style="padding:10px 14px;font-weight:600;color:#0f172a;">{{ $q->exam?->name }}</td>
            <td style="padding:10px 14px;color:#475569;">{{ $q->class }} Sec {{ $q->section }}<br><span style="font-size:11px;">{{ $q->subject }}</span></td>
            <td style="padding:10px 14px;color:#475569;">{{ $q->submitter?->name }}</td>
            <td style="padding:10px 14px;max-width:240px;">
                @if($q->question_text)<div style="font-size:12px;color:#334155;">{{ Str::limit($q->question_text, 80) }}</div>@endif
                @if($q->file_path)<a href="{{ Storage::url($q->file_path) }}" target="_blank" style="font-size:12px;font-weight:600;color:#0f766e;">📎 {{ $q->file_name }}</a>@endif
            </td>
            <td style="padding:10px 14px;">
                <span style="font-size:10px;font-weight:700;padding:3px 9px;border-radius:99px;background:{{ $q->status==='reviewed'?'#dcfce7':'#fef3c7' }};color:{{ $q->status==='reviewed'?'#15803d':'#92400e' }};">{{ ucfirst($q->status) }}</span>
            </td>
            <td style="padding:10px 14px;">
                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    @if($q->status==='pending')
                    <form method="POST" action="{{ route('admin.questions.review', $q) }}">
                        @csrf
                        <button type="submit" style="background:#dcfce7;color:#15803d;border:1px solid #86efac;padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;">Mark Reviewed</button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('admin.questions.destroy', $q) }}" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none;border:none;color:#dc2626;font-size:11px;font-weight:600;cursor:pointer;">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
