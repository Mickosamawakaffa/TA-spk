

<?php $__env->startSection('title', 'Hasil Rekomendasi SAW'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-3 px-md-4 py-3 py-md-4">
    <!-- Enhanced Mobile-First Styles -->
    <style>
        .breadcrumb {
            background: transparent;
            border-radius: 10px;
            padding: 0;
            margin-bottom: 1.5rem;
        }
        
        .breadcrumb-item a {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .breadcrumb-item a:hover {
            color: var(--secondary-color);
            transform: translateY(-1px);
        }
        
        .breadcrumb-item.active {
            color: var(--text-secondary);
            font-weight: 700;
        }
        
        .detail-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 25px rgba(var(--primary-color-rgb), 0.25);
            position: relative;
            overflow: hidden;
        }
        
        .detail-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at top right, rgba(255,255,255,0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .detail-header h2 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .detail-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 1.5rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        .detail-card-header {
            color: var(--primary-color);
            font-size: 1rem;
            font-weight: 700;
            padding: 1rem 1.25rem;
            border-bottom: 2px solid rgba(var(--primary-color-rgb), 0.1);
            background: rgba(248, 249, 250, 0.8);
            backdrop-filter: blur(5px);
        }
        
        .btn-back {
            background: rgba(255, 255, 255, 0.95);
            color: var(--primary-color);
            font-weight: 700;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .btn-back:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            background: white;
            color: var(--secondary-color);
            border-color: white;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }
        
        .ranking-card {
            transition: all 0.3s ease;
            border-radius: 1rem;
            overflow: hidden;
        }
        
        .ranking-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .ranking-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .ranking-badge.rank-1 {
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            color: #8b5cf6;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }
        
        .ranking-badge.rank-2 {
            background: linear-gradient(135deg, #c0c0c0, #e5e5e5);
            color: #374151;
            box-shadow: 0 4px 15px rgba(192, 192, 192, 0.3);
        }
        
        .ranking-badge.rank-3 {
            background: linear-gradient(135deg, #cd7f32, #d4943d);
            color: white;
            box-shadow: 0 4px 15px rgba(205, 127, 50, 0.3);
        }
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
            .detail-header {
                padding: 1.25rem;
                margin-bottom: 1.25rem;
                border-radius: 0.75rem;
            }
            
            .detail-header h2 {
                font-size: 1.4rem;
                line-height: 1.3;
            }
            
            .btn-back {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
                border-radius: 25px;
            }
            
            .glass-card {
                margin-left: -15px;
                margin-right: -15px;
                border-radius: 0.75rem;
            }
        }
        
        @media (max-width: 576px) {
            .detail-header h2 {
                font-size: 1.25rem;
            }
        }
    </style>
    
    <!-- Enhanced Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3 mb-md-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('dashboard')); ?>" class="d-flex align-items-center">
                    <i class="bi bi-house-door me-1"></i>
                    <span class="d-none d-sm-inline">Dashboard</span>
                    <span class="d-sm-none">Home</span>
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo e(route('saw.index')); ?>" class="d-flex align-items-center">
                    <i class="bi bi-calculator me-1"></i>
                    <span class="d-none d-sm-inline">Metode SAW</span>
                    <span class="d-sm-none">SAW</span>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-trophy me-1"></i>Hasil
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="detail-header">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h2 class="mb-1">
                    <i class="bi bi-trophy-fill me-2 d-md-none"></i>
                    Hasil SAW <?php echo e(ucfirst($tipe)); ?>

                </h2>
                <p class="mb-0 opacity-90" style="font-size: 1rem; font-weight: 500;">
                    <i class="bi bi-calculator me-1"></i>
                    Simple Additive Weighting Method
                </p>
                <!-- INFO REFERENCE POINT -->
                <?php if($tipe == 'kontrakan'): ?>
                <span class="badge bg-light text-dark" style="color: white !important;">
                    <i class="bi bi-geo-alt-fill me-1"></i>Jarak dari Kampus Polije
                </span>
                <?php else: ?>
                <span class="badge bg-light text-dark" style="color: white !important;">
                    <i class="bi bi-geo-alt-fill me-1"></i>Jarak dari Lokasi Anda
                </span>
                <?php endif; ?>
            </div>
            <a href="<?php echo e(route('saw.index')); ?>" class="btn btn-back">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Enhanced Info Alert -->
    <div class="alert border-0 mb-4 shadow-sm" style="background: linear-gradient(135deg, rgba(var(--info-color-rgb), 0.1) 0%, rgba(var(--primary-color-rgb), 0.1) 100%); border-left: 4px solid var(--info-color); border-radius: 1rem;">
        <div class="d-flex align-items-start">
            <div class="avatar-sm d-flex align-items-center justify-content-center me-3 rounded-circle" style="background: rgba(var(--info-color-rgb), 0.15); color: var(--info-color); min-width: 40px; height: 40px;">
                <i class="bi bi-info-circle-fill" style="font-size: 1.2rem;"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-2" style="color: var(--info-color);">
                    <i class="bi bi-geo-alt me-1"></i>
                    Informasi Perhitungan Jarak
                </h6>
                <p class="mb-2" style="color: var(--text-secondary); line-height: 1.6;">
                    <?php if($tipe == 'kontrakan'): ?>
                        Jarak <strong>kontrakan</strong> dihitung secara otomatis dari <strong style="color: var(--primary-color);">Kampus Politeknik Negeri Jember</strong> menggunakan koordinat GPS.
                    <?php else: ?>
                        Jarak <strong>laundry</strong> dihitung secara otomatis dari <strong style="color: var(--primary-color);">lokasi Anda saat ini</strong> menggunakan koordinat GPS.
                    <?php endif; ?>
                </p>
                <small style="color: var(--text-muted);">
                    <i class="bi bi-lightbulb me-1"></i>
                    <strong>Tips:</strong> Sistem menampilkan <?php echo e($tipe); ?> dengan ranking berdasarkan nilai SAW tertinggi.
                </small>
            </div>
        </div>
    </div>

    <!-- Enhanced Peta Lokasi Card -->
    <div class="glass-card mb-4">
        <div class="card-header border-0 py-3 py-md-4" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                <div>
                    <h6 class="mb-1 fw-bold" style="color: var(--text-primary);">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm d-flex align-items-center justify-content-center me-2 rounded-circle" style="background: rgba(var(--primary-color-rgb), 0.15); color: var(--primary-color);">
                                <i class="bi bi-map-fill"></i>
                            </div>
                            <span class="d-none d-sm-inline">Peta Lokasi Rekomendasi</span>
                            <span class="d-sm-none">Peta Lokasi</span>
                        </div>
                    </h6>
                    <small style="color: var(--text-muted);">Klik marker untuk detail informasi</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm rounded-pill" onclick="resetMapView()" style="background: rgba(var(--primary-color-rgb), 0.1); color: var(--primary-color); border: 1px solid rgba(var(--primary-color-rgb), 0.3); font-weight: 600;">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        <span class="d-none d-sm-inline">Reset View</span>
                        <span class="d-sm-none">Reset</span>
                    </button>
                    <button class="btn btn-sm rounded-pill d-none d-md-block" onclick="toggleMapFullscreen()" style="background: rgba(var(--success-color-rgb), 0.1); color: var(--success-color); border: 1px solid rgba(var(--success-color-rgb), 0.3); font-weight: 600;">
                        <i class="bi bi-arrows-fullscreen me-1"></i>Fullscreen
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="map" class="rounded-bottom" style="height: 350px; width: 100%; position: relative;"></div>
        </div>
        <div class="card-footer border-0 py-3 py-md-4" style="background: rgba(248, 249, 250, 0.8); backdrop-filter: blur(5px);">
            <div class="row g-3">
                <!-- Enhanced Legend -->
                <div class="col-12 col-lg-8">
                    <h6 class="fw-bold mb-3 fs-6">
                        <i class="bi bi-palette text-primary me-2"></i>Legenda Peta
                    </h6>
                    <div class="d-flex flex-wrap gap-3 small">
                        <div class="d-flex align-items-center px-3 py-2 rounded-pill" style="background: rgba(255, 193, 7, 0.15); border: 1px solid rgba(255, 193, 7, 0.3);">
                            <div class="rounded-circle me-2" style="width: 16px; height: 16px; background: linear-gradient(135deg, #ffd700, #ffed4e); border: 2px solid #fff; box-shadow: 0 2px 6px rgba(255, 215, 0, 0.3);"></div>
                            <span class="fw-bold" style="color: #8b5cf6;">🏆 Rank 1</span>
                        </div>
                        <div class="d-flex align-items-center px-3 py-2 rounded-pill" style="background: rgba(108, 117, 125, 0.15); border: 1px solid rgba(108, 117, 125, 0.3);">
                            <div class="rounded-circle me-2" style="width: 16px; height: 16px; background: linear-gradient(135deg, #c0c0c0, #e5e5e5); border: 2px solid #fff; box-shadow: 0 2px 6px rgba(192, 192, 192, 0.3);"></div>
                            <span class="fw-bold" style="color: #374151;">🥈 Rank 2</span>
                        </div>
                        <div class="d-flex align-items-center px-3 py-2 rounded-pill" style="background: rgba(205, 127, 50, 0.15); border: 1px solid rgba(205, 127, 50, 0.3);">
                            <div class="rounded-circle me-2" style="width: 16px; height: 16px; background: linear-gradient(135deg, #cd7f32, #d4943d); border: 2px solid #fff; box-shadow: 0 2px 6px rgba(205, 127, 50, 0.3);"></div>
                            <span class="fw-bold text-white" style="color: white;">🥉 Rank 3</span>
                        </div>
                        <div class="d-flex align-items-center px-3 py-2 rounded-pill" style="background: rgba(var(--primary-color-rgb), 0.15); border: 1px solid rgba(var(--primary-color-rgb), 0.3);">
                            <div class="rounded-circle me-2" style="width: 16px; height: 16px; background: var(--primary-color); border: 2px solid #fff; box-shadow: 0 2px 6px rgba(var(--primary-color-rgb), 0.3);"></div>
                            <span class="fw-bold" style="color: var(--primary-color);">Lainnya</span>
                        </div>
                        <?php if($tipe == 'kontrakan'): ?>
                        <div class="d-flex align-items-center px-3 py-2 rounded-pill" style="background: rgba(var(--info-color-rgb), 0.15); border: 1px solid rgba(var(--info-color-rgb), 0.3);">
                            <div class="rounded-circle me-2" style="width: 16px; height: 16px; background: var(--info-color); border: 2px solid #fff; box-shadow: 0 2px 6px rgba(var(--info-color-rgb), 0.3);"></div>
                            <span class="fw-bold" style="color: var(--info-color);"><i class="bi bi-building me-1"></i>Polije</span>
                        </div>
        </div>
        <?php else: ?>
        <div class="d-flex align-items-center px-3 py-2 rounded-pill" style="background: rgba(var(--success-color-rgb), 0.15); border: 1px solid rgba(var(--success-color-rgb), 0.3);">
            <div class="rounded-circle me-2" style="width: 16px; height: 16px; background: var(--success-color); border: 2px solid #fff; box-shadow: 0 2px 6px rgba(var(--success-color-rgb), 0.3);"></div>
            <span class="fw-bold" style="color: var(--success-color);"><i class="bi bi-geo me-1"></i>Lokasi Anda</span>
        </div>
        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Enhanced Tips -->
                <div class="col-12 col-lg-4">
                    <div class="p-3 rounded-3" style="background: rgba(var(--info-color-rgb), 0.1); border: 1px solid rgba(var(--info-color-rgb), 0.2);">
                        <h6 class="fw-bold mb-2" style="color: var(--info-color); font-size: 0.9rem;">
                            <i class="bi bi-lightbulb me-1"></i>Tips Navigasi
                        </h6>
                        <div class="d-flex flex-column gap-1">
                            <small style="color: var(--text-muted); font-size: 0.8rem;">
                                <i class="bi bi-cursor me-1" style="color: var(--info-color);"></i>Klik marker untuk detail lengkap
                            </small>
                            <small style="color: var(--text-muted); font-size: 0.8rem;">
                                <i class="bi bi-mouse me-1" style="color: var(--info-color);"></i>Scroll/pinch untuk zoom
                            </small>
                            <small style="color: var(--text-muted); font-size: 0.8rem;">
                                <i class="bi bi-arrows-move me-1" style="color: var(--info-color);"></i>Drag untuk menggeser peta
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Kriteria & Bobot Card -->
    <div class="glass-card mb-4">
        <div class="card-header border-0 py-3 py-md-4" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm d-flex align-items-center justify-content-center me-2 rounded-circle" style="background: rgba(var(--warning-color-rgb), 0.15); color: var(--warning-color);">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                        <span class="d-none d-sm-inline">Kriteria dan Bobot yang Digunakan</span>
                        <span class="d-sm-none">Kriteria & Bobot</span>
                    </h6>
                </div>
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(var(--primary-color-rgb), 0.15); color: var(--primary-color); font-weight: 700;">
                    <?php echo e(count($kriteria)); ?> Kriteria
                </span>
            </div>
        </div>
        <div class="card-body p-3 p-md-4">
            <div class="row g-3">
                <?php $__currentLoopData = $kriteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 col-lg-3">
                    <div class="card border-0 h-100" style="background: rgba(248, 249, 250, 0.8); backdrop-filter: blur(5px); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
                        <div class="card-body text-center p-3">
                            <div class="mb-3">
                                <div class="avatar-sm d-flex align-items-center justify-content-center mx-auto mb-2 rounded-circle" style="background: rgba(var(--primary-color-rgb), 0.1); color: var(--primary-color);">
                                    <i class="bi bi-star"></i>
                                </div>
                                <h6 class="fw-bold mb-0 small" style="color: var(--text-primary); line-height: 1.3;"><?php echo e($k->nama_kriteria); ?></h6>
                            </div>
                            
                            <div class="mb-3">
                                <span class="badge px-3 py-2 rounded-3" style="background: rgba(var(--info-color-rgb), 0.15); color: var(--info-color); font-weight: 700; font-size: 0.9rem;"><?php echo e($k->bobot); ?></span>
                                <small class="d-block mt-1 text-muted">(<?php echo e(number_format($k->bobot * 100, 1)); ?>%)</small>
                            </div>
                            
                            <?php if(strtolower($k->tipe) == 'benefit'): ?>
                                <span class="badge px-2 py-1 rounded-3" style="background: rgba(var(--success-color-rgb), 0.15); color: var(--success-color); font-size: 0.75rem; font-weight: 600;">
                                    <i class="bi bi-arrow-up-circle me-1"></i>Benefit
                                </span>
                            <?php else: ?>
                                <span class="badge px-2 py-1 rounded-3" style="background: rgba(var(--danger-color-rgb), 0.15); color: var(--danger-color); font-size: 0.75rem; font-weight: 600;">
                                    <i class="bi bi-arrow-down-circle me-1"></i>Cost
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            
            <div class="text-center mt-4">
                <div class="d-inline-flex align-items-center px-4 py-3 rounded-pill" style="background: rgba(var(--primary-color-rgb), 0.1); border: 2px solid rgba(var(--primary-color-rgb), 0.2);">
                    <i class="bi bi-calculator me-2" style="color: var(--primary-color); font-size: 1.2rem;"></i>
                    <span class="fw-bold" style="color: var(--primary-color);">Total Bobot: <?php echo e($kriteria->sum('bobot')); ?></span>
                    <span class="ms-2 badge rounded-pill" style="background: var(--primary-color); color: white; font-size: 0.7rem;"><?php echo e($kriteria->sum('bobot') == 1 ? 'VALID' : 'CHECK'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Visualisasi Chart -->
    <div class="row g-3 g-md-4 mb-4">
        <!-- Enhanced Bar Chart -->
        <div class="col-12 col-lg-8">
            <div class="glass-card">
                <div class="card-header border-0 py-3 py-md-4" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm d-flex align-items-center justify-content-center me-2 rounded-circle" style="background: rgba(var(--primary-color-rgb), 0.15); color: var(--primary-color);">
                                <i class="bi bi-bar-chart-fill"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold" style="color: var(--text-primary);">
                                    <span class="d-none d-sm-inline">Perbandingan Nilai SAW</span>
                                    <span class="d-sm-none">Chart SAW</span>
                                </h6>
                                <small style="color: var(--text-muted);">Ranking berdasarkan nilai tertinggi</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge rounded-pill px-3 py-2" style="background: rgba(var(--primary-color-rgb), 0.15); color: var(--primary-color); font-weight: 600;">
                                <i class="bi bi-graph-up me-1"></i>Live Chart
                            </span>
                            <button class="btn btn-sm rounded-pill d-none d-md-block" onclick="downloadChart()" style="background: rgba(var(--success-color-rgb), 0.1); color: var(--success-color); border: 1px solid rgba(var(--success-color-rgb), 0.3); font-weight: 600;">
                                <i class="bi bi-download me-1"></i>Download
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="chart-container" style="position: relative; height: 350px;">
                        <canvas id="barChartSAW"></canvas>
                    </div>
                </div>
                <div class="card-footer border-0 py-3" style="background: rgba(248, 249, 250, 0.8); backdrop-filter: blur(5px);">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle me-2" style="color: var(--info-color);"></i>
                        <small style="color: var(--text-muted); line-height: 1.5;">
                            Grafik menunjukkan perbandingan nilai SAW. <strong style="color: var(--primary-color);">Semakin tinggi nilai, semakin direkomendasikan</strong> berdasarkan kriteria yang dipilih.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Radar Chart -->
        <div class="col-12 col-lg-4">
            <div class="glass-card">
                <div class="card-header border-0 py-3 py-md-4" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm d-flex align-items-center justify-content-center me-2 rounded-circle" style="background: rgba(var(--success-color-rgb), 0.15); color: var(--success-color);">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold" style="color: var(--text-primary);">
                                <span class="d-none d-sm-inline">Analisis Top 3</span>
                                <span class="d-sm-none">Top 3</span>
                            </h6>
                            <small style="color: var(--text-muted);">Perbandingan multi-kriteria</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="radarChartTop3"></canvas>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-2 py-md-3">
                    <small class="text-muted" style="font-size: 0.8rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        Perbandingan multi-kriteria
                    </small>
                </div>
            </div>
        </div>
    </div>
    <!-- Progress Bars per Kriteria -->
    <div class="card border-0 shadow-sm mb-3 mb-md-4">
        <div class="card-header bg-white border-0 py-2 py-md-3">
            <h6 class="fw-bold mb-0 fs-6">
                <i class="bi bi-speedometer2 text-warning me-2"></i>Detail Nilai per Kriteria (Top 5)
            </h6>
        </div>
        <div class="card-body p-2 p-md-3">
            <?php $__currentLoopData = $kriteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="mb-3 mb-md-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-2 gap-2">
                    <h6 class="fw-semibold mb-0 small">
                        <?php echo e($k->nama_kriteria); ?>

                        <span class="badge bg-<?php echo e(strtolower($k->tipe) == 'benefit' ? 'success' : 'danger'); ?> ms-2">
                            <?php echo e(strtolower($k->tipe) == 'benefit' ? '↑ Benefit' : '↓ Cost'); ?>

                        </span>
                    </h6>
                    <span class="badge bg-info small">Bobot: <?php echo e($k->bobot); ?></span>
                </div>
                
                <?php $__currentLoopData = array_slice($hasil, 0, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $nilaiKriteria = $item['normalisasi'][$k->nama_kriteria] ?? null;
                        $percentage = $nilaiKriteria ? ($nilaiKriteria['normalisasi'] * 100) : 0;
                        $barColor = $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : ($index == 2 ? 'danger' : 'primary'));
                    ?>
                    
                    <?php if($nilaiKriteria): ?>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="fw-semibold" style="font-size: 0.85rem;">
                                <?php if($index == 0): ?>
                                    🏆
                                <?php elseif($index == 1): ?>
                                    🥈
                                <?php elseif($index == 2): ?>
                                    🥉
                                <?php else: ?>
                                    <?php echo e($index + 1); ?>.
                                <?php endif; ?>
                                <?php echo e(Str::limit($item['nama'], 30)); ?>

                            </small>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                <strong><?php echo e($nilaiKriteria['asli']); ?></strong>
                                <span class="text-primary ms-1">(<?php echo e(number_format($nilaiKriteria['normalisasi'], 4)); ?>)</span>
                            </small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div 
                                class="progress-bar bg-<?php echo e($barColor); ?>" 
                                role="progressbar" 
                                style="width: <?php echo e($percentage); ?>%;" 
                                aria-valuenow="<?php echo e($percentage); ?>" 
                                aria-valuemin="0" 
                                aria-valuemax="100"
                            ></div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                <?php if($loop->iteration < count($kriteria)): ?>
                <hr class="my-2 my-md-3">
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- 🆕 Export Results Buttons -->
    <div class="row g-2 mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                    <i class="bi bi-check-circle me-1"></i>
                    Analisis SAW Selesai - <?php echo e(count($hasil)); ?> hasil
                </span>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-success" id="btnExcelSAW" title="Export ke CSV">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnPdfSAW" title="Export ke PDF">
                        <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms untuk Export -->
    <form id="formExcelSAW" method="POST" action="<?php echo e(route('export.saw.excel')); ?>" style="display: none;">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="hasil_json">
        <input type="hidden" name="tipe" value="<?php echo e($tipe); ?>">
        <input type="hidden" name="jenis_layanan" value="<?php echo e($jenisLayanan ?? ''); ?>">
    </form>

    <form id="formPdfSAW" method="POST" action="<?php echo e(route('export.saw.pdf')); ?>" style="display: none;">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="hasil_json">
        <input type="hidden" name="tipe" value="<?php echo e($tipe); ?>">
        <input type="hidden" name="jenis_layanan" value="<?php echo e($jenisLayanan ?? ''); ?>">
    </form>

    <!-- Enhanced Mobile Card View -->
    <div class="d-lg-none mb-4">
        <div class="glass-card">
            <div class="card-header border-0 py-3" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm d-flex align-items-center justify-content-center me-2 rounded-circle" style="background: rgba(var(--warning-color-rgb), 0.15); color: var(--warning-color);">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                            <span class="d-none d-sm-inline">Peringkat Rekomendasi</span>
                            <span class="d-sm-none">Ranking</span>
                        </h6>
                    </div>
                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(var(--primary-color-rgb), 0.15); color: var(--primary-color); font-weight: 700;">
                        <?php echo e(count($hasil)); ?> <?php echo e(ucfirst($tipe)); ?>

                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <?php $__currentLoopData = $hasil; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="ranking-card p-3 border-bottom <?php echo e($index == 0 ? 'border-start border-5 border-warning' : ''); ?>" 
                     id="mobile-row-<?php echo e($index); ?>"
                     style="<?php echo e($index == 0 ? 'background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 215, 0, 0.1) 100%);' : ''); ?> transition: all 0.3s ease;"
                     ontouchstart="this.style.transform='scale(0.98)'"
                     ontouchend="this.style.transform='scale(1)'">
                    
                    <!-- Header Row -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <!-- Ranking Badge -->
                            <?php if($index == 0): ?>
                                <div class="ranking-badge rank-1 mb-2">
                                    <i class="bi bi-trophy-fill"></i>
                                    <span>#1 - Terbaik</span>
                                </div>
                            <?php elseif($index == 1): ?>
                                <div class="ranking-badge rank-2 mb-2">
                                    <i class="bi bi-award-fill"></i>
                                    <span>#2 - Baik</span>
                                </div>
                            <?php elseif($index == 2): ?>
                                <div class="ranking-badge rank-3 mb-2">
                                    <i class="bi bi-award-fill"></i>
                                    <span>#3 - Cukup</span>
                                </div>
                            <?php else: ?>
                                <div class="badge rounded-pill px-3 py-2 mb-2" style="background: rgba(var(--primary-color-rgb), 0.15); color: var(--primary-color); font-weight: 700;">
                                    <i class="bi bi-hash me-1"></i><?php echo e($item['ranking']); ?>

                                </div>
                            <?php endif; ?>
                            
                            <!-- Name & Location -->
                            <h6 class="fw-bold mb-1" style="color: var(--text-primary); line-height: 1.3;"><?php echo e($item['nama']); ?></h6>
                            <p class="small mb-0" style="color: var(--text-muted); line-height: 1.4;">
                                <i class="bi bi-geo-alt-fill me-1" style="color: var(--danger-color);"></i>
                                <?php echo e(Str::limit($item['alamat'], 60)); ?>

                            </p>
                        </div>
                        
                        <!-- SAW Score -->
                        <div class="text-end">
                            <div class="badge px-3 py-2 rounded-3" style="background: rgba(var(--<?php echo e($index == 0 ? 'success' : ($index == 1 ? 'info' : ($index == 2 ? 'warning' : 'primary'))); ?>-color-rgb), 0.15); color: var(--<?php echo e($index == 0 ? 'success' : ($index == 1 ? 'info' : ($index == 2 ? 'warning' : 'primary'))); ?>-color); font-weight: 700; font-size: 1rem;">
                                <?php echo e(number_format($item['nilai'], 4)); ?>

                            </div>
                            <small class="d-block mt-1" style="color: var(--text-muted);">Skor SAW</small>
                        </div>
                    </div>
                    
                    <!-- Achievement Badge -->
                    <?php if($index == 0): ?>
                        <div class="alert border-0 mb-3 py-2" style="background: rgba(var(--success-color-rgb), 0.1); border-left: 4px solid var(--success-color) !important;">
                            <small class="fw-bold" style="color: var(--success-color);">✨ Rekomendasi Terbaik Berdasarkan Kriteria Anda</small>
                        </div>
                    <?php elseif($index == 1): ?>
                        <div class="alert border-0 mb-3 py-2" style="background: rgba(var(--info-color-rgb), 0.1); border-left: 4px solid var(--info-color) !important;">
                            <small class="fw-bold" style="color: var(--info-color);">🥈 Pilihan Alternatif Terbaik Kedua</small>
                        </div>
                    <?php elseif($index == 2): ?>
                        <div class="alert border-0 mb-3 py-2" style="background: rgba(var(--warning-color-rgb), 0.1); border-left: 4px solid var(--warning-color) !important;">
                            <small class="fw-bold" style="color: var(--warning-color);">🥉 Pilihan Ketiga yang Layak Dipertimbangkan</small>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-sm rounded-pill flex-fill" 
                                onclick="focusOnMarker(<?php echo e($index); ?>)"
                                style="background: rgba(var(--primary-color-rgb), 0.1); color: var(--primary-color); border: 1px solid rgba(var(--primary-color-rgb), 0.3); font-weight: 600;">
                            <i class="bi bi-geo-alt-fill me-1"></i>
                            <span class="d-none d-sm-inline">Lihat di Peta</span>
                            <span class="d-sm-none">Peta</span>
                        </button>
                        
                        <?php if(isset($item['latitude']) && isset($item['longitude']) && $item['latitude'] && $item['longitude']): ?>
                        <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo e($item['latitude']); ?>,<?php echo e($item['longitude']); ?>" 
                           target="_blank"
                           class="btn btn-sm rounded-pill flex-fill"
                           style="background: rgba(var(--danger-color-rgb), 0.1); color: var(--danger-color); border: 1px solid rgba(var(--danger-color-rgb), 0.3); font-weight: 600;">
                            <i class="bi bi-google me-1"></i>
                            <span class="d-none d-sm-inline">Google Maps</span>
                            <span class="d-sm-none">Maps</span>
                        </a>
                        <?php endif; ?>
                        
                        <?php if(isset($item['no_whatsapp']) && !empty($item['no_whatsapp'])): ?>
                        <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $item['no_whatsapp'])); ?>" 
                           target="_blank"
                           class="btn btn-sm rounded-pill flex-fill"
                           style="background: rgba(var(--success-color-rgb), 0.1); color: var(--success-color); border: 1px solid rgba(var(--success-color-rgb), 0.3); font-weight: 600;">
                            <i class="bi bi-whatsapp me-1"></i>
                            <span class="d-none d-sm-inline">WhatsApp</span>
                            <span class="d-sm-none">Chat</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- Enhanced Desktop Table View -->
    <div class="glass-card mb-4 d-none d-lg-block">
        <div class="card-header border-0 py-3 py-md-4" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm d-flex align-items-center justify-content-center me-2 rounded-circle" style="background: rgba(var(--warning-color-rgb), 0.15); color: var(--warning-color);">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold" style="color: var(--text-primary);">Peringkat Rekomendasi</h6>
                        <small style="color: var(--text-muted);">Data lengkap perhitungan SAW</small>
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(var(--primary-color-rgb), 0.15); color: var(--primary-color); font-weight: 700;">
                        <?php echo e(count($hasil)); ?> Hasil
                    </span>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" onclick="window.print()" title="Print Hasil">
                        <i class="bi bi-printer me-1"></i>Print
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                <style>
                    .table-responsive::-webkit-scrollbar {
                        width: 6px;
                        height: 6px;
                    }
                    .table-responsive::-webkit-scrollbar-track {
                        background: rgba(0,0,0,0.05);
                        border-radius: 3px;
                    }
                    .table-responsive::-webkit-scrollbar-thumb {
                        background: rgba(var(--primary-color-rgb), 0.3);
                        border-radius: 3px;
                    }
                    .table-responsive::-webkit-scrollbar-thumb:hover {
                        background: rgba(var(--primary-color-rgb), 0.5);
                    }
                    .table tbody tr {
                        cursor: pointer;
                        transition: all 0.2s ease;
                    }
                    .table tbody tr:hover {
                        background: rgba(var(--primary-color-rgb), 0.05) !important;
                        transform: translateY(-1px);
                        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                    }
                    .table tbody tr.table-warning:hover {
                        background: rgba(var(--warning-color-rgb), 0.2) !important;
                        box-shadow: 0 4px 20px rgba(255, 193, 7, 0.3);
                    }
                    .table tbody tr.table-warning {
                        background: rgba(var(--warning-color-rgb), 0.1) !important;
                        border-left: 3px solid var(--warning-color);
                    }
                </style>
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: rgba(248, 249, 250, 0.8); backdrop-filter: blur(5px);">
                        <tr>
                            <th class="border-0 px-4 py-3" style="border-radius: 0;">
                                <small class="text-uppercase fw-bold" style="color: var(--text-muted); letter-spacing: 0.5px;">🏆 Peringkat</small>
                            </th>
                            <th class="border-0 py-3">
                                <small class="text-uppercase fw-bold" style="color: var(--text-muted); letter-spacing: 0.5px;">Nama <?php echo e(ucfirst($tipe)); ?></small>
                            </th>
                            <th class="border-0 py-3">
                                <small class="text-uppercase fw-bold" style="color: var(--text-muted); letter-spacing: 0.5px;">🗺 Alamat</small>
                            </th>
                            <?php $__currentLoopData = $kriteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th class="border-0 py-3 text-center">
                                <small class="text-uppercase fw-bold" style="color: var(--text-muted); letter-spacing: 0.5px; font-size: 0.75rem;"><?php echo e(Str::limit($k->nama_kriteria, 15)); ?></small>
                            </th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <th class="border-0 py-3 text-center">
                                <small class="text-uppercase fw-bold" style="color: var(--text-muted); letter-spacing: 0.5px;">🏆 Nilai SAW</small>
                            </th>
                            <th class="border-0 py-3 text-center">
                                <small class="text-uppercase fw-bold" style="color: var(--text-muted); letter-spacing: 0.5px;">⚙️ Aksi</small>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $hasil; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="<?php echo e($index == 0 ? 'table-warning table-warning-light' : ''); ?>" id="row-<?php echo e($index); ?>" style="transition: all 0.2s ease;">
                            <td class="px-4">
                                <?php if($index == 0): ?>
                                    <div class="ranking-badge rank-1">
                                        <i class="bi bi-trophy-fill"></i>
                                        <span>#<?php echo e($item['ranking']); ?></span>
                                    </div>
                                <?php elseif($index == 1): ?>
                                    <div class="ranking-badge rank-2">
                                        <i class="bi bi-award-fill"></i>
                                        <span>#<?php echo e($item['ranking']); ?></span>
                                    </div>
                                <?php elseif($index == 2): ?>
                                    <div class="ranking-badge rank-3">
                                        <i class="bi bi-award-fill"></i>
                                        <span>#<?php echo e($item['ranking']); ?></span>
                                    </div>
                                <?php else: ?>
                                    <div class="badge rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(var(--primary-color-rgb), 0.15); color: var(--primary-color); font-weight: 700;">
                                        <?php echo e($item['ranking']); ?>

                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if(isset($item['foto']) && $item['foto']): ?>
                                        <img src="<?php echo e(asset('uploads/' . $item['foto'])); ?>" 
                                             alt="<?php echo e($item['nama']); ?>" 
                                             class="rounded-circle object-cover me-2" 
                                             style="width: 40px; height: 40px; border: 2px solid rgba(var(--primary-color-rgb), 0.2); flex-shrink: 0;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-gradient-primary d-flex align-items-center justify-content-center text-white fw-bold me-2" style="width: 40px; height: 40px; font-size: 0.8rem; flex-shrink: 0;">
                                            <?php echo e(strtoupper(substr($item['nama'], 0, 2))); ?>

                                        </div>
                                    <?php endif; ?>
                                    <div style="min-width: 0;">
                                        <h6 class="mb-1 fw-bold text-truncate" style="color: var(--text-primary); font-size: 0.95rem;"><?php echo e(Str::limit($item['nama'], 25)); ?></h6>
                                        <?php if($index == 0): ?>
                                            <small class="text-success fw-bold d-flex align-items-center" style="font-size: 0.75rem;">
                                                <i class="bi bi-trophy-fill me-1"></i>Terbaik
                                            </small>
                                        <?php elseif($index == 1): ?>
                                            <small class="text-secondary fw-bold d-flex align-items-center" style="font-size: 0.75rem;">
                                                <i class="bi bi-award-fill me-1"></i>Runner-up
                                            </small>
                                        <?php elseif($index == 2): ?>
                                            <small class="text-danger fw-bold d-flex align-items-center" style="font-size: 0.75rem;">
                                                <i class="bi bi-award-fill me-1"></i>Ketiga
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center text-muted" style="font-size: 0.9rem;">
                                    <i class="bi bi-geo-alt me-2" style="color: var(--danger-color); flex-shrink: 0;"></i>
                                    <span class="text-truncate"><?php echo e(Str::limit($item['alamat'], 35)); ?></span>
                                </div>
                            </td>
                            <?php $__currentLoopData = $kriteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td class="text-center">
                                <?php if(isset($item['normalisasi'][$k->nama_kriteria])): ?>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                        <?php echo e($item['normalisasi'][$k->nama_kriteria]['asli']); ?>

                                    </small>
                                    <small class="badge bg-info bg-opacity-25 text-dark">
                                        <?php echo e($item['normalisasi'][$k->nama_kriteria]['normalisasi']); ?>

                                    </small>
                                <?php else: ?>
                                    <small class="text-muted">-</small>
                                <?php endif; ?>
                            </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <td class="text-center">
                                <div class="d-flex flex-column align-items-center">
                                    <?php if($index == 0): ?>
                                        <div class="badge rounded-pill px-3 py-2 mb-1" style="background: linear-gradient(135deg, #ffd700, #ffed4e); color: #8B5A00; font-weight: 700; font-size: 0.9rem; box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);">
                                            🏆 <?php echo e(number_format($item['nilai'], 4)); ?>

                                        </div>
                                        <small class="text-success fw-bold" style="font-size: 0.7rem;">TERBAIK</small>
                                    <?php elseif($index == 1): ?>
                                        <div class="badge rounded-pill px-3 py-2 mb-1" style="background: linear-gradient(135deg, #c0c0c0, #e5e5e5); color: #666; font-weight: 700; font-size: 0.9rem; box-shadow: 0 4px 12px rgba(192, 192, 192, 0.3);">
                                            🥈 <?php echo e(number_format($item['nilai'], 4)); ?>

                                        </div>
                                        <small class="text-secondary fw-bold" style="font-size: 0.7rem;">RUNNER-UP</small>
                                    <?php elseif($index == 2): ?>
                                        <div class="badge rounded-pill px-3 py-2 mb-1" style="background: linear-gradient(135deg, #cd7f32, #d4943d); color: white; font-weight: 700; font-size: 0.9rem; box-shadow: 0 4px 12px rgba(205, 127, 50, 0.3);">
                                            🥉 <?php echo e(number_format($item['nilai'], 4)); ?>

                                        </div>
                                        <small class="text-danger fw-bold" style="font-size: 0.7rem;">KETIGA</small>
                                    <?php else: ?>
                                        <div class="badge rounded-pill px-3 py-2" style="background: rgba(var(--primary-color-rgb), 0.15); color: var(--primary-color); font-weight: 700; font-size: 0.9rem;">
                                            <?php echo e(number_format($item['nilai'], 4)); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap" style="min-width: 0;">
                                    <button 
                                        class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" 
                                        onclick="focusOnMarker(<?php echo e($index); ?>)"
                                        title="Lihat di Peta"
                                        style="transition: all 0.2s ease; border-width: 1.5px;"
                                        onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 12px rgba(var(--primary-color-rgb), 0.3)'" 
                                        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow=''"
                                    >
                                        <i class="bi bi-pin-map-fill"></i>
                                        <span class="d-none d-lg-inline ms-1">Peta</span>
                                    </button>
                                    <?php if(isset($item['latitude']) && isset($item['longitude']) && $item['latitude'] && $item['longitude']): ?>
                                    <a 
                                        href="https://www.google.com/maps/dir/?api=1&destination=<?php echo e($item['latitude']); ?>,<?php echo e($item['longitude']); ?>" 
                                        target="_blank"
                                        class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1"
                                        title="Buka di Google Maps"
                                        style="transition: all 0.2s ease; border-width: 1.5px;"
                                        onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 12px rgba(var(--danger-color-rgb), 0.3)'" 
                                        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow=''"
                                    >
                                        <i class="bi bi-google"></i>
                                        <span class="d-none d-lg-inline ms-1">Maps</span>
                                    </a>
                                    <?php endif; ?>
                                    <?php if(isset($item['no_whatsapp']) && !empty($item['no_whatsapp'])): ?>
                                    <a 
                                        href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $item['no_whatsapp'])); ?>" 
                                        target="_blank"
                                        class="btn btn-sm btn-success rounded-pill px-3 py-1"
                                        title="Chat WhatsApp"
                                        style="transition: all 0.2s ease; border-width: 1.5px; background: #25D366; border-color: #25D366;"
                                        onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 12px rgba(37, 211, 102, 0.3)'" 
                                        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow=''"
                                    >
                                        <i class="bi bi-whatsapp"></i>
                                        <span class="d-none d-lg-inline ms-1">WA</span>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Enhanced Info & Action Buttons -->
    <div class="row g-3 g-md-4">
        <div class="col-12 col-lg-8">
            <div class="glass-card">
                <div class="card-header border-0 py-3" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm d-flex align-items-center justify-content-center me-2 rounded-circle" style="background: rgba(var(--info-color-rgb), 0.15); color: var(--info-color);">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                            <span class="d-none d-sm-inline">Keterangan Perhitungan SAW</span>
                            <span class="d-sm-none">Keterangan</span>
                        </h6>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <div class="avatar-xs d-flex align-items-center justify-content-center me-2 rounded-circle" style="background: rgba(var(--primary-color-rgb), 0.1); color: var(--primary-color); min-width: 24px; height: 24px;">
                                    <i class="bi bi-1-circle" style="font-size: 0.8rem;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1" style="color: var(--text-primary); font-size: 0.9rem;">Nilai Asli</h6>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem;">Data kriteria sebelum normalisasi</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <div class="avatar-xs d-flex align-items-center justify-content-center me-2 rounded-circle" style="background: rgba(var(--info-color-rgb), 0.1); color: var(--info-color); min-width: 24px; height: 24px;">
                                    <i class="bi bi-2-circle" style="font-size: 0.8rem;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1" style="color: var(--text-primary); font-size: 0.9rem;">Nilai Normalisasi</h6>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem;">Nilai setelah normalisasi (badge biru)</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <div class="avatar-xs d-flex align-items-center justify-content-center me-2 rounded-circle" style="background: rgba(var(--success-color-rgb), 0.1); color: var(--success-color); min-width: 24px; height: 24px;">
                                    <i class="bi bi-3-circle" style="font-size: 0.8rem;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1" style="color: var(--text-primary); font-size: 0.9rem;">Nilai SAW</h6>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem;">Hasil (normalisasi × bobot) semua kriteria</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <div class="avatar-xs d-flex align-items-center justify-content-center me-2 rounded-circle" style="background: rgba(var(--warning-color-rgb), 0.1); color: var(--warning-color); min-width: 24px; height: 24px;">
                                    <i class="bi bi-4-circle" style="font-size: 0.8rem;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1" style="color: var(--text-primary); font-size: 0.9rem;">Peringkat</h6>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem;">Urutan dari nilai SAW tertinggi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-3" style="border-top: 1px solid rgba(0,0,0,0.1);">
                        <div class="d-flex align-items-center">
                            <div class="avatar-xs d-flex align-items-center justify-content-center me-2 rounded-circle" style="background: rgba(var(--danger-color-rgb), 0.1); color: var(--danger-color); min-width: 24px; height: 24px;">
                                <i class="bi bi-geo-alt" style="font-size: 0.8rem;"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1" style="color: var(--text-primary); font-size: 0.9rem;">Jarak</h6>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                    <?php if($tipe == 'kontrakan'): ?>
                                        Dihitung otomatis dari Kampus Polije menggunakan Google Maps
                                    <?php else: ?>
                                        Dihitung otomatis dari lokasi Anda menggunakan GPS
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="glass-card h-100">
                <div class="card-header border-0 py-3 text-center" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="avatar-sm d-flex align-items-center justify-content-center me-2 rounded-circle" style="background: rgba(var(--warning-color-rgb), 0.15); color: var(--warning-color);">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">Rekomendasi Terbaik</h6>
                    </div>
                </div>
                <div class="card-body text-center p-3 p-md-4">
                    <?php if(count($hasil) > 0): ?>
                        <?php if(isset($hasil[0]['foto']) && $hasil[0]['foto']): ?>
                            <img src="<?php echo e(asset('uploads/' . $hasil[0]['foto'])); ?>" 
                                 alt="<?php echo e($hasil[0]['nama']); ?>" 
                                 class="rounded-circle object-cover mx-auto mb-3" 
                                 style="width: 80px; height: 80px; border: 3px solid rgba(var(--warning-color-rgb), 0.3);">
                        <?php else: ?>
                            <div class="rounded-circle bg-gradient-warning d-flex align-items-center justify-content-center text-white fw-bold mx-auto mb-3" style="width: 80px; height: 80px; font-size: 1.5rem;">
                                🏆
                            </div>
                        <?php endif; ?>
                        
                        <h6 class="fw-bold mb-2" style="color: var(--text-primary);"><?php echo e(Str::limit($hasil[0]['nama'], 25)); ?></h6>
                        <div class="d-flex align-items-center justify-content-center text-muted mb-3" style="font-size: 0.85rem;">
                            <i class="bi bi-geo-alt me-1" style="color: var(--danger-color);"></i>
                            <span><?php echo e(Str::limit($hasil[0]['alamat'], 30)); ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <div class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #ffd700, #ffed4e); color: #8B5A00; font-weight: 700; box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);">
                                🏆 Nilai: <?php echo e(number_format($hasil[0]['nilai'], 4)); ?>

                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <?php if(isset($hasil[0]['latitude']) && isset($hasil[0]['longitude']) && $hasil[0]['latitude'] && $hasil[0]['longitude']): ?>
                            <a 
                                href="https://www.google.com/maps/dir/?api=1&destination=<?php echo e($hasil[0]['latitude']); ?>,<?php echo e($hasil[0]['longitude']); ?>" 
                                target="_blank"
                                class="btn btn-outline-danger rounded-pill"
                                style="transition: all 0.3s ease;"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(var(--danger-color-rgb), 0.3)'" 
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''"
                            >
                                <i class="bi bi-map me-2"></i>Navigasi ke Lokasi
                            </a>
                            <?php endif; ?>
                            <?php if(isset($hasil[0]['no_whatsapp']) && !empty($hasil[0]['no_whatsapp'])): ?>
                            <a 
                                href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $hasil[0]['no_whatsapp'])); ?>?text=Halo,%20saya%20tertarik%20dengan%20<?php echo e(urlencode($hasil[0]['nama'])); ?>%20dari%20hasil%20rekomendasi%20SPK.%20Apakah%20masih%20tersedia?" 
                                target="_blank"
                                class="btn btn-success rounded-pill"
                                style="transition: all 0.3s ease; background: #25D366; border-color: #25D366;"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(37, 211, 102, 0.3)'" 
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''"
                            >
                                <i class="bi bi-whatsapp me-1"></i>Hubungi Pemilik
                            </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mt-3 mt-md-4">
        <a href="<?php echo e(route('saw.index')); ?>" class="btn btn-secondary btn-sm w-100 w-md-auto order-2 order-md-1">
            <i class="bi bi-arrow-left me-2"></i>Hitung Ulang
        </a>
        <button class="btn btn-danger btn-sm w-100 w-md-auto order-1 order-md-2" onclick="window.print()">
            <i class="bi bi-printer me-2"></i>Cetak Hasil
        </button>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let map;
let markers = [];
let referenceMarker = null;

document.addEventListener('DOMContentLoaded', function() {
    initMap();
    setTimeout(() => {
        initBarChart();
        initRadarChart();
    }, 500);
});

function initMap() {
    const hasil = <?php echo json_encode($hasil, 15, 512) ?>;
    const tipe = '<?php echo e($tipe); ?>';
    const userLat = <?php echo e($userLat ?? 'null'); ?>;
    const userLng = <?php echo e($userLng ?? 'null'); ?>;
    
    // Koordinat Kampus Polije (FIXED)
    const KAMPUS_LAT = -8.15981;
    const KAMPUS_LNG = 113.72312;
    
    let centerLat = -7.797068;
    let centerLng = 110.370529;
    
    if (hasil.length > 0 && hasil[0].latitude && hasil[0].longitude) {
        centerLat = hasil[0].latitude;
        centerLng = hasil[0].longitude;
    }
    
    map = L.map('map').setView([centerLat, centerLng], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19
    }).addTo(map);
    
    // PENTING: Marker reference point berbeda untuk kontrakan vs laundry
    if (tipe === 'kontrakan') {
        // KONTRAKAN: Tampilkan marker Kampus Polije
        const kampusIcon = L.divIcon({
            className: 'custom-marker',
            html: `<div style="background-color: #17a2b8; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"></div>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });
        
        referenceMarker = L.marker([KAMPUS_LAT, KAMPUS_LNG], { icon: kampusIcon })
            .addTo(map)
            .bindPopup(`
                <div class="p-2">
                    <h6 class="fw-bold mb-1 small">🎓 Kampus Polije</h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Titik acuan jarak kontrakan</small>
                </div>
            `);
    } else {
        // LAUNDRY: Tampilkan marker Lokasi User
        if (userLat && userLng) {
            const userIcon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: #28a745; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"></div>`,
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });
            
            referenceMarker = L.marker([userLat, userLng], { icon: userIcon })
                .addTo(map)
                .bindPopup(`
                    <div class="p-2">
                        <h6 class="fw-bold mb-1 small">📍 Lokasi Anda</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">Lat: ${userLat.toFixed(6)}, Lng: ${userLng.toFixed(6)}</small>
                    </div>
                `);
        }
    }
    
    hasil.forEach((item, index) => {
        if (item.latitude && item.longitude) {
            addMarker(item, index);
        }
    });
    
    if (markers.length > 0) {
        const group = new L.featureGroup(markers.map(m => m.marker));
        if (referenceMarker) group.addLayer(referenceMarker);
        map.fitBounds(group.getBounds().pad(0.1));
    }
}

function addMarker(item, index) {
    let color = '#0d6efd';
    let icon = '📍';
    
    if (index === 0) { color = '#ffc107'; icon = '🏆'; }
    else if (index === 1) { color = '#6c757d'; icon = '🥈'; }
    else if (index === 2) { color = '#dc3545'; icon = '🥉'; }
    
    const customIcon = L.divIcon({
        className: 'custom-marker',
        html: `
            <div style="position: relative;">
                <div style="background-color: ${color}; width: 24px; height: 24px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 12px;">${icon}</div>
                <div style="position: absolute; bottom: -6px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 6px solid transparent; border-right: 6px solid transparent; border-top: 6px solid ${color};"></div>
            </div>
        `,
        iconSize: [24, 30],
        iconAnchor: [12, 30],
        popupAnchor: [0, -30]
    });
    
    const marker = L.marker([item.latitude, item.longitude], { icon: customIcon }).addTo(map);
    
    let waButton = '';
    if (item.no_whatsapp) {
        const waNumber = item.no_whatsapp.replace(/[^0-9]/g, '');
        waButton = `
            <a href="https://wa.me/${waNumber}" 
               target="_blank" 
               class="btn btn-sm btn-success w-100 mt-2">
                <i class="bi bi-whatsapp me-1"></i>Chat WhatsApp
            </a>
        `;
    }
    
    const popupContent = `
        <div class="p-2" style="min-width: 200px;">
            <div class="d-flex align-items-center mb-2">
                <span class="badge bg-${index === 0 ? 'warning' : (index === 1 ? 'secondary' : (index === 2 ? 'danger' : 'primary'))} me-2 small">
                    #${item.ranking}
                </span>
                <h6 class="fw-bold mb-0 small">${item.nama}</h6>
            </div>
            <p class="small text-muted mb-2" style="font-size: 0.75rem;">
                <i class="bi bi-geo-alt me-1"></i>${item.alamat}
            </p>
            ${item.no_whatsapp ? `<p class="small mb-2" style="font-size: 0.75rem;"><i class="bi bi-whatsapp text-success me-1"></i>${item.no_whatsapp}</p>` : ''}
            <div class="border-top pt-2 mb-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted" style="font-size: 0.75rem;">Nilai SAW:</small>
                    <span class="badge bg-success small">${item.nilai.toFixed(4)}</span>
                </div>
            </div>
            <div class="d-grid gap-2">
                <a href="https://www.google.com/maps/dir/?api=1&destination=${item.latitude},${item.longitude}" 
                   target="_blank" 
                   class="btn btn-sm btn-danger">
                    <i class="bi bi-google me-1"></i>Buka di Google Maps
                </a>
                ${waButton}
            </div>
        </div>
    `;
    
    marker.bindPopup(popupContent, { maxWidth: 280, className: 'custom-popup' });
    markers.push({ marker: marker, index: index, data: item });
}

function focusOnMarker(index) {
    const markerData = markers.find(m => m.index === index);
    if (markerData) {
        map.setView(markerData.marker.getLatLng(), 16, { animate: true, duration: 1 });
        markerData.marker.openPopup();
        
        document.querySelectorAll('[id^="row-"], [id^="mobile-row-"]').forEach(row => {
            row.classList.remove('table-warning', 'bg-warning', 'bg-opacity-25');
        });
        
        const row = document.getElementById(`row-${index}`);
        const mobileRow = document.getElementById(`mobile-row-${index}`);
        
        if (row) {
            row.classList.add('table-warning');
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => row.classList.remove('table-warning'), 2000);
        }
        
        if (mobileRow) {
            mobileRow.classList.add('bg-warning', 'bg-opacity-25');
            mobileRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => mobileRow.classList.remove('bg-warning', 'bg-opacity-25'), 2000);
        }
    }
}

function resetMapView() {
    if (markers.length > 0) {
        const group = new L.featureGroup(markers.map(m => m.marker));
        if (referenceMarker) group.addLayer(referenceMarker);
        map.fitBounds(group.getBounds().pad(0.1), { animate: true, duration: 1 });
    }
}

function initBarChart() {
    const hasil = <?php echo json_encode($hasil, 15, 512) ?>;
    const top10 = hasil.slice(0, 10);
    
    const labels = top10.map(item => item.nama.length > 15 ? item.nama.substring(0, 15) + '...' : item.nama);
    const values = top10.map(item => item.nilai);
    
    const backgroundColors = top10.map((item, index) => {
        if (index === 0) return 'rgba(255, 193, 7, 0.8)';
        if (index === 1) return 'rgba(108, 117, 125, 0.8)';
        if (index === 2) return 'rgba(220, 53, 69, 0.8)';
        return 'rgba(13, 110, 253, 0.6)';
    });
    
    const ctx = document.getElementById('barChartSAW');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nilai SAW',
                data: values,
                backgroundColor: backgroundColors,
                borderColor: backgroundColors.map(c => c.replace('0.6)', '1)').replace('0.8)', '1)')),
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10,
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 11 },
                    callbacks: {
                        title: function(context) {
                            const index = context[0].dataIndex;
                            let medal = '';
                            if (index === 0) medal = '🏆 ';
                            if (index === 1) medal = '🥈 ';
                            if (index === 2) medal = '🥉 ';
                            return medal + 'Peringkat #' + (index + 1);
                        },
                        label: function(context) {
                            return 'Nilai: ' + context.parsed.y.toFixed(4);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    ticks: { font: { size: 10 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 9 }, maxRotation: 45, minRotation: 45 }
                }
            }
        }
    });
}

function initRadarChart() {
    const hasil = <?php echo json_encode($hasil, 15, 512) ?>;
    const kriteria = <?php echo json_encode($kriteria, 15, 512) ?>;
    const top3 = hasil.slice(0, 3);
    
    if (top3.length === 0) return;
    
    const labels = kriteria.map(k => k.nama_kriteria.length > 10 ? k.nama_kriteria.substring(0, 10) + '...' : k.nama_kriteria);
    
    const datasets = top3.map((item, index) => {
        const data = kriteria.map(k => {
            const norm = item.normalisasi[k.nama_kriteria];
            return norm ? norm.normalisasi : 0;
        });
        
        let color = 'rgba(13, 110, 253, 0.6)';
        let borderColor = 'rgba(13, 110, 253, 1)';
        let label = item.nama;
        
        if (index === 0) {
            color = 'rgba(255, 193, 7, 0.3)';
            borderColor = 'rgba(255, 193, 7, 1)';
            label = '🏆 ' + (item.nama.length > 12 ? item.nama.substring(0, 12) + '...' : item.nama);
        } else if (index === 1) {
            color = 'rgba(108, 117, 125, 0.3)';
            borderColor = 'rgba(108, 117, 125, 1)';
            label = '🥈 ' + (item.nama.length > 12 ? item.nama.substring(0, 12) + '...' : item.nama);
        } else if (index === 2) {
            color = 'rgba(220, 53, 69, 0.3)';
            borderColor = 'rgba(220, 53, 69, 1)';
            label = '🥉 ' + (item.nama.length > 12 ? item.nama.substring(0, 12) + '...' : item.nama);
        }
        
        return {
            label: label,
            data: data,
            backgroundColor: color,
            borderColor: borderColor,
            borderWidth: 2,
            pointBackgroundColor: borderColor,
            pointBorderColor: '#fff',
            pointRadius: 3,
            pointHoverRadius: 5
        };
    });
    
    const ctx = document.getElementById('radarChartTop3');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'radar',
        data: { labels: labels, datasets: datasets },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: { font: { size: 9 }, padding: 10, usePointStyle: true }
                }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    max: 1,
                    ticks: { stepSize: 0.2, font: { size: 8 } },
                    pointLabels: { font: { size: 9, weight: 'bold' } }
                }
            }
        }
    });
}
</script>

<style>
.table tbody tr {
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    transform: scale(1.005);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.custom-marker {
    background: none;
    border: none;
}

.custom-popup .leaflet-popup-content-wrapper {
    border-radius: 8px;
    padding: 0;
}

.table-warning, .bg-warning.bg-opacity-25 {
    animation: highlight 0.5s ease-in-out;
}

@keyframes highlight {
    0%, 100% { background-color: transparent; }
    50% { background-color: #fff3cd; }
}

.progress {
    background-color: #f0f0f0;
    border-radius: 10px;
}

.progress-bar {
    transition: width 1s ease-in-out;
    animation: progressAnimation 1.5s ease-out;
}

@keyframes progressAnimation {
    from { width: 0%; }
}

@media (max-width: 767px) {
    #map {
        height: 250px !important;
    }
    
    canvas {
        max-height: 250px !important;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.25em 0.5em;
    }
    
    .btn-sm {
        font-size: 0.8rem;
        padding: 0.35rem 0.6rem;
    }
}

@media (min-width: 768px) {
    #map {
        height: 400px !important;
    }
    
    canvas {
        max-height: 350px !important;
    }
}

@media print {
    .btn, .breadcrumb, nav, #map, .card-footer {
        display: none !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
    }
    
    canvas {
        max-height: 250px !important;
    }
}
</style>

<script>
// Handle Export Buttons
document.getElementById('btnExcelSAW').addEventListener('click', function() {
    const hasil = <?php echo json_encode($hasil); ?>;
    document.querySelector('#formExcelSAW input[name="hasil_json"]').value = JSON.stringify(hasil);
    document.getElementById('formExcelSAW').submit();
});

document.getElementById('btnPdfSAW').addEventListener('click', function() {
    const hasil = <?php echo json_encode($hasil); ?>;
    document.querySelector('#formPdfSAW input[name="hasil_json"]').value = JSON.stringify(hasil);
    document.getElementById('formPdfSAW').submit();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TA\spk_kontrakan\resources\views/saw/hasil.blade.php ENDPATH**/ ?>