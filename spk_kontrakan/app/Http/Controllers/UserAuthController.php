<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    /**
     * Halaman Login User
     */
    public function loginPage()
    {
        // Jika sudah login sebagai user, redirect ke halaman utama
        if (Auth::check() && Auth::user()->role === 'user') {
            return redirect()->route('welcome');
        }
        
        return view('auth.user-login');
    }

    /**
     * Proses Login User
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Cek apakah yang login adalah user biasa
            if ($user->role !== 'user') {
                Auth::logout();
                return back()->with('error', 'Akun ini bukan akun user. Silakan login di halaman admin.');
            }
            
            $request->session()->regenerate();
            
            // Redirect ke halaman yang diminta atau ke welcome
            $redirect = $request->input('redirect', route('welcome'));
            return redirect($redirect)->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        return back()->with('error', 'Email atau password salah!');
    }

    /**
     * Halaman Register User
     */
    public function registerPage()
    {
        return view('auth.user-register');
    }

    /**
     * Proses Register User
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:50',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed'
        ]);

        // Simpan ke tabel users dengan role USER
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'user'  // PAKSA SELALU USER
        ]);

        // Auto login setelah register
        Auth::login($user);

        return redirect()->route('welcome')->with('success', 'Akun berhasil dibuat! Selamat datang, ' . $user->name . '!');
    }

    /**
     * Logout User
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome')->with('success', 'Anda telah logout.');
    }
}
