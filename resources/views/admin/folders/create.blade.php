@extends('layouts.admin')
@section('page-title', 'Create Folder')

@section('content')

<div style="display:flex; align-items:center; gap:6px; font-size:13px; margin-bottom:20px;">
    <a href="{{ route('admin.folders.index') }}" style="color:#0d9488; text-decoration:none; font-weight:500;">Documents</a>
    @if($parent)
    <svg width="14" height="14" fill="none" stroke="#cbd5e1" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.folders.show', $parent) }}" style="color:#0d9488; text-decoration:none; font-weight:500;">{{ $parent->name }}</a>
    @endif
    <svg width="14" height="14" fill="none" stroke="#cbd5e1" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
    <span style="color:#0f172a; font-weight:700;">New Folder</span>
</div>

<div style="max-width:480px;">
    <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); padding:24px;">
        <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:18px;">Create New Folder</div>

        @if($errors->any())
        <div style="background:#fff1f2; border:1px solid #fecdd3; border-radius:10px; padding:12px 16px; margin-bottom:16px; font-size:13px; color:#9f1239;">
            <ul style="margin:0; padding-left:16px;">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.folders.store') }}">
            @csrf
            @if($parent)
            <input type="hidden" name="parent_id" value="{{ $parent->id }}">
            @endif

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:5px;">
                    Folder Name *
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       placeholder="{{ $parent ? 'Subfolder name' : 'e.g. 2025 Finance Documents' }}"
                       style="width:100%; padding:10px 14px; border-radius:9px; border:1px solid #e2e8f0; font-size:13px; outline:none; box-sizing:border-box;"
                       onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
                @error('name')<p style="color:#e11d48; font-size:11px; margin-top:4px;">{{ $message }}</p>@enderror
            </div>

            @if($parent)
            <div style="background:#f8fafc; border-radius:9px; padding:10px 14px; font-size:12px; color:#64748b; margin-bottom:16px;">
                Will be created inside: <strong style="color:#0f172a;">{{ $parent->name }}</strong>
            </div>
            @endif

            <div style="display:flex; gap:10px;">
                <button type="submit"
                        style="flex:1; background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; border:none; padding:11px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer;">
                    Create Folder
                </button>
                <a href="{{ $parent ? route('admin.folders.show', $parent) : route('admin.folders.index') }}"
                   style="flex:1; background:#f1f5f9; color:#64748b; border:none; padding:11px; border-radius:10px; font-size:13px; font-weight:600; text-decoration:none; text-align:center; display:block;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
