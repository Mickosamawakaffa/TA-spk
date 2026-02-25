<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Laundry;
use Illuminate\Support\Facades\Log;

class UpdateLaundryDistance extends Command
{
    // Koordinat Kampus Polije
    const KAMPUS_LAT = -8.15981;
    const KAMPUS_LNG = 113.72312;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laundry:update-distance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update jarak laundry dari kampus berdasarkan koordinat GPS';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Mulai update jarak laundry dari kampus...');
        
        $laundries = Laundry::whereNotNull('latitude')
                           ->whereNotNull('longitude')
                           ->get();
                           
        if ($laundries->isEmpty()) {
            $this->warn('⚠️  Tidak ada laundry dengan koordinat GPS yang ditemukan.');
            return 0;
        }
        
        $updated = 0;
        $failed = 0;
        
        foreach ($laundries as $laundry) {
            try {
                // Hitung jarak menggunakan method calculateDistance
                $jarakKm = $laundry->calculateDistance(self::KAMPUS_LAT, self::KAMPUS_LNG);
                
                // Update jarak dalam meter
                $laundry->update([
                    'jarak' => round($jarakKm * 1000)
                ]);
                
                $this->line("✅ {$laundry->nama}: {$jarakKm} km ({$laundry->jarak} m)");
                $updated++;
                
            } catch (\Exception $e) {
                $this->error("❌ Gagal update {$laundry->nama}: " . $e->getMessage());
                Log::error("Failed to update distance for laundry {$laundry->id}: " . $e->getMessage());
                $failed++;
            }
        }
        
        $this->newLine();
        $this->info("🎉 Selesai! Updated: {$updated}, Failed: {$failed}");
        
        return 0;
    }
}