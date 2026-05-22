@extends('layouts.admin')
@section('page-title', 'Edit Folder')

@section('content')

<div style="max-width:600px;">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
        <a href="{{ route('admin.gallery-folders.index', ['type' => $folder->type]) }}" style="color:#64748b; text-decoration:none; font-size:13px; display:flex; align-items:center; gap:5px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            {{ $folder->type === 'programs' ? 'Student Life Albums' : 'Gallery Folders' }}
        </a>
        <span style="color:#cbd5e1;">/</span>
        <span style="font-size:13px; color:#0f172a; font-weight:600;">Edit: {{ $folder->name }}</span>
    </div>

    <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); padding:28px;">
        @if($errors->any())
            <div style="background:#fff1f2; border:1px solid #fecdd3; color:#9f1239; padding:12px 16px; border-radius:10px; font-size:13px; margin-bottom:16px;">
                <ul style="margin:0; padding-left:16px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.gallery-folders.update', $folder) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:5px;">Folder Name <span style="color:#e11d48;">*</span></label>
                <input type="text" name="name" value="{{ old('name', $folder->name) }}" required maxlength="255"
                       style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; box-sizing:border-box;"
                       onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:5px;">Description</label>
                <textarea name="description" rows="3" maxlength="1000"
                          style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; box-sizing:border-box; resize:vertical;"
                          onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">{{ old('description', $folder->description) }}</textarea>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:5px;">Cover Image <span style="color:#94a3b8; font-weight:400;">(leave blank to keep current)</span></label>
                @if($folder->cover_image)
                    <div style="margin-bottom:8px;">
                        <img src="{{ \App\Helpers\Settings::storageUrl($folder->cover_image) }}"
                             alt="Cover" style="height:80px; width:auto; border-radius:8px; object-fit:cover; border:1px solid #e2e8f0;">
                    </div>
                @endif
                <input type="file" name="cover_image" accept="image/*"
                       style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; box-sizing:border-box; background:#f8fafc;">
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:5px;">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $folder->sort_order) }}" min="0"
                       style="width:120px; padding:9px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px;"
                       onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit"
                        style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; border:none; padding:10px 24px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer;">
                    Save Changes
                </button>
                <a href="{{ route('admin.gallery-folders.show', $folder) }}"
                   style="background:#f8fafc; color:#475569; border:1px solid #e2e8f0; padding:10px 20px; border-radius:10px; font-size:13px; font-weight:600; text-decoration:none;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
