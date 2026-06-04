@extends('layouts.admin')
@section('page-title', 'Exams')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <div>
        <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">Exams &amp; Terms</h1>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">Define terms / tests / final exams per academic year</div>
    </div>
    <button type="button" onclick="document.getElementById('addExam').style.display='block'" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
        + Add Exam
    </button>
</div>

{{-- Add form (toggle) --}}
<div id="addExam" style="display:none;background:#fff;border-radius:12px;padding:16px;margin-bottom:16px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <form method="POST" action="{{ route('admin.exams.store') }}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;align-items:end;">
        @csrf
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Academic Year</label>
            <select name="academic_year_id" required style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
                @foreach($years as $y)
                    <option value="{{ $y->id }}" {{ $y->is_active ? 'selected' : '' }}>{{ $y->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="grid-column:span 2;">
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Name *</label>
            <input type="text" name="name" required placeholder="e.g. First Term, Unit Test 1, Final Exam"
                   style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Type</label>
            <select name="type" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
                @foreach(\App\Models\Exam::TYPES as $t)
                    <option value="{{ $t }}" {{ $t === 'Custom' ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Code</label>
            <input type="text" name="code" placeholder="UT1" maxlength="40"
                   style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Starts</label>
            <input type="date" name="starts_on" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Ends</label>
            <input type="date" name="ends_on" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Q Subm. Starts</label>
            <input type="date" name="submission_starts_on" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Q Subm. Ends</label>
            <input type="date" name="submission_ends_on" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Order</label>
            <input type="number" name="sort_order" value="0" min="0" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
        </div>
        <button type="submit" style="background:#0f766e;color:#fff;border:none;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Save</button>
    </form>
</div>

@if($exams->isEmpty())
    <div style="background:#fff;border-radius:12px;padding:48px 24px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="font-size:36px;opacity:.3;margin-bottom:10px;">📝</div>
        <div style="font-weight:600;color:#475569;">No exams configured yet</div>
        <div style="font-size:12px;color:#94a3b8;margin-top:6px;">Click "Add Exam" to create your first term.</div>
    </div>
@else
    <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Year</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Name</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Type</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Code</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Dates</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Q Window</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Order</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Active</th>
                    <th style="text-align:right;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($exams as $exam)
                <tr style="border-top:1px solid #f1f5f9;">
                    <td style="padding:10px 14px;color:#64748b;">{{ $exam->academicYear?->name ?? '—' }}</td>
                    <td style="padding:10px 14px;color:#0f172a;font-weight:600;">{{ $exam->name }}</td>
                    <td style="padding:10px 14px;color:#475569;">
                        <span style="background:#f1f5f9;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:600;">{{ $exam->type }}</span>
                    </td>
                    <td style="padding:10px 14px;color:#475569;">{{ $exam->code ?: '—' }}</td>
                    <td style="padding:10px 14px;color:#64748b;font-size:12px;">
                        @if($exam->starts_on && $exam->ends_on)
                            {{ $exam->starts_on->format('d M') }} – {{ $exam->ends_on->format('d M Y') }}
                        @else — @endif
                    </td>
                    <td style="padding:10px 14px;color:#64748b;font-size:12px;">
                        @if($exam->submission_starts_on && $exam->submission_ends_on)
                            {{ $exam->submission_starts_on->format('d M') }} – {{ $exam->submission_ends_on->format('d M Y') }}
                        @else — @endif
                    </td>
                    <td style="padding:10px 14px;color:#475569;">{{ $exam->sort_order }}</td>
                    <td style="padding:10px 14px;">
                        <form method="POST" action="{{ route('admin.exams.toggle', $exam) }}">
                            @csrf
                            <button type="submit" style="border:none;cursor:pointer;background:{{ $exam->is_active ? '#dcfce7' : '#f1f5f9' }};color:{{ $exam->is_active ? '#15803d' : '#94a3b8' }};padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;">
                                {{ $exam->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td style="padding:10px 14px;text-align:right;">
                        <a href="{{ route('admin.exams.marks-config', $exam) }}" style="background:#eef2ff;color:#4338ca;font-size:11px;font-weight:700;padding:4px 10px;border-radius:6px;text-decoration:none;margin-right:6px;">
                            Marks Config
                        </a>
                        <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="button" onclick="customConfirm('Delete this exam? Only possible if no marks are linked.',()=>this.closest('form').submit(),'Delete')" style="background:none;border:none;color:#dc2626;cursor:pointer;font-size:12px;font-weight:600;">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
