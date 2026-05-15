@extends('layouts.admin')
@section('page-title', $member->name)

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.members.index') }}"
               class="p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-xl font-bold text-gray-900">Member Profile</h2>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.members.edit', $member) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <form method="POST" action="{{ route('admin.members.destroy', $member) }}"
                  onsubmit="return confirm('Are you sure you want to delete {{ addslashes($member->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 border border-red-300 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
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
                    <img src="{{ \App\Helpers\Settings::storageUrl($member->photo) }}" alt="{{ $member->name }}"
                         class="w-20 h-20 rounded-full border-4 border-white object-cover shadow shrink-0">
                @else
                    <div class="w-20 h-20 rounded-full border-4 border-white bg-teal-100 flex items-center justify-content-center justify-center text-teal-700 font-bold text-2xl shadow shrink-0">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                @endif
                <div class="pb-1 flex flex-wrap items-center gap-2">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $member->name }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold mt-1
                            {{ match($member->role) {
                                'Pastor'  => 'bg-purple-100 text-purple-800',
                                'Elder'   => 'bg-blue-100 text-blue-800',
                                'Deacon'  => 'bg-cyan-100 text-cyan-800',
                                'Youth'   => 'bg-green-100 text-green-800',
                                default   => 'bg-gray-100 text-gray-700',
                            } }}">
                            {{ $member->role ?? 'Member' }}
                        </span>
                    </div>
                    @php
                        $statusConfig = match($member->status ?? 'active') {
                            'active'    => ['label' => 'Active',    'class' => 'bg-green-100 text-green-700'],
                            'deceased'  => ['label' => 'Deceased',  'class' => 'bg-gray-100 text-gray-600'],
                            'moved_out' => ['label' => 'Moved Out', 'class' => 'bg-yellow-100 text-yellow-700'],
                            'inactive'  => ['label' => 'Inactive',  'class' => 'bg-red-100 text-red-600'],
                            default     => ['label' => 'Active',    'class' => 'bg-green-100 text-green-700'],
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusConfig['class'] }}">
                        {{ $statusConfig['label'] }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Phone</dt>
                    <dd class="text-gray-800">{{ $member->phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email</dt>
                    <dd class="text-gray-800">{{ $member->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Date of Birth</dt>
                    <dd class="text-gray-800">
                        @if($member->date_of_birth)
                            {{ $member->date_of_birth->format('d M Y') }}
                            <span class="text-gray-400 text-xs ml-1">({{ $member->date_of_birth->age }} yrs)</span>
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Registered</dt>
                    <dd class="text-gray-800">{{ $member->created_at->format('d M Y') }}</dd>
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
                <button type="submit"
                        class="px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition shrink-0">
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
                            <th class="px-6 py-3 hidden sm:table-cell">Uploaded By</th>
                            <th class="px-6 py-3 hidden sm:table-cell">Date</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($member->files as $file)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3 font-medium text-gray-800">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="truncate max-w-[180px]">{{ $file->original_name ?? basename($file->path) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-gray-500">
                                    {{ isset($file->size) ? number_format($file->size / 1024, 1) . ' KB' : '—' }}
                                </td>
                                <td class="px-6 py-3 text-gray-500 hidden sm:table-cell">{{ $file->uploader->name ?? '—' }}</td>
                                <td class="px-6 py-3 text-gray-500 hidden sm:table-cell">
                                    {{ $file->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.member-files.download', $file) }}"
                                           class="p-1.5 text-gray-400 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition" title="Download">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.member-files.destroy', $file) }}"
                                              onsubmit="return confirm('Delete this file?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
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
                No documents uploaded for this member yet.
            </div>
        @endif
    </div>
</div>
@endsection
