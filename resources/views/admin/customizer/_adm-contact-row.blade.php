{{-- One Admission WhatsApp-contact row. Vars: $idx, $name, $number, $active --}}
<div class="adm-row">
  <i class="fas fa-grip-vertical adm-row-grip" title="Drag to reorder"></i>
  <div style="flex:1; min-width:0;">
    <div class="g2">
      <div>
        <label class="flabel">Contact Name</label>
        <input class="finput" name="contacts[{{ $idx }}][name]" value="{{ $name }}" placeholder="e.g. Admission Help Desk">
      </div>
      <div>
        <label class="flabel">WhatsApp Number (digits only)</label>
        <input class="finput" name="contacts[{{ $idx }}][number]" value="{{ $number }}" placeholder="919862880292">
      </div>
    </div>
  </div>
  <label class="switch" title="Show on website" style="margin-top:22px;">
    <input type="hidden" name="contacts[{{ $idx }}][active]" value="0">
    <input type="checkbox" name="contacts[{{ $idx }}][active]" value="1" @checked($active)>
    <span class="slider"></span>
  </label>
  <button type="button" class="adm-del" onclick="admDelRow(this)" title="Delete contact" style="margin-top:18px;">&times;</button>
</div>
