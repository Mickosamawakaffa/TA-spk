<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kontrakan;
use App\Models\Galeri;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class KontrakanController extends Controller
{
    /**
     * Allowed sort columns (prevent SQL injection via sort_by)
     */
    private array $allowedSortColumns = [
        'created_at', 'nama', 'harga', 'jarak', 'jumlah_kamar', 'updated_at',
    ];

    /**
     * List semua kontrakan
     */
    public function index(Request $request)
    {
        $query = Kontrakan::with(['galeri' => function($q) {
            $q->orderBy('is_primary', 'desc')->orderBy('urutan');
        }])->withCount('reviews');

        // Filter by status - standardized
        if ($request->filled('status')) {
            $status = $request->status;
            if (in_array($status, ['tersedia', 'available'])) {
                $query->where(function($q) {
                    $q->where('status', 'tersedia')
                      ->orWhere('status', 'available');
                });
            } else {
                $query->where('status', $status);
            }
        }

        // Search by name or address
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('alamat', 'like', '%' . $search . '%');
            });
        }

        // Filter by price range
        if ($request->filled('harga_min')) {
            $query->where('harga', '>=', (int) $request->harga_min);
        }
        if ($request->filled('harga_max')) {
            $query->where('harga', '<=', (int) $request->harga_max);
        }

        // Filter by jumlah kamar
        if ($request->filled('jumlah_kamar')) {
            $query->where('jumlah_kamar', '>=', (int) $request->jumlah_kamar);
        }

        // Filter by jarak max (km -> meter)
        if ($request->filled('jarak_max')) {
            $jarakMeter = $request->jarak_max * 1000;
            $query->where('jarak', '<=', $jarakMeter);
        }

        // Safe sorting - prevent SQL injection
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = strtolower($request->get('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';
        
        if (!in_array($sortBy, $this->allowedSortColumns)) {
            $sortBy = 'created_at';
        }
        $query->orderBy($sortBy, $sortOrder);

        $perPage = min((int) $request->get('per_page', 15), 100); // Max 100 per page
        $kontrakan = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $kontrakan
        ], 200);
    }

    /**
     * Detail kontrakan by ID
     */
    public function show($id)
    {
        $kontrakan = Kontrakan::with([
            'galeri' => function($q) {
                $q->orderBy('is_primary', 'desc')->orderBy('urutan');
            },
            'reviews.user'
        ])
        ->withCount('reviews')
        ->find($id);

        if (!$kontrakan) {
            return response()->json([
                'success' => false,
                'message' => 'Kontrakan tidak ditemukan',
                'error_code' => 'NOT_FOUND',
            ], 404);
        }

        // Use aggregate query instead of loading all reviews again (N+1 fix)
        $kontrakan->avg_rating = round($kontrakan->reviews->avg('rating') ?? 0, 1);
        $kontrakan->total_reviews = $kontrakan->reviews_count;

        return response()->json([
            'success' => true,
            'data' => $kontrakan
        ], 200);
    }

    /**
     * Get galeri kontrakan
     */
    public function getGaleri($id)
    {
        // Verify kontrakan exists first
        if (!Kontrakan::where('id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kontrakan tidak ditemukan',
                'error_code' => 'NOT_FOUND',
            ], 404);
        }

        $galeri = Galeri::where('galeriable_type', Kontrakan::class)
            ->where('galeriable_id', $id)
            ->orderBy('is_primary', 'desc')
            ->orderBy('urutan')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $galeri
        ], 200);
    }

    /**
     * Get reviews kontrakan
     */
    public function getReviews($id)
    {
        // Verify kontrakan exists first
        if (!Kontrakan::where('id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kontrakan tidak ditemukan',
                'error_code' => 'NOT_FOUND',
            ], 404);
        }

        $reviews = Review::with('user:id,name,email')
            ->where('reviewable_type', Kontrakan::class)
            ->where('reviewable_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $reviews
        ], 200);
    }
}
