@extends('layouts.admin')
@section('page-title', 'Inquiry')

@section('content')
@php
    $wa = preg_replace('/\D+/', '', $inquiry->whatsapp ?: $inquiry->phone);
    if (strlen($wa) === 10) { $wa = '91'.$wa; }
    $waMsg = rawurlencode('Hello ' . $inquiry->name . ', thank you for your inquiry with J.N. Nazareth English School regarding admission.');
@endphp
<div class="max-w-2xl space-y-6">

    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.inquiries.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-900">Inquiry Detail</h2>
        </div>
        <div class="flex gap-2 flex-wrap">
            @if($wa)
            <a href="https://wa.me/{{ $wa }}?text={{ $waMsg }}" target="_blank"
               class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">WhatsApp Reply</a>
            @endif
            @if($inquiry->status !== 'replied')
            <form method="POST" action="{{ route('admin.inquiries.markReplied', $inquiry) }}">
                @csrf @method('PATCH')
                <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition">Mark Replied</button>
            </form>
            @endif
            <form method="POST" action="{{ route('admin.inquiries.destroy', $inquiry) }}" onsubmit="return confirm('Delete this inquiry?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 border border-red-300 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition">Delete</button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Name</dt>
                <dd class="text-gray-800">{{ $inquiry->name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Status</dt>
                <dd class="text-gray-800 capitalize">{{ $inquiry->status }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Phone</dt>
                <dd class="text-gray-800">{{ $inquiry->phone }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">WhatsApp</dt>
                <dd class="text-gray-800">{{ $inquiry->whatsapp ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email</dt>
                <dd class="text-gray-800">{{ $inquiry->email ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Class Interested</dt>
                <dd class="text-gray-800">{{ $inquiry->class_interested ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Received</dt>
                <dd class="text-gray-800">{{ $inquiry->created_at->format('d M Y, g:i A') }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Message</dt>
                <dd class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $inquiry->message ?: '—' }}</dd>
            </div>
        </div>
    </div>
</div>
@endsection
