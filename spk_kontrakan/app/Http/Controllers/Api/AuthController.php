<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Register user baru
     */
    public function register(RegisterRequest $request)
    {
        try {
            Log::info('Register attempt', ['email' => $request->email, 'name' => $request->name]);
            
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'user',
            ]);

            // Refresh user to ensure all fields are loaded from DB
            $user->refresh();

            // Fire registered event to trigger verification email notification
            event(new \Illuminate\Auth\Events\Registered($user));

            $token = $user->createToken('mobile-app-token')->plainTextToken;

            Log::info('Registration successful', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error during registration', [
                'email' => $request->email,
                'message' => $e->getMessage(),
                'sql' => $e->getSql() ?? 'N/A',
                'bindings' => $e->getBindings() ?? []
            ]);
            return response()->json([
                'success' => false,
                // ✅ Do not leak DB details to clients
                'message' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.',
                'error_code' => 'DB_ERROR',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Registration error', [
                'email' => $request->email,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                // ✅ Do not leak internal exception details to clients
                'message' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.',
                'error_code' => 'REGISTRATION_FAILED',
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
                'error_code' => 'INVALID_CREDENTIALS',
            ], 401);
        }

        $token = $user->createToken('mobile-app-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 200);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ], 200);
    }

    /**
     * Update profile user
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile berhasil diupdate',
            'data' => $user
        ], 200);
    }
}
