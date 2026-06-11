<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Second-pass backfill: the prior migration (2026_06_11_000002) only matched
 * marks where entered_by belonged to an admin user. On the live DB the vast
 * majority of orphan rows have entered_by = NULL (legacy admin save path
 * never populated it), so they were skipped and remain invisible.
 *
 * This pass catches any remaining orphan row that has a final score but no
 * lifecycle state at all, and stamps it as SUBMITTED (pending approval).
 * Admin must explicitly approve via /admin/marks for those marks to flow
 * into summary/results.
 *
 * Safety conditions:
 *   - total_marks IS NOT NULL  (has an actual score)
 *   - submitted_at IS NULL     (not already pending)
 *   - approved_at IS NULL      (not already approved)
 *   - rejected_at IS NULL      (not already sent back)
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
            ->update([
                'submitted_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Irreversible — once admin starts approving these we can't tell
        // which were backfilled versus normally submitted.
    }
};
