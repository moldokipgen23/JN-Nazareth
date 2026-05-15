<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bongbal_firsts') && !Schema::hasTable('hall_of_fames')) {
            Schema::rename('bongbal_firsts', 'hall_of_fames');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hall_of_fames') && !Schema::hasTable('bongbal_firsts')) {
            Schema::rename('hall_of_fames', 'bongbal_firsts');
        }
    }
};
