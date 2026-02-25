<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kriteria;

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing kriteria
        Kriteria::truncate();

        // Kriteria untuk Kontrakan
        $kontrakanKriteria = [
            [
                'tipe_bisnis' => 'kontrakan',
                'nama_kriteria' => 'harga',
                'bobot' => 0.3,
                'tipe' => 'Cost',  // Semakin murah semakin baik
                'keterangan' => 'Harga sewa per tahun',
            ],
            [
                'tipe_bisnis' => 'kontrakan',
                'nama_kriteria' => 'jarak',
                'bobot' => 0.25,
                'tipe' => 'Cost',  // Semakin dekat semakin baik
                'keterangan' => 'Jarak ke kampus dalam meter',
            ],
            [
                'tipe_bisnis' => 'kontrakan',
                'nama_kriteria' => 'jumlah_kamar',
                'bobot' => 0.25,
                'tipe' => 'Benefit',  // Semakin banyak semakin baik
                'keterangan' => 'Jumlah kamar yang tersedia',
            ],
            [
                'tipe_bisnis' => 'kontrakan',
                'nama_kriteria' => 'fasilitas_count',
                'bobot' => 0.2,
                'tipe' => 'Benefit',  // Semakin banyak fasilitas semakin baik
                'keterangan' => 'Jumlah fasilitas yang tersedia',
            ],
        ];

        // Kriteria untuk Laundry
        $laundryKriteria = [
            [
                'tipe_bisnis' => 'laundry',
                'nama_kriteria' => 'harga',
                'bobot' => 0.25,
                'tipe' => 'Cost',  // Semakin murah semakin baik
                'keterangan' => 'Harga per kilogram',
            ],
            [
                'tipe_bisnis' => 'laundry',
                'nama_kriteria' => 'jarak',
                'bobot' => 0.25,
                'tipe' => 'Cost',  // Semakin dekat semakin baik
                'keterangan' => 'Jarak ke kampus dalam meter',
            ],
            [
                'tipe_bisnis' => 'laundry',
                'nama_kriteria' => 'kecepatan_layanan',
                'bobot' => 0.25,
                'tipe' => 'Benefit',  // Semakin cepat semakin baik
                'keterangan' => 'Kecepatan layanan laundry',
            ],
            [
                'tipe_bisnis' => 'laundry',
                'nama_kriteria' => 'layanan',
                'bobot' => 0.25,
                'tipe' => 'Benefit',  // Semakin banyak layanan semakin baik
                'keterangan' => 'Jumlah variasi layanan yang tersedia',
            ],
        ];

        // Insert all kriteria
        foreach (array_merge($kontrakanKriteria, $laundryKriteria) as $kriteria) {
            Kriteria::create($kriteria);
        }

        echo "✅ Kriteria SAW berhasil di-seed!\n";
    }
}
