<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kontrakan;
use App\Models\Booking;

class SyncKontrakanStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kontrakan:sync-status 
                            {--all : Sync semua kontrakan, bukan hanya yang perlu update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi status kontrakan berdasarkan booking aktif';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai sinkronisasi status kontrakan...');
        
        $today = now()->toDateString();
        $updated = 0;
        $checked = 0;

        // 1. Reset kontrakan yang occupied_until sudah lewat
        $expiredCount = Kontrakan::where('status', 'occupied')
            ->whereNotNull('occupied_until')
            ->where('occupied_until', '<', $today)
            ->update([
                'status' => 'available',
                'occupied_until' => null,
            ]);

        if ($expiredCount > 0) {
            $this->info("- Reset {$expiredCount} kontrakan yang masa sewanya sudah berakhir.");
            $updated += $expiredCount;
        }

        // 2. Cek semua kontrakan dengan booking aktif
        if ($this->option('all')) {
            $kontrakans = Kontrakan::all();
        } else {
            // Hanya kontrakan yang punya booking aktif atau statusnya perlu dicek
            $kontrakanIds = Booking::select('kontrakan_id')
                ->distinct()
                ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_CHECKED_IN])
                ->pluck('kontrakan_id');
            
            $kontrakans = Kontrakan::whereIn('id', $kontrakanIds)
                ->orWhereIn('status', ['booked', 'occupied'])
                ->get();
        }

        foreach ($kontrakans as $kontrakan) {
            $checked++;
            $oldStatus = $kontrakan->status;
            
            $kontrakan->syncStatusFromBookings();
            
            if ($kontrakan->status !== $oldStatus) {
                $updated++;
                $this->line("  [{$kontrakan->id}] {$kontrakan->nama}: {$oldStatus} → {$kontrakan->status}");
            }
        }

        $this->info("Selesai! Dicek: {$checked}, Diupdate: {$updated}");
        
        return Command::SUCCESS;
    }
}
