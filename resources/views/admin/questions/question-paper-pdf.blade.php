<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Question Paper — {{ $exam->name }} — {{ $class }} — {{ $subject }}</title>
    <style>
        @page { margin: 18mm 20mm; }
        body { font-family:'DejaVu Sans',sans-serif; font-size:10pt; color:#1e293b; line-height:1.6; }
        .header { text-align:center; margin-bottom:16px; padding-bottom:12px; border-bottom:2px solid #1e3a5f; }
        .header .logo { height:46px; margin-bottom:4px; }
        .header h1 { font-size:16pt; font-weight:800; color:#1e3a5f; margin:2px 0; }
        .header h2 { font-size:13pt; font-weight:700; color:#4f46e5; margin:2px 0; letter-spacing:.5px; }
        .header p { font-size:8pt; color:#64748b; margin:1px 0; }
        .exam-info { display:flex; justify-content:space-between; font-size:9pt; padding:8px 12px; background:#f8fafc; border-radius:4px; margin-bottom:14px; }
        .exam-info div { }
        .exam-info .label { color:#64748b; font-size:7.5pt; }
        .exam-info .value { font-weight:700; color:#0f172a; }
        .instructions { font-size:8.5pt; color:#475569; margin-bottom:14px; padding:8px 12px; border:1px solid #e2e8f0; border-radius:4px; }
        .instructions strong { color:#1e293b; }
        .question { margin-bottom:10px; padding:6px 0; border-bottom:1px dotted #e2e8f0; }
        .question .q-num { display:inline-block; font-weight:800; color:#1e3a5f; min-width:30px; }
        .question .q-text { font-size:10pt; color:#0f172a; }
        .question .q-marks { float:right; font-size:9pt; font-weight:700; color:#4f46e5; background:#eef2ff; padding:2px 10px; border-radius:4px; }
        .total-marks { text-align:right; font-size:10pt; font-weight:800; color:#1e3a5f; margin-top:14px; padding-top:8px; border-top:2px solid #1e3a5f; }
        .end-note { text-align:center; font-size:8pt; color:#94a3b8; margin-top:20px; padding-top:8px; border-top:1px dashed #e2e8f0; }
        .sig-line { margin-top:30px; display:flex; justify-content:space-between; font-size:8pt; color:#64748b; }
        .sig-line span { text-align:center; }
        .sig-line .line { width:140px; border-top:1px solid #1e293b; margin:28px auto 2px; padding-top:4px; font-weight:600; color:#1e293b; font-size:9pt; }
    </style>
</head>
<body>

<div class="header">
    @php $logo = \App\Helpers\Settings::get('logo'); @endphp
    @if($logo && file_exists(storage_path('app/public/'.$logo)))
        <img src="{{ storage_path('app/public/'.$logo) }}" class="logo">
    @endif
    <h1>{{ \App\Helpers\Settings::get('site_name', 'JN Nazareth School') }}</h1>
    <p>Churachandpur, Manipur</p>
    <h2>{{ $exam->name }} {{ $exam->code ? '('.$exam->code.')' : '' }}</h2>
</div>

<div class="exam-info">
    <div>
        <div class="label">Class</div>
        <div class="value">{{ $class }}</div>
    </div>
    <div>
        <div class="label">Subject</div>
        <div class="value">{{ $subject }}</div>
    </div>
    <div>
        <div class="label">Date</div>
        <div class="value">{{ $exam->starts_on?->format('d M Y') ?? '—' }}</div>
    </div>
    <div>
        <div class="label">Total Marks</div>
        <div class="value">{{ number_format($totalMarks, $totalMarks == intval($totalMarks) ? 0 : 2) }}</div>
    </div>
    <div>
        <div class="label">Time</div>
        <div class="value">{{ $totalMarks > 0 ? ceil($totalMarks / 1.5).' Hrs' : '—' }}</div>
    </div>
</div>

<div class="instructions">
    <strong>General Instructions:</strong><br>
    1. All questions are compulsory.<br>
    2. Read each question carefully before answering.<br>
    3. Marks for each question are indicated against it.<br>
    4. Write your answers clearly and legibly.
</div>

@foreach($questions as $i => $q)
<div class="question">
    <span class="q-num">{{ $i + 1 }}.</span>
    <span class="q-text">{{ $q->question_text ?? 'See attached file' }}</span>
    <span class="q-marks">{{ number_format($q->marks ?? 0, ($q->marks == intval($q->marks ?? 0)) ? 0 : 2) }} mark{{ $q->marks != 1 ? 's' : '' }}</span>
    @if($q->file_path && !$q->question_text)
        <div style="font-size:8pt;color:#0f766e;margin-top:4px;">(Refer to attached file: {{ $q->file_name }})</div>
    @endif
</div>
@endforeach

<div class="total-marks">
    Total: {{ number_format($totalMarks, $totalMarks == intval($totalMarks) ? 0 : 2) }} marks
</div>

<div class="end-note">— End of Question Paper —</div>

<div class="sig-line">
    <span>
        <div class="line">Examiner</div>
    </span>
    <span>
        <div class="line">Scrutiniser</div>
    </span>
    <span>
        <div class="line">Principal</div>
    </span>
</div>

</body>
</html>
