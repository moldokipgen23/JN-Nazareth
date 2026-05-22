@extends('layouts.admin')
@section('page-title', 'Inquiries')

@section('content')

<div style="margin-bottom:20px;">
    <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">
        Admission Inquiries
        @if($counts['new'] > 0)
            <span style="background:#dc2626; color:#fff; border-radius:99px; font-size:11px; padding:2px 9px; font-weight:700; vertical-align:middle; margin-left:6px;">{{ $counts['new'] }} new</span>
        @endif
    </h2>
    <p style="font-size:12px; color:#64748b; margin:3px 0 0;">Enquiries submitted through the public website</p>
</div>

{{-- Status tabs --}}
<div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:16px;">
    @php
        $tabs = ['' => 'All', 'new' => 'New', 'read' => 'Read', 'replied' => 'Replied'];
        $tabCounts = ['' => $counts['all'], 'new' => $counts['new'], 'read' => $counts['read'], 'replied' => $counts['replied']];
    @endphp
    @foreach($tabs as $val => $label)
        @php $isActive = $status === ($val ?: null); @endphp
        <a href="{{ route('admin.inquiries.index', $val ? ['status'=>$val] : []) }}"
           style="padding:7px 14px; border-radius:99px; font-size:12px; font-weight:600; text-decoration:none; border:1.5px solid {{ $isActive ? '#0f766e' : '#e2e8f0' }}; background:{{ $isActive ? '#0f766e' : '#f8fafc' }}; color:{{ $isActive ? '#fff' : '#475569' }}; display:flex; align-items:center; gap:5px;">
            {{ $label }}
            <span style="background:{{ $isActive ? 'rgba(255,255,255,.25)' : '#cbd5e1' }}; color:#fff; border-radius:99px; font-size:10px; padding:1px 7px; font-weight:700;">{{ $tabCounts[$val] }}</span>
        </a>
    @endforeach
</div>

<div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; overflow:hidden;">
    @forelse($inquiries as $inq)
    @php
        $sc = match($inq->status) {
            'new'     => ['New','#dc2626','#fee2e2'],
            'read'    => ['Read','#ca8a04','#fef9c3'],
            'replied' => ['Replied','#16a34a','#dcfce7'],
            default   => ['New','#dc2626','#fee2e2'],
        };
        $wa = preg_replace('/\D+/', '', $inq->whatsapp ?: $inq->phone);
        if (strlen($wa) === 10) { $wa = '91'.$wa; }
    @endphp
    <div style="display:flex; align-items:center; gap:12px; padding:13px 18px; border-bottom:1px solid #f8fafc; flex-wrap:wrap;">
        <div style="flex:1; min-width:160px;">
            <div style="font-weight:600; color:#0f172a; font-size:13.5px;">{{ $inq->name }}</div>
            <div style="font-size:11px; color:#94a3b8;">
                {{ $inq->phone }}@if($inq->class_interested) · Class: {{ $inq->class_interested }}@endif · {{ $inq->created_at->format('d M Y') }}
            </div>
        </div>
        <span style="background:{{ $sc[2] }}; color:{{ $sc[1] }}; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600;">{{ $sc[0] }}</span>
        <div style="display:flex; gap:5px;">
            @if($wa)
            <a href="https://wa.me/{{ $wa }}" target="_blank" style="background:#dcfce7; color:#15803d; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none;">WhatsApp</a>
            @endif
            <a href="{{ route('admin.inquiries.show', $inq) }}" style="background:#f0fdfa; color:#0f766e; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none;">View</a>
            <form method="POST" action="{{ route('admin.inquiries.destroy', $inq) }}" onsubmit="return confirm('Delete this inquiry?')">
                @csrf @method('DELETE')
                <button type="submit" style="background:#fff1f2; color:#e11d48; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:600; border:none; cursor:pointer;">Del</button>
            </form>
        </div>
    </div>
    @empty
    <div style="padding:50px 20px; text-align:center;">
        <p style="font-size:14px; font-weight:600; color:#334155; margin:0;">No inquiries found</p>
        <p style="font-size:13px; color:#94a3b8; margin:6px 0 0;">Admission enquiries from the website will appear here.</p>
    </div>
    @endforelse

    @if($inquiries->hasPages())
    <div style="padding:14px 20px; border-top:1px solid #f1f5f9;">{{ $inquiries->links() }}</div>
    @endif
</div>
@endsection
