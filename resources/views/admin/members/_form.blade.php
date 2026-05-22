@php
    $prefillClass = $prefillClass ?? null;
    $defaultYear  = \App\Helpers\Settings::get('academic_year', '');
    $inputClass   = 'w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none';
@endphp

{{-- Class & Roll --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="class" class="block text-sm font-medium text-gray-700 mb-1">Class</label>
        <select id="class" name="class" class="{{ $inputClass }} bg-white">
            <option value="">— Select class —</option>
            @foreach($classes as $c)
                <option value="{{ $c }}" {{ old('class', $member->class ?? $prefillClass) === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
        @error('class')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="roll_number" class="block text-sm font-medium text-gray-700 mb-1">Roll Number</label>
        <input type="text" id="roll_number" name="roll_number" value="{{ old('roll_number', $member->roll_number ?? '') }}" class="{{ $inputClass }}">
        @error('roll_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

{{-- Admission No. & Aadhar No. --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="admission_number" class="block text-sm font-medium text-gray-700 mb-1">Admission No.</label>
        <input type="text" id="admission_number" name="admission_number" value="{{ old('admission_number', $member->admission_number ?? '') }}" class="{{ $inputClass }}">
        @error('admission_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="aadhar_number" class="block text-sm font-medium text-gray-700 mb-1">Aadhar No.</label>
        <input type="text" id="aadhar_number" name="aadhar_number" value="{{ old('aadhar_number', $member->aadhar_number ?? '') }}" inputmode="numeric" placeholder="1234 5678 9012" class="{{ $inputClass }}">
        <p class="mt-1 text-xs text-gray-400">Confidential — visible only in the admin panel.</p>
        @error('aadhar_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

{{-- Name --}}
<div>
    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
    <input type="text" id="name" name="name" value="{{ old('name', $member->name ?? '') }}" required class="{{ $inputClass }}">
    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

{{-- Father & Mother --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="father_name" class="block text-sm font-medium text-gray-700 mb-1">Father's Name</label>
        <input type="text" id="father_name" name="father_name" value="{{ old('father_name', $member->father_name ?? '') }}" class="{{ $inputClass }}">
        @error('father_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="mother_name" class="block text-sm font-medium text-gray-700 mb-1">Mother's Name</label>
        <input type="text" id="mother_name" name="mother_name" value="{{ old('mother_name', $member->mother_name ?? '') }}" class="{{ $inputClass }}">
        @error('mother_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

{{-- Parent phone & DOB --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="parent_phone" class="block text-sm font-medium text-gray-700 mb-1">Parent Phone</label>
        <input type="text" id="parent_phone" name="parent_phone" value="{{ old('parent_phone', $member->parent_phone ?? '') }}" class="{{ $inputClass }}">
        @error('parent_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', isset($member) && $member->date_of_birth ? $member->date_of_birth->format('Y-m-d') : '') }}" class="{{ $inputClass }} bg-white">
        @error('date_of_birth')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

{{-- Admission date & Academic year --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="admission_date" class="block text-sm font-medium text-gray-700 mb-1">Admission Date</label>
        <input type="date" id="admission_date" name="admission_date" value="{{ old('admission_date', isset($member) && $member->admission_date ? $member->admission_date->format('Y-m-d') : '') }}" class="{{ $inputClass }} bg-white">
        @error('admission_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
        <input type="text" id="academic_year" name="academic_year" value="{{ old('academic_year', $member->academic_year ?? $defaultYear) }}" placeholder="2026–2027" class="{{ $inputClass }}">
        @error('academic_year')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

{{-- Section & Student phone --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="section" class="block text-sm font-medium text-gray-700 mb-1">Section</label>
        <input type="text" id="section" name="section" value="{{ old('section', $member->section ?? 'A') }}" class="{{ $inputClass }}">
        @error('section')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email (optional)</label>
        <input type="email" id="email" name="email" value="{{ old('email', $member->email ?? '') }}" class="{{ $inputClass }}">
        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>


{{-- Address --}}
<div>
    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
    <input type="text" id="address" name="address" value="{{ old('address', $member->address ?? '') }}" class="{{ $inputClass }}">
    @error('address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

{{-- Photo --}}
<div>
    <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
    @if(isset($member) && $member->photo)
        <div class="flex items-center gap-3 mb-2">
            <img src="{{ \App\Helpers\Settings::storageUrl($member->photo) }}" class="w-16 h-16 rounded-full object-cover border border-gray-200">
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
    <textarea id="notes" name="notes" rows="3" class="{{ $inputClass }} resize-none">{{ old('notes', $member->notes ?? '') }}</textarea>
    @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

{{-- Active --}}
<div class="flex items-center gap-2">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $member->is_active ?? true) ? 'checked' : '' }}
           class="w-4 h-4 text-teal-600 rounded border-gray-300 focus:ring-teal-500">
    <label for="is_active" class="text-sm font-medium text-gray-700">Currently active student</label>
</div>
