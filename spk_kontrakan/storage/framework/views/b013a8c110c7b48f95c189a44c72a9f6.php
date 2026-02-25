

<?php $__env->startSection('title', 'Daftar Laundry'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2 px-md-3 page-laundry" style="padding-top: 0.5rem; padding-bottom: 1rem;">
    <!-- Enhanced Mobile-First Styling with Purple Theme -->
    <style>
        /* CSS Variables for Laundry Theme - Purple Theme */
        :root {
            --laundry-primary: #667eea;
            --laundry-secondary: #764ba2;
            --laundry-accent: #818cf8;
            --laundry-light: #e0e7ff;
            --laundry-gradient: linear-gradient(135deg, #818cf8 0%, #667eea 50%, #764ba2 100%);
        }

        /* Page Background & Pattern - Purple Theme */
        .page-laundry {
            position: relative;
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f7ff 0%, #f3f1ff 50%, #ede9fe 100%);
        }
        
        .page-laundry::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(102, 126, 234, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(118, 75, 162, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(129, 140, 248, 0.05) 0%, transparent 60%);
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
            top: 10%;
            right: 8%;
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.05) 100%);
            border-radius: 50%;
            filter: blur(25px);
            animation: float 7s ease-in-out infinite;
        }
        
        .floating-decoration.deco-2 {
            bottom: 25%;
            left: 5%;
            width: 180px;
            height: 180px;
            background: linear-gradient(135deg, rgba(118, 75, 162, 0.1) 0%, rgba(102, 126, 234, 0.05) 100%);
            border-radius: 50%;
            filter: blur(35px);
            animation: float 9s ease-in-out infinite reverse;
        }
        
        .floating-decoration.deco-3 {
            top: 55%;
            right: 12%;
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, rgba(129, 140, 248, 0.12) 0%, transparent 100%);
            border-radius: 50%;
            filter: blur(20px);
            animation: float 5s ease-in-out infinite 1s;
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
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
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            margin-bottom: 0.75rem;
        }
        
        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--stat-color);
            line-height: 1.2;
        }
        
        .stat-card .stat-label {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 500;
        }

        /* Enhanced Content Card */
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
            background: linear-gradient(90deg, #818cf8 0%, #667eea 50%, #818cf8 100%);
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
        }
        
        @keyframes gradientMove {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .laundry-header {
            background: linear-gradient(135deg, #818cf8 0%, #667eea 50%, #764ba2 100%);
            border-radius: 0.75rem;
            padding: 1rem;
            color: white;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .laundry-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        
        .btn-laundry {
            background: rgba(255,255,255,0.95);
            color: #667eea;
            font-weight: 600;
            border: 2px solid rgba(255,255,255,0.3);
            padding: 0.6rem 1rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .btn-laundry:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            background: white;
            color: #764ba2;
        }

        /* Enhanced Table - Purple Theme */
        .enhanced-table thead {
            background: linear-gradient(135deg, #818cf8 0%, #667eea 100%);
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
        
        @media (max-width: 768px) {
            .laundry-header {
                padding: 0.75rem;
                margin-bottom: 0.75rem;
                border-radius: 0.5rem;
            }
            
            .laundry-header h2 {
                font-size: 1.35rem;
            }
            
            .btn-laundry {
                width: 100%;
                margin-bottom: 0.5rem;
                padding: 0.5rem 0.75rem;
                font-size: 0.85rem;
                min-height: 44px;
            }
            
            .stats-overview {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.5rem;
            }
            
            .stat-card {
                padding: 0.6rem;
            }
            
            .stat-card .stat-value {
                font-size: 1.1rem !important;
            }
            
            .stat-card .stat-icon {
                width: 30px !important;
                height: 30px !important;
            }
            
            .stat-card .stat-label {
                font-size: 0.65rem !important;
            }
            
            /* Better touch targets */
            .btn {
                min-height: 44px;
            }
            
            .main-content-card {
                border-radius: 12px;
                margin: 0 -0.5rem;
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
                padding: 0.5rem;
            }
            
            .stat-card .stat-value {
                font-size: 1rem !important;
            }
            
            .stat-card .stat-icon {
                width: 28px !important;
                height: 28px !important;
                font-size: 0.85rem !important;
            }
            
            .stat-card .stat-label {
                font-size: 0.6rem !important;
            }
            
            .laundry-header {
                padding: 0.6rem 0.5rem;
            }
            
            .laundry-header h2 {
                font-size: 1.2rem;
            }
            
            /* Hide some decorations on small mobile */
            .floating-decoration.deco-2,
            .floating-decoration.deco-3 {
                display: none;
            }
            
            .d-flex.flex-column.flex-md-row.gap-2 {
                gap: 0.4rem !important;
            }
            
            .btn-laundry {
                padding: 0.45rem 0.65rem;
                font-size: 0.8rem;
            }
        }
        
        @media (max-width: 380px) {
            .laundry-header h2 {
                font-size: 1.1rem;
            }
            
            .stat-card .stat-value {
                font-size: 0.9rem !important;
            }
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
    </style>
    
    <!-- Floating Decorations -->
    <div class="floating-decoration deco-1"></div>
    <div class="floating-decoration deco-2"></div>
    <div class="floating-decoration deco-3"></div>
    
    <!-- Enhanced Header Section -->
    <div class="laundry-header">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-12 col-md-8">
                <h2 class="mb-1 fw-bold">
                    <i class="bi bi-droplet-fill me-2"></i>
                    <span class="d-none d-sm-inline">🧺 Daftar Laundry</span>
                    <span class="d-sm-none">🧺 Laundry</span>
                </h2>
                <p class="mb-0 opacity-90 small d-none d-md-block">
                    Kelola dan pantau data layanan laundry yang tersedia
                </p>
            </div>
            <div class="col-12 col-md-4 text-md-end mt-2 mt-md-0">
                <div class="d-flex flex-column flex-md-row gap-2">
                    <a href="<?php echo e(route('laundry.map')); ?>" class="btn btn-laundry">
                        <i class="bi bi-map me-2"></i>
                        <span class="d-none d-sm-inline">Lihat Peta</span>
                        <span class="d-sm-none">Peta</span>
                    </a>
                    <a href="<?php echo e(route('laundry.create')); ?>" class="btn btn-laundry">
                        <i class="bi bi-plus-circle me-2"></i>
                        <span class="d-none d-sm-inline">Tambah Laundry</span>
                        <span class="d-sm-none">Tambah</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Alert Success -->
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 py-2 mb-2" role="alert" style="background: linear-gradient(135deg, var(--success-color), #20c997); color: white; border-left: 4px solid white;">
        <i class="bi bi-check-circle-fill me-2"></i>
        <span class="small"><?php echo e(session('success')); ?></span>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Enhanced Alert Error -->
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 py-2 mb-2" role="alert" style="background: linear-gradient(135deg, var(--danger-color), #e74c3c); color: white; border-left: 4px solid white;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <span class="small"><?php echo e(session('error')); ?></span>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Enhanced Filter & Search Card -->
    <div class="glass-card mb-3" style="overflow: hidden; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
        <div class="filter-header p-2 px-3" style="background: linear-gradient(135deg, #667eea 0%, #667eea 100%); color: white; cursor: pointer; user-select: none; transition: all 0.3s ease;"
             data-bs-toggle="collapse" data-bs-target="#laundryFilterCollapse">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="mb-0 fw-bold" style="color: white !important;">
                        <i class="bi bi-funnel me-2"></i>
                        <span>Pencarian & Filter Laundry</span>
                    </h6>
                </div>
                <div class="col-auto">
                    <i class="bi bi-chevron-down" id="laundryFilterToggleIcon" style="transition: transform 0.3s ease;"></i>
                </div>
            </div>
        </div>

        <div class="collapse" id="laundryFilterCollapse">
            <div class="p-2 p-md-3" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                <form action="<?php echo e(route('laundry.index')); ?>" method="GET" id="filterForm">
                    <!-- Main Search Bar -->
                    <div class="mb-2">
                        <label class="form-label fw-semibold mb-1 small">
                            <i class="bi bi-search me-1" style="color: #667eea;"></i>
                            Cari Laundry
                        </label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-search" style="color: #667eea;"></i>
                            </span>
                            <input 
                                type="text" 
                                name="search" 
                                class="form-control form-control-sm border-start-0" 
                                placeholder="Cari nama, alamat, fasilitas..."
                                value="<?php echo e($filters['search'] ?? ''); ?>"
                                id="searchInput"
                            >
                            <button class="btn" type="submit" style="background: #667eea; color: white;">
                                <i class="bi bi-search me-1 me-md-2"></i><span class="d-none d-sm-inline">Cari</span>
                            </button>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="bi bi-lightbulb me-1" style="color: #ffc107;"></i>
                            <span class="d-none d-md-inline">💡 Coba "WiFi", "Antar Jemput", "24 Jam" untuk hasil lebih spesifik</span>
                            <span class="d-md-none">💡 Coba "WiFi", "Antar Jemput"</span>
                        </small>
                    </div>

                    <!-- Advanced Filters -->
                    <div class="border-top mt-2 pt-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-semibold mb-0 small" style="color: #667eea;">
                                <i class="bi bi-sliders me-1"></i>
                                Filter Lanjutan
                            </h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="resetFilters" onclick="window.location.href='<?php echo e(route('laundry.index')); ?>'">
                                <i class="bi bi-arrow-clockwise me-1"></i><span class="d-none d-sm-inline">Reset</span>
                            </button>
                        </div>
                        
                        <div class="row g-2">
                            <!-- 💰 Range Harga dengan Slider -->
                            <div class="col-12">
                                <label class="form-label fw-semibold mb-1 small">
                                    <i class="bi bi-currency-dollar me-1" style="color: #28a745;"></i>
                                    💰 Range Harga per KG
                                </label>
                                <div class="range-slider p-2 rounded-3" style="background: rgba(40, 167, 69, 0.05); border: 1px solid rgba(40, 167, 69, 0.1);">
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <div class="range-value text-center p-1 rounded" style="background: white; border: 2px solid #28a745; color: #28a745; font-weight: 600; font-size: 0.85rem;">
                                                <small class="d-block text-muted" style="font-size: 0.7rem;">Min</small>
                                                <span id="hargaMinDisplay"><?php echo e(number_format($filters['harga_min'] ?? $hargaMin, 0, ',', '.')); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="range-value text-center p-1 rounded" style="background: white; border: 2px solid #28a745; color: #28a745; font-weight: 600; font-size: 0.85rem;">
                                                <small class="d-block text-muted" style="font-size: 0.7rem;">Max</small>
                                                <span id="hargaMaxDisplay"><?php echo e(number_format($filters['harga_max'] ?? $hargaMax, 0, ',', '.')); ?></span>
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
                                                min="<?php echo e($hargaMin); ?>" 
                                                max="<?php echo e($hargaMax); ?>" 
                                                value="<?php echo e($filters['harga_min'] ?? $hargaMin); ?>"
                                                step="1000"
                                                style="accent-color: #28a745;"
                                            >
                                            <small class="text-muted mt-1 d-block">Geser untuk minimum</small>
                                        </div>
                                        <div class="col-md-6">
                                            <input 
                                                type="range" 
                                                name="harga_max" 
                                                id="harga_max" 
                                                class="form-range" 
                                                min="<?php echo e($hargaMin); ?>" 
                                                max="<?php echo e($hargaMax); ?>" 
                                                value="<?php echo e($filters['harga_max'] ?? $hargaMax); ?>"
                                                step="1000"
                                                style="accent-color: #28a745;"
                                            >
                                            <small class="text-muted mt-1 d-block">Geser untuk maksimum</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1" style="color: #667eea;"></i>
                                    <span class="d-none d-md-inline">Range: Rp <?php echo e(number_format($hargaMin, 0, ',', '.')); ?> - Rp <?php echo e(number_format($hargaMax, 0, ',', '.')); ?></span>
                                    <span class="d-md-none">Rp <?php echo e(number_format($hargaMin, 0, ',', '.')); ?> - <?php echo e(number_format($hargaMax, 0, ',', '.')); ?></span>
                                </div>
                            </div>

                            <!-- 📍 Jarak dengan Slider -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1 small">
                                    <i class="bi bi-pin-map me-1" style="color: #dc3545;"></i>
                                    📍 Jarak Max dari Kampus
                                </label>
                                <div class="range-slider p-2 rounded-3" style="background: rgba(220, 53, 69, 0.05); border: 1px solid rgba(220, 53, 69, 0.1);">
                                    <div class="range-value text-center p-1 rounded mb-2" style="background: white; border: 2px solid #dc3545; color: #dc3545; font-weight: 600; font-size: 0.85rem;">
                                        <span id="jarakDisplay"><?php echo e($filters['jarak_max'] ?? '10'); ?></span> km
                                    </div>
                                    <input 
                                        type="range" 
                                        name="jarak_max" 
                                        id="jarak_max" 
                                        class="form-range" 
                                        min="0" 
                                        max="<?php echo e($jarakMaxKm); ?>" 
                                        value="<?php echo e($filters['jarak_max'] ?? 10); ?>"
                                        step="0.5"
                                        style="accent-color: #dc3545;"
                                    >
                                    <small class="text-muted mt-1 d-block">Geser untuk menyesuaikan</small>
                                </div>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1" style="color: #667eea;"></i>
                                    <span class="d-none d-md-inline">Jarak terjauh: <?php echo e($jarakMaxKm); ?> km</span>
                                    <span class="d-md-none">Max: <?php echo e($jarakMaxKm); ?> km</span>
                                </div>
                            </div>

                            <!-- ⚡ Jenis Layanan Checkboxes -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1 small">
                                    <i class="bi bi-speedometer2 me-1" style="color: #667eea;"></i>
                                    ⚡ Jenis Layanan
                                </label>
                                <div class="glass-card border-0" style="background: rgba(102, 126, 234, 0.05);">
                                    <div class="card-body p-2">
                                        <div class="row g-2">
                                            <?php $__currentLoopData = $jenisLayananUnique; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jenis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="checkbox" 
                                                        name="jenis_layanan_filter[]" 
                                                        value="<?php echo e(strtolower($jenis)); ?>" 
                                                        id="jenis_<?php echo e($loop->index); ?>"
                                                        <?php echo e(in_array(strtolower($jenis), $filters['jenis_layanan_filter'] ?? []) ? 'checked' : ''); ?>

                                                        style="accent-color: #667eea;"
                                                    >
                                                    <label class="form-check-label small" for="jenis_<?php echo e($loop->index); ?>" style="cursor: pointer;">
                                                        <i class="bi bi-check-circle me-1" style="color: #28a745;"></i><?php echo e($jenis); ?>

                                                    </label>
                                                </div>
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ✨ Fasilitas Checkboxes -->
                            <?php if($fasilitasUnique->count() > 0): ?>
                            <div class="col-12">
                                <label class="form-label fw-semibold mb-1 small">
                                    <i class="bi bi-star me-1" style="color: #ffc107;"></i>
                                    ✨ Fasilitas yang Diinginkan
                                </label>
                                <div class="glass-card border-0" style="background: rgba(255, 193, 7, 0.05);">
                                    <div class="card-body p-2">
                                        <div class="row g-2">
                                            <?php $__currentLoopData = $fasilitasUnique; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fasilitas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <div class="form-check">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="checkbox" 
                                                        name="fasilitas_filter[]" 
                                                        value="<?php echo e($fasilitas); ?>" 
                                                        id="fasilitas_<?php echo e($loop->index); ?>"
                                                        <?php echo e(in_array($fasilitas, $filters['fasilitas_filter'] ?? []) ? 'checked' : ''); ?>

                                                        style="accent-color: #ffc107;"
                                                    >
                                                    <label class="form-check-label small" for="fasilitas_<?php echo e($loop->index); ?>" style="cursor: pointer;">
                                                        <i class="bi bi-check-circle me-1" style="color: #28a745;"></i><?php echo e($fasilitas); ?>

                                                    </label>
                                                </div>
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">
                                    <i class="bi bi-info-circle me-1" style="color: #667eea;"></i>
                                    <span class="d-none d-md-inline">Pilih fasilitas (hasil akan menampilkan laundry yang memiliki SEMUA fasilitas terpilih)</span>
                                    <span class="d-md-none">Pilih fasilitas yang diinginkan</span>
                                </small>
                            </div>
                            <?php endif; ?>

                            <!-- 📋 Urutkan (Radio Buttons) -->
                            <div class="col-12">
                                <label class="form-label fw-semibold mb-1 small">
                                    <i class="bi bi-sort-down me-1" style="color: #667eea;"></i>
                                    📋 Urutkan Berdasarkan
                                </label>
                                <div class="row g-2">
                                    <div class="col-6 col-md-3">
                                        <input type="radio" class="btn-check" name="sort_by" id="sort_terbaru" value="terbaru" <?php echo e(($filters['sort_by'] ?? 'terbaru') == 'terbaru' ? 'checked' : ''); ?>>
                                        <label class="btn btn-outline-primary w-100" for="sort_terbaru" style="border-color: #667eea; color: #667eea;">
                                            <i class="bi bi-clock"></i> <span class="d-none d-sm-inline">Terbaru</span><span class="d-sm-none">🕑</span>
                                        </label>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <input type="radio" class="btn-check" name="sort_by" id="sort_termurah" value="harga_termurah" <?php echo e(($filters['sort_by'] ?? '') == 'harga_termurah' ? 'checked' : ''); ?>>
                                        <label class="btn btn-outline-success w-100" for="sort_termurah" style="border-color: #28a745; color: #28a745;">
                                            <i class="bi bi-currency-dollar"></i> <span class="d-none d-sm-inline">Termurah</span><span class="d-sm-none">💰</span>
                                        </label>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <input type="radio" class="btn-check" name="sort_by" id="sort_terdekat" value="jarak_terdekat" <?php echo e(($filters['sort_by'] ?? '') == 'jarak_terdekat' ? 'checked' : ''); ?>>
                                        <label class="btn btn-outline-danger w-100" for="sort_terdekat" style="border-color: #dc3545; color: #dc3545;">
                                            <i class="bi bi-pin-map"></i> <span class="d-none d-sm-inline">Terdekat</span><span class="d-sm-none">📍</span>
                                        </label>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <input type="radio" class="btn-check" name="sort_by" id="sort_nama" value="nama_asc" <?php echo e(($filters['sort_by'] ?? '') == 'nama_asc' ? 'checked' : ''); ?>>
                                        <label class="btn btn-outline-info w-100" for="sort_nama" style="border-color: #667eea; color: #667eea;">
                                            <i class="bi bi-sort-alpha-down"></i> <span class="d-none d-sm-inline">Nama A-Z</span><span class="d-sm-none">🔤</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex flex-column flex-md-row gap-2 mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary flex-fill" style="background: #667eea; border-color: #667eea;">
                                <i class="bi bi-funnel-fill me-2"></i>
                                <span class="d-none d-sm-inline">Terapkan Filter</span>
                                <span class="d-sm-none">Terapkan</span>
                            </button>
                            <a href="<?php echo e(route('laundry.index')); ?>" class="btn btn-outline-secondary flex-fill">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                <span class="d-none d-sm-inline">Reset Filter</span>
                                <span class="d-sm-none">Reset</span>
                            </a>
                        </div>
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
                    <span class="badge px-2 py-1 rounded-3" style="background: rgba(23, 162, 184, 0.1); color: #667eea; font-size: 0.85rem; font-weight: 600;">
                        <i class="bi bi-droplet-fill me-1"></i>
                        <strong><?php echo e($filteredCount ?? 0); ?></strong> 
                        <?php if(isset($filteredCount) && isset($totalLaundry) && $filteredCount < $totalLaundry): ?>
                        <span class="d-none d-md-inline">dari <?php echo e($totalLaundry); ?></span>
                        <?php endif; ?>
                        laundry
                    </span>

                    <!-- Active Filter Badges -->
                    <?php if($filters['search'] ?? false): ?>
                    <span class="badge px-2 py-1 rounded-3" style="background: rgba(23, 162, 184, 0.1); color: #667eea; font-size: 0.8rem;">
                        <i class="bi bi-search me-1"></i>
                        <span class="d-none d-sm-inline">"<?php echo e(Str::limit($filters['search'], 20)); ?>"</span>
                        <span class="d-sm-none">🔍</span>
                    </span>
                    <?php endif; ?>

                    <?php if(($filters['harga_min'] ?? false) || ($filters['harga_max'] ?? false)): ?>
                    <span class="badge px-2 py-1 rounded-3" style="background: rgba(40, 167, 69, 0.1); color: #28a745; font-size: 0.8rem;">
                        <i class="bi bi-currency-dollar me-1"></i>
                        <span class="d-none d-lg-inline">
                            Rp <?php echo e(number_format($filters['harga_min'] ?? 0, 0, ',', '.')); ?> - Rp <?php echo e(number_format($filters['harga_max'] ?? ($hargaMax ?? 0), 0, ',', '.')); ?>

                        </span>
                        <span class="d-lg-none">💰</span>
                    </span>
                    <?php endif; ?>

                    <?php if($filters['jarak_max'] ?? false): ?>
                    <span class="badge px-2 py-1 rounded-3" style="background: rgba(220, 53, 69, 0.1); color: #dc3545; font-size: 0.85rem;">
                        <i class="bi bi-pin-map me-1"></i>
                        <span class="d-none d-sm-inline">Max <?php echo e($filters['jarak_max']); ?> km</span>
                        <span class="d-sm-none">📍<?php echo e($filters['jarak_max']); ?>km</span>
                    </span>
                    <?php endif; ?>

                    <?php if(!empty($filters['jenis_layanan_filter'])): ?>
                    <span class="badge px-2 py-1 rounded-3" style="background: rgba(102, 126, 234, 0.1); color: #667eea; font-size: 0.85rem;">
                        <i class="bi bi-speedometer2 me-1"></i>
                        <span class="d-none d-sm-inline"><?php echo e(count($filters['jenis_layanan_filter'])); ?> layanan</span>
                        <span class="d-sm-none">⚡<?php echo e(count($filters['jenis_layanan_filter'])); ?></span>
                    </span>
                    <?php endif; ?>

                    <?php if(!empty($filters['fasilitas_filter'])): ?>
                    <span class="badge px-2 py-1 rounded-3" style="background: rgba(255, 193, 7, 0.1); color: #ffc107; font-size: 0.85rem;">
                        <i class="bi bi-star me-1"></i>
                        <span class="d-none d-sm-inline"><?php echo e(count($filters['fasilitas_filter'])); ?> fasilitas</span>
                        <span class="d-sm-none">✨<?php echo e(count($filters['fasilitas_filter'])); ?></span>
                    </span>
                    <?php endif; ?>
                </div>

                <!-- Export Buttons -->
                <?php if(isset($filteredCount) && $filteredCount > 0): ?>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="<?php echo e(route('export.laundry.excel', request()->query())); ?>" 
                       class="btn btn-sm" 
                       title="Export ke CSV"
                       style="background: rgba(var(--success-color-rgb), 0.1); border: 1px solid var(--success-color); color: var(--success-color); padding: 0.25rem 0.5rem;">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                        CSV
                    </a>
                    <a href="<?php echo e(route('export.laundry.pdf', request()->query())); ?>" 
                       class="btn btn-sm" 
                       title="Export ke PDF"
                       style="background: rgba(var(--danger-color-rgb), 0.1); border: 1px solid var(--danger-color); color: var(--danger-color); padding: 0.25rem 0.5rem;">
                        <i class="bi bi-file-earmark-pdf me-1"></i>
                        PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Enhanced Main Card -->
    <div class="glass-card border-0 shadow" style="margin-top: 0.5rem;">
        <!-- Header Info with Enhanced Styling -->
        <div class="card-header border-0 py-2" style="border-radius: 0.75rem 0.75rem 0 0; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2 w-100 w-md-auto">
                    <span class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong><?php echo e($laundry->count()); ?></strong> laundry
                    </span>
                    
                    <?php if(auth()->user()->role == 'super_admin' && $laundry->count() > 0): ?>
                    <div id="bulkDeleteBtn" style="display: none;" class="w-100 w-md-auto">
                        <button 
                            type="button" 
                            class="btn btn-danger btn-sm w-100 w-md-auto"
                            onclick="showBulkDeleteModal()"
                        >
                            <i class="bi bi-trash me-1"></i>Hapus (<span id="selectedCount">0</span>)
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                
                <span class="badge bg-success bg-opacity-10 text-success px-2 px-md-3 py-2">
                    <i class="bi bi-database me-1"></i><strong><?php echo e($laundry->count()); ?></strong> Data
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <?php if($laundry->isEmpty()): ?>
                <!-- Enhanced Empty State -->
                <div class="text-center py-4 px-3" style="background: rgba(var(--primary-color-rgb), 0.02);">
                    <div class="mb-3">
                        <i class="bi bi-droplet text-muted opacity-50" style="font-size: 3rem; color: var(--muted-color);"></i>
                    </div>
                    <h5 class="text-muted mb-3" style="color: var(--text-secondary);">
                        <span class="d-none d-md-inline">Tidak ada data laundry ditemukan</span>
                        <span class="d-md-none">Data tidak ditemukan</span>
                    </h5>
                    <p class="text-muted mb-4 small" style="color: var(--text-muted);">
                        <?php if(request()->hasAny(['search', 'harga_min', 'harga_max', 'jarak', 'jenis_layanan'])): ?>
                            <span class="d-none d-md-block">Tidak ada laundry yang sesuai dengan kriteria filter. Coba ubah atau reset filter.</span>
                            <span class="d-md-none">Filter tidak menemukan hasil. Coba ubah filter.</span>
                        <?php else: ?>
                            <span class="d-none d-md-block">Belum ada data laundry. Mulai tambahkan layanan laundry pertama Anda!</span>
                            <span class="d-md-none">Belum ada data. Tambahkan laundry pertama!</span>
                        <?php endif; ?>
                    </p>
                    <div class="d-flex gap-2 justify-content-center flex-column flex-sm-row">
                        <?php if(request()->hasAny(['search', 'harga_min', 'harga_max', 'jarak', 'jenis_layanan'])): ?>
                        <a href="<?php echo e(route('laundry.index')); ?>" class="btn btn-outline-primary btn-sm" style="border-color: var(--primary-color); color: var(--primary-color); border-radius: 0.75rem;">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            <span class="d-none d-sm-inline">Reset Filter</span>
                            <span class="d-sm-none">Reset</span>
                        </a>
                        <?php else: ?>
                        <a href="<?php echo e(route('laundry.create')); ?>" class="btn btn-sm" style="background: var(--success-color); border-color: var(--success-color); color: white; border-radius: 0.75rem;">
                            <i class="bi bi-plus-circle me-2"></i>
                            <span class="d-none d-sm-inline">Tambah Laundry</span>
                            <span class="d-sm-none">Tambah</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
            <!-- Enhanced Mobile Card View -->
                <div class="d-md-none">
                    <?php $__currentLoopData = $laundry; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $filteredLayanan = $item->layanan;
                        if (!empty($filters['jenis_layanan'])) {
                            $filteredLayanan = $item->layanan->where('jenis_layanan', $filters['jenis_layanan']);
                        }
                    ?>
                    <div class="glass-card mb-2 mx-2 border-0 shadow-sm" style="border-radius: 1rem; transition: all 0.3s ease; background: rgba(255,255,255,0.9); backdrop-filter: blur(10px);" 
                         onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.15)'" 
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)'">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-start gap-2 flex-grow-1">
                                    <?php if(auth()->user()->role == 'super_admin'): ?>
                                    <input 
                                        type="checkbox" 
                                        class="form-check-input mt-1 row-checkbox" 
                                        value="<?php echo e($item->id); ?>"
                                        onchange="updateBulkDeleteButton()"
                                        style="accent-color: var(--primary-color);"
                                    >
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 fw-bold" style="color: var(--text-primary); font-size: 1.1rem;">
                                            <i class="bi bi-droplet-fill me-2" style="color: var(--primary-color);"></i>
                                            <?php echo e($item->nama); ?>

                                        </h6>
                                        <p class="text-muted mb-2 small" style="line-height: 1.4;">
                                            <i class="bi bi-geo-alt me-1" style="color: var(--danger-color);"></i><?php echo e(Str::limit($item->alamat, 45)); ?>

                                        </p>
                                    </div>
                                </div>
                                <span class="badge rounded-pill px-2 py-1" style="background: rgba(var(--primary-color-rgb), 0.1); color: var(--primary-color); font-size: 0.75rem;">
                                    #<?php echo e($index + 1); ?>

                                </span>
                            </div>
                            
                            <!-- Enhanced Layanan Info dengan Label -->
                            <?php if($filteredLayanan && $filteredLayanan->isNotEmpty()): ?>
                            <div class="mb-3">
                                <div class="row g-2">
                                    <?php $__currentLoopData = $filteredLayanan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $layanan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <!-- Harga -->
                                    <div class="col-6">
                                        <div class="text-center p-2 rounded-3" style="background: rgba(var(--success-color-rgb), 0.05); border: 1px solid rgba(var(--success-color-rgb), 0.2);">
                                            <small class="text-muted d-block mb-1" style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase;">Harga</small>
                                            <div class="fw-bold" style="color: var(--success-color); font-size: 0.9rem;">
                                                Rp <?php echo e(number_format($layanan->harga, 0, ',', '.')); ?>

                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Jenis Layanan -->
                                    <div class="col-6">
                                        <div class="text-center p-2 rounded-3" style="background: rgba(var(--primary-color-rgb), 0.05); border: 1px solid rgba(var(--primary-color-rgb), 0.2);">
                                            <small class="text-muted d-block mb-1" style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase;">Jenis Layanan</small>
                                            <div class="fw-bold" style="color: var(--primary-color); font-size: 0.9rem;">
                                                <?php echo e(ucfirst($layanan->jenis_layanan)); ?>

                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Estimasi Selesai -->
                                    <div class="col-12 mt-2">
                                        <div class="text-center p-2 rounded-3" style="background: rgba(var(--warning-color-rgb), 0.05); border: 1px solid rgba(var(--warning-color-rgb), 0.2);">
                                            <small class="text-muted d-block mb-1" style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase;">Estimasi Selesai</small>
                                            <div class="fw-bold" style="color: var(--warning-color); font-size: 0.9rem;">
                                                <i class="bi bi-clock me-1"></i><?php echo e($layanan->estimasi_selesai); ?> Jam
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Enhanced Distance Info dengan Label -->
                            <div class="mb-3">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <div class="text-center p-2 rounded-3" style="background: rgba(var(--info-color-rgb), 0.05); border: 1px solid rgba(var(--info-color-rgb), 0.2);">
                                            <small class="text-muted d-block mb-1" style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase;">Jarak dari Kampus</small>
                                            <div class="fw-bold" style="color: var(--info-color); font-size: 0.9rem;">
                                                <i class="bi bi-pin-map me-1"></i><?php echo e(number_format($item->jarak / 1000, 1)); ?> km
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Enhanced Action Buttons -->
                            <div class="d-flex gap-1">
                                <?php if($item->latitude && $item->longitude): ?>
                                <a 
                                    href="https://www.google.com/maps?q=<?php echo e($item->latitude); ?>,<?php echo e($item->longitude); ?>" 
                                    target="_blank"
                                    class="btn btn-sm flex-fill rounded-3"
                                    title="Lihat Lokasi"
                                    style="background: rgba(var(--success-color-rgb), 0.1); border: 1px solid var(--success-color); color: var(--success-color);"
                                >
                                    <i class="bi bi-geo-alt-fill"></i>
                                </a>
                                <?php endif; ?>
                                <a 
                                    href="<?php echo e(route('laundry.show', $item->id)); ?>" 
                                    class="btn btn-sm flex-fill rounded-3"
                                    title="Detail Laundry"
                                    style="background: rgba(var(--info-color-rgb), 0.1); border: 1px solid var(--info-color); color: var(--info-color);"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a 
                                    href="<?php echo e(route('laundry.edit', $item->id)); ?>" 
                                    class="btn btn-sm flex-fill rounded-3"
                                    title="Edit Data"
                                    style="background: rgba(var(--warning-color-rgb), 0.1); border: 1px solid var(--warning-color); color: var(--warning-color);"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                <?php if(auth()->user()->role == 'super_admin'): ?>
                                <button 
                                    type="button" 
                                    class="btn btn-sm flex-fill rounded-3"
                                    title="Hapus Data"
                                    style="background: rgba(var(--danger-color-rgb), 0.1); border: 1px solid var(--danger-color); color: var(--danger-color);"
                                    onclick="showDeleteModal('<?php echo e($item->id); ?>', '<?php echo e($item->nama); ?>', '<?php echo e(Str::limit($item->alamat, 50)); ?>', '<?php echo e($item->layanan->first() ? 'Rp ' . number_format($item->layanan->first()->harga, 0, ',', '.') . '/kg' : '-'); ?>')"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                                <form 
                                    id="delete-form-<?php echo e($item->id); ?>"
                                    action="<?php echo e(route('laundry.destroy', $item->id)); ?>" 
                                    method="POST" 
                                    class="d-none"
                                >
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Desktop Table View -->
                <div class="d-none d-md-block table-responsive" style="border-radius: 0 0 1rem 1rem; overflow: hidden;">
                    <table class="table enhanced-table align-middle mb-0">
                        <thead>
                            <tr>
                                <?php if(auth()->user()->role == 'super_admin'): ?>
                                <th class="border-0 py-3" style="width: 50px;">
                                    <div class="form-check d-flex align-items-center justify-content-center">
                                        <input 
                                            type="checkbox" 
                                            id="selectAll" 
                                            class="form-check-input select-all-checkbox"
                                            title="Pilih semua data di halaman ini"
                                            style="width: 20px; height: 20px; cursor: pointer; border: 2px solid rgba(255,255,255,0.8);"
                                        >
                                    </div>
                                </th>
                                <?php endif; ?>
                                
                                <th class="border-0 py-3" style="width: 60px;">
                                    <small class="text-uppercase text-muted fw-bold">No</small>
                                </th>
                                <th class="border-0 py-3">
                                    <small class="text-uppercase text-muted fw-bold"><i class="bi bi-droplet-fill me-1"></i>Nama Laundry</small>
                                </th>
                                <th class="border-0 py-3">
                                    <small class="text-uppercase text-muted fw-bold"><i class="bi bi-geo-alt me-1"></i>Alamat</small>
                                </th>
                                <th class="border-0 py-3 text-center">
                                    <small class="text-uppercase text-muted fw-bold"><i class="bi bi-currency-dollar me-1"></i>Harga</small>
                                </th>
                                <th class="border-0 py-3 text-center">
                                    <small class="text-uppercase text-muted fw-bold"><i class="bi bi-pin-map me-1"></i>Jarak</small>
                                </th>
                                <th class="border-0 py-3 text-center">
                                    <small class="text-uppercase text-muted fw-bold"><i class="bi bi-droplet me-1"></i>Jenis Layanan</small>
                                </th>
                                <th class="border-0 py-3 text-center">
                                    <small class="text-uppercase text-muted fw-bold"><i class="bi bi-clock me-1"></i>Estimasi</small>
                                </th>
                                <th class="border-0 py-3 text-center" style="width: 180px;">
                                    <small class="text-uppercase text-muted fw-bold">Aksi</small>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $laundry; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $filteredLayanan = $item->layanan;
                                if (!empty($filters['jenis_layanan'])) {
                                    $filteredLayanan = $item->layanan->where('jenis_layanan', $filters['jenis_layanan']);
                                }
                            ?>
                            <tr>
                                <?php if(auth()->user()->role == 'super_admin'): ?>
                                <td class="px-3">
                                    <input 
                                        type="checkbox" 
                                        class="form-check-input row-checkbox" 
                                        value="<?php echo e($item->id); ?>"
                                    >
                                </td>
                                <?php endif; ?>
                                
                                <td class="px-3">
                                    <span class="badge rounded-pill fw-bold" style="background: rgba(var(--primary-color-rgb), 0.1); color: var(--primary-color);"><?php echo e($laundry->firstItem() + $index); ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: linear-gradient(135deg, #818cf8, #667eea); color: white; flex-shrink: 0;">
                                            <i class="bi bi-droplet-fill fs-5"></i>
                                        </div>
                                        <div style="min-width: 0;">
                                            <h6 class="mb-0 fw-bold" style="color: var(--text-primary);"><?php echo e($item->nama); ?></h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted d-block" style="max-width: 250px; line-height: 1.3;">
                                        <i class="bi bi-geo-alt me-1" style="color: var(--danger-color);"></i><?php echo e(Str::limit($item->alamat, 50)); ?>

                                    </small>
                                </td>
                                
                                <!-- KOLOM HARGA -->
                                <td class="text-center">
                                    <?php if($filteredLayanan && $filteredLayanan->isNotEmpty()): ?>
                                        <div class="d-flex flex-column gap-1 align-items-center">
                                            <?php $__currentLoopData = $filteredLayanan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $layanan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge px-3 py-2 rounded-3" style="background: rgba(var(--success-color-rgb), 0.1); color: var(--success-color); font-weight: 600;">
                                                    <i class="bi bi-currency-dollar me-1"></i>Rp <?php echo e(number_format($layanan->harga, 0, ',', '.')); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php else: ?>
                                        <small class="text-muted">-</small>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- KOLOM JARAK -->
                                <td class="text-center">
                                    <span class="badge px-3 py-2 rounded-3" style="background: rgba(var(--info-color-rgb), 0.1); color: var(--info-color); font-weight: 600;">
                                        <i class="bi bi-pin-map me-1"></i><?php echo e(number_format($item->jarak / 1000, 1)); ?> km
                                    </span>
                                </td>
                                
                                <!-- KOLOM JENIS LAYANAN -->
                                <td class="text-center">
                                    <?php if($filteredLayanan && $filteredLayanan->isNotEmpty()): ?>
                                        <div class="d-flex flex-column gap-1 align-items-center">
                                            <?php $__currentLoopData = $filteredLayanan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $layanan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge px-2 py-1 rounded-pill small" style="background: rgba(var(--primary-color-rgb), 0.1); color: var(--primary-color);">
                                                    <?php echo e(ucfirst($layanan->jenis_layanan)); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php else: ?>
                                        <small class="text-muted">-</small>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- KOLOM ESTIMASI -->
                                <td class="text-center">
                                    <?php if($filteredLayanan && $filteredLayanan->isNotEmpty()): ?>
                                        <div class="d-flex flex-column gap-1 align-items-center">
                                            <?php $__currentLoopData = $filteredLayanan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $layanan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge px-3 py-2 rounded-3" style="background: rgba(var(--warning-color-rgb), 0.1); color: var(--warning-color); font-weight: 600;">
                                                    <i class="bi bi-clock me-1"></i><?php echo e($layanan->estimasi_selesai); ?> jam
                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php else: ?>
                                        <small class="text-muted">-</small>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <?php if($item->latitude && $item->longitude): ?>
                                        <a 
                                            href="https://www.google.com/maps?q=<?php echo e($item->latitude); ?>,<?php echo e($item->longitude); ?>" 
                                            target="_blank"
                                            class="btn btn-sm rounded-3"
                                            title="Lihat Lokasi"
                                            style="background: rgba(var(--success-color-rgb), 0.1); border: 1px solid var(--success-color); color: var(--success-color);"
                                        >
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </a>
                                        <?php endif; ?>
                                        <a 
                                            href="<?php echo e(route('laundry.show', $item->id)); ?>" 
                                            class="btn btn-sm rounded-3"
                                            title="Detail"
                                            style="background: rgba(var(--info-color-rgb), 0.1); border: 1px solid var(--info-color); color: var(--info-color);"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a 
                                            href="<?php echo e(route('laundry.edit', $item->id)); ?>" 
                                            class="btn btn-sm rounded-3"
                                            title="Edit"
                                            style="background: rgba(var(--warning-color-rgb), 0.1); border: 1px solid var(--warning-color); color: var(--warning-color);"
                                        >
                                            <i class="bi btn-pencil"></i>
                                        </a>
                                        
                                        <?php if(auth()->user()->role == 'super_admin'): ?>
                                        <button 
                                            type="button" 
                                            class="btn btn-sm rounded-3"
                                            onclick="showDeleteModal('<?php echo e($item->id); ?>', '<?php echo e($item->nama); ?>', '<?php echo e(Str::limit($item->alamat, 50)); ?>', '<?php echo e($item->layanan->first() ? 'Rp ' . number_format($item->layanan->first()->harga, 0, ',', '.') . '/kg' : '-'); ?>')"
                                            title="Hapus"
                                            style="background: rgba(var(--danger-color-rgb), 0.1); border: 1px solid var(--danger-color); color: var(--danger-color);"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <form 
                                            id="delete-form-<?php echo e($item->id); ?>"
                                            action="<?php echo e(route('laundry.destroy', $item->id)); ?>" 
                                            method="POST" 
                                            class="d-none"
                                        >
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if($laundry->hasPages()): ?>
        <div class="card-footer border-0 py-4" style="background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(248,249,255,0.95)); backdrop-filter: blur(10px); border-radius: 0 0 1.25rem 1.25rem;">
            <?php echo e($laundry->links('vendor.pagination.custom')); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<!-- ========== MODAL KONFIRMASI HAPUS SINGLE ========== -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="border: none; padding: 15px 15px 5px 15px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="text-align: center; padding: 0 20px 20px 20px;">
                <div style="margin-bottom: 15px;">
                    <div style="display: inline-flex; align-items: center; justify-content: center; width: 60px; height: 60px; background-color: rgba(220, 53, 69, 0.1); border-radius: 50%;">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size: 2rem; color: #dc3545;"></i>
                    </div>
                </div>
                <h5 style="font-weight: 700; margin-bottom: 8px; color: #212529; font-size: 1.1rem;">Konfirmasi Hapus Data</h5>
                <p style="color: #6c757d; margin-bottom: 20px; font-size: 0.9rem;">Tindakan ini tidak dapat dibatalkan!</p>
                <div style="background-color: #f8f9fa; border-radius: 10px; padding: 15px; margin-bottom: 20px; text-align: left;">
                    <p style="margin-bottom: 12px; font-size: 13px; color: #6c757d;">Apakah Anda yakin ingin menghapus laundry:</p>
                    <div style="display: flex; align-items: flex-start; margin-bottom: 10px;">
                        <i class="bi bi-basket3" style="color: #198754; margin-right: 8px; margin-top: 2px; font-size: 14px;"></i>
                        <strong id="deleteNama" style="display: block; color: #212529; font-size: 14px; word-break: break-word;"></strong>
                    </div>
                    <div style="display: flex; align-items: flex-start; margin-bottom: 10px;">
                        <i class="bi bi-geo-alt" style="color: #dc3545; margin-right: 8px; margin-top: 2px; font-size: 14px;"></i>
                        <small id="deleteAlamat" style="color: #6c757d; font-size: 12px; word-break: break-word;"></small>
                    </div>
                    <div style="display: flex; align-items: flex-start;">
                        <i class="bi bi-currency-dollar" style="color: #198754; margin-right: 8px; margin-top: 2px; font-size: 14px;"></i>
                        <small id="deleteHarga" style="color: #198754; font-weight: 600; font-size: 12px;"></small>
                    </div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal" style="flex: 1; padding: 10px; font-weight: 500; border: 1px solid #dee2e6;">
                        <i class="bi bi-x-circle" style="margin-right: 6px;"></i>Batal
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn" style="flex: 1; padding: 10px; font-weight: 500;">
                        <i class="bi bi-trash" style="margin-right: 6px;"></i>Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== MODAL KONFIRMASI HAPUS BULK ========== -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="border: none; padding: 15px 15px 5px 15px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="text-align: center; padding: 0 20px 20px 20px;">
                <div style="margin-bottom: 15px;">
                    <div style="display: inline-flex; align-items: center; justify-content: center; width: 60px; height: 60px; background-color: rgba(220, 53, 69, 0.1); border-radius: 50%;">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size: 2rem; color: #dc3545;"></i>
                    </div>
                </div>
                <h5 style="font-weight: 700; margin-bottom: 8px; color: #212529; font-size: 1.1rem;">Konfirmasi Hapus Data Terpilih</h5>
                <p style="color: #6c757d; margin-bottom: 20px; font-size: 0.9rem;">Tindakan ini tidak dapat dibatalkan!</p>
                <div style="background-color: #f8f9fa; border-radius: 10px; padding: 15px; margin-bottom: 20px; text-align: center;">
                    <p style="margin-bottom: 10px; font-size: 13px; color: #6c757d;">Anda akan menghapus:</p>
                    <div style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: white; padding: 12px 20px; border-radius: 10px; border: 2px solid #dc3545;">
                        <i class="bi bi-trash" style="font-size: 20px; color: #dc3545;"></i>
                        <span style="font-size: 24px; font-weight: 700; color: #dc3545;" id="bulkDeleteCount">0</span>
                        <span style="font-size: 14px; color: #6c757d;">Laundry</span>
                    </div>
                </div>
                <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 10px 12px; border-radius: 5px; margin-bottom: 15px; text-align: left;">
                    <small style="color: #856404; font-size: 11px;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <strong>Perhatian:</strong> Semua data laundry yang dipilih beserta layanannya akan dihapus permanen!
                    </small>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal" style="flex: 1; padding: 10px; font-weight: 500; border: 1px solid #dee2e6;">
                        <i class="bi bi-x-circle" style="margin-right: 6px;"></i>Batal
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" id="confirmBulkDeleteBtn" style="flex: 1; padding: 10px; font-weight: 500;">
                        <i class="bi bi-trash" style="margin-right: 6px;"></i>Ya, Hapus Semua
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="bulkDeleteForm" action="<?php echo e(route('laundry.bulk-destroy')); ?>" method="POST" class="d-none">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="ids" id="bulkDeleteIds">
</form>

<script>
let deleteItemId = null;
let selectedIds = [];

function showDeleteModal(id, nama, alamat, harga) {
    deleteItemId = id;
    document.getElementById('deleteNama').textContent = nama;
    document.getElementById('deleteAlamat').textContent = alamat;
    document.getElementById('deleteHarga').textContent = harga;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deleteItemId) {
        document.getElementById('delete-form-' + deleteItemId).submit();
    }
});

function showBulkDeleteModal() {
    const checkboxes = document.querySelectorAll('.row-checkbox:checked');
    selectedIds = Array.from(checkboxes).map(cb => cb.value);
    if (selectedIds.length === 0) {
        alert('Pilih minimal 1 laundry untuk dihapus!');
        return;
    }
    document.getElementById('bulkDeleteCount').textContent = selectedIds.length;
    const bulkDeleteModal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));
    bulkDeleteModal.show();
}

document.getElementById('confirmBulkDeleteBtn').addEventListener('click', function() {
    if (selectedIds.length > 0) {
        document.getElementById('bulkDeleteIds').value = JSON.stringify(selectedIds);
        document.getElementById('bulkDeleteForm').submit();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCountSpan = document.getElementById('selectedCount');
    
    function updateBulkDeleteButton() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        const count = checkedBoxes.length;
        if (count > 0 && bulkDeleteBtn) {
            bulkDeleteBtn.style.display = 'block';
            if (selectedCountSpan) selectedCountSpan.textContent = count;
        } else if (bulkDeleteBtn) {
            bulkDeleteBtn.style.display = 'none';
        }
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = count === rowCheckboxes.length && count > 0;
            selectAllCheckbox.indeterminate = count > 0 && count < rowCheckboxes.length;
        }
    }
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkDeleteButton();
        });
    }
    
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkDeleteButton();
        });
    });
    
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    updateBulkDeleteButton();
});

// Expose function to global scope for mobile
window.updateBulkDeleteButton = function() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const count = checkedBoxes.length;
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCountSpan = document.getElementById('selectedCount');
    
    if (count > 0 && bulkDeleteBtn) {
        bulkDeleteBtn.style.display = 'block';
        if (selectedCountSpan) selectedCountSpan.textContent = count;
    } else if (bulkDeleteBtn) {
        bulkDeleteBtn.style.display = 'none';
    }
};
</script>

<style>
.table tbody tr {
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.002);
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

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

.form-label {
    margin-bottom: 0.5rem;
}

.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.6);
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover:not(:disabled) {
    transform: translateY(-2px);
}

.form-check-input {
    width: 18px;
    height: 18px;
    cursor: pointer;
    border: 2px solid #dee2e6;
}

.form-check-input:checked {
    background-color: #dc3545;
    border-color: #dc3545;
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

#bulkDeleteBtn {
    animation: slideInRight 0.3s ease-out;
}

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

.table tbody tr:has(.row-checkbox:checked) {
    background-color: #fff5f5 !important;
    border-left: 3px solid #dc3545;
}

.avatar-sm {
    flex-shrink: 0;
}

/* Responsive table adjustments */
.table {
    font-size: 0.875rem;
}

.table th,
.table td {
    vertical-align: middle;
    white-space: nowrap;
}

.table td > div.d-flex.flex-column {
    white-space: normal;
}

/* Mobile optimizations */
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
    
    .alert {
        font-size: 0.9rem;
        padding: 0.75rem;
    }
}

/* Enhanced Tablet optimizations */
@media (min-width: 768px) and (max-width: 991px) {
    .table {
        font-size: 0.85rem;
    }
    
    .btn-group .btn {
        padding: 0.3rem 0.5rem;
    }
}

/* Enhanced Print styles */
@media print {
    .btn, .form-check-input, .modal {
        display: none !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
    }
}
</style>

<!-- Enhanced JavaScript for Mobile Responsiveness -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== RANGE SLIDER UPDATES ==========
    
    // Update Harga Min Display
    const hargaMinSlider = document.getElementById('harga_min');
    const hargaMinDisplay = document.getElementById('hargaMinDisplay');
    if (hargaMinSlider && hargaMinDisplay) {
        hargaMinSlider.addEventListener('input', function() {
            const value = parseInt(this.value);
            hargaMinDisplay.textContent = new Intl.NumberFormat('id-ID').format(value);
        });
    }
    
    // Update Harga Max Display
    const hargaMaxSlider = document.getElementById('harga_max');
    const hargaMaxDisplay = document.getElementById('hargaMaxDisplay');
    if (hargaMaxSlider && hargaMaxDisplay) {
        hargaMaxSlider.addEventListener('input', function() {
            const value = parseInt(this.value);
            hargaMaxDisplay.textContent = new Intl.NumberFormat('id-ID').format(value);
        });
    }
    
    // Update Jarak Display
    const jarakSlider = document.getElementById('jarak_max');
    const jarakDisplay = document.getElementById('jarakDisplay');
    if (jarakSlider && jarakDisplay) {
        jarakSlider.addEventListener('input', function() {
            jarakDisplay.textContent = this.value;
        });
    }
    
    // ========== ENHANCED MOBILE FEATURES ==========
    
    // Touch-friendly interactions for mobile
    const cards = document.querySelectorAll('.glass-card');
    cards.forEach(card => {
        card.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        });
        card.addEventListener('touchend', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Enhanced Filter Toggle
    const laundryFilterElement = document.getElementById('laundryFilterCollapse');
    const laundryToggleIcon = document.getElementById('laundryFilterToggleIcon');
    
    if (laundryFilterElement && laundryToggleIcon) {
        laundryFilterElement.addEventListener('show.bs.collapse', function() {
            laundryToggleIcon.style.transform = 'rotate(180deg)';
        });
        
        laundryFilterElement.addEventListener('hide.bs.collapse', function() {
            laundryToggleIcon.style.transform = 'rotate(0deg)';
        });
    }

    // Auto-submit form on mobile for better UX
    const sortSelect = document.querySelector('select[name="sort_by"]');
    if (sortSelect && window.innerWidth <= 768) {
        sortSelect.addEventListener('change', function() {
            // Add loading indicator
            this.style.opacity = '0.7';
            this.disabled = true;
            
            // Auto submit after short delay
            setTimeout(() => {
                this.form.submit();
            }, 300);
        });
    }

    // Enhanced bulk selection for mobile
    function updateBulkDeleteButton() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        const bulkBar = document.getElementById('bulkActionBar');
        const countSpan = document.getElementById('selectedCount');
        
        if (checkboxes.length > 0) {
            bulkBar.style.display = 'block';
            countSpan.textContent = checkboxes.length;
            
            // Add mobile-friendly styling
            if (window.innerWidth <= 768) {
                bulkBar.style.position = 'fixed';
                bulkBar.style.bottom = '20px';
                bulkBar.style.left = '50%';
                bulkBar.style.transform = 'translateX(-50%)';
                bulkBar.style.zIndex = '1050';
                bulkBar.style.width = 'auto';
                bulkBar.style.padding = '10px 20px';
                bulkBar.style.borderRadius = '25px';
                bulkBar.style.boxShadow = '0 5px 15px rgba(0,0,0,0.3)';
            }
        } else {
            bulkBar.style.display = 'none';
            // Reset mobile styles
            if (window.innerWidth <= 768) {
                bulkBar.style.position = '';
                bulkBar.style.bottom = '';
                bulkBar.style.left = '';
                bulkBar.style.transform = '';
                bulkBar.style.zIndex = '';
                bulkBar.style.width = '';
                bulkBar.style.padding = '';
                bulkBar.style.borderRadius = '';
                bulkBar.style.boxShadow = '';
            }
        }
    }
    
    // Make function available globally
    window.updateBulkDeleteButton = updateBulkDeleteButton;

    // Enhanced responsive behavior on window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // Update bulk action positioning
            updateBulkDeleteButton();
            
            // Reset any mobile-specific styles if switching to desktop
            if (window.innerWidth > 768) {
                const bulkBar = document.getElementById('bulkActionBar');
                if (bulkBar) {
                    bulkBar.style.position = '';
                    bulkBar.style.bottom = '';
                    bulkBar.style.left = '';
                    bulkBar.style.transform = '';
                    bulkBar.style.zIndex = '';
                    bulkBar.style.width = '';
                    bulkBar.style.padding = '';
                    bulkBar.style.borderRadius = '';
                    bulkBar.style.boxShadow = '';
                }
            }
        }, 250);
    });

    // Enhanced form validation with mobile feedback
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i><span class="d-none d-sm-inline">Memproses...</span><span class="d-sm-none">...</span>';
                submitBtn.disabled = true;
            }
        });
    }

    // Mobile-optimized tooltips
    if (window.innerWidth <= 768) {
        // Disable tooltips on mobile for better performance
        const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipElements.forEach(element => {
            element.removeAttribute('data-bs-toggle');
            element.removeAttribute('title');
        });
    } else {
        // Initialize tooltips for desktop
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TA\spk_kontrakan\resources\views/laundry/index.blade.php ENDPATH**/ ?>