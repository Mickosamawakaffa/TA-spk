<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSetPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:set-password {email} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set password for an admin account (by email)';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        if (! $password) {
            $password = $this->secret('New password');
            $confirm = $this->secret('Confirm password');
            if ($password !== $confirm) {
                $this->error('Passwords do not match.');
                return 1;
            }
        }

        $admin = Admin::where('email', $email)->first();
        if (! $admin) {
            $this->error('Admin not found: '.$email);
            return 1;
        }

        $admin->password = Hash::make($password);
        $admin->save();

        $this->info('Password updated for '.$email);
        return 0;
    }
}
