<?php

use App\Models\DivisionRule;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $rules = [
            ['name' => 'Excellent',    'min_percent' => 95.00, 'max_percent' => 100.00, 'is_active' => true],
            ['name' => 'Distinction',  'min_percent' => 80.00, 'max_percent' => 94.99,  'is_active' => true],
            ['name' => 'I Division',   'min_percent' => 60.00, 'max_percent' => 79.99,  'is_active' => true],
            ['name' => 'II Division',  'min_percent' => 50.00, 'max_percent' => 59.99,  'is_active' => true],
            ['name' => 'III Division', 'min_percent' => 33.00, 'max_percent' => 49.99,  'is_active' => true],
        ];

        foreach ($rules as $row) {
            DivisionRule::create($row);
        }
    }

    public function down(): void
    {
        DivisionRule::whereIn('name', ['Excellent', 'Distinction', 'I Division', 'II Division', 'III Division'])->delete();
    }
};
