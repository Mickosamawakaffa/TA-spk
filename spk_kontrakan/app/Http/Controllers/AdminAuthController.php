<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
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

        // Gunakan guard 'admin' - ambil dari tabel admins
        if (Auth::guard('admin')->attempt($credentials)) {
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
            'email'    => 'required|email|unique:admins,email',
            'password' => 'required|min:6|confirmed'
        ], [
            'name.required' => 'Nama wajib diisi',
            'name.max' => 'Nama maksimal 50 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok'
        ]);

        // Simpan ke tabel admins (ROLE SELALU admin)
        Admin::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'admin'  // Hanya admin, tidak bisa super_admin
        ]);

        return redirect()->route('admin.login')->with('success', 'Akun admin berhasil dibuat!');
    }

    // Logout Admin
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}