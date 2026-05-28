<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Identity Card — {{ $student->name }}</title>
    <style>
        @page { margin:0; padding:0; }
        body { margin:0; padding:0; font-family:'DejaVu Sans',sans-serif; background:#fff; }
        .card { width:320px; height:500px; margin:0 auto; border:2px solid #0f766e; border-radius:12px; overflow:hidden; position:relative; }
        .card-top { background:linear-gradient(135deg,#0f766e,#0d9488); padding:14px 16px 10px; text-align:center; color:#fff; }
        .card-top h1 { font-size:15px; font-weight:800; margin:0; letter-spacing:.5px; }
        .card-top p { font-size:8px; margin:2px 0 0; opacity:.85; }
        .card-body { padding:10px 16px; text-align:center; }
        .photo { width:70px; height:70px; border-radius:50%; object-fit:cover; border:3px solid #0f766e; margin:0 auto 8px; display:block; }
        .photo-placeholder { width:70px; height:70px; border-radius:50%; background:#ccfbf1; border:3px solid #0f766e; margin:0 auto 8px; display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:800; color:#0f766e; }
        .name { font-size:16px; font-weight:800; color:#0f172a; margin:4px 0 2px; }
        .class-row { font-size:12px; color:#475569; margin-bottom:6px; }
        .details { text-align:left; margin-top:6px; }
        .details table { width:100%; border-collapse:collapse; }
        .details td { padding:3px 4px; font-size:9px; vertical-align:top; }
        .details .lbl { font-weight:700; color:#64748b; width:80px; }
        .details .val { font-weight:600; color:#0f172a; }
        .divider { border-top:1px dashed #cbd5e1; margin:6px 0; }
        .card-footer { position:absolute; bottom:0; left:0; right:0; background:#f8fafc; padding:8px 16px; text-align:center; font-size:7px; color:#94a3b8; border-top:1px solid #e2e8f0; }
        .validity { font-size:8px; color:#0f766e; font-weight:700; margin-top:4px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-top">
            <h1>JN Nazareth School</h1>
            <p>Churachandpur, Manipur — Identity Card</p>
        </div>

        <div class="card-body">
            @if($student->photo)
                <img src="{{ storage_path('app/public/'.$student->photo) }}" class="photo" onerror="this.style.display='none'">
            @endif
            @if(!$student->photo)
                <div class="photo-placeholder">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
            @endif

            <div class="name">{{ $student->name }}</div>
            <div class="class-row">
                @if($enrollment)
                    {{ $enrollment->class }} · Section {{ $enrollment->section }}
                    @if($enrollment->roll_number) · Roll {{ $enrollment->roll_number }} @endif
                @else
                    {{ $student->class ?? '' }}@if($student->section) · Sec {{ $student->section }} @endif
                @endif
            </div>

            <div class="divider"></div>

            <div class="details">
                <table>
                    <tr>
                        <td class="lbl">Admission No.</td>
                        <td class="val">{{ $student->admission_number ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Father's Name</td>
                        <td class="val">{{ $student->father_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Mother's Name</td>
                        <td class="val">{{ $student->mother_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Blood Group</td>
                        <td class="val">{{ $student->blood_group ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">DOB</td>
                        <td class="val">{{ $student->date_of_birth?->format('d M Y') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Address</td>
                        <td class="val" style="font-size:8px;">{{ $student->address ?? '—' }}</td>
                    </tr>
                </table>
            </div>

            <div class="validity">
                Valid for Academic Year: {{ $enrollment?->academicYear?->name ?? $student->academic_year ?? '—' }}
            </div>
        </div>

        <div class="card-footer">
            Generated on {{ now()->format('d M Y') }} · Authorised by JN Nazareth School Administration
        </div>
    </div>
</body>
</html>
