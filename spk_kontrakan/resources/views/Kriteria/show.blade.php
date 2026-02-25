@extends('layouts.admin')

@section('title', 'Detail Kriteria')

@section('content')
<div class="container-fluid px-4">
    <style>
        .breadcrumb {
            background: transparent;
            border-radius: 8px;
            padding: 0;
            margin-bottom: 2rem;
        }
        
        .breadcrumb-item a {
            color: #667eea;
            font-weight: 500;
        }
        
        .breadcrumb-item.active {
            color: #764ba2;
            font-weight: 600;
        }
        
        .detail-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
        }
        
        .detail-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .detail-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .detail-card-header {
            color: #667eea;
            font-size: 1.1rem;
            font-weight: 700;
            padding: 1.25rem;
            border-bottom: 2px solid #667eea;
            background: #f8f9fa;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kriteria.index') }}">Kriteria</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>

    <!-- Detail Header -->
    <div class="detail-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">
                    <i class="bi bi-diagram-2 me-2"></i>{{ $kriteria->nama_kriteria }}
                </h2>
                <p class="mb-0 fs-6">Informasi lengkap kriteria penilaian</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('kriteria.edit', $kriteria->id) }}" class="btn btn-edit me-2">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="{{ route('kriteria.index') }}" class="btn btn-outline-light">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Info Card -->
        <div class="col-lg-9">
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="bi bi-info-circle me-2"></i>Informasi Kriteria
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Nama Kriteria -->
                        <div class="col-md-12">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-bookmark text-primary fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Nama Kriteria</small>
                                    <h5 class="mb-0 fw-semibold">{{ $kriteria->nama_kriteria }}</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Bobot -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-info bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-speedometer text-info fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Bobot Kriteria</small>
                                    <h5 class="mb-0 fw-semibold text-info">
                                        {{ $kriteria->bobot }}
                                    </h5>
                                    <small class="text-muted">atau {{ $kriteria->bobot * 100 }}%</small>
                                </div>
                            </div>
                        </div>

                        <!-- Tipe -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-{{ strtolower($kriteria->tipe) == 'benefit' ? 'success' : 'danger' }} bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-{{ strtolower($kriteria->tipe) == 'benefit' ? 'arrow-up-circle' : 'arrow-down-circle' }} text-{{ strtolower($kriteria->tipe) == 'benefit' ? 'success' : 'danger' }} fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Tipe Kriteria</small>
                                    @if(strtolower($kriteria->tipe) == 'benefit')
                                        <span class="badge bg-success px-3 py-2 fs-6">
                                            <i class="bi bi-arrow-up-circle me-1"></i>Benefit
                                        </span>
                                        <small class="d-block mt-2 text-muted">Semakin tinggi nilai semakin baik</small>
                                    @else
                                        <span class="badge bg-danger px-3 py-2 fs-6">
                                            <i class="bi bi-arrow-down-circle me-1"></i>Cost
                                        </span>
                                        <small class="d-block mt-2 text-muted">Semakin rendah nilai semakin baik</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="col-md-12">
                            <div class="d-flex align-items-start">
                                <div class="bg-secondary bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-chat-left-text text-secondary fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Keterangan</small>
                                    <p class="mb-0">
                                        {{ $kriteria->keterangan ?: 'Tidak ada keterangan' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Penjelasan Tipe -->
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border-0 bg-success bg-opacity-10 h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-2 text-success">
                                <i class="bi bi-arrow-up-circle me-2"></i>Kriteria Benefit
                            </h6>
                            <p class="mb-2 small">Nilai yang lebih tinggi lebih disukai.</p>
                            <p class="mb-0 small text-muted">
                                <strong>Contoh:</strong> Fasilitas, Jumlah Kamar, Kecepatan Layanan
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 bg-danger bg-opacity-10 h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-2 text-danger">
                                <i class="bi bi-arrow-down-circle me-2"></i>Kriteria Cost
                            </h6>
                            <p class="mb-2 small">Nilai yang lebih rendah lebih disukai.</p>
                            <p class="mb-0 small text-muted">
                                <strong>Contoh:</strong> Harga, Jarak, Waktu Tempuh
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Info -->
        <div class="col-lg-3">
            <!-- Quick Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-graph-up text-primary me-2"></i>Informasi Bobot
                    </h6>
                    <div class="mb-4">
                        <small class="text-muted d-block mb-2">Bobot dalam Persen</small>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $kriteria->bobot * 100 }}%">
                                <strong>{{ $kriteria->bobot * 100 }}%</strong>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Bobot Desimal</small>
                        <h4 class="mb-0 text-info">{{ $kriteria->bobot }}</h4>
                    </div>
                    <hr>
                    <div class="alert alert-light mb-0">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Total bobot semua kriteria disarankan = 1 atau 100%
                        </small>
                    </div>
                </div>
            </div>

            <!-- Timestamp Info -->
            <div class="card border-0 bg-light mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-clock-history text-primary me-2"></i>Riwayat
                    </h6>
                    <div class="mb-3">
                        <small class="text-muted d-block">Dibuat</small>
                        <strong>{{ $kriteria->created_at->format('d M Y, H:i') }} WIB</strong>
                    </div>
                    @if($kriteria->updated_at != $kriteria->created_at)
                    <div>
                        <small class="text-muted d-block">Terakhir Diupdate</small>
                        <strong>{{ $kriteria->updated_at->format('d M Y, H:i') }} WIB</strong>
                        <small class="text-muted d-block mt-1">
                            ({{ $kriteria->updated_at->diffForHumans() }})
                        </small>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-gear text-secondary me-2"></i>Aksi
                    </h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('kriteria.edit', $kriteria->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>Edit Data
                        </a>
                        <form action="{{ route('kriteria.destroy', $kriteria->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kriteria {{ $kriteria->nama_kriteria }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash me-2"></i>Hapus Data
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
    }
</style>
@endsection
