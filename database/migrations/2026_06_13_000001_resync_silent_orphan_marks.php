<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Third-pass cleanup of silent orphans.
 *
 * Symptom: certain classes (e.g. Class II) had marks visible in the admin
 * per-subject review with values entered, but they didn't appear in the
 * Pending Approvals panel and the summary said 'not submitted'. Cause:
 * total_marks IS NOT NULL while BOTH submitted_at AND approved_at are NULL
 * — a state the new code can't produce but pre-deploy paths could.
 *
 * Fix: stamp every such row as SUBMITTED (pending approval). Admin must
 * explicitly click Approve through /admin/marks for those marks to flow
 * into summary/results — no auto-approve, preserves the audit trail.
 *
 * Safety:
 *   - total_marks IS NOT NULL  (has a real score)
 *   - submitted_at IS NULL     (not already in the pipeline)
 *   - approved_at  IS NULL     (not already approved)
 *   - rejected_at  IS NULL     (not in a sent-back state)
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('marks')
            ->whereNotNull('total_marks')
            ->whereNull('submitted_at')
            ->whereNull('approved_at')
            ->whereNull('rejected_at')
            ->update(['submitted_at' => now()]);
    }

    public function down(): void
    {
        // Irreversible — once admin starts approving the resurfaced rows
        // there is no way to know which were touched by this migration.
    }
};
