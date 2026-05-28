<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Progress Report Card</title>
    <style>
        @page { margin: 15mm 12mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.4; }
        .header { text-align:center; margin-bottom:16px; padding-bottom:10px; border-bottom:3px double #0f766e; }
        .header .logo { max-height:50px; margin-bottom:4px; }
        .header h1 { font-size:20px; font-weight:800; color:#0f766e; margin:2px 0; }
        .header .subtitle { font-size:11px; color:#475569; margin:0; }
        .header .report-title { font-size:14px; font-weight:800; color:#0f172a; margin:6px 0 2px; letter-spacing:1px; }
        .student-info { margin-bottom:12px; }
        .student-info table { width:100%; border-collapse:collapse; }
        .student-info td { padding:2px 8px; font-size:10px; vertical-align:top; }
        .student-info .lbl { font-weight:700; color:#64748b; width:100px; }
        .student-info .val { font-weight:600; color:#0f172a; }
        .exam-title { font-size:12px; font-weight:800; color:#0f766e; text-align:center; margin:12px 0 6px; padding:5px; background:#f0fdf4; border-radius:4px; }
        .marks-table { width:100%; border-collapse:collapse; margin-bottom:10px; font-size:9px; }
        .marks-table th { background:#0f766e; color:#fff; padding:6px 6px; font-weight:700; text-align:center; }
        .marks-table td { padding:5px 6px; text-align:center; border-bottom:1px solid #e2e8f0; }
        .marks-table tr:nth-child(even) td { background:#f8fafc; }
        .marks-table .subject-name { text-align:left; font-weight:600; color:#0f172a; }
        .grade-pass { display:inline-block; padding:1px 8px; border-radius:99px; font-size:8px; font-weight:700; background:#dcfce7; color:#15803d; }
        .grade-fail { display:inline-block; padding:1px 8px; border-radius:99px; font-size:8px; font-weight:700; background:#fee2e2; color:#b91c1c; }
        .summary { margin-top:12px; padding-top:10px; border-top:2px solid #e2e8f0; }
        .summary table { width:100%; border-collapse:collapse; }
        .summary td { padding:3px 12px; font-size:10px; }
        .summary .lbl { font-weight:700; color:#64748b; }
        .summary .val { font-weight:800; color:#0f172a; }
        .attendance-box { margin-top:8px; }
        .attendance-box table { width:100%; border-collapse:collapse; }
        .attendance-box td { padding:3px 8px; font-size:10px; }
        .signature { margin-top:30px; padding-top:16px; }
        .signature table { width:100%; border-collapse:collapse; }
        .signature td { padding:4px 16px; font-size:10px; vertical-align:bottom; }
        .signature .line { border-top:1px solid #0f172a; width:160px; display:inline-block; padding-top:4px; margin-top:40px; font-weight:600; color:#0f172a; }
        .footer { text-align:center; font-size:8px; color:#94a3b8; margin-top:16px; padding-top:6px; border-top:1px solid #e2e8f0; }
        .remarks-box { margin-top:10px; border:1px solid #e2e8f0; border-radius:4px; padding:10px 14px; min-height:40px; }
        .remarks-box .label { font-size:9px; font-weight:700; color:#64748b; margin-bottom:4px; }
        .watermark { position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); font-size:100px; color:rgba(0,0,0,.03); z-index:-1; font-weight:900; }
    </style>
</head>
<body>
    @if($exam) <div class="watermark">{{ $exam->name }}</div> @endif

    <div class="header">
        @php $logo = \App\Helpers\Settings::get('logo'); @endphp
        @if($logo)
            <img src="{{ storage_path('app/public/'.$logo) }}" class="logo" alt="School Logo">
        @endif
        <h1>{{ \App\Helpers\Settings::get('site_name', 'JN Nazareth School') }}</h1>
        <p class="subtitle">Churachandpur, Manipur</p>
        <div class="report-title">PROGRESS REPORT CARD</div>
    </div>

    @if($exam)
        <div class="exam-title">{{ $exam->name }} — {{ $enrollment->academicYear?->name ?? '' }}</div>
    @else
        <div class="exam-title">Consolidated Annual Report — {{ $enrollment->academicYear?->name ?? '' }}</div>
    @endif

    <div class="student-info">
        <table>
            <tr>
                <td class="lbl">Student Name</td>
                <td class="val">{{ $student->name }}</td>
                <td class="lbl">Class</td>
                <td class="val">{{ $enrollment->class }}</td>
            </tr>
            <tr>
                <td class="lbl">Admission No.</td>
                <td class="val">{{ $student->admission_number ?? '—' }}</td>
                <td class="lbl">Section</td>
                <td class="val">{{ $enrollment->section }}</td>
            </tr>
            <tr>
                <td class="lbl">Roll Number</td>
                <td class="val">{{ $enrollment->roll_number ?? '—' }}</td>
                <td class="lbl">Academic Year</td>
                <td class="val">{{ $enrollment->academicYear?->name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="lbl">Father's Name</td>
                <td class="val">{{ $student->father_name ?? '—' }}</td>
                <td class="lbl">Mother's Name</td>
                <td class="val">{{ $student->mother_name ?? '—' }}</td>
            </tr>
        </table>
    </div>

    @if($subjects->isNotEmpty())
    <table class="marks-table">
        <thead>
            <tr>
                <th style="text-align:left;padding-left:10px;">Subject</th>
                <th>Full Marks</th>
                <th>Pass Marks</th>
                <th>Theory</th>
                <th>Assignment</th>
                <th>Total</th>
                <th>%</th>
                <th>Grade</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjects as $subject)
            <tr>
                <td class="subject-name">{{ $subject['name'] }}</td>
                <td>{{ $subject['full'] }}</td>
                <td>{{ $subject['pass'] }}</td>
                <td>{{ $subject['theory'] ?? '—' }}</td>
                <td>{{ $subject['assignment'] ?? '—' }}</td>
                <td style="font-weight:700;">{{ $subject['total'] ?? '—' }}</td>
                <td>{{ $subject['pct'] !== null ? $subject['pct'].'%' : '—' }}</td>
                <td>{{ $subject['grade'] ?? '—' }}</td>
                <td>
                    @if(($subject['status'] ?? '') === 'pass')
                        <span class="grade-pass">PASS</span>
                    @elseif(($subject['status'] ?? '') === 'fail')
                        <span class="grade-fail">FAIL</span>
                    @else
                        —
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align:center;color:#94a3b8;padding:16px;">No marks recorded.</p>
    @endif

    <div class="summary">
        <table>
            <tr>
                <td class="lbl" style="width:130px;">Total Marks</td>
                <td class="val">{{ $totalObtained ?? '—' }} / {{ $totalMax ?? '—' }}</td>
                <td class="lbl" style="width:100px;">Average %</td>
                <td class="val">{{ $avgPct !== null ? $avgPct.'%' : '—' }}</td>
            </tr>
            <tr>
                <td class="lbl">CGPA</td>
                <td class="val">{{ $cgpa !== null ? number_format($cgpa, 2) : '—' }}</td>
                <td class="lbl">Class Rank</td>
                <td class="val">{{ $rank ?? '—' }}</td>
            </tr>
        </table>

        <div class="attendance-box">
            <table>
                <tr>
                    <td class="lbl" style="width:130px;">Attendance</td>
                    <td class="val">
                        @if($attPct !== null)
                            {{ $attPct }}%
                            @if(isset($attTotal) && $attTotal > 0)
                                (P: {{ $attSummary['present'] }}, A: {{ $attSummary['absent'] }}, L: {{ $attSummary['late'] }}, E: {{ $attSummary['excused'] }})
                            @endif
                        @else
                            —
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Exam-wise breakdown for consolidated report --}}
    @if(!$exam && isset($exams) && $exams->isNotEmpty())
    <div style="margin-top:10px;font-size:8px;color:#64748b;">
        <strong>Exams included:</strong> {{ $exams->pluck('name')->implode(', ') }}
    </div>
    @endif

    {{-- Remarks --}}
    <div class="remarks-box">
        <div class="label">Teacher's Remarks</div>
        <div style="min-height:30px;"></div>
    </div>

    {{-- Signatures --}}
    <div class="signature">
        <table>
            <tr>
                <td style="text-align:left;">
                    <div class="line">&nbsp;</div>
                    <div style="font-size:9px;color:#64748b;margin-top:2px;">Class Teacher</div>
                </td>
                <td style="text-align:center;">
                    <div class="line">&nbsp;</div>
                    <div style="font-size:9px;color:#64748b;margin-top:2px;">Principal</div>
                </td>
                <td style="text-align:right;">
                    <div style="font-size:9px;color:#64748b;">Date: {{ now()->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Generated on {{ now()->format('d F Y, h:i A') }} · {{ \App\Helpers\Settings::get('site_name', 'JN Nazareth School') }}, Churachandpur
    </div>
</body>
</html>
