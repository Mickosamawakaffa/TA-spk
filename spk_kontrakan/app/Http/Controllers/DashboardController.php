<?php

namespace App\Http\Controllers;

use App\Models\Kontrakan;
use App\Models\Laundry;
use App\Models\Kriteria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Display dashboard with statistics and recent data
     * OPTIMIZED VERSION - Cache untuk 5 menit
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // ========== CACHE STATISTIK (5 menit) ==========
        $stats = Cache::remember('dashboard_stats', 300, function () {
            return [
                'jumlahKontrakan' => Kontrakan::count(),
                'jumlahLaundry' => Laundry::count(),
                'jumlahKriteria' => Kriteria::count(),
            ];
        });
        
        // ========== DATA TERBARU (No Cache) ==========
        $recentKontrakan = Kontrakan::latest()
            ->select('id', 'nama', 'alamat', 'harga', 'jarak', 'luas', 'created_at') // Only needed columns
            ->take(5)
            ->get();

        $recentLaundry = Laundry::with(['layanan' => function($query) {
                $query->select('laundry_id', 'jenis_layanan', 'harga')
                      ->orderBy('harga', 'asc')
                      ->limit(1); // Only cheapest service
            }])
            ->select('id', 'nama', 'alamat', 'jarak', 'created_at')
            ->latest()
            ->take(5)
            ->get();

        // ========== DATA CHART (Cache 10 menit) ==========
        $chartData = Cache::remember('dashboard_charts', 600, function () {
            
            // 1. Harga Kontrakan (Top 5)
            $hargaKontrakan = Kontrakan::select('nama', 'harga')
                ->orderBy('harga', 'desc')
                ->take(5)
                ->get();
            
            // 2. Harga Laundry (Top 5 dengan SUBQUERY - LEBIH CEPAT!)
            $hargaLaundry = DB::table('laundry')
                ->join(DB::raw('(
                    SELECT laundry_id, MIN(harga) as min_harga 
                    FROM layanan_laundry 
                    GROUP BY laundry_id
                ) as layanan'), 'laundry.id', '=', 'layanan.laundry_id')
                ->select('laundry.nama', 'layanan.min_harga as harga')
                ->orderBy('layanan.min_harga', 'asc')
                ->take(5)
                ->get();
            
            // 3. Distribusi Jarak (Single Query dengan CASE)
            $jarakKontrakan = DB::table('kontrakans')
                ->selectRaw("
                    SUM(CASE WHEN jarak <= 500 THEN 1 ELSE 0 END) as dekat,
                    SUM(CASE WHEN jarak > 500 AND jarak <= 1000 THEN 1 ELSE 0 END) as sedang,
                    SUM(CASE WHEN jarak > 1000 THEN 1 ELSE 0 END) as jauh
                ")
                ->first();
            
            $jarakLaundry = DB::table('laundry')
                ->selectRaw("
                    SUM(CASE WHEN jarak <= 500 THEN 1 ELSE 0 END) as dekat,
                    SUM(CASE WHEN jarak > 500 AND jarak <= 1000 THEN 1 ELSE 0 END) as sedang,
                    SUM(CASE WHEN jarak > 1000 THEN 1 ELSE 0 END) as jauh
                ")
                ->first();
            
            // 4. Statistik Aggregate (Single Query)
            $kontrakanStats = DB::table('kontrakans')
                ->selectRaw('
                    AVG(harga) as avg_harga,
                    AVG(jarak) as avg_jarak,
                    AVG(luas) as avg_luas,
                    MIN(harga) as min_harga,
                    MAX(harga) as max_harga
                ')
                ->first();
            
            // 5. Top Kontrakan by Luas
            $topKontrakan = Kontrakan::select('nama', 'harga', 'luas', 'jarak')
                ->orderBy('luas', 'desc')
                ->take(5)
                ->get();
            
            // 6. Monthly Data (Optimized dengan GROUP BY)
            $monthlyData = DB::table(DB::raw('
                (SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL n MONTH), "%Y-%m") as month
                 FROM (SELECT 0 as n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5) as numbers) as months
            '))
            ->leftJoin(DB::raw('kontrakans'), DB::raw('DATE_FORMAT(kontrakans.created_at, "%Y-%m")'), '=', 'months.month')
            ->leftJoin(DB::raw('laundry'), DB::raw('DATE_FORMAT(laundry.created_at, "%Y-%m")'), '=', 'months.month')
            ->selectRaw('
                months.month,
                COUNT(DISTINCT kontrakans.id) as kontrakan_count,
                COUNT(DISTINCT laundry.id) as laundry_count
            ')
            ->groupBy('months.month')
            ->orderBy('months.month', 'asc')
            ->get()
            ->map(function($item) {
                return [
                    'month' => date('M Y', strtotime($item->month . '-01')),
                    'kontrakan' => $item->kontrakan_count,
                    'laundry' => $item->laundry_count
                ];
            });
            
            return [
                'hargaKontrakan' => $hargaKontrakan,
                'hargaLaundry' => $hargaLaundry,
                'jarakKontrakan' => [
                    'dekat' => $jarakKontrakan->dekat ?? 0,
                    'sedang' => $jarakKontrakan->sedang ?? 0,
                    'jauh' => $jarakKontrakan->jauh ?? 0,
                ],
                'jarakLaundry' => [
                    'dekat' => $jarakLaundry->dekat ?? 0,
                    'sedang' => $jarakLaundry->sedang ?? 0,
                    'jauh' => $jarakLaundry->jauh ?? 0,
                ],
                'avgHargaKontrakan' => $kontrakanStats->avg_harga ?? 0,
                'avgJarakKontrakan' => $kontrakanStats->avg_jarak ?? 0,
                'avgLuasKontrakan' => $kontrakanStats->avg_luas ?? 0,
                'minHargaKontrakan' => $kontrakanStats->min_harga ?? 0,
                'maxHargaKontrakan' => $kontrakanStats->max_harga ?? 0,
                'topKontrakan' => $topKontrakan,
                'monthlyData' => $monthlyData,
            ];
        });

        // ========== TAMBAHAN DATA YANG HILANG ==========
        $additionalData = [
            // Data review
            'totalReviews' => \App\Models\Review::count() ?? 0,
            
            // Data admin
            'totalAdmins' => \App\Models\User::where('role', 'admin')->count() ?? 1,
            
            // Average kecepatan laundry (dari estimasi_selesai dalam jam)
            'avgKecepatan' => round(DB::table('layanan_laundry')
                ->where('estimasi_selesai', '>', 0)
                ->avg('estimasi_selesai') ?? 24, 1),
        ];

        // Merge semua data
        $data = array_merge($stats, $chartData, $additionalData, [
            'recentKontrakan' => $recentKontrakan,
            'recentLaundry' => $recentLaundry,
        ]);

        return view('dashboard.index', $data);
    }
    
    /**
     * Clear dashboard cache (optional - bisa dipanggil manual)
     */
    public function clearCache()
    {
        Cache::forget('dashboard_stats');
        Cache::forget('dashboard_charts');
        
        return redirect()->route('dashboard')
            ->with('success', 'Cache dashboard berhasil dibersihkan!');
    }
}