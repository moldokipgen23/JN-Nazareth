<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * One-time backfill: marks that were entered directly by admin (via the
 * admin per-row Save UI) never had submitted_at / approved_at set, so they
 * stayed invisible to the summary and pending lists. Mark them as
 * admin-approved retroactively so the existing data surfaces correctly.
 *
 * Safety conditions:
 *   - Only rows where entered_by belongs to an admin user (role 'admin')
 *   - Only rows that have a final score (total_marks IS NOT NULL)
 *   - Only rows where submitted_at IS NULL AND approved_at IS NULL
 *     (so teacher drafts, pending submissions, and existing approvals
 *      are all left untouched)
 */
return new class extends Migration
{
    public function up(): void
    {
        $adminUserIds = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', \App\Models\User::class)
            ->where('roles.name', 'admin')
            ->pluck('model_has_roles.model_id')
            ->all();

        if (empty($adminUserIds)) {
            return;
        }

        $now = now();

        DB::table('marks')
            ->whereIn('entered_by', $adminUserIds)
            ->whereNotNull('total_marks')
            ->whereNull('submitted_at')
            ->whereNull('approved_at')
            ->update([
                'submitted_at' => $now,
                'approved_at'  => $now,
                'approved_by'  => DB::raw('entered_by'),
            ]);
    }

    public function down(): void
    {
        // Irreversible by design — we cannot tell which rows were touched by
        // the backfill versus approved later through normal flow.
    }
};
