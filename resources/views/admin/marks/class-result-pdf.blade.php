<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Result — {{ $exam->name }} — {{ $class }}</title>
    <style>
        @page { margin: 10mm 12mm; }
        body { font-family:'DejaVu Sans',sans-serif; font-size:8pt; color:#000; line-height:1.3; }
        .header { text-align:center; margin-bottom:6px; padding-bottom:6px; border-bottom:2px solid #000; }
        .header h1 { font-size:14pt; font-weight:700; margin:2px 0; }
        .header p { font-size:7pt; color:#444; margin:1px 0; }
        .result-title { font-size:12pt; font-weight:700; text-align:center; margin:6px 0 1px; letter-spacing:0.5px; }
        .subtitle { text-align:center; font-size:7.5pt; color:#444; margin-bottom:6px; }
        .summary-line { text-align:center; font-size:7pt; margin-bottom:8px; padding:3px 0; border-top:1px solid #ccc; border-bottom:1px solid #ccc; }
        .section-head { font-size:8pt; font-weight:700; padding:4px 6px; margin:8px 0 2px; border:1px solid #000; background:#eee; }
        table { width:100%; border-collapse:collapse; font-size:7pt; margin-bottom:4px; }
        th { padding:3px 3px; text-align:center; font-weight:700; font-size:6.5pt; border:1px solid #000; }
        td { padding:2px 3px; text-align:center; border:1px solid #999; }
        td.left { text-align:left; }
        .pass-th { background:#2b5a2b; color:#fff; }
        .fail-th { background:#7a2020; color:#fff; }
        tr.top1 td { background:#f5e6c8; }
        tr.alt td { background:#f5f5f5; }
        .sig-section { margin-top:12px; padding-top:6px; border-top:1px solid #000; overflow:hidden; }
        .sig-block { float:left; width:30%; text-align:center; margin-right:3%; }
        .sig-block .line { border-top:1px solid #000; margin:28px auto 0; padding-top:2px; font-weight:600; font-size:8pt; width:70%; }
        .sig-block .label { font-size:6.5pt; color:#555; margin-top:0; }
        .footer { text-align:center; font-size:6pt; color:#666; margin-top:6px; padding-top:4px; border-top:1px dashed #ccc; clear:both; }
        .name-col { text-align:left; font-weight:600; }
        .subject-col { min-width:28px; }
        .total-col { font-weight:700; }
        .rank-col { width:20px; }
        .roll-col { width:24px; }
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
        &nbsp;|&nbsp; Class {{ $class }}{{ $section ? ' - Section '.$section : '' }}
        &nbsp;|&nbsp; Academic Year {{ $year->name }}
    </div>

    <div class="summary-line">
        Total Students: <strong>{{ $passCount + $failCount }}</strong>
        &nbsp;&nbsp;|&nbsp;&nbsp; Pass: <strong>{{ $passCount }}</strong>
        &nbsp;&nbsp;|&nbsp;&nbsp; Needs Improvement: <strong>{{ $failCount }}</strong>
    </div>

    @if($rankedPass->isNotEmpty())
    <div class="section-head">PASS</div>
    <table cellspacing="0">
        <thead>
            <tr class="pass-th">
                <th class="rank-col">Rank</th>
                <th class="roll-col">Roll</th>
                <th>Student Name</th>
                @foreach($analyticsSubjects as $subj)
                    <th class="subject-col">{{ $subj }}</th>
                @endforeach
                <th>Total</th>
                <th>Avg %</th>
                <th>CGPA</th>
                <th>Division</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rankedPass as $r)
            <tr class="top{{ $r['rank'] == 1 ? '1' : '' }}{{ $loop->even ? ' alt' : '' }}">
                <td><strong>{{ $r['rank'] }}</strong></td>
                <td>{{ $r['enrollment']?->roll_number ?: '—' }}</td>
                <td class="name-col">{{ $r['enrollment']?->student?->name ?? '—' }}</td>
                @foreach($analyticsSubjects as $subj)
                    @php $sd = $r['subjectData'][$subj] ?? null; @endphp
                    <td>{{ $sd && $sd['raw'] !== null ? $sd['raw'] : '—' }}</td>
                @endforeach
                <td class="total-col">{{ $r['totalRaw'] }}</td>
                <td class="total-col">{{ $r['avgPct'] !== null ? $r['avgPct'].'%' : '—' }}</td>
                <td>{{ $r['cgpa'] !== null ? number_format($r['cgpa'], 2) : '—' }}</td>
                <td>{{ $r['division'] ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($rankedFail->isNotEmpty())
    <div class="section-head">NEEDS IMPROVEMENT</div>
    <table cellspacing="0">
        <thead>
            <tr class="fail-th">
                <th class="rank-col">Rank</th>
                <th class="roll-col">Roll</th>
                <th>Student Name</th>
                @foreach($analyticsSubjects as $subj)
                    <th class="subject-col">{{ $subj }}</th>
                @endforeach
                <th>Total</th>
                <th>Avg %</th>
                <th>Division</th>
                <th>Failed In</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rankedFail as $r)
            <tr class="{{ $loop->even ? 'alt' : '' }}">
                <td><strong>{{ $r['rank'] }}</strong></td>
                <td>{{ $r['enrollment']?->roll_number ?: '—' }}</td>
                <td class="name-col">{{ $r['enrollment']?->student?->name ?? '—' }}</td>
                @foreach($analyticsSubjects as $subj)
                    @php $sd = $r['subjectData'][$subj] ?? null; @endphp
                    <td>{{ $sd && $sd['raw'] !== null ? $sd['raw'] : '—' }}</td>
                @endforeach
                <td class="total-col">{{ $r['totalRaw'] }}</td>
                <td class="total-col">{{ $r['avgPct'] !== null ? $r['avgPct'].'%' : '—' }}</td>
                <td>{{ $r['division'] ?? '—' }}</td>
                <td style="font-size:6.5pt;">{{ !empty($r['failedSubjects']) ? implode(', ', $r['failedSubjects']) : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="sig-section">
        <div class="sig-block">
            <div class="line">Class Teacher</div>
            <div class="label">Signature</div>
        </div>
        <div class="sig-block">
            <div class="line">Principal</div>
            <div class="label">{{ \App\Helpers\Settings::get('site_name', 'JN Nazareth School') }}</div>
        </div>
        <div class="sig-block">
            <div class="line">{{ now()->format('d M Y') }}</div>
            <div class="label">Date</div>
        </div>
    </div>

    <div class="footer">
        This is a computer-generated result. Authorised by {{ \App\Helpers\Settings::get('site_name', 'JN Nazareth School') }} Administration.
    </div>

</body>
</html>
