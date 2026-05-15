@extends('layouts.admin')
@section('page-title', 'Edit — ' . $page->title)

@section('content')

{{-- Header --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:10px;">
    <div style="display:flex; align-items:center; gap:12px;">
        <a href="{{ route('admin.pages.index') }}"
           style="width:34px; height:34px; border-radius:8px; background:#f8fafc; border:1px solid #e2e8f0; color:#64748b; text-decoration:none; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">{{ $page->title }}</h2>
            <div style="display:flex; align-items:center; gap:8px; margin-top:3px;">
                <a href="{{ in_array($page->slug, ['home','about']) ? route($page->slug) : route('page', $page->slug) }}"
                   target="_blank"
                   style="font-size:11px; color:#0d9488; text-decoration:none; font-family:monospace; background:#f0fdfa; padding:2px 8px; border-radius:4px; border:1px solid #99f6e4; display:inline-flex; align-items:center; gap:3px;">
                    /{{ in_array($page->slug, ['home','about']) ? $page->slug : 'page/' . $page->slug }}
                    <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>
    </div>
    @if(!in_array($page->slug, ['home','about']))
    <form method="POST" action="{{ route('admin.pages.destroy', $page) }}"
          onsubmit="return confirm('Permanently delete &quot;{{ addslashes($page->title) }}&quot;?')">
        @csrf @method('DELETE')
        <button type="submit" style="background:#fff1f2; color:#e11d48; border:1px solid #fecdd3; padding:7px 16px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:5px;">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Delete Page
        </button>
    </form>
    @endif
</div>

@if(session('success'))
    <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:10px; font-size:13px; font-weight:500; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
@endif
@if($errors->any())
    <div style="background:#fff1f2; border:1px solid #fecdd3; color:#9f1239; padding:12px 16px; border-radius:10px; font-size:13px; margin-bottom:16px;">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.pages.update', $page) }}" id="page-form">
    @csrf @method('PUT')

    <div style="display:grid; grid-template-columns:1fr 280px; gap:20px; align-items:start;">

        {{-- Main editor --}}
        <div style="display:flex; flex-direction:column; gap:16px;">

            {{-- Title --}}
            <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); padding:20px;">
                <label style="font-size:12px; font-weight:700; color:#374151; display:block; margin-bottom:8px; text-transform:uppercase; letter-spacing:.05em;">Page Title <span style="color:#e11d48;">*</span></label>
                <input type="text" name="title" value="{{ old('title', $page->title) }}" required maxlength="255"
                       style="width:100%; padding:11px 14px; border-radius:10px; border:1px solid #e2e8f0; font-size:16px; font-weight:600; outline:none; box-sizing:border-box; color:#0f172a;"
                       onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            {{-- Rich text editor --}}
            <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); overflow:hidden;">
                <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between;">
                    <label style="font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.05em; margin:0;">Page Content</label>
                    <span style="font-size:11px; color:#94a3b8;">HTML supported · Use headings, lists, links</span>
                </div>

                <div id="editor-toolbar" style="border-bottom:1px solid #f1f5f9; padding:8px 12px; background:#fafbfc;">
                    <span class="ql-formats">
                        <select class="ql-header" title="Heading">
                            <option value="1">Heading 1</option>
                            <option value="2">Heading 2</option>
                            <option value="3">Heading 3</option>
                            <option selected>Normal</option>
                        </select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-bold"></button>
                        <button class="ql-italic"></button>
                        <button class="ql-underline"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-list" value="ordered"></button>
                        <button class="ql-list" value="bullet"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-link"></button>
                        <button class="ql-blockquote"></button>
                        <button class="ql-image"></button>
                    </span>
                    <span class="ql-formats">
                        <select class="ql-align"></select>
                        <select class="ql-color"></select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-clean"></button>
                    </span>
                </div>

                <div id="quill-editor" style="min-height:400px; font-size:14px; line-height:1.8;"></div>
                <textarea name="content" id="content-hidden" style="display:none;">{{ old('content', $page->content) }}</textarea>
            </div>
        </div>

        {{-- Sidebar --}}
        <div style="display:flex; flex-direction:column; gap:14px;">

            {{-- Save --}}
            <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); padding:18px;">
                <div style="font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.05em; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid #f8fafc;">Settings</div>

                {{-- Published --}}
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                    <div>
                        <div style="font-size:13px; font-weight:600; color:#0f172a;">Status</div>
                        <div id="status-label" style="font-size:11px; color:{{ $page->published ? '#16a34a' : '#d97706' }}; margin-top:2px;">
                            {{ $page->published ? 'Published' : 'Draft' }}
                        </div>
                    </div>
                    <div style="width:42px; height:24px; border-radius:12px; background:{{ $page->published ? '#16a34a' : '#e2e8f0' }}; position:relative; cursor:pointer; transition:.2s;"
                         id="pub-track"
                         onclick="var cb=document.getElementById('pub-cb'); cb.checked=!cb.checked; cb.dispatchEvent(new Event('change'));">
                        <div style="position:absolute; top:3px; left:{{ $page->published ? '21px' : '3px' }}; width:18px; height:18px; border-radius:50%; background:#fff; transition:.2s; box-shadow:0 1px 4px rgba(0,0,0,.2);" id="pub-thumb"></div>
                    </div>
                    <input type="checkbox" name="published" value="1" id="pub-cb" {{ $page->published ? 'checked' : '' }} style="display:none;">
                </div>

                {{-- Show in nav --}}
                <div style="display:flex; align-items:center; justify-content:space-between; padding-top:12px; border-top:1px solid #f8fafc; margin-bottom:16px;">
                    <div>
                        <div style="font-size:13px; font-weight:600; color:#0f172a;">Show in Navigation</div>
                        <div style="font-size:11px; color:#94a3b8; margin-top:2px;">Add to website menu</div>
                    </div>
                    <div style="width:42px; height:24px; border-radius:12px; background:{{ $page->show_in_nav ? '#7c3aed' : '#e2e8f0' }}; position:relative; cursor:pointer; transition:.2s;"
                         id="nav-track"
                         onclick="var cb=document.getElementById('nav-cb'); cb.checked=!cb.checked; cb.dispatchEvent(new Event('change'));">
                        <div style="position:absolute; top:3px; left:{{ $page->show_in_nav ? '21px' : '3px' }}; width:18px; height:18px; border-radius:50%; background:#fff; transition:.2s; box-shadow:0 1px 4px rgba(0,0,0,.2);" id="nav-thumb"></div>
                    </div>
                    <input type="checkbox" name="show_in_nav" value="1" id="nav-cb" {{ $page->show_in_nav ? 'checked' : '' }} style="display:none;">
                </div>

                <button type="submit" style="width:100%; background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; border:none; padding:11px; border-radius:10px; font-size:13px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:7px; box-shadow:0 4px 12px rgba(20,184,166,.3);">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    Save Changes
                </button>
            </div>

            {{-- Page info --}}
            <div style="background:#f8fafc; border-radius:12px; border:1px solid #f1f5f9; padding:14px 16px; font-size:12px; color:#64748b;">
                <div style="font-weight:700; color:#334155; margin-bottom:8px;">Page Info</div>
                <div style="display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px solid #e2e8f0;">
                    <span>Slug</span><code style="font-size:11px; color:#0d9488;">{{ $page->slug }}</code>
                </div>
                <div style="display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px solid #e2e8f0;">
                    <span>Created</span><span style="color:#334155;">{{ $page->created_at->format('d M Y') }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:5px 0;">
                    <span>Last saved</span><span style="color:#334155;">{{ $page->updated_at->diffForHumans() }}</span>
                </div>
            </div>

            {{-- SEO --}}
            <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); padding:18px;">
                <div style="font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.05em; margin-bottom:12px; padding-bottom:10px; border-bottom:1px solid #f8fafc;">SEO</div>
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">Meta Description</label>
                <textarea name="meta_description" rows="3" maxlength="500"
                          placeholder="Brief description for search results..."
                          style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:12px; outline:none; box-sizing:border-box; resize:vertical;"
                          onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">{{ old('meta_description', $page->meta_description) }}</textarea>
            </div>
        </div>
    </div>
</form>

<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
var quill = new Quill('#quill-editor', {
    theme: 'snow',
    modules: { toolbar: '#editor-toolbar' },
    placeholder: 'Start writing your page content...',
});

// Pre-fill existing content
var existing = document.getElementById('content-hidden').value;
if (existing) quill.root.innerHTML = existing;

document.getElementById('page-form').addEventListener('submit', function() {
    document.getElementById('content-hidden').value = quill.root.innerHTML;
});

// Toggle handlers
document.getElementById('pub-cb').addEventListener('change', function() {
    document.getElementById('pub-track').style.background  = this.checked ? '#16a34a' : '#e2e8f0';
    document.getElementById('pub-thumb').style.left        = this.checked ? '21px' : '3px';
    document.getElementById('status-label').textContent    = this.checked ? 'Published' : 'Draft';
    document.getElementById('status-label').style.color    = this.checked ? '#16a34a' : '#d97706';
});
document.getElementById('nav-cb').addEventListener('change', function() {
    document.getElementById('nav-track').style.background = this.checked ? '#7c3aed' : '#e2e8f0';
    document.getElementById('nav-thumb').style.left       = this.checked ? '21px' : '3px';
});
</script>

@endsection
