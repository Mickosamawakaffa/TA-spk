

<?php $__env->startSection('title', 'Kelola Kriteria SAW'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2 px-md-4 page-kriteria">
    <!-- Enhanced Mobile-First Styling -->
    <style>
        /* Page Background & Pattern */
        .page-kriteria {
            position: relative;
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 50%, #faf5ff 100%);
        }
        
        .page-kriteria::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 15% 40%, rgba(251, 146, 60, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 85% 60%, rgba(102, 126, 234, 0.06) 0%, transparent 40%),
                radial-gradient(circle at 50% 80%, rgba(118, 75, 162, 0.04) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }
        
        /* Floating Decorations */
        .floating-decoration {
            position: fixed;
            pointer-events: none;
            z-index: 0;
            opacity: 0.5;
        }
        
        .floating-decoration.deco-1 {
            top: 12%;
            right: 10%;
            width: 130px;
            height: 130px;
            background: linear-gradient(135deg, rgba(251, 146, 60, 0.15) 0%, rgba(102, 126, 234, 0.05) 100%);
            border-radius: 50%;
            filter: blur(28px);
            animation: kritFloat 8s ease-in-out infinite;
        }
        
        .floating-decoration.deco-2 {
            bottom: 30%;
            left: 8%;
            width: 160px;
            height: 160px;
            background: linear-gradient(135deg, rgba(118, 75, 162, 0.12) 0%, rgba(251, 146, 60, 0.05) 100%);
            border-radius: 50%;
            filter: blur(35px);
            animation: kritFloat 10s ease-in-out infinite reverse;
        }
        
        .floating-decoration.deco-3 {
            top: 65%;
            right: 18%;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.12) 0%, transparent 100%);
            border-radius: 50%;
            filter: blur(20px);
            animation: kritFloat 6s ease-in-out infinite 1s;
        }
        
        @keyframes kritFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        /* Stats Cards */
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.25rem;
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
            box-shadow: 0 8px 30px rgba(251, 146, 60, 0.15);
        }
        
        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 0.75rem;
        }
        
        .stat-card .stat-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--stat-color);
            line-height: 1.2;
        }
        
        .stat-card .stat-label {
            font-size: 0.82rem;
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
            background: linear-gradient(90deg, #818cf8 0%, #667eea 50%, #764ba2 100%);
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
        }
        
        @keyframes gradientMove {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .kriteria-header {
            background: linear-gradient(135deg, #818cf8 0%, #667eea 50%, #764ba2 100%);
            border-radius: 1rem;
            padding: 1.5rem;
            color: white;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 30px rgba(251, 146, 60, 0.25);
            position: relative;
            overflow: hidden;
        }
        
        .kriteria-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: kriteriaFloat 8s ease-in-out infinite;
        }
        
        @keyframes kriteriaFloat {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(3deg); }
        }
        
        .btn-kriteria {
            background: rgba(255,255,255,0.95);
            color: #818cf8;
            font-weight: 600;
            border: 2px solid rgba(255,255,255,0.3);
            padding: 0.6rem 1rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .btn-kriteria:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            background: white;
            color: #667eea;
        }
        
        .enhanced-kriteria-card {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 1rem;
            box-shadow: 0 8px 25px rgba(251, 146, 60, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .enhanced-kriteria-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(251, 146, 60, 0.15);
        }

        /* Enhanced Table */
        .enhanced-table thead {
            background: linear-gradient(135deg, #818cf8 0%, #667eea 100%);
        }
        
        .enhanced-table thead th {
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 1rem 0.75rem;
            border: none;
        }
        
        .enhanced-table tbody tr {
            background: rgba(255, 255, 255, 0.7);
            transition: all 0.3s ease;
        }
        
        .enhanced-table tbody tr:nth-child(even) {
            background: rgba(251, 146, 60, 0.03);
        }
        
        .enhanced-table tbody tr:hover {
            background: rgba(251, 146, 60, 0.08);
            transform: scale(1.005);
        }
        
        .enhanced-table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
        }
        
        @media (max-width: 768px) {
            .kriteria-header {
                padding: 1rem;
                margin-bottom: 1rem;
            }
            .btn-kriteria {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            .stats-overview {
                grid-template-columns: repeat(2, 1fr);
            }
            .stat-card {
                padding: 1rem;
            }
            .stat-card .stat-value {
                font-size: 1.35rem;
            }
        }
        
        @media (max-width: 576px) {
            .stats-overview {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .glass-card-light {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
    
    <!-- Floating Decorations -->
    <div class="floating-decoration deco-1"></div>
    <div class="floating-decoration deco-2"></div>
    <div class="floating-decoration deco-3"></div>
    
    <!-- Enhanced Header Section -->
    <div class="kriteria-header">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-12 col-md-8">
                <h2 class="mb-2 fw-bold">
                    <i class="bi bi-diagram-3 me-2 me-md-3"></i>
                    <span class="d-none d-sm-inline">📊 Kelola Kriteria SAW</span>
                    <span class="d-sm-none">📊 Kriteria</span>
                </h2>
                <p class="mb-0 opacity-90">
                    <span class="d-none d-md-inline">Atur dan kelola kriteria penilaian untuk metode Simple Additive Weighting</span>
                    <span class="d-md-none">Kelola kriteria SAW</span>
                </p>
            </div>
            <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('kriteria.create')); ?>" class="btn btn-kriteria">
                    <i class="bi bi-plus-circle me-2"></i>
                    <span class="d-none d-sm-inline">Tambah Kriteria</span>
                    <span class="d-sm-none">Tambah</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Enhanced Alert Success -->
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-lg rounded-3 border-0" role="alert" style="background: linear-gradient(135deg, var(--success-color), #20c997); color: white; border-left: 4px solid white;">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>Berhasil!</strong> 
        <span class="small"><?php echo e(session('success')); ?></span>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Enhanced Alert Error -->
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-lg rounded-3 border-0" role="alert" style="background: linear-gradient(135deg, var(--danger-color), #e74c3c); color: white; border-left: 4px solid white;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Terjadi Kesalahan!</strong> 
        <span class="small"><?php echo e(session('error')); ?></span>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Enhanced Alert Validasi Bobot -->
    <?php if(!empty($bobotWarnings)): ?>
    <div class="alert alert-dismissible fade show shadow-lg rounded-3 border-0 mb-4" role="alert" style="background: linear-gradient(135deg, var(--warning-color), #f39c12); color: white; border-left: 4px solid white;">
        <div class="d-flex align-items-start">
            <div class="me-3">
                <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.8rem; opacity: 0.9;"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-2">⚠️ Peringatan Total Bobot!</h6>
                <ul class="mb-3 ps-3" style="line-height: 1.6;">
                    <?php $__currentLoopData = $bobotWarnings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warning): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="mb-1"><?php echo e($warning); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <div class="p-2 rounded" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(5px);">
                    <small class="d-block">
                        <i class="bi bi-info-circle me-1"></i>
                        <span class="d-none d-md-inline">Total bobot untuk setiap tipe bisnis harus = 1.00 (atau 100%) agar perhitungan SAW valid dan akurat.</span>
                        <span class="d-md-none">Total bobot tiap tipe bisnis harus = 1.00 (100%)</span>
                    </small>
                </div>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Enhanced Main Card -->
    <div class="glass-card border-0 shadow-lg">
        <!-- Enhanced Search & Filter Section -->
        <div class="card-header border-0 py-3 py-md-4" style="border-radius: 1rem 1rem 0 0; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
            <form action="<?php echo e(route('kriteria.index')); ?>" method="GET" id="filterForm">
                <!-- Hidden inputs untuk maintain sorting saat filter -->
                <input type="hidden" name="sort_by" id="hiddenSortBy" value="<?php echo e($sortBy ?? ''); ?>">
                <input type="hidden" name="sort_order" id="hiddenSortOrder" value="<?php echo e($sortOrder ?? ''); ?>">
                
                <div class="row g-3 align-items-center">
                    <!-- Enhanced Search Input -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold mb-2 d-block d-md-none">
                            <i class="bi bi-search me-1" style="color: var(--primary-color);"></i>Cari Kriteria
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-color: var(--border-color);">
                                <i class="bi bi-search" style="color: var(--primary-color);"></i>
                            </span>
                            <input 
                                type="text" 
                                class="form-control border-start-0" 
                                id="searchInput"
                                placeholder="Cari nama kriteria..."
                                style="border-color: var(--border-color);"
                            >
                        </div>
                    </div>
                    
                    <!-- Enhanced Filter Dropdown -->
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-bold mb-2 d-block d-md-none">
                            <i class="bi bi-funnel me-1" style="color: var(--secondary-color);"></i>Filter Tipe
                        </label>
                        <select class="form-select" id="filterTipeBisnis" name="filter" onchange="this.form.submit()" style="border-color: var(--border-color);">
                            <option value="" <?php echo e(empty($filterTipeBisnis) ? 'selected' : ''); ?>>🔍 Semua Tipe Bisnis</option>
                            <option value="kontrakan" <?php echo e($filterTipeBisnis == 'kontrakan' ? 'selected' : ''); ?>>🏠 Kontrakan</option>
                            <option value="laundry" <?php echo e($filterTipeBisnis == 'laundry' ? 'selected' : ''); ?>>👕 Laundry</option>
                        </select>
                    </div>
                    
                    <!-- Enhanced Count Badges -->
                    <div class="col-12 col-md-5">
                        <div class="d-flex flex-column flex-md-row gap-2 justify-content-md-end">
                            <span class="badge px-3 py-2 rounded-3" style="background: rgba(var(--primary-color-rgb), 0.1); color: var(--primary-color); font-size: 0.85rem; font-weight: 600;">
                                🏠 Kontrakan: <strong id="countKontrakan"><?php echo e(isset($allKriteria) ? $allKriteria->where('tipe_bisnis', 'kontrakan')->count() : 0); ?></strong>
                            </span>
                            <span class="badge px-3 py-2 rounded-3" style="background: rgba(var(--success-color-rgb), 0.1); color: var(--success-color); font-size: 0.85rem; font-weight: 600;">
                                👕 Laundry: <strong id="countLaundry"><?php echo e(isset($allKriteria) ? $allKriteria->where('tipe_bisnis', 'laundry')->count() : 0); ?></strong>
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Enhanced Card Body -->
        <div class="card-body p-0" style="background: rgba(255,255,255,0.98); backdrop-filter: blur(5px);">
            <?php if($kriteria->isEmpty()): ?>
                <!-- Enhanced Empty State -->
                <div class="text-center py-5 px-3">
                    <div class="mb-4" style="animation: pulse 2s infinite;">
                        <div class="d-inline-flex align-items-center justify-content-center" 
                             style="width: 100px; height: 100px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; opacity: 0.1;">
                            <i class="bi bi-list-check" style="font-size: 3rem; color: var(--primary-color);"></i>
                        </div>
                    </div>
                    <h5 class="mb-3" style="color: var(--text-secondary); font-weight: 600;">Belum ada kriteria</h5>
                    <p class="text-muted mb-4 px-md-5">Mulai tambahkan kriteria untuk penilaian SAW (Simple Additive Weighting) agar dapat melakukan perhitungan rekomendasi yang akurat.</p>
                    <a href="<?php echo e(route('kriteria.create')); ?>" class="btn btn-primary btn-lg rounded-3 px-4" style="box-shadow: 0 6px 20px rgba(var(--primary-color-rgb), 0.3); border: none; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Kriteria Pertama
                    </a>
                </div>
            <?php else: ?>
                <!-- Mobile Card View (Hidden on desktop) -->
                <div class="d-block d-lg-none">
                    <div class="p-3 p-md-4">
                        <?php $__currentLoopData = $kriteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="card border-0 mb-3 glass-card-light" 
                             data-tipe-bisnis="<?php echo e($item->tipe_bisnis); ?>" 
                             data-bobot="<?php echo e($item->bobot); ?>" 
                             data-tipe="<?php echo e(strtolower($item->tipe)); ?>"
                             style="transition: all 0.3s ease; cursor: pointer;"
                             onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.15)'"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)'">
                            <div class="card-body p-3">
                                <!-- Header Row -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-light text-primary rounded-circle me-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.8rem;"><?php echo e($index + 1); ?></div>
                                        <?php if($item->tipe_bisnis == 'kontrakan'): ?>
                                            <span class="badge px-3 py-2 rounded-3" style="background: rgba(var(--primary-color-rgb), 0.15); color: var(--primary-color); font-weight: 600;">
                                                🏠 Kontrakan
                                            </span>
                                        <?php else: ?>
                                            <span class="badge px-3 py-2 rounded-3" style="background: rgba(var(--success-color-rgb), 0.15); color: var(--success-color); font-weight: 600;">
                                                👕 Laundry
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Kriteria Name -->
                                <h6 class="fw-bold mb-3" style="color: var(--text-primary); line-height: 1.4;"><?php echo e($item->nama_kriteria); ?></h6>
                                
                                <!-- Details Grid -->
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted fw-bold mb-1">BOBOT</small>
                                            <div class="d-flex align-items-center">
                                                <span class="badge px-2 py-1 rounded-3 me-2" style="background: rgba(var(--warning-color-rgb), 0.15); color: var(--warning-color); font-weight: 700; font-size: 0.9rem;"><?php echo e($item->bobot); ?></span>
                                                <small class="text-muted">(<?php echo e(number_format($item->bobot * 100, 1)); ?>%)</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <small class="text-muted fw-bold mb-1">TIPE</small>
                                            <?php if(strtolower($item->tipe) == 'benefit'): ?>
                                                <span class="badge px-2 py-1 rounded-3" style="background: rgba(var(--success-color-rgb), 0.15); color: var(--success-color); font-weight: 600; font-size: 0.85rem;">
                                                    📈 <?php echo e(ucfirst($item->tipe)); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="badge px-2 py-1 rounded-3" style="background: rgba(var(--danger-color-rgb), 0.15); color: var(--danger-color); font-weight: 600; font-size: 0.85rem;">
                                                    📉 <?php echo e(ucfirst($item->tipe)); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Description -->
                                <?php if($item->keterangan): ?>
                                <div class="mb-3">
                                    <small class="text-muted fw-bold d-block mb-1">KETERANGAN</small>
                                    <p class="small mb-0" style="color: var(--text-secondary); line-height: 1.5;"><?php echo e($item->keterangan); ?></p>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Action Buttons -->
                                <div class="d-flex gap-2">
                                    <a href="<?php echo e(route('kriteria.edit', $item->id)); ?>" class="btn btn-sm rounded-3 flex-fill" style="background: rgba(var(--warning-color-rgb), 0.15); color: var(--warning-color); border: 1px solid rgba(var(--warning-color-rgb), 0.3); font-weight: 600;">
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </a>
                                    <button type="button" class="btn btn-sm rounded-3 flex-fill" 
                                            onclick="confirmDelete('<?php echo e($item->id); ?>', '<?php echo e(addslashes($item->nama_kriteria)); ?>')"
                                            style="background: rgba(var(--danger-color-rgb), 0.15); color: var(--danger-color); border: 1px solid rgba(var(--danger-color-rgb), 0.3); font-weight: 600;">
                                        <i class="bi bi-trash me-1"></i>Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                
                <!-- Desktop Table View (Hidden on mobile) -->
                <div class="d-none d-lg-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="kriteriaTable">
                            <thead style="background: rgba(248, 249, 250, 0.8); backdrop-filter: blur(5px);">
                                <tr>
                                    <th class="border-0 px-4 py-3" style="border-radius: 0;">
                                        <small class="text-uppercase fw-bold" style="color: var(--text-muted); letter-spacing: 0.5px;">No</small>
                                    </th>
                                    <th class="border-0 py-3 sortable" data-sort="tipe_bisnis" style="cursor: pointer; transition: all 0.2s ease;">
                                        <small class="text-uppercase fw-bold d-flex align-items-center" style="color: var(--text-muted); letter-spacing: 0.5px;">
                                            Tipe Bisnis
                                            <i class="bi bi-arrow-down-up ms-2 sort-icon" style="opacity: 0.6;"></i>
                                        </small>
                                    </th>
                                    <th class="border-0 py-3 sortable" data-sort="nama_kriteria" style="cursor: pointer; transition: all 0.2s ease;">
                                        <small class="text-uppercase fw-bold d-flex align-items-center" style="color: var(--text-muted); letter-spacing: 0.5px;">
                                            Nama Kriteria
                                            <i class="bi bi-arrow-down-up ms-2 sort-icon" style="opacity: 0.6;"></i>
                                        </small>
                                    </th>
                                    <th class="border-0 py-3 text-center sortable" data-sort="bobot" style="cursor: pointer; transition: all 0.2s ease;">
                                        <small class="text-uppercase fw-bold d-flex align-items-center justify-content-center" style="color: var(--text-muted); letter-spacing: 0.5px;">
                                            Bobot
                                            <i class="bi bi-arrow-down-up ms-2 sort-icon" style="opacity: 0.6;"></i>
                                        </small>
                                    </th>
                                    <th class="border-0 py-3 text-center sortable" data-sort="tipe" style="cursor: pointer; transition: all 0.2s ease;">
                                        <small class="text-uppercase fw-bold d-flex align-items-center justify-content-center" style="color: var(--text-muted); letter-spacing: 0.5px;">
                                            Tipe
                                            <i class="bi bi-arrow-down-up ms-2 sort-icon" style="opacity: 0.6;"></i>
                                        </small>
                                    </th>
                                    <th class="border-0 py-3">
                                        <small class="text-uppercase fw-bold" style="color: var(--text-muted); letter-spacing: 0.5px;">Keterangan</small>
                                    </th>
                                    <th class="border-0 py-3 text-center">
                                        <small class="text-uppercase fw-bold" style="color: var(--text-muted); letter-spacing: 0.5px;">Aksi</small>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $kriteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr data-tipe-bisnis="<?php echo e($item->tipe_bisnis); ?>" data-bobot="<?php echo e($item->bobot); ?>" data-tipe="<?php echo e(strtolower($item->tipe)); ?>" style="transition: all 0.2s ease;">
                                    <td class="px-4">
                                        <div class="badge bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; color: var(--primary-color); font-weight: 600;"><?php echo e($index + 1); ?></div>
                                    </td>
                                    <td>
                                        <?php if($item->tipe_bisnis == 'kontrakan'): ?>
                                            <span class="badge px-3 py-2 rounded-3" style="background: rgba(var(--primary-color-rgb), 0.15); color: var(--primary-color); font-weight: 600;">
                                                🏠 Kontrakan
                                            </span>
                                        <?php else: ?>
                                            <span class="badge px-3 py-2 rounded-3" style="background: rgba(var(--success-color-rgb), 0.15); color: var(--success-color); font-weight: 600;">
                                                👕 Laundry
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm d-flex align-items-center justify-content-center me-3 rounded-3" style="width: 42px; height: 42px; background: rgba(var(--primary-color-rgb), 0.1); color: var(--primary-color);">
                                                <i class="bi bi-star-fill" style="font-size: 1.2rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-bold" style="color: var(--text-primary); font-size: 0.95rem;"><?php echo e($item->nama_kriteria); ?></h6>
                                                <small style="color: var(--text-muted);">Kriteria <?php echo e($index + 1); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge px-3 py-2 rounded-3 mb-1" style="background: rgba(var(--info-color-rgb), 0.15); color: var(--info-color); font-weight: 700; font-size: 1rem;"><?php echo e($item->bobot); ?></span>
                                            <small style="color: var(--text-muted); font-size: 0.75rem;">(<?php echo e(number_format($item->bobot * 100, 1)); ?>%)</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if(strtolower($item->tipe) == 'benefit'): ?>
                                            <span class="badge px-3 py-2 rounded-3" style="background: rgba(var(--success-color-rgb), 0.15); color: var(--success-color); font-weight: 600;">
                                                <i class="bi bi-arrow-up-circle me-1"></i>📈 Benefit
                                            </span>
                                        <?php else: ?>
                                            <span class="badge px-3 py-2 rounded-3" style="background: rgba(var(--danger-color-rgb), 0.15); color: var(--danger-color); font-weight: 600;">
                                                <i class="bi bi-arrow-down-circle me-1"></i>📉 Cost
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="max-width: 200px;">
                                            <small style="color: var(--text-secondary); line-height: 1.4;">
                                                <?php echo e($item->keterangan ? Str::limit($item->keterangan, 60) : '-'); ?>

                                            </small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('kriteria.edit', $item->id)); ?>" 
                                               class="btn btn-sm rounded-3 me-2" 
                                               style="background: rgba(var(--warning-color-rgb), 0.15); color: var(--warning-color); border: 1px solid rgba(var(--warning-color-rgb), 0.3); font-weight: 600; transition: all 0.2s ease;"
                                               data-bs-toggle="tooltip" title="Edit Kriteria"
                                               onmouseover="this.style.background='var(--warning-color)'; this.style.color='white';"
                                               onmouseout="this.style.background='rgba(var(--warning-color-rgb), 0.15)'; this.style.color='var(--warning-color)';">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            
                                            <?php if(auth()->user()->role == 'super_admin'): ?>
                                            <button type="button" 
                                                    onclick="confirmDelete('<?php echo e($item->id); ?>', '<?php echo e(addslashes($item->nama_kriteria)); ?>')"
                                                    class="btn btn-sm rounded-3" 
                                                    style="background: rgba(var(--danger-color-rgb), 0.15); color: var(--danger-color); border: 1px solid rgba(var(--danger-color-rgb), 0.3); font-weight: 600; transition: all 0.2s ease;"
                                                    data-bs-toggle="tooltip" title="Hapus Kriteria"
                                                    onmouseover="this.style.background='var(--danger-color)'; this.style.color='white';"
                                                    onmouseout="this.style.background='rgba(var(--danger-color-rgb), 0.15)'; this.style.color='var(--danger-color)';">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php else: ?>
                                            <button type="button" 
                                                    class="btn btn-sm rounded-3" 
                                                    disabled
                                                    style="background: rgba(108, 117, 125, 0.15); color: #6c757d; border: 1px solid rgba(108, 117, 125, 0.3);"
                                                    data-bs-toggle="tooltip" title="Hanya Super Admin yang dapat menghapus">
                                                <i class="bi bi-lock"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            
                            <!-- Enhanced Table Footer dengan Validasi Bobot -->
                            <tfoot style="background: rgba(248, 249, 250, 0.9); backdrop-filter: blur(5px); border-top: 2px solid rgba(var(--primary-color-rgb), 0.1);">
                                <tr>
                                    <td colspan="3" class="px-4 py-4">
                                        <div class="d-flex flex-column gap-3">
                                            <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">📊 Validasi Total Bobot</h6>
                                            <div class="d-flex gap-4">
                                                <!-- Bobot Kontrakan -->
                                                <span id="labelBobotKontrakan" class="d-flex align-items-center">
                                                    <span class="badge px-3 py-2 rounded-3 me-2" style="background: rgba(var(--primary-color-rgb), 0.15); color: var(--primary-color); font-weight: 700;">🏠</span>
                                                    <strong style="color: var(--text-primary);">Kontrakan:</strong>
                                                    <span class="ms-2 badge px-2 py-1 rounded-3" id="totalBobotKontrakan" style="font-weight: 700;"></span>
                                                </span>
                                                
                                                <!-- Bobot Laundry -->
                                                <span id="labelBobotLaundry" class="d-flex align-items-center">
                                                    <span class="badge px-3 py-2 rounded-3 me-2" style="background: rgba(var(--success-color-rgb), 0.15); color: var(--success-color); font-weight: 700;">👕</span>
                                                    <strong style="color: var(--text-primary);">Laundry:</strong>
                                                    <span class="ms-2 badge px-2 py-1 rounded-3" id="totalBobotLaundry" style="font-weight: 700;"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td colspan="4" class="py-4 text-end">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <div class="p-3 rounded-3" style="background: rgba(var(--info-color-rgb), 0.1); border: 1px solid rgba(var(--info-color-rgb), 0.2);">
                                                <small style="color: var(--text-muted); line-height: 1.5;">
                                                    <i class="bi bi-info-circle me-1" style="color: var(--info-color);"></i>
                                                    <strong>Catatan:</strong> Total bobot untuk setiap tipe bisnis harus <span class="fw-bold" style="color: var(--success-color);">= 1.00 (100%)</span><br>
                                                    agar perhitungan SAW (Simple Additive Weighting) memberikan hasil yang akurat.
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                                            Total Bobot Kontrakan: 
                                            <?php if(isset($bobotKontrakanValid) && $bobotKontrakanValid): ?>
                                                <span class="badge bg-success" data-bs-toggle="tooltip" title="Total bobot sudah sesuai (1.00)">
                                                    <i class="bi bi-check-circle me-1"></i><?php echo e(number_format($bobotKontrakan ?? 0, 2)); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger" data-bs-toggle="tooltip" title="Total bobot harus = 1.00">
                                                    <i class="bi bi-exclamation-triangle me-1"></i><?php echo e(number_format($bobotKontrakan ?? 0, 2)); ?>

                                                </span>
                                            <?php endif; ?>
                                        </span>
                                        
                                        <!-- ✅ VALIDASI BOBOT LAUNDRY -->
                                        <span id="labelBobotLaundry">
                                            Total Bobot Laundry: 
                                            <?php if(isset($bobotLaundryValid) && $bobotLaundryValid): ?>
                                                <span class="badge bg-success" data-bs-toggle="tooltip" title="Total bobot sudah sesuai (1.00)">
                                                    <i class="bi bi-check-circle me-1"></i><?php echo e(number_format($bobotLaundry ?? 0, 2)); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger" data-bs-toggle="tooltip" title="Total bobot harus = 1.00">
                                                    <i class="bi bi-exclamation-triangle me-1"></i><?php echo e(number_format($bobotLaundry ?? 0, 2)); ?>

                                                </span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center py-3">
                                    <span class="badge bg-info px-3 py-2 fs-6" id="totalBobotKeseluruhan">
                                        Total: <span id="totalBobotValue"><?php echo e(number_format($kriteria->sum('bobot'), 2)); ?></span>
                                    </span>
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Enhanced Info Cards -->
    <?php if($kriteria->count() > 0): ?>
    <div class="row mt-4 g-4">
        <div class="col-lg-6">
            <div class="glass-card border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-sm d-flex align-items-center justify-content-center me-3 rounded-3" style="background: rgba(var(--info-color-rgb), 0.15); color: var(--info-color);">
                            <i class="bi bi-info-circle-fill" style="font-size: 1.2rem;"></i>
                        </div>
                        <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">📋 Informasi Kriteria SAW</h6>
                    </div>
                    <div class="ps-3 border-start border-3" style="border-color: var(--info-color) !important;">
                        <ul class="mb-0 small" style="color: var(--text-secondary); line-height: 1.6;">
                            <li class="mb-2"><strong style="color: var(--success-color);">📈 Benefit:</strong> Semakin tinggi nilai, semakin baik <br><small class="text-muted">(contoh: Fasilitas, Kualitas Layanan)</small></li>
                            <li class="mb-2"><strong style="color: var(--danger-color);">📉 Cost:</strong> Semakin rendah nilai, semakin baik <br><small class="text-muted">(contoh: Harga, Jarak, Biaya)</small></li>
                            <li><strong style="color: var(--warning-color);">⚖️ Total Bobot:</strong> Harus = 1.00 (100%) untuk setiap tipe bisnis <br><small class="text-muted">agar perhitungan SAW akurat dan valid</small></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="glass-card border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-sm d-flex align-items-center justify-content-center me-3 rounded-3" style="background: rgba(var(--success-color-rgb), 0.15); color: var(--success-color);">
                            <i class="bi bi-graph-up-arrow" style="font-size: 1.2rem;"></i>
                        </div>
                        <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">📊 Statistik Kriteria</h6>
                    </div>
                    <div class="row text-center g-3">
                        <div class="col-4">
                            <div class="p-3 rounded-3" style="background: rgba(var(--primary-color-rgb), 0.1);">
                                <div class="fw-bold fs-4" style="color: var(--primary-color);" id="statTotalKriteria"><?php echo e($kriteria->count()); ?></div>
                                <small style="color: var(--text-muted); font-weight: 600;">Total Kriteria</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded-3" style="background: rgba(var(--success-color-rgb), 0.1);">
                                <div class="fw-bold fs-4" style="color: var(--success-color);" id="statBenefit"><?php echo e($totalBenefit ?? 0); ?></div>
                                <small style="color: var(--text-muted); font-weight: 600;">Benefit</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded-3" style="background: rgba(var(--danger-color-rgb), 0.1);">
                                <div class="fw-bold fs-4" style="color: var(--danger-color);" id="statCost"><?php echo e($totalCost ?? 0); ?></div>
                                <small style="color: var(--text-muted); font-weight: 600;">Cost</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Enhanced Bootstrap Modal Delete Confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 1rem; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header border-0 py-4" style="background: linear-gradient(135deg, var(--danger-color), #e74c3c); color: white;">
                <h5 class="modal-title fw-bold d-flex align-items-center" id="deleteModalLabel">
                    <div class="avatar-sm d-flex align-items-center justify-content-center me-3 rounded-circle" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(5px);">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.2rem;"></i>
                    </div>
                    Konfirmasi Hapus Kriteria
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, rgba(var(--danger-color-rgb), 0.1), rgba(231, 76, 60, 0.1)); animation: pulse 2s infinite;">
                        <i class="bi bi-trash" style="font-size: 2.5rem; color: var(--danger-color);"></i>
                    </div>
                    <h6 class="fw-bold mb-2" style="color: var(--text-primary);">Apakah Anda yakin?</h6>
                </div>
                <p class="text-center mb-3" style="color: var(--text-secondary); line-height: 1.6;">
                    Kriteria <strong id="deleteKriteriaName" style="color: var(--danger-color);"></strong> akan dihapus secara permanen.
                </p>
                <div class="alert border-0 rounded-3 mb-0" style="background: rgba(var(--warning-color-rgb), 0.1); color: var(--warning-color);">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-info-circle-fill me-2 mt-1" style="font-size: 1rem;"></i>
                        <small style="line-height: 1.5;">
                            <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan. Data kriteria yang dihapus akan hilang selamanya dan dapat mempengaruhi perhitungan SAW.
                        </small>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
                <div class="d-flex gap-3 w-100">
                    <button type="button" class="btn btn-light border rounded-3 flex-fill py-2" data-bs-dismiss="modal" style="font-weight: 600;">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </button>
                    <button type="button" class="btn rounded-3 flex-fill py-2" id="confirmDeleteBtn" 
                            style="background: linear-gradient(135deg, var(--danger-color), #e74c3c); color: white; border: none; font-weight: 600;">
                        <i class="bi bi-trash me-2"></i>Ya, Hapus Kriteria
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Enhanced Search, Filter & Sorting Script with Mobile Optimization -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('kriteriaTable');
    const mobileCards = document.querySelectorAll('.d-block.d-lg-none .card');
    const sortableHeaders = document.querySelectorAll('.sortable');
    
    // ========== ENHANCED MOBILE INTERACTIONS ==========
    // Touch animations for mobile cards
    mobileCards.forEach(card => {
        // Touch start effect
        card.addEventListener('touchstart', function() {
            this.style.transition = 'all 0.1s ease';
            this.style.transform = 'scale(0.98)';
            this.style.opacity = '0.9';
        });
        
        // Touch end effect
        card.addEventListener('touchend', function() {
            this.style.transition = 'all 0.3s ease';
            this.style.transform = 'scale(1)';
            this.style.opacity = '1';
        });
        
        // Cancel touch effect if moved
        card.addEventListener('touchcancel', function() {
            this.style.transition = 'all 0.3s ease';
            this.style.transform = 'scale(1)';
            this.style.opacity = '1';
        });
    });
    
    // ========== ENHANCED SORTING FUNCTIONALITY ==========
    let currentSortBy = '<?php echo e($sortBy ?? ''); ?>';
    let currentSortOrder = '<?php echo e($sortOrder ?? 'asc'); ?>';
    
    // Update sort icons dengan enhanced styling
    updateSortIcons();
    
    function updateSortIcons() {
        sortableHeaders.forEach(header => {
            const sortField = header.getAttribute('data-sort');
            const icon = header.querySelector('.sort-icon');
            
            if (sortField === currentSortBy) {
                // Active header dengan enhanced styling
                if (currentSortOrder === 'asc') {
                    icon.className = 'bi bi-sort-up ms-2 sort-icon';
                    icon.style.color = 'var(--primary-color)';
                } else {
                    icon.className = 'bi bi-sort-down ms-2 sort-icon';
                    icon.style.color = 'var(--primary-color)';
                }
                header.style.color = 'var(--primary-color)';
                header.style.fontWeight = '700';
            } else {
                // Inactive header
                icon.className = 'bi bi-arrow-down-up ms-2 sort-icon';
                icon.style.color = '';
                header.style.color = '';
                header.style.fontWeight = '';
            }
        });
    }
    
    // Enhanced sorting dengan feedback visual
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            // Loading effect
            const icon = this.querySelector('.sort-icon');
            const originalClass = icon.className;
            icon.className = 'bi bi-arrow-clockwise ms-2 sort-icon';
            icon.style.animation = 'spin 0.5s linear';
            
            const sortField = this.getAttribute('data-sort');
            
            // Toggle order logic
            if (currentSortBy === sortField) {
                currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                currentSortBy = sortField;
                currentSortOrder = 'asc';
            }
            
            // Update hidden inputs
            document.getElementById('hiddenSortBy').value = currentSortBy;
            document.getElementById('hiddenSortOrder').value = currentSortOrder;
            
            // Submit dengan delay untuk animation
            setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 300);
        });
        
        // Enhanced hover effects
        header.addEventListener('mouseenter', function() {
            if (this.style.color !== 'var(--primary-color)') {
                this.style.background = 'rgba(var(--primary-color-rgb), 0.05)';
                this.style.color = 'var(--primary-color)';
                this.style.transition = 'all 0.2s ease';
            }
        });
        
        header.addEventListener('mouseleave', function() {
            if (this.getAttribute('data-sort') !== currentSortBy) {
                this.style.background = '';
                this.style.color = '';
            }
        });
    });
    
    // ========== ENHANCED SEARCH FUNCTIONALITY ==========
    function filterTable() {
        if (!table) return;
        
        const searchTerm = searchInput.value.toLowerCase().trim();
        
        // Filter desktop table
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        let visibleCount = 0;
        
        Array.from(rows).forEach(row => {
            const text = row.textContent.toLowerCase();
            const matchSearch = !searchTerm || text.includes(searchTerm);
            
            if (matchSearch) {
                row.style.display = '';
                row.style.animation = 'fadeIn 0.3s ease';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Filter mobile cards
        mobileCards.forEach(card => {
            const text = card.textContent.toLowerCase();
            const matchSearch = !searchTerm || text.includes(searchTerm);
            
            if (matchSearch) {
                card.style.display = '';
                card.style.animation = 'fadeIn 0.3s ease';
            } else {
                card.style.display = 'none';
            }
        });
        
        // Update nomor urut untuk desktop
        let visibleIndex = 1;
        Array.from(rows).forEach(row => {
            if (row.style.display !== 'none') {
                const badge = row.querySelector('.badge.bg-light');
                if (badge) {
                    badge.textContent = visibleIndex++;
                    badge.style.animation = 'pulse 0.3s ease';
                }
            }
        });
        
        // Update count badges
        updateCountBadges();
        
        // Show no results message if needed
        showNoResultsMessage(visibleCount === 0 && mobileCards.length === 0);
    }
    
    // Enhanced search dengan debounce untuk performance
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(filterTable, 150);
        });
        
        // Clear button untuk mobile
        if (this.value) {
            addClearButton();
        }
    }
    
    function addClearButton() {
        if (!document.querySelector('.search-clear-btn')) {
            const clearBtn = document.createElement('button');
            clearBtn.type = 'button';
            clearBtn.className = 'btn btn-sm search-clear-btn';
            clearBtn.innerHTML = '<i class="bi bi-x"></i>';
            clearBtn.style.cssText = 'position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 10; background: none; border: none; color: var(--text-muted);';
            
            const inputGroup = searchInput.closest('.input-group');
            inputGroup.style.position = 'relative';
            inputGroup.appendChild(clearBtn);
            
            clearBtn.addEventListener('click', () => {
                searchInput.value = '';
                filterTable();
                clearBtn.remove();
                searchInput.focus();
            });
        }
    }
    
    function updateCountBadges() {
        const kontrakanCount = Array.from(document.querySelectorAll('[data-tipe-bisnis="kontrakan"]')).filter(el => el.style.display !== 'none').length;
        const laundryCount = Array.from(document.querySelectorAll('[data-tipe-bisnis="laundry"]')).filter(el => el.style.display !== 'none').length;
        
        const kontrakanBadge = document.getElementById('countKontrakan');
        const laundryBadge = document.getElementById('countLaundry');
        
        if (kontrakanBadge) kontrakanBadge.textContent = kontrakanCount;
        if (laundryBadge) laundryBadge.textContent = laundryCount;
    }
    
    function showNoResultsMessage(show) {
        let noResultsDiv = document.getElementById('noResultsMessage');
        
        if (show && !noResultsDiv) {
            noResultsDiv = document.createElement('div');
            noResultsDiv.id = 'noResultsMessage';
            noResultsDiv.className = 'text-center py-5';
            noResultsDiv.innerHTML = `
                <div class="mb-3">
                    <i class="bi bi-search text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
                <h6 class="text-muted mb-2">Tidak ada kriteria ditemukan</h6>
                <small class="text-muted">Coba ubah kata kunci pencarian Anda</small>
            `;
            
            const cardBody = document.querySelector('.card-body');
            if (cardBody) cardBody.appendChild(noResultsDiv);
        } else if (!show && noResultsDiv) {
            noResultsDiv.remove();
        }
    }
    
    // ========== ENHANCED TOOLTIPS ==========
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            animation: true,
            delay: { show: 300, hide: 100 }
        });
    });
    
    // ========== ENHANCED BOBOT VALIDATION ==========
    function updateBobotValidation() {
        // Hanya ambil rows dari desktop table (visible) atau mobile cards
        const isMobile = window.innerWidth < 992; // Bootstrap lg breakpoint
        
        let kontrakanRows, laundryRows;
        
        if (isMobile) {
            // Mobile view: ambil dari card
            kontrakanRows = document.querySelectorAll('.d-block.d-lg-none [data-tipe-bisnis="kontrakan"]');
            laundryRows = document.querySelectorAll('.d-block.d-lg-none [data-tipe-bisnis="laundry"]');
        } else {
            // Desktop view: ambil dari table tbody
            kontrakanRows = document.querySelectorAll('table tbody tr[data-tipe-bisnis="kontrakan"]');
            laundryRows = document.querySelectorAll('table tbody tr[data-tipe-bisnis="laundry"]');
        }
        
        let totalKontrakan = 0;
        let totalLaundry = 0;
        
        kontrakanRows.forEach(row => {
            const bobot = parseFloat(row.getAttribute('data-bobot') || 0);
            totalKontrakan += bobot;
        });
        
        laundryRows.forEach(row => {
            const bobot = parseFloat(row.getAttribute('data-bobot') || 0);
            totalLaundry += bobot;
        });
        
        updateBobotDisplay('totalBobotKontrakan', totalKontrakan);
        updateBobotDisplay('totalBobotLaundry', totalLaundry);
    }
    
    function updateBobotDisplay(elementId, total) {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        const isValid = Math.abs(total - 1.00) < 0.01;
        const percentage = (total * 100).toFixed(1);
        
        if (isValid) {
            element.style.background = 'rgba(var(--success-color-rgb), 0.15)';
            element.style.color = 'var(--success-color)';
            element.innerHTML = `<i class="bi bi-check-circle me-1"></i>${total.toFixed(2)} (${percentage}%)`;
        } else {
            element.style.background = 'rgba(var(--danger-color-rgb), 0.15)';
            element.style.color = 'var(--danger-color)';
            element.innerHTML = `<i class="bi bi-exclamation-triangle me-1"></i>${total.toFixed(2)} (${percentage}%)`;
        }
    }
    
    // Initialize bobot validation
    updateBobotValidation();
});

// ========== ENHANCED DELETE CONFIRMATION ==========
function confirmDelete(id, namaKriteria) {
    // Animate button yang diklik
    const clickedButton = event.target.closest('button');
    if (clickedButton) {
        clickedButton.style.transform = 'scale(0.95)';
        setTimeout(() => {
            clickedButton.style.transform = 'scale(1)';
        }, 150);
    }
    
    document.getElementById('deleteKriteriaName').textContent = '"' + namaKriteria + '"';
    
    const modalElement = document.getElementById('deleteModal');
    const deleteModal = new bootstrap.Modal(modalElement, {
        backdrop: 'static',
        keyboard: false
    });
    
    deleteModal.show();
    
    // Setup delete action
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.onclick = function() {
        // Loading state
        const originalContent = this.innerHTML;
        this.innerHTML = '<i class="bi bi-arrow-clockwise me-2" style="animation: spin 1s linear infinite;"></i>Menghapus...';
        this.disabled = true;
        
        // Create and submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/kriteria/${id}`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        
        setTimeout(() => {
            form.submit();
        }, 800);
    };
}

// ========== CSS ANIMATIONS ==========
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    .glass-card-light {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .sortable:hover {
        background: rgba(var(--primary-color-rgb), 0.05) !important;
        cursor: pointer;
    }
    
    /* Mobile optimizations */
    @media (max-width: 768px) {
        .table-responsive {
            border-radius: 1rem;
        }
        
        .modal-dialog {
            margin: 1rem;
        }
        
        .glass-card {
            margin-left: -15px;
            margin-right: -15px;
            border-radius: 0 0 1rem 1rem;
        }
    }
`;
document.head.appendChild(style);
</script>

<style>
    .modal-content {
        border-radius: 12px;
        animation: modalFadeIn 0.3s ease;
    }
    
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    
    .modal-header.bg-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transition: all 0.2s ease;
    }
    
    .table tbody tr {
        transition: all 0.3s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
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

    .avatar-sm {
        flex-shrink: 0;
    }
    
    .form-select {
        cursor: pointer;
    }
    
    /* ========== SORTING STYLES ========== */
    .sortable {
        transition: all 0.2s ease;
        user-select: none;
        position: relative;
    }
    
    .sortable:hover {
        background-color: #f8f9fa !important;
    }
    
    .sortable:active {
        background-color: #e9ecef !important;
    }
    
    .sortable.text-primary {
        font-weight: 700;
    }
    
    .sort-icon {
        font-size: 0.8rem;
        transition: all 0.2s ease;
    }
    
    .sortable:hover .sort-icon {
        transform: scale(1.2);
    }
    
    .text-primary .sort-icon {
        animation: sortPulse 1.5s ease-in-out infinite;
    }
    
    @keyframes sortPulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.6;
        }
    }
    
    /* Sort indicator animation */
    @keyframes sortIndicator {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .sortable.text-primary::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, #0d6efd, transparent);
        animation: sortIndicator 0.3s ease;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TA\spk_kontrakan\resources\views/kriteria/index.blade.php ENDPATH**/ ?>