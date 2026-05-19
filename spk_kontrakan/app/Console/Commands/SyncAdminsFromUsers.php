<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class SyncAdminsFromUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:sync-users {--dry-run : Do not write changes} {--force : Force copy even if admin exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync admin and super_admin accounts from users table into admins table';

    public function handle()
    {
        $this->info('Scanning users table for admin accounts...');

        $candidates = collect();

        if (\Schema::hasColumn('users', 'role')) {
            $candidates = User::whereIn('role', ['admin', 'super_admin'])->get();
        }

        // Also include common superadmin email if present
        $maybe = User::where('email', 'superadmin@gmail.com')->get();
        if ($maybe->isNotEmpty()) $candidates = $candidates->merge($maybe)->unique('email');

        if ($candidates->isEmpty()) {
            $this->info('No admin candidates found in users table.');
            return 0;
        }

        $this->line('Found '. $candidates->count() .' candidate(s):');
        foreach ($candidates as $u) {
            $this->line(" - {$u->email} ({$u->name})");
        }

        if ($this->option('dry-run')) {
            $this->info('Dry run: no changes will be made.');
            return 0;
        }

        foreach ($candidates as $u) {
            $exists = Admin::where('email', $u->email)->exists();
            if ($exists && ! $this->option('force')) {
                $this->line("Skipping existing admin: {$u->email}");
                continue;
            }

            // Create or update admin record
            $admin = Admin::firstOrNew(['email' => $u->email]);
            $admin->name = $u->name;
            // copy hashed password as-is
            $admin->password = $u->password;
            $admin->role = property_exists($u, 'role') ? $u->role : 'admin';
            $admin->remember_token = $u->remember_token ?? null;

            $admin->save();

            $this->info("Synced admin: {$admin->email}");
        }

        $this->info('Sync complete.');
        return 0;
    }
}
