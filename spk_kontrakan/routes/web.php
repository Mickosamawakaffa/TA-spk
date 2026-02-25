<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KontrakanController;
use App\Http\Controllers\LaundryController;
use App\Http\Controllers\SAWController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\GaleriController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ExportController;      // ← EXPORT
use App\Http\Controllers\ActivityLogController;  // ← ACTIVITY LOG
use App\Http\Controllers\UserManagementController; // ← USER MANAGEMENT
use App\Http\Controllers\BackupController;       // ← BACKUP
use App\Http\Controllers\BookingController;     // ← BOOKING SYSTEM (ADMIN)

// -------------------------------------------------
// LANDING PAGE - Redirect ke Admin Portal
// -------------------------------------------------
Route::get('/', function () {
    return redirect()->route('admin.portal');
})->name('welcome');

// -------------------------------------------------
// ADMIN PORTAL - Landing page khusus untuk pemilik/admin
// -------------------------------------------------
Route::get('/admin-portal', function () {
    // Hitung statistik real-time
    $totalAdmins = \App\Models\User::where('role', 'admin')->count();
    $totalKontrakan = \App\Models\Kontrakan::count();
    $totalLaundry = \App\Models\Laundry::count();
    $totalProperti = $totalKontrakan + $totalLaundry;
    $totalBookings = \App\Models\Booking::where('status', 'confirmed')->count();
    $avgRating = \App\Models\Review::avg('rating') ?? 4.8;
    
    return view('admin-portal', [
        'totalAdmins' => $totalAdmins,
        'totalProperti' => $totalProperti,
        'totalBookings' => $totalBookings,
        'avgRating' => round($avgRating, 1)
    ]);
})->name('admin.portal');

// Shortcut URL - lebih mudah diingat
Route::get('/pemilik', function () {
    return redirect()->route('admin.portal');
});

// -------------------------------------------------
// AUTH ADMIN (PUBLIC - TIDAK PERLU LOGIN)
// -------------------------------------------------
Route::get('/admin/login', [AdminAuthController::class, 'loginPage'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');

Route::get('/admin/register', [AdminAuthController::class, 'registerPage'])->name('admin.register');
Route::post('/admin/register', [AdminAuthController::class, 'register'])->name('admin.register.post');

// -------------------------------------------------
// ROUTE ADMIN TERPROTEKSI LOGIN
// -------------------------------------------------
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // ========== 🆕 CLEAR DASHBOARD CACHE (BARU!) ==========
    Route::get('/dashboard/clear-cache', [DashboardController::class, 'clearCache'])->name('dashboard.clear-cache');

    // ========== 🆕 EXPORT ROUTES ==========
    Route::prefix('export')->name('export.')->group(function () {
        // Kontrakan
        Route::get('/kontrakan/excel', [ExportController::class, 'kontrakanExcel'])->name('kontrakan.excel');
        Route::get('/kontrakan/pdf', [ExportController::class, 'kontrakanPDF'])->name('kontrakan.pdf');
        
        // Laundry
        Route::get('/laundry/excel', [ExportController::class, 'laundryExcel'])->name('laundry.excel');
        Route::get('/laundry/pdf', [ExportController::class, 'laundryPDF'])->name('laundry.pdf');
        
        // SAW Results
        Route::post('/saw/pdf', [ExportController::class, 'sawResultsPDF'])->name('saw.pdf');
        Route::post('/saw/excel', [ExportController::class, 'sawResultsExcel'])->name('saw.excel');
    });

    // ========== 🆕 ACTIVITY LOG ROUTES ==========
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('activity-logs', ActivityLogController::class)->only(['index', 'show']);
        Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
        Route::post('/activity-logs/clear', [ActivityLogController::class, 'clear'])->name('activity-logs.clear');
    });

    // ========== 🆕 USER MANAGEMENT ROUTES ==========
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserManagementController::class);
        Route::post('/users/{user}/restore', [UserManagementController::class, 'restore'])->name('users.restore');
        Route::post('/users/bulk-delete', [UserManagementController::class, 'bulkDelete'])->name('users.bulk-delete');
    });

    // ========== 🆕 BACKUP & RESTORE ROUTES ==========
    Route::prefix('admin/backup')->name('admin.backup.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/create', [BackupController::class, 'create'])->name('create');
        Route::get('/download/{backup}', [BackupController::class, 'download'])->name('download');
        Route::delete('/delete/{backup}', [BackupController::class, 'delete'])->name('delete');
        Route::post('/restore/{backup}', [BackupController::class, 'restore'])->name('restore');
    });

    // ========== BULK DELETE KONTRAKAN (HARUS SEBELUM RESOURCE!) ==========
    Route::post('/kontrakan/bulk-destroy', [KontrakanController::class, 'bulkDestroy'])->name('kontrakan.bulk-destroy');

    // ========== 🆕 BOOKING SYSTEM ROUTES ==========
    Route::prefix('admin/bookings')->name('admin.bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/create', [BookingController::class, 'create'])->name('create');
        Route::post('/', [BookingController::class, 'store'])->name('store');
        Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
        Route::get('/{booking}/edit', [BookingController::class, 'edit'])->name('edit');
        Route::put('/{booking}', [BookingController::class, 'update'])->name('update');
        Route::delete('/{booking}', [BookingController::class, 'destroy'])->name('destroy');
        
        // Actions
        Route::post('/{booking}/confirm', [BookingController::class, 'confirm'])->name('confirm');
        Route::post('/{booking}/check-in', [BookingController::class, 'checkIn'])->name('check-in');
        Route::post('/{booking}/check-out', [BookingController::class, 'checkOut'])->name('check-out');
        Route::post('/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');
        Route::post('/{booking}/mark-paid', [BookingController::class, 'markPaid'])->name('mark-paid');
        Route::post('/{booking}/toggle-payment', [BookingController::class, 'togglePaymentStatus'])->name('toggle-payment');
        
        // API & History
        Route::post('/check-availability', [BookingController::class, 'checkAvailability'])->name('check-availability');
        Route::get('/kontrakan/{kontrakan}/history', [BookingController::class, 'kontrakanHistory'])->name('kontrakan-history');
    });

    // ========== UPDATE STATUS KONTRAKAN (QUICK) ==========
    Route::post('/kontrakan/{kontrakan}/update-status', [KontrakanController::class, 'updateStatus'])->name('kontrakan.update-status');

    // CRUD Kontrakan
    Route::resource('kontrakan', KontrakanController::class);

    // ========== 🗺️ MAP VIEW LAUNDRY (BARU - HARUS SEBELUM RESOURCE!) ==========
    Route::get('/laundry/map', [LaundryController::class, 'map'])->name('laundry.map');

    // ========== BULK DELETE LAUNDRY (HARUS SEBELUM RESOURCE!) ==========
    Route::post('/laundry/bulk-destroy', [LaundryController::class, 'bulkDestroy'])->name('laundry.bulk-destroy');

    // CRUD Laundry
    Route::resource('laundry', LaundryController::class);

    // CRUD Kriteria SAW
    Route::resource('kriteria', KriteriaController::class);

    // -------------------------------------------------
    // SAW (2 halaman)
    // -------------------------------------------------
    Route::get('/saw', [SAWController::class, 'index'])->name('saw.index');
    Route::get('/saw/bobot', [SAWController::class, 'bobot'])->name('saw.bobot');
    Route::post('/saw/proses', [SAWController::class, 'proses'])->name('saw.proses');

    // -------------------------------------------------
    // 🆕 GALERI ROUTES (UNTUK MULTIPLE FOTO)
    // -------------------------------------------------
    Route::prefix('galeri')->name('galeri.')->group(function () {
        // Kontrakan
        Route::post('/kontrakan/{kontrakan}/upload', [GaleriController::class, 'uploadKontrakan'])->name('upload');
        Route::post('/kontrakan/{galeri}/set-primary', [GaleriController::class, 'setPrimaryKontrakan'])->name('set-primary');
        Route::delete('/kontrakan/{galeri}', [GaleriController::class, 'deleteKontrakan'])->name('delete');
        
        // Laundry
        Route::post('/laundry/{laundry}/upload', [GaleriController::class, 'uploadLaundry'])->name('upload-laundry');
        Route::post('/laundry/{galeri}/set-primary', [GaleriController::class, 'setPrimaryLaundry'])->name('set-primary-laundry');
        Route::delete('/laundry/{galeri}', [GaleriController::class, 'deleteLaundry'])->name('delete-laundry');
    });

    // Review Routes
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::post('/kontrakan/{kontrakan}', [ReviewController::class, 'storeKontrakan'])->name('kontrakan.store');
        Route::post('/laundry/{laundry}', [ReviewController::class, 'storeLaundry'])->name('laundry.store');
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
    });

    // Favorite Routes
    Route::prefix('favorites')->name('favorites.')->group(function () {
        Route::post('/kontrakan/{kontrakan}', [FavoriteController::class, 'toggleKontrakan'])->name('kontrakan.toggle');
        Route::post('/laundry/{laundry}', [FavoriteController::class, 'toggleLaundry'])->name('laundry.toggle');
    });

    // Legacy favorite routes (untuk kompatibilitas)
    Route::post('/favorite', [FavoriteController::class, 'toggleOld'])->name('favorite.toggle');

    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});