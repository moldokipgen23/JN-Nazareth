<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\ClassSubject;
use App\Models\Mark;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\SubjectTeacherAssignment;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * QA Test data: 1 fake teacher + 3 fake students in Class X.
 * All records prefixed "QA_TEST_" so cleanup is safe and surgical.
 *
 *   php artisan qa:test-data --create
 *   php artisan qa:test-data --cleanup
 */
class QATestData extends Command
{
    protected $signature = 'qa:test-data {--create} {--cleanup}';
    protected $description = 'Create or clean up QA test data (1 teacher, 3 students, Class X)';

    private const PREFIX = 'QA_TEST_';
    private const TEST_CLASS = 'Class X';
    private const TEST_SECTION = 'A';
    private const TEACHER_EMAIL = 'qa.test.teacher@example.com';
    private const TEACHER_PASSWORD = 'qatest1234';

    public function handle(): int
    {
        if ($this->option('cleanup')) {
            return $this->cleanup();
        }
        if ($this->option('create')) {
            return $this->create();
        }

        $this->error('Pass --create or --cleanup.');
        return self::FAILURE;
    }

    private function create(): int
    {
        $year = AcademicYear::current();
        if (!$year) {
            $this->error('No active academic year. Set one in Admin → Academic Years.');
            return self::FAILURE;
        }

        $this->info('Creating QA test data...');

        // 1. Ensure Class X has 2 subjects in class_subjects (English, Mathematics)
        $subjects = collect(['English', 'Mathematics'])->map(function ($name) {
            return Subject::firstOrCreate(['name' => $name], ['is_active' => true]);
        });

        foreach ($subjects as $subject) {
            ClassSubject::firstOrCreate(
                [
                    'class' => self::TEST_CLASS,
                    'subject_id' => $subject->id,
                    'academic_year_id' => $year->id,
                    'section' => null,
                ],
                [
                    'full_marks' => 100,
                    'pass_marks' => 33,
                    'is_optional' => false,
                    'grade_only' => false,
                ]
            );
        }
        $this->info("  ✓ Class X linked to: ".$subjects->pluck('name')->join(', '));

        // 2. Create teacher
        $teacher = Teacher::firstOrCreate(
            ['email' => self::TEACHER_EMAIL],
            [
                'name' => self::PREFIX.'Test Teacher',
                'phone' => '9999999999',
                'designation' => 'Test Teacher',
                'is_active' => true,
            ]
        );

        // 3. Create user for teacher login
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $user = User::firstOrCreate(
            ['email' => self::TEACHER_EMAIL],
            [
                'name' => self::PREFIX.'Test Teacher',
                'password' => Hash::make(self::TEACHER_PASSWORD),
                'teacher_id' => $teacher->id,
            ]
        );
        if ($user->teacher_id !== $teacher->id) {
            $user->teacher_id = $teacher->id;
            $user->save();
        }
        if (!$user->hasRole('teacher')) {
            $user->assignRole($teacherRole);
        }
        $this->info("  ✓ Teacher: ".$teacher->name);

        // 4. Assign teacher to both subjects for Class X
        foreach ($subjects as $subject) {
            SubjectTeacherAssignment::firstOrCreate(
                [
                    'teacher_id' => $teacher->id,
                    'academic_year_id' => $year->id,
                    'class' => self::TEST_CLASS,
                    'section' => self::TEST_SECTION,
                    'subject' => $subject->name,
                ]
            );
        }
        $this->info("  ✓ Teacher assigned to Class X English + Mathematics");

        // 5. Create 3 students with enrollments
        $names = ['Alice Test', 'Bob Test', 'Charlie Test'];
        foreach ($names as $i => $name) {
            $student = Student::firstOrCreate(
                ['email' => 'qa.student'.($i + 1).'@example.com'],
                [
                    'name' => self::PREFIX.$name,
                    'gender' => $i % 2 === 0 ? 'female' : 'male',
                    'date_of_birth' => now()->subYears(15)->subDays($i * 30),
                    'phone' => '888888'.str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                    'class' => self::TEST_CLASS,
                ]
            );

            StudentEnrollment::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year_id' => $year->id,
                ],
                [
                    'class' => self::TEST_CLASS,
                    'section' => self::TEST_SECTION,
                    'roll_number' => 9000 + $i + 1,
                    'status' => 'active',
                ]
            );
        }
        $this->info('  ✓ 3 students created in Class X — Section A (roll 9001, 9002, 9003)');

        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('  TEST TEACHER LOGIN');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('  URL:      /teacher/login');
        $this->line('  Email:    '.self::TEACHER_EMAIL);
        $this->line('  Password: '.self::TEACHER_PASSWORD);
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();
        $this->comment('To remove all test data: php artisan qa:test-data --cleanup');

        return self::SUCCESS;
    }

    private function cleanup(): int
    {
        $this->info('Cleaning up QA test data...');

        // Find the teacher by email
        $teacher = Teacher::where('email', self::TEACHER_EMAIL)->first();
        $user = User::where('email', self::TEACHER_EMAIL)->first();
        $students = Student::where('name', 'like', self::PREFIX.'%')->get();

        // 1. Delete marks + attendance for QA students by enrollment id
        foreach ($students as $s) {
            $enrollmentIds = $s->enrollments()->pluck('id');
            if ($enrollmentIds->isNotEmpty()) {
                Mark::whereIn('student_enrollment_id', $enrollmentIds)->delete();
                AttendanceRecord::whereIn('student_enrollment_id', $enrollmentIds)->delete();
            }
            $s->enrollments()->delete();
        }
        $this->info("  ✓ Removed marks/attendance/enrollments for ".$students->count().' test students');

        // 2. Delete students
        Student::where('name', 'like', self::PREFIX.'%')->delete();
        $this->info('  ✓ Deleted test students');

        // 3. Delete teacher assignments + teacher
        if ($teacher) {
            $teacher->subjectTeacherAssignments()->delete();
            $teacher->classTeacherAssignments()->delete();
            $teacher->delete();
            $this->info('  ✓ Deleted test teacher + assignments');
        }

        // 4. Delete user
        if ($user) {
            $user->delete();
            $this->info('  ✓ Deleted test teacher login');
        }

        $this->newLine();
        $this->info('Cleanup complete. Class X class_subjects entries kept (they were not test-specific).');
        $this->comment('If you also want to remove the Class X English/Math class_subject mappings, do it from Admin → Subjects → Class Subjects.');

        return self::SUCCESS;
    }
}
