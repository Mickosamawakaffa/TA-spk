<?php

namespace App\Http\Controllers;

use App\Models\Kontrakan;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class KontrakanController extends Controller
{
    /**
     * Display a listing of kontrakan with ADVANCED filters and sorting
     */
    public function index(Request $request)
    {
        // Query builder
        $query = Kontrakan::query();
        
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
            $query->where('harga', '>=', $request->harga_min);
        }
        
        if ($request->filled('harga_max')) {
            $query->where('harga', '<=', $request->harga_max);
        }
        
        // ========== FILTER: JARAK MAKSIMAL (Slider dalam KM) ==========
        if ($request->filled('jarak_max')) {
            // Konversi km ke meter (karena jarak di DB dalam meter)
            $jarakMeter = $request->jarak_max * 1000;
            $query->where('jarak', '<=', $jarakMeter);
        }
        
        // ========== FILTER: jumlah_kamar MINIMAL & MAKSIMAL ==========
        if ($request->filled('jumlah_kamar_min')) {
            $query->where('jumlah_kamar', '>=', $request->jumlah_kamar_min);
        }
        
        if ($request->filled('jumlah_kamar_max')) {
            $query->where('jumlah_kamar', '<=', $request->jumlah_kamar_max);
        }
        
        // ========== FILTER: FASILITAS (Multiple Checkbox) ==========
        if ($request->filled('fasilitas_filter')) {
            $fasilitasArray = $request->fasilitas_filter;
            
            foreach ($fasilitasArray as $fasilitas) {
                // Cari kontrakan yang fasilitasnya mengandung kata kunci ini
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
                $query->orderBy('harga', 'ASC');
                break;
            case 'harga_termahal':
                $query->orderBy('harga', 'DESC');
                break;
            case 'jarak_terdekat':
                $query->orderBy('jarak', 'ASC');
                break;
            case 'jarak_terjauh':
                $query->orderBy('jarak', 'DESC');
                break;
            case 'jumlah_kamar_terbesar':
                $query->orderBy('jumlah_kamar', 'DESC');
                break;
            case 'jumlah_kamar_terkecil':
                $query->orderBy('jumlah_kamar', 'ASC');
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
        $kontrakan = $query->paginate(12)->withQueryString(); // 12 items per halaman
        
        // ========== STATISTIK & DATA UNTUK FILTER UI ==========
        
        // Total kontrakan (semua data, tanpa filter)
        $totalKontrakan = Kontrakan::count();
        
        // Jumlah hasil setelah filter
        $filteredCount = $kontrakan->total();
        
        // Range harga untuk slider (min & max dari database)
        $hasData = Kontrakan::count() > 0;
        $hargaMin = $hasData ? (Kontrakan::min('harga') ?? 0) : 0;
        $hargaMax = $hasData ? (Kontrakan::max('harga') ?? 0) : 10000000;
        
        // Range jarak untuk slider (dalam km)
        $jarakMaxDb = $hasData ? (Kontrakan::max('jarak') ?? 0) : 0; // dalam meter
        $jarakMaxKm = $jarakMaxDb > 0 ? ceil($jarakMaxDb / 1000) : 0; // konversi ke km, bulatkan ke atas
        
        // Range jumlah_kamar untuk input
        $jumlah_kamarMin = $hasData ? (Kontrakan::min('jumlah_kamar') ?? 0) : 0;
        $jumlah_kamarMax = $hasData ? (Kontrakan::max('jumlah_kamar') ?? 0) : 0;
        
        // Daftar fasilitas unik untuk checkbox (extract dari semua kontrakan)
        // Daftar fasilitas unik untuk checkbox (extract dari semua kontrakan)
                $allFasilitas = Kontrakan::pluck('fasilitas')->filter();
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
            'jumlah_kamar_min' => $request->jumlah_kamar_min,
            'jumlah_kamar_max' => $request->jumlah_kamar_max,
            'fasilitas_filter' => $request->fasilitas_filter ?? [],
            'sort_by' => $sortBy,
        ];
        
        return view('kontrakan.index', compact(
            'kontrakan',
            'filters',
            'totalKontrakan',
            'filteredCount',
            'hargaMin',
            'hargaMax',
            'jarakMaxKm',
            'jumlah_kamarMin',
            'jumlah_kamarMax',
            'fasilitasUnique'
        ));
    }

    /**
     * Show the form for creating a new kontrakan
     */
    public function create()
    {
        return view('kontrakan.create');
    }

    /**
     * Store a newly created kontrakan in storage
     */
    public function store(Request $request)
    {
        // Validasi input (DENGAN WHATSAPP)
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_whatsapp' => 'nullable|string|max:20|regex:/^[0-9]+$/',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'harga' => 'required|numeric',
            'jarak' => 'required|numeric',
            'fasilitas' => 'nullable|string',
            'jumlah_kamar' => 'required|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'latitude.required' => 'Koordinat latitude harus diisi (klik pada peta)',
            'longitude.required' => 'Koordinat longitude harus diisi (klik pada peta)',
            'no_whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka',
        ]);

        // Proses Upload Foto
        $filename = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/kontrakan'), $filename);
        }

        // Simpan data ke database (DENGAN WHATSAPP)
        $kontrakan = Kontrakan::create([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_whatsapp' => $request->no_whatsapp,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'harga' => $request->harga,
            'jarak' => $request->jarak,
            'fasilitas' => $request->fasilitas,
            'jumlah_kamar' => $request->jumlah_kamar,
            'luas' => $request->luas ?? 0, // Default 0 jika tidak diisi
            'foto' => $filename,
        ]);

        // Log activity
        ActivityLog::log('create', "Membuat kontrakan baru: {$kontrakan->nama}", 'Kontrakan', $kontrakan->id);

        return redirect()->route('kontrakan.index')->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Display the specified kontrakan
     */
    public function show($id)
    {
        $kontrakan = Kontrakan::find($id);
        
        if (!$kontrakan) {
            return redirect()->route('kontrakan.index')
                ->with('error', 'Data kontrakan tidak ditemukan!');
        }
        
        // Cek apakah user yang akses adalah admin atau user biasa
        if (auth()->check() && in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            // Admin: tampilkan view admin dengan fitur edit/delete
            return view('kontrakan.show', compact('kontrakan'));
        } else {
            // User biasa atau guest: tampilkan view user-friendly tanpa fitur admin
            return view('user.kontrakan-detail', compact('kontrakan'));
        }
    }

    /**
     * Show the form for editing the specified kontrakan
     */
    public function edit($id)
    {
        $kontrakan = Kontrakan::find($id);
        
        if (!$kontrakan) {
            return redirect()->route('kontrakan.index')
                ->with('error', 'Data kontrakan tidak ditemukan!');
        }
        
        return view('kontrakan.edit', compact('kontrakan'));
    }

    /**
     * Update the specified kontrakan in storage
     */
    public function update(Request $request, $id)
    {
        $kontrakan = Kontrakan::find($id);
        
        if (!$kontrakan) {
            return redirect()->route('kontrakan.index')
                ->with('error', 'Data kontrakan tidak ditemukan!');
        }
        
        // Validasi input (DENGAN WHATSAPP)
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_whatsapp' => 'nullable|string|max:20|regex:/^[0-9]+$/',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'harga' => 'required|numeric',
            'jarak' => 'required|numeric',
            'fasilitas' => 'nullable|string',
            'jumlah_kamar' => 'required|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'latitude.required' => 'Koordinat latitude harus diisi (klik pada peta)',
            'longitude.required' => 'Koordinat longitude harus diisi (klik pada peta)',
            'no_whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka',
        ]);

        // Default: foto tetap sama
        $filename = $kontrakan->foto;

        // Cek apakah user ingin HAPUS FOTO
        if ($request->has('hapus_foto') && $request->hapus_foto == '1') {
            if ($kontrakan->foto && File::exists(public_path('uploads/kontrakan/' . $kontrakan->foto))) {
                File::delete(public_path('uploads/kontrakan/' . $kontrakan->foto));
            }
            $filename = null;
        }
        // Cek apakah ada foto baru diupload
        elseif ($request->hasFile('foto')) {
            if ($kontrakan->foto && File::exists(public_path('uploads/kontrakan/' . $kontrakan->foto))) {
                File::delete(public_path('uploads/kontrakan/' . $kontrakan->foto));
            }
            $file = $request->file('foto');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/kontrakan'), $filename);
        }

        // Update data (DENGAN WHATSAPP)
        $oldValues = $kontrakan->toArray();
        
        $kontrakan->update([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_whatsapp' => $request->no_whatsapp,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'harga' => $request->harga,
            'jarak' => $request->jarak,
            'fasilitas' => $request->fasilitas,
            'jumlah_kamar' => $request->jumlah_kamar,
            'luas' => $request->luas ?? $kontrakan->luas ?? 0, // Gunakan nilai lama jika tidak diisi
            'foto' => $filename,
        ]);

        // Log activity
        ActivityLog::log('update', "Memperbarui kontrakan: {$kontrakan->nama}", 'Kontrakan', $kontrakan->id, $oldValues, $kontrakan->toArray());

        return redirect()->route('kontrakan.index')->with('success', 'Data berhasil diperbarui!');
    }

    /**
     * Remove the specified kontrakan from storage
     */
    public function destroy($id)
    {
        // Proteksi Role - Hanya Admin dan Super Admin yang bisa hapus
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return redirect()->route('kontrakan.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus data!');
        }

        // Cari kontrakan berdasarkan ID
        $kontrakan = Kontrakan::find($id);
        
        if (!$kontrakan) {
            return redirect()->route('kontrakan.index')
                ->with('error', 'Data kontrakan tidak ditemukan atau sudah dihapus sebelumnya!');
        }

        // Hapus foto dari folder sebelum hapus data
        if ($kontrakan->foto && File::exists(public_path('uploads/kontrakan/' . $kontrakan->foto))) {
            File::delete(public_path('uploads/kontrakan/' . $kontrakan->foto));
        }

        // Store nama kontrakan untuk logging sebelum dihapus
        $kontrakanNama = $kontrakan->nama;
        $kontrakanData = $kontrakan->toArray();

        // Hapus data dari database
        $kontrakan->delete();

        // Log activity
        ActivityLog::log('delete', "Menghapus kontrakan: {$kontrakanNama}", 'Kontrakan', $kontrakan->id, $kontrakanData, []);
        
        return redirect()->route('kontrakan.index')->with('success', 'Data berhasil dihapus!');
    }

    /**
     * Bulk delete multiple kontrakan
     */
    public function bulkDestroy(Request $request)
    {
        // Proteksi Role - Hanya Admin dan Super Admin yang bisa hapus
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return redirect()->route('kontrakan.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus data!');
        }

        // Handle both JSON string and array input
        $ids = $request->ids;
        if (is_string($ids)) {
            $ids = json_decode($ids, true);
        }
        
        if (empty($ids) || !is_array($ids)) {
            return redirect()->route('kontrakan.index')
                ->with('error', 'Pilih minimal 1 kontrakan untuk dihapus!');
        }
        
        // Merge decoded ids back to request for validation
        $request->merge(['ids' => $ids]);

        $deletedCount = 0;
        $deletedNames = [];

        // Loop dan hapus satu per satu
        foreach ($request->ids as $id) {
            $kontrakan = Kontrakan::find($id);
            
            if ($kontrakan) {
                $deletedNames[] = $kontrakan->nama;

                // Hapus foto jika ada
                if ($kontrakan->foto && File::exists(public_path('uploads/kontrakan/' . $kontrakan->foto))) {
                    File::delete(public_path('uploads/kontrakan/' . $kontrakan->foto));
                }
                
                // Hapus data
                $kontrakan->delete();
                $deletedCount++;

                // Log activity untuk setiap deletion
                ActivityLog::log('delete', "Menghapus kontrakan: {$kontrakan->nama} (bulk)", 'Kontrakan', $id);
            }
        }

        return redirect()->route('kontrakan.index')
            ->with('success', "Berhasil menghapus {$deletedCount} data kontrakan!");
    }

    /**
     * Update status kontrakan (Quick update)
     */
    public function updateStatus(Request $request, Kontrakan $kontrakan)
    {
        $request->validate([
            'status' => 'required|in:available,booked,occupied,maintenance',
        ]);

        $oldStatus = $kontrakan->status;
        $newStatus = $request->status;

        $kontrakan->update([
            'status' => $newStatus,
            // Reset occupied_until jika status jadi available
            'occupied_until' => $newStatus === 'available' ? null : $kontrakan->occupied_until,
        ]);

        // Log activity
        ActivityLog::log('update', "Mengubah status kontrakan {$kontrakan->nama}: {$oldStatus} → {$newStatus}", 'Kontrakan', $kontrakan->id);

        $statusLabels = [
            'available' => 'Tersedia',
            'booked' => 'Sudah Dipesan',
            'occupied' => 'Sedang Ditempati',
            'maintenance' => 'Pemeliharaan',
        ];

        return back()->with('success', "Status kontrakan berhasil diubah menjadi \"{$statusLabels[$newStatus]}\"!");
    }
}