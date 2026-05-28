<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique('name');
        });

        // Seed from existing subject_teacher_assignments — deduped, preserves real data.
        $existing = DB::table('subject_teacher_assignments')
            ->select('subject')
            ->distinct()
            ->whereNotNull('subject')
            ->pluck('subject');

        $now = now();
        foreach ($existing as $i => $name) {
            DB::table('subjects')->insertOrIgnore([
                'name'       => trim($name),
                'is_active'  => true,
                'sort_order' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
