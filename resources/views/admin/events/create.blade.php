@extends('layouts.admin')

@section('page-title', 'Add Event')

@section('content')
<div class="max-w-2xl">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.events.index') }}"
           class="p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Add New Event</h2>
            <p class="text-sm text-gray-500 mt-0.5">Fill in the event details below.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Event Title <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       placeholder="e.g. Weekly Community Meet-Up"
                       class="w-full px-3 py-2 border @error('title') border-red-400 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                @error('title')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description" name="description" rows="4"
                          placeholder="Describe the event..."
                          class="w-full px-3 py-2 border @error('description') border-red-400 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none resize-none">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Location --}}
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <input type="text" id="location" name="location" value="{{ old('location') }}"
                       placeholder="e.g. Main Sanctuary"
                       class="w-full px-3 py-2 border @error('location') border-red-400 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                @error('location')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Dates --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">
                        Starts At <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" id="starts_at" name="starts_at"
                           value="{{ old('starts_at') }}" required
                           class="w-full px-3 py-2 border @error('starts_at') border-red-400 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                    @error('starts_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="ends_at" class="block text-sm font-medium text-gray-700 mb-1">Ends At</label>
                    <input type="datetime-local" id="ends_at" name="ends_at"
                           value="{{ old('ends_at') }}"
                           class="w-full px-3 py-2 border @error('ends_at') border-red-400 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                    @error('ends_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Image --}}
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Event Image</label>
                <input type="file" id="image" name="image" accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                @error('image')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition shadow-sm">
                    Save Event
                </button>
                <a href="{{ route('admin.events.index') }}"
                   class="px-6 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
