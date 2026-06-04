<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('marks')
            ->whereNotNull('submitted_at')
            ->whereNull('approved_at')
            ->update([
                'approved_at' => DB::raw('submitted_at'),
                'approved_by' => null,
            ]);
    }

    public function down(): void
    {
        // No rollback — data is already correct.
    }
};
