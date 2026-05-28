@extends('layouts.admin')
@section('page-title', 'Grade Scale')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <div>
        <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">Grade Scale</h1>
        <div style="font-size:12px;color:#64748b;margin-top:2px;">Define letter grades, percentage ranges, and grade points</div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <form method="POST" action="{{ route('admin.grade-scales.seed') }}" style="display:inline;">
            @csrf
            <button type="submit" style="background:#f1f5f9;color:#475569;border:none;padding:9px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;">Seed Defaults</button>
        </form>
        <button type="button" onclick="document.getElementById('addGrade').style.display='block'" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
            + Add Grade
        </button>
    </div>
</div>

{{-- Add form --}}
<div id="addGrade" style="display:none;background:#fff;border-radius:12px;padding:16px;margin-bottom:16px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <form method="POST" action="{{ route('admin.grade-scales.store') }}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(100px,1fr));gap:10px;align-items:end;">
        @csrf
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Grade *</label>
            <input name="name" required maxlength="10" placeholder="e.g. A+"
                   style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Min %</label>
            <input type="number" name="min_percent" required step="0.01" min="0" max="100"
                   style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Max %</label>
            <input type="number" name="max_percent" required step="0.01" min="0" max="100"
                   style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Grade Point</label>
            <input type="number" name="grade_point" required step="0.01" min="0" max="10" value="0"
                   style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;">
        </div>
        <button type="submit" style="background:#0f766e;color:#fff;border:none;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Save</button>
    </form>
</div>

@if($grades->isEmpty())
    <div style="background:#fff;border-radius:12px;padding:48px 24px;text-align:center;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <div style="font-size:36px;opacity:.3;margin-bottom:10px;">📊</div>
        <div style="font-weight:600;color:#475569;">No grades configured</div>
        <div style="font-size:12px;color:#94a3b8;margin-top:6px;">Click "Seed Defaults" to add the standard A+–F scale, or add manually.</div>
    </div>
@else
    <div class="resp-table-wrap" style="background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
        <table style="border-collapse:collapse;font-size:13px;">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Grade</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Min %</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Max %</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Grade Point</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Active</th>
                    <th style="text-align:right;padding:10px 14px;font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($grades as $g)
                <tr style="border-top:1px solid #f1f5f9;">
                    <form method="POST" action="{{ route('admin.grade-scales.update', $g) }}" style="display:contents;">
                        @csrf @method('PUT')
                        <td style="padding:10px 14px;font-weight:700;color:#0f172a;">
                            <input name="name" value="{{ $g->name }}" maxlength="10" required
                                   style="border:1px solid #e2e8f0;border-radius:6px;padding:5px 8px;font-size:12px;width:60px;font-weight:700;">
                        </td>
                        <td style="padding:10px 14px;">
                            <input type="number" name="min_percent" value="{{ $g->min_percent }}" step="0.01" min="0" max="100" required
                                   style="border:1px solid #e2e8f0;border-radius:6px;padding:5px 8px;font-size:12px;width:70px;">
                        </td>
                        <td style="padding:10px 14px;">
                            <input type="number" name="max_percent" value="{{ $g->max_percent }}" step="0.01" min="0" max="100" required
                                   style="border:1px solid #e2e8f0;border-radius:6px;padding:5px 8px;font-size:12px;width:70px;">
                        </td>
                        <td style="padding:10px 14px;">
                            <input type="number" name="grade_point" value="{{ $g->grade_point }}" step="0.01" min="0" max="10" required
                                   style="border:1px solid #e2e8f0;border-radius:6px;padding:5px 8px;font-size:12px;width:70px;">
                        </td>
                        <td style="padding:10px 14px;">
                            <button type="submit" formaction="{{ route('admin.grade-scales.toggle', $g) }}" style="border:none;cursor:pointer;background:{{ $g->is_active ? '#dcfce7' : '#f1f5f9' }};color:{{ $g->is_active ? '#15803d' : '#94a3b8' }};padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;">
                                {{ $g->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td style="padding:10px 14px;text-align:right;display:flex;gap:4px;justify-content:flex-end;">
                            <button type="submit" style="background:#0f766e;color:#fff;border:none;padding:5px 10px;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">Save</button>
                            <button type="submit" formaction="{{ route('admin.grade-scales.destroy', $g) }}" formmethod="POST"
                                    onclick="return confirm('Delete grade {{ $g->name }}?')"
                                    style="background:#fff1f2;color:#e11d48;border:none;padding:5px 10px;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">Delete</button>
                        </td>
                    </form>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
