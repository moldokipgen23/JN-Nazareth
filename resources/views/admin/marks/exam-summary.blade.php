@extends('layouts.admin')
@section('page-title', 'Exam Summary')
@section('content')

<div style="margin-bottom:16px;">
    <h1 style="font-size:20px;font-weight:700;color:#0f172a;margin:0;">Exam Summary</h1>
    <div style="font-size:12px;color:#64748b;margin-top:2px;">
        @if($year) {{ $year->name }} @else No active year @endif
        — School-wide progress for each class
    </div>
</div>

<form method="GET" style="background:#fff;border-radius:12px;padding:14px 16px;margin-bottom:16px;display:flex;gap:10px;align-items:end;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div>
        <label style="display:block;font-size:11px;font-weight:600;color:#64748b;margin-bottom:4px;">Exam</label>
        <select name="exam" style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:13px;min-width:200px;">
            <option value="">— Select Exam —</option>
            @foreach($exams as $e)
                <option value="{{ $e->id }}" {{ (string) $examId === (string) $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" style="background:linear-gradient(135deg,#0f766e,#0d9488);color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">View Summary</button>
</form>

@if($examId && $classData->isNotEmpty())
    {{-- Overall stats --}}
    @php
        $totalExpected = $classData->sum('expected_count');
        $totalQ        = $classData->sum('questions_done');
        $totalApproved = $classData->sum('marks_done');
        $totalPending  = $classData->sum('marks_pending');
        $totalRejected = $classData->sum('marks_rejected');
    @endphp
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px;margin-bottom:16px;">
        <div style="background:#f1f5f9;border-radius:10px;padding:14px 16px;">
            <div style="font-size:22px;font-weight:700;">{{ $totalExpected }}</div>
            <div style="font-size:11px;color:#64748b;font-weight:600;">Total Subject Slots</div>
        </div>
        <div style="background:#dcfce7;border-radius:10px;padding:14px 16px;color:#15803d;">
            <div style="font-size:22px;font-weight:700;">{{ $totalQ }}/{{ $totalExpected }}</div>
            <div style="font-size:11px;font-weight:600;">Questions Approved</div>
        </div>
        <div style="background:#ede9fe;border-radius:10px;padding:14px 16px;color:#6d28d9;">
            <div style="font-size:22px;font-weight:700;">{{ $totalApproved }}/{{ $totalExpected }}</div>
            <div style="font-size:11px;font-weight:600;">Marks Approved</div>
        </div>
        <div style="background:#fef3c7;border-radius:10px;padding:14px 16px;color:#92400e;">
            <div style="font-size:22px;font-weight:700;">{{ $totalPending }}</div>
            <div style="font-size:11px;font-weight:600;">Pending Approval</div>
        </div>
        @if($totalRejected > 0)
        <div style="background:#fee2e2;border-radius:10px;padding:14px 16px;color:#b91c1c;">
            <div style="font-size:22px;font-weight:700;">{{ $totalRejected }}</div>
            <div style="font-size:11px;font-weight:600;">Sent Back</div>
        </div>
        @endif
        <div style="background:#f0fdf4;border-radius:10px;padding:14px 16px;color:#15803d;">
            <div style="font-size:22px;font-weight:700;">{{ $classData->where('all_complete', true)->count() }}/{{ $classData->count() }}</div>
            <div style="font-size:11px;font-weight:600;">Classes Ready</div>
        </div>
    </div>

    {{-- Class grid --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px;">
        @foreach($classData as $cd)
        @php
            $qPct = $cd['expected_count'] > 0 ? round($cd['questions_done'] / $cd['expected_count'] * 100) : 0;
            $mPct = $cd['expected_count'] > 0 ? round($cd['marks_done'] / $cd['expected_count'] * 100) : 0;
        @endphp
        <div style="background:#fff;border-radius:12px;padding:16px 18px;box-shadow:0 1px 3px rgba(15,23,42,.06);border:1px solid {{ $cd['all_complete'] ? '#bbf7d0' : '#e2e8f0' }};">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                <span style="font-size:15px;font-weight:700;color:#0f172a;">{{ $cd['class'] }}</span>
                @if($cd['all_complete'])
                    <span style="font-size:10px;font-weight:700;padding:3px 10px;border-radius:99px;background:#dcfce7;color:#15803d;">✅ Ready</span>
                @else
                    <span style="font-size:10px;font-weight:700;padding:3px 10px;border-radius:99px;background:#fef3c7;color:#92400e;">⏳ Pending</span>
                @endif
            </div>

            {{-- Questions bar --}}
            <div style="margin-bottom:8px;">
                <div style="display:flex;justify-content:space-between;font-size:11px;margin-bottom:3px;">
                    <span style="color:#64748b;">📝 Questions</span>
                    <span style="font-weight:600;color:{{ $cd['questions_complete'] ? '#15803d' : '#92400e' }};">{{ $cd['questions_done'] }}/{{ $cd['expected_count'] }}</span>
                </div>
                <div style="height:5px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ $qPct }}%;background:{{ $cd['questions_complete'] ? '#22c55e' : '#eab308' }};border-radius:99px;"></div>
                </div>
            </div>

            {{-- Marks bar --}}
            <div style="margin-bottom:10px;">
                <div style="display:flex;justify-content:space-between;font-size:11px;margin-bottom:3px;">
                    <span style="color:#64748b;">📊 Marks Approved</span>
                    <span style="font-weight:600;color:{{ $cd['marks_complete'] ? '#15803d' : '#92400e' }};">{{ $cd['marks_done'] }}/{{ $cd['expected_count'] }}</span>
                </div>
                <div style="height:5px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ $mPct }}%;background:{{ $cd['marks_complete'] ? '#22c55e' : '#eab308' }};border-radius:99px;"></div>
                </div>
                @if(($cd['marks_pending'] ?? 0) > 0 || ($cd['marks_rejected'] ?? 0) > 0)
                <div style="display:flex;gap:6px;font-size:10px;margin-top:4px;flex-wrap:wrap;">
                    @if(($cd['marks_pending'] ?? 0) > 0)
                        <span style="background:#fef3c7;color:#92400e;padding:1px 7px;border-radius:99px;font-weight:700;">⏳ {{ $cd['marks_pending'] }} pending</span>
                    @endif
                    @if(($cd['marks_rejected'] ?? 0) > 0)
                        <span style="background:#fee2e2;color:#b91c1c;padding:1px 7px;border-radius:99px;font-weight:700;">↩ {{ $cd['marks_rejected'] }} sent back</span>
                    @endif
                </div>
                @endif
            </div>

            {{-- Quick links --}}
            <div style="display:flex;gap:6px;">
                <a href="{{ route('admin.questions.index', ['exam' => $examId, 'class' => $cd['class']]) }}"
                   style="flex:1;text-align:center;font-size:10px;font-weight:600;padding:5px;border-radius:6px;background:#f8fafc;color:#64748b;text-decoration:none;">Questions</a>
                <a href="{{ route('admin.marks.index', ['view' => 'summary', 'exam' => $examId, 'class' => $cd['class'], 'section' => $cd['section']]) }}"
                   style="flex:1;text-align:center;font-size:10px;font-weight:600;padding:5px;border-radius:6px;background:#f8fafc;color:#64748b;text-decoration:none;">Marks</a>
                @if($cd['all_complete'])
                <a href="{{ route('admin.marks.index', ['view' => 'results', 'exam' => $examId, 'class' => $cd['class'], 'section' => $cd['section']]) }}"
                   style="flex:1;text-align:center;font-size:10px;font-weight:600;padding:5px;border-radius:6px;background:#f0fdf4;color:#15803d;text-decoration:none;">Results</a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
@elseif($examId && $classData->isEmpty())
    <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">
        No class_subjects configured for this academic year.
        <a href="{{ route('admin.class-subjects.index') }}" style="color:#0f766e;font-weight:600;">Set up class subjects →</a>
    </div>
@else
    <div style="background:#fff;border-radius:12px;padding:36px 20px;text-align:center;color:#64748b;">
        Select an exam above to see school-wide progress.
    </div>
@endif
@endsection