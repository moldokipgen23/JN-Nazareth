@extends('layouts.admin')
@section('page-title', 'Programmes Gallery')

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
    <div>
        <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin:0;">Programmes Gallery</h2>
        <p style="font-size:12px; color:#64748b; margin:3px 0 0;">Photos from your community programmes, outreach, and activities</p>
    </div>
    <span style="font-size:12px; color:#64748b; background:#f1f5f9; padding:5px 12px; border-radius:20px;">{{ $images->count() }} photo(s)</span>
</div>

@if(session('success'))
<div style="background:#f0fdf4; border:1px solid #86efac; border-radius:10px; padding:11px 16px; margin-bottom:20px; font-size:13px; color:#166534; display:flex; align-items:center; gap:8px;">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

<div style="background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f1f5f9; padding:24px 28px; margin-bottom:28px;">
    <div style="font-size:13px; font-weight:700; color:#0f172a; margin-bottom:16px; border-bottom:1px solid #f1f5f9; padding-bottom:10px;">Upload New Photos</div>
    <form method="POST" action="{{ route('admin.programs.store') }}" enctype="multipart/form-data">
        @csrf
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
            <div>
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">Photos <span style="color:#e11d48;">*</span></label>
                <input type="file" name="images[]" multiple accept="image/*" required
                       style="border:1px solid #e2e8f0; border-radius:8px; padding:8px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box; background:#fafafa;">
            </div>
            <div>
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">Title <span style="color:#94a3b8; font-weight:400;">(optional)</span></label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Community Clean-Up Day"
                       style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; outline:none; box-sizing:border-box;" maxlength="255"
                       onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">Category</label>
                <select name="category" style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; box-sizing:border-box; background:#fff;">
                    @foreach(['general'=>'General','outreach'=>'Outreach','education'=>'Education','events'=>'Events','volunteering'=>'Volunteering','community'=>'Community Service'] as $val => $label)
                        <option value="{{ $val }}" {{ old('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">Activity Date <span style="color:#94a3b8; font-weight:400;">(optional)</span></label>
                <input type="date" name="activity_date" value="{{ old('activity_date') }}"
                       style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; box-sizing:border-box;"
                       onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>
        <div style="margin-bottom:14px;">
            <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;">Description <span style="color:#94a3b8; font-weight:400;">(optional)</span></label>
            <textarea name="description" rows="2" maxlength="1000" placeholder="Short description of this activity..."
                      style="border:1px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; width:100%; box-sizing:border-box; resize:vertical;"
                      onfocus="this.style.borderColor='#14b8a6'" onblur="this.style.borderColor='#e2e8f0'">{{ old('description') }}</textarea>
        </div>
        <button type="submit"
                style="display:inline-flex; align-items:center; gap:7px; background:linear-gradient(135deg,#0f766e,#14b8a6); color:#fff; border:none; padding:10px 22px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
            Upload Photos
        </button>
    </form>
</div>

@if($images->isEmpty())
<div style="text-align:center; padding:60px 24px; background:#fff; border-radius:14px; border:1px dashed #e2e8f0;">
    <svg width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    <p style="color:#94a3b8; font-size:14px;">No photos yet. Upload the first one above.</p>
</div>
@else
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:14px;">
    @foreach($images as $img)
    <div style="background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 1px 6px rgba(0,0,0,.08); border:1px solid #f1f5f9; position:relative;">
        <div style="position:relative; padding-top:75%; background:#f8fafc; overflow:hidden;">
            <img src="{{ \App\Helpers\Settings::storageUrl($img->path) }}"
                 alt="{{ $img->caption ?? 'Programme Photo' }}"
                 style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;">
            @if(!$img->active)
            <div style="position:absolute; inset:0; background:rgba(0,0,0,.45); display:flex; align-items:center; justify-content:center;">
                <span style="background:#ef4444; color:#fff; font-size:10px; font-weight:700; padding:3px 9px; border-radius:20px; text-transform:uppercase;">Hidden</span>
            </div>
            @endif
        </div>
        @if($img->title || $img->category)
        <div style="padding:7px 10px; border-top:1px solid #f1f5f9;">
            @if($img->title)<div style="font-size:11px; font-weight:600; color:#334155;">{{ $img->title }}</div>@endif
            @if($img->category && $img->category !== 'general')
                <span style="font-size:9px; background:#f0fdfa; color:#0f766e; border:1px solid #99f6e4; padding:1px 7px; border-radius:20px; font-weight:600; text-transform:uppercase; letter-spacing:.05em;">{{ str_replace('_',' ',$img->category) }}</span>
            @endif
            @if($img->activity_date)<div style="font-size:10px; color:#94a3b8; margin-top:2px;">{{ $img->activity_date->format('d M Y') }}</div>@endif
        </div>
        @endif
        <div style="display:flex; gap:0; border-top:1px solid #f1f5f9;">
            <form method="POST" action="{{ route('admin.programs.toggle', $img) }}" style="flex:1;">
                @csrf
                <button type="submit"
                        style="width:100%; padding:7px; font-size:11px; font-weight:600; background:transparent; border:none; cursor:pointer; color:{{ $img->active ? '#0f766e' : '#64748b' }};">
                    {{ $img->active ? '👁 Visible' : '🙈 Hidden' }}
                </button>
            </form>
            <form method="POST" action="{{ route('admin.programs.destroy', $img) }}"
                  onsubmit="return confirm('Delete this photo?')"
                  style="border-left:1px solid #f1f5f9;">
                @csrf @method('DELETE')
                <button type="submit" style="padding:7px 12px; background:transparent; border:none; cursor:pointer; color:#ef4444; font-size:13px;" title="Delete">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection
