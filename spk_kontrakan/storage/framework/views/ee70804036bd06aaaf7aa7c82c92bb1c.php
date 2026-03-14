

<?php $__env->startSection('title', 'Metode SAW'); ?>

<?php $__env->startSection('content'); ?>
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
        
        .header-saw {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
        }
        
        .header-saw h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .header-saw p {
            opacity: 0.95;
            margin-bottom: 0;
        }
        
        .section-header {
            color: #667eea;
            font-size: 1.1rem;
            font-weight: 700;
            padding-bottom: 1rem;
            border-bottom: 2px solid #667eea;
            margin-bottom: 1.5rem;
        }
        
        .form-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            overflow: hidden;
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
            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Metode SAW</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="header-saw mb-4">
        <h2 class="mb-0">🧮 Metode SAW</h2>
        <p class="mb-0 opacity-95">Sistem Pendukung Keputusan untuk rekomendasi terbaik</p>
    </div>

    <!-- Alert -->
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <span><?php echo e(session('error')); ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Info Alert -->
    <div class="alert alert-info border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border-left: 4px solid #667eea;">
        <div class="d-flex">
            <div class="me-3">
                <i class="bi bi-info-circle fs-5" style="color: #667eea;"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-2" style="color: #667eea;">Tentang Metode SAW</h6>
                <p class="mb-0">
                    Metode SAW digunakan untuk menentukan ranking alternatif terbaik berdasarkan kriteria yang telah ditentukan. 
                    Sistem akan menghitung nilai normalisasi dan bobot untuk setiap kriteria, kemudian menghasilkan peringkat rekomendasi.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <!-- Choose Type Card -->
            <div class="card form-card mb-4">
                <div class="card-body p-4">
                    <h5 class="section-header mb-0">
                        <i class="bi bi-list-ul me-2" style="color: #667eea;"></i>Pilih Jenis Pencarian
                    </h5>

                    <form action="<?php echo e(route('saw.proses')); ?>" method="POST" id="sawForm">
                        <?php echo csrf_field(); ?>
                        
                        <!-- Hidden inputs untuk koordinat user (HANYA UNTUK LAUNDRY) -->
                        <input type="hidden" name="user_lat" id="user_lat">
                        <input type="hidden" name="user_lng" id="user_lng">
                        
                        <div class="row g-2 g-md-3 mb-3 mb-md-4">
                            <!-- Pilih Kontrakan -->
                            <div class="col-12 col-md-6">
                                <input type="radio" class="btn-check" name="tipe" id="tipe_kontrakan" value="kontrakan" checked>
                                <label class="btn btn-outline-warning w-100 py-3 py-md-4" for="tipe_kontrakan" style="border-color: #667eea; color: #667eea;">
                                    <i class="bi bi-building fs-2 fs-md-1 d-block mb-2 mb-md-3"></i>
                                    <h5 class="mb-1 mb-md-2 fs-6 fs-md-5">Kontrakan</h5>
                                    <small class="text-muted">Cari kontrakan deket kampus</small>
                                </label>
                            </div>

                            <!-- Pilih Laundry -->
                            <div class="col-12 col-md-6">
                                <input type="radio" class="btn-check" name="tipe" id="tipe_laundry" value="laundry">
                                <label class="btn btn-outline-warning w-100 py-3 py-md-4" for="tipe_laundry" style="border-color: #667eea; color: #667eea;">
                                    <i class="bi bi-basket3 fs-2 fs-md-1 d-block mb-2 mb-md-3"></i>
                                    <h5 class="mb-1 mb-md-2 fs-6 fs-md-5">Laundry</h5>
                                    <small class="text-muted">Cari laundry deket lokasi Anda</small>
                                </label>
                            </div>
                        </div>

                        <!-- INFO JARAK KONTRAKAN -->
                        <div class="alert border-0 mb-3 mb-md-4" id="infoKontrakan" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border-left: 4px solid #667eea !important;">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-info-circle-fill fs-5 me-2 mt-1" style="color: #667eea;"></i>
                                <div>
                                    <strong class="small" style="color: #667eea;">Jarak Kontrakan</strong>
                                    <p class="mb-0 small mt-1">
                                        Jarak dihitung otomatis dari <strong>Kampus Polije</strong>. 
                                        Sistem akan merekomendasikan kontrakan terdekat dari kampus.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- DETEKSI LOKASI UNTUK LAUNDRY (OPSIONAL) -->
                        <div class="card form-card mb-4" id="deteksiLokasiCard" style="display: none;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start align-items-md-center mb-3">
                                    <i class="bi bi-geo-alt-fill fs-4 fs-md-3 me-2 me-md-3 mt-1 mt-md-0" style="color: #667eea;"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1 fs-6">Referensi Jarak Laundry</h6>
                                        <small class="text-muted d-block" style="font-size: 0.8rem;">
                                            Pilih titik referensi untuk menghitung jarak ke laundry
                                        </small>
                                    </div>
                                </div>

                                <!-- Pilihan Referensi Jarak -->
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="referensi_jarak" id="referensi_kampus" value="kampus" checked>
                                        <label class="btn btn-outline-warning w-100 py-2" for="referensi_kampus" style="border-color: #667eea; color: #667eea;">
                                            <i class="bi bi-building d-block fs-4 mb-1"></i>
                                            <strong class="small d-block">Dari Kampus</strong>
                                            <small class="text-muted" style="font-size: 0.7rem;">Default</small>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="referensi_jarak" id="referensi_user" value="user">
                                        <label class="btn btn-outline-warning w-100 py-2" for="referensi_user" style="border-color: #667eea; color: #667eea;">
                                            <i class="bi bi-person-pin d-block fs-4 mb-1"></i>
                                            <strong class="small d-block">Dari Lokasi Saya</strong>
                                            <small class="text-muted" style="font-size: 0.7rem;">GPS</small>
                                        </label>
                                    </div>
                                </div>

                                <!-- Info Default (Dari Kampus) -->
                                <div id="infoDefaultKampus" class="alert alert-info border-0 mb-0 p-2 small">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Jarak laundry akan dihitung dari <strong>Kampus Polije</strong>.
                                </div>

                                <!-- Tombol Deteksi Lokasi (muncul ketika pilih "Dari Lokasi Saya") -->
                                <div id="deteksiLokasiWrapper" style="display: none;">
                                    <button type="button" id="detectLocationBtn" class="btn btn-success w-100 mb-2">
                                        <i class="bi bi-crosshair me-2"></i>Deteksi Lokasi Saya Sekarang
                                    </button>
                                    
                                    <div id="locationStatus" class="mt-2" style="display: none;">
                                        <div class="alert alert-success mb-0 p-2 small">
                                            <i class="bi bi-check-circle-fill me-2"></i>
                                            <strong>Lokasi terdeteksi!</strong>
                                            <div class="mt-1" style="font-size: 0.75rem;" id="locationCoords"></div>
                                        </div>
                                    </div>
                                    
                                    <div id="locationWarning" class="alert alert-warning mb-0 p-2 small">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        <strong>Klik tombol di atas</strong> untuk mendeteksi lokasi Anda. 
                                        Jika tidak diklik, jarak akan dihitung dari kampus.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pilihan Jenis Layanan (khusus Laundry) -->
                        <div class="card form-card mb-4" id="jenisLayananCard" style="display: none;">
                            <div class="card-body p-4">
                                <h6 class="section-header mb-0">
                                    <i class="bi bi-speedometer2 me-2" style="color: #667eea;"></i>Pilih Jenis Layanan
                                </h6>
                                <div class="row g-2 g-md-3">
                                    <div class="col-12 col-md-4">
                                        <input type="radio" class="btn-check" name="jenis_layanan" id="layanan_reguler" value="reguler">
                                        <label class="btn btn-outline-warning w-100 py-2 py-md-3" for="layanan_reguler" style="border-color: #667eea; color: #667eea;">
                                            <i class="bi bi-clock fs-5 fs-md-4 d-block mb-2"></i>
                                            <strong class="small">Reguler</strong>
                                            <small class="d-block text-muted" style="font-size: 0.75rem;">Normal</small>
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <input type="radio" class="btn-check" name="jenis_layanan" id="layanan_express" value="express">
                                        <label class="btn btn-outline-warning w-100 py-2 py-md-3" for="layanan_express" style="border-color: #667eea; color: #667eea;">
                                            <i class="bi bi-lightning-charge fs-5 fs-md-4 d-block mb-2"></i>
                                            <strong class="small">Express</strong>
                                            <small class="d-block text-muted" style="font-size: 0.75rem;">Cepat</small>
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <input type="radio" class="btn-check" name="jenis_layanan" id="layanan_kilat" value="kilat">
                                        <label class="btn btn-outline-warning w-100 py-2 py-md-3" for="layanan_kilat" style="border-color: #667eea; color: #667eea;">
                                            <i class="bi bi-rocket-takeoff fs-5 fs-md-4 d-block mb-2"></i>
                                            <strong class="small">Kilat</strong>
                                            <small class="d-block text-muted" style="font-size: 0.75rem;">Super Cepat</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kriteria Info -->
                        <div class="card form-card mb-4">
                            <div class="card-body p-4">
                                <h6 class="section-header mb-0">
                                    <i class="bi bi-star me-2" style="color: #667eea;"></i>Kriteria yang Digunakan
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0 small" id="kriteriaTable">
                                        <thead>
                                            <tr>
                                                <th>Kriteria</th>
                                                <th class="text-center">Bobot</th>
                                                <th class="text-center">Tipe</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(isset($kriteria) && $kriteria->count() > 0): ?>
                                                <?php $__currentLoopData = $kriteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr data-tipe-bisnis="<?php echo e($k->tipe_bisnis ?? 'kontrakan'); ?>" data-bobot="<?php echo e($k->bobot ?? 0); ?>">
                                                    <td><?php echo e($k->nama_kriteria ?? 'N/A'); ?></td>
                                                    <td class="text-center">
                                                        <span class="badge" style="background-color: #667eea;"><?php echo e($k->bobot ?? 0); ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if(isset($k->tipe) && strtolower($k->tipe) == 'benefit'): ?>
                                                            <span class="badge bg-success">
                                                                <i class="bi bi-arrow-up-circle me-1"></i>Benefit
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">
                                                                <i class="bi bi-arrow-down-circle me-1"></i>Cost
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">Tidak ada kriteria yang tersedia</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                        <tfoot class="fw-bold">
                                            <tr>
                                                <td>Total Bobot</td>
                                                <td class="text-center">
                                                    <span class="badge" id="totalBobotDisplay" style="background-color: #667eea;">
                                                        <?php echo e(isset($kriteria) ? $kriteria->where('tipe_bisnis', 'kontrakan')->sum('bobot') : 0); ?>

                                                    </span>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-lg" id="btnProsesSAW" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600;">
                                <span id="btnText">
                                    <i class="bi bi-calculator me-2"></i>Proses Perhitungan SAW
                                </span>
                                <span id="btnLoading" style="display: none;">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Memproses data...
                                </span>
                            </button>
                        </div>

                        <!-- Loading Overlay -->
                        <div id="loadingOverlay" style="display: none;">
                            <div class="loading-content">
                                <div class="spinner-grow text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <h5 class="text-white mb-2">Memproses Perhitungan SAW</h5>
                                <p class="text-white-50 small">Mohon tunggu sebentar...</p>
                                <div class="progress mt-3" style="height: 4px; width: 250px; max-width: 90vw;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="row g-2 g-md-3">
                <div class="col-12 col-md-6">
                    <div class="card border-0 h-100" style="background: rgba(102, 126, 234, 0.1);">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-2 fs-6" style="color: #667eea;">
                                <i class="bi bi-check-circle me-2"></i>Kelebihan SAW
                            </h6>
                            <ul class="small mb-0" style="font-size: 0.85rem;">
                                <li>Mudah dipahami</li>
                                <li>Perhitungan sederhana</li>
                                <li>Hasil objektif</li>
                                <li>Jarak dihitung otomatis dari GPS</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card border-0 h-100" style="background: rgba(102, 126, 234, 0.1);">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-2 fs-6" style="color: #667eea;">
                                <i class="bi bi-lightbulb me-2"></i>Cara Kerja
                            </h6>
                            <ol class="small mb-0" style="font-size: 0.85rem;">
                                <li><strong>Kontrakan:</strong> Jarak dari Kampus Polije</li>
                                <li><strong>Laundry:</strong> Jarak dari Kampus <em>atau</em> Lokasi Anda</li>
                                <li>Normalisasi nilai kriteria</li>
                                <li>Kalikan dengan bobot</li>
                                <li>Urutkan berdasarkan nilai tertinggi</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toastContainer" style="position: fixed; top: 10px; right: 10px; z-index: 10000; max-width: 90vw;"></div>
<style>
    /* Purple Theme for Radio Buttons */
    .btn-check:checked + .btn-outline-warning {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border-color: #667eea !important;
        color: white !important;
    }
    
    .btn-check:checked + .btn-outline-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border-color: #667eea !important;
        color: white !important;
    }
    
    .btn-check:checked + .btn-outline-success {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border-color: #667eea !important;
        color: white !important;
    }
    
    .btn-check:checked + .btn-outline-danger {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border-color: #667eea !important;
        color: white !important;
    }
    
    .btn-outline-warning:hover,
    .btn-outline-primary:hover,
    .btn-outline-success:hover,
    .btn-outline-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.2);
        transition: all 0.3s ease;
    }
    
    #kriteriaTable tbody tr {
        transition: all 0.3s ease;
    }
    
    .bg-gradient {
        position: relative;
        overflow: hidden;
    }
    
    .bg-gradient::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 3s ease-in-out infinite;
        pointer-events: none;
        z-index: 0;
    }
    
    .bg-gradient .btn,
    .bg-gradient .card-body > * {
        position: relative;
        z-index: 1;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }
    
    /* Loading Overlay */
    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(5px);
    }
    
    .loading-content {
        text-align: center;
        animation: fadeInUp 0.5s ease;
        padding: 0 1rem;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Toast Notification */
    .custom-toast {
        min-width: 250px;
        max-width: 350px;
        margin-bottom: 10px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideInRight 0.3s ease;
        font-size: 0.9rem;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .custom-toast.hiding {
        animation: slideOutRight 0.3s ease;
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    /* Mobile optimizations */
    @media (max-width: 767px) {
        .btn-lg {
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.25em 0.5em;
        }
        
        #toastContainer {
            right: 5px;
            top: 5px;
        }
        
        .custom-toast {
            min-width: auto;
            width: calc(100vw - 20px);
            max-width: calc(100vw - 20px);
        }
    }
</style>
<script>
// Toast Notification Function
function showToast(type, message) {
    const container = document.getElementById('toastContainer');
    
    const toastId = 'toast_' + Date.now();
    const iconMap = {
        success: 'check-circle-fill',
        error: 'exclamation-triangle-fill',
        warning: 'exclamation-circle-fill',
        info: 'info-circle-fill'
    };
    
    const bgMap = {
        success: 'bg-success',
        error: 'bg-danger',
        warning: 'bg-warning',
        info: 'bg-info'
    };
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `custom-toast alert ${bgMap[type]} alert-dismissible fade show text-white`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-${iconMap[type]} me-2"></i>
            <div class="flex-grow-1 small">${message}</div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    container.appendChild(toast);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        toast.classList.add('hiding');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 4000);
}

// Main Script
window.addEventListener('DOMContentLoaded', function() {
    console.log('Script SAW loaded!');
    
    const radioKontrakan = document.getElementById('tipe_kontrakan');
    const radioLaundry = document.getElementById('tipe_laundry');
    const kriteriaTable = document.getElementById('kriteriaTable');
    const jenisLayananCard = document.getElementById('jenisLayananCard');
    const referensiJarakCard = document.getElementById('referensiJarakCard');
    const deteksiLokasiCard = document.getElementById('deteksiLokasiCard');
    const infoKontrakan = document.getElementById('infoKontrakan');
    const sawForm = document.getElementById('sawForm');
    const detectLocationBtn = document.getElementById('detectLocationBtn');
    const locationStatus = document.getElementById('locationStatus');
    const locationCoords = document.getElementById('locationCoords');
    const userLatInput = document.getElementById('user_lat');
    const userLngInput = document.getElementById('user_lng');
    const radioReferensiUser = document.getElementById('referensi_user');
    const radioReferensiKampus = document.getElementById('referensi_kampus');
    const deteksiLokasiWrapper = document.getElementById('deteksiLokasiWrapper');
    const infoDefaultKampus = document.getElementById('infoDefaultKampus');
    const locationWarning = document.getElementById('locationWarning');
    
    let locationDetected = false;
    
    // ========== TOGGLE REFERENSI JARAK (KAMPUS/USER) ==========
    function toggleReferensiJarak() {
        if (radioReferensiUser && radioReferensiUser.checked) {
            // User memilih "Dari Lokasi Saya"
            if (deteksiLokasiWrapper) deteksiLokasiWrapper.style.display = 'block';
            if (infoDefaultKampus) infoDefaultKampus.style.display = 'none';
            if (locationWarning) locationWarning.style.display = locationDetected ? 'none' : 'block';
        } else {
            // Default: "Dari Kampus"
            if (deteksiLokasiWrapper) deteksiLokasiWrapper.style.display = 'none';
            if (infoDefaultKampus) infoDefaultKampus.style.display = 'block';
            // Reset user location ketika pilih kampus
            if (userLatInput) userLatInput.value = '';
            if (userLngInput) userLngInput.value = '';
        }
    }
    
    // ========== TOGGLE DETEKSI LOKASI BERDASARKAN TIPE ==========
    function toggleDeteksiLokasi() {
        if (radioLaundry.checked) {
            // LAUNDRY: Tampilkan jenis layanan dan pilihan referensi jarak
            jenisLayananCard.style.display = 'block';
            deteksiLokasiCard.style.display = 'block';
            infoKontrakan.style.display = 'none';
            toggleReferensiJarak();
        } else {
            // KONTRAKAN: Sembunyikan jenis layanan dan deteksi lokasi
            jenisLayananCard.style.display = 'none';
            deteksiLokasiCard.style.display = 'none';
            infoKontrakan.style.display = 'block';
            // Reset user location untuk kontrakan
            if (userLatInput) userLatInput.value = '';
            if (userLngInput) userLngInput.value = '';
        }
    }
    
    // ========== EVENT LISTENER UNTUK PILIHAN REFERENSI JARAK ==========
    if (radioReferensiUser) {
        radioReferensiUser.addEventListener('change', toggleReferensiJarak);
    }
    if (radioReferensiKampus) {
        radioReferensiKampus.addEventListener('change', toggleReferensiJarak);
    }
    
    // ========== DETEKSI LOKASI (HANYA UNTUK LAUNDRY) ==========
    if (detectLocationBtn) {
        detectLocationBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Tombol deteksi lokasi diklik!');
            
            const button = this;
            const originalHTML = button.innerHTML;
            
            if (!navigator.geolocation) {
                showToast('error', 'Browser Anda tidak mendukung Geolocation.');
                return;
            }
            
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mendeteksi lokasi...';
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    console.log('Lokasi berhasil terdeteksi!', position);
                    
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    userLatInput.value = lat;
                    userLngInput.value = lng;
                    locationDetected = true;
                    
                    console.log('Lat:', lat, 'Lng:', lng);
                    
                    locationCoords.innerHTML = `
                        <strong>Latitude:</strong> ${lat.toFixed(6)}<br>
                        <strong>Longitude:</strong> ${lng.toFixed(6)}
                    `;
                    locationStatus.style.display = 'block';
                    if (locationWarning) locationWarning.style.display = 'none';
                    
                    button.disabled = false;
                    button.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Lokasi Terdeteksi!';
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-success');
                    
                    showToast('success', '📍 Lokasi Anda berhasil terdeteksi! Jarak laundry akan dihitung dari posisi Anda.');
                },
                function(error) {
                    console.error('Error geolocation:', error);
                    
                    button.disabled = false;
                    button.innerHTML = originalHTML;
                    
                    let errorMsg = '';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMsg = 'Izinkan akses lokasi di browser Anda.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMsg = 'Informasi lokasi tidak tersedia.';
                            break;
                        case error.TIMEOUT:
                            errorMsg = 'Waktu permintaan habis. Coba lagi.';
                            break;
                        default:
                            errorMsg = 'Terjadi kesalahan saat mendeteksi lokasi.';
                    }
                    showToast('error', errorMsg);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    }
    
    // ========== FILTER KRITERIA ==========
    function filterKriteria() {
        if (!kriteriaTable) return;
        
        const selectedTipe = radioKontrakan.checked ? 'kontrakan' : 'laundry';
        
        const rows = kriteriaTable.querySelectorAll('tbody tr');
        let totalBobot = 0;
        
        rows.forEach(row => {
            const tipeBisnis = row.getAttribute('data-tipe-bisnis');
            const bobot = parseFloat(row.getAttribute('data-bobot')) || 0;
            
            if (tipeBisnis === selectedTipe) {
                row.style.display = '';
                totalBobot += bobot;
            } else {
                row.style.display = 'none';
            }
        });
        
        const totalBobotDisplay = document.getElementById('totalBobotDisplay');
        if (totalBobotDisplay) {
            totalBobotDisplay.textContent = totalBobot.toFixed(2);
        }
    }
    
    // ========== VALIDASI FORM ==========
    if (sawForm) {
        sawForm.addEventListener('submit', function(e) {
            // Validasi jenis layanan untuk laundry
            if (radioLaundry.checked) {
                const jenisLayananChecked = document.querySelector('input[name="jenis_layanan"]:checked');
                if (!jenisLayananChecked) {
                    e.preventDefault();
                    showToast('warning', '⚠️ Pilih jenis layanan terlebih dahulu!');
                    return false;
                }
            }
            
            // Tampilkan loading
            const btnProsesSAW = document.getElementById('btnProsesSAW');
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');
            const loadingOverlay = document.getElementById('loadingOverlay');
            
            btnProsesSAW.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline-block';
            loadingOverlay.style.display = 'flex';
        });
    }
    
    // Event listeners
    if (radioKontrakan) {
        radioKontrakan.addEventListener('change', function() {
            toggleDeteksiLokasi();
            filterKriteria();
        });
    }
    
    if (radioLaundry) {
        radioLaundry.addEventListener('change', function() {
            toggleDeteksiLokasi();
            filterKriteria();
        });
    }
    
    // Initialize
    toggleDeteksiLokasi();
    filterKriteria();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TA\spk_kontrakan\resources\views/saw/index.blade.php ENDPATH**/ ?>