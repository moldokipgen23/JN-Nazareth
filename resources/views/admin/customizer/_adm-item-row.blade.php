{{-- One Admission information-item row. Vars: $idx, $icon, $title, $desc, $active --}}
<div class="adm-row">
  <i class="fas fa-grip-vertical adm-row-grip" title="Drag to reorder"></i>
  <div style="flex:1; min-width:0;">
    <div class="g2" style="margin-bottom:8px;">
      <div>
        <label class="flabel">Icon (Font Awesome class)</label>
        <input class="finput" name="items[{{ $idx }}][icon]" value="{{ $icon }}" placeholder="fas fa-user-check">
      </div>
      <div>
        <label class="flabel">Title</label>
        <input class="finput" name="items[{{ $idx }}][title]" value="{{ $title }}" placeholder="e.g. Eligibility">
      </div>
    </div>
    <label class="flabel">Description</label>
    <textarea class="fta" name="items[{{ $idx }}][desc]" rows="2" placeholder="Description shown on the admission page">{{ $desc }}</textarea>
  </div>
  <label class="switch" title="Show on website" style="margin-top:22px;">
    <input type="hidden" name="items[{{ $idx }}][active]" value="0">
    <input type="checkbox" name="items[{{ $idx }}][active]" value="1" @checked($active)>
    <span class="slider"></span>
  </label>
  <button type="button" class="adm-del" onclick="admDelRow(this)" title="Delete item" style="margin-top:18px;">&times;</button>
</div>
