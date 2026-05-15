@extends('layouts.admin')
@section('page-title', 'Document Manager')

@section('content')

@php
    $flipDir  = $sortDir === 'asc' ? 'desc' : 'asc';
    $sortLink = fn($col) => request()->fullUrlWithQuery(['sort' => $col, 'dir' => $sortBy === $col ? $flipDir : 'asc']);
    $arrow    = fn($col) => $sortBy === $col ? ($sortDir === 'asc' ? ' ▲' : ' ▼') : '';
@endphp

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">Document Manager</h2>
        <p style="font-size:12px; color:#64748b; margin:3px 0 0;">{{ $folders->count() }} root folder{{ $folders->count() != 1 ? 's' : '' }}</p>
    </div>
    <div style="display:flex; gap:6px; align-items:center;">
        <span style="font-size:11px; color:#94a3b8; font-weight:600;">Sort folders:</span>
        <a href="{{ $sortLink('name') }}"
           style="font-size:11px; font-weight:600; padding:5px 12px; border-radius:7px; text-decoration:none;
                  {{ $sortBy === 'name' ? 'background:#0f766e; color:#fff;' : 'background:#f1f5f9; color:#64748b;' }}">
            Name{{ $arrow('name') }}
        </a>
        <a href="{{ $sortLink('date') }}"
           style="font-size:11px; font-weight:600; padding:5px 12px; border-radius:7px; text-decoration:none;
                  {{ $sortBy === 'date' ? 'background:#0f766e; color:#fff;' : 'background:#f1f5f9; color:#64748b;' }}">
            Date{{ $arrow('date') }}
        </a>
    </div>
</div>

{{-- Create root folder --}}
<div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); padding:20px; margin-bottom:20px;">
    <div style="font-size:13px; font-weight:700; color:#0f172a; margin-bottom:12px;">Create New Folder</div>
    <form method="POST" action="{{ route('admin.folders.store') }}" style="display:flex; gap:10px; align-items:flex-end;">
        @csrf
        <div style="flex:1; max-width:400px;">
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Folder name (e.g. 2025 Finance)"
                   style="width:100%; padding:10px 14px; border-radius:10px; border:1px solid #e2e8f0; font-size:13px; outline:none; box-sizing:border-box;"
                   onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
            @error('name')<p style="color:#e11d48; font-size:11px; margin-top:4px;">{{ $message }}</p>@enderror
        </div>
        <button type="submit" style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; border:none; padding:10px 20px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; white-space:nowrap;">
            Create Folder
        </button>
    </form>
</div>

{{-- Folders grid --}}
@if($folders->count())
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:14px;">
    @foreach($folders as $folder)
    <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.05); overflow:hidden;">
        <a href="{{ route('admin.folders.show', $folder) }}" style="display:block; padding:20px; text-decoration:none;" onmouseover="this.parentElement.style.borderColor='#99f6e4'" onmouseout="this.parentElement.style.borderColor='#f1f5f9'">
            <div style="width:48px; height:48px; background:linear-gradient(135deg,#fef9c3,#fde68a); border-radius:12px; display:flex; align-items:center; justify-content:center; margin-bottom:12px;">
                <svg width="24" height="24" fill="#d97706" viewBox="0 0 24 24"><path d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
            </div>
            <div style="font-size:13px; font-weight:700; color:#0f172a; margin-bottom:4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $folder->name }}</div>
            <div style="font-size:11px; color:#94a3b8;">{{ $folder->children->count() }} subfolder{{ $folder->children->count() != 1 ? 's' : '' }}</div>
        </a>
        <div style="border-top:1px solid #f8fafc; padding:8px 16px; display:flex; justify-content:flex-end; gap:6px;">
            <form method="POST" action="{{ route('admin.folders.destroy', $folder) }}" onsubmit="return confirm('Delete folder &quot;{{ addslashes($folder->name) }}&quot; and all its contents?')">
                @csrf @method('DELETE')
                <button type="submit" style="background:none; border:none; color:#94a3b8; font-size:11px; cursor:pointer; padding:3px 6px; border-radius:5px;" onmouseover="this.style.color='#e11d48'" onmouseout="this.style.color='#94a3b8'">Delete</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@else
<div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; padding:60px 20px; text-align:center;">
    <svg width="48" height="48" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 12px; display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
    <p style="font-size:14px; font-weight:600; color:#334155; margin:0 0 6px;">No folders yet</p>
    <p style="font-size:13px; color:#94a3b8; margin:0;">Create your first folder above to start organising documents.</p>
</div>
@endif

@endsection
