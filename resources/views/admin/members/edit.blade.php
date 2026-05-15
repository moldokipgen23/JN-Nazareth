@extends('layouts.admin')
@section('page-title', 'Edit Member')

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
            <h2 class="text-xl font-bold text-gray-900">Edit Member</h2>
            <p class="text-sm text-gray-500 mt-0.5">Update information for {{ $member->name }}.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.members.update', $member) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $member->name) }}" required
                       class="w-full px-3 py-2 border @error('name') border-red-400 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Phone & Email --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $member->phone) }}"
                           class="w-full px-3 py-2 border @error('phone') border-red-400 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $member->email) }}"
                           class="w-full px-3 py-2 border @error('email') border-red-400 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Date of Birth & Status --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth"
                           value="{{ old('date_of_birth', $member->date_of_birth?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border @error('date_of_birth') border-red-400 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none bg-white">
                    @error('date_of_birth')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status"
                            class="w-full px-3 py-2 border @error('status') border-red-400 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none bg-white">
                        <option value="active"    {{ old('status', $member->status) === 'active'    ? 'selected' : '' }}>Active</option>
                        <option value="inactive"  {{ old('status', $member->status) === 'inactive'  ? 'selected' : '' }}>Inactive</option>
                        <option value="moved_out" {{ old('status', $member->status) === 'moved_out' ? 'selected' : '' }}>Moved Out</option>
                        <option value="deceased"  {{ old('status', $member->status) === 'deceased'  ? 'selected' : '' }}>Deceased</option>
                    </select>
                    @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Address --}}
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input type="text" id="address" name="address" value="{{ old('address', $member->address) }}"
                       class="w-full px-3 py-2 border @error('address') border-red-400 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                @error('address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Role --}}
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                    Role <span class="text-red-500">*</span>
                </label>
                <select id="role" name="role" required
                        class="w-full px-3 py-2 border @error('role') border-red-400 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none bg-white">
                    <option value="">— Select a role —</option>
                    @foreach(['Pastor', 'Elder', 'Deacon', 'Member', 'Youth', 'Other'] as $r)
                        <option value="{{ $r }}" {{ old('role', $member->role) === $r ? 'selected' : '' }}>{{ $r }}</option>
                    @endforeach
                </select>
                @error('role')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Photo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
                @if($member->photo)
                    <div class="flex items-center gap-3 mb-2">
                        <img src="{{ \App\Helpers\Settings::storageUrl($member->photo) }}" alt="{{ $member->name }}"
                             class="w-16 h-16 rounded-full object-cover border border-gray-200">
                        <p class="text-xs text-gray-500">Current photo. Upload a new one to replace it.</p>
                    </div>
                @endif
                <input type="file" id="photo" name="photo" accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 transition">
                @error('photo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Notes --}}
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea id="notes" name="notes" rows="3"
                          class="w-full px-3 py-2 border @error('notes') border-red-400 @else border-gray-300 @enderror rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none resize-none">{{ old('notes', $member->notes) }}</textarea>
                @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 transition shadow-sm">
                    Update Member
                </button>
                <a href="{{ route('admin.members.show', $member) }}"
                   class="px-6 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
