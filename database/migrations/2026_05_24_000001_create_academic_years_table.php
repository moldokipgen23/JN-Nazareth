<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();
        });

        $configuredYear = DB::table('site_settings')->where('key', 'academic_year')->value('value');
        $memberYear = DB::table('members')
            ->whereNotNull('academic_year')
            ->where('academic_year', '!=', '')
            ->select('academic_year', DB::raw('count(*) as total'))
            ->groupBy('academic_year')
            ->orderByDesc('total')
            ->value('academic_year');

        $name = trim((string) ($memberYear ?: $configuredYear ?: '2025-26'));

        DB::table('academic_years')->insert([
            'name' => $name,
            'starts_on' => null,
            'ends_on' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
