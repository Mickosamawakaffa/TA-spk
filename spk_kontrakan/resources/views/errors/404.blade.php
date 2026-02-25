@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')
<div class="container-fluid px-4">
    <div class="row align-items-center justify-content-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="text-center">
                <div class="mb-4">
                    <h1 class="display-1 fw-bold" style="color: #f5576c;">404</h1>
                    <p class="fs-4 fw-semibold mb-2">Halaman Tidak Ditemukan</p>
                    <p class="text-muted mb-4">
                        Maaf, halaman yang Anda cari tidak ditemukan. 
                        Mungkin URL sudah berubah atau halaman telah dihapus.
                    </p>
                </div>

                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="bi bi-house me-2"></i>Kembali ke Dashboard
                    </a>
                    <a href="javascript:history.back()" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Halaman Sebelumnya
                    </a>
                </div>

                <div class="mt-5">
                    <small class="text-muted">
                        <i class="bi bi-search me-1"></i>
                        Path: {{ request()->getPathInfo() }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    .container-fluid {
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        margin: 2rem auto;
    }

    .display-1 {
        font-size: 5rem;
        line-height: 1;
    }
</style>
@endsection
