@extends('layouts.admin')
@section('page-title', 'Upload Download')

@section('content')
<div class="max-w-xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.downloads.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-gray-900">Upload Download</h2>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.downloads.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                <select name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-teal-500 outline-none">
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">File <span class="text-red-500">*</span></label>
                <input type="file" name="file" required
                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-teal-50 file:text-teal-700">
                <p class="mt-1 text-xs text-gray-400">Max 10 MB. PDF, DOC, images, etc.</p>
                @error('file')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            </div>
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}
                       class="w-4 h-4 text-teal-600 rounded border-gray-300">
                <label for="is_published" class="text-sm font-medium text-gray-700">Published (visible on website)</label>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-2 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 transition">Upload</button>
                <a href="{{ route('admin.downloads.index') }}" class="px-6 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
