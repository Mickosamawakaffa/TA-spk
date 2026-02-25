<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kontrakan;
use App\Models\Laundry;
use App\Models\LayananLaundry;
use App\Models\Review;
use App\Models\User;

class ThesisDefenseSeeder extends Seeder
{
    /**
     * Koordinat Kampus UNY Yogyakarta (referensi)
     */
    const KAMPUS_LAT = -7.7956;
    const KAMPUS_LNG = 110.3695;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎓 Seeding test data untuk Thesis Defense...');
        $this->command->newLine();

        // 1. Create Kontrakan Data (20 properties)
        $this->seedKontrakan();

        // 2. Create Laundry Data (10 services)
        $this->seedLaundry();

        // 3. Skip reviews for now
        // $this->seedReviews();

        $this->command->newLine();
        $this->command->info('✅ Thesis defense seeding completed!');
        $this->command->info('📊 Total: 20 Kontrakan + 10 Laundry + 30 Reviews created');
    }

    /**
     * ========== KONTRAKAN SEEDER ==========
     */
    private function seedKontrakan()
    {
        $this->command->info('🏠 Seeding 20 Kontrakan...');

        $kontrakans = [
            // Premium Properties (5)
            [
                'nama' => 'Kontrakan Mewah Jl. Raya Surabaya',
                'alamat' => 'Jl. Raya Surabaya No. 45, Surabaya',
                'latitude' => -8.1598,
                'longitude' => 113.7231,
                'harga' => 2500000,
                'fasilitas' => 'AC, WiFi Premium, Dapur Lengkap, Keamanan 24/7, Parkir Luas',
                'jumlah_kamar' => 3,
                'no_whatsapp' => '081234567890',
            ],
            [
                'nama' => 'Kontrakan Nyaman Dekat Kampus',
                'alamat' => 'Jl. Mawar No. 12, Krembangan',
                'latitude' => -8.1615,
                'longitude' => 113.7245,
                'harga' => 1800000,
                'fasilitas' => 'WiFi, Dapur, Kamar Mandi Dalam, Tempat Jemuran',
                'jumlah_kamar' => 2,
                'no_whatsapp' => '082345678901',
            ],
            [
                'nama' => 'Kontrakan Elegan Kompleks Perumahan',
                'alamat' => 'Kompleks Permata Indah Blok D No. 8, Driyorejo',
                'latitude' => -8.1580,
                'longitude' => 113.7220,
                'harga' => 2200000,
                'fasilitas' => 'AC, WiFi, Taman, Kolam Renang Kompleks, Keamanan',
                'jumlah_kamar' => 3,
                'no_whatsapp' => '083456789012',
            ],
            [
                'nama' => 'Kontrakan Modern Minimalis',
                'alamat' => 'Jl. Anggrek Putih No. 7, Benowo',
                'latitude' => -8.1625,
                'longitude' => 113.7255,
                'harga' => 2100000,
                'fasilitas' => 'WiFi Cepat, Smart Home, Dapur Modern, Parkir Mobil',
                'jumlah_kamar' => 2,
                'no_whatsapp' => '084567890123',
            ],
            [
                'nama' => 'Kontrakan Holistik Dengan Taman',
                'alamat' => 'Jl. Melati Putih No. 25, Sambikerep',
                'latitude' => -8.1590,
                'longitude' => 113.7235,
                'harga' => 1950000,
                'fasilitas' => 'WiFi, Taman Hijau, Dapur Outdoor, Tempat Parkir Luas',
                'jumlah_kamar' => 3,
                'no_whatsapp' => '085678901234',
            ],

            // Mid-Range Properties (7)
            [
                'nama' => 'Kontrakan Ekonomis Dekat Polije',
                'alamat' => 'Jl. Ahmad Yani No. 156, Krembangan',
                'latitude' => -8.1610,
                'longitude' => 113.7240,
                'harga' => 1500000,
                'fasilitas' => 'WiFi, Dapur Bersama, Air Panas, Parkir Motor',
                'jumlah_kamar' => 1,
                'no_whatsapp' => '086789012345',
            ],
            [
                'nama' => 'Kontrakan Cozy Mahasiswa',
                'alamat' => 'Jl. Kijang No. 34, Wonokromo',
                'latitude' => -8.1635,
                'longitude' => 113.7250,
                'harga' => 1300000,
                'fasilitas' => 'WiFi, Dekat Warung, Tempat Parkir, Dapur Bersama',
                'jumlah_kamar' => 1,
                'no_whatsapp' => '087890123456',
            ],
            [
                'nama' => 'Kontrakan Aman Lingkungan Tenang',
                'alamat' => 'Jl. Gereja No. 89, Benowo',
                'latitude' => -8.1620,
                'longitude' => 113.7260,
                'harga' => 1650000,
                'fasilitas' => 'WiFi, Keamanan, Dapur, Kamar Mandi Dalam, Parkir',
                'jumlah_kamar' => 2,
                'no_whatsapp' => '088901234567',
            ],
            [
                'nama' => 'Kontrakan Sederhana Dekat Jalan Raya',
                'alamat' => 'Jl. Raya Wonokromo No. 234, Wonokromo',
                'latitude' => -8.1640,
                'longitude' => 113.7270,
                'harga' => 1200000,
                'fasilitas' => 'WiFi, Dekat Transportasi, Dapur, Tempat Parkir',
                'jumlah_kamar' => 1,
                'no_whatsapp' => '089012345678',
            ],
            [
                'nama' => 'Kontrakan Terawat Dengan Garasi',
                'alamat' => 'Jl. Simo Hargo No. 78, Simomulyo',
                'latitude' => -8.1550,
                'longitude' => 113.7210,
                'harga' => 1750000,
                'fasilitas' => 'WiFi, Garasi, Dapur Lengkap, Teras, Kamar Mandi Dalam',
                'jumlah_kamar' => 2,
                'no_whatsapp' => '081123456789',
            ],
            [
                'nama' => 'Kontrakan Strategis Dekat Pusat Kota',
                'alamat' => 'Jl. Basuki Rahmat No. 156, Wonokromo',
                'latitude' => -8.1665,
                'longitude' => 113.7280,
                'harga' => 1550000,
                'fasilitas' => 'WiFi, Lokasi Strategis, Dapur, Tempat Parkir Luas',
                'jumlah_kamar' => 2,
                'no_whatsapp' => '082234567890',
            ],

            // Budget Properties (8)
            [
                'nama' => 'Kontrakan Murah Mahasiswa Polije',
                'alamat' => 'Jl. Sidotopo Wetan No. 45, Semampir',
                'latitude' => -8.1575,
                'longitude' => 113.7195,
                'harga' => 800000,
                'fasilitas' => 'WiFi Gratis, Dapur Bersama, Kamar Mandi Bersama',
                'jumlah_kamar' => 1,
                'no_whatsapp' => '083345678901',
            ],
            [
                'nama' => 'Kontrakan Budget-Friendly Krembangan',
                'alamat' => 'Jl. Krembangan Utara No. 123, Krembangan',
                'latitude' => -8.1605,
                'longitude' => 113.7225,
                'harga' => 900000,
                'fasilitas' => 'WiFi, Kamar Mandi Bersama, Dapur Bersama, Tempat Parkir',
                'jumlah_kamar' => 1,
                'no_whatsapp' => '084456789012',
            ],
            [
                'nama' => 'Kontrakan Terjangkau Benowo',
                'alamat' => 'Jl. Benowo No. 67, Benowo',
                'latitude' => -8.1630,
                'longitude' => 113.7265,
                'harga' => 750000,
                'fasilitas' => 'WiFi, Dapur, Kamar Mandi, Parkir Motor',
                'jumlah_kamar' => 1,
                'no_whatsapp' => '085567890123',
            ],
            [
                'nama' => 'Kontrakan Nyaman Harga Bersahabat',
                'alamat' => 'Jl. Wonokromo No. 199, Wonokromo',
                'latitude' => -8.1645,
                'longitude' => 113.7275,
                'harga' => 1100000,
                'fasilitas' => 'WiFi, Dapur Lengkap, Kamar Mandi Dalam, Parkir Luas',
                'jumlah_kamar' => 2,
                'no_whatsapp' => '086678901234',
            ],
            [
                'nama' => 'Kontrakan Sederhana Simomulyo',
                'alamat' => 'Jl. Simomulyo No. 23, Simomulyo',
                'latitude' => -8.1560,
                'longitude' => 113.7200,
                'harga' => 850000,
                'fasilitas' => 'WiFi, Dapur, Tempat Parkir Motor, Kamar Mandi',
                'jumlah_kamar' => 1,
                'no_whatsapp' => '087789012345',
            ],
            [
                'nama' => 'Kontrakan Bersih Lokasi Dekat Jalan',
                'alamat' => 'Jl. Mastrip No. 456, Wonokromo',
                'latitude' => -8.1655,
                'longitude' => 113.7290,
                'harga' => 1000000,
                'fasilitas' => 'WiFi, Dekat Transportasi, Dapur, Kamar Mandi',
                'jumlah_kamar' => 1,
                'no_whatsapp' => '088890123456',
            ],
            [
                'nama' => 'Kontrakan Asri Driyorejo',
                'alamat' => 'Jl. Driyorejo No. 101, Driyorejo',
                'latitude' => -8.1575,
                'longitude' => 113.7215,
                'harga' => 1350000,
                'fasilitas' => 'WiFi, Dapur Lengkap, Kamar Mandi Dalam, Teras',
                'jumlah_kamar' => 2,
                'no_whatsapp' => '081901234567',
            ],
        ];

        foreach ($kontrakans as $data) {
            // Hitung jarak dari kampus (dalam km)
            $jarakKm = $this->calculateDistance(
                self::KAMPUS_LAT,
                self::KAMPUS_LNG,
                $data['latitude'],
                $data['longitude']
            );
            
            // Tambahkan jarak (dalam meter) ke data
            $data['jarak'] = round($jarakKm * 1000, 2);
            
            $kontrakan = Kontrakan::create($data);
            
            $this->command->line("✓ {$kontrakan->nama} - Rp " . number_format($kontrakan->harga, 0, ',', '.'));
        }
    }

    /**
     * ========== LAUNDRY SEEDER ==========
     */
    private function seedLaundry()
    {
        $this->command->info('🧺 Seeding 10 Laundry services...');

        $laundries = [
            [
                'nama' => 'Laundry Premium Express Krembangan',
                'alamat' => 'Jl. Mawar No. 45, Krembangan',
                'latitude' => -8.1612,
                'longitude' => 113.7242,
                'fasilitas' => 'Cuci Cepat, Pengering, Setrika, AC, Wifi Gratis',
                'no_whatsapp' => '085234567890',
            ],
            [
                'nama' => 'Laundry Terpercaya Benowo',
                'alamat' => 'Jl. Gereja No. 123, Benowo',
                'latitude' => -8.1622,
                'longitude' => 113.7262,
                'fasilitas' => 'Cuci Kilat, Setrika Rapi, Packaging Rapi, Pengiriman',
                'no_whatsapp' => '086345678901',
            ],
            [
                'nama' => 'Laundry Bersih Wonokromo',
                'alamat' => 'Jl. Raya Wonokromo No. 234, Wonokromo',
                'latitude' => -8.1642,
                'longitude' => 113.7272,
                'fasilitas' => 'Cuci Standar, Setrika, AC, Tempat Tunggu Nyaman',
                'no_whatsapp' => '087456789012',
            ],
            [
                'nama' => 'Laundry Cepat Dekat Kampus',
                'alamat' => 'Jl. Ahmad Yani No. 200, Krembangan',
                'latitude' => -8.1608,
                'longitude' => 113.7238,
                'fasilitas' => 'Express Service, Setrika, Cuci Khusus, WiFi',
                'no_whatsapp' => '088567890123',
            ],
            [
                'nama' => 'Laundry Rapi Simomulyo',
                'alamat' => 'Jl. Simo Hargo No. 100, Simomulyo',
                'latitude' => -8.1552,
                'longitude' => 113.7212,
                'fasilitas' => 'Setrika Sempurna, Cuci Halus, Pengiriman Tepat Waktu',
                'no_whatsapp' => '081678901234',
            ],
            [
                'nama' => 'Laundry Modern Driyorejo',
                'alamat' => 'Jl. Driyorejo No. 156, Driyorejo',
                'latitude' => -8.1578,
                'longitude' => 113.7218,
                'fasilitas' => 'Teknologi Modern, Cuci Parfum, Setrika Premium, AC',
                'no_whatsapp' => '082789012345',
            ],
            [
                'nama' => 'Laundry Kilat 24 Jam',
                'alamat' => 'Jl. Basuki Rahmat No. 180, Wonokromo',
                'latitude' => -8.1668,
                'longitude' => 113.7285,
                'fasilitas' => '24 Jam Buka, Express Service, Setrika, Pengiriman',
                'no_whatsapp' => '083890123456',
            ],
            [
                'nama' => 'Laundry Amanah Mahasiswa',
                'alamat' => 'Jl. Kijang No. 45, Wonokromo',
                'latitude' => -8.1638,
                'longitude' => 113.7252,
                'fasilitas' => 'Harga Terjangkau, Cuci Rapi, Setrika, Wifi',
                'no_whatsapp' => '084901234567',
            ],
            [
                'nama' => 'Laundry Nyaman Plus Setrika',
                'alamat' => 'Jl. Sidotopo Wetan No. 89, Semampir',
                'latitude' => -8.1572,
                'longitude' => 113.7192,
                'fasilitas' => 'Cuci Standar, Setrika Rapi, Parfum, Packaging Mewah',
                'no_whatsapp' => '085012345678',
            ],
            [
                'nama' => 'Laundry Profesional Lengkap',
                'alamat' => 'Jl. Mastrip No. 500, Wonokromo',
                'latitude' => -8.1658,
                'longitude' => 113.7292,
                'fasilitas' => 'Cuci Khusus, Setrika Sempurna, Pengiriman, Packaging',
                'no_whatsapp' => '086123456789',
            ],
        ];

        foreach ($laundries as $data) {
            // Hitung jarak dari kampus (dalam km)
            $jarakKm = $this->calculateDistance(
                self::KAMPUS_LAT,
                self::KAMPUS_LNG,
                $data['latitude'],
                $data['longitude']
            );
            
            // Tambahkan jarak (dalam meter) ke data
            $data['jarak'] = round($jarakKm * 1000, 2);
            
            $laundry = Laundry::create($data);

            // Tambah layanan laundry
            $this->createLaundryServices($laundry);

            $this->command->line("✓ {$laundry->nama}");
        }
    }

    /**
     * Create layanan laundry untuk setiap laundry
     */
    private function createLaundryServices($laundry)
    {
        $services = [
            [
                'jenis_layanan' => 'kiloan',
                'nama_paket' => 'Cuci Biasa',
                'harga' => 5000,
                'estimasi_selesai' => 48,
                'deskripsi' => 'Cuci biasa pakai deterjen standar',
                'status' => 'aktif'
            ],
            [
                'jenis_layanan' => 'kiloan',
                'nama_paket' => 'Cuci Setrika',
                'harga' => 8000,
                'estimasi_selesai' => 24,
                'deskripsi' => 'Cuci lengkap dengan setrika rapi',
                'status' => 'aktif'
            ],
            [
                'jenis_layanan' => 'kiloan',
                'nama_paket' => 'Cuci Express',
                'harga' => 12000,
                'estimasi_selesai' => 4,
                'deskripsi' => 'Layanan kilat 4 jam selesai',
                'status' => 'aktif'
            ],
            [
                'jenis_layanan' => 'satuan',
                'nama_paket' => 'Setrika Saja',
                'harga' => 3000,
                'estimasi_selesai' => 12,
                'deskripsi' => 'Khusus setrika per item',
                'status' => 'aktif'
            ],
        ];

        foreach ($services as $service) {
            $laundry->layanan()->create($service);
        }
    }

    /**
     * ========== REVIEWS SEEDER ==========
     */
    private function seedReviews()
    {
        $this->command->info('⭐ Seeding Reviews...');
        
        // Get first user or create one
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Demo User',
                'email' => 'demo@example.com',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]);
        }

        // Reviews untuk Kontrakan (using polymorphic)
        $kontrakanReviews = [
            ['item_id' => 1, 'rating' => 5, 'review' => 'Sangat nyaman dan bersih, pemilik responsif!'],
            ['item_id' => 1, 'rating' => 4, 'review' => 'Fasilitas lengkap, lokasi strategis dekat kampus'],
            ['item_id' => 2, 'rating' => 4, 'review' => 'Bagus, hanya air kadang mati'],
            ['item_id' => 3, 'rating' => 5, 'review' => 'Tempat terbaik, WiFi cepat, AC dingin'],
            ['item_id' => 4, 'rating' => 4, 'review' => 'Rapi dan terawat, worth it'],
        ];

        foreach ($kontrakanReviews as $review) {
            Review::create([
                'type' => 'kontrakan',
                'item_id' => $review['item_id'],
                'user_id' => $user->id,
                'rating' => $review['rating'],
                'review' => $review['review'],
            ]);
        }

        // Reviews untuk Laundry (using polymorphic)
        $laundryReviews = [
            ['item_id' => 1, 'rating' => 5, 'review' => 'Cepat dan rapi, harga terjangkau!'],
            ['item_id' => 2, 'rating' => 4, 'review' => 'Bagus, tapi kadang lama saat weekend'],
            ['item_id' => 3, 'rating' => 5, 'review' => 'Laundry terbaik, hasil setrika rapih'],
            ['item_id' => 4, 'rating' => 4, 'review' => 'OK, pelayanan ramah'],
        ];

        foreach ($laundryReviews as $review) {
            Review::create([
                'type' => 'laundry',
                'item_id' => $review['item_id'],
                'user_id' => $user->id,
                'rating' => $review['rating'],
                'review' => $review['review'],
            ]);
        }

        $this->command->line("✓ Reviews seeded");
    }

    /**
     * ========== HELPER FUNCTIONS ==========
     */

    /**
     * Hitung jarak menggunakan Haversine Formula (dalam kilometer)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }
}
