<?php

namespace App\Console\Commands;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Console\Command;

class LinkTeachersToUsers extends Command
{
    protected $signature = 'teachers:link';
    protected $description = 'Link user accounts to teacher records by matching name';

    public function handle(): int
    {
        $linked = 0;
        $created = 0;

        $teachers = Teacher::all()->keyBy(fn ($t) => strtolower(trim($t->name)));

        $users = User::whereHas('roles', fn ($q) => $q->where('name', 'teacher'))
            ->whereNull('teacher_id')->get();

        foreach ($users as $user) {
            $key = strtolower(trim($user->name));

            if ($user->teacher_id) continue;

            $teacher = $teachers->get($key);

            if (! $teacher) {
                $teacher = Teacher::create([
                    'name'        => $user->name,
                    'designation' => $user->designation ?? 'Teacher',
                    'email'       => $user->email,
                    'phone'       => $user->phone,
                    'is_active'   => true,
                ]);
                $created++;
                $this->line("  Created teacher record: {$teacher->name}");
            }

            $user->teacher_id = $teacher->id;
            $user->save();
            $linked++;
            $this->line("  Linked {$user->name} → Teacher #{$teacher->id}");
        }

        $this->info("Done. {$linked} linked, {$created} teacher records created.");

        return self::SUCCESS;
    }
}
