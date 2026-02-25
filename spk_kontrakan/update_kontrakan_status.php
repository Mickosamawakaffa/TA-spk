<?php
// Update kontrakan 37 status dari booked ke available
require 'bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Kontrakan;

$kontrakan = Kontrakan::find(37);
if ($kontrakan) {
    $kontrakan->status = 'available';
    $kontrakan->save();
    echo "✓ Status kontrakan ID 37 berhasil diubah menjadi: " . $kontrakan->status . "\n";
    echo "  Nama: " . $kontrakan->nama . "\n";
} else {
    echo "✗ Kontrakan ID 37 tidak ditemukan\n";
}
