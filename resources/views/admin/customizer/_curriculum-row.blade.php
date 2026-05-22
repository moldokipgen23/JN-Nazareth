{{-- One curriculum card row. Vars: $idx, $icon, $title, $desc --}}
<div class="adm-row">
  <i class="fas fa-grip-vertical adm-row-grip" title="Drag to reorder"></i>
  <div style="flex:1; min-width:0;">
    <div class="g2" style="margin-bottom:8px;">
      <div>
        <label class="flabel">Icon (Font Awesome class)</label>
        <input class="finput" name="curriculum[{{ $idx }}][icon]" value="{{ $icon }}" placeholder="fas fa-book">
      </div>
      <div>
        <label class="flabel">Title</label>
        <input class="finput" name="curriculum[{{ $idx }}][title]" value="{{ $title }}" placeholder="e.g. English Language">
      </div>
    </div>
    <label class="flabel">Description</label>
    <textarea class="finput" name="curriculum[{{ $idx }}][desc]" rows="2" placeholder="Short description of this subject / curriculum area">{{ $desc }}</textarea>
  </div>
  <button type="button" class="adm-del" onclick="admDelRow(this)" title="Delete item" style="margin-top:18px;">&times;</button>
</div>
