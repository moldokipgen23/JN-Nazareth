{{-- One certificate row. Vars: $idx, $title, $file, $active --}}
@php
  $cExt = strtolower(pathinfo((string) $file, PATHINFO_EXTENSION));
@endphp
<div class="adm-row">
  <i class="fas fa-grip-vertical adm-row-grip" title="Drag to reorder"></i>
  <div style="flex:1; min-width:0;">
    <div style="margin-bottom:8px;">
      <label class="flabel">Title</label>
      <input class="finput" name="certs[{{ $idx }}][title]" value="{{ $title }}" placeholder="e.g. Fire Safety Certificate">
    </div>
    <input type="hidden" name="certs[{{ $idx }}][file_existing]" value="{{ $file }}">
    @if($file)
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
        @if(in_array($cExt, ['jpg','jpeg','png','webp','gif']))
          <img src="{{ \App\Helpers\Settings::storageUrl($file) }}" style="height:54px;width:54px;border-radius:8px;border:1px solid #e2e8f0;object-fit:cover;">
        @else
          <span style="display:inline-flex;align-items:center;gap:6px;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:8px;padding:6px 10px;font-size:11.5px;font-weight:700;"><i class="fas fa-file-pdf"></i> PDF</span>
        @endif
        <a href="{{ \App\Helpers\Settings::storageUrl($file) }}" target="_blank" style="font-size:12px;color:#0f766e;font-weight:600;">View current file ↗</a>
        <label style="font-size:11.5px;color:#dc2626;display:flex;align-items:center;gap:5px;">
          <input type="checkbox" name="certs[{{ $idx }}][file_remove]" value="1"> Remove
        </label>
      </div>
    @endif
    <label class="flabel">{{ $file ? 'Replace file' : 'Upload file' }} (PDF, JPG or PNG)</label>
    <input type="file" name="certs[{{ $idx }}][file]" accept=".pdf,image/*" class="finput" style="padding:6px;">
    <label style="font-size:12px;color:#374151;display:flex;align-items:center;gap:7px;margin-top:10px;">
      <input type="hidden" name="certs[{{ $idx }}][active]" value="0">
      <input type="checkbox" name="certs[{{ $idx }}][active]" value="1" {{ ($active ?? true) ? 'checked' : '' }}>
      Show this certificate on the website
    </label>
  </div>
  <button type="button" class="adm-del" onclick="admDelRow(this)" title="Delete certificate" style="margin-top:18px;">&times;</button>
</div>
