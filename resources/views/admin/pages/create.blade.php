@extends('layouts.admin')
@section('page-title', 'New Page')

@section('content')

{{-- Header --}}
<div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
    <a href="{{ route('admin.pages.index') }}"
       style="width:34px; height:34px; border-radius:8px; background:#f8fafc; border:1px solid #e2e8f0; color:#64748b; text-decoration:none; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">Create New Page</h2>
        <p style="font-size:12px; color:#64748b; margin:3px 0 0;">Build a new public page for your website.</p>
    </div>
</div>

@if($errors->any())
    <div style="background:#fff1f2; border:1px solid #fecdd3; color:#9f1239; padding:12px 16px; border-radius:10px; font-size:13px; margin-bottom:16px;">
        @foreach($errors->all() as $e)<div style="margin-bottom:2px;">• {{ $e }}</div>@endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.pages.store') }}" id="page-form">
    @csrf

    <div style="display:grid; grid-template-columns:1fr 280px; gap:20px; align-items:start;">

        {{-- Main editor --}}
        <div style="display:flex; flex-direction:column; gap:16px;">

            {{-- Title --}}
            <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); padding:20px;">
                <label style="font-size:12px; font-weight:700; color:#374151; display:block; margin-bottom:8px; text-transform:uppercase; letter-spacing:.05em;">Page Title <span style="color:#e11d48;">*</span></label>
                <input type="text" name="title" id="title-input" value="{{ old('title') }}" required maxlength="255"
                       placeholder="e.g. Our Vision, Donate, Contact Us, Privacy Policy..."
                       style="width:100%; padding:11px 14px; border-radius:10px; border:1px solid #e2e8f0; font-size:16px; font-weight:600; outline:none; box-sizing:border-box; color:#0f172a;"
                       onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'"
                       oninput="updateSlug(this.value)">
                <div style="margin-top:8px; display:flex; align-items:center; gap:8px;">
                    <span style="font-size:11px; color:#94a3b8;">Public URL:</span>
                    <span id="slug-preview" style="font-size:11px; color:#0d9488; font-family:monospace; background:#f0fdfa; padding:2px 8px; border-radius:4px; border:1px solid #99f6e4;">/page/...</span>
                </div>
            </div>

            {{-- Rich text editor --}}
            <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); overflow:hidden;">
                <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between;">
                    <label style="font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.05em; margin:0;">Page Content</label>
                    <span style="font-size:11px; color:#94a3b8;">HTML supported</span>
                </div>

                {{-- Quill toolbar --}}
                <div id="editor-toolbar" style="border-bottom:1px solid #f1f5f9; padding:8px 12px; background:#fafbfc; display:flex; flex-wrap:wrap; gap:4px;">
                    <span class="ql-formats">
                        <select class="ql-header" title="Heading">
                            <option value="1">Heading 1</option>
                            <option value="2">Heading 2</option>
                            <option value="3">Heading 3</option>
                            <option selected>Normal</option>
                        </select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-bold" title="Bold"></button>
                        <button class="ql-italic" title="Italic"></button>
                        <button class="ql-underline" title="Underline"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-list" value="ordered" title="Numbered list"></button>
                        <button class="ql-list" value="bullet" title="Bullet list"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-link" title="Insert link"></button>
                        <button class="ql-blockquote" title="Blockquote"></button>
                    </span>
                    <span class="ql-formats">
                        <select class="ql-align" title="Alignment"></select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-clean" title="Clear formatting"></button>
                    </span>
                </div>

                {{-- Editor area --}}
                <div id="quill-editor" style="min-height:320px; font-size:14px; line-height:1.7;"></div>
                <textarea name="content" id="content-hidden" style="display:none;">{{ old('content') }}</textarea>
            </div>
        </div>

        {{-- Sidebar settings --}}
        <div style="display:flex; flex-direction:column; gap:14px;">

            {{-- Publish actions --}}
            <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); padding:18px;">
                <div style="font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.05em; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid #f8fafc;">Publish</div>

                {{-- Status toggle --}}
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                    <div>
                        <div style="font-size:13px; font-weight:600; color:#0f172a;">Status</div>
                        <div id="status-label" style="font-size:11px; color:#16a34a; margin-top:2px;">Published</div>
                    </div>
                    <label style="position:relative; display:inline-flex; cursor:pointer; align-items:center; gap:0;">
                        <input type="checkbox" name="published" value="1" id="published-toggle" checked
                               style="opacity:0; width:0; height:0; position:absolute;"
                               onchange="document.getElementById('status-label').textContent = this.checked ? 'Published' : 'Draft'; document.getElementById('status-label').style.color = this.checked ? '#16a34a' : '#d97706';">
                        <div id="toggle-track" style="width:42px; height:24px; border-radius:12px; background:#16a34a; transition:.2s; position:relative; flex-shrink:0;"
                             onclick="var cb=document.getElementById('published-toggle'); cb.checked=!cb.checked; cb.dispatchEvent(new Event('change')); this.style.background=cb.checked?'#16a34a':'#e2e8f0';">
                            <div style="position:absolute; top:3px; left:3px; width:18px; height:18px; border-radius:50%; background:#fff; transition:.2s; box-shadow:0 1px 4px rgba(0,0,0,.2);"
                                 id="toggle-thumb"></div>
                        </div>
                    </label>
                </div>

                {{-- Show in nav --}}
                <div style="display:flex; align-items:center; justify-content:space-between; padding-top:12px; border-top:1px solid #f8fafc; margin-bottom:16px;">
                    <div>
                        <div style="font-size:13px; font-weight:600; color:#0f172a;">Show in Navigation</div>
                        <div style="font-size:11px; color:#94a3b8; margin-top:2px;">Add to website menu</div>
                    </div>
                    <label style="position:relative; display:inline-flex; cursor:pointer;">
                        <input type="checkbox" name="show_in_nav" value="1" id="nav-toggle"
                               style="opacity:0; width:0; height:0; position:absolute;">
                        <div id="nav-track" style="width:42px; height:24px; border-radius:12px; background:#e2e8f0; transition:.2s; position:relative;"
                             onclick="var cb=document.getElementById('nav-toggle'); cb.checked=!cb.checked; this.style.background=cb.checked?'#7c3aed':'#e2e8f0';">
                            <div style="position:absolute; top:3px; left:3px; width:18px; height:18px; border-radius:50%; background:#fff; transition:.2s; box-shadow:0 1px 4px rgba(0,0,0,.2);"></div>
                        </div>
                    </label>
                </div>

                <button type="submit" style="width:100%; background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; border:none; padding:11px; border-radius:10px; font-size:13px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:7px; box-shadow:0 4px 12px rgba(20,184,166,.3);">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    Create Page
                </button>
                <a href="{{ route('admin.pages.index') }}"
                   style="display:block; text-align:center; margin-top:8px; font-size:12px; color:#94a3b8; text-decoration:none;">
                    Cancel
                </a>
            </div>

            {{-- SEO --}}
            <div style="background:#fff; border-radius:14px; border:1px solid #f1f5f9; box-shadow:0 1px 8px rgba(0,0,0,.06); padding:18px;">
                <div style="font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.05em; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid #f8fafc;">SEO</div>
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">Meta Description</label>
                <textarea name="meta_description" rows="3" maxlength="500"
                          placeholder="Brief description shown in search results..."
                          style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #e2e8f0; font-size:12px; outline:none; box-sizing:border-box; resize:vertical; line-height:1.5;"
                          onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">{{ old('meta_description') }}</textarea>
                <div style="font-size:11px; color:#94a3b8; margin-top:4px;">Recommended: 120–160 characters</div>
            </div>

            {{-- Tips --}}
            <div style="background:#f8fafc; border-radius:12px; border:1px solid #f1f5f9; padding:14px 16px; font-size:12px; color:#64748b; line-height:1.6;">
                <div style="font-weight:700; color:#334155; margin-bottom:8px; display:flex; align-items:center; gap:5px;">
                    <svg width="13" height="13" fill="#0f766e" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    Tips
                </div>
                <ul style="margin:0; padding-left:14px; display:flex; flex-direction:column; gap:5px;">
                    <li>The URL slug is auto-generated from the title</li>
                    <li>Toggle <strong>Show in Navigation</strong> to add to the website menu</li>
                    <li>Save as <strong>Draft</strong> to hide from public until ready</li>
                    <li>Use <strong>Heading 2</strong> for section titles</li>
                </ul>
            </div>
        </div>
    </div>
</form>

{{-- Quill CSS + JS --}}
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
// Quill editor
var quill = new Quill('#quill-editor', {
    theme: 'snow',
    modules: { toolbar: '#editor-toolbar' },
    placeholder: 'Write your page content here...',
});

// Pre-fill from old() if validation failed
var existingContent = document.getElementById('content-hidden').value;
if (existingContent) quill.root.innerHTML = existingContent;

// Sync to hidden textarea on submit
document.getElementById('page-form').addEventListener('submit', function() {
    document.getElementById('content-hidden').value = quill.root.innerHTML;
});

// Slug preview
function slugify(str) {
    return str.toLowerCase().trim()
        .replace(/[^\w\s-]/g, '').replace(/[\s_-]+/g, '-').replace(/^-+|-+$/g, '');
}
function updateSlug(val) {
    var s = slugify(val);
    document.getElementById('slug-preview').textContent = s ? '/page/' + s : '/page/...';
}

// Toggle thumb position helpers
document.getElementById('published-toggle').addEventListener('change', function() {
    document.getElementById('toggle-thumb').style.left = this.checked ? '21px' : '3px';
});
document.getElementById('nav-toggle').addEventListener('change', function() {
    this.closest('label').querySelector('div div').style.left = this.checked ? '21px' : '3px';
});
</script>

@endsection
