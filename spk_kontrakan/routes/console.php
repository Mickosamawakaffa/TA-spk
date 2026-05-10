<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('bookings:auto-verify')
    ->dailyAt('00:00')
    ->description('Auto-verify bookings yang sudah payment_paid dan check-in date tiba')
    ->onSuccess(function () {
        Log::info('✅ Auto-verify bookings scheduler selesai dijalankan');
    })
    ->onFailure(function () {
        Log::error('❌ Auto-verify bookings scheduler gagal');
    });

Schedule::command('database:backup')
    ->weeklyOn(0, '01:00')
    ->description('Auto backup database mingguan untuk super admin')
    ->onSuccess(function () {
        Log::info('✅ Weekly database backup scheduler selesai dijalankan');
    })
    ->onFailure(function () {
        Log::error('❌ Weekly database backup scheduler gagal');
    });

Schedule::command('kontrakan:weekly-availability-reminder')
    ->weeklyOn(1, '09:00')
    ->description('Reminder mingguan untuk konfirmasi ketersediaan kontrakan')
    ->onSuccess(function () {
        Log::info('✅ Weekly kontrakan availability reminder selesai dijalankan');
    })
    ->onFailure(function () {
        Log::error('❌ Weekly kontrakan availability reminder gagal');
    });
