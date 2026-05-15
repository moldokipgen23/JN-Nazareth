@extends('layouts.admin')
@section('page-title', 'Edit Achievement')

@section('content')

<div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
    <a href="{{ route('admin.hall-of-fame.index') }}"
       style="display:inline-flex; align-items:center; gap:5px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Back
    </a>
    <span style="color:#e2e8f0;">|</span>
    <h2 style="font-size:17px; font-weight:700; color:#0f172a; margin:0;">Edit: {{ $item->name }}</h2>
</div>

@if($errors->any())
<div style="background:#fef2f2; border:1px solid #fca5a5; border-radius:10px; padding:12px 16px; margin-bottom:18px;">
    <ul style="margin:0; padding-left:18px;">
        @foreach($errors->all() as $error)
        <li style="font-size:12px; color:#b91c1c;">{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('admin.hall-of-fame.update', $item) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; padding:28px 32px; margin-bottom:20px;">

        <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid #f1f5f9;">
            Person Details
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
            <div>
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Person's Name <span style="color:#e11d48;">*</span></label>
                <input type="text" name="name" value="{{ old('name', $item->name) }}"
                       style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                       required maxlength="150">
            </div>
            <div>
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Achievement Title <span style="color:#e11d48;">*</span></label>
                <input type="text" name="achievement_title" value="{{ old('achievement_title', $item->achievement_title) }}"
                       style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                       required maxlength="200">
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
            <div>
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Year / Period</label>
                <input type="text" name="year" value="{{ old('year', $item->year) }}"
                       style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                       maxlength="10" placeholder="e.g. 1985, ~2000">
            </div>
            <div>
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">External Link</label>
                <input type="text" name="external_link" value="{{ old('external_link', $item->external_link) }}"
                       style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                       maxlength="300">
            </div>
        </div>

        <div style="margin-bottom:20px;">
            <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Story / Description</label>
            <textarea name="description" rows="5" maxlength="2000"
                      style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; resize:vertical; box-sizing:border-box;">{{ old('description', $item->description) }}</textarea>
        </div>

        <div style="margin-bottom:24px;">
            <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Video URL (YouTube)</label>
            <input type="text" name="video_url" value="{{ old('video_url', $item->video_url) }}"
                   style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;"
                   maxlength="300" placeholder="https://www.youtube.com/watch?v=...">
        </div>

        <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:12px; border-bottom:1px solid #f1f5f9;">
            Photo
        </div>
        <div style="display:flex; align-items:flex-start; gap:20px; flex-wrap:wrap; margin-bottom:24px;">
            @if($item->photo)
            <div style="flex-shrink:0; text-align:center;">
                <div style="font-size:11px; font-weight:600; color:#94a3b8; margin-bottom:6px; text-transform:uppercase; letter-spacing:.05em;">Current</div>
                <img src="{{ \App\Helpers\Settings::storageUrl($item->photo) }}" alt="{{ $item->name }}"
                     style="width:80px; height:80px; object-fit:cover; border-radius:10px; border:2px solid #e2e8f0;">
            </div>
            @endif
            <div style="flex:1; min-width:200px;">
                <div style="border:2px dashed #e2e8f0; border-radius:10px; padding:18px; text-align:center; cursor:pointer; transition:border-color .15s;"
                     onclick="document.getElementById('photo-input').click()"
                     onmouseover="this.style.borderColor='#14b8a6'" onmouseout="this.style.borderColor='#e2e8f0'">
                    <div id="photo-label" style="font-size:12px; color:#64748b; font-weight:500;">
                        {{ $item->photo ? 'Click to replace photo' : 'Click to upload photo' }} (max 3 MB)
                    </div>
                </div>
                <input type="file" name="photo" id="photo-input" accept="image/*" style="display:none;"
                       onchange="document.getElementById('photo-label').textContent=this.files[0]?.name||'Click to upload'">
            </div>
        </div>

        <div style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:16px; padding-bottom:12px; border-bottom:1px solid #f1f5f9;">
            Options
        </div>
        <div style="display:flex; gap:28px; flex-wrap:wrap; margin-bottom:8px;">
            <label style="display:inline-flex; align-items:center; gap:10px; cursor:pointer; user-select:none; font-size:13px; font-weight:500; color:#374151;">
                <input type="checkbox" name="featured" value="1" {{ old('featured', $item->featured) ? 'checked' : '' }}
                       style="width:16px; height:16px; border-radius:4px; accent-color:#0f766e;">
                ⭐ Mark as Featured
            </label>
            <label style="display:inline-flex; align-items:center; gap:10px; cursor:pointer; user-select:none; font-size:13px; font-weight:500; color:#374151;">
                <input type="checkbox" name="active" value="1" {{ old('active', $item->active) ? 'checked' : '' }}
                       style="width:16px; height:16px; border-radius:4px; accent-color:#0f766e;">
                Show on Homepage
            </label>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px;">
            <a href="{{ route('admin.hall-of-fame.index') }}"
               style="padding:10px 20px; border-radius:10px; font-size:13px; font-weight:600; border:1px solid #e2e8f0; background:#fff; color:#64748b; text-decoration:none;">
                Cancel
            </a>
            <button type="submit"
                    style="background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; font-size:13px; font-weight:600; padding:10px 24px; border-radius:10px; border:none; cursor:pointer; box-shadow:0 4px 12px rgba(20,184,166,.3); display:flex; align-items:center; gap:8px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                Save Changes
            </button>
        </div>
    </div>
</form>

@endsection
