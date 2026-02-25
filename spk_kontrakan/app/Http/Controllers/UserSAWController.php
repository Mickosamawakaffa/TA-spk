<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kontrakan;
use App\Models\Laundry;
use App\Models\Kriteria;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Exception;

class UserSAWController extends Controller
{
    // Koordinat Kampus Polije (FIXED)
    const KAMPUS_LAT = -8.15981;
    const KAMPUS_LNG = 113.72312;
    
    /**
     * Tampilkan halaman input preferensi user (Hybrid System)
     */
    public function index()
    {
        try {
            // Ambil kriteria untuk kontrakan
            $kriteria = Kriteria::where('tipe_bisnis', 'kontrakan')->get();
            
            if ($kriteria->isEmpty()) {
                return view('user.preferensi')->with('error', 'Data kriteria belum tersedia. Hubungi administrator.');
            }
            
            // Ambil ID kontrakan yang sedang di-booking aktif
            $bookedKontrakanIds = Booking::whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_CONFIRMED, 
                Booking::STATUS_CHECKED_IN
            ])->pluck('kontrakan_id')->toArray();

            // Ambil kontrakan yang tersedia (tidak sedang di-booking)
            $kontrakans = Kontrakan::whereNotIn('id', $bookedKontrakanIds)->get();
            
            // Hitung total dan yang tersedia untuk info
            $totalKontrakan = Kontrakan::count();
            $availableCount = $kontrakans->count();
            $bookedCount = count($bookedKontrakanIds);
            
            return view('user.preferensi', compact('kriteria', 'kontrakans', 'totalKontrakan', 'availableCount', 'bookedCount'));
            
        } catch (Exception $e) {
            Log::error('Error di User SAW Index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    /**
     * Proses perhitungan SAW dengan bobot hybrid (10-70%)
     */
    public function calculate(Request $request)
    {
        try {
            // Validasi input bobot (sekarang dalam %)
            $request->validate([
                'bobot_harga' => 'required|integer|min:10|max:70',
                'bobot_jarak' => 'required|integer|min:10|max:70',
                'bobot_jumlah_kamar' => 'required|integer|min:10|max:70',
                'bobot_fasilitas' => 'required|integer|min:10|max:70',
                'mode' => 'required|in:preset,manual',
            ], [
                'bobot_harga.min' => 'Bobot harga minimal 10%!',
                'bobot_harga.max' => 'Bobot harga maksimal 70%!',
                'bobot_jarak.min' => 'Bobot jarak minimal 10%!',
                'bobot_jarak.max' => 'Bobot jarak maksimal 70%!',
                'bobot_jumlah_kamar.min' => 'Bobot jumlah kamar minimal 10%!',
                'bobot_jumlah_kamar.max' => 'Bobot jumlah kamar maksimal 70%!',
                'bobot_fasilitas.min' => 'Bobot fasilitas minimal 10%!',
                'bobot_fasilitas.max' => 'Bobot fasilitas maksimal 70%!',
            ]);

            // Validasi total bobot harus 100%
            $totalBobot = $request->bobot_harga + $request->bobot_jarak + 
                          $request->bobot_jumlah_kamar + $request->bobot_fasilitas;
            
            if ($totalBobot != 100) {
                return back()->withErrors([
                    'bobot' => "Total bobot harus tepat 100%. Sekarang: {$totalBobot}%"
                ])->withInput();
            }

            // Gunakan koordinat kampus sebagai referensi (FIXED)
            $refLat = self::KAMPUS_LAT;
            $refLng = self::KAMPUS_LNG;
            $refName = 'Kampus Polije';

            // Convert bobot ke decimal (0-1)
            $bobot = [
                'harga' => $request->bobot_harga / 100,
                'jarak' => $request->bobot_jarak / 100,
                'jumlah_kamar' => $request->bobot_jumlah_kamar / 100,
                'fasilitas' => $request->bobot_fasilitas / 100,
            ];

            Log::info('User SAW Hybrid Input:', [
                'bobot' => $bobot,
                'reference_point' => $refName,
                'ref_lat' => $refLat,
                'ref_lng' => $refLng,
            ]);

            // Ambil kriteria kontrakan
            $kriteria = Kriteria::where('tipe_bisnis', 'kontrakan')->get();
            
            if ($kriteria->isEmpty()) {
                return redirect()->route('user.search')
                    ->with('error', 'Kriteria belum tersedia!');
            }

            // Ambil ID kontrakan yang sedang di-booking aktif (pending, confirmed, checked_in)
            $bookedKontrakanIds = Booking::whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_CONFIRMED, 
                Booking::STATUS_CHECKED_IN
            ])->pluck('kontrakan_id')->toArray();

            // Ambil semua kontrakan yang TERSEDIA (tidak sedang di-booking)
            $kontrakan = Kontrakan::whereNotIn('id', $bookedKontrakanIds)->get();
            
            if ($kontrakan->isEmpty()) {
                return redirect()->route('user.search')
                    ->with('error', 'Semua kontrakan sedang tidak tersedia (sudah di-booking). Silakan coba lagi nanti.');
            }

            // Proses data kontrakan dengan lokasi referensi
            $kontrakanProcessed = $this->processKontrakan($kontrakan, $refLat, $refLng);

            // Hitung max/min
            $maxMin = $this->calculateMaxMin($kontrakanProcessed);

            // Hitung SAW dengan bobot user
            $hasil = $this->calculateSAW($kontrakanProcessed, $kriteria, $bobot, $maxMin);

            if (empty($hasil)) {
                return redirect()->route('user.search')
                    ->with('error', 'Gagal menghitung hasil. Silakan coba lagi.');
            }

            // Info bobot untuk ditampilkan (TRANSPARANSI!)
            $bobotInfo = [
                'mode' => $request->mode,
                'preset_type' => $request->preset_type ?? null,
                'values' => [
                    'harga' => $request->bobot_harga,
                    'jarak' => $request->bobot_jarak,
                    'kamar' => $request->bobot_jumlah_kamar,
                    'fasilitas' => $request->bobot_fasilitas,
                ],
                'reference_point' => $refName,
                'user_location' => ($request->user_lat && $request->user_lng) ? [
                    'lat' => $request->user_lat,
                    'lng' => $request->user_lng
                ] : null
            ];

            return view('user.hasil', compact('hasil', 'kriteria', 'bobotInfo'));
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('user.search')
                ->withErrors($e->validator)
                ->withInput();
                
        } catch (Exception $e) {
            Log::error('Error di User SAW Calculate: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('user.search')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Proses data kontrakan dengan hitung fasilitas & jarak
     */
    private function processKontrakan($kontrakan, $refLat, $refLng)
    {
        return $kontrakan->map(function($item) use ($refLat, $refLng) {
            try {
                // Hitung jumlah fasilitas
                $item->jumlah_fasilitas = $item->fasilitas ? count(explode(',', $item->fasilitas)) : 0;
                
                // Set harga
                $item->harga_value = $item->harga ?? 0;
                
                // Hitung jarak dari lokasi referensi
                if ($item->latitude && $item->longitude) {
                    try {
                        // calculateDistance mengembalikan jarak dalam km, konversi ke meter
                        $item->jarak_value = $item->calculateDistance($refLat, $refLng) * 1000;
                    } catch (Exception $e) {
                        Log::warning("Gagal hitung jarak untuk {$item->nama}: " . $e->getMessage());
                        $item->jarak_value = $item->jarak ?? 0;
                    }
                } else {
                    $item->jarak_value = $item->jarak ?? 0;
                }
                
                // Set jumlah_kamar
                $item->jumlah_kamar_value = $item->jumlah_kamar ?? 0;
                
                return $item;
                
            } catch (Exception $e) {
                Log::error("Error processing kontrakan {$item->id}: " . $e->getMessage());
                return null;
            }
        })->filter();
    }

    /**
     * Hitung max/min untuk normalisasi
     */
    private function calculateMaxMin($kontrakan)
    {
        return [
            'harga' => [
                'max' => $kontrakan->max('harga_value') ?: 1,
                'min' => $kontrakan->min('harga_value') ?: 1,
            ],
            'jarak' => [
                'max' => $kontrakan->max('jarak_value') ?: 1,
                'min' => $kontrakan->min('jarak_value') ?: 1,
            ],
            'jumlah_kamar' => [
                'max' => $kontrakan->max('jumlah_kamar_value') ?: 1,
                'min' => $kontrakan->min('jumlah_kamar_value') ?: 1,
            ],
            'fasilitas' => [
                'max' => $kontrakan->max('jumlah_fasilitas') ?: 1,
                'min' => $kontrakan->min('jumlah_fasilitas') ?: 1,
            ],
        ];
    }

    /**
     * Hitung SAW dengan bobot dari user
     */
    private function calculateSAW($kontrakan, $kriteria, $bobot, $maxMin)
    {
        $hasil = [];

        foreach ($kontrakan as $item) {
            try {
                $normalisasi = [];
                $nilaiTotal = 0;

                // Hitung untuk setiap kriteria
                $kriteriaMap = [
                    'harga' => ['value' => $item->harga_value, 'maxmin' => $maxMin['harga']],
                    'jarak' => ['value' => $item->jarak_value, 'maxmin' => $maxMin['jarak']],
                    'jumlah_kamar' => ['value' => $item->jumlah_kamar_value, 'maxmin' => $maxMin['jumlah_kamar']],
                    'fasilitas' => ['value' => $item->jumlah_fasilitas, 'maxmin' => $maxMin['fasilitas']],
                ];

                foreach ($kriteria as $krit) {
                    $kriteriaKey = $this->getKriteriaKey($krit->nama_kriteria);
                    
                    if (!isset($kriteriaMap[$kriteriaKey]) || !isset($bobot[$kriteriaKey])) {
                        continue;
                    }

                    $nilaiAsli = $kriteriaMap[$kriteriaKey]['value'];
                    $maxMinKrit = $kriteriaMap[$kriteriaKey]['maxmin'];
                    $bobotKrit = $bobot[$kriteriaKey];
                    $tipeKrit = strtolower($krit->tipe);

                    $nilaiNormalisasi = $this->normalize($nilaiAsli, $maxMinKrit, $tipeKrit);

                    // Debug untuk jumlah kamar
                    if ($kriteriaKey === 'jumlah_kamar') {
                        Log::info("Debug Kamar - {$item->nama}: asli={$nilaiAsli}, max={$maxMinKrit['max']}, tipe={$tipeKrit}, normalisasi={$nilaiNormalisasi}");
                    }

                    $normalisasi[$krit->nama_kriteria] = [
                        'asli' => $nilaiAsli,
                        'normalisasi' => round($nilaiNormalisasi, 4),
                        'bobot' => $bobotKrit,
                        'tipe' => $krit->tipe,
                    ];

                    $nilaiTotal += ($nilaiNormalisasi * $bobotKrit);
                }

                $hasil[] = [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'alamat' => $item->alamat,
                    'harga' => $item->harga,
                    'jarak' => $item->jarak_value,
                    'jumlah_kamar' => $item->jumlah_kamar,
                    'fasilitas' => $item->fasilitas,
                    'jumlah_fasilitas' => $item->jumlah_fasilitas,
                    'foto' => $item->foto,
                    'latitude' => $item->latitude,
                    'longitude' => $item->longitude,
                    'no_whatsapp' => $item->no_whatsapp,
                    'status' => $item->status ?? 'available', // Status ketersediaan
                    'normalisasi' => $normalisasi,
                    'nilai' => round($nilaiTotal, 4),
                ];

            } catch (Exception $e) {
                Log::error("Error calculating SAW for kontrakan {$item->id}: " . $e->getMessage());
                continue;
            }
        }

        // Sort berdasarkan nilai tertinggi
        usort($hasil, fn($a, $b) => $b['nilai'] <=> $a['nilai']);

        // Tambahkan ranking
        foreach ($hasil as $key => $value) {
            $hasil[$key]['ranking'] = $key + 1;
        }

        return $hasil;
    }

    /**
     * Normalisasi nilai
     */
    private function normalize($nilai, $maxMin, $tipe)
    {
        try {
            if ($nilai == 0 && $tipe == 'cost') {
                return 0;
            }

            if ($tipe == 'cost') {
                return $nilai > 0 ? $maxMin['min'] / $nilai : 0;
            } else {
                return $maxMin['max'] > 0 ? $nilai / $maxMin['max'] : 0;
            }

        } catch (Exception $e) {
            Log::error('Error normalize: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get bobot info untuk transparansi
     */
    private function getBobotInfo($kriteria, $request)
    {
        $bobotInfo = [];
        foreach ($kriteria as $k) {
            $bobotInfo[] = [
                'nama' => $k->nama_kriteria,
                'bobot' => $request->input('bobot_' . $k->kode_kriteria, 50),
            ];
        }
        return $bobotInfo;
    }

    /**
     * Helper: Convert nama kriteria ke key
     */
    private function getKriteriaKey($namaKriteria)
    {
        $namaLower = strtolower($namaKriteria);
        
        if (strpos($namaLower, 'harga') !== false) return 'harga';
        if (strpos($namaLower, 'jarak') !== false) return 'jarak';
        if (strpos($namaLower, 'jumlah_kamar') !== false || 
            strpos($namaLower, 'jumlah kamar') !== false || 
            strpos($namaLower, 'luas') !== false) return 'jumlah_kamar';
        if (strpos($namaLower, 'fasilitas') !== false) return 'fasilitas';
        
        return explode(' ', $namaLower)[0];
    }

    /**
     * Halaman Preferensi SAW untuk Kontrakan
     */
    public function preferensi()
    {
        try {
            $kriteria = Kriteria::where('tipe_bisnis', 'kontrakan')->get();
            
            if ($kriteria->isEmpty()) {
                return redirect()->route('welcome')
                    ->with('error', 'Data kriteria belum tersedia. Hubungi administrator.');
            }
            
            // Ambil ID kontrakan yang sedang di-booking aktif
            $bookedKontrakanIds = Booking::whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_CONFIRMED, 
                Booking::STATUS_CHECKED_IN
            ])->pluck('kontrakan_id')->toArray();

            // Ambil kontrakan yang tersedia (tidak sedang di-booking)
            $kontrakans = Kontrakan::whereNotIn('id', $bookedKontrakanIds)->get();
            
            // Hitung total dan yang tersedia untuk info
            $totalKontrakan = Kontrakan::count();
            $availableCount = $kontrakans->count();
            $bookedCount = count($bookedKontrakanIds);
            
            return view('user.preferensi', compact('kriteria', 'kontrakans', 'totalKontrakan', 'availableCount', 'bookedCount'));
            
        } catch (Exception $e) {
            Log::error('Error di User Preferensi: ' . $e->getMessage());
            return redirect()->route('welcome')->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * Halaman Preferensi SAW untuk Laundry
     */
    public function preferensiLaundry()
    {
        try {
            $kriteria = Kriteria::where('tipe_bisnis', 'laundry')->get();
            
            if ($kriteria->isEmpty()) {
                return redirect()->route('welcome')
                    ->with('error', 'Data kriteria laundry belum tersedia.');
            }
            
            return view('user.preferensi-laundry', compact('kriteria'));
            
        } catch (Exception $e) {
            Log::error('Error di User Preferensi Laundry: ' . $e->getMessage());
            return redirect()->route('welcome')->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * Halaman Search Laundry
     */
    public function searchLaundry()
    {
        try {
            $kriteria = Kriteria::where('tipe_bisnis', 'laundry')->get();
            
            if ($kriteria->isEmpty()) {
                return redirect()->route('welcome')
                    ->with('error', 'Data kriteria laundry belum tersedia.');
            }
            
            return view('user.search-laundry', compact('kriteria'));
            
        } catch (Exception $e) {
            Log::error('Error di User Search Laundry: ' . $e->getMessage());
            return redirect()->route('welcome')->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * Calculate Laundry dengan SAW (Auto - Tanpa Bobot Manual)
     */
    public function calculateLaundry(Request $request)
    {
        try {
            // Ambil kriteria untuk laundry
            $kriteria = Kriteria::where('tipe_bisnis', 'laundry')->get();
            
            if ($kriteria->isEmpty()) {
                return redirect()->route('user.search.laundry')
                    ->with('error', 'Data kriteria belum tersedia. Hubungi administrator.');
            }

            // Validasi input hanya kategori dan lokasi
            $request->validate([
                'kategori_layanan' => 'required|in:reguler,express,kilat,premium',
                'user_lat' => 'nullable|numeric',
                'user_lng' => 'nullable|numeric',
            ]);

            // Ambil semua laundry
            $laundries = Laundry::all();

            if ($laundries->isEmpty()) {
                return redirect()->route('user.search.laundry')
                    ->with('error', 'Belum ada data laundry yang tersedia.');
            }

            // Tentukan lokasi referensi untuk perhitungan jarak
            $refLat = $request->user_lat ?: self::KAMPUS_LAT;
            $refLng = $request->user_lng ?: self::KAMPUS_LNG;
            $refName = ($request->user_lat && $request->user_lng) ? 'Lokasi Anda' : 'Kampus Polije';

            Log::info('Laundry SAW Calculation (Auto):', [
                'kategori_layanan' => $request->kategori_layanan,
                'reference_location' => $refName,
                'ref_lat' => $refLat,
                'ref_lng' => $refLng,
            ]);

            // Hitung jarak dari lokasi referensi untuk setiap laundry
            foreach ($laundries as $laundry) {
                if (!empty($laundry->latitude) && !empty($laundry->longitude)) {
                    $laundry->jarak_kampus = $this->haversineDistance(
                        $refLat, 
                        $refLng, 
                        $laundry->latitude, 
                        $laundry->longitude
                    );
                } else {
                    $laundry->jarak_kampus = 999; // Default jarak besar jika koordinat tidak ada
                }
            }

            // Proses Normalisasi dan Perhitungan SAW dengan filter kategori
            $hasil = $this->prosesLaundrySAW($laundries, $kriteria, $request);

            // Simpan info untuk tampilan
            $bobotInfo = [
                'kategori_layanan' => $request->kategori_layanan,
                'reference_point' => $refName,
                'user_location' => ($request->user_lat && $request->user_lng) ? [
                    'lat' => $request->user_lat,
                    'lng' => $request->user_lng
                ] : null,
                'mode' => 'auto'
            ];

            return view('user.hasil-laundry', compact('hasil', 'kriteria', 'bobotInfo'));

        } catch (Exception $e) {
            Log::error('Error di Calculate Laundry: ' . $e->getMessage());
            return redirect()->route('user.search.laundry')
                ->with('error', 'Terjadi kesalahan dalam perhitungan. Silakan coba lagi.');
        }
    }

    /**
     * Proses perhitungan SAW untuk Laundry (Auto - Bobot Seimbang)
     */
    private function prosesLaundrySAW($laundries, $kriteria, $request)
    {
        $hasil = [];
        $normalizedData = [];
        $kategoriLayanan = $request->kategori_layanan;

        // Filter laundry yang memiliki layanan sesuai kategori yang dipilih
        $laundries = $laundries->filter(function($laundry) use ($kategoriLayanan) {
            return $laundry->layanan()
                ->where('jenis_layanan', $kategoriLayanan)
                ->exists();
        });

        if ($laundries->isEmpty()) {
            Log::warning('Tidak ada laundry dengan kategori: ' . $kategoriLayanan);
            return [];
        }

        // Step 1: Normalisasi
        foreach ($kriteria as $k) {
            $values = [];
            
            // Kumpulkan nilai dengan key = laundry->id untuk menghindari undefined index
            foreach ($laundries as $laundry) {
                switch ($k->kode_kriteria) {
                    case 'H': // Harga
                        $values[$laundry->id] = $this->getHargaLaundryByKategori($laundry, $kategoriLayanan);
                        break;
                    case 'J': // Jarak
                        $values[$laundry->id] = $laundry->jarak_kampus;
                        break;
                    case 'R': // Rating
                        $values[$laundry->id] = $laundry->rating ?: 3; // Default 3 jika tidak ada rating
                        break;
                    case 'F': // Fasilitas
                        $values[$laundry->id] = $this->getFasilitasScore($laundry);
                        break;
                }
            }

            // Cek apakah array values tidak kosong sebelum memanggil max/min
            if (empty($values)) {
                continue; // Skip kriteria ini jika tidak ada nilai
            }

            $max = max($values);
            $min = min($values);

            foreach ($laundries as $laundry) {
                $value = $values[$laundry->id]; // Gunakan ID bukan index
                
                if ($k->jenis_kriteria === 'benefit') {
                    $normalizedData[$laundry->id][$k->kode_kriteria] = $max != $min ? $value / $max : 1;
                } else { // cost
                    $normalizedData[$laundry->id][$k->kode_kriteria] = $min != $max ? $min / $value : 1;
                }
            }
        }

        // Step 2: Perhitungan SAW dengan bobot seimbang otomatis
        $jumlahKriteria = $kriteria->count();
        $bobotSeimbang = $jumlahKriteria > 0 ? (1 / $jumlahKriteria) : 0.25; // Default 25% jika ada 4 kriteria
        
        foreach ($laundries as $laundry) {
            $nilaiSAW = 0;
            
            foreach ($kriteria as $k) {
                // Cek apakah ada data normalisasi untuk kriteria ini
                $nilaiNormalisasi = $normalizedData[$laundry->id][$k->kode_kriteria] ?? 0;
                $nilaiSAW += $bobotSeimbang * $nilaiNormalisasi;
            }

            // Ambil layanan spesifik untuk kategori yang dipilih
            $layananSpesifik = $laundry->layanan()
                ->where('jenis_layanan', $kategoriLayanan)
                ->first();

            $hasil[] = [
                'laundry' => $laundry,
                'layanan' => $layananSpesifik,
                'nilai' => round($nilaiSAW, 4),
                'detail' => $normalizedData[$laundry->id] ?? []
            ];
        }

        // Sort by nilai descending
        usort($hasil, function($a, $b) {
            return $b['nilai'] <=> $a['nilai'];
        });

        return $hasil;
    }

    /**
     * Get harga rata-rata laundry
     */
    private function getHargaLaundry($laundry)
    {
        // Ambil rata-rata harga layanan laundry
        $layanan = $laundry->layanan;
        if ($layanan->isEmpty()) {
            return 5000; // Default harga jika tidak ada layanan
        }

        return $layanan->avg('harga') ?: 5000;
    }

    /**
     * Get harga laundry berdasarkan kategori layanan
     */
    private function getHargaLaundryByKategori($laundry, $kategoriLayanan)
    {
        // Ambil harga layanan laundry berdasarkan kategori
        $layanan = $laundry->layanan()
            ->where('jenis_layanan', $kategoriLayanan)
            ->first();
        
        if (!$layanan) {
            return 999999; // Return nilai besar jika tidak ada layanan untuk kategori ini
        }

        return $layanan->harga ?: 5000;
    }

    /**
     * Get fasilitas score berdasarkan jumlah fasilitas
     */
    private function getFasilitasScore($laundry)
    {
        $fasilitas = $laundry->fasilitas ? explode(',', $laundry->fasilitas) : [];
        return count($fasilitas);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     * 
     * @param float $lat1 Latitude of first point
     * @param float $lng1 Longitude of first point
     * @param float $lat2 Latitude of second point
     * @param float $lng2 Longitude of second point
     * @return float Distance in kilometers
     */
    private function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Radius bumi dalam kilometer

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c; // Jarak dalam kilometer

        return round($distance, 2); // Bulatkan 2 desimal
    }
}
