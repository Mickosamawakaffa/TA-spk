@extends('layouts.admin')

@section('title', 'Daftar Kontrakan')

@section('content')
<div class="container-fluid px-2 px-md-3 page-kontrakan" style="padding-top: 0.5rem; padding-bottom: 1rem;">
    <style>
        /* CSS Variables for Kontrakan Theme - Purple Theme */
        :root {
            --danger-color: #dc3545;
            --danger-color-rgb: 220, 53, 69;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --kontrakan-primary: #667eea;
            --kontrakan-secondary: #764ba2;
            --kontrakan-accent: #5b21b6;
            --kontrakan-light: #e0e7ff;
        }
        
        /* Page Background & Pattern - Purple Theme */
        .page-kontrakan {
            position: relative;
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f7ff 0%, #f3f1ff 50%, #ede9fe 100%);
        }
        
        .page-kontrakan::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(102, 126, 234, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(102, 126, 234, 0.06) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(118, 75, 162, 0.04) 0%, transparent 60%);
            pointer-events: none;
            z-index: -1;
        }
        
        /* Floating Decorations - Purple Theme */
        .floating-decoration {
            position: fixed;
            pointer-events: none;
            z-index: 0;
            opacity: 0.5;
        }
        
        .floating-decoration.deco-1 {
            top: 15%;
            right: 5%;
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.12) 0%, rgba(102, 126, 234, 0.05) 100%);
            border-radius: 50%;
            filter: blur(30px);
            animation: float 8s ease-in-out infinite;
        }
        
        .floating-decoration.deco-2 {
            bottom: 20%;
            left: 10%;
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, rgba(118, 75, 162, 0.1) 0%, rgba(102, 126, 234, 0.05) 100%);
            border-radius: 50%;
            filter: blur(40px);
            animation: float 10s ease-in-out infinite reverse;
        }
        
        .floating-decoration.deco-3 {
            top: 60%;
            right: 15%;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, transparent 100%);
            border-radius: 50%;
            filter: blur(25px);
            animation: float 6s ease-in-out infinite 1s;
        }

        /* Stats Cards */
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 0.75rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.8);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--stat-color), transparent);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.15);
        }
        
        .stat-card .stat-icon {
            width: 35px;
            height: 35px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--stat-color);
            line-height: 1.2;
        }
        
        .stat-card .stat-label {
            font-size: 0.75rem;
            color: #6c757d;
            font-weight: 500;
        }

        /* Enhanced Table Card */
        .main-content-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.9);
            overflow: hidden;
            position: relative;
        }
        
        .main-content-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #667eea 100%);
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
        }
        
        @keyframes gradientMove {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Table Enhancements */
        .enhanced-table {
            background: transparent;
        }
        
        .enhanced-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .enhanced-table thead th {
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.3px;
            padding: 0.75rem 0.5rem;
            border: none;
        }
        
        .enhanced-table tbody tr {
            background: rgba(255, 255, 255, 0.7);
            transition: all 0.3s ease;
        }
        
        .enhanced-table tbody tr:nth-child(even) {
            background: rgba(102, 126, 234, 0.03);
        }
        
        .enhanced-table tbody tr:hover {
            background: rgba(102, 126, 234, 0.08);
            transform: scale(1.002);
        }
        
        .enhanced-table tbody td {
            padding: 0.6rem 0.5rem;
            vertical-align: middle;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
        }
        
        /* Card Header with Gradient Border */
        .card-header-enhanced {
            background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(248,249,255,0.98) 100%);
            border-bottom: 2px solid transparent;
            background-clip: padding-box;
            position: relative;
        }
        
        .card-header-enhanced::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
            opacity: 0.3;
        }

        @media (max-width: 768px) {
            .stats-overview {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.5rem;
            }
            
            .stat-card {
                padding: 0.75rem;
            }
            
            .stat-card .stat-value {
                font-size: 1.25rem;
            }
            
            .stat-card .stat-icon {
                width: 32px;
                height: 32px;
                font-size: 1rem;
            }
            
            .stat-card .stat-label {
                font-size: 0.7rem;
            }
            
            .main-content-card {
                border-radius: 12px;
                margin: 0 -0.5rem;
            }
            
            .card-header-enhanced {
                padding: 0.75rem 1rem;
            }
            
            /* Floating decorations reduced on mobile */
            .floating-decoration {
                opacity: 0.3;
                transform: scale(0.5);
            }
        }
        
        @media (max-width: 576px) {
            .stats-overview {
                grid-template-columns: 1fr 1fr;
                gap: 0.4rem;
            }
            
            .stat-card {
                padding: 0.6rem;
            }
            
            .stat-card .stat-value {
                font-size: 1.1rem;
            }
            
            .stat-card .stat-icon {
                width: 28px;
                height: 28px;
                font-size: 0.85rem;
                margin-bottom: 0.4rem;
            }
            
            .stat-card .stat-label {
                font-size: 0.65rem;
            }
            
            /* Hide some decorations on small mobile */
            .floating-decoration.deco-2,
            .floating-decoration.deco-3 {
                display: none;
            }
        }
        
        @media (max-width: 380px) {
            .stat-card .stat-value {
                font-size: 1rem;
            }
            
            .stat-card .stat-label {
                font-size: 0.6rem;
            }
        }
        .header-kontrakan {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 0.75rem;
            padding: 1rem;
            color: white;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .header-kontrakan::before {
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
        
        .header-kontrakan h2 {
            font-size: clamp(1.5rem, 4vw, 2rem);
            font-weight: 800;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }
        
        .header-kontrakan p {
            opacity: 0.95;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
            font-size: clamp(0.9rem, 2vw, 1rem);
        }
        
        .btn-tambah-kontrakan {
            background: rgba(255,255,255,0.95);
            color: #667eea;
            font-weight: 700;
            border: none;
            padding: 0.875rem 1.75rem;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
        }
        
        .btn-tambah-kontrakan:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
            color: #764ba2;
            background: white;
        }
        
        .filter-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 1rem;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }
        
        .filter-card-header {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
            color: white;
            padding: 0.75rem;
            font-weight: 700;
        }
        
        .kontrakan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        
        .kontrakan-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            background: white;
        }
        
        .kontrakan-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.15);
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .header-kontrakan {
                padding: 1rem 0.75rem;
                margin-bottom: 0.75rem;
                border-radius: 0.5rem;
            }
            
            .header-kontrakan::before {
                width: 150px;
                height: 150px;
                right: -20%;
            }
            
            .btn-tambah-kontrakan {
                padding: 0.65rem 1rem;
                font-size: 0.85rem;
                width: 100%;
                margin-top: 0.75rem;
                border-radius: 8px;
            }
            
            .kontrakan-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
            
            .filter-card-header {
                padding: 0.75rem 1rem;
            }
            
            .kontrakan-card:hover {
                transform: translateY(-4px);
            }
            
            /* Better touch targets */
            .btn {
                min-height: 44px;
            }
            
            /* Filter section improvements */
            .range-slider {
                padding: 0.75rem !important;
            }
            
            .range-value {
                font-size: 0.8rem !important;
            }
        }
        
        @media (max-width: 576px) {
            .header-kontrakan {
                padding: 0.75rem 0.5rem;
                margin-bottom: 0.5rem;
            }
            
            .header-kontrakan h2 {
                font-size: 1.25rem;
            }
            
            .header-kontrakan p {
                font-size: 0.8rem;
            }
            
            .kontrakan-grid {
                gap: 0.5rem;
            }
            
            .btn-tambah-kontrakan {
                padding: 0.6rem 0.75rem;
                font-size: 0.8rem;
            }
            
            /* Mobile card optimizations */
            .glass-card.mb-3 {
                margin-left: 0.5rem !important;
                margin-right: 0.5rem !important;
            }
        }
        
        @media (max-width: 380px) {
            .header-kontrakan h2 {
                font-size: 1.1rem;
            }
            
            .header-kontrakan h2 i {
                font-size: 1rem;
            }
        }
    </style>
    
    <!-- Floating Decorations -->
    <div class="floating-decoration deco-1"></div>
    <div class="floating-decoration deco-2"></div>
    <div class="floating-decoration deco-3"></div>

    <!-- Header Section -->
    <div class="header-kontrakan">
        <div class="row align-items-center">
            <div class="col-12 col-md-8">
                <h2 class="mb-2">
                    <i class="bi bi-houses me-2 me-md-3"></i><span class="d-none d-sm-inline">Kelola Kontrakan</span><span class="d-sm-none">Kontrakan</span>
                </h2>
                <p class="mb-0"><span class="d-none d-md-inline">Informasi lengkap dan manajemen data kontrakan yang tersedia</span><span class="d-md-none">Manajemen data kontrakan</span></p>
            </div>
            <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('kontrakan.create') }}" class="btn btn-tambah-kontrakan">
                    <i class="bi bi-plus-circle me-2"></i><span class="d-none d-sm-inline">Tambah Kontrakan Baru</span><span class="d-sm-none">Tambah</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Overview -->
    <div class="stats-overview">
        <div class="stat-card" style="--stat-color: #667eea;">
            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.1));">
                <i class="bi bi-house-fill" style="color: #667eea;"></i>
            </div>
            <div class="stat-value">{{ $kontrakan->total() ?? 0 }}</div>
            <div class="stat-label">Total Kontrakan</div>
        </div>
        <div class="stat-card" style="--stat-color: #28a745;">
            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(40, 167, 69, 0.2), rgba(40, 167, 69, 0.1));">
                <i class="bi bi-currency-dollar" style="color: #28a745;"></i>
            </div>
            <div class="stat-value">{{ isset($hargaMin) ? 'Rp ' . number_format($hargaMin / 1000000, 1) . 'jt' : '-' }}</div>
            <div class="stat-label">Harga Terendah</div>
        </div>
        <div class="stat-card" style="--stat-color: #dc3545;">
            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.2), rgba(220, 53, 69, 0.1));">
                <i class="bi bi-geo-alt-fill" style="color: #dc3545;"></i>
            </div>
            <div class="stat-value">{{ isset($jarakMaxKm) ? number_format($jarakMaxKm, 1) . 'km' : '-' }}</div>
            <div class="stat-label">Jarak Terjauh</div>
        </div>
        <div class="stat-card" style="--stat-color: #ffc107;">
            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.2), rgba(255, 193, 7, 0.1));">
                <i class="bi bi-door-open-fill" style="color: #ffc107;"></i>
            </div>
            <div class="stat-value">{{ isset($jumlah_kamarMax) ? $jumlah_kamarMax : '-' }}</div>
            <div class="stat-label">Kamar Terbanyak</div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 py-2 mb-2" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <span class="small">{{ session('success') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 py-2 mb-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <span class="small">{{ session('error') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter Card with Enhanced UI -->
    <div class="glass-card mb-3" style="overflow: hidden; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
        <div class="filter-header p-2 px-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; cursor: pointer; user-select: none; transition: all 0.3s ease;"
             data-bs-toggle="collapse" data-bs-target="#collapseFilters">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="mb-0 fw-bold" style="color: white !important;">
                        <i class="bi bi-funnel me-2"></i>
                        <span>Pencarian & Filter Kontrakan</span>
                    </h6>
                </div>
                <div class="col-auto">
                    <div class="d-flex gap-2 align-items-center">
                        <span class="badge bg-light text-dark small d-none d-sm-inline" id="filterCount" style="display: none;">
                            <i class="bi bi-check-circle me-1"></i><span id="filterCountNum">0</span> filter
                        </span>
                        <i class="bi bi-chevron-down" id="filterToggleIcon" style="transition: transform 0.3s ease;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="collapse" id="collapseFilters">
            <div class="p-2 p-md-3" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                <form action="{{ route('kontrakan.index') }}" method="GET" id="filterForm">
                    <!-- Main Search Bar with Enhanced UI -->
                    <div class="mb-2">
                        <label class="form-label fw-semibold mb-1 small">
                            <i class="bi bi-search me-1" style="color: var(--primary-color);"></i>
                            Cari Kontrakan
                        </label>
                        <div class="search-input-enhanced position-relative">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0" style="border-color: var(--border-color);">
                                    <i class="bi bi-search" style="color: var(--primary-color);"></i>
                                </span>
                            <input 
                                type="text" 
                                name="search" 
                                class="form-control form-control-sm border-start-0" 
                                placeholder="Cari nama, alamat, fasilitas..."
                                value="{{ $filters['search'] ?? '' }}"
                                id="searchInput"
                                style="border-color: var(--border-color);"
                            >
                            @if($filters['search'] ?? false)
                            <button type="button" class="btn btn-outline-secondary d-none d-md-inline" id="clearSearch">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            @endif
                            <button class="btn" type="submit" style="background: var(--primary-color); color: white; border-color: var(--primary-color);">
                                <i class="bi bi-search me-1 me-md-2"></i><span class="d-none d-sm-inline">Cari</span>
                            </button>
                        </div>
                        @if($filters['search'] ?? false)
                        <div class="d-md-none mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSearchMobile">
                                <i class="bi bi-x-lg me-1"></i>Hapus Pencarian
                            </button>
                        </div>
                        @endif
                        <div class="search-suggestions" id="searchSuggestions"></div>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="bi bi-lightbulb me-1" style="color: var(--accent-color);"></i>
                        <span class="d-none d-md-inline">💡 Ketik "WiFi", "Dapur", "AC" atau kata kunci lainnya untuk hasil lebih spesifik</span>
                        <span class="d-md-none">💡 Coba "WiFi", "Dapur", "AC"</span>
                    </small>
                </div>

                <!-- Advanced Filters Section -->
                <div class="border-top mt-2 pt-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-semibold mb-0 small" style="color: var(--primary-color);">
                            <i class="bi bi-sliders me-1"></i>
                            Filter Lanjutan
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="resetFilters">
                            <i class="bi bi-arrow-clockwise me-1"></i><span class="d-none d-sm-inline">Reset</span>
                        </button>
                    </div>
                    
                    <div class="row g-2">
                        <!-- 💰 Range Harga with Visual Slider -->
                        <div class="col-12">
                            <label class="form-label fw-semibold mb-1 small">
                                <i class="bi bi-currency-dollar me-1" style="color: var(--success-color);"></i>
                                💰 Range Harga per Tahun
                            </label>
                            <div class="range-slider p-2 rounded-3" style="background: rgba(var(--primary-color-rgb), 0.05); border: 1px solid rgba(var(--primary-color-rgb), 0.1);">
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <div class="range-value text-center p-1 rounded" style="background: white; border: 2px solid var(--primary-color); color: var(--primary-color); font-weight: 600; font-size: 0.85rem;">
                                            <small class="d-block text-muted" style="font-size: 0.7rem;">Min</small>
                                            <span id="hargaMinDisplay">{{ number_format($filters['harga_min'] ?? $hargaMin, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="range-value text-center p-1 rounded" style="background: white; border: 2px solid var(--primary-color); color: var(--primary-color); font-weight: 600; font-size: 0.85rem;">
                                            <small class="d-block text-muted" style="font-size: 0.7rem;">Max</small>
                                            <span id="hargaMaxDisplay">{{ number_format($filters['harga_max'] ?? $hargaMax, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <input 
                                            type="range" 
                                            name="harga_min" 
                                            id="harga_min" 
                                            class="form-range" 
                                            min="{{ $hargaMin }}" 
                                            max="{{ $hargaMax }}" 
                                            value="{{ $filters['harga_min'] ?? $hargaMin }}"
                                            step="1000000"
                                            style="accent-color: var(--primary-color);"
                                        >
                                        <small class="text-muted mt-1 d-block">Geser untuk minimum</small>
                                    </div>
                                    <div class="col-md-6">
                                        <input 
                                            type="range" 
                                            name="harga_max" 
                                            id="harga_max" 
                                            class="form-range" 
                                            min="{{ $hargaMin }}" 
                                            max="{{ $hargaMax }}" 
                                            value="{{ $filters['harga_max'] ?? $hargaMax }}"
                                            step="1000000"
                                            style="accent-color: var(--primary-color);"
                                        >
                                        <small class="text-muted mt-1 d-block">Geser untuk maksimum</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1" style="color: var(--info-color);"></i>
                                <span class="d-none d-md-inline">Range tersedia: Rp {{ number_format($hargaMin, 0, ',', '.') }} - Rp {{ number_format($hargaMax, 0, ',', '.') }}</span>
                                <span class="d-md-none">Rp {{ number_format($hargaMin, 0, ',', '.') }} - {{ number_format($hargaMax, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- 📍 Jarak dengan Visual Slider -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1 small">
                                <i class="bi bi-pin-map me-1" style="color: var(--danger-color);"></i>
                                📍 Jarak Max dari Kampus
                            </label>
                            <div class="range-slider p-2 rounded-3" style="background: rgba(var(--danger-color-rgb), 0.05); border: 1px solid rgba(var(--danger-color-rgb), 0.1);">
                                <div class="range-value text-center p-1 rounded mb-2" style="background: white; border: 2px solid var(--danger-color); color: var(--danger-color); font-weight: 600; font-size: 0.85rem;">
                                    <span id="jarakDisplay">{{ $filters['jarak_max'] ?? '10' }}</span> km
                                </div>
                                <input 
                                    type="range" 
                                    name="jarak_max" 
                                    id="jarak_max" 
                                    class="form-range" 
                                    min="0" 
                                    max="{{ $jarakMaxKm }}" 
                                    value="{{ $filters['jarak_max'] ?? 10 }}"
                                    step="0.5"
                                    style="accent-color: var(--danger-color);"
                                >
                                <small class="text-muted mt-1 d-block">Geser untuk menyesuaikan</small>
                            </div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1" style="color: var(--info-color);"></i>
                                <span class="d-none d-md-inline">Jarak terjauh tersedia: {{ $jarakMaxKm }} km</span>
                                <span class="d-md-none">Max: {{ $jarakMaxKm }} km</span>
                            </div>
                        </div>

                        <!-- 🏠 Jumlah Kamar dengan Visual Slider -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1 small">
                                <i class="bi bi-rulers me-1" style="color: var(--warning-color);"></i>
                                🏠 Jumlah Kamar
                            </label>
                            <div class="range-slider p-2 rounded-3" style="background: rgba(var(--warning-color-rgb), 0.05); border: 1px solid rgba(var(--warning-color-rgb), 0.1);">
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <div class="range-value text-center p-1 rounded" style="background: white; border: 2px solid var(--warning-color); color: var(--warning-color); font-weight: 600; font-size: 0.85rem;">
                                            <small class="d-block text-muted" style="font-size: 0.7rem;">Min</small>
                                            <span id="kamarMinDisplay">{{ $filters['jumlah_kamar_min'] ?? $jumlah_kamarMin }}</span> kmr
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="range-value text-center p-1 rounded" style="background: white; border: 2px solid var(--warning-color); color: var(--warning-color); font-weight: 600; font-size: 0.85rem;">
                                            <small class="d-block text-muted" style="font-size: 0.7rem;">Max</small>
                                            <span id="kamarMaxDisplay">{{ $filters['jumlah_kamar_max'] ?? $jumlah_kamarMax }}</span> kmr
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <input 
                                            type="range" 
                                            name="jumlah_kamar_min" 
                                            id="kamar_min" 
                                            class="form-range" 
                                            min="{{ $jumlah_kamarMin }}" 
                                            max="{{ $jumlah_kamarMax }}" 
                                            value="{{ $filters['jumlah_kamar_min'] ?? $jumlah_kamarMin }}"
                                            step="1"
                                            style="accent-color: var(--warning-color);"
                                        >
                                        <small class="text-muted mt-1 d-block">Minimum kamar</small>
                                    </div>
                                    <div class="col-md-6">
                                        <input 
                                            type="range" 
                                            name="jumlah_kamar_max" 
                                            id="kamar_max" 
                                            class="form-range" 
                                            min="{{ $jumlah_kamarMin }}" 
                                            max="{{ $jumlah_kamarMax }}" 
                                            value="{{ $filters['jumlah_kamar_max'] ?? $jumlah_kamarMax }}"
                                            step="1"
                                            style="accent-color: var(--warning-color);"
                                        >
                                            <small class="text-muted">Maksimum kamar</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Range: {{ $jumlah_kamarMin }} - {{ $jumlah_kamarMax }} kamar
                                </div>
                            </div>

                        <!-- ✨ Fasilitas Checkboxes -->
                        @if($fasilitasUnique->count() > 0)
                        <div class="col-12">
                            <label class="form-label fw-semibold mb-1 small">
                                <i class="bi bi-star me-1" style="color: var(--warning-color);"></i>
                                ✨ Fasilitas yang Diinginkan
                            </label>
                            <div class="glass-card border-0" style="background: rgba(var(--warning-color-rgb), 0.05);">
                                <div class="card-body p-2">
                                    <div class="row g-2">
                                        @foreach($fasilitasUnique as $fasilitas)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="form-check">
                                                <input 
                                                    class="form-check-input" 
                                                    type="checkbox" 
                                                    name="fasilitas_filter[]" 
                                                    value="{{ $fasilitas }}" 
                                                    id="fasilitas_{{ $loop->index }}"
                                                    {{ in_array($fasilitas, $filters['fasilitas_filter'] ?? []) ? 'checked' : '' }}
                                                    style="accent-color: var(--primary-color);"
                                                >
                                                <label class="form-check-label small" for="fasilitas_{{ $loop->index }}" style="cursor: pointer;">
                                                    <i class="bi bi-check-circle me-1" style="color: var(--success-color);"></i>{{ $fasilitas }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <i class="bi bi-info-circle me-1" style="color: var(--info-color);"></i>
                                <span class="d-none d-md-inline">Pilih satu atau lebih fasilitas (hasil akan menampilkan kontrakan yang memiliki SEMUA fasilitas terpilih)</span>
                                <span class="d-md-none">Pilih fasilitas yang diinginkan</span>
                            </small>
                        </div>
                        @endif

                        <!-- 📋 Urutkan (Radio Buttons) -->
                        <div class="col-12">
                            <label class="form-label fw-semibold mb-1 small">
                                <i class="bi bi-sort-down me-1" style="color: var(--info-color);"></i>
                                📋 Urutkan Berdasarkan
                            </label>
                            <div class="row g-2">
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="sort_by" id="sort_terbaru" value="terbaru" {{ ($filters['sort_by'] ?? 'terbaru') == 'terbaru' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100" for="sort_terbaru" style="border-color: var(--primary-color); color: var(--primary-color);">
                                        <i class="bi bi-clock"></i> <span class="d-none d-sm-inline">Terbaru</span><span class="d-sm-none">🕑</span>
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="sort_by" id="sort_termurah" value="harga_termurah" {{ ($filters['sort_by'] ?? '') == 'harga_termurah' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success w-100" for="sort_termurah" style="border-color: var(--success-color); color: var(--success-color);">
                                        <i class="bi bi-currency-dollar"></i> <span class="d-none d-sm-inline">Termurah</span><span class="d-sm-none">💰</span>
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="sort_by" id="sort_terdekat" value="jarak_terdekat" {{ ($filters['sort_by'] ?? '') == 'jarak_terdekat' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-danger w-100" for="sort_terdekat" style="border-color: var(--danger-color); color: var(--danger-color);">
                                        <i class="bi bi-pin-map"></i> <span class="d-none d-sm-inline">Terdekat</span><span class="d-sm-none">📍</span>
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="sort_by" id="sort_kamar_terbanyak" value="jumlah_kamar_terbesar" {{ ($filters['sort_by'] ?? '') == 'jumlah_kamar_terbesar' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-warning w-100" for="sort_kamar_terbanyak" style="border-color: var(--warning-color); color: var(--warning-color);">
                                        <i class="bi bi-rulers"></i> <span class="d-none d-sm-inline">Terbanyak</span><span class="d-sm-none">🏠</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-column flex-md-row gap-2 mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary flex-fill" style="background: var(--primary-color); border-color: var(--primary-color);">
                            <i class="bi bi-funnel-fill me-2"></i>
                            <span class="d-none d-sm-inline">Terapkan Filter</span>
                            <span class="d-sm-none">Terapkan</span>
                        </button>
                        <a href="{{ route('kontrakan.index') }}" class="btn btn-outline-secondary flex-fill">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            <span class="d-none d-sm-inline">Reset Filter</span>
                            <span class="d-sm-none">Reset</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 📊 Active Filters & Result Info -->
    <div class="row g-2 mb-2">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row gap-2 align-items-start align-items-md-center justify-content-between">
                <div class="d-flex flex-wrap gap-2 align-items-center w-100 w-md-auto">
                    <!-- Result Count Badge -->
                    <span class="badge px-2 py-1 rounded-3" style="background: rgba(var(--primary-color-rgb), 0.1); color: var(--primary-color); font-size: 0.85rem; font-weight: 600;">
                        <i class="bi bi-house-fill me-1"></i>
                        <strong>{{ $filteredCount ?? 0 }}</strong> 
                        @if(isset($filteredCount) && isset($totalKontrakan) && $filteredCount < $totalKontrakan)
                        <span class="d-none d-md-inline">dari {{ $totalKontrakan }}</span>
                        @endif
                        kontrakan
                    </span>

                    <!-- Active Filter Badges -->
                    @if($filters['search'] ?? false)
                    <span class="badge px-2 py-1 rounded-3" style="background: rgba(var(--info-color-rgb), 0.1); color: var(--info-color); font-size: 0.8rem;">
                        <i class="bi bi-search me-1"></i>
                        <span class="d-none d-sm-inline">"{{ Str::limit($filters['search'], 20) }}"</span>
                        <span class="d-sm-none">🔍</span>
                    </span>
                    @endif

                    @if(($filters['harga_min'] ?? false) || ($filters['harga_max'] ?? false))
                    <span class="badge px-2 py-1 rounded-3" style="background: rgba(var(--success-color-rgb), 0.1); color: var(--success-color); font-size: 0.8rem;">
                        <i class="bi bi-currency-dollar me-1"></i>
                        <span class="d-none d-lg-inline">
                            Rp {{ number_format($filters['harga_min'] ?? 0, 0, ',', '.') }} - Rp {{ number_format($filters['harga_max'] ?? ($hargaMax ?? 0), 0, ',', '.') }}
                        </span>
                        <span class="d-lg-none">💰</span>
                    </span>
                    @endif

                    @if($filters['jarak_max'] ?? false)
                    <span class="badge px-2 px-md-3 py-2 rounded-3" style="background: rgba(var(--danger-color-rgb), 0.1); color: var(--danger-color); font-size: 0.85rem;">
                        <i class="bi bi-pin-map me-1"></i>
                        <span class="d-none d-sm-inline">Max {{ $filters['jarak_max'] }} km</span>
                        <span class="d-sm-none">📍{{ $filters['jarak_max'] }}km</span>
                    </span>
                    @endif

                    @if(($filters['jumlah_kamar_min'] ?? false) || ($filters['jumlah_kamar_max'] ?? false))
                    <span class="badge px-2 px-md-3 py-2 rounded-3" style="background: rgba(var(--warning-color-rgb), 0.1); color: var(--warning-color); font-size: 0.85rem;">
                        <i class="bi bi-rulers me-1"></i>
                        <span class="d-none d-md-inline">{{ $filters['jumlah_kamar_min'] ?? 0 }} - {{ $filters['jumlah_kamar_max'] ?? ($jumlah_kamarMax ?? 0) }} kamar</span>
                        <span class="d-md-none">🏠{{ $filters['jumlah_kamar_min'] ?? 0 }}-{{ $filters['jumlah_kamar_max'] ?? ($jumlah_kamarMax ?? 0) }}</span>
                    </span>
                    @endif

                    @if(!empty($filters['fasilitas_filter']))
                    <span class="badge px-2 px-md-3 py-2 rounded-3" style="background: rgba(var(--secondary-color-rgb), 0.1); color: var(--secondary-color); font-size: 0.85rem;">
                        <i class="bi bi-star me-1"></i>
                        <span class="d-none d-sm-inline">{{ count($filters['fasilitas_filter']) }} fasilitas</span>
                        <span class="d-sm-none">✨{{ count($filters['fasilitas_filter']) }}</span>
                    </span>
                    @endif
                </div>

                <!-- 🆕 Export Buttons -->
                @if(isset($filteredCount) && $filteredCount > 0)
                @php
                    try {
                        $queryParams = request()->query();
                        $queryString = !empty($queryParams) ? '?' . http_build_query($queryParams) : '';
                    } catch (\Exception $e) {
                        $queryString = '';
                    }
                @endphp
                <div class="btn-group" role="group">
                    <a href="{{ route('export.kontrakan.excel') }}{{ $queryString }}" 
                       class="btn btn-sm btn-outline-success" 
                       title="Export ke CSV"
                       style="border-color: var(--success-color); color: var(--success-color);">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                        <span class="d-none d-sm-inline">CSV</span>
                    </a>
                    <a href="{{ route('export.kontrakan.pdf') }}{{ $queryString }}" 
                       class="btn btn-sm btn-outline-danger" 
                       title="Export ke PDF"
                       style="border-color: var(--danger-color); color: var(--danger-color);">
                        <i class="bi bi-file-earmark-pdf me-1"></i>
                        <span class="d-none d-sm-inline">PDF</span>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Main Card -->
    <div class="main-content-card">
        <!-- Header Info dengan Bulk Actions -->
        <div class="card-header card-header-enhanced py-2 py-md-3" style="border-radius: 1rem 1rem 0 0;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 w-100">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2 w-100 w-md-auto">
                    <span class="text-muted small">
                        <i class="bi bi-info-circle me-1" style="color: var(--info-color);"></i>
                        <strong>{{ $kontrakan->count() }}</strong> kontrakan
                    </span>
                    
                    <!-- Bulk Action Buttons - Style seperti Laundry -->
                    @if(auth()->user()->role == 'super_admin' && $kontrakan->count() > 0)
                    <div id="bulkDeleteBtn" style="display: none;" class="w-100 w-md-auto">
                        <button 
                            type="button" 
                            class="btn btn-danger btn-sm w-100 w-md-auto"
                            onclick="showBulkDeleteModal()"
                        >
                            <i class="bi bi-trash me-1"></i>Hapus (<span id="selectedCount">0</span>)
                        </button>
                    </div>
                    @endif
                </div>
                
                <span class="badge px-3 py-2 rounded-3" style="background: rgba(var(--primary-color-rgb), 0.1); color: var(--primary-color); font-size: 0.9rem; font-weight: 600;">
                    <i class="bi bi-database me-1"></i>
                    <strong>{{ $kontrakan->total() }}</strong> Data
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            @if($kontrakan->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-5 px-3" style="background: rgba(var(--primary-color-rgb), 0.02);">
                    <div class="mb-4">
                        <i class="bi bi-inbox text-muted opacity-50" style="font-size: 3rem; color: var(--muted-color);"></i>
                    </div>
                    <h5 class="text-muted mb-3" style="color: var(--text-secondary);">
                        <span class="d-none d-md-inline">Tidak ada data kontrakan ditemukan</span>
                        <span class="d-md-none">Data tidak ditemukan</span>
                    </h5>
                    <p class="text-muted mb-4 small" style="color: var(--text-muted);">
                        @if(request()->hasAny(['search', 'harga_min', 'harga_max', 'jarak_max', 'luas_min', 'luas_max', 'fasilitas_filter']))
                            <span class="d-none d-md-block">Tidak ada kontrakan yang sesuai dengan kriteria filter Anda.<br>Coba ubah atau reset filter untuk melihat hasil lain.</span>
                            <span class="d-md-none">Filter tidak menemukan hasil. Coba ubah atau reset filter.</span>
                        @else
                            <span class="d-none d-md-block">Belum ada data kontrakan. Mulai tambahkan data kontrakan pertama Anda!</span>
                            <span class="d-md-none">Belum ada data. Tambahkan kontrakan pertama!</span>
                        @endif
                    </p>
                    <div class="d-flex gap-2 justify-content-center flex-column flex-sm-row">
                        @if(request()->hasAny(['search', 'harga_min', 'harga_max', 'jarak_max', 'luas_min', 'luas_max', 'fasilitas_filter']))
                        <a href="{{ route('kontrakan.index') }}" class="btn btn-outline-primary btn-sm" style="border-color: var(--primary-color); color: var(--primary-color);">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            <span class="d-none d-sm-inline">Reset Filter</span>
                            <span class="d-sm-none">Reset</span>
                        </a>
                        @endif
                        <a href="{{ route('kontrakan.create') }}" class="btn btn-sm" style="background: var(--primary-color); border-color: var(--primary-color); color: white;">
                            <i class="bi bi-plus-circle me-2"></i>
                            <span class="d-none d-sm-inline">Tambah Kontrakan</span>
                            <span class="d-sm-none">Tambah</span>
                        </a>
                    </div>
                </div>
            @else
                <!-- Mobile Card View -->
                <div class="d-md-none">
                    @foreach($kontrakan as $index => $item)
                    <div class="glass-card mb-3 mx-3 border-0 shadow-sm" style="border-radius: 1rem; transition: all 0.3s ease; background: rgba(255,255,255,0.9); backdrop-filter: blur(10px);" 
                         onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.15)'" 
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)'">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-start gap-2 flex-grow-1">
                                    @if(auth()->user()->role == 'super_admin')
                                    <input 
                                        type="checkbox" 
                                        class="form-check-input mt-1 row-checkbox" 
                                        value="{{ $item->id }}"
                                        data-nama="{{ $item->nama }}"
                                        style="accent-color: var(--primary-color);"
                                    >
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 fw-bold" style="color: var(--text-primary); font-size: 1.1rem;">
                                            <i class="bi bi-building me-2" style="color: var(--primary-color);"></i>
                                            {{ $item->nama }}
                                            <span class="badge {{ $item->status_badge_class }} ms-1" style="font-size: 0.65rem;">{{ $item->status_label }}</span>
                                        </h6>
                                        <p class="text-muted mb-2 small" style="line-height: 1.4;">
                                            <i class="bi bi-geo-alt me-1" style="color: var(--danger-color);"></i>{{ Str::limit($item->alamat, 45) }}
                                        </p>
                                    </div>
                                </div>
                                <span class="badge rounded-pill px-2 py-1" style="background: rgba(var(--primary-color-rgb), 0.1); color: var(--primary-color); font-size: 0.75rem;">
                                    #{{ $kontrakan->firstItem() + $index }}
                                </span>
                            </div>
                            
                            <!-- Info Grid -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">💰 Harga/Tahun</small>
                                    <span class="badge w-100 py-2 rounded-3" style="background: rgba(var(--success-color-rgb), 0.1); color: var(--success-color); font-size: 0.8rem; font-weight: 600;">
                                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="col-3">
                                    <small class="text-muted d-block mb-1">📏 Jarak</small>
                                    <span class="badge w-100 py-2 rounded-3" style="background: rgba(var(--info-color-rgb), 0.1); color: var(--info-color); font-size: 0.8rem; font-weight: 600;">
                                        {{ number_format($item->jarak / 1000, 1) }} km
                                    </span>
                                </div>
                                <div class="col-3">
                                    <small class="text-muted d-block mb-1">🚪 Kamar</small>
                                    <span class="badge w-100 py-2 rounded-3" style="background: rgba(var(--warning-color-rgb), 0.1); color: var(--warning-color); font-size: 0.8rem; font-weight: 600;">
                                        {{ $item->jumlah_kamar }} Kamar
                                    </span>
                                </div>
                            </div>

                            <!-- Fasilitas -->
                            @if($item->fasilitas)
                            <div class="mb-3">
                                <small class="text-muted d-block mb-2">✨ Fasilitas:</small>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach(array_slice(array_map('trim', explode(',', $item->fasilitas)), 0, 3) as $f)
                                    <span class="badge border small rounded-pill px-2 py-1" style="background: rgba(var(--secondary-color-rgb), 0.1); border-color: var(--border-color); color: var(--text-secondary); font-size: 0.75rem;">{{ $f }}</span>
                                    @endforeach
                                    @if(count(explode(',', $item->fasilitas)) > 3)
                                    <span class="badge small rounded-pill px-2 py-1" style="background: var(--secondary-color); color: white; font-size: 0.75rem;">+{{ count(explode(',', $item->fasilitas)) - 3 }}</span>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- WhatsApp Button -->
                            @if($item->hasWhatsapp())
                            <a href="{{ $item->whatsapp_url }}" target="_blank" class="btn btn-sm w-100 mb-3 rounded-3" style="background: #25D366; border-color: #25D366; color: white; font-weight: 600; padding: 0.5rem;">
                                <i class="bi bi-whatsapp me-2"></i>Hubungi Pemilik via WhatsApp
                            </a>
                            @endif
                            
                            <!-- Action Buttons -->
                            <div class="d-flex gap-1">
                                @if($item->latitude && $item->longitude)
                                <a 
                                    href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" 
                                    target="_blank"
                                    class="btn btn-sm flex-fill rounded-3"
                                    title="Lihat Lokasi"
                                    style="background: rgba(var(--success-color-rgb), 0.1); border: 1px solid var(--success-color); color: var(--success-color);"
                                >
                                    <i class="bi bi-geo-alt-fill"></i>
                                </a>
                                @endif
                                <a 
                                    href="{{ route('kontrakan.show', $item->id) }}" 
                                    class="btn btn-sm flex-fill rounded-3"
                                    title="Detail"
                                    style="background: rgba(var(--info-color-rgb), 0.1); border: 1px solid var(--info-color); color: var(--info-color);"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a 
                                    href="{{ route('kontrakan.edit', $item->id) }}" 
                                    class="btn btn-sm flex-fill rounded-3"
                                    title="Edit"
                                    style="background: rgba(var(--warning-color-rgb), 0.1); border: 1px solid var(--warning-color); color: var(--warning-color);"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                @if(auth()->user()->role == 'super_admin')
                                <button 
                                    type="button" 
                                    class="btn btn-sm flex-fill rounded-3"
                                    onclick="showDeleteModal({{ $item->id }}, {{ json_encode($item->nama) }}, {{ json_encode($item->alamat) }}, {{ json_encode('Rp ' . number_format($item->harga, 0, ',', '.')) }})"
                                    title="Hapus"
                                    style="background: rgba(var(--danger-color-rgb), 0.1); border: 1px solid var(--danger-color); color: var(--danger-color);"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                                <form 
                                    id="delete-form-{{ $item->id }}"
                                    action="{{ route('kontrakan.destroy', $item->id) }}" 
                                    method="POST" 
                                    class="d-none"
                                >
                                    @csrf
                                    @method('DELETE')
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Desktop Table View -->
                <div class="d-none d-md-block table-responsive" style="border-radius: 0 0 1rem 1rem; overflow: hidden;">
                    <table class="table enhanced-table align-middle mb-0">
                        <thead>
                            <tr>
                                @if(auth()->user()->role == 'super_admin')
                                <th class="border-0 py-3" style="width: 50px;">
                                    <div class="form-check d-flex align-items-center justify-content-center">
                                        <input 
                                            type="checkbox" 
                                            class="form-check-input select-all-checkbox" 
                                            id="selectAll"
                                            title="Pilih semua data di halaman ini"
                                            style="width: 20px; height: 20px; cursor: pointer; border: 2px solid rgba(255,255,255,0.8);"
                                        >
                                    </div>
                                </th>
                                @endif
                                
                                <th class="border-0 py-3" style="width: 60px;">
                                    <small class="text-uppercase text-muted fw-bold">No</small>
                                </th>
                                <th class="border-0 py-3">
                                    <small class="text-uppercase text-muted fw-bold"><i class="bi bi-building me-1"></i>Nama Kontrakan</small>
                                </th>
                                <th class="border-0 py-3">
                                    <small class="text-uppercase text-muted fw-bold"><i class="bi bi-geo-alt me-1"></i>Alamat</small>
                                </th>
                                <th class="border-0 py-3">
                                    <small class="text-uppercase text-muted fw-bold"><i class="bi bi-currency-dollar me-1"></i>Harga/Tahun</small>
                                </th>
                                <th class="border-0 py-3 text-center">
                                    <small class="text-uppercase text-muted fw-bold"><i class="bi bi-pin-map me-1"></i>Jarak</small>
                                </th>
                                <th class="border-0 py-3">
                                    <small class="text-uppercase text-muted fw-bold"><i class="bi bi-star me-1"></i>Fasilitas</small>
                                </th>
                                <th class="border-0 py-3 text-center">
                                    <small class="text-uppercase text-muted fw-bold"><i class="bi bi-door-closed me-1"></i>Kamar</small>
                                </th>
                                <th class="border-0 py-3 text-center" style="width: 180px;">
                                    <small class="text-uppercase text-muted fw-bold">Aksi</small>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kontrakan as $index => $item)
                            <tr>
                                @if(auth()->user()->role == 'super_admin')
                                <td class="px-3">
                                    <input 
                                        type="checkbox" 
                                        class="form-check-input row-checkbox" 
                                        value="{{ $item->id }}"
                                        data-nama="{{ $item->nama }}"
                                        style="accent-color: var(--primary-color); transform: scale(1.1);"
                                    >
                                </td>
                                @endif
                                
                                <td class="px-3">
                                    <span class="badge rounded-pill fw-bold" style="background: rgba(var(--primary-color-rgb), 0.1); color: var(--primary-color);">{{ $kontrakan->firstItem() + $index }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; flex-shrink: 0;">
                                            <i class="bi bi-building fs-5"></i>
                                        </div>
                                        <div style="min-width: 0;">
                                            <h6 class="mb-1 fw-bold" style="color: var(--text-primary);">
                                                {{ $item->nama }}
                                                <span class="badge {{ $item->status_badge_class }} ms-1" style="font-size: 0.7rem;">{{ $item->status_label }}</span>
                                            </h6>
                                            @if($item->hasWhatsapp())
                                            <small style="color: #25D366;">
                                                <i class="bi bi-whatsapp me-1"></i>Ada kontak WhatsApp
                                            </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted d-block" style="max-width: 250px; line-height: 1.3;">
                                        <i class="bi bi-geo-alt me-1" style="color: var(--danger-color);"></i>{{ Str::limit($item->alamat, 50) }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge px-3 py-2 rounded-3" style="background: rgba(var(--success-color-rgb), 0.1); color: var(--success-color); font-weight: 600;">
                                        <i class="bi bi-currency-dollar me-1"></i>
                                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge px-3 py-2 rounded-3" style="background: rgba(var(--info-color-rgb), 0.1); color: var(--info-color); font-weight: 600;">
                                        <i class="bi bi-pin-map me-1"></i>{{ number_format($item->jarak / 1000, 1) }} km
                                    </span>
                                </td>
                                <td>
                                    @if($item->fasilitas)
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach(array_slice(array_map('trim', explode(',', $item->fasilitas)), 0, 2) as $f)
                                        <span class="badge border small rounded-pill px-2 py-1" style="background: rgba(var(--secondary-color-rgb), 0.1); border-color: var(--border-color); color: var(--text-secondary); font-size: 0.75rem;">{{ $f }}</span>
                                        @endforeach
                                        @if(count(explode(',', $item->fasilitas)) > 2)
                                        <span class="badge small rounded-pill px-2 py-1" style="background: var(--secondary-color); color: white; font-size: 0.75rem;" title="{{ $item->fasilitas }}">+{{ count(explode(',', $item->fasilitas)) - 2 }}</span>
                                        @endif
                                    </div>
                                    @else
                                    <small class="text-muted">-</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge px-3 py-2 rounded-3" style="background: rgba(var(--warning-color-rgb), 0.1); color: var(--warning-color); font-weight: 600;">
                                        <i class="bi bi-door-closed me-1"></i>{{ $item->jumlah_kamar }} Kamar
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @if($item->latitude && $item->longitude)
                                        <a 
                                            href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" 
                                            target="_blank"
                                            class="btn btn-sm"
                                            data-bs-toggle="tooltip"
                                            title="Buka di Google Maps"
                                            style="background: rgba(var(--success-color-rgb), 0.1); border: 1px solid var(--success-color); color: var(--success-color);"
                                        >
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </a>
                                        @endif
                                        <a 
                                            href="{{ route('kontrakan.show', $item->id) }}" 
                                            class="btn btn-sm"
                                            data-bs-toggle="tooltip"
                                            title="Lihat Detail"
                                            style="background: rgba(var(--info-color-rgb), 0.1); border: 1px solid var(--info-color); color: var(--info-color);"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a 
                                            href="{{ route('kontrakan.edit', $item->id) }}" 
                                            class="btn btn-sm"
                                            data-bs-toggle="tooltip"
                                            title="Edit Data"
                                            style="background: rgba(var(--warning-color-rgb), 0.1); border: 1px solid var(--warning-color); color: var(--warning-color);"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        
                                        @if(auth()->user()->role == 'super_admin')
                                        <button 
                                            type="button" 
                                            class="btn btn-sm"
                                            data-bs-toggle="tooltip"
                                            title="Hapus Data"
                                            style="background: rgba(var(--danger-color-rgb), 0.1); border: 1px solid var(--danger-color); color: var(--danger-color);"
                                            onclick="showDeleteModal({{ $item->id }}, {{ json_encode($item->nama) }}, {{ json_encode($item->alamat) }}, {{ json_encode('Rp ' . number_format($item->harga, 0, ',', '.')) }})"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>

                                        <form 
                                            id="delete-form-{{ $item->id }}"
                                            action="{{ route('kontrakan.destroy', $item->id) }}" 
                                            method="POST" 
                                            class="d-none"
                                        >
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        @else
                                        <button 
                                            type="button" 
                                            class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="tooltip"
                                            title="Hanya Super Admin"
                                            disabled
                                        >
                                            <i class="bi bi-lock"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($kontrakan->hasPages())
        <div class="card-footer border-0 py-4" style="background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(248,249,255,0.95)); backdrop-filter: blur(10px); border-radius: 0 0 1.25rem 1.25rem;">
            {{ $kontrakan->links('vendor.pagination.custom') }}
        </div>
        @endif
    </div>
</div>
<!-- ========== MODAL KONFIRMASI HAPUS SINGLE ========== -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.5rem; background: white; backdrop-filter: blur(20px);">
            <div class="modal-header border-0 pb-2 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark">Konfirmasi Hapus Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background-size: 1.2rem;"></button>
            </div>
            <div class="modal-body text-center px-4 pb-4">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.2)); border-radius: 50%; border: 3px solid rgba(220, 53, 69, 0.2);">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
                
                <h5 class="fw-bold mb-2 text-dark">Konfirmasi Hapus Data</h5>
                <p class="text-muted mb-4 small">Tindakan ini tidak dapat dibatalkan dan akan menghapus data secara permanen!</p>
                
                <div class="rounded-3 p-3 mb-4 text-start border" style="background-color: #fdf2f2; border-color: #fecaca !important;">
                    <p class="mb-3 small text-muted">Apakah Anda yakin ingin menghapus kontrakan berikut:</p>
                    <div class="d-flex align-items-start mb-2">
                        <i class="bi bi-building me-2 mt-1" style="color: #667eea;"></i>
                        <strong id="deleteNama" class="text-dark"></strong>
                    </div>
                    <div class="d-flex align-items-start mb-2">
                        <i class="bi bi-geo-alt me-2 mt-1 text-danger"></i>
                        <small id="deleteAlamat" class="text-muted"></small>
                    </div>
                    <div class="d-flex align-items-start">
                        <i class="bi bi-currency-dollar me-2 mt-1 text-success"></i>
                        <small id="deleteHarga" class="fw-semibold text-success"></small>
                    </div>
                </div>
                
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal" style="border-radius: 0.75rem; border: 1px solid #dee2e6;">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </button>
                    <button type="button" class="btn btn-danger px-4 py-2" id="confirmDeleteBtn" style="border-radius: 0.75rem; font-weight: 600;">
                        <i class="bi bi-trash me-2"></i>Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
            </div>
        </div>
    </div>
</div>

<!-- ========== MODAL KONFIRMASI BULK DELETE ========== -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.5rem; background: white; backdrop-filter: blur(20px);">
            <div class="modal-header border-0 pb-2 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark">Konfirmasi Hapus Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background-size: 1.2rem;"></button>
            </div>
            <div class="modal-body text-center px-4 pb-4">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.2)); border-radius: 50%; border: 3px solid rgba(220, 53, 69, 0.2);">
                        <i class="bi bi-trash-fill text-danger" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
                
                <h5 class="fw-bold mb-2 text-dark">Konfirmasi Hapus Multiple Data</h5>
                <p class="text-muted mb-4 small">Tindakan ini tidak dapat dibatalkan dan akan menghapus data secara permanen!</p>
                
                <div class="rounded-3 p-3 mb-4 text-start border" style="background-color: #fdf2f2; border-color: #fecaca !important;">
                    <p class="mb-3 small text-muted">
                        Anda akan menghapus <strong id="bulkDeleteCount" class="text-danger">0</strong> kontrakan:
                    </p>
                    <div id="bulkDeleteList" style="max-height: 200px; overflow-y: auto;" class="small text-dark">
                        <!-- List akan diisi JavaScript -->
                    </div>
                </div>
                
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal" style="border-radius: 0.75rem; border: 1px solid #dee2e6;">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </button>
                    <button type="button" class="btn btn-danger px-4 py-2" id="confirmBulkDeleteBtn" style="border-radius: 0.75rem; font-weight: 600;">
                        <i class="bi bi-trash me-2"></i>Ya, Hapus Semua
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Form untuk Bulk Delete -->
<form id="bulkDeleteForm" action="{{ route('kontrakan.bulk-destroy') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="ids" id="bulkDeleteIds">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== ENHANCED MOBILE FEATURES ==========
    
    // Touch-friendly interactions
    const cards = document.querySelectorAll('.glass-card, .hover-row');
    cards.forEach(card => {
        card.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        });
        card.addEventListener('touchend', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Mobile search suggestions
    const searchInput = document.getElementById('searchInput');
    const searchSuggestions = document.getElementById('searchSuggestions');
    
    if (searchInput && searchSuggestions) {
        const commonSearches = ['WiFi', 'AC', 'Dapur', 'Kamar Mandi Dalam', 'Parkir', 'Listrik', 'Air', 'Dekat Kampus'];
        
        searchInput.addEventListener('input', function() {
            const value = this.value.toLowerCase();
            if (value.length > 0) {
                const matches = commonSearches.filter(item => 
                    item.toLowerCase().includes(value)
                ).slice(0, 5);
                
                if (matches.length > 0) {
                    searchSuggestions.innerHTML = matches.map(match => 
                        `<div class="search-suggestion-item" onclick="selectSuggestion('${match}')">
                            <i class="bi bi-search me-2"></i>${match}
                        </div>`
                    ).join('');
                    searchSuggestions.style.display = 'block';
                } else {
                    searchSuggestions.style.display = 'none';
                }
            } else {
                searchSuggestions.style.display = 'none';
            }
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                searchSuggestions.style.display = 'none';
            }
        });
    }

    // Mobile clear search buttons
    const clearSearchMobile = document.getElementById('clearSearchMobile');
    if (clearSearchMobile) {
        clearSearchMobile.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            window.location.href = '{{ route('kontrakan.index') }}';
        });
    }

    // ========== FILTER TOGGLE ==========
    const collapseElement = document.getElementById('collapseFilters');
    const toggleIcon = document.getElementById('filterToggleIcon');
    
    if (collapseElement && toggleIcon) {
        collapseElement.addEventListener('show.bs.collapse', function() {
            toggleIcon.style.transform = 'rotate(180deg)';
        });
        
        collapseElement.addEventListener('hide.bs.collapse', function() {
            toggleIcon.style.transform = 'rotate(0deg)';
        });
    }
    
    // ========== CLEAR SEARCH BUTTON ==========
    const clearSearchBtn = document.getElementById('clearSearch');
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            document.getElementById('searchInput').value = '';
            document.getElementById('filterForm').submit();
        });
    }
});
</script>

<style>
/* ========== GRADIENT HEADER ========== */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* ========== HOVER EFFECTS ========== */
.hover-row {
    transition: all 0.3s ease;
}

.hover-row:hover {
    background-color: #f8f9fa;
    transform: scale(1.001);
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.hover-lift-mobile {
    transition: all 0.3s ease;
}

.hover-lift-mobile:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* ========== BUTTON GROUPS ========== */
.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
}

/* ========== BUTTONS ========== */
.btn {
    transition: all 0.2s ease;
}

.btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}

/* ========== CHECKBOXES ========== */
.form-check-input {
    cursor: pointer;
    width: 18px;
    height: 18px;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-check-input:indeterminate {
    background-color: #6c757d;
    border-color: #6c757d;
}

/* ========== SELECT ALL CHECKBOX ENHANCED ========== */
.select-all-checkbox {
    background-color: rgba(255,255,255,0.9) !important;
    transition: all 0.3s ease;
}

.select-all-checkbox:checked {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
}

.select-all-checkbox:indeterminate {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
}

.select-all-checkbox:hover {
    transform: scale(1.15);
    box-shadow: 0 0 10px rgba(255,255,255,0.5);
}

.row-checkbox {
    transition: all 0.2s ease;
}

.row-checkbox:checked {
    background-color: #667eea !important;
    border-color: #667eea !important;
}

.row-checkbox:hover {
    transform: scale(1.1);
}

/* ========== BADGES ========== */
.badge {
    font-weight: 500;
    padding: 0.35em 0.65em;
}

/* ========== CARDS ========== */
.card {
    transition: all 0.3s ease;
}

/* ========== RADIO BUTTONS (SORT) ========== */
.btn-check:checked + .btn {
    transform: scale(1.05);
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}

/* ========== ANIMATIONS ========== */
@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-3px); }
    20%, 40%, 60%, 80% { transform: translateX(3px); }
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}

#bulkActionBar {
    animation: slideInRight 0.3s ease-out;
}

/* ========== MODAL ========== */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.65);
}

.modal-content {
    border: none;
}

/* ========== COLLAPSE ========== */
.collapse {
    transition: all 0.3s ease;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: #0d6efd;
    box-shadow: none;
}

/* ========== PAGINATION ========== */
.pagination {
    margin-bottom: 0;
}

.page-link {
    color: #667eea;
    border-radius: 0.25rem;
    margin: 0 0.1rem;
}

.page-item.active .page-link {
    background-color: #667eea;
    border-color: #667eea;
}

.page-link:hover {
    color: #764ba2;
    background-color: #f8f9fa;
}

/* ========== TABLE ========== */
.table tbody tr {
    transition: all 0.2s ease;
}

.avatar-sm {
    transition: all 0.3s ease;
}

.hover-row:hover .avatar-sm {
    transform: scale(1.1);
}

/* ========== ALERTS ========== */
.alert {
    animation: slideDown 0.4s ease-out;
    border: none;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ========== MOBILE OPTIMIZATIONS ========== */
@media (max-width: 767px) {
    .container-fluid {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .btn-sm {
        font-size: 0.8rem;
        padding: 0.35rem 0.6rem;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .modal-dialog {
        margin: 0.5rem;
    }
    
    h2 {
        font-size: 1.5rem !important;
    }
    
    .input-group-lg .form-control {
        font-size: 0.95rem;
    }
}

/* ========== TABLET OPTIMIZATIONS ========== */
@media (min-width: 768px) and (max-width: 991px) {
    .table {
        font-size: 0.9rem;
    }
    
    .btn-group .btn {
        padding: 0.3rem 0.5rem;
        font-size: 0.85rem;
    }
}

/* ========== FORM STYLING ========== */
.form-label.fw-bold {
    color: #495057;
    font-size: 0.95rem;
}

.form-control:focus,
.form-check-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
}

/* ========== SMOOTH SCROLL ========== */
html {
    scroll-behavior: smooth;
}

/* ========== BETTER SPACING ========== */
.gap-1 {
    gap: 0.25rem !important;
}

.gap-2 {
    gap: 0.5rem !important;
}

.gap-3 {
    gap: 1rem !important;
}

.gap-4 {
    gap: 1.5rem !important;
}

/* ========== LOADING STATE ========== */
.btn:disabled {
    cursor: not-allowed;
    opacity: 0.65;
}

/* ========== EMPTY STATE ========== */
.bi-inbox {
    opacity: 0.3;
}

/* ========== PRINT STYLES ========== */
@media print {
    .btn,
    .alert,
    .modal,
    .pagination,
    #bulkActionBar {
        display: none !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
    }
}
</style>

@endsection

@section('scripts')
<script>
// ========== GLOBAL VARIABLES ==========
let deleteItemId = null;
let selectedIds = [];

// ========== SINGLE DELETE FUNCTION ==========
window.showDeleteModal = function(id, nama, alamat, harga) {
    console.log('showDeleteModal called:', id, nama, alamat, harga);
    deleteItemId = id;
    
    const deleteNamaEl = document.getElementById('deleteNama');
    const deleteAlamatEl = document.getElementById('deleteAlamat');
    const deleteHargaEl = document.getElementById('deleteHarga');
    const deleteModalEl = document.getElementById('deleteModal');
    
    if (deleteNamaEl) deleteNamaEl.textContent = nama;
    if (deleteAlamatEl) deleteAlamatEl.textContent = alamat;
    if (deleteHargaEl) deleteHargaEl.textContent = harga;
    
    if (deleteModalEl && typeof bootstrap !== 'undefined') {
        const deleteModal = new bootstrap.Modal(deleteModalEl);
        deleteModal.show();
    } else {
        console.error('Modal element or Bootstrap not found');
    }
};

// ========== BULK DELETE FUNCTIONS ==========
window.updateBulkDeleteButton = function() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const count = checkedBoxes.length;
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCountSpan = document.getElementById('selectedCount');
    const selectAllCheckbox = document.getElementById('selectAll');
    const totalCheckboxes = document.querySelectorAll('.row-checkbox').length;
    
    console.log('updateBulkDeleteButton - Checked:', count, 'Total:', totalCheckboxes);
    
    if (count > 0 && bulkDeleteBtn) {
        bulkDeleteBtn.style.display = 'block';
        if (selectedCountSpan) selectedCountSpan.textContent = count;
    } else if (bulkDeleteBtn) {
        bulkDeleteBtn.style.display = 'none';
    }
    
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = count === totalCheckboxes && count > 0;
        selectAllCheckbox.indeterminate = count > 0 && count < totalCheckboxes;
    }
};

window.toggleSelectAll = function(source) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
    updateBulkDeleteButton();
};

window.showBulkDeleteModal = function() {
    const checkboxes = document.querySelectorAll('.row-checkbox:checked');
    selectedIds = Array.from(checkboxes).map(cb => cb.value);
    
    console.log('showBulkDeleteModal - Selected IDs:', selectedIds);
    
    if (selectedIds.length === 0) {
        alert('Pilih minimal 1 kontrakan untuk dihapus!');
        return;
    }
    
    const bulkDeleteCountEl = document.getElementById('bulkDeleteCount');
    const bulkDeleteListEl = document.getElementById('bulkDeleteList');
    const bulkDeleteModalEl = document.getElementById('bulkDeleteModal');
    
    if (bulkDeleteCountEl) bulkDeleteCountEl.textContent = selectedIds.length;
    
    if (bulkDeleteListEl) {
        let listHtml = '<ul class="mb-0 ps-3">';
        checkboxes.forEach(cb => {
            const nama = cb.getAttribute('data-nama') || 'Kontrakan #' + cb.value;
            listHtml += '<li class="mb-1">' + nama + '</li>';
        });
        listHtml += '</ul>';
        bulkDeleteListEl.innerHTML = listHtml;
    }
    
    if (bulkDeleteModalEl && typeof bootstrap !== 'undefined') {
        const bulkDeleteModal = new bootstrap.Modal(bulkDeleteModalEl);
        bulkDeleteModal.show();
    }
};

// ========== INITIALIZE ON DOM READY ==========
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Ready - Initializing delete handlers');
    
    // Confirm Single Delete Button
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            console.log('Confirm delete clicked, ID:', deleteItemId);
            if (deleteItemId) {
                const form = document.getElementById('delete-form-' + deleteItemId);
                if (form) {
                    form.submit();
                } else {
                    console.error('Delete form not found for ID:', deleteItemId);
                }
            }
        });
    }
    
    // Confirm Bulk Delete Button
    const confirmBulkDeleteBtn = document.getElementById('confirmBulkDeleteBtn');
    if (confirmBulkDeleteBtn) {
        confirmBulkDeleteBtn.addEventListener('click', function() {
            console.log('Confirm bulk delete clicked, IDs:', selectedIds);
            if (selectedIds.length > 0) {
                const bulkDeleteIds = document.getElementById('bulkDeleteIds');
                const bulkDeleteForm = document.getElementById('bulkDeleteForm');
                if (bulkDeleteIds && bulkDeleteForm) {
                    bulkDeleteIds.value = JSON.stringify(selectedIds);
                    bulkDeleteForm.submit();
                }
            }
        });
    }
    
    // Select All Checkbox
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            toggleSelectAll(this);
        });
    }
    
    // Row Checkboxes - Event Delegation
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('row-checkbox')) {
            updateBulkDeleteButton();
        }
    });
    
    // Initialize button state
    updateBulkDeleteButton();
});

// ========== RANGE SLIDER HANDLERS ==========
document.addEventListener('DOMContentLoaded', function() {
    const hargaMinInput = document.getElementById('harga_min');
    const hargaMaxInput = document.getElementById('harga_max');
    const hargaMinDisplay = document.getElementById('hargaMinDisplay');
    const hargaMaxDisplay = document.getElementById('hargaMaxDisplay');
    
    const jarakInput = document.getElementById('jarak_max');
    const jarakDisplay = document.getElementById('jarakDisplay');
    
    const kamarMinInput = document.getElementById('kamar_min');
    const kamarMaxInput = document.getElementById('kamar_max');
    const kamarMinDisplay = document.getElementById('kamarMinDisplay');
    const kamarMaxDisplay = document.getElementById('kamarMaxDisplay');
    
    // Format number to IDR
    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).replace('IDR', 'Rp').trim();
    }
    
    // Harga Min Handler
    if (hargaMinInput) {
        hargaMinInput.addEventListener('input', function() {
            hargaMinDisplay.textContent = formatCurrency(this.value);
            if (parseInt(this.value) > parseInt(hargaMaxInput.value)) {
                hargaMaxInput.value = this.value;
                hargaMaxDisplay.textContent = formatCurrency(this.value);
            }
            updateFilterCount();
        });
    }
    
    // Harga Max Handler
    if (hargaMaxInput) {
        hargaMaxInput.addEventListener('input', function() {
            hargaMaxDisplay.textContent = formatCurrency(this.value);
            if (parseInt(this.value) < parseInt(hargaMinInput.value)) {
                hargaMinInput.value = this.value;
                hargaMinDisplay.textContent = formatCurrency(this.value);
            }
            updateFilterCount();
        });
    }
    
    // Jarak Handler
    if (jarakInput) {
        jarakInput.addEventListener('input', function() {
            jarakDisplay.textContent = parseFloat(this.value).toFixed(1);
            updateFilterCount();
        });
    }
    
    // Kamar Min Handler
    if (kamarMinInput) {
        kamarMinInput.addEventListener('input', function() {
            kamarMinDisplay.textContent = this.value;
            if (parseInt(this.value) > parseInt(kamarMaxInput.value)) {
                kamarMaxInput.value = this.value;
                kamarMaxDisplay.textContent = this.value;
            }
            updateFilterCount();
        });
    }
    
    // Kamar Max Handler
    if (kamarMaxInput) {
        kamarMaxInput.addEventListener('input', function() {
            kamarMaxDisplay.textContent = this.value;
            if (parseInt(this.value) < parseInt(kamarMinInput.value)) {
                kamarMinInput.value = this.value;
                kamarMinDisplay.textContent = this.value;
            }
            updateFilterCount();
        });
    }
    
    // Update filter count badge
    function updateFilterCount() {
        const form = document.getElementById('filterForm');
        const filterCount = document.getElementById('filterCount');
        const filterCountNum = document.getElementById('filterCountNum');
        
        let count = 0;
        const inputs = form.querySelectorAll('input[type="text"], input[type="number"], input[type="range"], input[type="checkbox"]:checked');
        
        inputs.forEach(input => {
            if (input.value && input.value !== input.defaultValue && input.name !== 'sort_by') {
                count++;
            }
        });
        
        if (count > 0) {
            filterCount.style.display = 'inline-block';
            filterCountNum.textContent = count;
        } else {
            filterCount.style.display = 'none';
        }
    }
    
    // Initialize filter count on page load
    updateFilterCount();
    
    // Monitor all filter changes
    document.querySelectorAll('input, select').forEach(el => {
        el.addEventListener('change', updateFilterCount);
    });
});

// ========== CLEAR SEARCH ==========
document.getElementById('clearSearch')?.addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterForm').submit();
});

// ========== FILTER TOGGLE ICON ANIMATION ==========
document.querySelector('[data-bs-toggle="collapse"][data-bs-target="#collapseFilters"]')?.addEventListener('click', function() {
    const icon = document.getElementById('filterToggleIcon');
    icon.style.transition = 'transform 0.3s ease';
    icon.style.transform = icon.style.transform === 'rotate(180deg)' ? 'rotate(0deg)' : 'rotate(180deg)';
});

// Auto-open if filters are active
const hasActiveFilters = {{ 
    (request()->hasAny(['search', 'harga_min', 'harga_max', 'jarak_max', 'luas_min', 'luas_max', 'fasilitas_filter']) || 
    ($filters['sort_by'] ?? 'terbaru') != 'terbaru') ? 'true' : 'false' 
}};

if (hasActiveFilters) {
    const collapseElement = document.getElementById('collapseFilters');
    const collapse = new bootstrap.Collapse(collapseElement, {
        toggle: true
    });
    document.getElementById('filterToggleIcon').style.transform = 'rotate(180deg)';
}

// ========== INITIALIZE TOOLTIPS ==========
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// ========== RANGE SLIDER HANDLERS ==========
document.addEventListener('DOMContentLoaded', function() {
    const hargaMinInput = document.getElementById('harga_min');
    const hargaMaxInput = document.getElementById('harga_max');
    const hargaMinDisplay = document.getElementById('hargaMinDisplay');
    const hargaMaxDisplay = document.getElementById('hargaMaxDisplay');
    
    const jarakInput = document.getElementById('jarak_max');
    const jarakDisplay = document.getElementById('jarakDisplay');
    
    const kamarMinInput = document.getElementById('kamar_min');
    const kamarMaxInput = document.getElementById('kamar_max');
    const kamarMinDisplay = document.getElementById('kamarMinDisplay');
    const kamarMaxDisplay = document.getElementById('kamarMaxDisplay');
    
    // Format number to IDR
    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).replace('IDR', 'Rp').trim();
    }
    
    // Harga Min Handler
    if (hargaMinInput) {
        hargaMinInput.addEventListener('input', function() {
            hargaMinDisplay.textContent = formatCurrency(this.value);
            if (parseInt(this.value) > parseInt(hargaMaxInput.value)) {
                hargaMaxInput.value = this.value;
                hargaMaxDisplay.textContent = formatCurrency(this.value);
            }
            updateFilterCount();
        });
    }
    
    // Harga Max Handler
    if (hargaMaxInput) {
        hargaMaxInput.addEventListener('input', function() {
            hargaMaxDisplay.textContent = formatCurrency(this.value);
            if (parseInt(this.value) < parseInt(hargaMinInput.value)) {
                hargaMinInput.value = this.value;
                hargaMinDisplay.textContent = formatCurrency(this.value);
            }
            updateFilterCount();
        });
    }
    
    // Jarak Handler
    if (jarakInput) {
        jarakInput.addEventListener('input', function() {
            jarakDisplay.textContent = parseFloat(this.value).toFixed(1);
            updateFilterCount();
        });
    }
    
    // Kamar Min Handler
    if (kamarMinInput) {
        kamarMinInput.addEventListener('input', function() {
            kamarMinDisplay.textContent = this.value;
            if (parseInt(this.value) > parseInt(kamarMaxInput.value)) {
                kamarMaxInput.value = this.value;
                kamarMaxDisplay.textContent = this.value;
            }
            updateFilterCount();
        });
    }
    
    // Kamar Max Handler
    if (kamarMaxInput) {
        kamarMaxInput.addEventListener('input', function() {
            kamarMaxDisplay.textContent = this.value;
            if (parseInt(this.value) < parseInt(kamarMinInput.value)) {
                kamarMinInput.value = this.value;
                kamarMinDisplay.textContent = this.value;
            }
            updateFilterCount();
        });
    }
    
    // Update filter count badge
    function updateFilterCount() {
        const form = document.getElementById('filterForm');
        const filterCount = document.getElementById('filterCount');
        const filterCountNum = document.getElementById('filterCountNum');
        
        let count = 0;
        const inputs = form.querySelectorAll('input[type="text"], input[type="number"], input[type="range"], input[type="checkbox"]:checked');
        
        inputs.forEach(input => {
            if (input.value && input.value !== input.defaultValue && input.name !== 'sort_by') {
                count++;
            }
        });
        
        if (count > 0) {
            filterCount.style.display = 'inline-block';
            filterCountNum.textContent = count;
        } else {
            filterCount.style.display = 'none';
        }
    }
    
    // Initialize filter count on page load
    updateFilterCount();
    
    // Monitor all filter changes
    document.querySelectorAll('input, select').forEach(el => {
        el.addEventListener('change', updateFilterCount);
    });
});
</script>
@endsection
