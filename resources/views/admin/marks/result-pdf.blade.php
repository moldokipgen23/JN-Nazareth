<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Result — {{ $exam->name }} — {{ $class }}</title>
    <style>
        @page { margin: 15mm 18mm; }
        body { font-family:'DejaVu Sans',sans-serif; font-size:10pt; color:#1e293b; line-height:1.5; }
        .header { text-align:center; margin-bottom:14px; padding-bottom:12px; border-bottom:2px solid #1e3a5f; }
        .header .logo { height:50px; margin-bottom:6px; }
        .header h1 { font-size:18pt; font-weight:800; color:#1e3a5f; margin:2px 0; }
        .header h2 { font-size:12pt; font-weight:700; color:#4f46e5; margin:2px 0; }
        .header p { font-size:8pt; color:#64748b; margin:2px 0; }
        .result-title { font-size:16pt; font-weight:800; color:#1e3a5f; text-align:center; margin:10px 0 4px; letter-spacing:1px; }
        .subtitle { text-align:center; font-size:9pt; color:#64748b; margin-bottom:14px; }
        .info-bar { display:flex; justify-content:space-between; font-size:8pt; color:#475569; margin-bottom:10px; padding:6px 10px; background:#f8fafc; border-radius:4px; }
        table { width:100%; border-collapse:collapse; font-size:8pt; }
        th { background:#1e3a5f; color:#fff; padding:6px 5px; text-align:center; font-weight:700; font-size:7.5pt; }
        th.left { text-align:left; }
        td { padding:5px; text-align:center; border-bottom:1px solid #e2e8f0; }
        td.left { text-align:left; }
        tr:nth-child(even) td { background:#f8fafc; }
        tr.top-1 td { background:#fef3c7; font-weight:700; }
        tr.top-2 td { background:#f1f5f9; }
        tr.top-3 td { background:#fed7aa; }
        .rank-badge { display:inline-block; width:20px; height:20px; border-radius:50%; line-height:20px; font-weight:800; font-size:7pt; text-align:center; }
        .rank-1 { background:#fbbf24; color:#92400e; }
        .rank-2 { background:#cbd5e1; color:#334155; }
        .rank-3 { background:#fdba74; color:#9a3412; }
        .signatures { margin-top:24px; padding-top:12px; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; font-size:9pt; }
        .signatures .sig-block { text-align:center; }
        .signatures .sig-line { width:160px; border-top:1px solid #1e293b; margin:32px auto 4px; padding-top:4px; font-weight:600; color:#1e293b; }
        .signatures .sig-label { font-size:7pt; color:#64748b; }
        .footer { text-align:center; font-size:6.5pt; color:#94a3b8; margin-top:10px; padding-top:6px; border-top:1px dashed #e2e8f0; }
        .summary-box { display:flex; gap:12px; justify-content:center; margin-bottom:10px; flex-wrap:wrap; }
        .summary-box .stat { background:#f8fafc; border-radius:4px; padding:6px 14px; text-align:center; min-width:80px; }
        .summary-box .stat .num { font-size:14pt; font-weight:800; color:#1e3a5f; }
        .summary-box .stat .lbl { font-size:7pt; color:#64748b; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

@foreach($classResults as $i => $cr)
<div class="{{ $i > 0 ? 'page-break' : '' }}">

    {{-- Header --}}
    <div class="header">
        @php $logo = \App\Helpers\Settings::get('logo'); @endphp
        @if($logo && file_exists(storage_path('app/public/'.$logo)))
            <img src="{{ storage_path('app/public/'.$logo) }}" class="logo">
        @endif
        <h1>{{ \App\Helpers\Settings::get('site_name', 'JN Nazareth School') }}</h1>
        <p>Churachandpur, Manipur</p>
    </div>

    <div class="result-title">EXAMINATION RESULT</div>
    <div class="subtitle">
        {{ $exam->name }} — {{ $exam->code ? '('.$exam->code.')' : '' }}
        &nbsp;|&nbsp; {{ $cr['class'] }} @if($cr['section']) · Section {{ $cr['section'] }} @endif
        &nbsp;|&nbsp; Academic Year: {{ $year->name }}
    </div>

    {{-- Info bar --}}
    <div class="info-bar">
        <span>Total Students: <strong>{{ $cr['total'] }}</strong></span>
        <span>Date of Exam: <strong>{{ $exam->starts_on?->format('d M Y') ?? '—' }}</strong></span>
        <span>Generated: <strong>{{ now()->format('d M Y') }}</strong></span>
    </div>

    {{-- Summary stats --}}
    <div class="summary-box">
        <div class="stat"><div class="num">{{ $cr['total'] }}</div><div class="lbl">Students</div></div>
        <div class="stat">
            <div class="num" style="color:#15803d;">{{ collect($cr['rankings'])->where('cgpa', '>=', 4)->count() }}</div>
            <div class="lbl">CGPA ≥ 4.0</div>
        </div>
        <div class="stat">
            <div class="num" style="color:#b91c1c;">{{ collect($cr['rankings'])->where('cgpa', '<', 4)->where('cgpa', '!=', null)->count() }}</div>
            <div class="lbl">Below 4.0</div>
        </div>
    </div>

    {{-- Rankings table --}}
    <table>
        <thead>
            <tr>
                <th style="width:28px;">Rank</th>
                <th style="width:32px;" class="left">Roll</th>
                <th class="left">Student Name</th>
                @if($showSubjectWise)
                    @foreach($cr['subjects'] as $subj)
                        <th style="min-width:40px;">{{ $subj }}</th>
                    @endforeach
                @endif
                <th style="min-width:44px;">Avg %</th>
                <th style="min-width:32px;">CGPA</th>
                <th style="min-width:32px;">Division</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cr['rankings'] as $r)
            <tr class="top-{{ $r['rank'] <= 3 ? $r['rank'] : '' }}">
                <td>
                    @if($r['rank'] <= 3)
                        <span class="rank-badge rank-{{ $r['rank'] }}">{{ $r['rank'] }}</span>
                    @else
                        {{ $r['rank'] }}
                    @endif
                </td>
                <td class="left">{{ $r['roll'] ?? '—' }}</td>
                <td class="left" style="font-weight:600;">{{ $r['name'] }}</td>
                @if($showSubjectWise)
                    @foreach($cr['subjects'] as $subj)
                        <td>
                            @php $sd = $r['subjectData'][$subj] ?? null; @endphp
                            @if($sd && $sd['pct'] !== null)
                                <span style="font-weight:600;">{{ $sd['pct'] }}%</span>
                                <span style="font-size:6.5pt;color:#94a3b8;">{{ $sd['grade'] }}</span>
                            @else
                                <span style="color:#cbd5e1;">—</span>
                            @endif
                        </td>
                    @endforeach
                @endif
                <td style="font-weight:700;">{{ $r['avgPct'] !== null ? $r['avgPct'].'%' : '—' }}</td>
                <td style="font-weight:700;color:#4f46e5;">{{ $r['cgpa'] !== null ? number_format($r['cgpa'], 2) : '—' }}</td>
                <td style="font-weight:700;">{{ $r['division'] ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Signatures --}}
    <div class="signatures">
        <div class="sig-block">
            <div class="sig-line">Class Teacher</div>
            <div class="sig-label">Signature</div>
        </div>
        <div class="sig-block">
            <div class="sig-line">Principal</div>
            <div class="sig-label">{{ \App\Helpers\Settings::get('site_name', 'JN Nazareth School') }}</div>
        </div>
        <div class="sig-block">
            <div class="sig-line">Date</div>
            <div class="sig-label">{{ now()->format('d M Y') }}</div>
        </div>
    </div>

    <div class="footer">
        This is a computer-generated result. Authorised by {{ \App\Helpers\Settings::get('site_name', 'JN Nazareth School') }} Administration.
    </div>
</div>
@endforeach

</body>
</html>
