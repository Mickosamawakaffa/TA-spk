<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ============================
        // Rate Limiting Configuration
        // ============================

        // Default API rate limit: 60 requests per minute
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(
                $request->user()?->id ?: $request->ip()
            )->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak request. Maksimal 60 request per menit.',
                    'error_code' => 'RATE_LIMIT_EXCEEDED',
                ], 429);
            });
        });

        // Login rate limit: 5 attempts per minute (brute force protection)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(
                $request->input('email', '') . '|' . $request->ip()
            )->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak percobaan login. Coba lagi dalam 1 menit.',
                    'error_code' => 'LOGIN_THROTTLED',
                ], 429);
            });
        });

        // Register rate limit: 3 per hour per IP
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(3)->by(
                $request->ip()
            )->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak registrasi dari IP ini. Coba lagi nanti.',
                    'error_code' => 'REGISTER_THROTTLED',
                ], 429);
            });
        });

        // SAW calculation rate limit: 30 per minute
        RateLimiter::for('saw', function (Request $request) {
            return Limit::perMinute(30)->by(
                $request->user()?->id ?: $request->ip()
            )->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak perhitungan SAW. Coba lagi nanti.',
                    'error_code' => 'SAW_THROTTLED',
                ], 429);
            });
        });

        // Export rate limit: 10 per minute
        RateLimiter::for('export', function (Request $request) {
            return Limit::perMinute(10)->by(
                $request->user()?->id ?: $request->ip()
            );
        });
    }
}
