@extends('layouts.admin')

@section('title', 'Tambah User')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Kelola User</a></li>
                    <li class="breadcrumb-item active">Tambah User</li>
                </ol>
            </nav>

            <!-- Form Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-person-plus-fill me-2" style="color: #667eea;"></i>Tambah User Baru
                    </h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold">
                                <i class="bi bi-person me-2"></i>Nama User
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name"
                                placeholder="Masukkan nama lengkap"
                                value="{{ old('name') }}"
                            >
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold">
                                <i class="bi bi-envelope me-2"></i>Email
                            </label>
                            <input 
                                type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                id="email" 
                                name="email"
                                placeholder="user@example.com"
                                value="{{ old('email') }}"
                            >
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="role" class="form-label fw-semibold">
                                <i class="bi bi-shield me-2"></i>Role
                            </label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role">
                                <option value="">-- Pilih Role --</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Super Admin memiliki akses penuh ke semua fitur sistem
                            </small>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">
                                <i class="bi bi-lock me-2"></i>Password
                            </label>
                            <input 
                                type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                id="password" 
                                name="password"
                                placeholder="Minimal 8 karakter"
                            >
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">
                                <i class="bi bi-lock-check me-2"></i>Konfirmasi Password
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password_confirmation" 
                                name="password_confirmation"
                                placeholder="Ulangi password"
                            >
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-check-circle me-2"></i>Simpan User
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary flex-fill">
                                <i class="bi bi-arrow-left me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
