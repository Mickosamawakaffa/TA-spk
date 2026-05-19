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

    /**
     * Get range data for questionnaire (min/max harga, jarak, jumlah_kamar)
     * Used by mobile app for smart questions
     */
    public function getRange()
    {
        try {
            $kontrakan = Kontrakan::query();

            // Get min/max harga
            $hargaMin = (int) $kontrakan->min('harga') ?? 0;
            $hargaMax = (int) $kontrakan->max('harga') ?? 10000000;

            // Get min/max jarak (convert dari meter ke km)
            $jarakMinMeter = $kontrakan->min('jarak') ?? 0;
            $jarakMaxMeter = $kontrakan->max('jarak') ?? 50000;
            $jarakMin = (int) ($jarakMinMeter / 1000);
            $jarakMax = (int) ($jarakMaxMeter / 1000);

            // Get min/max jumlah_kamar
            $kamarMin = (int) $kontrakan->min('jumlah_kamar') ?? 1;
            $kamarMax = (int) $kontrakan->max('jumlah_kamar') ?? 20;

            // Get available fasilitas options (hanya yang relevan untuk mahasiswa)
            // Hilangkan: kamar mandi (pasti ada), AC, duplikat
            $allItems = $kontrakan->whereNotNull('fasilitas')->get(['fasilitas']);
            $fasilitasCount = [];
            
            foreach ($allItems as $item) {
                $facilities = array_map('trim', explode(',', $item->fasilitas));
                foreach ($facilities as $f) {
                    if (!isset($fasilitasCount[$f])) {
                        $fasilitasCount[$f] = 0;
                    }
                    $fasilitasCount[$f]++;
                }
            }
            
            // Facilities yang tidak relevan untuk mahasiswa (remove from list)
            $excludeFacilities = [
                'Kamar Mandi',           // Pasti ada
                'Kamar Mandi Dalam',     // Pasti ada
                'Kamar Mandi Luar',      // Pasti ada
                'AC',                    // Tidak penting
                'Dapur Bersama',         // Duplikat - gunakan hanya "Dapur"
            ];
            
            // Filter: hilangkan facilities yang tidak relevan
            foreach ($excludeFacilities as $exclude) {
                unset($fasilitasCount[$exclude]);
            }
            
            // Filter: hanya fasilitas yang digunakan 2+ kontrakan
            $minUsage = 2;
            $filteredFasilitas = array_keys(array_filter($fasilitasCount, function($count) use ($minUsage) {
                return $count >= $minUsage;
            }));
            
            // If less than 10 facilities found, lower threshold to 1+
            if (count($filteredFasilitas) < 10) {
                $filteredFasilitas = array_keys(array_filter($fasilitasCount, function($count) {
                    return $count >= 1;
                }));
            }
            
            // Sort alphabetically for consistency
            sort($filteredFasilitas);

            return response()->json([
                'success' => true,
                'data' => [
                    'harga' => [
                        'min' => $hargaMin,
                        'max' => $hargaMax,
                    ],
                    'jarak' => [
                        'min' => $jarakMin,
                        'max' => $jarakMax,
                    ],
                    'jumlah_kamar' => [
                        'min' => $kamarMin,
                        'max' => $kamarMax,
                    ],
                    'fasilitas' => $filteredFasilitas,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal ambil range data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
