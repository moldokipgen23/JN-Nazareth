@extends('layouts.admin')
@section('page-title','Academic Years')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
    <div>
        <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">Academic Years</h1>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">Manage school years, set active year, and promote students</div>
    </div>
</div>

@if(session('success'))
<div style="background:#dcfce7;color:#15803d;border-radius:10px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:600;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fee2e2;color:#b91c1c;border-radius:10px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:600;">{{ session('error') }}</div>
@endif

{{-- Years table --}}
<div class="resp-table-wrap" style="background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(15,23,42,.06);margin-bottom:20px;">
    <table style="border-collapse:collapse;font-size:13px;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="text-align:left;padding:10px 16px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Year</th>
                <th style="text-align:left;padding:10px 16px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Dates</th>
                <th style="text-align:left;padding:10px 16px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Students</th>
                <th style="text-align:left;padding:10px 16px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Status</th>
                <th style="text-align:right;padding:10px 16px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($years as $year)
        <tr style="border-top:1px solid #f1f5f9;" id="year-row-{{ $year->id }}">
            <td style="padding:12px 16px;">
                {{-- Inline edit form --}}
                <form method="POST" action="{{ route('admin.academic-years.update', $year) }}" style="display:flex;gap:6px;align-items:center;">
                    @csrf @method('PUT')
                    <input type="text" name="name" value="{{ $year->name }}" required
                           style="width:90px;border:1px solid #e2e8f0;border-radius:7px;padding:5px 8px;font-size:13px;font-weight:600;color:#0f172a;">
                    <input type="date" name="starts_on" value="{{ $year->starts_on?->format('Y-m-d') }}"
                           style="border:1px solid #e2e8f0;border-radius:7px;padding:5px 8px;font-size:12px;color:#475569;">
                    <input type="date" name="ends_on" value="{{ $year->ends_on?->format('Y-m-d') }}"
                           style="border:1px solid #e2e8f0;border-radius:7px;padding:5px 8px;font-size:12px;color:#475569;">
                    <button type="submit" style="background:#f0fdfa;color:#0f766e;border:1px solid #99f6e4;padding:5px 10px;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;">Save</button>
                </form>
            </td>
            <td style="padding:12px 16px;color:#475569;font-size:12px;">
                {{ $year->starts_on?->format('d M Y') ?? '—' }} – {{ $year->ends_on?->format('d M Y') ?? '—' }}
            </td>
            <td style="padding:12px 16px;">
                <a href="{{ route('admin.students.index') }}" style="font-weight:700;color:#0f766e;text-decoration:none;">
                    {{ $year->enrollments_count }}
                </a>
            </td>
            <td style="padding:12px 16px;">
                @if($year->is_active)
                    <span style="background:#dcfce7;color:#15803d;font-size:10px;font-weight:700;padding:3px 10px;border-radius:99px;">Active</span>
                @else
                    <form method="POST" action="{{ route('admin.academic-years.activate', $year) }}" style="display:inline;">
                        @csrf
                        <button type="submit" style="background:#f8fafc;color:#475569;border:1px solid #e2e8f0;padding:4px 10px;border-radius:7px;font-size:11px;font-weight:600;cursor:pointer;">Set Active</button>
                    </form>
                @endif
            </td>
            <td style="padding:12px 16px;text-align:right;">
                <div style="display:flex;gap:6px;justify-content:flex-end;align-items:center;">
                    @if($year->is_active)
                    <a href="{{ route('admin.academic-years.promote.form', $year) }}"
                       style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;padding:5px 12px;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;">
                        Promote Students →
                    </a>
                    @endif
                    @if(!$year->is_active && $year->enrollments_count == 0)
                    <form method="POST" action="{{ route('admin.academic-years.destroy', $year) }}"
                          onsubmit="return confirm('Delete {{ addslashes($year->name) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none;border:none;color:#dc2626;font-size:11px;font-weight:600;cursor:pointer;">Delete</button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="padding:40px;text-align:center;color:#94a3b8;font-size:13px;">No academic years yet. Create one below.</td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Create new year --}}
<div style="background:#fff;border-radius:12px;padding:16px 20px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:12px;">Create New Academic Year</div>
    <form method="POST" action="{{ route('admin.academic-years.store') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
        @csrf
        @error('name')<div style="color:#b91c1c;font-size:12px;width:100%;">{{ $message }}</div>@enderror
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Year Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. 2026-27" required maxlength="30"
                   style="border:1px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;width:130px;">
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Start Date</label>
            <input type="date" name="starts_on" value="{{ old('starts_on') }}"
                   style="border:1px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;">
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">End Date</label>
            <input type="date" name="ends_on" value="{{ old('ends_on') }}"
                   style="border:1px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;">
        </div>
        <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:9px 20px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">Create Year</button>
    </form>
</div>

@endsection
