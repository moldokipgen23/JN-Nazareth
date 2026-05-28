@php
    $rule = $academicYear->relationLoaded('promotionRule') ? $academicYear->promotionRule : $academicYear->promotionRule;
    $exams = \App\Models\Exam::where('academic_year_id', $academicYear->id)->orderBy('sort_order')->get();
@endphp

<div style="background:#fff;border-radius:12px;padding:16px 20px;margin-bottom:16px;box-shadow:0 1px 3px rgba(15,23,42,.06);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <div style="font-size:14px;font-weight:700;color:#0f172a;">Promotion Rules</div>
        <span style="font-size:11px;color:#64748b;">Configure pass/fail criteria</span>
    </div>

    <form method="POST" action="{{ route('admin.promotion-rules.update', $academicYear) }}" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
        @csrf @method('PUT')

        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:3px;">Eval Exam</label>
            <select name="exam_id" style="border:1px solid #e2e8f0;border-radius:7px;padding:7px 10px;font-size:12px;min-width:150px;">
                <option value="">— Select exam —</option>
                @foreach($exams as $exam)
                    <option value="{{ $exam->id }}" {{ $rule?->exam_id === $exam->id ? 'selected' : '' }}>{{ $exam->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:3px;">Min Pass %</label>
            <input type="number" name="min_pass_percentage" value="{{ $rule->min_pass_percentage ?? 40 }}" min="0" max="100" step="0.01"
                   style="border:1px solid #e2e8f0;border-radius:7px;padding:7px 10px;font-size:12px;width:80px;">
        </div>

        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:3px;">Min Attendance %</label>
            <input type="number" name="min_attendance_percentage" value="{{ $rule->min_attendance_percentage ?? 75 }}" min="0" max="100" step="0.01"
                   style="border:1px solid #e2e8f0;border-radius:7px;padding:7px 10px;font-size:12px;width:80px;">
        </div>

        <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:3px;">Allow Fails</label>
            <input type="number" name="fail_subjects_allowed" value="{{ $rule->fail_subjects_allowed ?? 0 }}" min="0" max="20"
                   style="border:1px solid #e2e8f0;border-radius:7px;padding:7px 10px;font-size:12px;width:60px;">
        </div>

        <div style="display:flex;align-items:center;gap:6px;padding-bottom:4px;">
            <input type="checkbox" name="use_cgpa" value="1" id="use_cgpa" {{ $rule?->use_cgpa ? 'checked' : '' }}
                   style="width:16px;height:16px;accent-color:#0f766e;">
            <label for="use_cgpa" style="font-size:12px;font-weight:600;color:#475569;">Use CGPA</label>
        </div>

        <div id="min-cgpa-field" style="{{ $rule?->use_cgpa ? '' : 'display:none;' }}">
            <label style="display:block;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:3px;">Min CGPA</label>
            <input type="number" name="min_cgpa" value="{{ $rule->min_cgpa ?? 2.0 }}" min="0" max="10" step="0.01"
                   style="border:1px solid #e2e8f0;border-radius:7px;padding:7px 10px;font-size:12px;width:70px;">
        </div>

        <button type="submit" style="background:#0f766e;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">
            Save Rules
        </button>
    </form>
</div>

<script>
document.getElementById('use_cgpa')?.addEventListener('change', function() {
    document.getElementById('min-cgpa-field').style.display = this.checked ? '' : 'none';
});
</script>
