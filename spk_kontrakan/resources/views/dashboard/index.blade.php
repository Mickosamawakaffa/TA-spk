@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<!-- Load Skeleton Loader Script -->
@include('components.skeleton-script')

<div class="container-fluid px-2 px-md-4">
<style>
        .header-dashboard {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 2.5rem 2rem;
            color: white;
            margin-bottom: 2.5rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .header-dashboard::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .header-dashboard h2 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }
        
        .header-dashboard p {
            opacity: 0.95;
            margin-bottom: 0;
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
        }
        
        .stats-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: none !important;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
        }
        
        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            filter: brightness(1.1);
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
            z-index: 0;
        }
        
        .stats-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        }
        
        .stats-card:hover::before {
            opacity: 1;
        }
        
        .stats-card .btn {
            pointer-events: auto;
            cursor: pointer;
        }
        
        .chart-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        
        .chart-card .card-header {
            border-bottom: 2px solid #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            padding: 1.5rem;
            font-weight: 700;
        }
        
        .section-header {
            color: #667eea;
            font-size: 1.2rem;
            font-weight: 800;
            padding-bottom: 1rem;
            border-bottom: 3px solid #667eea;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .section-header::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        /* Mobile Responsive Enhancements */
        @media (max-width: 768px) {
            .header-dashboard {
                padding: 2rem 1.5rem;
                margin-bottom: 2rem;
            }
            
            .header-dashboard h2 {
                font-size: 1.8rem;
            }
            
            .header-dashboard p {
                font-size: 1rem;
            }
            
            .stats-card {
                margin-bottom: 1.5rem;
            }
            
            .chart-card .card-header {
                padding: 1.25rem;
            }
            
            .section-header {
                font-size: 1.1rem;
            }
        }
        
        @media (max-width: 480px) {
            .header-dashboard {
                padding: 1.5rem 1rem;
            }
            
            .header-dashboard h2 {
                font-size: 1.6rem;
            }
            
            .stats-card:hover {
                transform: translateY(-4px) scale(1.01);
            }
        }

        /* Skeleton Loader Styles */
        .skeleton {
            background: linear-gradient(90deg, #e0e0e0 25%, #f0f0f0 50%, #e0e0e0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 4px;
        }

        html.dark-mode .skeleton {
            background: linear-gradient(90deg, #3a3a3a 25%, #4a4a4a 50%, #3a3a3a 75%);
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Konsistensi Warna Admin - Purple Theme */
        .btn-admin-outline {
            color: #667eea;
            border: 2px solid #667eea;
            background: transparent;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-admin-outline:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-admin-solid {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-admin-solid:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .text-admin-primary {
            color: #667eea !important;
        }
        
        .bg-admin-light {
            background: rgba(102, 126, 234, 0.1) !important;
        }
        
        .border-admin {
            border-color: #667eea !important;
        }
    </style>
    
    <!-- Header Section -->
    <div class="header-dashboard">
        <h2 class="mb-2">📊 Dashboard Analytics</h2>
        <p class="mb-0">Selamat datang, <strong>{{ Auth::user()->name }}!</strong> 👋</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 g-md-4 mb-4 mb-md-5" id="statsContainer">
        <!-- Card Kontrakan - Purple Theme -->
        <div class="col-6 col-lg-3">
            <div class="card stats-card h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none !important; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.35); position: relative; z-index: 1;">
                <div class="card-body text-white p-3 p-md-4" style="position: relative; z-index: 2;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <p class="mb-2 opacity-75 small fw-semibold"><i class="bi bi-house-door me-1"></i>Total Kontrakan</p>
                            <h2 class="mb-2 mb-md-3 fw-bold display-6 counter" data-target="{{ $jumlahKontrakan }}">0</h2>
                            <p class="mb-0 opacity-85 small d-none d-md-block">
                                <i class="bi bi-currency-dollar me-1"></i>
                                Rp {{ number_format($avgHargaKontrakan, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="rounded-circle p-2 p-md-3 d-flex align-items-center justify-content-center d-none d-sm-flex" style="min-width: 50px; min-height: 50px; background: rgba(255,255,255,0.25);">
                            <i class="bi bi-building" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <button type="button" class="btn btn-light btn-sm w-100 fw-semibold d-none d-md-block" style="position: relative; z-index: 3; color: #764ba2;" data-bs-toggle="modal" data-bs-target="#kontrakanModal">
                        <i class="bi bi-arrow-right me-2"></i>Lihat Detail
                    </button>
                </div>
            </div>
        </div>

        <!-- Card Laundry - Purple Theme -->
        <div class="col-6 col-lg-3">
            <div class="card stats-card h-100" style="background: linear-gradient(135deg, #818cf8 0%, #667eea 100%); border: none !important; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.35); position: relative; z-index: 1;">
                <div class="card-body text-white p-3 p-md-4" style="position: relative; z-index: 2;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <p class="mb-2 opacity-75 small fw-semibold"><i class="bi bi-droplet me-1"></i>Total Laundry</p>
                            <h2 class="mb-2 mb-md-3 fw-bold display-6 counter" data-target="{{ $jumlahLaundry }}">0</h2>
                            <p class="mb-0 opacity-85 small d-none d-md-block">
                                <i class="bi bi-clock-history me-1"></i>
                                {{ $avgKecepatan ?? 'N/A' }} jam rata-rata
                            </p>
                        </div>
                        <div class="rounded-circle p-2 p-md-3 d-flex align-items-center justify-content-center d-none d-sm-flex" style="min-width: 50px; min-height: 50px; background: rgba(255,255,255,0.25);">
                            <i class="bi bi-water" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <button type="button" class="btn btn-light btn-sm w-100 fw-semibold d-none d-md-block" style="position: relative; z-index: 3; color: #667eea;" data-bs-toggle="modal" data-bs-target="#laundryModal">
                        <i class="bi bi-arrow-right me-2"></i>Lihat Detail
                    </button>
                </div>
            </div>
        </div>

        <!-- Card Kriteria -->
        <div class="col-6 col-lg-3">
            <div class="card stats-card h-100" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #4338ca 100%); border: none !important; box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3); position: relative; z-index: 1;">
                <div class="card-body text-white p-3 p-md-4" style="position: relative; z-index: 2;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <p class="mb-2 opacity-75 small fw-semibold"><i class="bi bi-sliders me-1"></i>Total Kriteria</p>
                            <h2 class="mb-2 mb-md-3 fw-bold display-6 counter" data-target="{{ $jumlahKriteria }}">0</h2>
                            <p class="mb-0 opacity-85 small d-none d-md-block">
                                <i class="bi bi-star-fill me-1"></i>
                                Untuk perhitungan SAW
                            </p>
                        </div>
                        <div class="rounded-circle p-2 p-md-3 d-flex align-items-center justify-content-center d-none d-sm-flex" style="min-width: 50px; min-height: 50px; background: rgba(255,255,255,0.25);">
                            <i class="bi bi-list-check" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <button type="button" class="btn btn-light btn-sm w-100 fw-semibold d-none d-md-block" style="position: relative; z-index: 3; color: #4338ca;" data-bs-toggle="modal" data-bs-target="#kriteriaModal">
                        <i class="bi bi-arrow-right me-2"></i>Lihat Detail
                    </button>
                </div>
            </div>
        </div>

        <!-- Card SAW - Amber/Gold Tone -->
        <div class="col-6 col-lg-3">
            <div class="card stats-card h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%); border: none !important; box-shadow: 0 8px 25px rgba(217, 119, 6, 0.35); position: relative; z-index: 1;">
                <div class="card-body text-white p-3 p-md-4" style="position: relative; z-index: 2;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <p class="mb-2 opacity-75 small fw-semibold"><i class="bi bi-calculator me-1"></i>Sistem Rekomendasi</p>
                            <h2 class="mb-2 mb-md-3 fw-bold display-6">Analisis</h2>
                            <p class="mb-0 opacity-85 small d-none d-md-block">
                                <i class="bi bi-diagram-3 me-1"></i>
                                Keputusan Terpadu
                            </p>
                        </div>
                        <div class="rounded-circle p-2 p-md-3 d-flex align-items-center justify-content-center d-none d-sm-flex" style="min-width: 50px; min-height: 50px; background: rgba(255,255,255,0.25);">
                            <i class="bi bi-calculator" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <button type="button" class="btn btn-light btn-sm w-100 fw-semibold d-none d-md-block" style="position: relative; z-index: 3; color: #b45309;" data-bs-toggle="modal" data-bs-target="#sawModal">
                        <i class="bi bi-arrow-right me-2"></i>Lihat Detail
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-3 g-md-4 mb-4 mb-md-5">
        <!-- Bar Chart: Perbandingan Harga -->
        <div class="col-12 col-xl-8">
            <div class="card chart-card h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold" style="color: #667eea;">
                        <i class="bi bi-bar-chart-fill me-2"></i>
                        <span class="d-none d-md-inline">Perbandingan Harga (Top 5)</span>
                        <span class="d-md-none">Harga (Top 5)</span>
                    </h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <canvas id="hargaChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Donut Charts: Distribusi Jarak -->
        <div class="col-12 col-xl-4">
            <div class="card chart-card h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold" style="color: #667eea;">
                        <i class="bi bi-pie-chart-fill me-2"></i>
                        <span class="d-none d-md-inline">Distribusi Jarak</span>
                        <span class="d-md-none">Jarak</span>
                    </h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <canvas id="jarakChart" height="200"></canvas>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2 py-2 border-bottom">
                            <span class="small"><span class="badge" style="background-color: #198754;">●</span> <span class="d-none d-sm-inline">Dekat (≤500m)</span><span class="d-sm-none">Dekat</span></span>
                            <strong style="color: #667eea;">{{ $jarakKontrakan['dekat'] + $jarakLaundry['dekat'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 py-2 border-bottom">
                            <span class="small"><span class="badge" style="background-color: #ffc107;">●</span> <span class="d-none d-sm-inline">Sedang (501-1000m)</span><span class="d-sm-none">Sedang</span></span>
                            <strong style="color: #667eea;">{{ $jarakKontrakan['sedang'] + $jarakLaundry['sedang'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="small"><span class="badge" style="background-color: #dc3545;">●</span> <span class="d-none d-sm-inline">Jauh (>1000m)</span><span class="d-sm-none">Jauh</span></span>
                            <strong style="color: #667eea;">{{ $jarakKontrakan['jauh'] + $jarakLaundry['jauh'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Line Chart: Trend Data -->
    <div class="row g-3 g-md-4 mb-4 mb-md-5">
        <div class="col-12">
            <div class="card chart-card">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold" style="color: #667eea;">
                        <i class="bi bi-graph-up me-2"></i>
                        <span class="d-none d-md-inline">Trend Data (6 Bulan Terakhir)</span>
                        <span class="d-md-none">Trend Data</span>
                    </h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <canvas id="trendChart" height="60"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card chart-card">
                <div class="card-body">
                    <h5 class="card-title mb-4 fw-bold" style="color: #667eea;">
                        <i class="bi bi-lightning-charge me-2"></i>
                        Quick Actions
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('kontrakan.create') }}" class="btn btn-admin-outline w-100 py-3">
                                <i class="bi bi-building me-2"></i> Tambah Kontrakan
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('laundry.create') }}" class="btn btn-admin-outline w-100 py-3">
                                <i class="bi bi-water me-2"></i> Tambah Laundry
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('kriteria.create') }}" class="btn btn-admin-outline w-100 py-3">
                                <i class="bi bi-list-check me-2"></i> Tambah Kriteria
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('saw.index') }}" class="btn btn-admin-solid w-100 py-3">
                                <i class="bi bi-calculator me-2"></i> Sistem Analisis
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Section -->
    <div class="row g-4 mb-4">
        <!-- Activity Stats -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-2 opacity-75 small fw-semibold">Total Review</p>
                            <h3 class="fw-bold mb-0">{{ $totalReviews ?? 0 }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; opacity: 0.3;">⭐</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-2 opacity-75 small fw-semibold">System Status</p>
                            <h3 class="fw-bold mb-0"><i class="bi bi-check-circle-fill"></i> Aktif</h3>
                        </div>
                        <div style="font-size: 2.5rem; opacity: 0.3;">✓</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Last Backup -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%); color: white; border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-2 opacity-75 small fw-semibold">Database Size</p>
                            <h3 class="fw-bold mb-0">{{ number_format(round((($jumlahKontrakan + $jumlahLaundry) * 0.15) / 1024, 2), 2, ',', '.') }} MB</h3>
                        </div>
                        <div style="font-size: 2.5rem; opacity: 0.3;">💾</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #fd7e14 0%, #fd7e14 100%); color: white; border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-2 opacity-75 small fw-semibold">Admin Users</p>
                            <h3 class="fw-bold mb-0">{{ $totalAdmins ?? 1 }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; opacity: 0.3;">👤</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Data Tables -->
    <div class="row g-4">
        <!-- Recent Kontrakan -->
        <div class="col-lg-6">
            <div class="card chart-card">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold" style="color: #667eea;">
                        <i class="bi bi-building me-2"></i>
                        Data Kontrakan Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentKontrakan && $recentKontrakan->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Lokasi</th>
                                    <th class="text-end">Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentKontrakan as $k)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $k->nama }}</div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt-fill me-1"></i>{{ $k->lokasi ?? $k->alamat }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge px-3 py-2" style="background-color: rgba(102, 126, 234, 0.15); color: #667eea;">
                                            Rp {{ number_format($k->harga, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2 mb-0">Belum ada data kontrakan</p>
                    </div>
                    @endif
                    <div class="mt-3">
                        <a href="{{ route('kontrakan.index') }}" class="btn btn-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <i class="bi bi-arrow-right-circle me-1"></i> Lihat Semua Data
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Laundry -->
        <div class="col-lg-6">
            <div class="card chart-card">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold" style="color: #667eea;">
                        <i class="bi bi-basket3 me-2"></i>
                        Data Laundry Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentLaundry && $recentLaundry->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Lokasi</th>
                                    <th class="text-end">Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLaundry as $l)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $l->nama }}</div>
                                        @if($l->layanan && $l->layanan->count() > 0)
                                            <small class="text-muted">
                                                {{ $l->layanan->count() }} layanan
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt-fill me-1"></i>{{ $l->alamat ?? '-' }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        @if($l->layanan && $l->layanan->count() > 0)
                                            @php
                                                $minHarga = $l->layanan->min('harga');
                                            @endphp
                                            <span class="badge bg-success-subtle text-success px-3 py-2">
                                                Rp {{ number_format($minHarga, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary px-3 py-2">
                                                -
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2 mb-0">Belum ada data laundry</p>
                    </div>
                    @endif
                    <div class="mt-3">
                        <a href="{{ route('laundry.index') }}" class="btn btn-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <i class="bi bi-arrow-right-circle me-1"></i> Lihat Semua Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kontrakan -->
<div class="modal fade" id="kontrakanModal" tabindex="-1" aria-labelledby="kontrakanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1rem 1.25rem;">
                <h6 class="modal-title mb-0" id="kontrakanModalLabel">
                    <i class="bi bi-building me-2"></i>Detail Kontrakan
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 1.25rem;">
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center p-2">
                                <i class="bi bi-building-fill" style="font-size: 1.5rem; color: #667eea;"></i>
                                <h5 class="mt-1 mb-0 fw-bold" style="color: #667eea; font-size: 1.25rem;">{{ $jumlahKontrakan }}</h5>
                                <p class="mb-0 text-muted" style="font-size: 0.7rem;">Total Kontrakan</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center p-2">
                                <i class="bi bi-currency-dollar" style="font-size: 1.5rem; color: #667eea;"></i>
                                <h6 class="mt-1 mb-0 fw-bold" style="color: #667eea; font-size: 0.85rem;">Rp {{ number_format($avgHargaKontrakan / 1000, 0) }}k</h6>
                                <p class="mb-0 text-muted" style="font-size: 0.7rem;">Rata-rata Harga</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center p-2">
                                <i class="bi bi-geo-alt-fill" style="font-size: 1.5rem; color: #667eea;"></i>
                                <h5 class="mt-1 mb-0 fw-bold" style="color: #667eea; font-size: 1.25rem;">{{ $jarakKontrakan['dekat'] + $jarakKontrakan['sedang'] + $jarakKontrakan['jauh'] }}</h5>
                                <p class="mb-0 text-muted" style="font-size: 0.7rem;">Lokasi Tersedia</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Kontrakan Terbaru</h6>
                    <span class="badge" style="font-size: 0.7rem; background-color: #667eea;">{{ $recentKontrakan->count() ?? 0 }} data</span>
                </div>
                @if($recentKontrakan && $recentKontrakan->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentKontrakan as $k)
                        <div class="list-group-item px-0 py-2">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1 me-2">
                                    <h6 class="mb-1 fw-semibold" style="font-size: 0.875rem;">{{ $k->nama }}</h6>
                                    <p class="mb-0 text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-geo-alt me-1"></i>{{ Str::limit($k->lokasi ?? $k->alamat, 30) }}
                                    </p>
                                </div>
                                <span class="badge" style="font-size: 0.75rem; white-space: nowrap; background-color: #667eea;">
                                    Rp {{ number_format($k->harga / 1000, 0) }}k
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-3 mb-0" style="font-size: 0.875rem;">Belum ada data kontrakan</p>
                @endif
            </div>
            <div class="modal-footer" style="padding: 0.75rem 1.25rem;">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('kontrakan.index') }}" class="btn btn-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <i class="bi bi-arrow-right-circle me-1"></i>Lihat Semua
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Laundry -->
<div class="modal fade" id="laundryModal" tabindex="-1" aria-labelledby="laundryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 1rem 1.25rem;">
                <h6 class="modal-title mb-0" id="laundryModalLabel">
                    <i class="bi bi-water me-2"></i>Detail Laundry
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 1.25rem;">
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center p-2">
                                <i class="bi bi-water" style="font-size: 1.5rem; color: #f5576c;"></i>
                                <h5 class="mt-1 mb-0 fw-bold" style="color: #f5576c; font-size: 1.25rem;">{{ $jumlahLaundry }}</h5>
                                <p class="mb-0 text-muted" style="font-size: 0.7rem;">Total Laundry</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center p-2">
                                <i class="bi bi-clock-history" style="font-size: 1.5rem; color: #f5576c;"></i>
                                <h5 class="mt-1 mb-0 fw-bold" style="color: #f5576c; font-size: 1.25rem;">{{ $avgKecepatan ?? 'N/A' }}</h5>
                                <p class="mb-0 text-muted" style="font-size: 0.7rem;">Jam Rata-rata</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center p-2">
                                <i class="bi bi-geo-alt-fill" style="font-size: 1.5rem; color: #f5576c;"></i>
                                <h5 class="mt-1 mb-0 fw-bold" style="color: #f5576c; font-size: 1.25rem;">{{ $jarakLaundry['dekat'] + $jarakLaundry['sedang'] + $jarakLaundry['jauh'] }}</h5>
                                <p class="mb-0 text-muted" style="font-size: 0.7rem;">Lokasi Tersedia</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Laundry Terbaru</h6>
                    <span class="badge" style="background: #f5576c; font-size: 0.7rem;">{{ $recentLaundry->count() ?? 0 }} data</span>
                </div>
                @if($recentLaundry && $recentLaundry->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentLaundry as $l)
                        <div class="list-group-item px-0 py-2">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1 me-2">
                                    <h6 class="mb-1 fw-semibold" style="font-size: 0.875rem;">{{ $l->nama }}</h6>
                                    <p class="mb-0 text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-geo-alt me-1"></i>{{ Str::limit($l->alamat ?? '-', 25) }}
                                    </p>
                                    @if($l->layanan && $l->layanan->count() > 0)
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ $l->layanan->count() }} layanan</small>
                                    @endif
                                </div>
                                @if($l->layanan && $l->layanan->count() > 0)
                                    <span class="badge bg-success" style="font-size: 0.75rem; white-space: nowrap;">
                                        Rp {{ number_format($l->layanan->min('harga') / 1000, 0) }}k
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-3 mb-0" style="font-size: 0.875rem;">Belum ada data laundry</p>
                @endif
            </div>
            <div class="modal-footer" style="padding: 0.75rem 1.25rem;">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('laundry.index') }}" class="btn btn-sm" style="background: #f5576c; color: white;">
                    <i class="bi bi-arrow-right-circle me-1"></i>Lihat Semua
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kriteria -->
<div class="modal fade" id="kriteriaModal" tabindex="-1" aria-labelledby="kriteriaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 1rem 1.25rem;">
                <h6 class="modal-title mb-0" id="kriteriaModalLabel">
                    <i class="bi bi-list-check me-2"></i>Detail Kriteria
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 1.25rem;">
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center p-2">
                                <i class="bi bi-list-check" style="font-size: 1.5rem; color: #4facfe;"></i>
                                <h5 class="mt-1 mb-0 fw-bold" style="color: #4facfe; font-size: 1.25rem;">{{ $jumlahKriteria }}</h5>
                                <p class="mb-0 text-muted" style="font-size: 0.7rem;">Total Kriteria</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center p-2">
                                <i class="bi bi-star-fill" style="font-size: 1.5rem; color: #4facfe;"></i>
                                <h5 class="mt-1 mb-0 fw-bold" style="color: #4facfe; font-size: 1.25rem;">SAW</h5>
                                <p class="mb-0 text-muted" style="font-size: 0.7rem;">Metode Perhitungan</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h6 class="fw-bold mb-2" style="font-size: 0.9rem;">Informasi Kriteria</h6>
                <div class="alert alert-info py-2 px-3 mb-3" style="font-size: 0.8rem;">
                    <i class="bi bi-info-circle me-1"></i>
                    Kriteria digunakan untuk penilaian dengan metode SAW (Simple Additive Weighting) untuk rekomendasi terbaik.
                </div>
                
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0 py-2">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill me-2" style="font-size: 1rem; color: #667eea;"></i>
                                <div>
                                    <h6 class="mb-0" style="font-size: 0.85rem;">Kriteria Aktif</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">Digunakan dalam perhitungan SAW</small>
                                </div>
                            </div>
                            <span class="badge" style="font-size: 0.75rem; background-color: #667eea;">{{ $jumlahKriteria }}</span>
                        </div>
                    </div>
                    <div class="list-group-item px-0 py-2">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calculator me-2" style="font-size: 1rem; color: #667eea;"></i>
                                <div>
                                    <h6 class="mb-0" style="font-size: 0.85rem;">Bobot Total</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">Total bobot harus 100%</small>
                                </div>
                            </div>
                            <span class="badge" style="font-size: 0.75rem; background-color: #667eea;">100%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 0.75rem 1.25rem;">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('kriteria.index') }}" class="btn btn-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <i class="bi bi-arrow-right-circle me-1"></i>Lihat Semua
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal SAW -->
<div class="modal fade" id="sawModal" tabindex="-1" aria-labelledby="sawModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; padding: 1rem 1.25rem;">
                <h6 class="modal-title mb-0" id="sawModalLabel">
                    <i class="bi bi-calculator me-2"></i>Sistem Rekomendasi
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 1.25rem;">
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center p-2">
                                <i class="bi bi-calculator" style="font-size: 1.5rem; color: #fa709a;"></i>
                                <h5 class="mt-1 mb-0 fw-bold" style="color: #fa709a; font-size: 1.25rem;">SAW</h5>
                                <p class="mb-0 text-muted" style="font-size: 0.7rem;">Metode Analisis</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center p-2">
                                <i class="bi bi-star-fill" style="font-size: 1.5rem; color: #fa709a;"></i>
                                <h5 class="mt-1 mb-0 fw-bold" style="color: #fa709a; font-size: 1.25rem;">{{ $jumlahKriteria }}</h5>
                                <p class="mb-0 text-muted" style="font-size: 0.7rem;">Kriteria Penilaian</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h6 class="fw-bold mb-2" style="font-size: 0.9rem;">Tentang Sistem Analisis</h6>
                <div class="alert alert-warning py-2 px-3 mb-3" style="font-size: 0.8rem;">
                    <i class="bi bi-lightbulb me-1"></i>
                    <strong>SAW</strong> adalah metode penjumlahan terbobot untuk rekomendasi kontrakan dan laundry terbaik berdasarkan kriteria.
                </div>
                
                <h6 class="fw-bold mb-2" style="font-size: 0.9rem;">Langkah-langkah Analisis</h6>
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0 py-2">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-2" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">1</span>
                            <div>
                                <h6 class="mb-0" style="font-size: 0.85rem;">Pilih Kriteria</h6>
                                <small class="text-muted" style="font-size: 0.7rem;">Tentukan kriteria penilaian</small>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item px-0 py-2">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-2" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">2</span>
                            <div>
                                <h6 class="mb-0" style="font-size: 0.85rem;">Normalisasi Nilai</h6>
                                <small class="text-muted" style="font-size: 0.7rem;">Normalisasi nilai alternatif</small>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item px-0 py-2">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-2" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">3</span>
                            <div>
                                <h6 class="mb-0" style="font-size: 0.85rem;">Perhitungan SAW</h6>
                                <small class="text-muted" style="font-size: 0.7rem;">Hitung nilai preferensi</small>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item px-0 py-2">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-2" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">4</span>
                            <div>
                                <h6 class="mb-0" style="font-size: 0.85rem;">Hasil Rekomendasi</h6>
                                <small class="text-muted" style="font-size: 0.7rem;">Ranking rekomendasi terbaik</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 0.75rem 1.25rem;">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('saw.index') }}" class="btn btn-sm" style="background: #fa709a; color: white;">
                    <i class="bi bi-arrow-right-circle me-1"></i>Mulai Analisis
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ========== FADE-IN ANIMATION FOR STATS CARDS ==========
    const statsCards = document.querySelectorAll('.stats-card');
    statsCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 * (index + 1));
    });
    
    // ========== COUNTER ANIMATION ==========
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 1000;
        const increment = target / (duration / 16);
        let current = 0;
        
        const updateCounter = () => {
            current += increment;
            if (current < target) {
                counter.textContent = Math.ceil(current);
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target;
            }
        };
        updateCounter();
    });
    
    // ========== BAR CHART: PERBANDINGAN HARGA ==========
    const hargaKontrakan = @json($hargaKontrakan);
    const hargaLaundry = @json($hargaLaundry);
    
    new Chart(document.getElementById('hargaChart'), {
        type: 'bar',
        data: {
            labels: hargaKontrakan.map(k => k.nama),
            datasets: [{
                label: 'Kontrakan',
                data: hargaKontrakan.map(k => k.harga),
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderColor: 'rgba(102, 126, 234, 1)',
                borderWidth: 2
            }, {
                label: 'Laundry',
                data: hargaLaundry.map(l => l.harga),
                backgroundColor: 'rgba(240, 147, 251, 0.8)',
                borderColor: 'rgba(240, 147, 251, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value/1000) + 'k';
                        }
                    }
                }
            }
        }
    });
    
    // ========== DONUT CHART: DISTRIBUSI JARAK ==========
    const jarakData = @json($jarakKontrakan);
    const totalDekat = jarakData.dekat + @json($jarakLaundry['dekat']);
    const totalSedang = jarakData.sedang + @json($jarakLaundry['sedang']);
    const totalJauh = jarakData.jauh + @json($jarakLaundry['jauh']);
    
    new Chart(document.getElementById('jarakChart'), {
        type: 'doughnut',
        data: {
            labels: ['Dekat (≤500m)', 'Sedang (501-1000m)', 'Jauh (>1000m)'],
            datasets: [{
                data: [totalDekat, totalSedang, totalJauh],
                backgroundColor: [
                    'rgba(25, 135, 84, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ],
                borderColor: [
                    'rgba(25, 135, 84, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // ========== LINE CHART: TREND DATA ==========
    const monthlyData = @json($monthlyData);
    
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: monthlyData.map(m => m.month),
            datasets: [{
                label: 'Kontrakan',
                data: monthlyData.map(m => m.kontrakan),
                borderColor: 'rgba(102, 126, 234, 1)',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Laundry',
                data: monthlyData.map(m => m.laundry),
                borderColor: 'rgba(240, 147, 251, 1)',
                backgroundColor: 'rgba(240, 147, 251, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>

<style>
    .stats-card {
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
    }
    
    .stats-card .card-body {
        position: relative;
        z-index: 2;
    }
    
    .stats-card .btn {
        pointer-events: auto;
        cursor: pointer;
        position: relative;
        z-index: 3;
        text-decoration: none;
    }
    
    .stats-card .btn:hover {
        background-color: #f8f9fa !important;
        transform: scale(1.02);
    }
    
    .table > :not(caption) > * > * {
        padding: 1rem 0.75rem;
    }
    
    .badge {
        font-weight: 500;
        font-size: 0.85rem;
    }
    
    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    
    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }
    
    .bg-secondary-subtle {
        background-color: rgba(108, 117, 125, 0.1) !important;
    }
    
    canvas {
        max-height: 400px;
    }
    
    /* Perbaikan untuk developer tools inspect */
    .stats-card,
    .stats-card * {
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        transform-style: preserve-3d;
        -webkit-transform-style: preserve-3d;
    }
</style>
@endsection
