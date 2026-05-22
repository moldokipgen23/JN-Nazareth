@extends('layouts.admin')
@section('page-title', 'Edit Link')

@section('content')
<div class="max-w-xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.important-links.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-gray-900">Edit Important Link</h2>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.important-links.update', $link) }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $link->title) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">URL <span class="text-red-500">*</span></label>
                <input type="url" name="url" value="{{ old('url', $link->url) }}" required placeholder="https://example.com"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                @error('url')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">{{ old('description', $link->description) }}</textarea>
                @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $link->sort_order) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            </div>
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published', $link->is_published) ? 'checked' : '' }}
                       class="w-4 h-4 text-teal-600 rounded border-gray-300">
                <label for="is_published" class="text-sm font-medium text-gray-700">Published (visible on website)</label>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-2 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 transition">Save Changes</button>
                <a href="{{ route('admin.important-links.index') }}" class="px-6 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
