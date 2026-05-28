<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ID Cards</title>
    <style>
        @page { margin: 8mm; }
        body { margin:0; padding:0; font-family:'DejaVu Sans',sans-serif; font-size:7pt; }
        .page { width:194mm; }
        .grid { display:flex; flex-wrap:wrap; margin:0 -2.5mm; }
        .card {
            width:85mm; height:54mm;
            border:1.2px solid #0f766e; border-radius:2.5mm;
            margin:2.5mm; overflow:hidden;
            display:flex; flex-direction:column;
            position:relative;
            page-break-inside:avoid;
        }
        .card-inner { display:flex; height:100%; }
        .card-left {
            width:20mm; background:linear-gradient(135deg,#0f766e,#0d9488);
            display:flex; flex-direction:column; align-items:center; justify-content:center;
            padding:2mm 1mm; flex-shrink:0;
        }
        .card-left .photo { width:14mm; height:18mm; object-fit:cover; border-radius:1mm; border:1px solid rgba(255,255,255,.5); display:block; }
        .card-left .photo-placeholder {
            width:14mm; height:18mm; border-radius:1mm; background:rgba(255,255,255,.2);
            display:flex; align-items:center; justify-content:center;
            font-size:10pt; font-weight:800; color:#fff;
        }
        .card-right { flex:1; padding:1.5mm 2mm; display:flex; flex-direction:column; justify-content:center; }
        .school-name { font-size:6pt; font-weight:800; color:#0f766e; line-height:1.2; }
        .student-name { font-size:8pt; font-weight:800; color:#0f172a; margin-top:1mm; line-height:1.2; }
        .class-row { font-size:6pt; color:#475569; margin-top:.5mm; }
        .details { margin-top:1mm; font-size:5.5pt; color:#475569; }
        .details .row { margin-top:.3mm; }
        .details .lbl { font-weight:700; color:#64748b; }
        .footer-note { font-size:4.5pt; color:#94a3b8; margin-top:auto; text-align:center; border-top:.3px dashed #e2e8f0; padding-top:.8mm; }
    </style>
</head>
<body>
    @php $chunks = $enrollments->chunk(8); @endphp
    @foreach($chunks as $chunk)
    <div class="page">
        <div class="grid">
            @foreach($chunk as $enrollment)
                @php $student = $enrollment->student; @endphp
                <div class="card">
                    <div class="card-inner">
                        <div class="card-left">
                            @php $photoPath = $student->photo ? storage_path('app/public/'.$student->photo) : null; @endphp
                            @if($photoPath && file_exists($photoPath))
                                <img src="{{ $photoPath }}" class="photo">
                            @else
                                <div class="photo-placeholder">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
                            @endif
                        </div>
                        <div class="card-right">
                            <div class="school-name">JN Nazareth School</div>
                            <div class="student-name">{{ $student->name }}</div>
                            <div class="class-row">
                                {{ $enrollment->class }}
                                @if($enrollment->section) · Sec {{ $enrollment->section }} @endif
                                @if($enrollment->roll_number) · Roll {{ $enrollment->roll_number }} @endif
                            </div>
                            <div class="details">
                                <div class="row"><span class="lbl">Adm No:</span> {{ $student->admission_number ?? '—' }}</div>
                                <div class="row"><span class="lbl">Father:</span> {{ $student->father_name ?? '—' }}</div>
                                <div class="row"><span class="lbl">DOB:</span> {{ $student->date_of_birth?->format('d M Y') ?? '—' }}</div>
                                <div class="row"><span class="lbl">Blood:</span> {{ $student->blood_group ?? '—' }}</div>
                            </div>
                            <div class="footer-note">
                                {{ $enrollment->academicYear?->name ?? '' }} · Authorised by JN Nazareth School
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endforeach
</body>
</html>
