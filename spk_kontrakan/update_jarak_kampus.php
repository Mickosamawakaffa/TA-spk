<?php

/**
 * Script untuk update jarak dari koordinat yang sudah ada
 * Menggunakan koordinat UNY Yogyakarta
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Kontrakan;
use App\Models\Laundry;

// Koordinat Kampus UNY
$kampusLat = -7.7956;
$kampusLng = 110.3695;

echo "🔄 Updating jarak untuk semua data...\n\n";

// Update Kontrakan
$kontrakanUpdated = 0;
$kontrakanList = Kontrakan::all();
foreach ($kontrakanList as $k) {
    if ($k->latitude && $k->longitude) {
        $jarak = calculateDistance($kampusLat, $kampusLng, $k->latitude, $k->longitude);
        $k->jarak_kampus = $jarak; // dalam km
        $k->save();
        $kontrakanUpdated++;
        echo "✅ {$k->nama}: {$jarak} km\n";
    }
}

echo "\n";

// Update Laundry
$laundryUpdated = 0;
$laundryList = Laundry::all();
foreach ($laundryList as $l) {
    if ($l->latitude && $l->longitude) {
        $jarak = calculateDistance($kampusLat, $kampusLng, $l->latitude, $l->longitude);
        $l->jarak_kampus = $jarak; // dalam km
        $l->jarak = $jarak; // field alternatif
        $l->save();
        $laundryUpdated++;
        echo "✅ {$l->nama}: {$jarak} km\n";
    }
}

echo "\n";
echo "📊 Summary:\n";
echo "   - Kontrakan updated: $kontrakanUpdated\n";
echo "   - Laundry updated: $laundryUpdated\n";
echo "✅ Jarak calculation completed!\n";

/**
 * Calculate distance using Haversine formula
 */
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // km

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);

    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $distance = $earthRadius * $c;

    return round($distance, 2);
}
