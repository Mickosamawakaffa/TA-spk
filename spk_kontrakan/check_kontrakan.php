<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DATA KONTRAKAN ===\n\n";

$kontrakans = \App\Models\Kontrakan::all();

if ($kontrakans->isEmpty()) {
    echo "Tidak ada data kontrakan!\n\n";
    echo "Membuat sample data kontrakan...\n\n";
    
    $sample = [
        [
            'nama' => 'Kontrakan Dekat Kampus A',
            'alamat' => 'Jl. Pendidikan No. 123, Jambi',
            'no_whatsapp' => '08123456789',
            'latitude' => -1.6101229,
            'longitude' => 103.6131203,
            'harga' => 500000,
            'jarak' => 2,
            'fasilitas' => 'WiFi,AC,Kasur,Lemari,Meja Belajar',
            'jumlah_kamar' => 10,
            'bathroom_count' => 1,
            'luas' => 20,
            'status' => 'available',
        ],
        [
            'nama' => 'Kontrakan Nyaman Pusat Kota',
            'alamat' => 'Jl. Gatot Subroto No. 45, Jambi',
            'no_whatsapp' => '08234567890',
            'latitude' => -1.5920659,
            'longitude' => 103.6151660,
            'harga' => 750000,
            'jarak' => 5,
            'fasilitas' => 'WiFi,AC,Kasur,Lemari,Dapur,Parkir',
            'jumlah_kamar' => 8,
            'bathroom_count' => 1,
            'luas' => 25,
            'status' => 'available',
        ],
        [
            'nama' => 'Kontrakan Murah Strategis',
            'alamat' => 'Jl. Ahmad Yani No. 78, Jambi',
            'no_whatsapp' => '08345678901',
            'latitude' => -1.6034229,
            'longitude' => 103.6081203,
            'harga' => 400000,
            'jarak' => 3,
            'fasilitas' => 'WiFi,Kasur,Lemari',
            'jumlah_kamar' => 12,
            'bathroom_count' => 1,
            'luas' => 18,
            'status' => 'available',
        ],
    ];
    
    foreach ($sample as $data) {
        \App\Models\Kontrakan::create($data);
        echo "✓ Created: {$data['nama']}\n";
    }
    
    echo "\nSample data berhasil dibuat!\n";
} else {
    echo "Total kontrakan: " . $kontrakans->count() . "\n\n";
    foreach ($kontrakans as $k) {
        echo "ID: {$k->id}\n";
        echo "Nama: {$k->nama}\n";
        echo "Alamat: {$k->alamat}\n";
        echo "Harga: Rp " . number_format($k->harga, 0, ',', '.') . "\n";
        echo "Jarak: {$k->jarak} km\n";
        echo "Kamar: {$k->jumlah_kamar}\n";
        echo "Status: {$k->status}\n";
        echo "---\n";
    }
}

echo "\n=== SELESAI ===\n";
