@extends('layouts.admin')

@section('title', 'Input Bobot Kriteria')

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
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
        }
        
        .page-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .form-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        
        .section-header {
            color: #667eea;
            font-size: 1.1rem;
            font-weight: 700;
            padding-bottom: 1rem;
            border-bottom: 2px solid #667eea;
            margin-bottom: 1.5rem;
        }
        
        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.75rem 1.75rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('saw.index') }}">Metode SAW</a></li>
            <li class="breadcrumb-item active">Input Bobot</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="page-header mb-4">
        <h2 class="mb-0">⚖️ Input Bobot Kriteria</h2>
        <p class="mb-0 opacity-95">Tentukan tingkat kepentingan setiap kriteria untuk perhitungan SAW</p>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card form-card">
                <div class="card-body p-4">
                    <form action="{{ route('saw.proses') }}" method="POST">
                        @csrf
                        
                        <h5 class="section-header mb-0">Tentukan Bobot Kriteria</h5>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-currency-dollar text-success me-2"></i>Harga
                            </label>
                            <select name="bobot_harga" class="form-select" required>
                                <option value="0.1">Rendah</option>
                                <option value="0.3">Sedang</option>
                                <option value="0.5" selected>Tinggi</option>
                            </select>
                            <small class="text-muted">Semakin tinggi bobot, semakin penting faktor harga</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-pin-map text-danger me-2"></i>Jarak
                            </label>
                            <select name="bobot_jarak" class="form-select" required>
                                <option value="0.1">Rendah</option>
                                <option value="0.3" selected>Sedang</option>
                                <option value="0.5">Tinggi</option>
                            </select>
                            <small class="text-muted">Semakin tinggi bobot, semakin penting faktor jarak</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-star text-warning me-2"></i>Fasilitas
                            </label>
                            <select name="bobot_fasilitas" class="form-select" required>
                                <option value="0.1">Rendah</option>
                                <option value="0.3" selected>Sedang</option>
                                <option value="0.5">Tinggi</option>
                            </select>
                            <small class="text-muted">Semakin tinggi bobot, semakin penting faktor fasilitas</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-door-closed text-info me-2"></i>Jumlah Kamar
                            </label>
                            <select name="bobot_luas" class="form-select" required>
                                <option value="0.1">Rendah</option>
                                <option value="0.3">Sedang</option>
                                <option value="0.5" selected>Tinggi</option>
                            </select>
                            <small class="text-muted">Semakin tinggi bobot, semakin penting faktor jumlah kamar</small>
                        </div>

                        <button type="submit" class="btn btn-submit btn-lg w-100">
                            <i class="bi bi-calculator me-2"></i>Proses SAW
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
