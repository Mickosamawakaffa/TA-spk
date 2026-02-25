<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laundry;
use App\Models\Galeri;
use App\Models\Review;
use Illuminate\Http\Request;

class LaundryController extends Controller
{
    /**
     * Allowed sort columns (prevent SQL injection)
     */
    private array $allowedSortColumns = [
        'created_at', 'nama', 'jarak', 'updated_at',
    ];

    /**
     * List semua laundry
     */
    public function index(Request $request)
    {
        $query = Laundry::with(['galeri' => function($q) {
            $q->orderBy('is_primary', 'desc')->orderBy('urutan');
        }, 'layanan'])
        ->withCount('reviews');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'buka');
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
            $query->whereHas('layanan', function($lq) use ($request) {
                $lq->where('harga', '>=', (int) $request->harga_min);
            });
        }
        if ($request->filled('harga_max')) {
            $query->whereHas('layanan', function($lq) use ($request) {
                $lq->where('harga', '<=', (int) $request->harga_max);
            });
        }

        // Filter by jarak max (km -> meter)
        if ($request->filled('jarak_max')) {
            $jarakMeter = $request->jarak_max * 1000;
            $query->where('jarak', '<=', $jarakMeter);
        }

        // Safe sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = strtolower($request->get('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';
        
        if (!in_array($sortBy, $this->allowedSortColumns)) {
            $sortBy = 'created_at';
        }
        $query->orderBy($sortBy, $sortOrder);

        $perPage = min((int) $request->get('per_page', 50), 100); // Max 100 per page
        $laundry = $query->paginate($perPage);
        
        // Add computed fields (using already eager loaded relations)
        $laundry->getCollection()->transform(function($item) {
            $layananKiloan = $item->layanan->where('jenis_layanan', 'kiloan')->first();
            $layananSatuan = $item->layanan->where('jenis_layanan', 'satuan')->first();
            
            $item->harga_kiloan = $layananKiloan ? $layananKiloan->harga : 0;
            $item->harga_satuan = $layananSatuan ? $layananSatuan->harga : 0;
            $item->estimasi_selesai = $layananKiloan ? $layananKiloan->estimasi_selesai : 24;
            
            // Standardize jarak field (always in km for mobile)
            if (!$item->jarak && $item->latitude && $item->longitude) {
                $kampusLat = -8.15981;
                $kampusLng = 113.72312;
                $jarakKm = $item->calculateDistance($kampusLat, $kampusLng);
                $item->jarak = round($jarakKm, 2);
            } else if ($item->jarak) {
                $item->jarak = $item->jarak > 100 ? round($item->jarak / 1000, 2) : $item->jarak;
            }
            
            // Add avg rating from already loaded reviews count
            $item->avg_rating = round($item->reviews->avg('rating') ?? 0, 1);
            $item->total_reviews = $item->reviews_count;
            
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $laundry
        ], 200);
    }

    /**
     * Detail laundry by ID
     */
    public function show($id)
    {
        $laundry = Laundry::with([
            'galeri' => function($q) {
                $q->orderBy('is_primary', 'desc')->orderBy('urutan');
            },
            'layanan',
            'reviews.user:id,name,email'
        ])
        ->withCount('reviews')
        ->find($id);

        if (!$laundry) {
            return response()->json([
                'success' => false,
                'message' => 'Laundry tidak ditemukan',
                'error_code' => 'NOT_FOUND',
            ], 404);
        }

        // Use already loaded data (N+1 fix)
        $laundry->avg_rating = round($laundry->reviews->avg('rating') ?? 0, 1);
        $laundry->total_reviews = $laundry->reviews_count;
        
        // Add layanan info
        $layananKiloan = $laundry->layanan->where('jenis_layanan', 'kiloan')->first();
        $layananSatuan = $laundry->layanan->where('jenis_layanan', 'satuan')->first();
        
        $laundry->harga_kiloan = $layananKiloan ? $layananKiloan->harga : 0;
        $laundry->harga_satuan = $layananSatuan ? $layananSatuan->harga : 0;
        $laundry->estimasi_selesai = $layananKiloan ? $layananKiloan->estimasi_selesai : 24;
        
        // Standardize jarak
        if (!$laundry->jarak && $laundry->latitude && $laundry->longitude) {
            $kampusLat = -8.15981;
            $kampusLng = 113.72312;
            $jarakKm = $laundry->calculateDistance($kampusLat, $kampusLng);
            $laundry->jarak = round($jarakKm, 2);
        } else if ($laundry->jarak) {
            $laundry->jarak = $laundry->jarak > 100 ? round($laundry->jarak / 1000, 2) : $laundry->jarak;
        }

        return response()->json([
            'success' => true,
            'data' => $laundry
        ], 200);
    }

    /**
     * Get galeri laundry
     */
    public function getGaleri($id)
    {
        if (!Laundry::where('id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Laundry tidak ditemukan',
                'error_code' => 'NOT_FOUND',
            ], 404);
        }

        $galeri = Galeri::where('galeriable_type', Laundry::class)
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
     * Get reviews laundry
     */
    public function getReviews($id)
    {
        if (!Laundry::where('id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Laundry tidak ditemukan',
                'error_code' => 'NOT_FOUND',
            ], 404);
        }

        $reviews = Review::with('user:id,name,email')
            ->where('reviewable_type', Laundry::class)
            ->where('reviewable_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $reviews
        ], 200);
    }
}
