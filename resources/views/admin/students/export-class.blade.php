@extends('layouts.admin')
@section('title', 'Export Students')
@section('content')
<div class="max-w-5xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.students.index') }}"
           class="p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Export Students</h2>
            <p class="text-sm text-gray-500 mt-0.5">Download student data and documents per class.</p>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
        <div class="flex items-end gap-4 flex-wrap">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Class</label>
                <select name="class" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none bg-white">
                    <option value="">— Choose class —</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}" {{ $class === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                    class="px-5 py-2 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 transition shadow-sm">
                View Students
            </button>
        </div>
    </form>

    @if($class && $students->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
                <h3 class="font-semibold text-gray-900">{{ $class }} — {{ $students->count() }} student(s)</h3>
                <div class="flex gap-2">
                    <a href="{{ route('admin.students.export-class-csv', ['class' => $class]) }}"
                       class="px-4 py-2 bg-teal-600 text-white text-xs font-semibold rounded-lg hover:bg-teal-700 transition">
                        Download CSV
                    </a>
                    <a href="{{ route('admin.students.download-class-docs', ['class' => $class]) }}"
                       class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold rounded-lg hover:bg-gray-700 transition">
                        Download All Documents
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Admission No</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Roll</th>
                            <th class="px-4 py-3">Father</th>
                            <th class="px-4 py-3">Mother</th>
                            <th class="px-4 py-3">Phone</th>
                            <th class="px-4 py-3">DOB</th>
                            <th class="px-4 py-3">Docs</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($students as $i => $s)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $s->admission_number ?? '—' }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $s->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $s->roll_number ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $s->father_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $s->mother_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $s->parent_phone ?? $s->phone ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $s->date_of_birth?->format('d M Y') ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $s->files->count() > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-400' }}">
                                    {{ $s->files->count() }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($class)
        <div class="text-center py-12 text-gray-400 bg-white rounded-xl border border-gray-200 shadow-sm">
            <p class="text-sm font-medium">No active students in {{ $class }}.</p>
        </div>
    @endif
</div>
@endsection