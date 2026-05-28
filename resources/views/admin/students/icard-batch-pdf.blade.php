<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ID Cards — Batch</title>
    @php
        $logo      = \App\Helpers\Settings::get('school_logo');
        $emblem    = \App\Helpers\Settings::get('about_emblem');
        $signature = \App\Helpers\Settings::get('principal_signature');
        $schoolName    = \App\Helpers\Settings::get('school_name', 'JN Nazareth English School');
        $schoolAddress = \App\Helpers\Settings::get('contact_address', \App\Helpers\Settings::get('address_short', 'Khengjang Village, Churachandpur District, Manipur-795128'));
        $officeContact = \App\Helpers\Settings::get('contact_phone', '');
        $validUntilGlobal = \App\Helpers\Settings::get('icard_valid_until', '');
        $logoPath   = $logo   && file_exists(storage_path('app/public/'.$logo))   ? storage_path('app/public/'.$logo)   : (file_exists(public_path('images/logo.png'))   ? public_path('images/logo.png')   : null);
        $emblemPath = $emblem && file_exists(storage_path('app/public/'.$emblem)) ? storage_path('app/public/'.$emblem) : (file_exists(public_path('images/emblem.jpg')) ? public_path('images/emblem.jpg') : null);
        $sigPath    = $signature && file_exists(storage_path('app/public/'.$signature)) ? storage_path('app/public/'.$signature) : null;
    @endphp
    <style>
        @page { margin:6mm; }
        body { margin:0; padding:0; font-family:'DejaVu Sans',sans-serif; font-size:7pt; }
        .grid { width:100%; }
        .row { display:table; width:100%; table-layout:fixed; margin-bottom:4mm; }
        .cell { display:table-cell; width:50%; padding:0 2mm; }
        .card {
            width:90mm; background:#fff; border:0.5pt solid #d1d5db;
            margin:0 auto; page-break-inside:avoid;
        }

        .hdr { background:#1e3a8a; padding:3mm 3mm 3.5mm; text-align:center; color:#fff; }
        .hdr h1 { font-size:10pt; font-weight:800; margin:0; letter-spacing:.3pt; line-height:1.1; }
        .hdr .addr { font-size:6pt; margin:1mm 0 0.5mm; line-height:1.25; }
        .hdr .recog { font-size:5.5pt; margin:0; font-style:italic; font-weight:600; }

        .emblem-row { background:#fff; padding:2mm 3mm 1mm; }
        .emblem-row table { width:100%; border-collapse:collapse; }
        .emblem-row td { vertical-align:middle; }
        .emblem-row .emblem-img { height:10mm; }
        .emblem-row .valid { font-size:6.5pt; color:#b91c1c; font-style:italic; font-weight:700; text-align:right; }

        .ribbon { background:#7c1d1d; padding:1.5mm 0; text-align:center; }
        .ribbon h2 { font-size:9pt; font-weight:800; color:#fff; margin:0; letter-spacing:1pt; }

        .photo-wrap { text-align:center; padding:3mm 0 2mm; background:#fff; }
        .photo { width:22mm; height:22mm; border-radius:11mm; border:0.7mm solid #1e3a8a; }
        .photo-placeholder { width:22mm; height:22mm; border-radius:11mm; border:0.7mm solid #1e3a8a; background:#dbeafe; text-align:center; line-height:21mm; font-size:13pt; font-weight:800; color:#1e3a8a; display:inline-block; }

        .name-ribbon { background:#7c1d1d; padding:1.5mm 0; text-align:center; }
        .name-ribbon h3 { font-size:8.5pt; font-weight:800; color:#fff; margin:0; letter-spacing:.5pt; }

        .details { padding:2mm 4mm 1mm; background:#fff; }
        .details table { width:100%; border-collapse:collapse; }
        .details td { padding:0.6mm 0; font-size:6.5pt; color:#0f172a; vertical-align:top; line-height:1.3; }
        .details td.lbl { width:38%; font-style:italic; }
        .details td.colon { width:5%; }
        .details td.val { font-weight:700; }

        .sig-row { padding:0 4mm 1mm; text-align:right; }
        .sig-row img { height:5mm; display:inline-block; margin-bottom:-0.5mm; }
        .sig-row .sig-lbl { font-size:6pt; font-weight:700; font-style:italic; color:#0f172a; display:block; }

        .ftr { background:#1e3a8a; padding:1.5mm 3mm; }
        .ftr p { margin:0; font-size:5.5pt; font-weight:700; color:#fff; }
    </style>
</head>
<body>

@php $chunks = $enrollments->chunk(2); @endphp

<div class="grid">
@foreach($chunks as $pair)
    <div class="row">
        @foreach($pair as $enrollment)
            @php
                $student = $enrollment->student;
                if (!$student) continue;
                $validYear = $enrollment->academicYear?->name ?? ($student->academic_year ?? date('Y').'-'.((int)date('y')+1));
                $validLabel = $validUntilGlobal;
                if (!$validLabel && preg_match('/(\d{4})\D+(\d{2,4})/', $validYear, $m)) {
                    $endYear = strlen($m[2]) === 2 ? '20'.$m[2] : $m[2];
                    $validLabel = 'March ' . $endYear;
                }
                $photoPath = $student->photo && file_exists(storage_path('app/public/'.$student->photo)) ? storage_path('app/public/'.$student->photo) : null;
            @endphp
            <div class="cell">
                <div class="card">
                    <div class="hdr">
                        <h1>{{ strtoupper($schoolName) }}</h1>
                        <p class="addr">{{ $schoolAddress }}</p>
                        <p class="recog">Govt. Recognised</p>
                    </div>

                    <div class="emblem-row">
                        <table>
                            <tr>
                                <td style="width:14mm;">
                                    @if($emblemPath)
                                        <img src="{{ $emblemPath }}" class="emblem-img">
                                    @elseif($logoPath)
                                        <img src="{{ $logoPath }}" class="emblem-img">
                                    @endif
                                </td>
                                <td>
                                    @if($validLabel)
                                        <div class="valid">Valid till {{ $validLabel }}</div>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="ribbon"><h2>IDENTITY CARD</h2></div>

                    <div class="photo-wrap">
                        @if($photoPath)
                            <img src="{{ $photoPath }}" class="photo">
                        @else
                            <div class="photo-placeholder">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
                        @endif
                    </div>

                    <div class="name-ribbon"><h3>{{ strtoupper($student->name) }}</h3></div>

                    <div class="details">
                        <table>
                            <tr><td class="lbl">Class</td><td class="colon">:</td><td class="val">{{ $enrollment->class ?? $student->class ?? '—' }}</td></tr>
                            <tr><td class="lbl">Roll No.</td><td class="colon">:</td><td class="val">{{ $enrollment->roll_number ?? $student->roll_number ?? '—' }}</td></tr>
                            <tr><td class="lbl">Father's Name</td><td class="colon">:</td><td class="val">{{ $student->father_name ?? '—' }}</td></tr>
                            <tr><td class="lbl">Mother's Name</td><td class="colon">:</td><td class="val">{{ $student->mother_name ?? '—' }}</td></tr>
                            <tr><td class="lbl">Date of Birth</td><td class="colon">:</td><td class="val">{{ $student->date_of_birth?->format('d-m-Y') ?? '—' }}</td></tr>
                            <tr><td class="lbl">Address</td><td class="colon">:</td><td class="val">{{ $student->address ?? '—' }}</td></tr>
                            <tr><td class="lbl">Contact No.</td><td class="colon">:</td><td class="val">{{ $student->parent_phone ?? $student->phone ?? '—' }}</td></tr>
                        </table>
                    </div>

                    <div class="sig-row">
                        @if($sigPath)<img src="{{ $sigPath }}">@endif
                        <span class="sig-lbl">Principal</span>
                    </div>

                    <div class="ftr">
                        <p>Office Contact : {{ $officeContact ?: '—' }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endforeach
</div>

</body>
</html>
