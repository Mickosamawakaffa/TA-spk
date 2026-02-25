<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter dan sorting dari request
        $filterTipeBisnis = $request->get('filter', '');
        $sortBy = $request->get('sort_by', 'id'); // Default sort by id
        $sortOrder = $request->get('sort_order', 'asc'); // Default ascending
        
        // Query builder
        $query = Kriteria::query();
        
        // Filter berdasarkan tipe bisnis jika ada
        if ($filterTipeBisnis) {
            $query->where('tipe_bisnis', $filterTipeBisnis);
        }
        
        // Sorting
        switch($sortBy) {
            case 'nama_kriteria':
                $query->orderBy('nama_kriteria', $sortOrder);
                break;
            case 'bobot':
                $query->orderBy('bobot', $sortOrder);
                break;
            case 'tipe_bisnis':
                $query->orderBy('tipe_bisnis', $sortOrder);
                break;
            case 'tipe':
                $query->orderBy('tipe', $sortOrder);
                break;
            default:
                $query->orderBy('id', $sortOrder);
        }
        
        // Get kriteria yang sudah difilter & sorted
        $kriteria = $query->get();
        
        // Ambil SEMUA data untuk statistik (tidak terpengaruh filter)
        $allKriteria = Kriteria::all();
        
        // Hitung benefit dan cost dengan case-insensitive
        $totalBenefit = $allKriteria->filter(function($item) {
            return strtolower($item->tipe) == 'benefit';
        })->count();
        
        $totalCost = $allKriteria->filter(function($item) {
            return strtolower($item->tipe) == 'cost';
        })->count();
        
        // ✅ VALIDASI TOTAL BOBOT
        $bobotKontrakan = $allKriteria->where('tipe_bisnis', 'kontrakan')->sum('bobot');
        $bobotLaundry = $allKriteria->where('tipe_bisnis', 'laundry')->sum('bobot');
        
        // Cek apakah bobot valid (toleransi 0.01 untuk floating point)
        $bobotKontrakanValid = abs($bobotKontrakan - 1.0) < 0.01;
        $bobotLaundryValid = abs($bobotLaundry - 1.0) < 0.01;
        
        // Buat pesan warning jika ada yang tidak valid
        $bobotWarnings = [];
        
        if (!$bobotKontrakanValid && $allKriteria->where('tipe_bisnis', 'kontrakan')->count() > 0) {
            if ($bobotKontrakan > 1.0) {
                $bobotWarnings[] = "Total bobot Kontrakan ({$bobotKontrakan}) melebihi 1.00. Harap sesuaikan bobot kriteria.";
            } else {
                $bobotWarnings[] = "Total bobot Kontrakan ({$bobotKontrakan}) kurang dari 1.00. Harap sesuaikan bobot kriteria.";
            }
        }
        
        if (!$bobotLaundryValid && $allKriteria->where('tipe_bisnis', 'laundry')->count() > 0) {
            if ($bobotLaundry > 1.0) {
                $bobotWarnings[] = "Total bobot Laundry ({$bobotLaundry}) melebihi 1.00. Harap sesuaikan bobot kriteria.";
            } else {
                $bobotWarnings[] = "Total bobot Laundry ({$bobotLaundry}) kurang dari 1.00. Harap sesuaikan bobot kriteria.";
            }
        }
        
        return view('kriteria.index', compact(
            'kriteria', 
            'filterTipeBisnis',
            'sortBy',
            'sortOrder',
            'allKriteria',
            'totalBenefit',
            'totalCost',
            'bobotKontrakan',
            'bobotLaundry',
            'bobotKontrakanValid',
            'bobotLaundryValid',
            'bobotWarnings'
        ));
    }

    public function create()
    {
        return view('kriteria.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_bisnis' => 'required|in:kontrakan,laundry',
            'nama_kriteria' => 'required|string|max:255',
            'bobot' => 'required|numeric|min:0|max:1',
            'tipe' => 'required|in:Benefit,Cost',
        ]);

        Kriteria::create($request->all());
        
        // Redirect dengan parameter filter
        return redirect()
            ->route('kriteria.index', ['filter' => $request->tipe_bisnis])
            ->with('success', 'Kriteria berhasil ditambahkan!');
    }

    public function show(Kriteria $kriterium)
    {
        return view('kriteria.show', ['kriteria' => $kriterium]);
    }

    public function edit(Kriteria $kriterium)
    {
        return view('kriteria.edit', ['kriteria' => $kriterium]);
    }

    public function update(Request $request, Kriteria $kriterium)
    {
        $request->validate([
            'tipe_bisnis' => 'required|in:kontrakan,laundry',
            'nama_kriteria' => 'required|string|max:255',
            'bobot' => 'required|numeric|min:0|max:1',
            'tipe' => 'required|in:Benefit,Cost',
        ]);

        $kriterium->update($request->all());
        
        // Redirect dengan parameter filter
        return redirect()
            ->route('kriteria.index', ['filter' => $request->tipe_bisnis])
            ->with('success', 'Kriteria berhasil diperbarui!');
    }

    public function destroy(Kriteria $kriterium)
    {
        // CEK ROLE - HANYA ADMIN DAN SUPER ADMIN YANG BOLEH HAPUS
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus data!');
        }
        
        $tipeBisnis = $kriterium->tipe_bisnis; // Simpan sebelum dihapus
        $kriterium->delete();
        
        // Redirect dengan parameter filter
        return redirect()
            ->route('kriteria.index', ['filter' => $tipeBisnis])
            ->with('success', 'Kriteria berhasil dihapus!');
    }
}