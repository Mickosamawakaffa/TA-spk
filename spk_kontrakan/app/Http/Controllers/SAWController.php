<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kontrakan;
use App\Models\Laundry;
use App\Models\Kriteria;
use Illuminate\Support\Facades\Log;
use Exception;

class SAWController extends Controller
{
    // Koordinat Kampus Polije (FIXED)
    const KAMPUS_LAT = -8.15981;  // Ganti dengan koordinat kampus Polije yang sebenarnya
    const KAMPUS_LNG = 113.72312; // Ganti dengan koordinat kampus Polije yang sebenarnya
    
    public function index()
    {
        try {
            $kriteria = Kriteria::all();
            
            if ($kriteria->isEmpty()) {
                return view('saw.index')->with('error', 'Data kriteria belum tersedia. Hubungi administrator.');
            }
            
            return view('saw.index', compact('kriteria'));
            
        } catch (Exception $e) {
            Log::error('Error di SAW Index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat halaman. Silakan coba lagi.');
        }
    }

    // Halaman untuk setting bobot kriteria
    public function bobot()
    {
        try {
            $kriteriaKontrakan = Kriteria::where('tipe_bisnis', 'kontrakan')->get();
            $kriteriaLaundry = Kriteria::where('tipe_bisnis', 'laundry')->get();
            
            return view('SAW.bobot', compact('kriteriaKontrakan', 'kriteriaLaundry'));
            
        } catch (Exception $e) {
            Log::error('Error di SAW Bobot: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    public function proses(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'tipe' => 'required|in:kontrakan,laundry',
                'jenis_layanan' => 'required_if:tipe,laundry',
            ], [
                'tipe.required' => 'Pilih tipe bisnis terlebih dahulu!',
                'tipe.in' => 'Tipe bisnis tidak valid!',
                'jenis_layanan.required_if' => 'Pilih jenis layanan untuk laundry!',
            ]);
            
            $tipe = $request->tipe;
            $jenisLayanan = $request->jenis_layanan ?? null;
            
            // Default: gunakan koordinat KAMPUS
            $userLat = self::KAMPUS_LAT;
            $userLng = self::KAMPUS_LNG;
            $referencePoint = 'Kampus Polije';
            
            // Untuk LAUNDRY: cek apakah user memilih "dari lokasi saya" dan sudah deteksi lokasi
            if ($tipe == 'laundry') {
                $referensiJarak = $request->input('referensi_jarak', 'kampus');
                
                if ($referensiJarak == 'user' && $request->filled('user_lat') && $request->filled('user_lng')) {
                    // User memilih "dari lokasi saya" DAN sudah deteksi lokasi
                    $userLat = floatval($request->user_lat);
                    $userLng = floatval($request->user_lng);
                    $referencePoint = 'Lokasi Anda';
                    
                    Log::info('Laundry: Menggunakan lokasi USER', [
                        'lat' => $userLat,
                        'lng' => $userLng
                    ]);
                } else {
                    // Default atau user pilih "dari kampus" atau belum deteksi lokasi
                    Log::info('Laundry: Menggunakan lokasi KAMPUS (default)');
                }
            }
            
            Log::info('SAW Input:', [
                'tipe' => $tipe,
                'jenis_layanan' => $jenisLayanan,
                'reference_lat' => $userLat,
                'reference_lng' => $userLng,
                'reference_point' => $referencePoint,
            ]);
            
            // Ambil kriteria
            $kriteria = Kriteria::where('tipe_bisnis', $tipe)->get();
            
            if ($kriteria->isEmpty()) {
                return redirect()->route('saw.index')
                    ->with('error', 'Kriteria untuk ' . $tipe . ' belum tersedia!');
            }
            
            // Validasi total bobot harus = 1
            $totalBobot = $kriteria->sum('bobot');
            if (abs($totalBobot - 1) > 0.01) {
                Log::warning('Total bobot kriteria tidak = 1: ' . $totalBobot);
                return redirect()->route('saw.index')
                    ->with('error', 'Konfigurasi bobot kriteria tidak valid (total: ' . $totalBobot . '). Hubungi administrator.');
            }
            
            // Ambil data
            $data = $this->getData($tipe, $jenisLayanan);
            
            if ($data->isEmpty()) {
                return redirect()->route('saw.index')
                    ->with('error', 'Tidak ada data ' . $tipe . ' yang tersedia' . 
                           ($jenisLayanan ? ' untuk layanan ' . $jenisLayanan : '') . '!');
            }
            
            // Proses data dengan fasilitas/layanan dan hitung jarak
            $dataWithFasilitas = $this->processData($data, $tipe, $jenisLayanan, $userLat, $userLng);
            
            if ($dataWithFasilitas->isEmpty()) {
                return redirect()->route('saw.index')
                    ->with('error', 'Tidak ada data yang valid untuk diproses!');
            }
            
            // Hitung max/min
            $maxMin = $this->calculateMaxMin($dataWithFasilitas, $tipe);
            
            // Validasi max/min tidak nol
            foreach ($maxMin as $key => $values) {
                if ($values['max'] == 0 && $values['min'] == 0) {
                    Log::warning("Max/Min untuk {$key} adalah 0");
                }
            }
            
            Log::info('Max/Min Values:', $maxMin);
            
            // Normalisasi dan hitung
            $hasil = $this->calculateSAW($dataWithFasilitas, $kriteria, $maxMin, $tipe);
            
            if (empty($hasil)) {
                return redirect()->route('saw.index')
                    ->with('error', 'Gagal menghitung hasil. Silakan coba lagi.');
            }
            
            // Kirim info reference point ke view
            $referencePoint = $referencePoint; // sudah didefinisikan di atas
            
            return view('saw.hasil', compact('hasil', 'kriteria', 'tipe', 'jenisLayanan', 'userLat', 'userLng', 'referencePoint'));
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('saw.index')
                ->withErrors($e->validator)
                ->withInput();
                
        } catch (Exception $e) {
            Log::error('Error di SAW Proses: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('saw.index')
                ->with('error', 'Terjadi kesalahan saat memproses data: ' . $e->getMessage());
        }
    }
    
    // Ambil data sesuai tipe
    private function getData($tipe, $jenisLayanan)
    {
        try {
            if ($tipe == 'kontrakan') {
                return Kontrakan::all();
            }
            
            $data = Laundry::with('layanan')->get();
            
            return $data->filter(function($laundry) use ($jenisLayanan) {
                return $laundry->layanan->where('jenis_layanan', $jenisLayanan)->isNotEmpty();
            });
            
        } catch (Exception $e) {
            Log::error('Error getData: ' . $e->getMessage());
            throw new Exception('Gagal mengambil data dari database');
        }
    }
    
    // Proses data dengan hitung fasilitas/jarak
    private function processData($data, $tipe, $jenisLayanan, $refLat, $refLng)
    {
        return $data->map(function($item) use ($tipe, $jenisLayanan, $refLat, $refLng) {
            try {
                if ($tipe == 'kontrakan') {
                    $item->jumlah_fasilitas = $item->fasilitas ? count(explode(',', $item->fasilitas)) : 0;
                    $item->harga_value = $item->harga ?? 0;
                    
                    // KONTRAKAN: Hitung jarak dari KAMPUS POLIJE
                    if ($item->latitude && $item->longitude) {
                        try {
                            $item->jarak_value = $item->calculateDistance($refLat, $refLng) * 1000; // ke meter
                        } catch (Exception $e) {
                            Log::warning("Gagal hitung jarak untuk kontrakan {$item->nama}: " . $e->getMessage());
                            $item->jarak_value = $item->jarak ?? 0;
                        }
                    } else {
                        $item->jarak_value = $item->jarak ?? 0;
                    }
                    
                    $item->latitude_value = $item->latitude;
                    $item->longitude_value = $item->longitude;
                    
                } else {
                    // LAUNDRY
                    $layanan = $item->layanan->where('jenis_layanan', $jenisLayanan)->first();
                    
                    if (!$layanan) {
                        return null;
                    }
                    
                    $item->harga_value = $layanan->harga ?? 0;
                    $item->kecepatan_value = $this->convertToHours(
                        $layanan->kecepatan ?? 0, 
                        $layanan->satuan_kecepatan ?? 'jam'
                    );
                    
                    // LAUNDRY: Hitung jarak dari LOKASI USER
                    if ($item->latitude && $item->longitude) {
                        try {
                            $item->jarak_value = $item->calculateDistance($refLat, $refLng) * 1000; // ke meter
                        } catch (Exception $e) {
                            Log::warning("Gagal hitung jarak untuk laundry {$item->nama}: " . $e->getMessage());
                            $item->jarak_value = $item->jarak ?? 0;
                        }
                    } else {
                        $item->jarak_value = $item->jarak ?? 0;
                    }
                    
                    $item->jumlah_fasilitas = $item->layanan->count();
                    $item->latitude_value = $item->latitude;
                    $item->longitude_value = $item->longitude;
                }
                
                return $item;
                
            } catch (Exception $e) {
                Log::error("Error processing item {$item->id}: " . $e->getMessage());
                return null;
            }
        })->filter();
    }
    
    // Hitung max/min untuk setiap kriteria
    private function calculateMaxMin($dataWithFasilitas, $tipe)
    {
        $maxMin = [];
        
        $maxMin['harga'] = [
            'max' => $dataWithFasilitas->max('harga_value') ?: 1,
            'min' => $dataWithFasilitas->min('harga_value') ?: 1,
        ];
        
        $maxMin['jarak'] = [
            'max' => $dataWithFasilitas->max('jarak_value') ?: 1,
            'min' => $dataWithFasilitas->min('jarak_value') ?: 1,
        ];
        
        if ($tipe == 'kontrakan') {
            $maxMin['jumlah_kamar'] = [
                'max' => $dataWithFasilitas->max('jumlah_kamar') ?: 1,
                'min' => $dataWithFasilitas->min('jumlah_kamar') ?: 1,
            ];
        }
        
        if ($tipe == 'laundry') {
            $maxMin['kecepatan'] = [
                'max' => $dataWithFasilitas->max('kecepatan_value') ?: 1,
                'min' => $dataWithFasilitas->min('kecepatan_value') ?: 1,
            ];
        }
        
        $maxMin['fasilitas'] = [
            'max' => $dataWithFasilitas->max('jumlah_fasilitas') ?: 1,
            'min' => $dataWithFasilitas->min('jumlah_fasilitas') ?: 1,
        ];
        
        return $maxMin;
    }
    
    // Hitung SAW
    private function calculateSAW($dataWithFasilitas, $kriteria, $maxMin, $tipe)
    {
        $hasil = [];
        
        foreach ($dataWithFasilitas as $item) {
            try {
                $normalisasi = [];
                $nilaiTotal = 0;
                
                foreach ($kriteria as $krit) {
                    $namaKrit = strtolower($krit->nama_kriteria);
                    $bobotKrit = $krit->bobot;
                    $tipeKrit = strtolower($krit->tipe);
                    
                    $nilaiAsli = 0;
                    $nilaiNormalisasi = 0;
                    
                    // Mapping kriteria
                    if (strpos($namaKrit, 'harga') !== false) {
                        $nilaiAsli = $item->harga_value;
                        $nilaiNormalisasi = $this->normalize($nilaiAsli, $maxMin['harga'], $tipeKrit);
                        
                    } elseif (strpos($namaKrit, 'jarak') !== false) {
                        $nilaiAsli = $item->jarak_value;
                        $nilaiNormalisasi = $this->normalize($nilaiAsli, $maxMin['jarak'], $tipeKrit);
                        
                    } elseif ((strpos($namaKrit, 'jumlah_kamar') !== false || strpos($namaKrit, 'jumlah kamar') !== false || strpos($namaKrit, 'luas') !== false) && $tipe == 'kontrakan') {
                        // Support backward compatibility dengan 'luas' tapi gunakan jumlah_kamar
                        $nilaiAsli = $item->jumlah_kamar ?? 0;
                        $nilaiNormalisasi = $this->normalize($nilaiAsli, $maxMin['jumlah_kamar'], $tipeKrit);
                        
                    } elseif (strpos($namaKrit, 'fasilitas') !== false || strpos($namaKrit, 'layanan') !== false) {
                        $nilaiAsli = $item->jumlah_fasilitas;
                        $nilaiNormalisasi = $this->normalize($nilaiAsli, $maxMin['fasilitas'], $tipeKrit);
                        
                    } elseif (strpos($namaKrit, 'kecepatan') !== false && $tipe == 'laundry') {
                        $nilaiAsli = $item->kecepatan_value;
                        $nilaiNormalisasi = $this->normalize($nilaiAsli, $maxMin['kecepatan'], $tipeKrit);
                    }
                    
                    $normalisasi[$krit->nama_kriteria] = [
                        'asli' => $nilaiAsli,
                        'normalisasi' => round($nilaiNormalisasi, 4),
                        'bobot' => $bobotKrit,
                    ];
                    
                    $nilaiTotal += ($nilaiNormalisasi * $bobotKrit);
                }
                
                $hasil[] = [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'alamat' => $item->alamat,
                    'latitude' => $item->latitude_value,
                    'longitude' => $item->longitude_value,
                    'no_whatsapp' => $item->no_whatsapp ?? null,
                    'normalisasi' => $normalisasi,
                    'nilai' => round($nilaiTotal, 4),
                ];
                
            } catch (Exception $e) {
                Log::error("Error calculating SAW for item {$item->id}: " . $e->getMessage());
                continue;
            }
        }
        
        // Sort hasil
        usort($hasil, fn($a, $b) => $b['nilai'] <=> $a['nilai']);
        
        // Tambahkan ranking
        foreach ($hasil as $key => $value) {
            $hasil[$key]['ranking'] = $key + 1;
        }
        
        return $hasil;
    }
    
    // Helper normalisasi dengan error handling
    private function normalize($nilai, $maxMin, $tipe)
    {
        try {
            // Cegah division by zero
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
    
    // Convert kecepatan ke jam
    private function convertToHours($kecepatan, $satuan)
    {
        try {
            $kecepatan = floatval($kecepatan);
            
            if ($satuan == 'hari') {
                return $kecepatan * 24;
            }
            return $kecepatan;
            
        } catch (Exception $e) {
            Log::error('Error convertToHours: ' . $e->getMessage());
            return 0;
        }
    }
}