{{-- One people-group member row. Vars: $idx, $name, $role, $photo --}}
<div class="adm-row">
  <i class="fas fa-grip-vertical adm-row-grip" title="Drag to reorder"></i>
  <div style="flex:1; min-width:0;">
    <div class="g2" style="margin-bottom:8px;">
      <div>
        <label class="flabel">Name</label>
        <input class="finput" name="members[{{ $idx }}][name]" value="{{ $name }}" placeholder="Full name">
      </div>
      <div>
        <label class="flabel">Role / Designation</label>
        <input class="finput" name="members[{{ $idx }}][role]" value="{{ $role }}" placeholder="e.g. Principal">
      </div>
    </div>
    <input type="hidden" name="members[{{ $idx }}][photo_existing]" value="{{ $photo }}">
    @if($photo)
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
        <img src="{{ \App\Helpers\Settings::storageUrl($photo) }}" style="height:48px;width:48px;border-radius:50%;border:1px solid #e2e8f0;object-fit:cover;">
        <label style="font-size:11.5px;color:#dc2626;display:flex;align-items:center;gap:5px;">
          <input type="checkbox" name="members[{{ $idx }}][photo_remove]" value="1"> Remove photo
        </label>
      </div>
    @endif
    <label class="flabel">{{ $photo ? 'Replace photo' : 'Photo (optional)' }}</label>
    <input type="file" name="members[{{ $idx }}][photo]" accept="image/*" class="finput" style="padding:6px;">
  </div>
  <button type="button" class="adm-del" onclick="admDelRow(this)" title="Delete member" style="margin-top:18px;">&times;</button>
</div>
