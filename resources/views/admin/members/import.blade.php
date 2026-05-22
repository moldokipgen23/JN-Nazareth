@extends('layouts.admin')
@section('page-title', 'Import Students')

@section('content')
<div class="max-w-2xl">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.members.index') }}"
           class="p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Import Students from CSV</h2>
            <p class="text-sm text-gray-500 mt-0.5">Bulk-add students and assign them to a class &amp; section.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- How it works --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-5 text-sm text-blue-900">
        <p class="font-semibold mb-1.5">How it works</p>
        <ul class="list-disc list-inside space-y-1 text-blue-800">
            <li>The first row of the CSV must be column headers.</li>
            <li>Required column: <code class="bg-white px-1 rounded">name</code>.</li>
            <li>Optional columns: <code class="bg-white px-1 rounded">roll_number, admission_number, father_name, mother_name, parent_phone, phone, email, date_of_birth, aadhar_number, address, class, section</code>.</li>
            <li>If a row leaves <code class="bg-white px-1 rounded">class</code>/<code class="bg-white px-1 rounded">section</code> blank, the values you choose below are used.</li>
            <li>A <code class="bg-white px-1 rounded">class</code> value in the CSV must match a school class exactly (e.g. <em>Class III</em>).</li>
        </ul>
        <a href="{{ route('admin.members.import.template') }}"
           class="inline-flex items-center gap-1.5 mt-3 text-blue-700 font-semibold hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
            Download CSV template
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.members.import') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Assign all to class</label>
                <select name="class" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-teal-500 focus:border-teal-500">
                    <option value="">— Use class column from CSV —</option>
                    @foreach($classes as $class)
                        <option value="{{ $class }}" @selected(old('class') === $class)>{{ $class }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Applied to any row that has no <code>class</code> value in the CSV.</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                    <input type="text" name="section" value="{{ old('section') }}" maxlength="10"
                           placeholder="e.g. A"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                    <input type="text" name="academic_year" value="{{ old('academic_year') }}" maxlength="20"
                           placeholder="e.g. 2026-27"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CSV file</label>
                <input type="file" name="file" accept=".csv,text/csv" required
                       class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                <p class="text-xs text-gray-500 mt-1">CSV file, up to 4 MB.</p>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-2 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 transition shadow-sm">
                    Import Students
                </button>
                <a href="{{ route('admin.members.index') }}" class="px-6 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
