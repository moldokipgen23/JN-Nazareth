@extends('layouts.admin')
@section('page-title', $member->name)

@section('content')
@php
    $waDigits = preg_replace('/\D+/', '', $member->parent_phone ?? $member->phone ?? '');
    if (strlen($waDigits) === 10) { $waDigits = '91' . $waDigits; }
@endphp
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.members.index', $member->class ? ['class'=>$member->class] : []) }}"
               class="p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-xl font-bold text-gray-900">Student Profile</h2>
        </div>
        <div class="flex gap-2 flex-wrap">
            @if($waDigits)
            <a href="https://wa.me/{{ $waDigits }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                WhatsApp Parent
            </a>
            @endif
            <a href="{{ route('admin.members.edit', $member) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition">
                Edit
            </a>
            <form method="POST" action="{{ route('admin.members.destroy', $member) }}"
                  onsubmit="return confirm('Delete {{ addslashes($member->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 border border-red-300 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition">
                    Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Profile Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-teal-600 to-teal-700 h-24"></div>
        <div class="px-6 pb-6">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-12 mb-6">
                @if($member->photo)
                    <img src="{{ \App\Helpers\Settings::storageUrl($member->photo) }}"
                         class="w-20 h-20 rounded-full border-4 border-white object-cover shadow shrink-0">
                @else
                    <div class="w-20 h-20 rounded-full border-4 border-white bg-teal-100 flex items-center justify-center text-teal-700 font-bold text-2xl shadow shrink-0">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                @endif
                <div class="pb-1 flex flex-wrap items-center gap-2">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $member->name }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold mt-1 bg-blue-100 text-blue-800">
                            {{ $member->class ?? 'No class' }}@if($member->roll_number) · Roll {{ $member->roll_number }}@endif
                        </span>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $member->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ $member->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Father's Name</dt>
                    <dd class="text-gray-800">{{ $member->father_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Mother's Name</dt>
                    <dd class="text-gray-800">{{ $member->mother_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Parent Phone</dt>
                    <dd class="text-gray-800">{{ $member->parent_phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Section</dt>
                    <dd class="text-gray-800">{{ $member->section ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Date of Birth</dt>
                    <dd class="text-gray-800">
                        @if($member->date_of_birth)
                            {{ $member->date_of_birth->format('d M Y') }}
                            <span class="text-gray-400 text-xs ml-1">({{ $member->date_of_birth->age }} yrs)</span>
                        @else — @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Admission Date</dt>
                    <dd class="text-gray-800">{{ $member->admission_date?->format('d M Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Admission No.</dt>
                    <dd class="text-gray-800">{{ $member->admission_number ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Aadhar No.</dt>
                    <dd class="text-gray-800">
                        @if($member->aadhar_number)
                            <span data-aadhar-masked>{{ $member->maskedAadhar() }}</span>
                            <span data-aadhar-full hidden>{{ $member->aadhar_number }}</span>
                            <button type="button" class="ml-2 text-xs text-teal-600 hover:underline"
                                    onclick="(function(b){var d=b.parentNode;var m=d.querySelector('[data-aadhar-masked]'),f=d.querySelector('[data-aadhar-full]');var s=f.hidden;f.hidden=!s;m.hidden=s;b.textContent=s?'Hide':'Show';})(this)">Show</button>
                        @else — @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Academic Year</dt>
                    <dd class="text-gray-800">{{ $member->academic_year ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email</dt>
                    <dd class="text-gray-800">{{ $member->email ?? '—' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Address</dt>
                    <dd class="text-gray-800">{{ $member->address ?? '—' }}</dd>
                </div>
                @if($member->notes)
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Notes</dt>
                    <dd class="text-gray-700 leading-relaxed">{{ $member->notes }}</dd>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Documents --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Documents</h3>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
            <form method="POST" action="{{ route('admin.member-files.store', $member) }}" enctype="multipart/form-data"
                  class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                @csrf
                <div class="flex-1">
                    <label for="file" class="block text-xs font-medium text-gray-600 mb-1">Upload a file</label>
                    <input type="file" name="file" id="file" required
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 transition">
                    @error('file')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition shrink-0">
                    Upload
                </button>
            </form>
        </div>

        @if(isset($member->files) && $member->files->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                            <th class="px-6 py-3">File Name</th>
                            <th class="px-6 py-3">Size</th>
                            <th class="px-6 py-3 hidden sm:table-cell">Date</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($member->files as $file)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3 font-medium text-gray-800">
                                    <span class="truncate max-w-[180px]">{{ $file->original_name ?? basename($file->path) }}</span>
                                </td>
                                <td class="px-6 py-3 text-gray-500">
                                    {{ isset($file->size) ? number_format($file->size / 1024, 1) . ' KB' : '—' }}
                                </td>
                                <td class="px-6 py-3 text-gray-500 hidden sm:table-cell">{{ $file->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.member-files.download', $file) }}"
                                           class="px-3 py-1.5 text-xs font-medium text-teal-700 bg-teal-50 hover:bg-teal-100 rounded-lg transition">Download</a>
                                        <form method="POST" action="{{ route('admin.member-files.destroy', $file) }}"
                                              onsubmit="return confirm('Delete this file?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-10 text-gray-400 text-sm">
                No documents uploaded for this student yet.
            </div>
        @endif
    </div>
</div>
@endsection
