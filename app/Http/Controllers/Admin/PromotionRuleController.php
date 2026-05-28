<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\PromotionRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromotionRuleController extends Controller
{
    public function update(Request $request, AcademicYear $academicYear)
    {
        $data = $request->validate([
            'exam_id' => ['nullable', 'exists:exams,id'],
            'min_pass_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'min_attendance_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'use_cgpa' => ['nullable', 'boolean'],
            'min_cgpa' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'fail_subjects_allowed' => ['nullable', 'integer', 'min:0', 'max:20'],
        ]);

        $rule = PromotionRule::updateOrCreate(
            ['academic_year_id' => $academicYear->id],
            [
                'exam_id' => $data['exam_id'],
                'min_pass_percentage' => $data['min_pass_percentage'] ?? 40.00,
                'min_attendance_percentage' => $data['min_attendance_percentage'] ?? 75.00,
                'use_cgpa' => $request->boolean('use_cgpa'),
                'min_cgpa' => $data['min_cgpa'],
                'fail_subjects_allowed' => $data['fail_subjects_allowed'] ?? 0,
            ]
        );

        return back()->with('success', 'Promotion rules saved for ' . $academicYear->name);
    }
}
