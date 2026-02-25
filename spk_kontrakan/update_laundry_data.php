<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Update Data Laundry ===\n\n";

// Update laundry yang sudah ada dengan data lengkap
$laundries = [
    [
        'id' => 1,
        'nama' => 'Laundry Express 24',
        'alamat' => 'Jl. Mastrip No. 45, Warugunung, Surabaya',
        'no_whatsapp' => '6281234567890',
        'latitude' => -7.3192,
        'longitude' => 112.7291,
        'jam_buka' => '06:00',
        'jam_tutup' => '22:00',
        'harga_kiloan' => 8000,
        'harga_satuan' => 12000,
        'estimasi_selesai' => 24,
        'rating' => 4.5,
        'status' => 'buka',
    ],
    [
        'id' => 2,
        'nama' => 'Clean & Fresh Laundry',
        'alamat' => 'Jl. Gebang Putih No. 12, Sukolilo, Surabaya',
        'no_whatsapp' => '6281234567891',
        'latitude' => -7.2875,
        'longitude' => 112.8050,
        'jam_buka' => '07:00',
        'jam_tutup' => '21:00',
        'harga_kiloan' => 7500,
        'harga_satuan' => 10000,
        'estimasi_selesai' => 48,
        'rating' => 4.3,
        'status' => 'buka',
    ],
];

// Tambah laundry baru
$newLaundries = [
    [
        'nama' => 'Laundry Kilat Keputih',
        'alamat' => 'Jl. Keputih Tegal Timur No. 88, Surabaya',
        'no_whatsapp' => '6281234567892',
        'latitude' => -7.2929,
        'longitude' => 112.7973,
        'jam_buka' => '08:00',
        'jam_tutup' => '20:00',
        'harga_kiloan' => 9000,
        'harga_satuan' => 15000,
        'estimasi_selesai' => 24,
        'rating' => 4.7,
        'status' => 'buka',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'nama' => 'Laundry Premium ITS',
        'alamat' => 'Jl. Teknik Kimia ITS, Keputih, Surabaya',
        'no_whatsapp' => '6281234567893',
        'latitude' => -7.2819,
        'longitude' => 112.7951,
        'jam_buka' => '07:00',
        'jam_tutup' => '23:00',
        'harga_kiloan' => 10000,
        'harga_satuan' => 18000,
        'estimasi_selesai' => 12,
        'rating' => 4.8,
        'status' => 'buka',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'nama' => 'Laundry Murah Meriah',
        'alamat' => 'Jl. Gebang Lor No. 23, Sukolilo, Surabaya',
        'no_whatsapp' => '6281234567894',
        'latitude' => -7.2950,
        'longitude' => 112.8020,
        'jam_buka' => '06:00',
        'jam_tutup' => '20:00',
        'harga_kiloan' => 6000,
        'harga_satuan' => 8000,
        'estimasi_selesai' => 48,
        'rating' => 4.0,
        'status' => 'buka',
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

// Update existing laundries
foreach ($laundries as $laundry) {
    $id = $laundry['id'];
    unset($laundry['id']);
    $laundry['updated_at'] = now();
    
    DB::table('laundry')->where('id', $id)->update($laundry);
    echo "✓ Updated laundry ID {$id}: {$laundry['nama']}\n";
}

// Insert new laundries
foreach ($newLaundries as $laundry) {
    DB::table('laundry')->insert($laundry);
    echo "✓ Inserted new laundry: {$laundry['nama']}\n";
}

echo "\n=== Summary ===\n";
$total = DB::table('laundry')->count();
echo "Total laundry in database: {$total}\n\n";

// Display all laundries
echo "All Laundries:\n";
$allLaundries = DB::table('laundry')->get();
foreach ($allLaundries as $laundry) {
    echo "ID: {$laundry->id}\n";
    echo "  Nama: {$laundry->nama}\n";
    echo "  Alamat: {$laundry->alamat}\n";
    echo "  Harga Kiloan: Rp " . number_format($laundry->harga_kiloan, 0, ',', '.') . "/kg\n";
    echo "  Harga Satuan: Rp " . number_format($laundry->harga_satuan, 0, ',', '.') . "/pcs\n";
    echo "  Rating: {$laundry->rating}\n";
    echo "  Status: {$laundry->status}\n";
    echo "  Jam: {$laundry->jam_buka} - {$laundry->jam_tutup}\n\n";
}

echo "✓ Data laundry berhasil diupdate!\n";
