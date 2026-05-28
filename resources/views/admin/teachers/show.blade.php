@extends('layouts.admin')
@section('page-title', $teacher->name)

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
    <div style="display:flex;align-items:center;gap:12px;">
        <a href="{{ route('admin.teachers.index') }}"
           style="padding:8px;border-radius:8px;color:#94a3b8;text-decoration:none;display:flex;"
           onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">{{ $teacher->name }}</h2>
            <p style="font-size:12px;color:#64748b;margin:2px 0 0;">{{ $teacher->designation ?: 'Teacher' }}</p>
        </div>
    </div>
    <a href="{{ route('admin.teachers.edit', $teacher) }}"
       style="background:linear-gradient(135deg,#0f766e,#14b8a6);color:#fff;font-size:13px;font-weight:600;padding:10px 18px;border-radius:10px;text-decoration:none;display:flex;align-items:center;gap:7px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit
    </a>
</div>

{{-- Profile Card --}}
<div style="background:#fff;border-radius:14px;box-shadow:0 1px 8px rgba(0,0,0,.06);border:1px solid #f1f5f9;padding:20px 24px;margin-bottom:20px;display:flex;align-items:center;gap:18px;flex-wrap:wrap;">
    <div style="width:56px;height:56px;border-radius:50%;overflow:hidden;background:linear-gradient(135deg,#14b8a6,#0f766e);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:20px;flex-shrink:0;">
        @if($teacher->photo)
            <img src="{{ \App\Helpers\Settings::storageUrl($teacher->photo) }}" style="width:100%;height:100%;object-fit:cover;">
        @else
            {{ strtoupper(substr($teacher->name, 0, 1)) }}
        @endif
    </div>
    <div>
        <div style="font-weight:600;color:#0f172a;font-size:14px;">{{ $teacher->name }}</div>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $teacher->designation ?: 'Teacher' }}</div>
        @if($teacher->subjects)
            <div style="font-size:11px;color:#94a3b8;margin-top:1px;">{{ $teacher->subjects }}</div>
        @endif
    </div>
    <div>
        @if($teacher->phone)
            <div style="font-size:12px;color:#475569;"><strong>Phone:</strong> {{ $teacher->phone }}</div>
        @endif
        @if($teacher->email)
            <div style="font-size:12px;color:#475569;"><strong>Email:</strong> {{ $teacher->email }}</div>
        @endif
    </div>
    <div>
        @if($teacher->classes)
            <div style="font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Classes:</div>
            @foreach($teacher->classes as $c)
                <span style="background:#ecfeff;color:#0e7490;padding:2px 9px;border-radius:99px;font-size:11px;font-weight:600;display:inline-block;margin:1px;">{{ $c }}</span>
            @endforeach
        @endif
    </div>
</div>

{{-- Year-wise Status --}}
<div style="background:#fff;border-radius:14px;box-shadow:0 1px 8px rgba(0,0,0,.06);border:1px solid #f1f5f9;overflow:hidden;">
    <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <h3 style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">Year-wise Status</h3>
        <button type="button" onclick="document.getElementById('status-form').classList.toggle('hidden')"
                style="background:#f0fdfa;color:#0f766e;border:1px solid #99f6e4;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;">
            Update Status for {{ (app('workingYear'))?->name ?? 'Current Year' }}
        </button>
    </div>

    {{-- Update status form --}}
    <div id="status-form" class="hidden" style="padding:16px 20px;background:#f0fdfa;border-bottom:1px solid #99f6e4;">
        <form method="POST" action="{{ route('admin.teachers.year-status', $teacher) }}"
              style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            @csrf
            <div>
                <label style="display:block;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:3px;">Academic Year</label>
                <select name="academic_year_id" required
                        style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;background:#fff;">
                    @foreach($years as $yr)
                        <option value="{{ $yr->id }}" {{ (app('workingYear')?->id ?? '') == $yr->id ? 'selected' : '' }}>
                            {{ $yr->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:3px;">Status</label>
                <select name="status" required
                        style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;background:#fff;">
                    @foreach(['active'=>'Active','resigned'=>'Resigned','on_leave'=>'On Leave','transferred'=>'Transferred'] as $val=>$lbl)
                        <option value="{{ $val }}">{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:3px;">Effective From</label>
                <input type="date" name="effective_from"
                       style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
            </div>
            <div>
                <label style="display:block;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:3px;">Effective To</label>
                <input type="date" name="effective_to"
                       style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
            </div>
            <div>
                <label style="display:block;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:3px;">Remarks</label>
                <input type="text" name="remarks" maxlength="500" placeholder="Reason / note…"
                       style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;width:160px;">
            </div>
            <button type="submit"
                    style="background:#0f766e;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">
                Save Status
            </button>
        </form>
    </div>

    {{-- Status table --}}
    @if($teacher->yearStatuses->isNotEmpty())
    <div style="overflow-x:auto;">
        <table style="border-collapse:collapse;font-size:13px;width:100%;">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="text-align:left;padding:10px 20px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Year</th>
                    <th style="text-align:left;padding:10px 20px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Status</th>
                    <th style="text-align:left;padding:10px 20px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">From</th>
                    <th style="text-align:left;padding:10px 20px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">To</th>
                    <th style="text-align:left;padding:10px 20px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teacher->yearStatuses->sortByDesc(fn($s) => $s->academicYear?->starts_on) as $status)
                @php
                    $colors = ['active'=>'#dcfce7 text-green-700','resigned'=>'#fee2e2 text-red-600','on_leave'=>'#fef3c7 text-yellow-700','transferred'=>'#dbeafe text-blue-700'];
                    $bg = explode(' ', $colors[$status->status] ?? '#f1f5f9 text-gray-600')[0];
                    $tx = explode(' ', $colors[$status->status] ?? '#f1f5f9 text-gray-600')[1];
                @endphp
                <tr style="border-top:1px solid #f1f5f9;">
                    <td style="padding:12px 20px;font-weight:600;color:#0f172a;">{{ $status->academicYear?->name ?? '—' }}</td>
                    <td style="padding:12px 20px;">
                        <span style="background:{{ $bg }};color:{{ $tx }};padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;display:inline-block;">
                            {{ ucfirst(str_replace('_', ' ', $status->status)) }}
                        </span>
                    </td>
                    <td style="padding:12px 20px;color:#475569;">{{ $status->effective_from?->format('d M Y') ?? '—' }}</td>
                    <td style="padding:12px 20px;color:#475569;">{{ $status->effective_to?->format('d M Y') ?? '—' }}</td>
                    <td style="padding:12px 20px;color:#64748b;font-size:12px;">{{ $status->remarks ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="padding:30px 20px;text-align:center;color:#94a3b8;font-size:13px;">
        No yearly status records yet. Click "Update Status" to add one.
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle hidden class
    document.querySelectorAll('.hidden').forEach(function(el) {
        var s = el.style;
        if (s.display === 'none') s.display = '';
    });
});
</script>

<style>
.hidden { display:none; }
</style>
@endsection
