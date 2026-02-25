<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Laundry;
use App\Models\LayananLaundry;

class LaundrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Koordinat kampus UNY sebagai patokan
        $kampusLat = -7.7956;
        $kampusLng = 110.3695;

        $laundries = [
            [
                'laundry' => [
                    'nama' => 'Laundry Express 24 Jam',
                    'alamat' => 'Jl. Colombo No. 12, Karangmalang',
                    'no_whatsapp' => '6281234567890',
                    'latitude' => -7.7945,
                    'longitude' => 110.3705,
                    'jarak' => 0.8,
                    'jam_buka' => '00:00',
                    'jam_tutup' => '23:59',
                    'status' => 'buka',
                ],
                'layanan' => [
                    [
                        'jenis_layanan' => 'kiloan',
                        'nama_paket' => 'Cuci + Setrika',
                        'harga' => 7000,
                        'estimasi_selesai' => 24,
                        'deskripsi' => 'Paket cuci komplit dengan setrika rapi',
                    ],
                    [
                        'jenis_layanan' => 'satuan',
                        'nama_paket' => 'Setrika Saja',
                        'harga' => 5000,
                        'estimasi_selesai' => 12,
                        'deskripsi' => 'Khusus setrika per item',
                    ],
                ],
            ],
            [
                'laundry' => [
                    'nama' => 'Laundry Kilat Premium',
                    'alamat' => 'Jl. Affandi No. 25, Caturtunggal',
                    'no_whatsapp' => '6281234567891',
                    'latitude' => -7.7820,
                    'longitude' => 110.3750,
                    'jarak' => 1.5,
                    'jam_buka' => '07:00',
                    'jam_tutup' => '21:00',
                    'status' => 'buka',
                ],
                'layanan' => [
                    [
                        'jenis_layanan' => 'kiloan',
                        'nama_paket' => 'Express 6 Jam',
                        'harga' => 12000,
                        'estimasi_selesai' => 6,
                        'deskripsi' => 'Layanan kilat selesai 6 jam',
                    ],
                    [
                        'jenis_layanan' => 'kiloan',
                        'nama_paket' => 'Reguler',
                        'harga' => 8000,
                        'estimasi_selesai' => 24,
                        'deskripsi' => 'Paket reguler 1 hari selesai',
                    ],
                    [
                        'jenis_layanan' => 'satuan',
                        'nama_paket' => 'Dry Clean',
                        'harga' => 25000,
                        'estimasi_selesai' => 48,
                        'deskripsi' => 'Cuci kering untuk pakaian khusus',
                    ],
                ],
            ],
            [
                'laundry' => [
                    'nama' => 'Laundry Hemat Bersih',
                    'alamat' => 'Jl. Kaliurang KM 5, Sleman',
                    'no_whatsapp' => '6281234567892',
                    'latitude' => -7.7650,
                    'longitude' => 110.3800,
                    'jarak' => 2.3,
                    'jam_buka' => '08:00',
                    'jam_tutup' => '18:00',
                    'status' => 'buka',
                ],
                'layanan' => [
                    [
                        'jenis_layanan' => 'kiloan',
                        'nama_paket' => 'Cuci Lipat',
                        'harga' => 5000,
                        'estimasi_selesai' => 48,
                        'deskripsi' => 'Paket ekonomis cuci dan lipat',
                    ],
                    [
                        'jenis_layanan' => 'kiloan',
                        'nama_paket' => 'Cuci + Setrika',
                        'harga' => 6000,
                        'estimasi_selesai' => 48,
                        'deskripsi' => 'Paket lengkap harga terjangkau',
                    ],
                ],
            ],
            [
                'laundry' => [
                    'nama' => 'Clean & Fresh Laundry',
                    'alamat' => 'Jl. Seturan Raya No. 88',
                    'no_whatsapp' => '6281234567893',
                    'latitude' => -7.7500,
                    'longitude' => 110.3950,
                    'jarak' => 3.1,
                    'jam_buka' => '06:00',
                    'jam_tutup' => '22:00',
                    'status' => 'buka',
                ],
                'layanan' => [
                    [
                        'jenis_layanan' => 'kiloan',
                        'nama_paket' => 'Reguler',
                        'harga' => 7500,
                        'estimasi_selesai' => 24,
                        'deskripsi' => 'Paket standar cuci setrika',
                    ],
                    [
                        'jenis_layanan' => 'satuan',
                        'nama_paket' => 'Sepatu',
                        'harga' => 15000,
                        'estimasi_selesai' => 48,
                        'deskripsi' => 'Cuci sepatu lengkap',
                    ],
                ],
            ],
            [
                'laundry' => [
                    'nama' => 'Mahasiswa Laundry',
                    'alamat' => 'Jl. C. Simanjuntak No. 45',
                    'no_whatsapp' => '6281234567894',
                    'latitude' => -7.7700,
                    'longitude' => 110.3600,
                    'jarak' => 1.2,
                    'jam_buka' => '08:00',
                    'jam_tutup' => '20:00',
                    'status' => 'buka',
                ],
                'layanan' => [
                    [
                        'jenis_layanan' => 'kiloan',
                        'nama_paket' => 'Paket Mahasiswa',
                        'harga' => 6000,
                        'estimasi_selesai' => 36,
                        'deskripsi' => 'Paket khusus mahasiswa harga murah',
                    ],
                ],
            ],
        ];

        foreach ($laundries as $data) {
            $laundry = Laundry::create($data['laundry']);
            
            foreach ($data['layanan'] as $layanan) {
                LayananLaundry::create([
                    'laundry_id' => $laundry->id,
                    'jenis_layanan' => $layanan['jenis_layanan'],
                    'nama_paket' => $layanan['nama_paket'],
                    'harga' => $layanan['harga'],
                    'estimasi_selesai' => $layanan['estimasi_selesai'],
                    'deskripsi' => $layanan['deskripsi'],
                    'status' => 'aktif',
                ]);
            }
        }

        $this->command->info('✓ Laundry dan Layanan berhasil di-seed!');
    }
}
