<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kontrakan;

class KontrakanSeeder extends Seeder
{
    public function run(): void
    {
        Kontrakan::create([
            'nama' => 'Kontrakan A',
            'alamat' => 'Jl. Mawar No. 10',
            'harga' => 1500000,
            'jarak' => 500,
            'fasilitas' => 'Wifi, Dapur',
            'luas' => 30
        ]);

        Kontrakan::create([
            'nama' => 'Kontrakan B',
            'alamat' => 'Jl. Melati No. 20',
            'harga' => 2000000,
            'jarak' => 800,
            'fasilitas' => 'AC, Kamar Mandi Dalam',
            'luas' => 35
        ]);

        Kontrakan::create([
            'nama' => 'Kontrakan C',
            'alamat' => 'Jl. Anggrek No. 5',
            'harga' => 1800000,
            'jarak' => 600,
            'fasilitas' => 'Wifi, Parkir Motor',
            'luas' => 28
        ]);
    }
}
