@extends('layouts.app')

@section('title', 'Terjadi Kesalahan Server')

@section('content')
<div class="container-fluid px-4">
    <div class="row align-items-center justify-content-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="text-center">
                <div class="mb-4">
                    <h1 class="display-1 fw-bold" style="color: #ff6b6b;">500</h1>
                    <p class="fs-4 fw-semibold mb-2">Terjadi Kesalahan Server</p>
                    <p class="text-muted mb-4">
                        Maaf, terjadi kesalahan di server. Tim kami telah dinotifikasi dan sedang mengatasinya. 
                        Silakan coba lagi nanti.
                    </p>
                </div>

                <div class="alert alert-danger border-0 rounded-3 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Error ID:</strong> {{ $exception->getCode() ?? 'Unknown' }}
                </div>

                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="bi bi-house me-2"></i>Kembali ke Dashboard
                    </a>
                    <a href="javascript:location.reload()" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                    </a>
                </div>

                <div class="mt-5 p-3 bg-light rounded-3">
                    <small class="text-muted d-block">
                        <i class="bi bi-info-circle me-1"></i>
                        Path: {{ request()->getPathInfo() }}
                    </small>
                    <small class="text-muted d-block mt-2">
                        <i class="bi bi-clock me-1"></i>
                        Waktu: {{ now()->format('d M Y H:i:s') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #ff6b6b 0%, #ff8787 100%);
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
