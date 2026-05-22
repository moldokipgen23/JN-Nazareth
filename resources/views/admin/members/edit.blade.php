@extends('layouts.admin')
@section('page-title', 'Edit Student')

@section('content')
<div class="max-w-2xl">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.members.show', $member) }}"
           class="p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Edit Student</h2>
            <p class="text-sm text-gray-500 mt-0.5">Update information for {{ $member->name }}.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.members.update', $member) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.members._form')

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-2 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 transition shadow-sm">
                    Update Student
                </button>
                <a href="{{ route('admin.members.show', $member) }}" class="px-6 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
