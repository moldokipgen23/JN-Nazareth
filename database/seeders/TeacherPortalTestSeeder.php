<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\ClassTeacherAssignment;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\NoteAssignment;
use App\Models\StudentEnrollment;
use App\Models\SubjectTeacherAssignment;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeacherPortalTestSeeder extends Seeder
{
    public function run(): void
    {
        $year = AcademicYear::current();
        if (! $year) {
            $this->command?->error('No active academic year found. Run DatabaseSeeder first.');
            return;
        }

        $this->renameGenericTeacher();
        $this->createTeacherUserAccounts();
        $this->assignClassTeachers($year);
        $this->assignSubjects($year);
        $this->seedAttendance($year);
        $this->seedMarks($year);
        $this->seedNotes($year);

        $this->command?->info('Teacher portal test data seeded successfully.');
    }

    private function renameGenericTeacher(): void
    {
        $teacher = Teacher::find(9);
        if ($teacher && $teacher->name === 'teacher') {
            $teacher->update([
                'name' => 'Mr. James Sangma',
                'designation' => 'Teacher',
                'subjects' => 'Mathematics, Science',
                'classes' => ['Class IV', 'Class V'],
                'phone' => '9862800033',
                'email' => 'james.sangma@jnnazareth.edu',
                'sort_order' => 9,
                'is_active' => true,
            ]);
        }
    }

    private function createTeacherUserAccounts(): void
    {
        $teachers = Teacher::all();
        foreach ($teachers as $t) {
            $hasUser = User::where('teacher_id', $t->id)->exists();
            if ($hasUser) {
                continue;
            }
            $slug = Str::slug($t->name, '.');
            $user = User::firstOrCreate(
                ['email' => $slug . '@jnnazareth.edu'],
                [
                    'name' => $t->name,
                    'password' => Hash::make('password'),
                    'teacher_id' => $t->id,
                ]
            );
            $user->syncRoles('teacher');
        }
    }

    private function assignClassTeachers(AcademicYear $year): void
    {
        $assignments = [
            [1, 'Class X', 'A'],
            [2, 'Class VIII', 'A'],
            [3, 'Class IX', 'A'],
            [4, 'Class VI', 'A'],
            [5, 'Class VII', 'A'],
            [6, 'Class III', 'A'],
            [9, 'Class IV', 'A'],
        ];

        foreach ($assignments as [$teacherId, $class, $section]) {
            ClassTeacherAssignment::firstOrCreate(
                [
                    'academic_year_id' => $year->id,
                    'teacher_id' => $teacherId,
                    'class' => $class,
                    'section' => $section,
                ]
            );
        }
    }

    private function assignSubjects(AcademicYear $year): void
    {
        $assignments = [
            [1, 'Class X', 'A', 'Social Studies'],
            [2, 'Class VIII', 'A', 'English'],
            [2, 'Class VII', 'A', 'Social Studies'],
            [3, 'Class IX', 'A', 'Mathematics'],
            [3, 'Class IX', 'A', 'Science'],
            [3, 'Class X', 'A', 'Mathematics'],
            [3, 'Class X', 'A', 'Science'],
            [4, 'Class VI', 'A', 'English'],
            [4, 'Class V', 'A', 'English'],
            [4, 'Class V', 'A', 'Moral Science'],
            [5, 'Class VII', 'A', 'Science'],
            [5, 'Class VIII', 'A', 'Science'],
            [5, 'Class VII', 'A', 'Computer'],
            [5, 'Class VIII', 'A', 'Computer'],
            [6, 'Class I', 'A', 'All Subjects'],
            [6, 'Class I', 'B', 'All Subjects'],
            [6, 'Class II', 'A', 'All Subjects'],
            [6, 'Class II', 'B', 'All Subjects'],
            [6, 'Class III', 'A', 'All Subjects'],
            [6, 'Class III', 'B', 'All Subjects'],
            [7, 'Preparatory', 'A', 'Foundation Learning'],
            [7, 'LKG', 'A', 'Foundation Learning'],
            [7, 'UKG', 'A', 'Foundation Learning'],
            [8, 'Class IV', 'A', 'Physical Education'],
            [8, 'Class V', 'A', 'Physical Education'],
            [8, 'Class VI', 'A', 'Physical Education'],
            [8, 'Class VII', 'A', 'Physical Education'],
            [8, 'Class VIII', 'A', 'Physical Education'],
            [8, 'Class IX', 'A', 'Physical Education'],
            [8, 'Class X', 'A', 'Physical Education'],
            [9, 'Class IV', 'A', 'Mathematics'],
            [9, 'Class IV', 'A', 'Science'],
            [9, 'Class V', 'A', 'Mathematics'],
        ];

        foreach ($assignments as [$teacherId, $class, $section, $subject]) {
            SubjectTeacherAssignment::firstOrCreate(
                [
                    'academic_year_id' => $year->id,
                    'teacher_id' => $teacherId,
                    'class' => $class,
                    'section' => $section,
                    'subject' => $subject,
                ]
            );
        }
    }

    private function seedAttendance(AcademicYear $year): void
    {
        $admin = User::where('email', 'admin@demo.com')->first() ?? User::first();

        $enrollments = StudentEnrollment::forActiveYear()->active()
            ->with('student')
            ->get();

        $statuses = AttendanceRecord::STATUSES;

        for ($day = 5; $day >= 0; $day--) {
            $date = now()->subDays($day)->toDateString();

            foreach ($enrollments as $enrollment) {
                AttendanceRecord::firstOrCreate(
                    [
                        'student_enrollment_id' => $enrollment->id,
                        'date' => $date,
                    ],
                    [
                        'academic_year_id' => $year->id,
                        'class' => $enrollment->class,
                        'section' => $enrollment->section,
                        'status' => $statuses[array_rand($statuses)],
                        'marked_by' => $admin->id,
                    ]
                );
            }
        }
    }

    private function seedMarks(AcademicYear $year): void
    {
        $exam = Exam::forActiveYear()->active()->first();
        if (! $exam) {
            $this->command?->warn('No active exam found. Skipping marks seeding.');
            return;
        }

        $admin = User::where('email', 'admin@demo.com')->first() ?? User::first();

        $subjects = SubjectTeacherAssignment::where('academic_year_id', $year->id)
            ->select('class', 'section', 'subject')
            ->groupBy('class', 'section', 'subject')
            ->get();

        foreach ($subjects as $slot) {
            $enrollments = StudentEnrollment::forActiveYear()->active()
                ->where('class', $slot->class)
                ->where('section', $slot->section)
                ->get();

            $fullMarks = 100;
            $passMarks = 33;

            foreach ($enrollments as $enrollment) {
                $theory = rand(20, 60);
                $assignment = rand(10, 25);
                $total = $theory + $assignment;
                $grade = $total >= $passMarks ? ($total >= 75 ? 'A' : ($total >= 60 ? 'B' : ($total >= 45 ? 'C' : 'D'))) : 'F';

                Mark::firstOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'student_enrollment_id' => $enrollment->id,
                        'subject' => $slot->subject,
                    ],
                    [
                        'academic_year_id' => $year->id,
                        'class' => $slot->class,
                        'section' => $slot->section,
                        'full_marks' => $fullMarks,
                        'pass_marks' => $passMarks,
                        'theory_marks' => $theory,
                        'assignment_marks' => $assignment,
                        'total_marks' => $total,
                        'obtained_marks' => $total,
                        'grade' => $grade,
                        'entered_by' => $admin->id,
                    ]
                );
            }
        }
    }

    private function seedNotes(AcademicYear $year): void
    {
        $teachers = Teacher::whereHas('users')->get();

        foreach ($teachers as $teacher) {
            $user = $teacher->users()->first();
            if (! $user) continue;

            $subjects = SubjectTeacherAssignment::where('teacher_id', $teacher->id)
                ->where('academic_year_id', $year->id)
                ->first();
            if (! $subjects) continue;

            NoteAssignment::create([
                'academic_year_id' => $year->id,
                'submitted_by' => $user->id,
                'class' => $subjects->class,
                'section' => $subjects->section,
                'subject' => $subjects->subject,
                'type' => 'note',
                'title' => 'Welcome Note — ' . $teacher->name,
                'content' => 'Dear students, welcome to the new academic year. Please bring your textbooks and notebooks from tomorrow. Let us work together to make this year a successful one.',
                'due_date' => null,
            ]);

            NoteAssignment::create([
                'academic_year_id' => $year->id,
                'submitted_by' => $user->id,
                'class' => $subjects->class,
                'section' => $subjects->section,
                'subject' => $subjects->subject,
                'type' => 'assignment',
                'title' => 'Practice Exercise — Week 1',
                'content' => 'Complete the practice questions from the textbook (chapters 1-2). Submit your answers in the next class.',
                'due_date' => now()->addDays(7),
            ]);
        }
    }
}
