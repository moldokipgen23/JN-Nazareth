@extends('layouts.admin')
@section('page-title', $folder->name)

@section('content')

{{-- Breadcrumb --}}
<div style="display:flex; align-items:center; gap:6px; font-size:13px; margin-bottom:20px; flex-wrap:wrap;">
    <a href="{{ route('admin.folders.index') }}" style="color:#0d9488; text-decoration:none; font-weight:500; display:flex; align-items:center; gap:4px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
        Documents
    </a>
    @if($folder->parent)
    <svg width="14" height="14" fill="none" stroke="#cbd5e1" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.folders.show', $folder->parent) }}" style="color:#0d9488; text-decoration:none; font-weight:500;">{{ $folder->parent->name }}</a>
    @endif
    <svg width="14" height="14" fill="none" stroke="#cbd5e1" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
    <span style="color:#0f172a; font-weight:700;">{{ $folder->name }}</span>
</div>

<div style="display:grid; grid-template-columns:1fr 280px; gap:20px; align-items:start;">

    {{-- Main content --}}
    <div>

        {{-- Subfolders --}}
        @if($subfolders->count())
        <div style="margin-bottom:20px;">
            <div style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:10px;">Subfolders</div>
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(160px, 1fr)); gap:10px;">
                @foreach($subfolders as $sub)
                <a href="{{ route('admin.folders.show', $sub) }}" style="display:flex; align-items:center; gap:10px; background:#fff; border:1px solid #f1f5f9; border-radius:10px; padding:12px; text-decoration:none;" onmouseover="this.style.borderColor='#99f6e4'" onmouseout="this.style.borderColor='#f1f5f9'">
                    <svg width="20" height="20" fill="#d97706" viewBox="0 0 24 24"><path d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                    <span style="font-size:12px; font-weight:600; color:#334155; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $sub->name }}</span>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Files table --}}
        @php
            $flipDir = $sortDir === 'asc' ? 'desc' : 'asc';
            $sortLink = fn($col) => request()->fullUrlWithQuery(['sort' => $col, 'dir' => $sortBy === $col ? $flipDir : 'asc']);
            $arrow = fn($col) => $sortBy === $col ? ($sortDir === 'asc' ? ' ▲' : ' ▼') : '';
        @endphp
        <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); overflow:hidden;">
            <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                <span style="font-size:13px; font-weight:700; color:#0f172a;">Files <span style="color:#94a3b8; font-weight:400;">({{ $documents->count() }})</span></span>
                <div style="display:flex; gap:6px; align-items:center;">
                    <span style="font-size:11px; color:#94a3b8; font-weight:600;">Sort:</span>
                    @foreach(['date' => 'Date', 'name' => 'Name', 'size' => 'Size'] as $col => $label)
                    <a href="{{ $sortLink($col) }}"
                       style="font-size:11px; font-weight:600; padding:4px 10px; border-radius:6px; text-decoration:none;
                              {{ $sortBy === $col ? 'background:#0f766e; color:#fff;' : 'background:#f1f5f9; color:#64748b;' }}">
                        {{ $label }}{{ $arrow($col) }}
                    </a>
                    @endforeach
                </div>
            </div>

            @if($documents->count())
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead>
                        <tr style="background:#f8fafc; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.06em;">
                            <th style="padding:10px 20px; text-align:left;">
                                <a href="{{ $sortLink('name') }}" style="color:{{ $sortBy==='name'?'#0f766e':'#64748b' }}; text-decoration:none;">Name{{ $arrow('name') }}</a>
                            </th>
                            <th style="padding:10px 20px; text-align:left;">Type</th>
                            <th style="padding:10px 20px; text-align:left;">
                                <a href="{{ $sortLink('size') }}" style="color:{{ $sortBy==='size'?'#0f766e':'#64748b' }}; text-decoration:none;">Size{{ $arrow('size') }}</a>
                            </th>
                            <th style="padding:10px 20px; text-align:left;">
                                <a href="{{ $sortLink('date') }}" style="color:{{ $sortBy==='date'?'#0f766e':'#64748b' }}; text-decoration:none;">Uploaded{{ $arrow('date') }}</a>
                            </th>
                            <th style="padding:10px 20px; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $doc)
                        @php
                            $ext = strtolower(pathinfo($doc->original_name ?? $doc->filename, PATHINFO_EXTENSION));
                            $iconColor = match($ext) {
                                'pdf'        => '#ef4444',
                                'doc','docx' => '#3b82f6',
                                'jpg','jpeg','png' => '#8b5cf6',
                                default      => '#64748b'
                            };
                        @endphp
                        <tr style="border-top:1px solid #f8fafc;" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background=''">
                            <td style="padding:12px 20px;">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="width:32px; height:32px; background:{{ $iconColor }}18; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <span style="font-size:9px; font-weight:800; color:{{ $iconColor }}; text-transform:uppercase;">{{ $ext ?: 'file' }}</span>
                                    </div>
                                    <span style="font-weight:600; color:#0f172a;">{{ $doc->original_name ?? $doc->filename }}</span>
                                </div>
                            </td>
                            <td style="padding:12px 20px; color:#64748b;">{{ $doc->mime_type ? explode('/', $doc->mime_type)[1] : '—' }}</td>
                            <td style="padding:12px 20px; color:#64748b;">{{ $doc->size ? number_format($doc->size / 1024, 1) . ' KB' : '—' }}</td>
                            <td style="padding:12px 20px; color:#94a3b8; font-size:12px;">{{ $doc->created_at->format('d M Y') }}</td>
                            <td style="padding:12px 20px;">
                                <div style="display:flex; align-items:center; justify-content:flex-end; gap:6px;">
                                    <a href="{{ route('admin.documents.download', $doc) }}"
                                       style="background:#f0fdfa; color:#0f766e; padding:5px 12px; border-radius:7px; font-size:11px; font-weight:600; text-decoration:none;">Download</a>
                                    <form method="POST" action="{{ route('admin.documents.destroy', $doc) }}" onsubmit="return confirm('Delete this file?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" style="background:#fff1f2; color:#e11d48; padding:5px 12px; border-radius:7px; font-size:11px; font-weight:600; border:none; cursor:pointer;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div style="padding:40px 20px; text-align:center; color:#94a3b8; font-size:13px;">
                <svg width="36" height="36" fill="none" stroke="#e2e8f0" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 10px; display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                No files yet. Upload one using the form on the right.
            </div>
            @endif
        </div>
    </div>

    {{-- Right sidebar --}}
    <div style="display:flex; flex-direction:column; gap:16px;">

        {{-- Upload file --}}
        <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); padding:18px;">
            <div style="font-size:13px; font-weight:700; color:#0f172a; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">Upload File</div>
            <form method="POST" action="{{ route('admin.documents.store', $folder) }}" enctype="multipart/form-data">
                @csrf
                <div style="border:2px dashed #e2e8f0; border-radius:10px; padding:20px; text-align:center; margin-bottom:12px; cursor:pointer;" onclick="document.getElementById('doc-upload').click()">
                    <svg width="24" height="24" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 6px; display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <div id="upload-label" style="font-size:12px; color:#94a3b8;">Click to choose file</div>
                </div>
                <input type="file" id="doc-upload" name="file" required style="display:none;"
                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                       onchange="document.getElementById('upload-label').textContent = this.files[0]?.name || 'Click to choose file'">
                <div style="font-size:11px; color:#94a3b8; margin-bottom:12px;">PDF, DOC, DOCX, JPG, PNG — max 10MB</div>
                @error('file')<p style="color:#e11d48; font-size:11px; margin-bottom:8px;">{{ $message }}</p>@enderror
                <button type="submit" style="width:100%; background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; border:none; padding:10px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer;">
                    Upload File
                </button>
            </form>
        </div>

        {{-- Create subfolder --}}
        <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); padding:18px;">
            <div style="font-size:13px; font-weight:700; color:#0f172a; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">Create Subfolder</div>
            <form method="POST" action="{{ route('admin.folders.store') }}">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $folder->id }}">
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Subfolder name"
                       style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; outline:none; margin-bottom:10px; box-sizing:border-box;"
                       onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
                @error('name')<p style="color:#e11d48; font-size:11px; margin-bottom:8px;">{{ $message }}</p>@enderror
                <button type="submit" style="width:100%; background:#f0fdfa; color:#0f766e; border:1px solid #99f6e4; padding:9px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer;">
                    Create Subfolder
                </button>
            </form>
        </div>

        {{-- Folder info --}}
        <div style="background:#f8fafc; border-radius:14px; border:1px solid #f1f5f9; padding:16px; font-size:12px; color:#64748b;">
            <div style="font-weight:700; color:#334155; margin-bottom:8px;">Folder Info</div>
            <div style="display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px solid #e2e8f0;">
                <span>Files</span><span style="font-weight:600; color:#0f172a;">{{ $documents->count() }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px solid #e2e8f0;">
                <span>Subfolders</span><span style="font-weight:600; color:#0f172a;">{{ $subfolders->count() }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:5px 0;">
                <span>Created</span><span style="font-weight:600; color:#0f172a;">{{ $folder->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>
</div>

@endsection
