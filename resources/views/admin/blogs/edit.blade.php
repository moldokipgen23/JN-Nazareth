@extends('layouts.admin')

@section('page-title', 'Edit Blog Post')

@section('content')
<div class="max-w-3xl">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.blogs.index') }}"
           class="p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Edit Blog Post</h2>
            <p class="text-sm text-gray-500 mt-0.5">Editing: {{ Str::limit($blog->title, 60) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.blogs.update', $blog) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title', $blog->title) }}" required
                       class="w-full px-3 py-2 border @error('title') border-red-400 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                @error('title')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Content --}}
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                    Content <span class="text-red-500">*</span>
                </label>
                <textarea id="content" name="content" rows="14" required
                          class="w-full px-3 py-2 border @error('content') border-red-400 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none resize-y">{{ old('content', $blog->content) }}</textarea>
                @error('content')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Featured Image --}}
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Featured Image</label>
                @if($blog->image)
                    <div class="mb-2">
                        <img src="{{ \App\Helpers\Settings::storageUrl($blog->image) }}" alt="Current image"
                             class="h-32 rounded-lg object-cover border border-gray-200">
                        <p class="text-xs text-gray-400 mt-1">Upload a new image to replace the current one.</p>
                    </div>
                @endif
                <input type="file" id="image" name="image" accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                @error('image')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Published toggle --}}
            <div class="flex items-center gap-3">
                <input type="checkbox" id="published" name="published" value="1"
                       {{ old('published', $blog->published) ? 'checked' : '' }}
                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="published" class="text-sm font-medium text-gray-700">Published</label>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition shadow-sm">
                    Update Post
                </button>
                <a href="{{ route('admin.blogs.index') }}"
                   class="px-6 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
