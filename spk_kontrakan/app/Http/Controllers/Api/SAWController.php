<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\Kontrakan;
use App\Models\Laundry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class SAWController extends Controller
{
    /**
     * Get kriteria untuk kontrakan
     */
    public function getKriteriaKontrakan()
    {
        $kriteria = Cache::remember('kriteria_kontrakan', 3600, function () {
            return Kriteria::where('tipe_bisnis', 'kontrakan')->get();
        });

        return response()->json([
            'success' => true,
            'data' => $kriteria
        ], 200);
    }

    /**
     * Get kriteria untuk laundry
     */
    public function getKriteriaLaundry()
    {
        $kriteria = Cache::remember('kriteria_laundry', 3600, function () {
            return Kriteria::where('tipe_bisnis', 'laundry')->get();
        });

        return response()->json([
            'success' => true,
            'data' => $kriteria
        ], 200);
    }

    /**
     * Calculate SAW untuk kontrakan
     * Supports custom bobot from mobile (like UserSAWController presets)
     */
    public function calculateKontrakan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'harga_min' => 'nullable|numeric',
            'harga_max' => 'nullable|numeric',
            'jumlah_kamar' => 'nullable|integer',
            'jarak_max' => 'nullable|numeric',
            'fasilitas' => 'nullable|string',
            // Custom bobot support (in percentage, total must be 100)
            'bobot_harga' => 'nullable|integer|min:10|max:70',
            'bobot_jarak' => 'nullable|integer|min:10|max:70',
            'bobot_jumlah_kamar' => 'nullable|integer|min:10|max:70',
            'bobot_fasilitas' => 'nullable|integer|min:10|max:70',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get kriteria
        $kriteria = Cache::remember('kriteria_kontrakan', 3600, function () {
            return Kriteria::where('tipe_bisnis', 'kontrakan')->get();
        });
        
        // Check if custom bobot provided
        $customBobot = null;
        if ($request->filled('bobot_harga') && $request->filled('bobot_jarak') && 
            $request->filled('bobot_jumlah_kamar') && $request->filled('bobot_fasilitas')) {
            $totalBobot = $request->bobot_harga + $request->bobot_jarak + 
                          $request->bobot_jumlah_kamar + $request->bobot_fasilitas;
            if ($totalBobot == 100) {
                $customBobot = [
                    'harga' => $request->bobot_harga / 100,
                    'jarak' => $request->bobot_jarak / 100,
                    'jumlah_kamar' => $request->bobot_jumlah_kamar / 100,
                    'fasilitas_count' => $request->bobot_fasilitas / 100,
                ];
            }
        }

        // Check total available kontrakan first (without extra filters)
        $totalAvailable = Kontrakan::where(function($q) {
            $q->where('status', 'tersedia')
              ->orWhere('status', 'available');
        })->count();

        // Get kontrakan dengan filter
        $query = Kontrakan::where(function($q) {
            $q->where('status', 'tersedia')
              ->orWhere('status', 'available');
        })->with('galeri');

        if ($request->filled('harga_min')) {
            $query->where('harga', '>=', $request->harga_min);
        }
        if ($request->filled('harga_max')) {
            $query->where('harga', '<=', $request->harga_max);
        }
        if ($request->filled('jumlah_kamar')) {
            $query->where('jumlah_kamar', '>=', $request->jumlah_kamar);
        }
        if ($request->filled('jarak_max')) {
            $jarakMeter = $request->jarak_max * 1000;
            $query->where('jarak', '<=', $jarakMeter);
        }
        if ($request->filled('fasilitas')) {
            $query->where('fasilitas', 'like', '%' . $request->fasilitas . '%');
        }

        $kontrakan = $query->get();

        if ($kontrakan->isEmpty()) {
            if ($totalAvailable === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum ada kontrakan yang tersedia saat ini',
                    'no_data' => true,
                ], 404);
            }
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada kontrakan yang memenuhi kriteria yang Anda pilih',
                'no_data' => false,
            ], 404);
        }

        // Proses SAW with optional custom bobot
        $hasil = $this->prosesMetodeSAWKontrakan($kontrakan, $kriteria, $customBobot);

        // Build bobot info for response
        $bobotInfo = [];
        foreach ($kriteria as $k) {
            $key = $k->nama_kriteria;
            $bobotInfo[$key] = [
                'bobot' => $customBobot ? ($customBobot[$key] ?? $k->bobot) : $k->bobot,
                'tipe' => $k->tipe,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kriteria' => $kriteria,
                'bobot_used' => $bobotInfo,
                'custom_bobot' => $customBobot !== null,
                'hasil' => $hasil
            ]
        ], 200);
    }

    /**
     * Calculate SAW untuk laundry
     * Supports custom bobot from mobile
     */
    public function calculateLaundry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_layanan' => 'nullable|string|in:reguler,express,kilat',
            'harga_min' => 'nullable|numeric',
            'harga_max' => 'nullable|numeric',
            'jarak_max' => 'nullable|numeric',
            'rating_min' => 'nullable|numeric|min:0|max:5',
            'user_lat' => 'nullable|numeric',
            'user_lng' => 'nullable|numeric',
            // Custom bobot support
            'bobot_harga' => 'nullable|integer|min:10|max:70',
            'bobot_jarak' => 'nullable|integer|min:10|max:70',
            'bobot_kecepatan' => 'nullable|integer|min:10|max:70',
            'bobot_layanan' => 'nullable|integer|min:10|max:70',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get kriteria
        $kriteria = Cache::remember('kriteria_laundry', 3600, function () {
            return Kriteria::where('tipe_bisnis', 'laundry')->get();
        });
        
        // Check if custom bobot provided
        $customBobot = null;
        if ($request->filled('bobot_harga') && $request->filled('bobot_jarak') && 
            $request->filled('bobot_kecepatan') && $request->filled('bobot_layanan')) {
            $totalBobot = $request->bobot_harga + $request->bobot_jarak + 
                          $request->bobot_kecepatan + $request->bobot_layanan;
            if ($totalBobot == 100) {
                $customBobot = [
                    'harga' => $request->bobot_harga / 100,
                    'jarak' => $request->bobot_jarak / 100,
                    'kecepatan_layanan' => $request->bobot_kecepatan / 100,
                    'layanan' => $request->bobot_layanan / 100,
                ];
            }
        }

        // Get jenis_layanan filter
        $jenisLayanan = $request->input('jenis_layanan', null);

        // Get laundry dengan filter
        $query = Laundry::where('status', 'buka')
            ->with(['galeri', 'layanan', 'reviews']);

        // Filter by jenis_layanan if provided
        if ($jenisLayanan) {
            $query->whereHas('layanan', function($q) use ($jenisLayanan) {
                $q->where('jenis_layanan', $jenisLayanan);
            });
        }

        if ($request->filled('harga_min')) {
            $query->whereHas('layanan', function($q) use ($request) {
                $q->where('harga', '>=', $request->harga_min);
            });
        }
        if ($request->filled('harga_max')) {
            $query->whereHas('layanan', function($q) use ($request) {
                $q->where('harga', '<=', $request->harga_max);
            });
        }
        if ($request->filled('jarak_max')) {
            $jarakMeter = $request->jarak_max * 1000;
            $query->where('jarak', '<=', $jarakMeter);
        }

        $laundry = $query->get();

        if ($laundry->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada laundry yang memenuhi kriteria'
            ], 404);
        }

        // If user location provided, calculate distance from user location
        $useUserLocation = $request->filled('user_lat') && $request->filled('user_lng');
        if ($useUserLocation) {
            $userLat = $request->user_lat;
            $userLng = $request->user_lng;
            
            $laundry = $laundry->map(function($item) use ($userLat, $userLng) {
                if ($item->latitude && $item->longitude) {
                    $distance = $item->calculateDistance($userLat, $userLng);
                    $item->jarak_kampus = $distance;
                    $item->jarak = $distance * 1000;
                }
                return $item;
            });
        }

        // Proses SAW with optional custom bobot and jenis_layanan
        $hasil = $this->prosesMetodeSAWLaundry($laundry, $kriteria, $customBobot, $jenisLayanan);

        // Build bobot info for response
        $bobotInfo = [];
        foreach ($kriteria as $k) {
            $key = $k->nama_kriteria;
            $bobotInfo[$key] = [
                'bobot' => $customBobot ? ($customBobot[$key] ?? $k->bobot) : $k->bobot,
                'tipe' => $k->tipe,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kriteria' => $kriteria,
                'bobot_used' => $bobotInfo,
                'custom_bobot' => $customBobot !== null,
                'hasil' => $hasil,
                'jenis_layanan' => $jenisLayanan,
                'location_source' => $useUserLocation ? 'user' : 'kampus'
            ]
        ], 200);
    }

    /**
     * Proses metode SAW untuk Kontrakan
     * @param $items - Collection of Kontrakan
     * @param $kriteria - Collection of Kriteria
     * @param $customBobot - Optional custom bobot array (key => decimal value)
     */
    private function prosesMetodeSAWKontrakan($items, $kriteria, $customBobot = null)
    {
        $data = [];
        
        // Get min/max values for normalization
        $maxValues = [];
        $minValues = [];
        
        foreach ($kriteria as $k) {
            $field = $k->nama_kriteria;
            
            if ($field === 'fasilitas_count') {
                $values = $items->map(function($item) {
                    $fasilitas = $item->fasilitas ?? '';
                    return count(array_filter(explode(',', $fasilitas)));
                })->toArray();
                $maxValues[$field] = max($values ?: [1]);
                $minValues[$field] = min($values ?: [1]);
            } else {
                $values = $items->pluck($field)->filter()->toArray();
                $maxValues[$field] = !empty($values) ? max($values) : 1;
                $minValues[$field] = !empty($values) ? min($values) : 1;
            }
        }

        // Process each item
        foreach ($items as $item) {
            $row = [
                'id' => $item->id,
                'nama' => $item->nama,
                'nilai' => [],
                'normalisasi' => [],
            ];

            foreach ($kriteria as $k) {
                $field = $k->nama_kriteria;
                
                // Get nilai
                if ($field === 'fasilitas_count') {
                    $nilai = count(array_filter(explode(',', $item->fasilitas ?? '')));
                } else {
                    $nilai = $item->{$field} ?? 0;
                }
                
                $row['nilai'][$field] = $nilai;

                // Normalisasi based on tipe (Benefit/Cost)
                if (strtolower($k->tipe) === 'benefit') {
                    $maxVal = $maxValues[$field] ?: 1;
                    $row['normalisasi'][$field] = $nilai / $maxVal;
                } else { // Cost
                    $minVal = $minValues[$field] ?: 1;
                    $row['normalisasi'][$field] = $nilai > 0 ? $minVal / $nilai : 0;
                }
            }

            // Hitung skor total (use custom bobot if provided)
            $skor = 0;
            foreach ($kriteria as $k) {
                $field = $k->nama_kriteria;
                $bobot = ($customBobot && isset($customBobot[$field])) ? $customBobot[$field] : $k->bobot;
                $skor += ($row['normalisasi'][$field] ?? 0) * $bobot;
            }
            $row['skor'] = $skor;
            
            // Add full item data for mobile app
            $row['data'] = $item;

            $data[] = $row;
        }

        // Sort by skor descending
        usort($data, function($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        // Add ranking
        foreach ($data as $i => &$row) {
            $row['ranking'] = $i + 1;
        }

        return $data;
    }

    /**
     * Proses metode SAW untuk Laundry
     * @param $items - Collection of Laundry
     * @param $kriteria - Collection of Kriteria
     * @param $customBobot - Optional custom bobot array (key => decimal value)
     */
    private function prosesMetodeSAWLaundry($items, $kriteria, $customBobot = null, $jenisLayanan = null)
    {
        $data = [];
        
        // Get min/max values for normalization
        $maxValues = [];
        $minValues = [];
        
        foreach ($kriteria as $k) {
            $field = $k->nama_kriteria;
            $values = [];
            
            foreach ($items as $item) {
                $nilai = $this->getNilaiLaundry($item, $field, $jenisLayanan);
                $values[] = $nilai > 0 ? $nilai : 0.01;
            }
            
            $maxValues[$field] = max($values ?: [1]);
            $minValues[$field] = min($values ?: [1]);
        }

        // Process each item
        foreach ($items as $item) {
            $row = [
                'id' => $item->id,
                'nama' => $item->nama,
                'nilai' => [],
                'normalisasi' => [],
            ];

            foreach ($kriteria as $k) {
                $field = $k->nama_kriteria;
                $nilai = $this->getNilaiLaundry($item, $field, $jenisLayanan);
                
                $row['nilai'][$field] = $nilai;

                // Normalisasi based on tipe (Benefit/Cost)
                if (strtolower($k->tipe) === 'benefit') {
                    $maxVal = $maxValues[$field] ?: 1;
                    $row['normalisasi'][$field] = $nilai / $maxVal;
                } else { // Cost
                    $minVal = $minValues[$field] ?: 1;
                    $row['normalisasi'][$field] = $nilai > 0 ? $minVal / $nilai : 0;
                }
            }

            // Hitung skor total (use custom bobot if provided)
            $skor = 0;
            foreach ($kriteria as $k) {
                $field = $k->nama_kriteria;
                $bobot = ($customBobot && isset($customBobot[$field])) ? $customBobot[$field] : $k->bobot;
                $skor += ($row['normalisasi'][$field] ?? 0) * $bobot;
            }
            $row['skor'] = round($skor, 6);
            $row['skor_akhir'] = round($skor, 4);
            
            // Add full item data for mobile app
            $row['data'] = $item;

            $data[] = $row;
        }

        // Sort by skor descending
        usort($data, function($a, $b) {
            return $b['skor'] <=> $a['skor'];
        });

        // Add ranking
        foreach ($data as $i => &$row) {
            $row['ranking'] = $i + 1;
        }

        return $data;
    }

    /**
     * Helper: Get nilai laundry berdasarkan nama kriteria
     */
    private function getNilaiLaundry($item, $field, $jenisLayanan = null)
    {
        // Filter layanan by jenis if specified
        $layananCollection = $jenisLayanan 
            ? $item->layanan->where('jenis_layanan', $jenisLayanan)
            : $item->layanan;

        switch ($field) {
            case 'harga':
                // Get harga from filtered layanan
                return $layananCollection->min('harga') ?? ($item->layanan->min('harga') ?? 0);
            
            case 'kecepatan_layanan':
            case 'waktu_proses':
                // Kecepatan layanan = waktu proses (jam)
                $waktu = $layananCollection->avg('waktu_proses');
                if ($waktu === null || $waktu == 0) {
                    $waktu = $layananCollection->avg('estimasi_selesai') ?? 24;
                }
                if ($waktu === null || $waktu == 0) {
                    $waktu = $item->layanan->avg('waktu_proses') ?? 24;
                }
                return $waktu > 0 ? $waktu : 24;
            
            case 'layanan':
                // Jumlah variasi layanan yang tersedia
                return $item->layanan->count() ?: 1;
            
            case 'rating':
                return $item->reviews->avg('rating') ?? 0;
            
            case 'jarak':
                return $item->jarak ?? 0;
            
            default:
                // Safely handle - avoid returning collections
                $value = $item->{$field} ?? 0;
                return is_numeric($value) ? $value : 0;
        }
    }
}
