<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Identity Card — {{ $student->name }}</title>
    @php
        $logo    = \App\Helpers\Settings::get('school_logo');
        $emblem  = \App\Helpers\Settings::get('about_emblem');
        $signature = \App\Helpers\Settings::get('principal_signature');
        $schoolName    = \App\Helpers\Settings::get('site_name', 'JN Nazareth English School');
        $schoolAddress = \App\Helpers\Settings::get('site_address', 'Khengjang Village, Churachandpur District, Manipur-795128');
        $officeContact = \App\Helpers\Settings::get('site_phone', '');
        $logoPath      = $logo   && file_exists(storage_path('app/public/'.$logo))   ? storage_path('app/public/'.$logo)   : (file_exists(public_path('images/logo.png'))   ? public_path('images/logo.png')   : null);
        $emblemPath    = $emblem && file_exists(storage_path('app/public/'.$emblem)) ? storage_path('app/public/'.$emblem) : (file_exists(public_path('images/emblem.jpg')) ? public_path('images/emblem.jpg') : null);
        $sigPath       = $signature && file_exists(storage_path('app/public/'.$signature)) ? storage_path('app/public/'.$signature) : null;
        $photoPath     = $student->photo && file_exists(storage_path('app/public/'.$student->photo)) ? storage_path('app/public/'.$student->photo) : null;
        $validYear     = $enrollment?->academicYear?->name ?? ($student->academic_year ?? date('Y').'-'.((int)date('y')+1));
        // Compute "Valid till March YYYY"
        $validTillYear = null;
        if (preg_match('/(\d{4})\D+(\d{2,4})/', $validYear, $m)) {
            $validTillYear = strlen($m[2]) === 2 ? '20'.$m[2] : $m[2];
        }
    @endphp
    <style>
        @page { margin:0; }
        body { margin:0; padding:0; font-family:'DejaVu Sans',sans-serif; background:#e5e7eb; }
        .card { width:340px; margin:8px auto; background:#fff; border:1px solid #d1d5db; }

        /* === HEADER (navy blue) === */
        .hdr { background:#1e3a8a; padding:14px 16px 18px; text-align:center; color:#fff; }
        .hdr h1 { font-size:16px; font-weight:800; margin:0; letter-spacing:.5px; line-height:1.15; }
        .hdr .addr { font-size:9px; margin:4px 0 2px; font-weight:500; line-height:1.3; }
        .hdr .recog { font-size:8.5px; margin:0; font-weight:600; font-style:italic; }

        /* === EMBLEM ROW === */
        .emblem-row { background:#fff; padding:8px 14px 6px; position:relative; }
        .emblem-row table { width:100%; border-collapse:collapse; }
        .emblem-row td { vertical-align:middle; }
        .emblem-row .emblem-img { height:48px; display:block; }
        .emblem-row .valid { font-size:10px; color:#b91c1c; font-style:italic; font-weight:700; text-align:right; }

        /* === IDENTITY CARD RIBBON === */
        .ribbon { background:#7c1d1d; padding:7px 0; text-align:center; }
        .ribbon h2 { font-size:15px; font-weight:800; color:#fff; margin:0; letter-spacing:2px; text-shadow:1px 1px 1px rgba(0,0,0,.3); }

        /* === PHOTO === */
        .photo-wrap { text-align:center; padding:14px 0 10px; background:#fff; }
        .photo { width:110px; height:110px; border-radius:55px; border:3px solid #1e3a8a; object-fit:cover; }
        .photo-placeholder { width:110px; height:110px; border-radius:55px; border:3px solid #1e3a8a; background:#dbeafe; text-align:center; line-height:104px; font-size:42px; font-weight:800; color:#1e3a8a; display:inline-block; }

        /* === NAME RIBBON === */
        .name-ribbon { background:#7c1d1d; padding:7px 0; text-align:center; }
        .name-ribbon h3 { font-size:14px; font-weight:800; color:#fff; margin:0; letter-spacing:.8px; }

        /* === DETAILS === */
        .details { padding:10px 16px 6px; background:#fff; }
        .details table { width:100%; border-collapse:collapse; }
        .details td { padding:2px 0; font-size:10.5px; color:#0f172a; vertical-align:top; line-height:1.4; }
        .details td.lbl { width:38%; font-style:italic; font-weight:500; color:#1e293b; }
        .details td.colon { width:6%; }
        .details td.val { font-weight:700; }

        /* === SIGNATURE === */
        .sig-row { padding:0 16px 8px; text-align:right; }
        .sig-row img { height:24px; display:inline-block; margin-bottom:-4px; }
        .sig-row .sig-lbl { font-size:9.5px; font-weight:700; font-style:italic; color:#0f172a; display:block; }

        /* === FOOTER (navy) === */
        .ftr { background:#1e3a8a; padding:6px 14px; text-align:left; }
        .ftr p { margin:0; font-size:9px; font-weight:700; color:#fff; }
    </style>
</head>
<body>
<div class="card">

    {{-- HEADER --}}
    <div class="hdr">
        <h1>{{ strtoupper($schoolName) }}</h1>
        <p class="addr">{{ $schoolAddress }}</p>
        <p class="recog">Govt. Recognised</p>
    </div>

    {{-- EMBLEM + VALIDITY --}}
    <div class="emblem-row">
        <table>
            <tr>
                <td style="width:60px;">
                    @if($emblemPath)
                        <img src="{{ $emblemPath }}" class="emblem-img">
                    @elseif($logoPath)
                        <img src="{{ $logoPath }}" class="emblem-img">
                    @endif
                </td>
                <td>
                    @if($validTillYear)
                        <div class="valid">Valid till March {{ $validTillYear }}</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- IDENTITY CARD RIBBON --}}
    <div class="ribbon">
        <h2>IDENTITY CARD</h2>
    </div>

    {{-- PHOTO --}}
    <div class="photo-wrap">
        @if($photoPath)
            <img src="{{ $photoPath }}" class="photo">
        @else
            <div class="photo-placeholder">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
        @endif
    </div>

    {{-- NAME --}}
    <div class="name-ribbon">
        <h3>{{ strtoupper($student->name) }}</h3>
    </div>

    {{-- DETAILS --}}
    <div class="details">
        <table>
            <tr>
                <td class="lbl">Class</td>
                <td class="colon">:</td>
                <td class="val">{{ $enrollment->class ?? $student->class ?? '—' }}</td>
            </tr>
            <tr>
                <td class="lbl">Roll No.</td>
                <td class="colon">:</td>
                <td class="val">{{ $enrollment->roll_number ?? $student->roll_number ?? '—' }}</td>
            </tr>
            <tr>
                <td class="lbl">Father's Name</td>
                <td class="colon">:</td>
                <td class="val">{{ $student->father_name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="lbl">Mother's Name</td>
                <td class="colon">:</td>
                <td class="val">{{ $student->mother_name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="lbl">Date of Birth</td>
                <td class="colon">:</td>
                <td class="val">{{ $student->date_of_birth?->format('d-m-Y') ?? '—' }}</td>
            </tr>
            <tr>
                <td class="lbl">Address</td>
                <td class="colon">:</td>
                <td class="val">{{ $student->address ?? '—' }}</td>
            </tr>
            <tr>
                <td class="lbl">Contact No.</td>
                <td class="colon">:</td>
                <td class="val">{{ $student->parent_phone ?? $student->phone ?? '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- SIGNATURE --}}
    <div class="sig-row">
        @if($sigPath)
            <img src="{{ $sigPath }}">
        @endif
        <span class="sig-lbl">Principal</span>
    </div>

    {{-- FOOTER --}}
    <div class="ftr">
        <p>Office Contact : {{ $officeContact ?: '—' }}</p>
    </div>

</div>
</body>
</html>
