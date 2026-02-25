<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KontrakanController;
use App\Http\Controllers\Api\LaundryController;
use App\Http\Controllers\Api\SAWController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\FavoriteController;

/*
|--------------------------------------------------------------------------
| API Routes untuk Mobile App
|--------------------------------------------------------------------------
| Routes ini khusus untuk aplikasi mobile Flutter
| Semua response menggunakan format JSON
| Rate limiting diterapkan di semua endpoint
*/

// -------------------------------------------------
// API HEALTH CHECK & DOCUMENTATION
// -------------------------------------------------
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'version' => '1.0.0',
        'server_time' => now()->toIso8601String(),
        'status' => 'healthy',
    ]);
});

Route::get('/docs', function () {
    return response()->json([
        'success' => true,
        'message' => 'SPK Kontrakan & Laundry API Documentation',
        'version' => '1.0.0',
        'base_url' => url('/api'),
        'endpoints' => [
            'health' => [
                'GET /api/health' => 'Cek status API',
            ],
            'auth' => [
                'POST /api/register' => 'Register user baru (rate: 3/hour)',
                'POST /api/login' => 'Login user (rate: 5/min)',
                'POST /api/logout' => 'Logout user [AUTH]',
                'PUT /api/profile/update' => 'Update profile [AUTH]',
            ],
            'kontrakan' => [
                'GET /api/kontrakan' => 'List kontrakan (filter: search, harga_min, harga_max, jumlah_kamar, sort_by, sort_order, per_page)',
                'GET /api/kontrakan/{id}' => 'Detail kontrakan',
                'GET /api/kontrakan/{id}/galeri' => 'Galeri foto kontrakan',
                'GET /api/kontrakan/{id}/reviews' => 'Reviews kontrakan',
            ],
            'laundry' => [
                'GET /api/laundry' => 'List laundry (filter: search, harga_min, harga_max, sort_by, sort_order, per_page)',
                'GET /api/laundry/{id}' => 'Detail laundry',
                'GET /api/laundry/{id}/galeri' => 'Galeri foto laundry',
                'GET /api/laundry/{id}/reviews' => 'Reviews laundry',
            ],
            'saw_calculation' => [
                'GET /api/saw/kriteria/kontrakan' => 'Get kriteria SAW kontrakan',
                'POST /api/saw/calculate/kontrakan' => 'Hitung SAW kontrakan (rate: 30/min)',
                'GET /api/saw/kriteria/laundry' => 'Get kriteria SAW laundry',
                'POST /api/saw/calculate/laundry' => 'Hitung SAW laundry (rate: 30/min)',
            ],
            'bookings' => [
                'GET /api/bookings' => 'History booking [AUTH]',
                'GET /api/bookings/{id}' => 'Detail booking [AUTH]',
                'POST /api/bookings' => 'Create booking [AUTH]',
                'POST /api/bookings/{id}/cancel' => 'Cancel booking [AUTH]',
                'POST /api/bookings/{id}/extend' => 'Extend booking [AUTH]',
                'POST /api/bookings/{id}/payment-proof' => 'Upload bukti bayar [AUTH]',
            ],
            'reviews' => [
                'POST /api/reviews/kontrakan/{id}' => 'Review kontrakan [AUTH]',
                'POST /api/reviews/laundry/{id}' => 'Review laundry [AUTH]',
                'PUT /api/reviews/{id}' => 'Update review [AUTH]',
                'DELETE /api/reviews/{id}' => 'Hapus review [AUTH]',
            ],
            'favorites' => [
                'GET /api/favorites' => 'List favorites [AUTH]',
                'POST /api/favorites/kontrakan/{id}' => 'Toggle favorite kontrakan [AUTH]',
                'POST /api/favorites/laundry/{id}' => 'Toggle favorite laundry [AUTH]',
                'DELETE /api/favorites/{id}' => 'Hapus favorite [AUTH]',
            ],
        ],
        'authentication' => [
            'type' => 'Bearer Token (Laravel Sanctum)',
            'header' => 'Authorization: Bearer {token}',
            'note' => 'Token didapat dari endpoint login/register',
        ],
        'response_format' => [
            'success' => ['success' => true, 'message' => '...', 'data' => '...'],
            'error' => ['success' => false, 'message' => '...', 'error_code' => '...', 'errors' => '...'],
        ],
        'error_codes' => [
            'VALIDATION_ERROR' => '422 - Input tidak valid',
            'UNAUTHENTICATED' => '401 - Token tidak valid / kadaluarsa',
            'NOT_FOUND' => '404 - Resource tidak ditemukan',
            'METHOD_NOT_ALLOWED' => '405 - HTTP method salah',
            'TOO_MANY_REQUESTS' => '429 - Rate limit exceeded',
            'RATE_LIMIT_EXCEEDED' => '429 - Batas request terlampaui',
            'SERVER_ERROR' => '500 - Kesalahan server',
            'INVALID_CREDENTIALS' => '401 - Email/password salah',
        ],
    ]);
});

// -------------------------------------------------
// AUTH ROUTES (PUBLIC - DENGAN RATE LIMITING)
// -------------------------------------------------
Route::middleware('throttle:register')->post('/register', [AuthController::class, 'register']);
Route::middleware('throttle:login')->post('/login', [AuthController::class, 'login']);

// -------------------------------------------------
// KONTRAKAN ROUTES (PUBLIC - TANPA AUTH)
// -------------------------------------------------
Route::prefix('kontrakan')->group(function () {
    Route::get('/', [KontrakanController::class, 'index']);
    Route::get('/{id}', [KontrakanController::class, 'show']);
    Route::get('/{id}/galeri', [KontrakanController::class, 'getGaleri']);
    Route::get('/{id}/reviews', [KontrakanController::class, 'getReviews']);
});

// -------------------------------------------------
// LAUNDRY ROUTES (PUBLIC - TANPA AUTH)
// -------------------------------------------------
Route::prefix('laundry')->group(function () {
    Route::get('/', [LaundryController::class, 'index']);
    Route::get('/{id}', [LaundryController::class, 'show']);
    Route::get('/{id}/galeri', [LaundryController::class, 'getGaleri']);
    Route::get('/{id}/reviews', [LaundryController::class, 'getReviews']);
});

// -------------------------------------------------
// SAW CALCULATION ROUTES (PUBLIC - RATE LIMITED)
// -------------------------------------------------
Route::prefix('saw')->middleware('throttle:saw')->group(function () {
    // Kontrakan
    Route::get('/kriteria/kontrakan', [SAWController::class, 'getKriteriaKontrakan']);
    Route::post('/calculate/kontrakan', [SAWController::class, 'calculateKontrakan']);
    
    // Laundry
    Route::get('/kriteria/laundry', [SAWController::class, 'getKriteriaLaundry']);
    Route::post('/calculate/laundry', [SAWController::class, 'calculateLaundry']);
});

// -------------------------------------------------
// PROTECTED ROUTES (PERLU AUTH TOKEN)
// -------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {
    
    // User Profile
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/profile/update', [AuthController::class, 'updateProfile']);
    
    // Booking Routes
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index']);
        Route::get('/{id}', [BookingController::class, 'show']);
        Route::post('/', [BookingController::class, 'store']);
        Route::post('/{id}/cancel', [BookingController::class, 'cancel']);
        Route::post('/{id}/extend', [BookingController::class, 'extend']);
        Route::post('/{id}/payment-proof', [BookingController::class, 'uploadPaymentProof']);
    });
    
    // Review Routes
    Route::prefix('reviews')->group(function () {
        Route::post('/kontrakan/{id}', [ReviewController::class, 'storeKontrakan']);
        Route::post('/laundry/{id}', [ReviewController::class, 'storeLaundry']);
        Route::put('/{id}', [ReviewController::class, 'update']);
        Route::delete('/{id}', [ReviewController::class, 'destroy']);
    });
    
    // Favorite Routes
    Route::prefix('favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index']);
        Route::post('/kontrakan/{id}', [FavoriteController::class, 'toggleKontrakan']);
        Route::post('/laundry/{id}', [FavoriteController::class, 'toggleLaundry']);
        Route::delete('/{id}', [FavoriteController::class, 'destroy']);
    });
});
