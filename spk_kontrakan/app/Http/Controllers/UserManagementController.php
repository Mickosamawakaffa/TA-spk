<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at');
            } else {
                $query->whereNotNull('deleted_at');
            }
        }

        $users = $query->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,super_admin',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'role.required' => 'Role harus dipilih',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        ActivityLog::log('create', "Membuat user baru: {$user->name}", 'User', $user->id, null, $user->toArray());

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,super_admin',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $oldValues = $user->toArray();

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        ActivityLog::log('update', "Memperbarui user: {$user->name}", 'User', $user->id, $oldValues, $user->toArray());

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun sendiri');
        }

        // Soft delete
        $user->delete();

        ActivityLog::log('delete', "Menghapus user: {$user->name}", 'User', $user->id);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus');
    }

    public function restore(User $user)
    {
        $user->restore();

        ActivityLog::log('restore', "Mengembalikan user: {$user->name}", 'User', $user->id);

        return redirect()->back()->with('success', 'User berhasil dikembalikan');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $count = 0;
        foreach ($validated['ids'] as $id) {
            $user = User::find($id);
            if ($user && $user->id !== auth()->id()) {
                $user->delete();
                $count++;
                ActivityLog::log('delete', "Menghapus user: {$user->name}", 'User', $user->id);
            }
        }

        return redirect()->back()->with('success', "$count user berhasil dihapus");
    }
}
