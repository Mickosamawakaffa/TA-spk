<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ALL KONTRAKANS ===\n";
foreach (\App\Models\Kontrakan::with('galeri')->get() as $k) {
    echo "ID: {$k->id} | Nama: {$k->nama}\n";
    echo "  foto: " . var_export($k->foto, true) . "\n";
    echo "  galeri count: " . count($k->galeri) . "\n";
    foreach ($k->galeri as $g) {
        echo "    - galeri: " . $g->foto . " (is_primary: " . var_export($g->is_primary, true) . ")\n";
    }
}

echo "\n=== ALL LAUNDRIES ===\n";
foreach (\App\Models\Laundry::with('galeri')->get() as $l) {
    echo "ID: {$l->id} | Nama: {$l->nama}\n";
    echo "  foto: " . var_export($l->foto, true) . "\n";
    echo "  galeri count: " . count($l->galeri) . "\n";
}
