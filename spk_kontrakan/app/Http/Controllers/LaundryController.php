<?php

namespace App\Http\Controllers;

use App\Models\Laundry;
use App\Models\LayananLaundry;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class LaundryController extends Controller
{
    // Koordinat Kampus Polije (FIXED)
    const KAMPUS_LAT = -8.15981;
    const KAMPUS_LNG = 113.72312;
    
    /**
     * Display a listing of laundry with ADVANCED filters and sorting (SEPERTI KONTRAKAN)
     */
    public function index(Request $request)
    {
        // Query builder
        $query = Laundry::with('layanan');
        
        // ========== FILTER: SEARCH (Nama, Alamat, Fasilitas) ==========
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('alamat', 'LIKE', "%{$search}%")
                  ->orWhere('fasilitas', 'LIKE', "%{$search}%");
            });
        }
        
        // ========== FILTER: RANGE HARGA (Min & Max dengan Slider) ==========
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
        
        // ========== FILTER: JARAK MAKSIMAL (Slider dalam KM) ==========
        if ($request->filled('jarak_max')) {
            // Konversi km ke meter (karena jarak di DB dalam meter)
            $jarakMeter = $request->jarak_max * 1000;
            $query->where('jarak', '<=', $jarakMeter);
        }
        
        // ========== FILTER: JENIS LAYANAN (Multiple Checkbox) ==========
        if ($request->filled('jenis_layanan_filter')) {
            $jenisLayananArray = $request->jenis_layanan_filter;
            
            $query->whereHas('layanan', function($q) use ($jenisLayananArray) {
                $q->whereIn('jenis_layanan', $jenisLayananArray);
            });
        }
        
        // ========== FILTER: FASILITAS (Multiple Checkbox) ==========
        if ($request->filled('fasilitas_filter')) {
            $fasilitasArray = $request->fasilitas_filter;
            
            foreach ($fasilitasArray as $fasilitas) {
                // Cari laundry yang fasilitasnya mengandung kata kunci ini
                $query->where('fasilitas', 'LIKE', "%{$fasilitas}%");
            }
        }
        
        // ========== SORTING ==========
        $sortBy = $request->get('sort_by', 'terbaru'); // Default: terbaru
        
        switch ($sortBy) {
            case 'nama_asc':
                $query->orderBy('nama', 'ASC');
                break;
            case 'nama_desc':
                $query->orderBy('nama', 'DESC');
                break;
            case 'harga_termurah':
                // Sort by minimum price from layanan
                $query->leftJoin('layanan_laundry', 'laundry.id', '=', 'layanan_laundry.laundry_id')
                      ->select('laundry.*', DB::raw('MIN(layanan_laundry.harga) as min_harga'))
                      ->groupBy('laundry.id')
                      ->orderBy('min_harga', 'ASC');
                break;
            case 'harga_termahal':
                // Sort by maximum price from layanan
                $query->leftJoin('layanan_laundry', 'laundry.id', '=', 'layanan_laundry.laundry_id')
                      ->select('laundry.*', DB::raw('MAX(layanan_laundry.harga) as max_harga'))
                      ->groupBy('laundry.id')
                      ->orderBy('max_harga', 'DESC');
                break;
            case 'jarak_terdekat':
                $query->orderBy('jarak', 'ASC');
                break;
            case 'jarak_terjauh':
                $query->orderBy('jarak', 'DESC');
                break;
            case 'terlama':
                $query->orderBy('created_at', 'ASC');
                break;
            case 'terbaru':
            default:
                $query->orderBy('created_at', 'DESC');
                break;
        }
        
        // Get results dengan pagination
        $laundry = $query->paginate(12)->withQueryString(); // 12 items per halaman (sama seperti kontrakan)
        
        // ========== STATISTIK & DATA UNTUK FILTER UI ==========
        
        // Total laundry (semua data, tanpa filter)
        $totalLaundry = Laundry::count();
        
        // Jumlah hasil setelah filter
        $filteredCount = $laundry->total();
        
        // Range harga untuk slider (min & max dari database layanan)
        $hargaMin = LayananLaundry::min('harga') ?? 0;
        $hargaMax = LayananLaundry::max('harga') ?? 100000;
        
        // Range jarak untuk slider (dalam km)
        $jarakMaxDb = Laundry::max('jarak') ?? 10000; // dalam meter
        $jarakMaxKm = ceil($jarakMaxDb / 1000); // konversi ke km, bulatkan ke atas
        
        // Daftar jenis layanan unik untuk checkbox
        $jenisLayananUnique = LayananLaundry::distinct('jenis_layanan')
            ->pluck('jenis_layanan')
            ->filter()
            ->sort()
            ->map(function($item) {
                return ucfirst($item); // Kapitalisasi huruf pertama
            })
            ->values();
        
        // Daftar fasilitas unik untuk checkbox (extract dari semua laundry)
        $allFasilitas = Laundry::pluck('fasilitas')->filter();
        $fasilitasUnique = collect();

        foreach ($allFasilitas as $fasilitasString) {
            // Split by comma dan trim whitespace
            $items = array_map('trim', explode(',', $fasilitasString));
            foreach ($items as $item) {
                if (!empty($item)) {
                    // Normalisasi ke lowercase untuk menghindari duplikat
                    $fasilitasUnique->push(strtolower($item));
                }
            }
        }

        // Remove duplicates (case-insensitive), sort alphabetically, capitalize first letter
        $fasilitasUnique = $fasilitasUnique->unique()
            ->sort()
            ->map(function($item) {
                return ucfirst($item); // Kapitalisasi huruf pertama untuk tampilan
            })
            ->values();
        
        // Kirim data filter ke view untuk maintain state
        $filters = [
            'search' => $request->search,
            'harga_min' => $request->harga_min ?? $hargaMin,
            'harga_max' => $request->harga_max ?? $hargaMax,
            'jarak_max' => $request->jarak_max,
            'jenis_layanan_filter' => $request->jenis_layanan_filter ?? [],
            'fasilitas_filter' => $request->fasilitas_filter ?? [],
            'sort_by' => $sortBy,
        ];
        
        return view('laundry.index', compact(
            'laundry',
            'filters',
            'totalLaundry',
            'filteredCount',
            'hargaMin',
            'hargaMax',
            'jarakMaxKm',
            'jenisLayananUnique',
            'fasilitasUnique'
        ));
    }

    /**
     * Tampilkan semua laundry di peta
     */
    public function map(Request $request)
    {
        try {
            // Validasi input filter
            $request->validate([
                'search' => 'nullable|string|max:255',
                'harga_min' => 'nullable|numeric|min:0',
                'harga_max' => 'nullable|numeric|min:0|gte:harga_min',
                'jarak' => 'nullable|in:dekat,sedang,jauh',
                'jenis_layanan' => 'nullable|in:express,reguler,kilat',
            ]);
            
            $query = Laundry::with('layanan');
            
            // ========== FILTER SEARCH ==========
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                      ->orWhere('alamat', 'LIKE', "%{$search}%");
                });
            }
            
            // ========== FILTER HARGA ==========
            if ($request->filled('harga_min') || $request->filled('harga_max')) {
                $query->whereHas('layanan', function($q) use ($request) {
                    if ($request->filled('harga_min')) {
                        $q->where('harga', '>=', $request->harga_min);
                    }
                    if ($request->filled('harga_max')) {
                        $q->where('harga', '<=', $request->harga_max);
                    }
                });
            }
            
            // ========== FILTER JARAK ==========
            if ($request->filled('jarak')) {
                switch ($request->jarak) {
                    case 'dekat':
                        $query->where('jarak', '<', 500);
                        break;
                    case 'sedang':
                        $query->whereBetween('jarak', [500, 1000]);
                        break;
                    case 'jauh':
                        $query->where('jarak', '>', 1000);
                        break;
                }
            }
            
            // ========== FILTER JENIS LAYANAN ==========
            if ($request->filled('jenis_layanan')) {
                $query->whereHas('layanan', function($q) use ($request) {
                    $q->where('jenis_layanan', $request->jenis_layanan);
                });
            }
            
            $laundry = $query->get();
            
            $filters = [
                'search' => $request->search,
                'harga_min' => $request->harga_min,
                'harga_max' => $request->harga_max,
                'jarak' => $request->jarak,
                'jenis_layanan' => $request->jenis_layanan,
            ];
            
            return view('laundry.map', compact('laundry', 'filters'));
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
                
        } catch (Exception $e) {
            Log::error('Error di Laundry Map: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memuat peta.');
        }
    }

    /**
     * Show the form for creating a new laundry
     */
    public function create()
    {
        try {
            return view('laundry.create');
        } catch (Exception $e) {
            Log::error('Error di Laundry Create: ' . $e->getMessage());
            return redirect()->route('laundry.index')
                ->with('error', 'Tidak dapat membuka form tambah data.');
        }
    }

    /**
     * Store a newly created laundry in storage
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Validasi input lengkap
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'alamat' => 'required|string|max:500',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'fasilitas' => 'nullable|string|max:1000',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                
                // Validasi Layanan
                'layanan' => 'required|array|min:1',
                'layanan.*.jenis_layanan' => 'required|in:express,reguler,kilat',
                'layanan.*.nama_paket' => 'required|string|max:255',
                'layanan.*.harga' => 'required|numeric|min:0',
                'layanan.*.estimasi_selesai' => 'required|numeric|min:1',
                'layanan.*.deskripsi' => 'nullable|string|max:1000',
                'layanan.*.status' => 'required|in:aktif,nonaktif',
            ], [
                'nama.required' => 'Nama laundry harus diisi',
                'alamat.required' => 'Alamat harus diisi',
                'latitude.required' => 'Koordinat latitude harus diisi (klik pada peta)',
                'latitude.between' => 'Koordinat latitude tidak valid',
                'longitude.required' => 'Koordinat longitude harus diisi (klik pada peta)',
                'longitude.between' => 'Koordinat longitude tidak valid',
                'foto.image' => 'File harus berupa gambar',
                'foto.mimes' => 'Format foto harus: jpeg, png, jpg, atau webp',
                'foto.max' => 'Ukuran foto maksimal 2MB',
                'layanan.required' => 'Minimal harus ada 1 jenis layanan',
                'layanan.min' => 'Minimal harus ada 1 jenis layanan',
                'layanan.*.jenis_layanan.required' => 'Jenis layanan harus dipilih',
                'layanan.*.jenis_layanan.in' => 'Jenis layanan harus express, reguler, atau kilat',
                'layanan.*.nama_paket.required' => 'Nama paket harus diisi',
                'layanan.*.harga.required' => 'Harga layanan harus diisi',
                'layanan.*.harga.min' => 'Harga tidak boleh negatif',
                'layanan.*.estimasi_selesai.required' => 'Estimasi waktu selesai harus diisi (jam)',
                'layanan.*.estimasi_selesai.min' => 'Estimasi minimal 1 jam',
                'layanan.*.status.required' => 'Status layanan harus dipilih',
                'layanan.*.status.in' => 'Status harus aktif atau nonaktif',
            ]);

            // Validasi tidak ada duplikasi jenis layanan
            $jenisLayanan = array_column($request->layanan, 'jenis_layanan');
            if (count($jenisLayanan) !== count(array_unique($jenisLayanan))) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tidak boleh ada jenis layanan yang sama!');
            }

            // Proses Upload Foto
            $filename = null;
            if ($request->hasFile('foto')) {
                try {
                    $file = $request->file('foto');
                    
                    if (!$file->isValid()) {
                        throw new Exception('File tidak valid atau corrupt');
                    }
                    
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    
                    $uploadPath = public_path('uploads/Laundry');
                    if (!File::exists($uploadPath)) {
                        File::makeDirectory($uploadPath, 0755, true);
                    }
                    
                    $file->move($uploadPath, $filename);
                    
                } catch (Exception $e) {
                    Log::error('Error upload foto laundry: ' . $e->getMessage());
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gagal mengupload foto: ' . $e->getMessage());
                }
            }

            // Hitung jarak otomatis dari kampus (seperti kontrakan)
            $jarakKm = 0;
            if ($validated['latitude'] && $validated['longitude']) {
                try {
                    // Menggunakan method calculateDistance dari model
                    $tempLaundry = new Laundry();
                    $tempLaundry->latitude = $validated['latitude'];
                    $tempLaundry->longitude = $validated['longitude'];
                    
                    $jarakKm = $tempLaundry->calculateDistance(self::KAMPUS_LAT, self::KAMPUS_LNG);
                    Log::info("Jarak laundry '{$validated['nama']}' dari kampus: {$jarakKm} km");
                } catch (Exception $e) {
                    Log::warning("Gagal menghitung jarak untuk laundry '{$validated['nama']}': " . $e->getMessage());
                    $jarakKm = 0;
                }
            }

            // Simpan Laundry dengan jarak yang sudah dihitung
            $laundry = Laundry::create([
                'nama' => $validated['nama'],
                'alamat' => $validated['alamat'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'jarak' => round($jarakKm * 1000), // Simpan dalam meter (seperti kontrakan)
                'fasilitas' => $request->fasilitas,
                'foto' => $filename,
            ]);

            // Simpan Layanan
            foreach ($request->layanan as $layananData) {
                $laundry->layanan()->create([
                    'jenis_layanan' => $layananData['jenis_layanan'],
                    'nama_paket' => $layananData['nama_paket'],
                    'harga' => $layananData['harga'],
                    'estimasi_selesai' => $layananData['estimasi_selesai'],
                    'deskripsi' => $layananData['deskripsi'] ?? null,
                    'status' => $layananData['status'] ?? 'aktif',
                ]);
            }

            DB::commit();
            
            // Log activity
            ActivityLog::log('create', "Membuat laundry baru: {$laundry->nama}", 'Laundry', $laundry->id);
            
            return redirect()->route('laundry.index')
                ->with('success', 'Data laundry berhasil ditambahkan!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            
            // Hapus foto jika ada error
            if (isset($filename) && File::exists(public_path('uploads/Laundry/' . $filename))) {
                File::delete(public_path('uploads/Laundry/' . $filename));
            }
            
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
                
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error di Laundry Store: ' . $e->getMessage());
            
            // Hapus foto jika ada error
            if (isset($filename) && File::exists(public_path('uploads/Laundry/' . $filename))) {
                File::delete(public_path('uploads/Laundry/' . $filename));
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified laundry
     */
    public function show(Laundry $laundry)
    {
        try {
            $laundry->load('layanan');
            return view('laundry.show', compact('laundry'));
        } catch (Exception $e) {
            Log::error('Error di Laundry Show: ' . $e->getMessage());
            return redirect()->route('laundry.index')
                ->with('error', 'Data tidak dapat ditampilkan.');
        }
    }

    /**
     * Show the form for editing the specified laundry
     */
    public function edit(Laundry $laundry)
    {
        try {
            $laundry->load('layanan');
            return view('laundry.edit', compact('laundry'));
        } catch (Exception $e) {
            Log::error('Error di Laundry Edit: ' . $e->getMessage());
            return redirect()->route('laundry.index')
                ->with('error', 'Tidak dapat membuka form edit.');
        }
    }

    /**
     * Update the specified laundry in storage
     */
    public function update(Request $request, Laundry $laundry)
    {
        DB::beginTransaction();
        
        try {
            // Validasi input
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'alamat' => 'required|string|max:500',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'fasilitas' => 'nullable|string|max:1000',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'hapus_foto' => 'nullable|in:0,1',
                
                // Validasi Layanan
                'layanan' => 'required|array|min:1',
                'layanan.*.jenis_layanan' => 'required|in:express,reguler,kilat',
                'layanan.*.nama_paket' => 'required|string|max:255',
                'layanan.*.harga' => 'required|numeric|min:0',
                'layanan.*.estimasi_selesai' => 'required|numeric|min:1',
                'layanan.*.deskripsi' => 'nullable|string|max:1000',
                'layanan.*.status' => 'required|in:aktif,nonaktif',
            ], [
                'nama.required' => 'Nama laundry harus diisi',
                'alamat.required' => 'Alamat harus diisi',
                'latitude.required' => 'Koordinat latitude harus diisi (klik pada peta)',
                'longitude.required' => 'Koordinat longitude harus diisi (klik pada peta)',
                'foto.max' => 'Ukuran foto maksimal 2MB',
                'layanan.required' => 'Minimal harus ada 1 jenis layanan',
                'layanan.min' => 'Minimal harus ada 1 jenis layanan',
                'layanan.*.jenis_layanan.required' => 'Jenis layanan harus dipilih',
                'layanan.*.jenis_layanan.in' => 'Jenis layanan harus express, reguler, atau kilat',
                'layanan.*.nama_paket.required' => 'Nama paket harus diisi',
                'layanan.*.harga.required' => 'Harga layanan harus diisi',
                'layanan.*.harga.min' => 'Harga tidak boleh negatif',
                'layanan.*.estimasi_selesai.required' => 'Estimasi waktu selesai harus diisi (jam)',
                'layanan.*.estimasi_selesai.min' => 'Estimasi minimal 1 jam',
                'layanan.*.status.required' => 'Status layanan harus dipilih',
                'layanan.*.status.in' => 'Status harus aktif atau nonaktif',
            ]);

            // Validasi duplikasi jenis layanan
            $jenisLayanan = array_column($request->layanan, 'jenis_layanan');
            if (count($jenisLayanan) !== count(array_unique($jenisLayanan))) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tidak boleh ada jenis layanan yang sama!');
            }

            $filename = $laundry->foto;
            $fotoLamaPath = $laundry->foto ? public_path('uploads/Laundry/' . $laundry->foto) : null;

            // Cek apakah user ingin HAPUS FOTO
            if ($request->has('hapus_foto') && $request->hapus_foto == '1') {
                if ($fotoLamaPath && File::exists($fotoLamaPath)) {
                    try {
                        File::delete($fotoLamaPath);
                    } catch (Exception $e) {
                        Log::warning('Gagal hapus foto lama: ' . $e->getMessage());
                    }
                }
                $filename = null;
            }
            // Cek apakah ada foto baru diupload
            elseif ($request->hasFile('foto')) {
                try {
                    $file = $request->file('foto');
                    
                    if (!$file->isValid()) {
                        throw new Exception('File tidak valid atau corrupt');
                    }
                    
                    // Hapus foto lama
                    if ($fotoLamaPath && File::exists($fotoLamaPath)) {
                        File::delete($fotoLamaPath);
                    }
                    
                    // Upload foto baru
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    
                    $uploadPath = public_path('uploads/Laundry');
                    if (!File::exists($uploadPath)) {
                        File::makeDirectory($uploadPath, 0755, true);
                    }
                    
                    $file->move($uploadPath, $filename);
                    
                } catch (Exception $e) {
                    Log::error('Error upload foto: ' . $e->getMessage());
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gagal mengupload foto baru: ' . $e->getMessage());
                }
            }

            // Hitung jarak otomatis dari kampus jika koordinat berubah
            $jarakKm = 0;
            $needUpdateJarak = ($laundry->latitude != $validated['latitude'] || $laundry->longitude != $validated['longitude']);
            
            if ($needUpdateJarak && $validated['latitude'] && $validated['longitude']) {
                try {
                    // Menggunakan method calculateDistance dari model
                    $tempLaundry = new Laundry();
                    $tempLaundry->latitude = $validated['latitude'];
                    $tempLaundry->longitude = $validated['longitude'];
                    
                    $jarakKm = $tempLaundry->calculateDistance(self::KAMPUS_LAT, self::KAMPUS_LNG);
                    Log::info("Jarak laundry '{$validated['nama']}' dari kampus diupdate: {$jarakKm} km");
                } catch (Exception $e) {
                    Log::warning("Gagal menghitung jarak untuk laundry '{$validated['nama']}': " . $e->getMessage());
                    $jarakKm = $laundry->jarak / 1000; // gunakan jarak lama
                }
            } else {
                $jarakKm = $laundry->jarak / 1000; // gunakan jarak lama jika koordinat tidak berubah
            }

            // Update data laundry dengan jarak yang sudah dihitung
            $laundry->update([
                'nama' => $validated['nama'],
                'alamat' => $validated['alamat'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'jarak' => round($jarakKm * 1000), // Simpan dalam meter (seperti kontrakan)
                'fasilitas' => $request->fasilitas,
                'foto' => $filename,
            ]);

            // Hapus layanan lama, buat layanan baru
            $laundry->layanan()->delete();
            
            foreach ($request->layanan as $layananData) {
                $laundry->layanan()->create([
                    'jenis_layanan' => $layananData['jenis_layanan'],
                    'nama_paket' => $layananData['nama_paket'],
                    'harga' => $layananData['harga'],
                    'estimasi_selesai' => $layananData['estimasi_selesai'],
                    'deskripsi' => $layananData['deskripsi'] ?? null,
                    'status' => $layananData['status'] ?? 'aktif',
                ]);
            }

            DB::commit();
            
            // Log activity
            ActivityLog::log('update', "Memperbarui laundry: {$laundry->nama}", 'Laundry', $laundry->id);
            
            return redirect()->route('laundry.index')
                ->with('success', 'Data berhasil diperbarui!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
                
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error di Laundry Update: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified laundry from storage
     */
    public function destroy(Laundry $laundry)
    {
        try {
            // Proteksi Role - Hanya Admin dan Super Admin yang bisa hapus
            if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
                return redirect()->route('laundry.index')
                    ->with('error', 'Anda tidak memiliki akses untuk menghapus data!');
            }

            // Hapus foto
            if ($laundry->foto && File::exists(public_path('uploads/Laundry/' . $laundry->foto))) {
                try {
                    File::delete(public_path('uploads/Laundry/' . $laundry->foto));
                } catch (Exception $e) {
                    Log::warning('Gagal hapus foto: ' . $e->getMessage());
                }
            }

            // Store nama laundry untuk logging
            $laundryNama = $laundry->nama;
            $laundryData = $laundry->toArray();

            // Hapus laundry (layanan otomatis terhapus via cascade)
            $laundry->delete();
            
            // Log activity
            ActivityLog::log('delete', "Menghapus laundry: {$laundryNama}", 'Laundry', $laundry->id, $laundryData, []);
            
            return redirect()->route('laundry.index')
                ->with('success', 'Data berhasil dihapus!');
                
        } catch (Exception $e) {
            Log::error('Error di Laundry Destroy: ' . $e->getMessage());
            return redirect()->route('laundry.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete selected laundry items
     */
    public function bulkDestroy(Request $request)
    {
        try {
            // Proteksi Role - Hanya Admin dan Super Admin yang bisa hapus
            if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
                return redirect()->route('laundry.index')
                    ->with('error', 'Anda tidak memiliki akses untuk menghapus data!');
            }

            // Handle both JSON string and array input
            $ids = $request->ids;
            if (is_string($ids)) {
                $ids = json_decode($ids, true);
            }
            
            if (empty($ids) || !is_array($ids)) {
                return redirect()->route('laundry.index')
                    ->with('error', 'Pilih minimal 1 laundry untuk dihapus!');
            }
            
            // Merge decoded ids back to request
            $request->merge(['ids' => $ids]);

            // Validasi
            $request->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'exists:laundry,id'
            ], [
                'ids.required' => 'Pilih minimal 1 laundry untuk dihapus',
                'ids.min' => 'Pilih minimal 1 laundry',
                'ids.*.exists' => 'Data laundry tidak valid'
            ]);

            $deletedCount = 0;
            $deletedNames = [];
            $errors = [];
            
            $laundryItems = Laundry::whereIn('id', $request->ids)->get();
            
            foreach ($laundryItems as $laundry) {
                try {
                    // Hapus foto jika ada
                    if ($laundry->foto && File::exists(public_path('uploads/Laundry/' . $laundry->foto))) {
                        File::delete(public_path('uploads/Laundry/' . $laundry->foto));
                    }
                    
                    $deletedNames[] = $laundry->nama;
                    $laundry->delete();
                    $deletedCount++;

                    // Log activity untuk setiap deletion
                    ActivityLog::log('delete', "Menghapus laundry: {$laundry->nama} (bulk)", 'Laundry', $laundry->id);
                    
                } catch (Exception $e) {
                    Log::error("Error menghapus laundry ID {$laundry->id}: " . $e->getMessage());
                    $errors[] = "Gagal menghapus {$laundry->nama}";
                }
            }
            
            if ($deletedCount > 0) {
                $message = "Berhasil menghapus {$deletedCount} data laundry!";
                if (!empty($errors)) {
                    $message .= " Namun ada " . count($errors) . " data yang gagal dihapus.";
                }
                return redirect()->route('laundry.index')->with('success', $message);
            } else {
                return redirect()->route('laundry.index')
                    ->with('error', 'Tidak ada data yang berhasil dihapus.');
            }
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator);
                
        } catch (Exception $e) {
            Log::error('Error di Laundry Bulk Destroy: ' . $e->getMessage());
            return redirect()->route('laundry.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}