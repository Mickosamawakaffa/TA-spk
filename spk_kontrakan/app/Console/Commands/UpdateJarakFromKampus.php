<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kontrakan;
use Illuminate\Support\Facades\Log;

class UpdateJarakFromKampus extends Command
{
    /**
     * Koordinat Kampus Polije
     */
    const KAMPUS_LAT = -8.15981;
    const KAMPUS_LNG = 113.72312;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:jarak-kampus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update jarak kontrakan dari kampus POLIJE';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai update jarak dari kampus POLIJE...');
        $this->info('Koordinat Kampus: ' . self::KAMPUS_LAT . ', ' . self::KAMPUS_LNG);
        $this->newLine();

        // Update Kontrakan
        $this->info('=== UPDATE KONTRAKAN ===');
        $kontrakan = Kontrakan::whereNotNull('latitude')
                             ->whereNotNull('longitude')
                             ->get();

        $kontrakanUpdated = 0;
        $kontrakanSkipped = 0;

        foreach ($kontrakan as $item) {
            try {
                $jarakKm = $item->calculateDistance(self::KAMPUS_LAT, self::KAMPUS_LNG);
                
                if ($jarakKm !== null) {
                    // Simpan jarak dalam meter
                    $item->jarak = round($jarakKm * 1000, 2);
                    $item->save();
                    $kontrakanUpdated++;
                    $this->line("✓ {$item->nama}: {$item->jarak} meter");
                } else {
                    $kontrakanSkipped++;
                    $this->warn("✗ {$item->nama}: Tidak ada koordinat");
                }
            } catch (\Exception $e) {
                $kontrakanSkipped++;
                $this->error("✗ {$item->nama}: Error - " . $e->getMessage());
                Log::error("Error updating jarak kontrakan {$item->id}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("Kontrakan: {$kontrakanUpdated} updated, {$kontrakanSkipped} skipped");
        $this->newLine();

        $this->info("=== RINGKASAN ===");
        $this->info("Total Updated: {$kontrakanUpdated}");
        $this->info("Total Skipped: {$kontrakanSkipped}");
        $this->newLine();
        $this->info('✓ Update jarak selesai!');

        return Command::SUCCESS;
    }
}




