<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // Alias middleware admin
        $middleware->alias([
            'auth.admin' => \App\Http\Middleware\AdminAuth::class,
        ]);

        // Redirect guest ke admin.login
        $middleware->redirectGuestsTo(fn () => route('admin.login'));

        // Rate limiting untuk API
        $middleware->api(prepend: [
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // ============================
        // Centralized API Error Handler
        // ============================

        // 404 Not Found
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource tidak ditemukan',
                    'error_code' => 'NOT_FOUND',
                ], 404);
            }
        });

        // 405 Method Not Allowed
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'HTTP method tidak diizinkan untuk endpoint ini',
                    'error_code' => 'METHOD_NOT_ALLOWED',
                ], 405);
            }
        });

        // 429 Too Many Requests (Rate Limiting)
        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak request. Silakan coba lagi nanti.',
                    'error_code' => 'TOO_MANY_REQUESTS',
                    'retry_after' => (int) $retryAfter,
                ], 429);
            }
        });

        // 401 Authentication Error
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid atau sudah kadaluarsa. Silakan login kembali.',
                    'error_code' => 'UNAUTHENTICATED',
                ], 401);
            }
        });

        // 422 Validation Error
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'error_code' => 'VALIDATION_ERROR',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // 500 Generic Server Error (catch-all for API)
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                $isDebug = config('app.debug');
                return response()->json([
                    'success' => false,
                    'message' => $isDebug ? $e->getMessage() : 'Terjadi kesalahan pada server',
                    'error_code' => 'SERVER_ERROR',
                    'debug' => $isDebug ? [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ] : null,
                ], 500);
            }
        });

    })->create();