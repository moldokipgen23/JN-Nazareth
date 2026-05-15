{{-- Variables: $name, $label, $value (default '#000000') --}}
@php $value = $value ?? '#000000'; @endphp
<div style="margin-bottom:16px;">
    <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">{{ $label }}</label>
    <div style="display:flex; align-items:center; gap:10px;">
        <input type="color" name="{{ $name }}" value="{{ $value }}"
               style="width:44px; height:36px; border:1px solid #e2e8f0; border-radius:8px; cursor:pointer; padding:2px;"
               oninput="document.getElementById('{{ $name }}_hex').value=this.value">
        <input type="text" id="{{ $name }}_hex" value="{{ $value }}" maxlength="7"
               style="flex:1; border:1px solid #e2e8f0; border-radius:8px; padding:8px 12px; font-size:13px; font-family:monospace;"
               oninput="document.querySelector('[name={{ $name }}]').value=this.value">
    </div>
</div>
