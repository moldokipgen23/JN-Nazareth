{{-- One academic-calendar image row. Vars: $idx, $caption, $file --}}
<div class="adm-row">
  <i class="fas fa-grip-vertical adm-row-grip" title="Drag to reorder"></i>
  <div style="flex:1; min-width:0;">
    <div style="margin-bottom:8px;">
      <label class="flabel">Caption (optional)</label>
      <input class="finput" name="calendar[{{ $idx }}][caption]" value="{{ $caption }}" placeholder="e.g. April – June 2026">
    </div>
    <input type="hidden" name="calendar[{{ $idx }}][file_existing]" value="{{ $file }}">
    @if($file)
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
        <img src="{{ \App\Helpers\Settings::storageUrl($file) }}" style="height:60px;width:84px;border-radius:8px;border:1px solid #e2e8f0;object-fit:cover;">
        <label style="font-size:11.5px;color:#dc2626;display:flex;align-items:center;gap:5px;">
          <input type="checkbox" name="calendar[{{ $idx }}][file_remove]" value="1"> Remove
        </label>
      </div>
    @endif
    <label class="flabel">{{ $file ? 'Replace image' : 'Calendar image (JPG or PNG)' }}</label>
    <input type="file" name="calendar[{{ $idx }}][file]" accept="image/*" class="finput" style="padding:6px;">
  </div>
  <button type="button" class="adm-del" onclick="admDelRow(this)" title="Delete image" style="margin-top:18px;">&times;</button>
</div>
