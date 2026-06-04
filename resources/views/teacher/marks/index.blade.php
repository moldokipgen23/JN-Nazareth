@extends('layouts.teacher')
@section('page-title', 'Marks Entry')

@section('content')

<div style="margin-bottom:14px;">
    <h1 style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">Marks Entry</h1>
    <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $year ? $year->name : 'No active year' }} — pick a subject + exam</div>
</div>

@if(!$year)
    <div style="background:#fef3c7;border:1px solid #fcd34d;color:#92400e;border-radius:10px;padding:14px 16px;font-size:13px;">No academic year is set as active.</div>
@elseif($exams->isEmpty())
    <div style="background:#fef3c7;border:1px solid #fcd34d;color:#92400e;border-radius:10px;padding:14px 16px;font-size:13px;">No active exams. Ask admin to create one.</div>
@elseif($slots->isEmpty())
    <div class="card" style="padding:40px 20px;text-align:center;">
        <div style="font-size:14px;font-weight:600;color:#475569;">No subject assignments</div>
        <div style="font-size:12px;color:#94a3b8;margin-top:4px;">Ask admin to assign you a subject.</div>
    </div>
@else
    @foreach($slots as $slot)
        <div style="background:#fff;border-radius:12px;padding:14px;margin-bottom:10px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#fef3c7,#fde68a);color:#92400e;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">{{ strtoupper(substr($slot->subject,0,2)) }}</div>
                <div>
                    <div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $slot->subject }}</div>
                    <div style="font-size:12px;color:#64748b;">{{ $slot->class }} — Sec {{ $slot->section }}</div>
                </div>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                @foreach($exams as $exam)
                    @php
                        $slotStatus = $slotStatuses[$slot->class][$slot->section][$slot->subject][$exam->id] ?? null;
                    @endphp
                    <div style="display:flex;align-items:center;gap:4px;">
                        <a href="{{ route('teacher.marks.sheet',['exam'=>$exam->id,'class'=>$slot->class,'section'=>$slot->section,'subject'=>$slot->subject]) }}" style="font-size:12px;font-weight:600;color:#0f766e;background:#f0fdf4;border:1px solid #bbf7d0;padding:7px 14px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;">{{ $exam->name }}{{ $exam->code?' ('.$exam->code.')':'' }}</a>
                        @if($slotStatus === 'revised')
                            <span style="background:#fef3c7;color:#92400e;font-size:10px;font-weight:700;padding:2px 7px;border-radius:99px;">⚠️ Revised</span>
                        @elseif($slotStatus === 'approved')
                            <span style="background:#dcfce7;color:#15803d;font-size:10px;font-weight:700;padding:2px 7px;border-radius:99px;">✅</span>
                        @elseif($slotStatus === 'pending')
                            <span style="background:#dbeafe;color:#1d4ed8;font-size:10px;font-weight:700;padding:2px 7px;border-radius:99px;">⏳</span>
                        @elseif($slotStatus === 'draft')
                            <span style="background:#f1f5f9;color:#475569;font-size:10px;font-weight:700;padding:2px 7px;border-radius:99px;border:1px dashed #cbd5e1;">📝 Draft</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@endif

@endsection