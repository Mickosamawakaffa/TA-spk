<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Kontrakan;

// Check available statuses
$statuses = Kontrakan::distinct()->pluck('status');
echo "Available statuses: " . json_encode($statuses->toArray()) . "\n\n";

$kontrakan = Kontrakan::first();
echo "Sample kontrakan: " . $kontrakan->nama . "\n";
echo "Fasilitas: " . $kontrakan->fasilitas . "\n";

$count = count(array_filter(array_map('trim', explode(',', $kontrakan->fasilitas))));
echo "Fasilitas count: " . $count . "\n\n";

// Check max fasilitas
$all = Kontrakan::get();
$counts = $all->map(function($k) {
    return count(array_filter(array_map('trim', explode(',', $k->fasilitas))));
});

echo "Max fasilitas in any kontrakan: " . $counts->max() . "\n";
echo "Min fasilitas: " . $counts->min() . "\n";
echo "Average fasilitas: " . round($counts->avg(), 1) . "\n";
echo "\nKontrakan dengan >= 15 fasilitas: " . $all->filter(function($k) {
    $count = count(array_filter(array_map('trim', explode(',', $k->fasilitas))));
    return $count >= 15;
})->count() . " / " . $all->count() . "\n";

echo "\nDistribution:\n";
$distribution = $counts->groupBy(function($item) {
    return $item;
})->sortKeys();

foreach ($distribution as $num => $items) {
    echo "- $num fasilitas: " . $items->count() . " kontrakan\n";
}
