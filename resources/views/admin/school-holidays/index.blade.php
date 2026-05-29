@extends('layouts.admin')
@section('page-title', 'School Holidays')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <div>
        <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">School Holidays</h1>
        <p style="font-size:12px;color:#64748b;margin-top:2px;">Dates marked as holiday are excluded from attendance.</p>
    </div>
</div>

{{-- Add holiday form --}}
<div style="background:#fff;border-radius:12px;padding:16px;margin-bottom:16px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <form method="POST" action="{{ route('admin.school-holidays.store') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
        @csrf
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Date *</label>
            <input type="date" name="date" required style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
        </div>
        <div style="flex:1;min-width:200px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Reason (optional)</label>
            <input type="text" name="reason" placeholder="e.g. Annual Day, Sports Day" maxlength="255" style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
        </div>
        <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:8px 20px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Mark Holiday</button>
    </form>
</div>

{{-- Holiday list --}}
<div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    @if($holidays->isEmpty())
    <div style="padding:40px 20px;text-align:center;color:#94a3b8;">
        No holidays marked yet.
    </div>
    @else
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Date</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Day</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Reason</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Added By</th>
                <th style="text-align:right;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($holidays as $h)
        <tr style="border-top:1px solid #f1f5f9;">
            <td style="padding:10px 14px;font-weight:600;color:#0f172a;">{{ $h->date->format('d M Y') }}</td>
            <td style="padding:10px 14px;color:#64748b;">{{ $h->date->format('l') }}</td>
            <td style="padding:10px 14px;color:#475569;">{{ $h->reason ?? '—' }}</td>
            <td style="padding:10px 14px;color:#64748b;font-size:12px;">{{ $h->creator?->name ?? '—' }}</td>
            <td style="padding:10px 14px;text-align:right;">
                <form method="POST" action="{{ route('admin.school-holidays.destroy', $h) }}" onsubmit="return confirm('Remove this holiday?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="background:none;border:none;color:#dc2626;font-size:12px;font-weight:600;cursor:pointer;">Remove</button>
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>

@endsection
