@extends('layouts.admin')

@section('title', 'Admin Access - SPK Kontrakan & Laundry')

@section('content')
<style>
    .admin-access-hero {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
        padding: 80px 20px;
        border-radius: 15px;
        margin-bottom: 40px;
        text-align: center;
    }

    .security-notice {
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 30px;
        text-align: center;
    }

    .admin-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .admin-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
</style>

<div class="container py-5">
    <!-- Security Notice -->
    <div class="security-notice">
        <h5 class="mb-2">⚠️ Area Khusus Administrator</h5>
        <p class="mb-0">
            Halaman ini hanya untuk administrator yang telah terdaftar dan terotorisasi. 
            Semua aktivitas akses dipantau dan dicatat untuk keamanan sistem.
        </p>
    </div>

    <!-- Admin Access Hero -->
    <div class="admin-access-hero">
        <h1 class="display-4 fw-bold mb-3">🔐 Administrator Access</h1>
        <p class="lead mb-0">Sistem manajemen untuk pengelolaan data kontrakan dan laundry</p>
    </div>

    <!-- Admin Options -->
    <div class="row justify-content-center g-4">
        <!-- Login Card -->
        <div class="col-md-8 col-lg-6">
            <div class="card admin-card h-100">
                <div class="card-body text-center p-5">
                    <div class="mb-4" style="font-size: 5rem; color: #28a745;">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <h3 class="fw-bold mb-3">🔐 Login Administrator</h3>
                    <p class="text-muted mb-4">
                        Silakan masuk menggunakan kredensial administrator yang telah terdaftar dalam sistem.
                        Akses hanya diberikan kepada administrator yang berwenang.
                    </p>
                    <a href="{{ route('admin.login') }}" class="btn btn-success btn-lg w-100 px-4 py-3">
                        <i class="bi bi-key me-2"></i>Masuk ke Admin Panel
                    </a>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Hubungi administrator senior jika Anda memerlukan akses
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Guidelines -->
    <div class="row mt-5">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-bold">📋 Panduan Administrator</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success">✅ Yang Dapat Dilakukan:</h6>
                            <ul class="list-unstyled">
                                <li>✓ Mengelola data kontrakan</li>
                                <li>✓ Mengelola data laundry</li>
                                <li>✓ Mengatur kriteria penilaian</li>
                                <li>✓ Export data ke Excel/PDF</li>
                                <li>✓ Monitoring aktivitas sistem</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-danger">❌ Yang Tidak Diperbolehkan:</h6>
                            <ul class="list-unstyled">
                                <li>✗ Akses tanpa otoritas yang sah</li>
                                <li>✗ Manipulasi data atau sistem</li>
                                <li>✗ Membocorkan informasi sensitif</li>
                                <li>✗ Menggunakan untuk kepentingan pribadi</li>
                                <li>✗ Membagi kredensial dengan orang lain</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Home -->
    <div class="text-center mt-4">
        <a href="{{ route('welcome') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Beranda
        </a>
    </div>
</div>

<script>
// Log access attempt (for security monitoring)
console.log(`Admin access page viewed at: ${new Date().toISOString()}`);
</script>
@endsection
