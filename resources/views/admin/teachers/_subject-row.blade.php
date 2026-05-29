@php $inp = 'border:1px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:13px;width:100%;box-sizing:border-box;outline:none;'; @endphp
<div class="subject-row" style="display:grid;grid-template-columns:1.2fr 0.8fr 1.5fr 40px;gap:8px;align-items:center;">
    <select name="subject_assignments[{{ $i }}][class]" required style="{{ $inp }}">
        <option value="">— Class —</option>
        @foreach($classes as $c)
            <option value="{{ $c }}" {{ ($row['class'] ?? '') === $c ? 'selected' : '' }}>{{ $c }}</option>
        @endforeach
    </select>
    <select name="subject_assignments[{{ $i }}][section]" required style="{{ $inp }}">
        @foreach($sectionNames as $s)
            <option value="{{ $s }}" {{ ($row['section'] ?? 'A') === $s ? 'selected' : '' }}>{{ $s }}</option>
        @endforeach
    </select>
    <select name="subject_assignments[{{ $i }}][subject]" required style="{{ $inp }}">
        <option value="">— Subject —</option>
        @foreach($subjectsList as $subj)
            <option value="{{ $subj->name }}" {{ ($row['subject'] ?? '') === $subj->name ? 'selected' : '' }}>{{ $subj->name }}</option>
        @endforeach
    </select>
    <button type="button" onclick="this.closest('.subject-row').remove()" style="background:#fee2e2;color:#dc2626;border:none;border-radius:8px;height:38px;cursor:pointer;font-size:18px;font-weight:700;" title="Remove">×</button>
</div>
