@extends('layouts.admin')
@section('page-title', $type === 'programs' ? 'New Student Life Album' : 'New Gallery Folder')

@section('content')

<div style="max-width:600px;">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
        <a href="{{ route('admin.gallery-folders.index', ['type' => $type]) }}" style="color:#64748b; text-decoration:none; font-size:13px; display:flex; align-items:center; gap:5px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            {{ $type === 'programs' ? 'Student Life Albums' : 'Gallery Folders' }}
        </a>
        <span style="color:#cbd5e1;">/</span>
        <span style="font-size:13px; color:#0f172a; font-weight:600;">New Folder</span>
    </div>

    <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); padding:28px;">
        <h2 style="font-size:16px; font-weight:700; color:#0f172a; margin:0 0 20px;">
            {{ $type === 'programs' ? 'Create Student Life Album' : 'Create Gallery Folder' }}
        </h2>

        @if($errors->any())
            <div style="background:#fff1f2; border:1px solid #fecdd3; color:#9f1239; padding:12px 16px; border-radius:10px; font-size:13px; margin-bottom:16px;">
                <ul style="margin:0; padding-left:16px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.gallery-folders.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:5px;">Folder Name <span style="color:#e11d48;">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="255"
                       placeholder="{{ $type === 'programs' ? 'e.g. Community Clean-Up Day' : 'e.g. Annual Gathering 2024' }}"
                       style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; box-sizing:border-box;"
                       onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:5px;">Description <span style="color:#94a3b8; font-weight:400;">(optional)</span></label>
                <textarea name="description" rows="3" maxlength="1000"
                          placeholder="Short description of this album..."
                          style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; box-sizing:border-box; resize:vertical;"
                          onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">{{ old('description') }}</textarea>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:5px;">Cover Image <span style="color:#94a3b8; font-weight:400;">(optional — auto-set from first upload)</span></label>
                <input type="file" name="cover_image" accept="image/*"
                       style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; box-sizing:border-box; background:#f8fafc;">
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:5px;">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                       style="width:120px; padding:9px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px;"
                       onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit"
                        style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; border:none; padding:10px 24px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer;">
                    Create Folder
                </button>
                <a href="{{ route('admin.gallery-folders.index', ['type' => $type]) }}"
                   style="background:#f8fafc; color:#475569; border:1px solid #e2e8f0; padding:10px 20px; border-radius:10px; font-size:13px; font-weight:600; text-decoration:none;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
