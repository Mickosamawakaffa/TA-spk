<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Kontrakan;

// Get all facilities
$allItems = Kontrakan::get(['fasilitas']);
$fasilitasCount = [];

foreach ($allItems as $item) {
    $facilities = array_map('trim', explode(',', $item->fasilitas));
    foreach ($facilities as $f) {
        if (!isset($fasilitasCount[$f])) {
            $fasilitasCount[$f] = 0;
        }
        $fasilitasCount[$f]++;
    }
}

arsort($fasilitasCount);

echo "=== ALL FACILITIES IN DATABASE ===\n\n";
foreach ($fasilitasCount as $facility => $count) {
    echo "$facility: $count kontrakan\n";
}
