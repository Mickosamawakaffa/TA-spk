<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Scheduler didaftarkan di routes/console.php (Laravel 11 style).
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        // Register commands
        $this->commands([
            \App\Console\Commands\SyncAdminsFromUsers::class,
            \App\Console\Commands\DedupeAdmins::class,
            \App\Console\Commands\AdminSetPassword::class,
            \App\Console\Commands\MigratePaymentProofsToPrivate::class,
        ]);

        require base_path('routes/console.php');
    }
}
