@php
    $lbl = 'font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;';
    $inp = 'border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; box-sizing:border-box; outline:none;';
    $isEdit = isset($teacher);
    $classes = \App\Models\Student::classes();
    $ctClasses = $ctClasses ?? [];
    $ctSection = $ctSection ?? 'A';
    $sections = $sections ?? collect();
    $subjectsList = \App\Models\Subject::active()->orderBy('sort_order')->orderBy('name')->get();
    $existingSubjectAssignments = $isEdit && $teacher ? $teacher->subjectAssignments() : [];
    // For section dropdown: collect all section names available
    $sectionNames = $sections->pluck('name')->unique()->sort()->values();
    if ($sectionNames->isEmpty()) { $sectionNames = collect(['A']); }
@endphp

<div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:11px 15px; margin-bottom:16px; font-size:12px; color:#1e40af;">
    <strong>Directory record first — login is optional.</strong>
    This creates a teacher record in the staff directory. After saving, you can
    <strong>Create Login</strong> from the teacher list to give them portal access.
</div>

<div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; padding:24px 28px;">

    <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:14px; padding-bottom:9px; border-bottom:1px solid #f1f5f9;">Teacher Details</div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
        <div>
            <label style="{{ $lbl }}">Full Name <span style="color:#e11d48;">*</span></label>
            <input type="text" name="name" value="{{ old('name', $teacher->name ?? '') }}" required style="{{ $inp }}">
        </div>
        <div>
            <label style="{{ $lbl }}">Designation</label>
            <input type="text" name="designation" value="{{ old('designation', $teacher->designation ?? '') }}" placeholder="e.g. Class Teacher" style="{{ $inp }}">
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
        <div>
            <label style="{{ $lbl }}">Subject(s)</label>
            <input type="text" name="subjects" value="{{ old('subjects', $teacher->subjects ?? '') }}" placeholder="e.g. English, Mathematics" style="{{ $inp }}">
        </div>
        <div>
            <label style="{{ $lbl }}">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $teacher->phone ?? '') }}" style="{{ $inp }}">
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
        <div>
            <label style="{{ $lbl }}">Email <span style="font-weight:400;color:#94a3b8;">— used for login if created</span></label>
            <input type="email" name="email" value="{{ old('email', $teacher->email ?? '') }}" style="{{ $inp }}">
        </div>
        <div>
            <label style="{{ $lbl }}">Display Order</label>
            <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $teacher->sort_order ?? 0) }}" style="{{ $inp }}">
        </div>
    </div>

    <div style="margin-bottom:16px;">
        <label style="{{ $lbl }}">Photo</label>
        @if($isEdit && $teacher->photo)
            <img src="{{ \App\Helpers\Settings::storageUrl($teacher->photo) }}" style="height:54px; width:54px; border-radius:50%; object-fit:cover; border:1px solid #e2e8f0; margin-bottom:6px; display:block;">
        @endif
        <input type="file" name="photo" accept="image/*" style="{{ $inp }} padding:6px;">
    </div>

    <label style="display:flex; align-items:center; gap:8px; font-size:12.5px; color:#374151; margin-bottom:6px;">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $teacher->is_active ?? true) ? 'checked' : '' }}>
        Active (available for assignments, login works)
    </label>

    <div style="font-size:14px; font-weight:700; color:#0f172a; margin:18px 0 4px; padding-bottom:9px; border-bottom:1px solid #f1f5f9;">Class Teacher <span style="font-weight:400;font-size:11px;color:#94a3b8;">— attendance only, once per day</span></div>
    <p style="font-size:11.5px; color:#94a3b8; margin:0 0 12px;">Which class is this teacher the <strong>class teacher</strong> of? They'll mark attendance <strong>once per day</strong> for this class. A teacher should normally be assigned <strong>only 1 class</strong>.</p>

    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:8px;">
        @foreach($classes as $c)
        <label style="display:flex; align-items:center; gap:8px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:9px 11px; font-size:12.5px; cursor:pointer;">
            <input type="checkbox" name="ct_classes[]" value="{{ $c }}" class="ct-checkbox"
                   {{ in_array($c, old('ct_classes', $ctClasses ?? [])) ? 'checked' : '' }}>
            {{ $c }}
        </label>
        @endforeach
    </div>

    {{-- Disclaimer & override — shown when multiple classes are selected --}}
    <div id="ct-disclaimer" style="display:none; margin-top:12px; background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:12px 14px;">
        <div style="font-size:13px; font-weight:700; color:#991b1b; margin-bottom:6px;">⚠️ Multiple Classes Selected</div>
        <div style="font-size:12px; color:#7f1d1d; line-height:1.5; margin-bottom:10px;">
            A class teacher takes <strong>attendance once per day</strong> for their assigned class.
            Assigning <strong><span id="ct-count">0</span> classes</strong> to one teacher means they must
            take attendance for each of these classes daily.
            <br><br>
            This is not recommended unless the teacher has sufficient free periods.
        </div>
        <label style="display:flex; align-items:flex-start; gap:8px; font-size:12px; color:#7f1d1d; cursor:pointer;">
            <input type="checkbox" name="ct_override" value="1" {{ old('ct_override') ? 'checked' : '' }} style="margin-top:1px;">
            <span>I understand the responsibility. Allow this teacher to be class teacher for <strong><span id="ct-count2">0</span> classes</strong>.</span>
        </label>
        @error('ct_override')
            <div style="font-size:11px; color:#dc2626; margin-top:4px;">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-top:8px;">
        <label style="{{ $lbl }}">Section for these classes</label>
        <select name="ct_section" style="{{ $inp }} max-width:120px;">
            @foreach($sections->pluck('name')->unique()->sort()->values() as $s)
                <option value="{{ $s }}" {{ old('ct_section', $ctSection) === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
    </div>

    <script>
    (function() {
        const checkboxes = document.querySelectorAll('.ct-checkbox');
        const disclaimer = document.getElementById('ct-disclaimer');
        const countSpan = document.getElementById('ct-count');
        const countSpan2 = document.getElementById('ct-count2');
        function updateDisclaimer() {
            const checked = document.querySelectorAll('.ct-checkbox:checked').length;
            if (disclaimer) {
                disclaimer.style.display = checked > 1 ? 'block' : 'none';
                if (countSpan) countSpan.textContent = checked;
                if (countSpan2) countSpan2.textContent = checked;
            }
        }
        checkboxes.forEach(cb => cb.addEventListener('change', updateDisclaimer));
        updateDisclaimer();
    })();
    </script>

    @unless($hideSubjects ?? false)
    {{-- ─────────── SUBJECT TEACHER ASSIGNMENTS ─────────── --}}
    <div style="font-size:14px; font-weight:700; color:#0f172a; margin:20px 0 4px; padding-bottom:9px; border-bottom:1px solid #f1f5f9;">Subjects Taught</div>
    <p style="font-size:11.5px; color:#94a3b8; margin:0 0 12px;">Which subjects does this teacher teach, and in which classes/sections? They'll be able to enter marks for these.</p>

    <div id="subject-rows" style="display:flex; flex-direction:column; gap:8px;">
        @php
            $oldRows = old('subject_assignments', null);
            if (is_array($oldRows)) {
                $rows = $oldRows;
            } else {
                $rows = $existingSubjectAssignments;
            }
        @endphp
        @forelse($rows as $i => $row)
            @include('admin.teachers._subject-row', ['i' => $i, 'row' => $row])
        @empty
        @endforelse
    </div>

    <button type="button" onclick="addSubjectRow()" style="background:#f0fdfa; color:#0f766e; border:1.5px dashed #99f6e4; border-radius:8px; padding:8px 16px; font-size:12px; font-weight:700; cursor:pointer; margin-top:10px;">+ Add Subject</button>

    {{-- Template for new rows --}}
    <template id="subject-row-template">
        @include('admin.teachers._subject-row', ['i' => '__INDEX__', 'row' => []])
    </template>

    <script>
        let subjectRowIndex = {{ count($rows) }};
        function addSubjectRow() {
            const tpl = document.getElementById('subject-row-template').innerHTML.replace(/__INDEX__/g, subjectRowIndex++);
            document.getElementById('subject-rows').insertAdjacentHTML('beforeend', tpl);
        }
    </script>
    @endunless

    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:22px;">
        <a href="{{ route('admin.teachers.index') }}" style="background:#f1f5f9; color:#475569; font-size:13px; font-weight:600; padding:10px 20px; border-radius:9px; text-decoration:none;">Cancel</a>
        <button type="submit" id="submitBtn" style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:700; padding:10px 24px; border-radius:9px; border:none; cursor:pointer;">{{ $isEdit ? 'Update Teacher' : 'Add Teacher' }}</button>
    </div>
</div>

<script>
document.querySelector('form').addEventListener('submit', function (e) {
    const checked = document.querySelectorAll('input[name="ct_classes[]"]:checked');
    if (checked.length > 1) {
        const msg = checked.length + ' classes selected for this class teacher.\n\n'
            + '⚠️ Important: A class teacher marks attendance for their class once a day.\n'
            + 'Assigning multiple classes means this teacher will need to take attendance\n'
            + 'for ALL selected classes daily.\n\n'
            + 'Are you sure you want to assign ' + checked.length + ' classes to one teacher?';
        if (!confirm(msg)) {
            e.preventDefault();
        }
    }
});
</script>
