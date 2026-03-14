<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Kontrakan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoVerifyBookings extends Command
{
    /**
     * Nama command yang dijalankan
     */
    protected $signature = 'bookings:auto-verify';

    /**
     * Deskripsi command
     */
    protected $description = 'Auto-verify bookings yang sudah payment_paid dan check-in date sudah tiba';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Memulai auto-verify bookings...');

        // Get today's date
        $today = Carbon::now()->toDateString();

        try {
            // Cari semua booking dengan:
            // 1. Status = pending atau confirmed (belum checked_in)
            // 2. Payment status = paid (sudah bayar)
            // 3. Start date = hari ini atau lebih lama
            // 4. Belum di-checked_in (checked_in_at masih NULL)
            
            $bookings = Booking::where('payment_status', 'paid')
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('start_date', '<=', $today)
                ->whereNull('checked_in_at')
                ->get();

            $verified_count = 0;

            foreach ($bookings as $booking) {
                // Update booking status ke checked_in
                $booking->update([
                    'status' => 'checked_in',
                    'checked_in_at' => now(),
                ]);

                // Update kontrakan status ke occupied
                Kontrakan::where('id', $booking->kontrakan_id)
                    ->update(['status' => 'occupied']);

                $verified_count++;

                $this->line("✅ Booking #{$booking->id} ({$booking->user->name}) auto-verified");
            }

            if ($verified_count > 0) {
                $this->info("✨ Total {$verified_count} bookings berhasil di-verify");
            } else {
                $this->info("ℹ️  Tidak ada booking yang perlu di-verify hari ini");
            }

            // Log untuk audit trail
            \Log::info("Auto-verify bookings executed", [
                'verified_count' => $verified_count,
                'executed_at' => now(),
            ]);

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error saat auto-verify: {$e->getMessage()}");
            \Log::error('Auto-verify bookings error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return self::FAILURE;
        }
    }
}
