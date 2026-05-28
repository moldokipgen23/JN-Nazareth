<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPassword extends Command
{
    protected $signature = 'user:reset-password {email} {password?}';

    protected $description = 'Reset a user\'s password. Use this if SMTP is not configured.';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("No user found with email: {$email}");
            return 1;
        }

        $password = $this->argument('password') ?? $this->secret('Enter new password');
        $user->password = Hash::make($password);
        $user->save();

        $this->info("Password reset successfully for {$user->name} ({$email}).");
        return 0;
    }
}
