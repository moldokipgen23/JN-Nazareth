<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Result — {{ $exam->name }} — {{ $class }}</title>
    <style>
        @page { margin: 12mm 15mm; }
        body { font-family:'DejaVu Sans',sans-serif; font-size:9pt; color:#1e293b; line-height:1.4; }
        .header { text-align:center; margin-bottom:10px; padding-bottom:10px; border-bottom:2px solid #1e3a5f; }
        .header h1 { font-size:16pt; font-weight:800; color:#1e3a5f; margin:2px 0; }
        .header p { font-size:8pt; color:#64748b; margin:2px 0; }
        .result-title { font-size:14pt; font-weight:800; color:#1e3a5f; text-align:center; margin:8px 0 2px; letter-spacing:1px; }
        .subtitle { text-align:center; font-size:8pt; color:#64748b; margin-bottom:10px; }
        table { width:100%; border-collapse:collapse; font-size:7.5pt; margin-bottom:6px; }
        th { padding:4px 4px; text-align:center; font-weight:700; font-size:7pt; }
        td { padding:3px 4px; text-align:center; border-bottom:1px solid #e2e8f0; }
        td.left { text-align:left; }
        .pass-header { background:#15803d; color:#fff; }
        .fail-header { background:#b91c1c; color:#fff; }
        .section-label { font-size:10pt; font-weight:800; padding:6px 8px; margin:8px 0 4px; border-radius:3px; }
        .section-pass { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
        .section-fail { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; }
        tr.top-1 td { background:#fef3c7; }
        tr:nth-child(even) td { background:#f8fafc; }
        tr.top-1 td { background:#fef3c7; font-weight:700; }
        .rank-badge { display:inline-block; width:18px; height:18px; border-radius:50%; line-height:18px; font-weight:800; font-size:6.5pt; text-align:center; }
        .rank-1 { background:#fbbf24; color:#92400e; }
        .signatures { margin-top:14px; padding-top:8px; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; font-size:8pt; }
        .signatures .sig-line { width:140px; border-top:1px solid #1e293b; margin:24px auto 2px; padding-top:3px; font-weight:600; text-align:center; font-size:8pt; }
        .signatures .sig-label { font-size:6.5pt; color:#64748b; text-align:center; }
        .footer { text-align:center; font-size:6pt; color:#94a3b8; margin-top:6px; padding-top:4px; border-top:1px dashed #e2e8f0; }
        .info-bar { font-size:7pt; color:#475569; margin-bottom:6px; text-align:center; }
        .stat-box { display:flex; gap:8px; justify-content:center; margin-bottom:8px; }
        .stat-box .stat { padding:4px 10px; border-radius:3px; text-align:center; font-size:7pt; }
        .stat-box .stat .num { font-size:12pt; font-weight:800; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ \App\Helpers\Settings::get('site_name', 'JN Nazareth School') }}</h1>
        <p>Churachandpur, Manipur</p>
    </div>

    <div class="result-title">EXAMINATION RESULT</div>
    <div class="subtitle">
        {{ $exam->name }}{{ $exam->code ? ' ('.$exam->code.')' : '' }}
        &nbsp;|&nbsp; Class {{ $class }}{{ $section ? ' · Section '.$section : '' }}
        &nbsp;|&nbsp; {{ $year->name }}
    </div>

    <div class="info-bar">
        Total: <strong>{{ $passCount + $failCount }}</strong> |
        Pass: <strong style="color:#15803d;">{{ $passCount }}</strong> |
        Needs Improvement: <strong style="color:#b91c1c;">{{ $failCount }}</strong>
    </div>

    {{-- PASS SECTION --}}
    @if($rankedPass->isNotEmpty())
    <div class="section-label section-pass">PASS — Ranked</div>
    <table>
        <thead>
            <tr class="pass-header">
                <th style="width:24px;">Rank</th>
                <th style="width:28px;">Roll</th>
                <th class="left">Student Name</th>
                @foreach($analyticsSubjects as $subj)
                    <th>{{ $subj }}</th>
                @endforeach
                <th>Total</th>
                <th>Avg %</th>
                <th>CGPA</th>
                <th>Division</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rankedPass as $r)
            <tr class="top-{{ $r['rank'] <= 3 ? $r['rank'] : '' }}">
                <td>
                    @if($r['rank'] <= 3)
                        <span class="rank-badge rank-{{ $r['rank'] }}">{{ $r['rank'] }}</span>
                    @else
                        {{ $r['rank'] }}
                    @endif
                </td>
                <td>{{ $r['enrollment']?->roll_number ?: '—' }}</td>
                <td class="left" style="font-weight:600;">{{ $r['enrollment']?->student?->name ?? '—' }}</td>
                @foreach($analyticsSubjects as $subj)
                    @php $sd = $r['subjectData'][$subj] ?? null; @endphp
                    <td>{{ $sd && $sd['raw'] !== null ? $sd['raw'] : '—' }}</td>
                @endforeach
                <td style="font-weight:700;">{{ $r['totalRaw'] }}</td>
                <td style="font-weight:700;">{{ $r['avgPct'] !== null ? $r['avgPct'].'%' : '—' }}</td>
                <td style="font-weight:700;color:#0f766e;">{{ $r['cgpa'] !== null ? number_format($r['cgpa'], 2) : '—' }}</td>
                <td style="font-weight:700;">{{ $r['division'] ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- FAIL SECTION --}}
    @if($rankedFail->isNotEmpty())
    <div class="section-label section-fail">NEEDS IMPROVEMENT</div>
    <table>
        <thead>
            <tr class="fail-header">
                <th style="width:24px;">Rank</th>
                <th style="width:28px;">Roll</th>
                <th class="left">Student Name</th>
                @foreach($analyticsSubjects as $subj)
                    <th>{{ $subj }}</th>
                @endforeach
                <th>Total</th>
                <th>Avg %</th>
                <th>Division</th>
                <th>Failed In</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rankedFail as $r)
            <tr>
                <td style="font-weight:800;color:#b91c1c;">{{ $r['rank'] }}</td>
                <td>{{ $r['enrollment']?->roll_number ?: '—' }}</td>
                <td class="left" style="font-weight:600;">{{ $r['enrollment']?->student?->name ?? '—' }}</td>
                @foreach($analyticsSubjects as $subj)
                    @php $sd = $r['subjectData'][$subj] ?? null; @endphp
                    <td>{{ $sd && $sd['raw'] !== null ? $sd['raw'] : '—' }}</td>
                @endforeach
                <td style="font-weight:700;">{{ $r['totalRaw'] }}</td>
                <td style="font-weight:700;">{{ $r['avgPct'] !== null ? $r['avgPct'].'%' : '—' }}</td>
                <td style="font-weight:700;">{{ $r['division'] ?? '—' }}</td>
                <td style="color:#b91c1c;font-weight:600;font-size:6.5pt;">{{ !empty($r['failedSubjects']) ? implode(', ', $r['failedSubjects']) : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

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

</body>
</html>
