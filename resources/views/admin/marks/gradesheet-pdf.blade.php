<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gradesheet — {{ $exam->name }} — {{ $class }}</title>
    <style>
        @page { margin: 12mm 15mm; }
        body { font-family:'DejaVu Sans',sans-serif; font-size:8pt; color:#1e293b; }
        .header { text-align:center; margin-bottom:10px; padding-bottom:8px; border-bottom:2px solid #1e3a5f; }
        .header h1 { font-size:14pt; font-weight:800; color:#1e3a5f; margin:2px 0; }
        .header p { font-size:7.5pt; color:#64748b; margin:1px 0; }
        .header .exam-title { font-size:10pt; font-weight:700; color:#4f46e5; margin:4px 0 2px; }
        .info-bar { font-size:7pt; color:#475569; margin-bottom:8px; padding:4px 8px; background:#f8fafc; border-radius:3px; }
        table { width:100%; border-collapse:collapse; font-size:7pt; }
        th { background:#1e3a5f; color:#fff; padding:5px 4px; text-align:center; font-weight:700; font-size:6.5pt; }
        th.left { text-align:left; }
        td { padding:4px; text-align:center; border-bottom:1px solid #e2e8f0; }
        td.left { text-align:left; }
        td.name { font-weight:600; color:#0f172a; }
        tr:nth-child(even) td { background:#f8fafc; }
        .total-row td { font-weight:700; background:#eef2ff; border-top:2px solid #1e3a5f; }
        .footer { text-align:center; font-size:6pt; color:#94a3b8; margin-top:10px; padding-top:6px; border-top:1px dashed #e2e8f0; }
        .sig-line { margin-top:20px; display:flex; justify-content:space-between; font-size:7pt; color:#64748b; }
        .sig-line .line { width:130px; border-top:1px solid #1e293b; margin:24px auto 2px; padding-top:3px; font-weight:600; color:#1e293b; font-size:8pt; text-align:center; }
    </style>
</head>
<body>

<div class="header">
    <h1>{{ \App\Helpers\Settings::get('site_name', 'JN Nazareth School') }}</h1>
    <p>Churachandpur, Manipur</p>
    <div class="exam-title">GRADESHEET — {{ $exam->name }}</div>
</div>

<div class="info-bar">
    <strong>Class:</strong> {{ $class }} @if($section)· Section {{ $section }} @endif
    &nbsp;|&nbsp; <strong>Academic Year:</strong> {{ $year->name }}
    &nbsp;|&nbsp; <strong>Generated:</strong> {{ now()->format('d M Y') }}
    &nbsp;|&nbsp; <strong>Total Students:</strong> {{ count($rows) }}
</div>

<table>
    <thead>
        <tr>
            <th style="width:22px;">#</th>
            <th style="width:28px;">Roll</th>
            <th class="left">Student Name</th>
            @foreach($subjects as $subj)
                <th style="min-width:38px;">{{ $subj }}<br><span style="font-weight:400;font-size:5.5pt;">(T/F)</span></th>
            @endforeach
            <th style="min-width:32px;">Total</th>
            <th style="min-width:28px;">Avg %</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $i => $r)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $r['roll'] ?? '—' }}</td>
            <td class="left name">{{ $r['name'] }}</td>
            @foreach($subjects as $subj)
                <td>
                    @php $sd = $r['subjectData'][$subj] ?? null; @endphp
                    @if($sd)
                        {{ $sd['total'] ?? '—' }}/{{ $sd['full'] ?? '—' }}
                    @else
                        —
                    @endif
                </td>
            @endforeach
            <td style="font-weight:700;">{{ $r['totalObtained'] }}/{{ $r['totalFull'] }}</td>
            <td style="font-weight:700;">{{ $r['avgPct'] !== null ? $r['avgPct'].'%' : '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="sig-line">
    <span><div class="line">Class Teacher</div></span>
    <span><div class="line">Principal</div></span>
    <span><div class="line">Date</div></span>
</div>

<div class="footer">
    This is a computer-generated gradesheet. Authorised by {{ \App\Helpers\Settings::get('site_name', 'JN Nazareth School') }} Administration.
</div>

</body>
</html>
