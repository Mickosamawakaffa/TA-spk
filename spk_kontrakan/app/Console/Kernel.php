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
        // Auto-verify bookings setiap hari jam 12 malam (00:00)
        // Ini akan check booking dengan payment_paid dan check-in date sudah tiba
        // Kemudian auto-update status menjadi "checked_in"
        $schedule->command('bookings:auto-verify')
                 ->dailyAt('00:00')  // Jam 12 malam setiap hari
                 ->description('Auto-verify bookings yang sudah payment_paid dan check-in date tiba');

        // Log setiap kali scheduler jalan
        $schedule->command('bookings:auto-verify')
                 ->dailyAt('00:00')
                 ->onSuccess(function () {
                     \Log::info('✅ Auto-verify bookings scheduler selesai dijalankan');
                 })
                 ->onFailure(function () {
                     \Log::error('❌ Auto-verify bookings scheduler gagal');
                 });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
