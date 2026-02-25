

<?php $__env->startSection('title', 'Tambah Kontrakan'); ?>

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
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .input-group-text {
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        
        .form-control:focus {
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
        
        .info-card {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border: 1px solid #667eea;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
    </style>
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('kontrakan.index')); ?>">Kontrakan</a></li>
            <li class="breadcrumb-item active">Tambah Data</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header mb-4">
        <h2 class="mb-2">
            <i class="bi bi-plus-circle me-2"></i>Tambah Kontrakan Baru
        </h2>
        <p class="mb-0 fs-6">Lengkapi formulir di bawah untuk menambahkan data kontrakan ke sistem</p>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-12">
            <div class="card form-card">
                <div class="card-body p-4">
                    <form action="<?php echo e(route('kontrakan.store')); ?>" method="POST" enctype="multipart/form-data" id="kontrakanForm">
                        <?php echo csrf_field(); ?>
                        
                        <!-- Informasi Dasar Section -->
                        <div class="mb-4">
                            <h5 class="section-header">
                                <i class="bi bi-info-circle me-2"></i>Informasi Dasar
                            </h5>
                            
                            <div class="row g-3">
                                <!-- Nama Kontrakan -->
                                <div class="col-md-12">
                                    <label for="nama" class="form-label fw-semibold">
                                        Nama Kontrakan <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-building" style="color: #667eea;"></i>
                                        </span>
                                        <input 
                                            type="text" 
                                            name="nama" 
                                            class="form-control border-start-0 <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="nama" 
                                            placeholder="Contoh: Kontrakan Melati Residence"
                                            value="<?php echo e(old('nama')); ?>"
                                            required
                                        >
                                        <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <small class="text-muted">Masukkan nama kontrakan yang mudah dikenali</small>
                                </div>

                                <!-- Alamat -->
                                <div class="col-md-12">
                                    <label for="alamat" class="form-label fw-semibold">
                                        Alamat Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 align-items-start pt-2">
                                            <i class="bi bi-geo-alt text-danger"></i>
                                        </span>
                                        <textarea 
                                            name="alamat" 
                                            class="form-control border-start-0 <?php $__errorArgs = ['alamat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="alamat" 
                                            rows="3"
                                            placeholder="Contoh: Jl. Kaliurang KM 5, Sleman, Yogyakarta"
                                            required
                                        ><?php echo e(old('alamat')); ?></textarea>
                                        <?php $__errorArgs = ['alamat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <small class="text-muted">Alamat lengkap beserta RT/RW jika ada. Alamat akan auto-update saat klik peta.</small>
                                </div>

                                <!-- WhatsApp -->
                                <div class="col-md-12">
                                    <label for="no_whatsapp" class="form-label fw-semibold">
                                        <i class="bi bi-whatsapp text-success"></i> Nomor WhatsApp Pemilik <span class="text-muted">(Opsional)</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-telephone text-success"></i>
                                        </span>
                                        <input 
                                            type="text" 
                                            name="no_whatsapp" 
                                            class="form-control border-start-0 <?php $__errorArgs = ['no_whatsapp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="no_whatsapp" 
                                            placeholder="Contoh: 081234567890"
                                            value="<?php echo e(old('no_whatsapp')); ?>"
                                            maxlength="20"
                                        >
                                        <?php $__errorArgs = ['no_whatsapp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Format: 08xxx atau 628xxx (untuk kemudahan calon penyewa menghubungi)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- ========== LOKASI & KOORDINAT SECTION - UPDATED! ========== -->
                        <div class="mb-4">
                            <h5 class="section-header">
                                <i class="bi bi-pin-map-fill me-2"></i>Lokasi & Koordinat
                            </h5>
                            
                            <!-- Alert Info - UPDATED -->
                            <div class="alert alert-info border-info">
                                    <div class="d-flex">
                                        <i class="bi bi-info-circle me-2 mt-1"></i>
                                        <div>
                                            <strong>⭐ Cara Menentukan Lokasi (PILIH SALAH SATU):</strong>
                                            <ul class="mb-0 mt-2 small">
                                                <li class="mb-2">
                                                    <strong class="text-success">🎯 REKOMENDASI #1: Klik di Peta</strong>
                                                    <div class="text-muted" style="font-size: 0.85rem;">
                                                        Klik pada peta di lokasi kontrakan → Alamat & koordinat <strong>otomatis terisi</strong><br>
                                                        <span class="badge bg-success bg-opacity-10 text-success mt-1">Paling Akurat & Mudah!</span>
                                                    </div>
                                                </li>
                                                <li class="mb-2">
                                                    <strong style="color: #667eea;">📍 Deteksi GPS</strong>
                                                    <div class="text-muted" style="font-size: 0.85rem;">
                                                        Klik "Deteksi Lokasi Saya" <strong>HANYA jika Anda sedang berada di lokasi kontrakan</strong>
                                                    </div>
                                                </li>
                                                <li class="mb-2">
                                                    <strong class="text-warning">⌨️ Input Manual Koordinat</strong>
                                                    <div class="text-muted" style="font-size: 0.85rem;">
                                                        Ketik latitude & longitude dari Google Maps → Marker otomatis pindah
                                                    </div>
                                                </li>
                                                <li>
                                                    <strong class="text-secondary">🔍 Cari dari Alamat</strong>
                                                    <div class="text-muted" style="font-size: 0.85rem;">
                                                        <span class="text-danger">⚠️ Hanya untuk alamat umum</span> (contoh: "Sumbersari, Jember")<br>
                                                        <span class="text-danger">❌ TIDAK untuk nama perumahan/kontrakan spesifik</span>
                                                    </div>
                                                </li>
                                            </ul>
                                            <div class="alert alert-warning bg-opacity-10 border-warning mt-2 mb-0 py-2 px-3">
                                                <small class="mb-0">
                                                    <strong>💡 Tips:</strong> Jika alamat berisi nama perumahan/gang (contoh: "Perumahan Melati, Gang Mawar"), 
                                                    <strong class="text-danger">gunakan cara KLIK PETA atau INPUT KOORDINAT manual</strong>, 
                                                    jangan gunakan tombol "Cari dari Alamat"
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <!-- Tombol Geocoding & Deteksi Lokasi -->
                            <div class="row g-2 mb-3">
                                <div class="col-12 col-md-6">
                                    <button type="button" id="geocodeBtn" class="btn btn-secondary w-100">
                                        <i class="bi bi-search me-2"></i>Cari dari Alamat Umum
                                    </button>
                                    <small class="text-danger d-block mt-1">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Hanya untuk alamat umum (Kota/Kecamatan), bukan nama perumahan!
                                    </small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <button type="button" id="detectLocation" class="btn w-100" style="border: 2px solid #667eea; color: #667eea; background: transparent;">
                                        <i class="bi bi-crosshair me-2"></i>Deteksi Lokasi Saya
                                    </button>
                                    <small class="text-muted d-block mt-1">
                                        <i class="bi bi-geo-alt me-1"></i>Gunakan GPS Anda (harus di lokasi)
                                    </small>
                                </div>
                            </div>

                            <div class="row g-3">
                                <!-- Latitude - UPDATED: Bisa Input Manual -->
                                <div class="col-md-6">
                                    <label for="latitude" class="form-label fw-semibold">
                                        Latitude <span class="text-danger">*</span>
                                        <span class="badge bg-warning bg-opacity-10 text-warning ms-2 small">
                                            <i class="bi bi-pencil me-1"></i>Bisa input manual
                                        </span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-geo text-danger"></i>
                                        </span>
                                        <input 
                                            type="number" 
                                            name="latitude" 
                                            class="form-control <?php $__errorArgs = ['latitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="latitude" 
                                            placeholder="-6.966667"
                                            value="<?php echo e(old('latitude')); ?>"
                                            step="0.00000001"
                                            min="-90"
                                            max="90"
                                            required
                                        >
                                        <?php $__errorArgs = ['latitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <small class="text-muted">Range: -90 sampai 90</small>
                                </div>

                                <!-- Longitude - UPDATED: Bisa Input Manual -->
                                <div class="col-md-6">
                                    <label for="longitude" class="form-label fw-semibold">
                                        Longitude <span class="text-danger">*</span>
                                        <span class="badge bg-warning bg-opacity-10 text-warning ms-2 small">
                                            <i class="bi bi-pencil me-1"></i>Bisa input manual
                                        </span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-geo text-danger"></i>
                                        </span>
                                        <input 
                                            type="number" 
                                            name="longitude" 
                                            class="form-control <?php $__errorArgs = ['longitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="longitude" 
                                            placeholder="110.416664"
                                            value="<?php echo e(old('longitude')); ?>"
                                            step="0.00000001"
                                            min="-180"
                                            max="180"
                                            required
                                        >
                                        <?php $__errorArgs = ['longitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <small class="text-muted">Range: -180 sampai 180</small>
                                </div>

                                <!-- Map Container -->
                                <div class="col-12">
                                    <div id="map" style="height: 400px; border-radius: 8px; border: 2px solid #dee2e6;"></div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-hand-index me-1"></i>Klik pada peta → alamat & koordinat otomatis terisi | Drag marker untuk ubah posisi
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Properti Section -->
                        <div class="mb-4">
                            <h5 class="section-header">
                                <i class="bi bi-house me-2"></i>Detail Properti
                            </h5>
                            
                            <div class="row g-3">
                                <!-- Harga -->
                                <div class="col-md-6">
                                    <label for="harga" class="form-label fw-semibold">
                                        Harga Sewa/Tahun <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-currency-dollar text-success"></i>
                                        </span>
                                        <input 
                                            type="number" 
                                            name="harga" 
                                            class="form-control border-start-0 <?php $__errorArgs = ['harga'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="harga" 
                                            placeholder="500000"
                                            value="<?php echo e(old('harga')); ?>"
                                            min="0"
                                            required
                                        >
                                        <?php $__errorArgs = ['harga'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <small class="text-muted">Harga dalam Rupiah per tahun</small>
                                    <div id="harga_preview" class="mt-1" style="display:none;">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.82rem; padding: 4px 10px;">
                                            <i class="bi bi-info-circle me-1"></i>
                                            <span id="harga_preview_text"></span>
                                        </span>
                                    </div>
                                </div>

                                <!-- jumlah_kamar -->
                                <div class="col-md-6">
                                    <label for="jumlah_kamar" class="form-label fw-semibold">
                                        Jumlah Kamar Tidur <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-door-closed" style="color: #667eea;"></i>
                                        </span>
                                        <input 
                                            type="number" 
                                            name="jumlah_kamar" 
                                            class="form-control border-start-0 <?php $__errorArgs = ['jumlah_kamar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="jumlah_kamar" 
                                            placeholder="Contoh: 3"
                                            value="<?php echo e(old('jumlah_kamar')); ?>"
                                            min="1"
                                            step="1"
                                            required
                                        >
                                        <span class="input-group-text bg-light">kamar</span>
                                        <?php $__errorArgs = ['jumlah_kamar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <small class="text-muted">Jumlah kamar tidur yang tersedia</small>
                                </div>
                                        <i class="bi bi-info-circle me-1"></i>
                                        Total kamar mandi (sistem akan otomatis menghitung kenyamanan)
                                    </small>
                                </div>

                                <!-- Jarak - AUTO CALCULATED! -->
                                <div class="col-md-6">
                                    <label for="jarak" class="form-label fw-semibold">
                                        Jarak ke Kampus <span class="text-danger">*</span>
                                        <span class="badge bg-info bg-opacity-10 text-info ms-2">
                                            <i class="bi bi-calculator me-1"></i>Auto-calculated
                                        </span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-pin-map text-info"></i>
                                        </span>
                                        <input 
                                            type="number" 
                                            name="jarak" 
                                            class="form-control border-start-0 <?php $__errorArgs = ['jarak'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="jarak" 
                                            placeholder="Otomatis dihitung"
                                            value="<?php echo e(old('jarak')); ?>"
                                            min="0"
                                            readonly
                                            required
                                        >
                                        <span class="input-group-text bg-light">meter</span>
                                        <?php $__errorArgs = ['jarak'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Jarak otomatis dihitung dari koordinat kontrakan ke kampus
                                    </small>
                                </div>

                                <!-- Fasilitas -->
                                <div class="col-md-6">
                                    <label for="fasilitas" class="form-label fw-semibold">
                                        Fasilitas
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-star text-warning"></i>
                                        </span>
                                        <input 
                                            type="text" 
                                            name="fasilitas" 
                                            class="form-control border-start-0 <?php $__errorArgs = ['fasilitas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="fasilitas" 
                                            placeholder="Contoh: WiFi, AC, Kasur, Lemari"
                                            value="<?php echo e(old('fasilitas')); ?>"
                                        >
                                        <?php $__errorArgs = ['fasilitas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <small class="text-muted">Pisahkan dengan koma (,) untuk fasilitas lebih dari satu</small>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Foto Section -->
                        <div class="mb-4">
                            <h5 class="section-header">
                                <i class="bi bi-camera me-2"></i>Upload Foto
                            </h5>
                            
                            <div class="row g-3">
                                <!-- Input Upload Foto -->
                                <div class="col-md-12">
                                    <label for="foto" class="form-label fw-semibold">
                                        Foto Kontrakan <span class="text-muted">(Opsional)</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-image" style="color: #667eea;"></i>
                                        </span>
                                        <input 
                                            type="file" 
                                            name="foto" 
                                            class="form-control border-start-0 <?php $__errorArgs = ['foto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="foto" 
                                            accept="image/jpeg,image/png,image/jpg"
                                        >
                                        <?php $__errorArgs = ['foto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <small class="text-muted">Format: JPG, PNG, JPEG. Maksimal 2MB</small>
                                </div>

                                <!-- Preview Foto -->
                                <div class="col-md-12">
                                    <div id="preview-foto-container" style="display: none;">
                                        <label class="form-label fw-semibold">Preview Foto:</label>
                                        <div class="border rounded p-2 bg-light position-relative">
                                            <img id="preview-foto" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 8px; display: block; margin: 0 auto;">
                                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage()">
                                                <i class="bi bi-x-circle me-1"></i>Hapus Foto
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="alert alert-light border mb-4" id="previewSection" style="display: none;">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-eye me-2" style="color: #667eea;"></i>Preview Data
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><small class="text-muted">Nama:</small> <span id="previewNama" class="fw-semibold">-</span></p>
                                    <p class="mb-1"><small class="text-muted">Harga:</small> <span id="previewHarga" class="fw-semibold">-</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><small class="text-muted">Kamar Tidur:</small> <span id="previewjumlah_kamar" class="fw-semibold">-</span></p>
                                    <p class="mb-1"><small class="text-muted">Kamar Mandi:</small> <span id="previewBathroomCount" class="fw-semibold">-</span></p>
                                    <p class="mb-1"><small class="text-muted">Jarak:</small> <span id="previewJarak" class="fw-semibold">-</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                            <a href="<?php echo e(route('kontrakan.index')); ?>" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-arrow-left me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-submit px-4">
                                <i class="bi bi-check-circle me-2"></i>Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="info-card">
                <div class="d-flex">
                    <div class="me-3">
                        <i class="bi bi-lightbulb text-warning" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-2">Tips Pengisian Data:</h6>
                            <ul class="mb-0 small">
                                <li class="mb-2">Pastikan semua data yang ditandai <span class="text-danger">(*)</span> wajib diisi</li>
                                <li class="mb-2"><strong class="text-success">CARA TERBAIK:</strong> Klik langsung pada peta untuk menentukan lokasi! Alamat akan otomatis terisi.</li>
                                <li class="mb-2"><strong class="text-warning">PENTING:</strong> Tombol "Cari dari Alamat" HANYA untuk alamat umum seperti "Jember" atau "Sumbersari", BUKAN untuk nama perumahan spesifik.</li>
                                <li class="mb-2"><strong>Alternatif:</strong> Copy koordinat dari Google Maps, lalu ketik manual di form (marker otomatis pindah)</li>
                                <li class="mb-2">Koordinat bisa input manual, marker otomatis pindah!</li>
                                <li class="mb-2">Jarak ke kampus akan otomatis dihitung setelah koordinat terisi</li>
                                <li>Nomor WhatsApp memudahkan calon penyewa untuk menghubungi</li>
                            </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== KOORDINAT KAMPUS POLIJE ==========
    const KAMPUS_LAT = -8.15981;
    const KAMPUS_LNG = 113.72312;
    
    // ========== MAP INITIALIZATION ==========
    const map = L.map('map').setView([KAMPUS_LAT, KAMPUS_LNG], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Marker kampus (optional - untuk referensi)
    const kampusIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="background: #dc3545; color: white; padding: 5px 10px; border-radius: 5px; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.3);">🏫 KAMPUS</div>',
        iconSize: [80, 30],
        iconAnchor: [40, 15]
    });
    L.marker([KAMPUS_LAT, KAMPUS_LNG], { icon: kampusIcon }).addTo(map);
    
    let marker;
    let isUpdatingFromInput = false; // Flag untuk prevent loop
    
    // ========== FUNCTION: REVERSE GEOCODING (KOORDINAT → ALAMAT) - BARU! ==========
    function reverseGeocode(lat, lng) {
        const reverseGeocodeURL = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;
        
        fetch(reverseGeocodeURL)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    document.getElementById('alamat').value = data.display_name;
                    console.log('✅ Alamat berhasil di-update:', data.display_name);
                }
            })
            .catch(error => {
                console.error('⚠️ Reverse geocoding error:', error);
            });
    }
    
    // ========== FUNCTION: ADD/UPDATE MARKER ==========
    function addMarker(lat, lng, shouldReverseGeocode = true) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);
            
            // Event: Drag marker
            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                updateCoordinates(position.lat, position.lng);
                calculateDistance(position.lat, position.lng);
                reverseGeocode(position.lat, position.lng); // Auto update alamat
            });
        }
        
        map.setView([lat, lng], 15);
        updateCoordinates(lat, lng);
        calculateDistance(lat, lng);
        
        // Reverse geocoding (koordinat → alamat)
        if (shouldReverseGeocode) {
            reverseGeocode(lat, lng);
        }
    }
    
    // ========== FUNCTION: UPDATE COORDINATES INPUT ==========
    function updateCoordinates(lat, lng) {
        if (!isUpdatingFromInput) {
            document.getElementById('latitude').value = lat.toFixed(8);
            document.getElementById('longitude').value = lng.toFixed(8);
        }
    }
    
    // ========== FUNCTION: VALIDATE COORDINATES - BARU! ==========
    function validateCoordinates(lat, lng) {
        lat = parseFloat(lat);
        lng = parseFloat(lng);
        
        if (isNaN(lat) || isNaN(lng)) {
            return { valid: false, message: '❌ Koordinat harus berupa angka!' };
        }
        
        if (lat < -90 || lat > 90) {
            return { valid: false, message: '❌ Latitude harus antara -90 sampai 90!' };
        }
        
        if (lng < -180 || lng > 180) {
            return { valid: false, message: '❌ Longitude harus antara -180 sampai 180!' };
        }
        
        return { valid: true, lat: lat, lng: lng };
    }
    
    // ========== FUNCTION: CALCULATE DISTANCE (HAVERSINE FORMULA) ==========
    function calculateDistance(lat, lng) {
        const R = 6371e3; // Earth radius in meters
        const φ1 = KAMPUS_LAT * Math.PI / 180;
        const φ2 = lat * Math.PI / 180;
        const Δφ = (lat - KAMPUS_LAT) * Math.PI / 180;
        const Δλ = (lng - KAMPUS_LNG) * Math.PI / 180;
        
        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                  Math.cos(φ1) * Math.cos(φ2) *
                  Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        
        const distance = Math.round(R * c); // Distance in meters
        document.getElementById('jarak').value = distance;
        
        // Update preview
        updatePreview();
    }
    
    // ========== INPUT MANUAL KOORDINAT → UPDATE PETA (FITUR BARU!) ==========
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    
    let inputTimeout;
    
    function handleCoordinateInput() {
        clearTimeout(inputTimeout);
        
        inputTimeout = setTimeout(() => {
            const lat = latInput.value;
            const lng = lngInput.value;
            
            if (lat && lng) {
                const validation = validateCoordinates(lat, lng);
                
                if (validation.valid) {
                    isUpdatingFromInput = true;
                    addMarker(validation.lat, validation.lng, true); // true = reverse geocode
                    isUpdatingFromInput = false;
                    
                    console.log('✅ Marker dipindahkan ke:', validation.lat, validation.lng);
                } else {
                    alert(validation.message);
                }
            }
        }, 800); // Delay 800ms untuk avoid spam
    }
    
    latInput.addEventListener('input', handleCoordinateInput);
    lngInput.addEventListener('input', handleCoordinateInput);
    
   // ========== GEOCODING: ALAMAT → KOORDINAT (IMPROVED!) ==========
document.getElementById('geocodeBtn').addEventListener('click', function() {
    const alamat = document.getElementById('alamat').value.trim();
    
    if (!alamat) {
        alert('⚠️ Mohon isi alamat lengkap terlebih dahulu!');
        document.getElementById('alamat').focus();
        return;
    }
    
    const button = this;
    const originalHTML = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mencari koordinat...';
    
    // Fungsi untuk mencari koordinat
    function tryGeocode(searchQuery) {
        const geocodeURL = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchQuery)}&limit=5&countrycodes=id`;
        
        return fetch(geocodeURL)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    // Filter hasil yang paling relevan (prioritaskan yang ada "jember")
                    let bestMatch = data[0];
                    const jemberMatch = data.find(item => 
                        item.display_name.toLowerCase().includes('jember')
                    );
                    if (jemberMatch) bestMatch = jemberMatch;
                    
                    return bestMatch;
                }
                return null;
            });
    }
    
    // STRATEGI 1: Cari dengan alamat lengkap
    tryGeocode(alamat)
        .then(result => {
            if (result) {
                return result;
            }
            
            // STRATEGI 2: Ambil kata kunci penting (kecamatan + kota)
            const parts = alamat.split(',').map(s => s.trim());
            if (parts.length >= 2) {
                // Coba: Kecamatan, Kota
                const simplifiedQuery = `${parts[parts.length - 2]}, ${parts[parts.length - 1]}`;
                return tryGeocode(simplifiedQuery);
            }
            return null;
        })
        .then(result => {
            if (result) {
                return result;
            }
            
            // STRATEGI 3: Cari hanya nama kota/kabupaten
            const cityMatch = alamat.match(/jember|sumbersari/i);
            if (cityMatch) {
                return tryGeocode('Jember, Jawa Timur, Indonesia');
            }
            return null;
        })
        .then(result => {
            button.disabled = false;
            button.innerHTML = originalHTML;
            
            if (result) {
                const lat = parseFloat(result.lat);
                const lng = parseFloat(result.lon);
                
                addMarker(lat, lng, false);
                
                // Success notification dengan info lengkap
                alert('✅ Koordinat berhasil ditemukan!\n\n' +
                      '📍 Lokasi ditemukan: ' + result.display_name + '\n' +
                      '🌍 Koordinat: ' + lat.toFixed(6) + ', ' + lng.toFixed(6) + '\n' +
                      '📏 Jarak ke kampus: ' + document.getElementById('jarak').value + ' meter\n\n' +
                      '💡 Anda bisa drag marker untuk penyesuaian lokasi yang lebih tepat!');
            } else {
                // Gagal semua strategi
                alert('❌ Alamat tidak ditemukan di peta!\n\n' +
                      '💡 Solusi Alternatif:\n\n' +
                      '1️⃣ CARA TERMUDAH: Klik langsung pada PETA di lokasi yang Anda inginkan\n' +
                      '   → Alamat akan otomatis terisi!\n\n' +
                      '2️⃣ Sederhanakan alamat:\n' +
                      '   • Dari: "' + alamat + '"\n' +
                      '   • Jadi: "Sumbersari, Jember, Jawa Timur"\n' +
                      '   • Atau: "Jember, Jawa Timur"\n\n' +
                      '3️⃣ Gunakan GPS:\n' +
                      '   • Klik "Deteksi Lokasi Saya" jika Anda di lokasi\n\n' +
                      '4️⃣ Input Manual:\n' +
                      '   • Ketik koordinat latitude & longitude langsung\n' +
                      '   • Contoh: Latitude: -8.159917, Longitude: 113.722750');
            }
        })
        .catch(error => {
            button.disabled = false;
            button.innerHTML = originalHTML;
            
            console.error('Geocoding error:', error);
            alert('⚠️ Terjadi kesalahan saat mencari koordinat.\n\n' +
                  '🎯 SOLUSI CEPAT:\n' +
                  '• Klik langsung pada PETA (paling mudah!)\n' +
                  '• Atau gunakan "Deteksi Lokasi Saya"\n' +
                  '• Atau ketik koordinat manual');
        });
});
    
    // ========== DETECT USER LOCATION (GPS) ==========
    document.getElementById('detectLocation').addEventListener('click', function() {
        const button = this;
        const originalHTML = button.innerHTML;
        
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mendeteksi lokasi...';
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    addMarker(lat, lng, true); // true = reverse geocode
                    
                    button.disabled = false;
                    button.innerHTML = originalHTML;
                    
                    alert('✅ Lokasi GPS berhasil terdeteksi!\n\n' +
                          '🌍 Koordinat: ' + lat.toFixed(6) + ', ' + lng.toFixed(6) + '\n' +
                          '📏 Jarak ke kampus: ' + document.getElementById('jarak').value + ' meter\n' +
                          '📍 Alamat otomatis di-update!');
                },
                function(error) {
                    button.disabled = false;
                    button.innerHTML = originalHTML;
                    
                    let errorMsg = '❌ Gagal mendeteksi lokasi GPS.\n\n';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMsg += '🚫 Izin lokasi ditolak.\n\n' +
                                       '💡 Solusi:\n' +
                                       '1. Klik ikon gembok/info di address bar\n' +
                                       '2. Izinkan akses lokasi\n' +
                                       '3. Refresh halaman dan coba lagi';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMsg += '📡 Informasi lokasi tidak tersedia.\n\n' +
                                       '💡 Coba gunakan "Cari dari Alamat" sebagai gantinya.';
                            break;
                        case error.TIMEOUT:
                            errorMsg += '⏱️ Waktu permintaan habis.\n\n' +
                                       '💡 Coba lagi atau gunakan cara lain.';
                            break;
                        default:
                            errorMsg += '⚠️ Terjadi kesalahan yang tidak diketahui.';
                    }
                    alert(errorMsg);
                }
            );
        } else {
            button.disabled = false;
            button.innerHTML = originalHTML;
            alert('❌ Browser Anda tidak mendukung Geolocation.\n\n' +
                  '💡 Gunakan "Cari dari Alamat" atau klik pada peta.');
        }
    });
    
    // ========== CLICK ON MAP → AUTO UPDATE ALAMAT (FITUR BARU!) ==========
    map.on('click', function(e) {
        addMarker(e.latlng.lat, e.latlng.lng, true); // true = reverse geocode
    });
    
    // ========== WHATSAPP VALIDATION ==========
    const whatsappInput = document.getElementById('no_whatsapp');
    
    whatsappInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 20) {
            this.value = this.value.substring(0, 20);
        }
    });
    
    whatsappInput.addEventListener('blur', function() {
        const value = this.value.trim();
        if (value) {
            if (!value.startsWith('08') && !value.startsWith('628') && !value.startsWith('62')) {
                alert('⚠️ Format nomor WhatsApp tidak valid.\n\nGunakan format: 08xxx atau 628xxx');
                this.focus();
            }
            if (value.length < 10) {
                alert('⚠️ Nomor WhatsApp terlalu pendek.\n\nMinimal 10 digit.');
                this.focus();
            }
        }
    });
    
    // ========== LIVE PREVIEW ==========
    const form = document.getElementById('kontrakanForm');
    const previewSection = document.getElementById('previewSection');
    
    const namaInput = document.getElementById('nama');
    const hargaInput = document.getElementById('harga');
    const jumlah_kamarInput = document.getElementById('jumlah_kamar');
    const bathroomCountInput = document.getElementById('bathroom_count');
    const jarakInput = document.getElementById('jarak');
    
    function updatePreview() {
        const nama = namaInput ? namaInput.value : '';
        const harga = hargaInput ? hargaInput.value : '';
        const jumlah_kamar = jumlah_kamarInput ? jumlah_kamarInput.value : '';
        const bathroomCount = bathroomCountInput ? bathroomCountInput.value : '';
        const jarak = jarakInput ? jarakInput.value : '';
        
        if (nama || harga || jumlah_kamar || bathroomCount || jarak) {
            previewSection.style.display = 'block';
            
            document.getElementById('previewNama').textContent = nama || '-';
            document.getElementById('previewHarga').textContent = harga ? 'Rp ' + parseInt(harga).toLocaleString('id-ID') + '/tahun' : '-';
            document.getElementById('previewjumlah_kamar').textContent = jumlah_kamar ? jumlah_kamar + ' kamar' : '-';
            document.getElementById('previewBathroomCount').textContent = bathroomCount ? bathroomCount + ' kamar mandi' : '-';
            document.getElementById('previewJarak').textContent = jarak ? jarak + ' meter (' + (jarak/1000).toFixed(2) + ' km)' : '-';
        } else {
            previewSection.style.display = 'none';
        }
    }
    
    if (namaInput) namaInput.addEventListener('input', updatePreview);
    if (hargaInput) hargaInput.addEventListener('input', updatePreview);
    if (jumlah_kamarInput) jumlah_kamarInput.addEventListener('input', updatePreview);
    if (bathroomCountInput) bathroomCountInput.addEventListener('input', updatePreview);
    if (jarakInput) jarakInput.addEventListener('input', updatePreview);
    
    // ========== FOTO PREVIEW ==========
    const fotoInput = document.getElementById('foto');
    const previewFoto = document.getElementById('preview-foto');
    const previewContainer = document.getElementById('preview-foto-container');
    
    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            if (file.size > 2097152) {
                alert('❌ Ukuran file maksimal 2MB!');
                this.value = '';
                previewContainer.style.display = 'none';
                return;
            }
            
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                alert('❌ Hanya file JPG, PNG, dan JPEG yang diperbolehkan!');
                this.value = '';
                previewContainer.style.display = 'none';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                previewFoto.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    });
    
    window.removeImage = function() {
        fotoInput.value = '';
        previewContainer.style.display = 'none';
        previewFoto.src = '';
    }
    
    // ========== FORM VALIDATION ==========
    form.addEventListener('submit', function(e) {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        
        // Validasi koordinat
        const validation = validateCoordinates(lat, lng);
        if (!validation.valid) {
            e.preventDefault();
            alert(validation.message);
            return;
        }
        
        // Validasi WhatsApp
        const waValue = whatsappInput.value.trim();
        if (waValue) {
            if (waValue.length < 10) {
                e.preventDefault();
                alert('❌ Nomor WhatsApp minimal 10 digit!');
                whatsappInput.focus();
                return;
            }
            if (!waValue.startsWith('08') && !waValue.startsWith('628') && !waValue.startsWith('62')) {
                e.preventDefault();
                alert('❌ Format nomor WhatsApp tidak valid.\n\nGunakan: 08xxx atau 628xxx');
                whatsappInput.focus();
                return;
            }
        }
        
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
    
    // ========== LIVE PREVIEW HARGA ==========
    const hargaPreview = document.getElementById('harga_preview');
    const hargaPreviewText = document.getElementById('harga_preview_text');

    function terbilangSingkat(angka) {
        if (!angka || angka <= 0) return '';
        const m = Math.floor(angka / 1000000);
        const rb = Math.floor((angka % 1000000) / 1000);
        const s = angka % 1000;
        let parts = [];
        if (m > 0) parts.push(m + ' Juta');
        if (rb > 0) parts.push(rb + ' Ribu');
        if (s > 0) parts.push(s + ' Rupiah');
        return parts.join(' ') || '0';
    }

    function formatRibu(angka) {
        return parseInt(angka).toLocaleString('id-ID');
    }

    function updateHargaPreview() {
        const val = parseInt(hargaInput.value);
        if (val > 0) {
            hargaPreviewText.textContent = 'Rp ' + formatRibu(val) + ' — ' + terbilangSingkat(val);
            hargaPreview.style.display = 'block';
        } else {
            hargaPreview.style.display = 'none';
        }
    }

    if (hargaInput) hargaInput.addEventListener('input', updateHargaPreview);
    // Tampilkan saat load jika sudah ada nilai
    if (hargaInput && hargaInput.value) updateHargaPreview();
});
</script>
<style>
    .form-control:focus,
    .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
    
    .input-group-text {
        transition: all 0.3s ease;
    }
    
    .form-control:focus + .input-group-text,
    .input-group:focus-within .input-group-text {
        border-color: #86b7fe;
        background-color: #e7f1ff;
    }
    
    .card {
        transition: box-shadow 0.3s ease;
    }
    
    .was-validated .form-control:invalid {
        border-color: #dc3545;
    }
    
    .was-validated .form-control:valid {
        border-color: #198754;
    }
    
    #preview-foto-container {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .leaflet-container {
        font-family: inherit;
    }
    
    .leaflet-popup-content-wrapper {
        border-radius: 8px;
    }
    
    #no_whatsapp:focus {
        border-color: #25D366 !important;
        box-shadow: 0 0 0 0.25rem rgba(37, 211, 102, 0.15) !important;
    }
    
    #no_whatsapp.is-valid {
        border-color: #25D366;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2325D366' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    }
    
    /* Loading animation */
    .spinner-border {
        width: 1rem;
        height: 1rem;
        border-width: 0.15em;
    }
    
    /* Custom marker kampus */
    .custom-marker {
        background: none;
        border: none;
    }
    
    /* Input koordinat style - BARU! */
    #latitude:focus,
    #longitude:focus {
        border-color: #ffc107 !important;
        box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.15) !important;
    }
    
    /* Highlight saat input manual aktif - BARU! */
    #latitude:not([readonly]):hover,
    #longitude:not([readonly]):hover {
        background-color: #fffbf0;
        cursor: text;
    }
    
    /* Badge animation */
    .badge {
        animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    /* Smooth transitions */
    .form-control,
    .btn {
        transition: all 0.3s ease;
    }
    
    /* Alert styling */
    .alert-info {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-left: 4px solid #2196f3;
    }
    
    /* Map container styling */
    #map {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease;
    }
    
    #map:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }
    
    /* Button hover effects */
    #geocodeBtn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
    }
    
    #detectLocation:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        #map {
            height: 300px !important;
        }
        
        .alert ul {
            padding-left: 1.2rem;
        }
        
        .badge {
            font-size: 0.7rem;
        }
    }
    
    /* Input number remove spinner arrows (optional) */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        opacity: 0.5;
    }
    
    input[type="number"]:hover::-webkit-inner-spin-button,
    input[type="number"]:hover::-webkit-outer-spin-button {
        opacity: 1;
    }
    
    /* Success state for coordinates */
    #latitude.is-valid,
    #longitude.is-valid {
        border-color: #198754;
        background-color: #f0fff4;
    }
    
    /* Warning state for manual input */
    #latitude:not([readonly]),
    #longitude:not([readonly]) {
        border-left: 3px solid #ffc107;
    }
    
    /* Tooltip style */
    small.text-muted {
        font-size: 0.8rem;
        display: block;
        margin-top: 0.25rem;
    }
    
    /* Preview section animation */
    #previewSection {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            max-height: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            max-height: 200px;
            transform: translateY(0);
        }
    }
    
    /* Button loading state */
    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    /* Card info styling */
    .card.bg-light {
        border-left: 4px solid #0d6efd;
    }
    
    /* Input group icon colors */
    .input-group-text i.bi-building {
        color: #0d6efd;
    }
    
    .input-group-text i.bi-geo-alt {
        color: #dc3545;
    }
    
    .input-group-text i.bi-geo {
        color: #dc3545;
    }
    
    .input-group-text i.bi-telephone {
        color: #25D366;
    }
    
    .input-group-text i.bi-currency-dollar {
        color: #198754;
    }
    
    .input-group-text i.bi-rulers {
        color: #ffc107;
    }
    
    .input-group-text i.bi-pin-map {
        color: #0dcaf0;
    }
    
    .input-group-text i.bi-star {
        color: #ffc107;
    }
    
    .input-group-text i.bi-image {
        color: #0d6efd;
    }
    
    /* Smooth scroll behavior */
    html {
        scroll-behavior: smooth;
    }
    
    /* Focus visible for accessibility */
    *:focus-visible {
        outline: 2px solid #0d6efd;
        outline-offset: 2px;
    }
    
    /* Print styles */
    @media print {
        .btn,
        .breadcrumb,
        nav,
        #map {
            display: none !important;
        }
        
        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TA\spk_kontrakan\resources\views/kontrakan/create.blade.php ENDPATH**/ ?>