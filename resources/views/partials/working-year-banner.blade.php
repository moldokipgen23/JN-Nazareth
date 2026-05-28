@php
    $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
    $isPastYear = $workingYear && $activeYear && $workingYear->id !== $activeYear->id;
@endphp

@if($isPastYear)
<div style="display:flex;align-items:center;gap:10px;background:#fef3c7;border:1px solid #fcd34d;color:#92400e;border-radius:10px;padding:11px 14px;font-size:13px;margin-bottom:12px;">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
    </svg>
    <span>⚠ You are viewing <strong>{{ $workingYear->name }}</strong> (past year). Data is read-only. Switch to the active year to make changes.</span>
</div>
@endif
