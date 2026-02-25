<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DATA LAUNDRY ===\n\n";

$laundries = \App\Models\Laundry::all();

if ($laundries->isEmpty()) {
    echo "Tidak ada data laundry!\n\n";
    echo "Membuat sample data laundry...\n\n";
    
    $sample = [
        [
            'nama' => 'Laundry Express 24 Jam',
            'alamat' => 'Jl. Sudirman No. 45, Jambi',
            'no_whatsapp' => '08123456789',
            'latitude' => -1.5920659,
            'longitude' => 103.6151660,
            'jam_buka' => '00:00',
            'jam_tutup' => '23:59',
            'harga_kiloan' => 5000,
            'harga_satuan' => 15000,
            'estimasi_selesai' => 24,
            'rating' => 4.5,
            'status' => 'buka',
        ],
        [
            'nama' => 'Laundry Kilat Prima',
            'alamat' => 'Jl. Ahmad Yani No. 123, Jambi',
            'no_whatsapp' => '08234567890',
            'latitude' => -1.6034229,
            'longitude' => 103.6081203,
            'jam_buka' => '08:00',
            'jam_tutup' => '20:00',
            'harga_kiloan' => 4500,
            'harga_satuan' => 12000,
            'estimasi_selesai' => 48,
            'rating' => 4.8,
            'status' => 'buka',
        ],
        [
            'nama' => 'Laundry Bersih Wangi',
            'alamat' => 'Jl. Gatot Subroto No. 78, Jambi',
            'no_whatsapp' => '08345678901',
            'latitude' => -1.6101229,
            'longitude' => 103.6131203,
            'jam_buka' => '07:00',
            'jam_tutup' => '21:00',
            'harga_kiloan' => 6000,
            'harga_satuan' => 18000,
            'estimasi_selesai' => 24,
            'rating' => 4.2,
            'status' => 'buka',
        ],
    ];
    
    foreach ($sample as $data) {
        \App\Models\Laundry::create($data);
        echo "✓ Created: {$data['nama']}\n";
    }
    
    echo "\nSample data berhasil dibuat!\n";
} else {
    echo "Total laundry: " . $laundries->count() . "\n\n";
    foreach ($laundries as $l) {
        echo "ID: {$l->id}\n";
        echo "Nama: {$l->nama}\n";
        echo "Alamat: {$l->alamat}\n";
        echo "Harga Kiloan: Rp " . number_format($l->harga_kiloan, 0, ',', '.') . "/kg\n";
        echo "Rating: {$l->rating}\n";
        echo "Status: {$l->status}\n";
        echo "---\n";
    }
}

echo "\n=== SELESAI ===\n";
