@php
    $lbl = 'font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;';
    $inp = 'border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; box-sizing:border-box; outline:none;';
    $isEdit = isset($teacher);
    $assigned = $assigned ?? [];
@endphp

<div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:11px 15px; margin-bottom:16px; font-size:12px; color:#1e40af;">
    <strong>Directory record only — no login.</strong> This adds the teacher to the staff directory.
    To give a teacher access to sign in and view their classes, create a <a href="{{ route('admin.users.index') }}" style="color:#1d4ed8; font-weight:700;">User account</a> with the <em>Teacher</em> role.
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
            <label style="{{ $lbl }}">Email <span style="font-weight:400;color:#94a3b8;">— optional, for records</span></label>
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
        Active (show in the directory)
    </label>

    <div style="font-size:14px; font-weight:700; color:#0f172a; margin:16px 0 4px; padding-bottom:9px; border-bottom:1px solid #f1f5f9;">Classes Handled</div>
    <p style="font-size:11.5px; color:#94a3b8; margin:0 0 12px;">Which classes this teacher handles — for directory/records.</p>

    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:8px;">
        @foreach($classes as $c)
        <label style="display:flex; align-items:center; gap:8px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:9px 11px; font-size:12.5px; cursor:pointer;">
            <input type="checkbox" name="classes[]" value="{{ $c }}"
                   {{ in_array($c, old('classes', $assigned)) ? 'checked' : '' }}>
            {{ $c }}
        </label>
        @endforeach
    </div>

    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:22px;">
        <a href="{{ route('admin.teachers.index') }}" style="background:#f1f5f9; color:#475569; font-size:13px; font-weight:600; padding:10px 20px; border-radius:9px; text-decoration:none;">Cancel</a>
        <button type="submit" style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:700; padding:10px 24px; border-radius:9px; border:none; cursor:pointer;">{{ $isEdit ? 'Update Teacher' : 'Add Teacher' }}</button>
    </div>
</div>
