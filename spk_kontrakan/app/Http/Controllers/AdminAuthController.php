<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    // Halaman Login
    public function loginPage()
    {
        return view('auth.admin-login');
    }

    // Proses Login Admin
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // FIXED: Hapus guard('admin'), pakai default Auth
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->with('error', 'Email atau password salah!');
    }

    // Halaman Register
    public function registerPage()
    {
        return view('auth.admin-register');
    }

    // Proses Register Admin
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:50',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        // Simpan ke tabel users (ROLE SELALU admin - tidak bisa diubah user)
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'admin'  // PAKSA SELALU ADMIN - tidak bisa jadi super_admin lewat register
        ]);

        return redirect()->route('admin.login')->with('success', 'Akun admin berhasil dibuat!');
    }

    // Logout Admin
    public function logout(Request $request)
    {
        // FIXED: Hapus guard('admin'), pakai default Auth
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}