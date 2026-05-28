@php
    $prefillClass = $prefillClass ?? null;
    $inputClass   = 'w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none';
@endphp

{{-- Tab Navigation --}}
<div class="flex border-b border-gray-200 mb-6" id="tab-nav">
    <button type="button" class="tab-btn px-5 py-3 text-sm font-semibold border-b-2 border-teal-600 text-teal-700 transition" data-tab="tab-1">
        1 · Personal Details
    </button>
    <button type="button" class="tab-btn px-5 py-3 text-sm font-semibold text-gray-400 border-b-2 border-transparent hover:text-gray-600 transition" data-tab="tab-2">
        2 · Parent / Guardian
    </button>
    <button type="button" class="tab-btn px-5 py-3 text-sm font-semibold text-gray-400 border-b-2 border-transparent hover:text-gray-600 transition" data-tab="tab-3">
        3 · Enrollment
    </button>
</div>

{{-- Section 1: Personal Details --}}
<div id="tab-1" class="tab-panel space-y-4">
    <h3 class="text-base font-semibold text-gray-800 mb-1">Personal Details</h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $student->name ?? '') }}" required class="{{ $inputClass }}">
            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', isset($student) && $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '') }}" class="{{ $inputClass }} bg-white">
            @error('date_of_birth')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
            <select name="gender" class="{{ $inputClass }} bg-white">
                <option value="">— Select —</option>
                <option value="Male" {{ old('gender', $student->gender ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender', $student->gender ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
            </select>
            @error('gender')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Blood Group</label>
            <select name="blood_group" class="{{ $inputClass }} bg-white">
                <option value="">— Select —</option>
                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                    <option value="{{ $bg }}" {{ old('blood_group', $student->blood_group ?? '') === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                @endforeach
            </select>
            @error('blood_group')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Aadhar No.</label>
            <input type="text" name="aadhar_number" value="{{ old('aadhar_number', $student->aadhar_number ?? '') }}" inputmode="numeric" placeholder="1234 5678 9012" class="{{ $inputClass }}">
            <p class="mt-1 text-xs text-gray-400">Confidential</p>
            @error('aadhar_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
        <input type="text" name="address" value="{{ old('address', $student->address ?? '') }}" class="{{ $inputClass }}">
        @error('address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
        @if(isset($student) && $student->photo)
            <div class="flex items-center gap-3 mb-2">
                <img src="{{ \App\Helpers\Settings::storageUrl($student->photo) }}" class="w-16 h-16 rounded-full object-cover border border-gray-200">
                <p class="text-xs text-gray-500">Current photo. Upload to replace.</p>
            </div>
        @endif
        <input type="file" name="photo" accept="image/*"
               class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 transition">
        @error('photo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

{{-- Section 2: Parent / Guardian --}}
<div id="tab-2" class="tab-panel hidden space-y-4">
    <h3 class="text-base font-semibold text-gray-800 mb-1">Parent / Guardian Information</h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Father's Name</label>
            <input type="text" name="father_name" value="{{ old('father_name', $student->father_name ?? '') }}" class="{{ $inputClass }}">
            @error('father_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mother's Name</label>
            <input type="text" name="mother_name" value="{{ old('mother_name', $student->mother_name ?? '') }}" class="{{ $inputClass }}">
            @error('mother_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="border-t border-gray-100 pt-4">
        <p class="text-sm font-medium text-gray-500 mb-3">Guardian (if different from parents)</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Guardian Name</label>
                <input type="text" name="guardian_name" value="{{ old('guardian_name', $student->guardian_name ?? '') }}" class="{{ $inputClass }}">
                @error('guardian_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Relationship to Student</label>
                <input type="text" name="guardian_relation" value="{{ old('guardian_relation', $student->guardian_relation ?? '') }}" placeholder="e.g. Uncle, Aunt, Grandparent" class="{{ $inputClass }}">
                @error('guardian_relation')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Primary Phone <span class="text-red-500">*</span></label>
            <input type="text" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone ?? '') }}" required class="{{ $inputClass }}">
            @error('parent_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $student->phone ?? '') }}" class="{{ $inputClass }}">
            @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $student->email ?? '') }}" class="{{ $inputClass }}">
        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

{{-- Section 3: Enrollment --}}
<div id="tab-3" class="tab-panel hidden space-y-4">
    <h3 class="text-base font-semibold text-gray-800 mb-1">Enrollment Details</h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
            <select name="academic_year" class="{{ $inputClass }} bg-white">
                @foreach(\App\Models\AcademicYear::orderBy('name', 'desc')->get() as $ay)
                    <option value="{{ $ay->name }}" {{ old('academic_year', $student->academic_year ?? $defaultYear ?? '') === $ay->name ? 'selected' : '' }}>{{ $ay->name }}</option>
                @endforeach
            </select>
            @error('academic_year')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
            <select id="class" name="class" class="{{ $inputClass }} bg-white">
                <option value="">— Select class —</option>
                @foreach($classes as $c)
                    <option value="{{ $c }}" {{ old('class', $student->class ?? $prefillClass) === $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
            @error('class')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
            <select id="section" name="section" class="{{ $inputClass }} bg-white">
                <option value="">— Select section —</option>
            </select>
            @error('section')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Roll Number</label>
            <div class="flex gap-2 items-center">
                <input type="text" id="roll_number" name="roll_number" value="{{ old('roll_number', $student->roll_number ?? $nextRoll ?? '') }}" class="{{ $inputClass }} flex-1">
                <button type="button" id="suggest-roll-btn" class="px-3 py-2 text-xs font-medium bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition whitespace-nowrap">
                    Suggest
                </button>
            </div>
            <p class="mt-1 text-xs text-gray-400">Auto-suggested based on class. Adjust if needed.</p>
            @error('roll_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Admission Date</label>
            <input type="date" name="admission_date" value="{{ old('admission_date', isset($student) && $student->admission_date ? $student->admission_date->format('Y-m-d') : now()->format('Y-m-d')) }}" class="{{ $inputClass }} bg-white">
            @error('admission_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="is_active" class="{{ $inputClass }} bg-white">
                <option value="1" {{ old('is_active', $student->is_active ?? true) ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('is_active', $student->is_active ?? true) ? '' : 'selected' }}>Inactive</option>
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
        <textarea name="notes" rows="3" class="{{ $inputClass }} resize-none">{{ old('notes', $student->notes ?? '') }}</textarea>
        @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

@push('scripts')
<script>
var sectionData = {!! $sectionList !!};

function updateSectionDropdown() {
    var classVal = document.getElementById('class').value;
    var sel = document.getElementById('section');
    var currentVal = sel.dataset.current || '';
    sel.innerHTML = '<option value="">— Select section —</option>';
    if (classVal && sectionData[classVal]) {
        sectionData[classVal].forEach(function(s) {
            var opt = document.createElement('option');
            opt.value = s;
            opt.textContent = s;
            if (s === currentVal) opt.selected = true;
            sel.appendChild(opt);
        });
    }
}

function suggestRoll() {
    var classVal = document.getElementById('class').value;
    if (!classVal) return;
    fetch('{{ route("admin.students.suggest-roll") }}?class=' + encodeURIComponent(classVal))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            document.getElementById('roll_number').value = data.roll;
        });
}

document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    var tabs = document.querySelectorAll('.tab-btn');
    var panels = document.querySelectorAll('.tab-panel');
    tabs.forEach(function(btn) {
        btn.addEventListener('click', function() {
            tabs.forEach(function(t) {
                t.classList.remove('border-teal-600', 'text-teal-700');
                t.classList.add('border-transparent', 'text-gray-400');
            });
            btn.classList.remove('border-transparent', 'text-gray-400');
            btn.classList.add('border-teal-600', 'text-teal-700');
            panels.forEach(function(p) { p.classList.add('hidden'); });
            document.getElementById(btn.dataset.tab).classList.remove('hidden');
        });
    });

    // Section dropdown
    var classSel = document.getElementById('class');
    var sectionSel = document.getElementById('section');
    sectionSel.dataset.current = '{{ old("section", $student->section ?? "") }}';
    if (classSel) {
        classSel.addEventListener('change', updateSectionDropdown);
        updateSectionDropdown();
    }

    // Suggest roll
    document.getElementById('suggest-roll-btn').addEventListener('click', suggestRoll);
});
</script>
@endpush