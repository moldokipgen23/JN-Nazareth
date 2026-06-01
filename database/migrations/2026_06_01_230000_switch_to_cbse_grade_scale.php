<?php

use App\Models\GradeScale;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        GradeScale::truncate();

        $cbse = [
            ['name' => 'A1', 'min_percent' => 91.00, 'max_percent' => 100.00, 'grade_point' => 10.00],
            ['name' => 'A2', 'min_percent' => 81.00, 'max_percent' => 90.99,  'grade_point' => 9.00],
            ['name' => 'B1', 'min_percent' => 71.00, 'max_percent' => 80.99,  'grade_point' => 8.00],
            ['name' => 'B2', 'min_percent' => 61.00, 'max_percent' => 70.99,  'grade_point' => 7.00],
            ['name' => 'C1', 'min_percent' => 51.00, 'max_percent' => 60.99,  'grade_point' => 6.00],
            ['name' => 'C2', 'min_percent' => 41.00, 'max_percent' => 50.99,  'grade_point' => 5.00],
            ['name' => 'D',  'min_percent' => 33.00, 'max_percent' => 40.99,  'grade_point' => 4.00],
            ['name' => 'E',  'min_percent' => 0.00,  'max_percent' => 32.99,  'grade_point' => 0.00],
        ];

        foreach ($cbse as $row) {
            GradeScale::create($row);
        }
    }

    public function down(): void
    {
        GradeScale::truncate();

        $original = [
            ['name' => 'A+', 'min_percent' => 90.00, 'max_percent' => 100.00, 'grade_point' => 4.00],
            ['name' => 'A',  'min_percent' => 80.00, 'max_percent' => 89.99,  'grade_point' => 3.50],
            ['name' => 'B+', 'min_percent' => 70.00, 'max_percent' => 79.99,  'grade_point' => 3.00],
            ['name' => 'B',  'min_percent' => 60.00, 'max_percent' => 69.99,  'grade_point' => 2.50],
            ['name' => 'C+', 'min_percent' => 50.00, 'max_percent' => 59.99,  'grade_point' => 2.00],
            ['name' => 'C',  'min_percent' => 40.00, 'max_percent' => 49.99,  'grade_point' => 1.50],
            ['name' => 'D',  'min_percent' => 33.00, 'max_percent' => 39.99,  'grade_point' => 1.00],
            ['name' => 'F',  'min_percent' => 0.00,  'max_percent' => 32.99,  'grade_point' => 0.00],
        ];

        foreach ($original as $row) {
            GradeScale::create($row);
        }
    }
};
