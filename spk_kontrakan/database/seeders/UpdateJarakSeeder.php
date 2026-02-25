<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kontrakan;
use App\Models\Laundry;
use Illuminate\Support\Facades\Log;

class UpdateJarakSeeder extends Seeder
{
    /**
     * Koordinat Kampus Polije
     */
    const KAMPUS_LAT = -8.15981;
    const KAMPUS_LNG = 113.72312;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai update jarak dari kampus POLIJE...');
        $this->command->info('Koordinat Kampus: ' . self::KAMPUS_LAT . ', ' . self::KAMPUS_LNG);
        $this->command->newLine();

        // Update Kontrakan
        $this->command->info('=== UPDATE KONTRAKAN ===');
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
                    $this->command->line("✓ {$item->nama}: {$item->jarak} meter");
                } else {
                    $kontrakanSkipped++;
                    $this->command->warn("✗ {$item->nama}: Tidak ada koordinat");
                }
            } catch (\Exception $e) {
                $kontrakanSkipped++;
                $this->command->error("✗ {$item->nama}: Error - " . $e->getMessage());
                Log::error("Error updating jarak kontrakan {$item->id}: " . $e->getMessage());
            }
        }

        $this->command->newLine();
        $this->command->info("Kontrakan: {$kontrakanUpdated} updated, {$kontrakanSkipped} skipped");
        
        // Update Laundry (set jarak = 0 karena dihitung real-time dari user location)
        $this->command->newLine();
        $this->command->info('=== UPDATE LAUNDRY ===');
        $laundryItems = Laundry::whereNotNull('latitude')
                             ->whereNotNull('longitude')
                             ->get();

        $laundryUpdated = 0;
        foreach ($laundryItems as $item) {
            try {
                // Set jarak 0 untuk laundry karena dihitung dinamis dari user location
                $item->jarak = 0;
                $item->save();
                $laundryUpdated++;
                $this->command->line("✓ {$item->nama}: jarak set ke 0 (dinamis dari user)");
            } catch (\Exception $e) {
                $this->command->error("✗ {$item->nama}: Error - " . $e->getMessage());
                Log::error("Error updating jarak laundry {$item->id}: " . $e->getMessage());
            }
        }
        
        $this->command->newLine();
        $this->command->info("Laundry: {$laundryUpdated} updated");
        $this->command->newLine();

        $this->command->info("=== RINGKASAN ===");
        $this->command->info("Total Kontrakan Updated: {$kontrakanUpdated}");
        $this->command->info("Total Laundry Updated: {$laundryUpdated}");
        $this->command->info("Total Skipped: {$kontrakanSkipped}");
        $this->command->newLine();
        $this->command->info('✓ Update jarak selesai!');
    }
}




