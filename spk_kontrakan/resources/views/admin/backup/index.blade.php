@extends('layouts.admin')

@section('title', 'Backup & Restore Database')

@section('content')
<div class="container-fluid px-4">
    <style>
        .backup-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .backup-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
    </style>

    <!-- Header -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 2rem; color: white; margin-bottom: 2rem;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="mb-2">
                    <i class="bi bi-cloud-arrow-down me-3"></i>Backup & Restore Database
                </h2>
                <p class="mb-0 fs-6">Kelola backup database untuk keamanan data</p>
            </div>
            <form action="{{ route('admin.backup.create') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-light fw-semibold" onclick="return confirm('Mulai backup database?')">
                    <i class="bi bi-cloud-check me-2"></i>Buat Backup Baru
                </button>
            </form>
        </div>
    </div>

    <!-- Alert -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Info Card -->
    <div class="alert alert-info border-0 rounded-3 mb-4">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Tips:</strong> Lakukan backup secara berkala untuk mengamankan data. Backup dibuat dalam format ZIP dan dapat di-download.
    </div>

    <!-- Backups List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-archive me-2"></i>Daftar Backup ({{ count($backups) }})
            </h5>
        </div>

        <div class="card-body p-0">
            @if(empty($backups))
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">Tidak ada backup ditemukan</h5>
                    <p class="text-muted mb-4">Klik tombol "Buat Backup Baru" untuk membuat backup pertama Anda</p>
                </div>
            @else
            <div class="row g-3 p-4">
                @foreach($backups as $backup)
                <div class="col-12">
                    <div class="backup-card card">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white;">
                                            <i class="bi bi-file-zip" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-semibold">{{ $backup['name'] }}</h6>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar me-1"></i>
                                                {{ date('d M Y H:i', $backup['date']) }}
                                                <span class="mx-2">•</span>
                                                <i class="bi bi-file-size me-1"></i>
                                                {{ number_format($backup['size'] / 1024 / 1024, 2) }} MB
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex gap-2 justify-content-md-end mt-3 mt-md-0">
                                        <a href="{{ route('admin.backup.download', $backup['name']) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-download me-1"></i>Download
                                        </a>

                                        <form action="{{ route('admin.backup.restore', $backup['name']) }}" method="POST" style="display: inline;" onsubmit="return confirm('Restore dari backup ini? Data saat ini akan ditimpa!');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Restore
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.backup.delete', $backup['name']) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus backup ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash me-1"></i>Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <!-- Backup Statistics -->
    <div class="row g-3 mt-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">
                        <i class="bi bi-shield-check me-2" style="color: #667eea;"></i>Backup Information
                    </h6>
                    <small class="text-muted d-block mb-2">
                        <strong>Total Backups:</strong> {{ count($backups) }}
                    </small>
                    <small class="text-muted d-block mb-2">
                        <strong>Total Size:</strong> {{ number_format(array_sum(array_column($backups, 'size')) / 1024 / 1024, 2) }} MB
                    </small>
                    @if(count($backups) > 0)
                    <small class="text-muted d-block">
                        <strong>Latest:</strong> {{ date('d M Y H:i', $backups[0]['date']) }}
                    </small>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm border-warning">
                <div class="card-body p-4 bg-warning bg-opacity-10">
                    <h6 class="fw-semibold mb-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>Reminder
                    </h6>
                    <small class="text-muted d-block">
                        ⚠️ Backup database secara rutin (minimal 1x sehari) untuk mencegah kehilangan data yang tidak dapat dipulihkan.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
