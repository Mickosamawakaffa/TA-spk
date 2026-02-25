<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Kriteria;

echo "=== FIX TIPE KRITERIA JUMLAH KAMAR ===\n";

// Cek kriteria saat ini
$kriteriaKamar = Kriteria::where('nama_kriteria', 'LIKE', '%kamar%')->get();

if ($kriteriaKamar->isEmpty()) {
    echo "Tidak ada kriteria kamar ditemukan!\n";
    exit;
}

foreach ($kriteriaKamar as $k) {
    echo "Sebelum: {$k->nama_kriteria} => tipe: {$k->tipe}, bobot: {$k->bobot}\n";
    
    // Perbaiki jika tipe salah
    if ($k->tipe !== 'Benefit') {
        $k->tipe = 'Benefit';
        $k->save();
        echo "DIPERBAIKI: {$k->nama_kriteria} => tipe: {$k->tipe}\n";
    } else {
        echo "OK: {$k->nama_kriteria} => sudah Benefit\n";
    }
}

echo "\nSelesai!\n";