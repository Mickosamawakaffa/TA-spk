<?php

namespace App\Http\Controllers;

use App\Models\Kontrakan;
use App\Models\Laundry;
use App\Models\User;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Display welcome page with real statistics
     */
    public function index()
    {
        // Cache data untuk 5 menit untuk performa yang lebih baik
        $stats = Cache::remember('welcome_stats', 300, function () {
            return [
                'jumlahKontrakan' => Kontrakan::count(),
                'jumlahLaundry' => Laundry::count(),
                'jumlahUsers' => User::count(),
                'totalReviews' => Review::count() ?? 0,
            ];
        });

        // Rekomendasi Kontrakan Terbaik (berdasarkan rating/harga)
        $topKontrakan = Cache::remember('top_kontrakan', 600, function () {
            return Kontrakan::select('id', 'nama', 'alamat', 'harga', 'jarak', 'luas', 'foto')
                ->orderBy('luas', 'desc')  // Prioritas luas besar
                ->orderBy('harga', 'asc')  // Harga murah
                ->take(3)
                ->get();
        });

        // Rekomendasi Laundry Terbaik (berdasarkan harga termurah)
        $topLaundry = Cache::remember('top_laundry', 600, function () {
            return DB::table('laundry')
                ->join(DB::raw('(
                    SELECT laundry_id, MIN(harga) as min_harga 
                    FROM layanan_laundry 
                    GROUP BY laundry_id
                ) as layanan'), 'laundry.id', '=', 'layanan.laundry_id')
                ->select('laundry.*', 'layanan.min_harga')
                ->orderBy('layanan.min_harga', 'asc')
                ->take(3)
                ->get();
        });

        // Rating rata-rata (jika ada table reviews)
        $avgRating = Cache::remember('avg_rating', 600, function () {
            return Review::avg('rating') ?? 4.8;
        });

        return view('welcome', compact(
            'stats', 
            'topKontrakan', 
            'topLaundry', 
            'avgRating'
        ));
    }

    /**
     * API endpoint untuk mendapatkan statistik real-time
     */
    public function getStats()
    {
        $stats = Cache::remember('api_welcome_stats', 60, function () {
            return [
                'jumlahKontrakan' => Kontrakan::count(),
                'jumlahLaundry' => Laundry::count(),
                'jumlahUsers' => User::count(),
                'totalReviews' => Review::count() ?? 0,
                'avgRating' => round(Review::avg('rating') ?? 4.8, 1),
            ];
        });

        return response()->json($stats);
    }

    /**
     * Show admin access page (separated from main homepage)
     */
    public function adminAccess()
    {
        return view('admin.access');
    }
}