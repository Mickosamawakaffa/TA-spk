

<?php $__env->startSection('title', 'Edit Laundry'); ?>

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
            background: linear-gradient(135deg, #818cf8 0%, #667eea 50%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.25);
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
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #818cf8 0%, #667eea 100%);
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
            <li class="breadcrumb-item"><a href="<?php echo e(route('laundry.index')); ?>">Laundry</a></li>
            <li class="breadcrumb-item active">Edit Data</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="page-header">
        <h2 class="mb-0">✏️ Edit Laundry</h2>
        <p class="mb-0 opacity-95">Update informasi laundry <strong><?php echo e($laundry->nama); ?></strong></p>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-12">
            <div class="card form-card">
                <div class="card-body p-4">
                    <form action="<?php echo e(route('laundry.update', $laundry->id)); ?>" method="POST" enctype="multipart/form-data" id="laundryForm">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <!-- Informasi Dasar Section -->
                        <div class="mb-4">
                            <h5 class="section-header mb-0">
                                <i class="bi bi-info-circle text-success me-2"></i>Informasi Dasar
                            </h5>
                            
                            <div class="row g-3">
                                <!-- Nama Laundry -->
                                <div class="col-md-12">
                                    <label for="nama" class="form-label fw-semibold">
                                        Nama Laundry <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-basket3 text-success"></i>
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
                                            placeholder="Contoh: Laundry Express 88"
                                            value="<?php echo e(old('nama', $laundry->nama)); ?>"
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
                                            required
                                        ><?php echo e(old('alamat', $laundry->alamat)); ?></textarea>
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
                                                <div class="d-flex flex-wrap gap-2 align-items-center mt-2">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="alamatAutoBtn">
                                                        <i class="bi bi-geo-alt me-1"></i>Gunakan alamat dari peta
                                                    </button>
                                                    <small class="text-muted">Alamat otomatis dari peta, silakan lengkapi RT/RW/No rumah.</small>
                                                </div>
                                    </div>
                                </div>

                                <!-- Info Jarak Otomatis -->
                                <div class="col-12">
                                    <div class="alert alert-info border-0 d-flex align-items-start">
                                        <i class="bi bi-info-circle-fill fs-5 me-2 mt-1"></i>
                                        <div>
                                            <strong class="small">Perhitungan Jarak Otomatis</strong>
                                            <p class="mb-0 small mt-1">
                                                Jarak akan dihitung otomatis dari <strong>Kampus Polije</strong> berdasarkan koordinat GPS yang Anda tentukan.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Jarak ke Kampus - AUTO CALCULATED! -->
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
                                            value="<?php echo e(old('jarak', $laundry->jarak)); ?>"
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
                                        Jarak otomatis dihitung dari koordinat laundry ke kampus
                                    </small>
                                </div>

                                <!-- Fasilitas -->
                                <div class="col-md-6">
                                    <?php
                                        $masterFasilitas = [
                                            'Cuci Kering',
                                            'Cuci Lipat',
                                            'Cuci + Setrika',
                                            'Setrika Saja',
                                            'Express',
                                            'Reguler',
                                            'Antar Jemput',
                                            'Pewangi Premium',
                                            'Packaging Rapi',
                                            'Cuci Boneka',
                                            'Cuci Gordyn',
                                            'Cuci Tas',
                                            'Cuci Bedcover',
                                            'Cuci Seprei',
                                            'Cuci Selimut',
                                        ];
                                        $selectedFasilitas = collect(explode(',', old('fasilitas', $laundry->fasilitas)))
                                            ->map(function ($item) { return trim($item); })
                                            ->filter()
                                            ->values();
                                        $fasilitasOptions = collect($masterFasilitas)
                                            ->merge($selectedFasilitas)
                                            ->unique()
                                            ->values();
                                    ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="fasilitas_search" class="form-label fw-semibold mb-0">
                                            Fasilitas <span class="text-danger">*</span>
                                        </label>
                                        <span id="fasilitas_selected_count" class="badge text-bg-primary rounded-pill">0 dipilih</span>
                                    </div>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-search text-secondary"></i>
                                        </span>
                                        <input
                                            type="text"
                                            id="fasilitas_search"
                                            class="form-control border-start-0"
                                            placeholder="Cari fasilitas..."
                                        >
                                    </div>
                                    <div class="d-flex gap-2 mb-2">
                                        <button type="button" id="select_all_fasilitas" class="btn btn-sm btn-outline-primary py-1 px-2">
                                            <i class="bi bi-check2-square me-1"></i>Pilih Semua
                                        </button>
                                        <button type="button" id="clear_all_fasilitas" class="btn btn-sm btn-outline-secondary py-1 px-2">
                                            <i class="bi bi-x-square me-1"></i>Hapus Semua
                                        </button>
                                    </div>
                                    <div id="fasilitas_checkbox_container" class="fasilitas-panel border rounded p-2 bg-white <?php $__errorArgs = ['fasilitas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-danger <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <div class="fasilitas-chip-list">
                                        <?php $__currentLoopData = $fasilitasOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fasilitasOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="fasilitas-option-item">
                                                <input
                                                    class="fasilitas-checkbox visually-hidden"
                                                    type="checkbox"
                                                    value="<?php echo e($fasilitasOption); ?>"
                                                    id="fasilitas_<?php echo e($loop->index); ?>"
                                                    <?php echo e($selectedFasilitas->contains($fasilitasOption) ? 'checked' : ''); ?>

                                                >
                                                <label class="fasilitas-chip" for="fasilitas_<?php echo e($loop->index); ?>">
                                                    <?php echo e($fasilitasOption); ?>

                                                </label>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        <div id="fasilitas_empty_state" class="text-muted small py-2 px-1" style="display: none;">
                                            Tidak ada fasilitas yang cocok dengan pencarian.
                                        </div>
                                    </div>
                                    <input type="hidden" name="fasilitas" id="fasilitas" value="<?php echo e(old('fasilitas', $laundry->fasilitas)); ?>">
                                    <?php $__errorArgs = ['fasilitas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">Pilih satu atau lebih fasilitas yang disediakan laundry ini.</small>
                                </div>

                                <!-- Status Operasional -->
                                <div class="col-md-4">
                                    <label for="status" class="form-label fw-semibold">
                                        Status Operasional <span class="text-danger">*</span>
                                    </label>
                                    <select name="status" id="status" class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                        <option value="buka" <?php echo e(old('status', $laundry->status) == 'buka' ? 'selected' : ''); ?>>🟢 Buka</option>
                                        <option value="tutup" <?php echo e(old('status', $laundry->status) == 'tutup' ? 'selected' : ''); ?>>🔴 Tutup</option>
                                    </select>
                                    <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">Status laundry saat ini</small>
                                </div>

                                <!-- Jam Buka -->
                                <div class="col-md-4">
                                    <label for="jam_buka" class="form-label fw-semibold">
                                        Jam Buka <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-clock-history text-success"></i>
                                        </span>
                                        <input 
                                            type="time" 
                                            name="jam_buka" 
                                            id="jam_buka"
                                            class="form-control border-start-0 <?php $__errorArgs = ['jam_buka'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            value="<?php echo e(old('jam_buka', \Carbon\Carbon::parse($laundry->jam_buka)->format('H:i'))); ?>"
                                            required
                                        >
                                        <?php $__errorArgs = ['jam_buka'];
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
                                </div>

                                <!-- Jam Tutup -->
                                <div class="col-md-4">
                                    <label for="jam_tutup" class="form-label fw-semibold">
                                        Jam Tutup <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-clock text-danger"></i>
                                        </span>
                                        <input 
                                            type="time" 
                                            name="jam_tutup" 
                                            id="jam_tutup"
                                            class="form-control border-start-0 <?php $__errorArgs = ['jam_tutup'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            value="<?php echo e(old('jam_tutup', \Carbon\Carbon::parse($laundry->jam_tutup)->format('H:i'))); ?>"
                                            required
                                        >
                                        <?php $__errorArgs = ['jam_tutup'];
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
                                </div>
                            </div>
                        </div>

                        <!-- Lokasi & Koordinat Section -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 pb-2 border-bottom">
                                <i class="bi bi-pin-map text-danger me-2"></i>Lokasi & Koordinat
                            </h5>
                            
                            <div class="alert alert-info border-info">
                                <div class="d-flex">
                                    <i class="bi bi-info-circle me-2 mt-1"></i>
                                    <div>
                                        <strong>Tips:</strong> Klik pada peta, ketik manual latitude/longitude, atau gunakan tombol "Deteksi Lokasi Saya" untuk mendapatkan koordinat otomatis.
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <!-- Latitude -->
                                <div class="col-md-6">
                                    <label for="latitude" class="form-label fw-semibold">
                                        Latitude <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-geo text-danger"></i>
                                        </span>
                                        <input 
                                            type="text" 
                                            name="latitude" 
                                            class="form-control" 
                                            id="latitude" 
                                            placeholder="-6.966667"
                                            value="<?php echo e(old('latitude', $laundry->latitude)); ?>"
                                            required
                                        >
                                    </div>
                                </div>

                                <!-- Longitude -->
                                <div class="col-md-6">
                                    <label for="longitude" class="form-label fw-semibold">
                                        Longitude <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-pin-angle text-danger"></i>
                                        </span>
                                        <input 
                                            type="text" 
                                            name="longitude" 
                                            class="form-control" 
                                            id="longitude" 
                                            placeholder="110.416664"
                                            value="<?php echo e(old('longitude', $laundry->longitude)); ?>"
                                            required
                                        >
                                    </div>
                                </div>

                                <!-- Tombol Deteksi Lokasi -->
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="detectLocationBtn">
                                        <i class="bi bi-crosshair me-1"></i>Deteksi Lokasi Saya
                                    </button>
                                </div>

                                <!-- Map Container -->
                                <div class="col-md-12">
                                    <div id="map" style="height: 400px; border-radius: 8px; border: 2px solid #dee2e6;"></div>
                                    <small class="text-muted">Marker menunjukkan lokasi saat ini. Klik di peta untuk memperbarui lokasi.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Layanan Section (Dynamic) -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <h5 class="fw-bold mb-0">
                                    <i class="bi bi-gear text-primary me-2"></i>Jenis Layanan
                                </h5>
                                <button type="button" class="btn btn-sm btn-primary" id="addLayanan">
                                    <i class="bi bi-plus-circle me-1"></i>Tambah Layanan
                                </button>
                            </div>
                            
                            <div id="layananContainer">
                                <?php if($laundry->layanan && $laundry->layanan->count() > 0): ?>
                                    <?php $__currentLoopData = $laundry->layanan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $layanan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="layanan-item card border mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0 fw-semibold text-success">Layanan #<?php echo e($index + 1); ?></h6>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-layanan" style="<?php echo e($laundry->layanan->count() > 1 ? '' : 'display: none;'); ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                            
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        Jenis Layanan <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="layanan[<?php echo e($index); ?>][jenis_layanan]" class="form-select" required>
                                                        <option value="">-- Pilih Jenis --</option>
                                                        <option value="harian" <?php echo e(in_array($layanan->jenis_layanan, ['harian', 'reguler', 'kiloan']) ? 'selected' : ''); ?>>🕐 Harian</option>
                                                        <option value="jam" <?php echo e(in_array($layanan->jenis_layanan, ['jam', 'express', 'kilat', 'satuan']) ? 'selected' : ''); ?>>⚡ Jam</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        Nama Paket <span class="text-danger">*</span>
                                                    </label>
                                                    <input 
                                                        type="text" 
                                                        name="layanan[<?php echo e($index); ?>][nama_paket]" 
                                                        class="form-control" 
                                                        placeholder="Paket Harian"
                                                        value="<?php echo e($layanan->nama_paket); ?>"
                                                        required
                                                    >
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        Harga (Rp) <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input 
                                                            type="number" 
                                                            name="layanan[<?php echo e($index); ?>][harga]" 
                                                            class="form-control" 
                                                            placeholder="7000"
                                                            value="<?php echo e($layanan->harga); ?>"
                                                            min="0"
                                                            required
                                                        >
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <?php
                                                        $estimasiValue = (float) $layanan->estimasi_selesai;
                                                        $estimasiSatuan = 'jam';
                                                        if ($estimasiValue >= 24 && fmod($estimasiValue, 24) == 0.0) {
                                                            $estimasiSatuan = 'harian';
                                                            $estimasiValue = $estimasiValue / 24;
                                                        }
                                                    ?>
                                                    <label class="form-label fw-semibold">
                                                        Estimasi Selesai <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input 
                                                            type="number" 
                                                            name="layanan[<?php echo e($index); ?>][estimasi_selesai]" 
                                                            class="form-control" 
                                                            placeholder="1"
                                                            value="<?php echo e(rtrim(rtrim(number_format($estimasiValue, 2, '.', ''), '0'), '.')); ?>"
                                                            min="1"
                                                            required
                                                        >
                                                        <select name="layanan[<?php echo e($index); ?>][estimasi_satuan]" class="form-select" style="max-width: 140px;" required>
                                                            <option value="jam" <?php echo e($estimasiSatuan == 'jam' ? 'selected' : ''); ?>>Jam</option>
                                                            <option value="harian" <?php echo e($estimasiSatuan == 'harian' ? 'selected' : ''); ?>>Harian</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label fw-semibold">
                                                        Deskripsi <span class="text-muted">(Opsional)</span>
                                                    </label>
                                                    <textarea 
                                                        name="layanan[<?php echo e($index); ?>][deskripsi]" 
                                                        class="form-control" 
                                                        rows="2"
                                                        placeholder="Deskripsi singkat paket ini..."
                                                    ><?php echo e($layanan->deskripsi); ?></textarea>
                                                </div>
                                                <input type="hidden" name="layanan[<?php echo e($index); ?>][status]" value="<?php echo e($layanan->status ?? 'aktif'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <div class="layanan-item card border mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0 fw-semibold text-success">Layanan #1</h6>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-layanan" style="display: none;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                            
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <label class="form-label fw-semibold">
                                                        Jenis Layanan <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="layanan[0][jenis_layanan]" class="form-select" required>
                                                        <option value="">-- Pilih Jenis --</option>
                                                        <option value="harian">🕐 Harian</option>
                                                        <option value="jam">⚡ Jam</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        Harga/kg <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input 
                                                            type="number" 
                                                            name="layanan[0][harga]" 
                                                            class="form-control" 
                                                            placeholder="7000"
                                                            min="0"
                                                            required
                                                        >
                                                        <span class="input-group-text">/kg</span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        Nama Paket <span class="text-danger">*</span>
                                                    </label>
                                                    <input 
                                                        type="text" 
                                                        name="layanan[0][nama_paket]" 
                                                        class="form-control" 
                                                        placeholder="Paket Harian"
                                                        required
                                                    >
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        Estimasi Selesai <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input 
                                                            type="number" 
                                                            name="layanan[0][estimasi_selesai]" 
                                                            class="form-control" 
                                                            placeholder="1"
                                                            min="1"
                                                            required
                                                        >
                                                        <select name="layanan[0][estimasi_satuan]" class="form-select" style="max-width: 140px;" required>
                                                            <option value="jam" selected>Jam</option>
                                                            <option value="harian">Harian</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label fw-semibold">
                                                        Deskripsi <span class="text-muted">(Opsional)</span>
                                                    </label>
                                                    <textarea 
                                                        name="layanan[0][deskripsi]" 
                                                        class="form-control" 
                                                        rows="2"
                                                        placeholder="Deskripsi singkat paket ini..."
                                                    ></textarea>
                                                </div>
                                                <input type="hidden" name="layanan[0][status]" value="aktif">
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php $__errorArgs = ['layanan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="alert alert-danger"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Upload Foto Section -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 pb-2 border-bottom">
                                <i class="bi bi-camera text-success me-2"></i>Foto Laundry
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="border rounded p-3 bg-light">
                                        <div class="row align-items-center">
                                            <div class="col-md-4 text-center mb-3 mb-md-0">
                                                <?php if($laundry->foto): ?>
                                                    <img 
                                                        src="<?php echo e(asset('uploads/Laundry/' . $laundry->foto)); ?>" 
                                                        alt="<?php echo e($laundry->nama); ?>" 
                                                        class="img-fluid rounded"
                                                        style="max-height: 150px; cursor: pointer;"
                                                        onclick="openImageModal(this.src)"
                                                        id="current-foto"
                                                    >
                                                <?php else: ?>
                                                    <div class="py-3 position-relative" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 10px; padding: 30px !important;">
                                                        <div style="animation: float 3s ease-in-out infinite;">
                                                            <i class="bi bi-image" style="font-size: 3rem; color: #cbd5e0; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));"></i>
                                                        </div>
                                                        <p class="text-muted mb-0 small mt-2">Belum ada foto</p>
                                                    </div>
                                                    
                                                    <style>
                                                        @keyframes float {
                                                            0%, 100% { transform: translateY(0px); }
                                                            50% { transform: translateY(-5px); }
                                                        }
                                                    </style>
                                                <?php endif; ?>
                                                                                                
                                                <div id="preview-foto-container" style="display: none;">
                                                    <img id="preview-foto" src="" alt="Preview" class="img-fluid rounded" style="max-height: 150px;">
                                                    <p class="text-success mb-0 small mt-1">
                                                        <i class="bi bi-check-circle me-1"></i>Foto baru
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-8">
                                                <label for="foto" class="form-label fw-semibold mb-2">
                                                    <i class="bi bi-upload me-1"></i>Upload / Ganti Foto
                                                </label>
                                                <input 
                                                    type="file" 
                                                    name="foto" 
                                                    class="form-control form-control-sm <?php $__errorArgs = ['foto'];
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
                                                <small class="text-muted d-block mt-1">Format: JPG, PNG, JPEG (Max 2MB)</small>
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
                                                
                                                <?php if($laundry->foto): ?>
                                                <div class="form-check mt-3">
                                                    <input 
                                                        class="form-check-input border-danger" 
                                                        type="checkbox" 
                                                        name="hapus_foto" 
                                                        value="1" 
                                                        id="hapus_foto"
                                                    >
                                                    <label class="form-check-label text-danger fw-semibold" for="hapus_foto">
                                                        <i class="bi bi-trash me-1"></i>Hapus foto ini
                                                    </label>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Perubahan -->
                        <div class="alert alert-info border-info mb-4">
                            <h6 class="fw-bold mb-2">
                                <i class="bi bi-info-circle me-2"></i>Info Perubahan
                            </h6>
                            <p class="mb-0 small">
                                Anda sedang mengedit data laundry <strong><?php echo e($laundry->nama); ?></strong>. 
                                Pastikan semua perubahan sudah benar sebelum menyimpan.
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                            <a href="<?php echo e(route('laundry.index')); ?>" class="btn btn-light px-4">
                                <i class="bi bi-arrow-left me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data History Card -->
            <div class="card border-0 bg-light mt-3">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="bi bi-clock-history text-success" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-2">Riwayat Data:</h6>
                            <p class="mb-1 small text-muted">
                                <strong>Dibuat:</strong> <?php echo e($laundry->created_at->format('d M Y, H:i')); ?> WIB
                            </p>
                            <?php if($laundry->updated_at != $laundry->created_at): ?>
                            <p class="mb-0 small text-muted">
                                <strong>Terakhir diupdate:</strong> <?php echo e($laundry->updated_at->format('d M Y, H:i')); ?> WIB
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Zoom Foto -->
<div id="imageModal" class="modal-fullscreen" style="display: none;">
    <span class="modal-close" onclick="closeImageModal()">&times;</span>
    <img id="modalImage" class="modal-content-img" src="">
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== KOORDINAT KAMPUS POLIJE ==========
    const KAMPUS_LAT = -8.15981;
    const KAMPUS_LNG = 113.72312;
    
    // ========== FUNGSI HITUNG JARAK (Haversine Formula) ==========
    function calculateDistance(lat1, lng1, lat2, lng2) {
        const R = 6371; // Radius bumi dalam km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLng/2) * Math.sin(dLng/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c; // Jarak dalam km
    }
    
    // ========== UPDATE JARAK KE KAMPUS ==========
    function updateJarakKampus(lat, lng) {
        if (lat && lng) {
            const jarakKm = calculateDistance(lat, lng, KAMPUS_LAT, KAMPUS_LNG);
            const jarakMeter = Math.round(jarakKm * 1000); // Convert ke meter
            document.getElementById('jarak').value = jarakMeter;
            console.log(`Jarak ke kampus: ${jarakKm.toFixed(2)} km (${jarakMeter} m)`);
        }
    }

    // ========== AUTO UPDATE ALAMAT DARI MAP ==========
    const alamatInput = document.getElementById('alamat');
    const alamatAutoBtn = document.getElementById('alamatAutoBtn');
    let reverseGeocodeTimeout;
    let lastGeocodeKey = '';
    let lastLatLng = null;
    let isManualAddress = false;
    let isSettingAlamat = false;

    function setAlamatValue(value) {
        if (!alamatInput) {
            return;
        }

        isSettingAlamat = true;
        alamatInput.value = value;
        isSettingAlamat = false;
        isManualAddress = false;
    }

    if (alamatInput) {
        alamatInput.addEventListener('input', function () {
            if (isSettingAlamat) {
                return;
            }
            isManualAddress = alamatInput.value.trim() !== '';
        });
    }

    function scheduleReverseGeocode(lat, lng, force = false) {
        if (!alamatInput) {
            return;
        }

        lastLatLng = { lat, lng };
        if (!force && isManualAddress && alamatInput.value.trim() !== '') {
            return;
        }

        const key = `${lat.toFixed(6)},${lng.toFixed(6)}`;
        if (!force && key === lastGeocodeKey) {
            return;
        }

        lastGeocodeKey = key;
        clearTimeout(reverseGeocodeTimeout);
        reverseGeocodeTimeout = setTimeout(() => {
            reverseGeocode(lat, lng, force);
        }, 450);
    }

    async function reverseGeocode(lat, lng, force = false) {
        if (!alamatInput) {
            return;
        }

        if (!force && isManualAddress && alamatInput.value.trim() !== '') {
            return;
        }

        const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&addressdetails=1&zoom=18&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}&accept-language=id`;

        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            const detailedAddress = buildDetailedAddress(data?.address);
            if (detailedAddress) {
                setAlamatValue(detailedAddress);
                return;
            }

            if (data && data.display_name) {
                setAlamatValue(data.display_name);
            }
        } catch (error) {
            console.warn('Gagal mengambil alamat otomatis.', error);
        }
    }

    function buildDetailedAddress(address) {
        if (!address) {
            return '';
        }

        const parts = [];
        const seen = new Set();
        const addPart = (value) => {
            if (!value) {
                return;
            }
            const text = String(value).trim();
            if (!text) {
                return;
            }
            const key = text.toLowerCase();
            if (seen.has(key)) {
                return;
            }
            seen.add(key);
            parts.push(text);
        };

        const road = address.road || address.pedestrian || address.cycleway || address.footway || address.path || address.residential;
        const houseNumber = address.house_number || address.house_name || address.building;

        if (road && houseNumber) {
            addPart(`${road} No ${houseNumber}`);
        } else {
            addPart(road);
            addPart(houseNumber);
        }

        addPart(address.neighbourhood);
        addPart(address.suburb);
        addPart(address.hamlet);
        addPart(address.village);
        addPart(address.town);
        addPart(address.city_district);
        addPart(address.city);
        addPart(address.county);
        addPart(address.state);
        addPart(address.postcode);
        addPart(address.country);

        return parts.join(', ');
    }
    
    // ========== LAYANAN MANAGEMENT ==========
    let layananCount = <?php echo e($laundry->layanan ? $laundry->layanan->count() : 1); ?>;
    const container = document.getElementById('layananContainer');
    const addButton = document.getElementById('addLayanan');
    const laundryFormElement = document.getElementById('laundryForm');
    const DRAFT_KEY = 'laundry_edit_draft_<?php echo e($laundry->id); ?>_v1';
    const excludedDraftFields = new Set(['_token', '_method']);
    let isRestoringDraft = false;
    let draftSaveTimeout;
    
    addButton.addEventListener('click', function() {
        const newItem = document.createElement('div');
        newItem.className = 'layanan-item card border mb-3';
        newItem.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-semibold text-success">Layanan #${layananCount + 1}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-layanan">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Jenis Layanan <span class="text-danger">*</span>
                        </label>
                        <select name="layanan[${layananCount}][jenis_layanan]" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="harian">🕐 Harian</option>
                            <option value="jam">⚡ Jam</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Nama Paket <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="layanan[${layananCount}][nama_paket]" 
                            class="form-control" 
                            placeholder="Paket Harian"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Harga (Rp) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input 
                                type="number" 
                                name="layanan[${layananCount}][harga]" 
                                class="form-control" 
                                placeholder="7000"
                                min="0"
                                required
                            >
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Estimasi Selesai <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input 
                                type="number" 
                                name="layanan[${layananCount}][estimasi_selesai]" 
                                class="form-control" 
                                placeholder="1"
                                min="1"
                                required
                            >
                            <select name="layanan[${layananCount}][estimasi_satuan]" class="form-select" style="max-width: 140px;" required>
                                <option value="jam" selected>Jam</option>
                                <option value="harian">Harian</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            Deskripsi <span class="text-muted">(Opsional)</span>
                        </label>
                        <textarea 
                            name="layanan[${layananCount}][deskripsi]" 
                            class="form-control" 
                            rows="2"
                            placeholder="Deskripsi singkat paket ini..."
                        ></textarea>
                    </div>
                    <input type="hidden" name="layanan[${layananCount}][status]" value="aktif">
                </div>
            </div>
        `;
        
        container.appendChild(newItem);
        layananCount++;
        updateRemoveButtons();
        scheduleDraftSave();
    });
    
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-layanan')) {
            e.target.closest('.layanan-item').remove();
            updateRemoveButtons();
            scheduleDraftSave();
        }
    });
    
    function updateRemoveButtons() {
        const items = container.querySelectorAll('.layanan-item');
        items.forEach((item) => {
            const removeBtn = item.querySelector('.remove-layanan');
            if (items.length > 1) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }

    // ========== AUTOSAVE DRAFT (LOCALSTORAGE) ==========
    function scheduleDraftSave() {
        if (!laundryFormElement || isRestoringDraft) {
            return;
        }

        clearTimeout(draftSaveTimeout);
        draftSaveTimeout = setTimeout(saveDraft, 250);
    }

    function saveDraft() {
        if (!laundryFormElement || isRestoringDraft) {
            return;
        }

        syncFasilitasInput();
        const draftData = {};
        const fields = laundryFormElement.querySelectorAll('input[name], select[name], textarea[name]');

        fields.forEach(field => {
            if (field.type === 'file') {
                return;
            }

            if (excludedDraftFields.has(field.name)) {
                return;
            }

            if (field.type === 'checkbox' || field.type === 'radio') {
                if (field.checked) {
                    draftData[field.name] = field.value;
                }
                return;
            }

            draftData[field.name] = field.value;
        });

        try {
            localStorage.setItem(DRAFT_KEY, JSON.stringify(draftData));
        } catch (error) {
            console.warn('Gagal menyimpan draft form laundry.', error);
        }
    }

    function restoreDraft() {
        if (!laundryFormElement) {
            return;
        }

        let rawDraft = null;
        try {
            rawDraft = localStorage.getItem(DRAFT_KEY);
        } catch (error) {
            console.warn('Gagal membaca draft form laundry.', error);
            return;
        }

        if (!rawDraft) {
            return;
        }

        let draftData;
        try {
            draftData = JSON.parse(rawDraft);
        } catch (error) {
            console.warn('Draft form laundry tidak valid.', error);
            return;
        }

        isRestoringDraft = true;

        const layananIndices = Object.keys(draftData)
            .map(key => key.match(/^layanan\[(\d+)\]\[/))
            .filter(Boolean)
            .map(match => parseInt(match[1], 10));

        const maxLayananIndex = layananIndices.length > 0 ? Math.max(...layananIndices) : 0;
        while (layananCount <= maxLayananIndex) {
            addButton.click();
        }

        const fields = laundryFormElement.querySelectorAll('input[name], select[name], textarea[name]');
        fields.forEach(field => {
            if (field.type === 'file') {
                return;
            }

            if (excludedDraftFields.has(field.name)) {
                return;
            }

            if (!(field.name in draftData)) {
                return;
            }

            const value = draftData[field.name];
            if (field.type === 'checkbox' || field.type === 'radio') {
                field.checked = value === field.value;
            } else {
                field.value = value;
            }
        });

        const fasilitasValues = String(draftData.fasilitas || '')
            .split(',')
            .map(item => item.trim())
            .filter(Boolean);

        Array.from(fasilitasCheckboxes).forEach(checkbox => {
            checkbox.checked = fasilitasValues.includes(checkbox.value);
        });

        syncFasilitasInput();
        filterFasilitasOptions();
        updateRemoveButtons();
        handleManualCoordinateInput();

        isRestoringDraft = false;
    }

    if (laundryFormElement) {
        laundryFormElement.addEventListener('input', scheduleDraftSave);
        laundryFormElement.addEventListener('change', scheduleDraftSave);
        laundryFormElement.addEventListener('submit', function (event) {
            if (!event.defaultPrevented) {
                try {
                    localStorage.removeItem(DRAFT_KEY);
                } catch (error) {
                    console.warn('Gagal menghapus draft form laundry.', error);
                }
            }
        });
    }
    
    updateRemoveButtons();
    
    // ========== FOTO PREVIEW ==========
    const fotoInput = document.getElementById('foto');
    const previewFoto = document.getElementById('preview-foto');
    const previewContainer = document.getElementById('preview-foto-container');
    
    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const currentFoto = document.getElementById('current-foto');
        
        if (file) {
            if (file.size > 2097152) {
                alert('Ukuran file maksimal 2MB!');
                this.value = '';
                previewContainer.style.display = 'none';
                if (currentFoto) currentFoto.style.display = 'block';
                return;
            }
            
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                alert('Hanya file JPG, PNG, dan JPEG yang diperbolehkan!');
                this.value = '';
                previewContainer.style.display = 'none';
                if (currentFoto) currentFoto.style.display = 'block';
                return;
            }
            
            if (currentFoto) currentFoto.style.display = 'none';
            
            const reader = new FileReader();
            reader.onload = function(e) {
                previewFoto.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
            if (currentFoto) currentFoto.style.display = 'block';
        }
    });

    // ========== LEAFLET MAP INITIALIZATION ==========
    const existingLat = <?php echo e($laundry->latitude ?? '-7.797068'); ?>;
    const existingLng = <?php echo e($laundry->longitude ?? '110.370529'); ?>;
    
    const map = L.map('map').setView([existingLat, existingLng], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    let marker = L.marker([existingLat, existingLng], {
        draggable: true
    }).addTo(map);
    
    // Initialize jarak on page load
    updateJarakKampus(existingLat, existingLng);
    
    // Update input fields saat marker di-drag
    marker.on('dragend', function(e) {
        const position = marker.getLatLng();
        document.getElementById('latitude').value = position.lat.toFixed(6);
        document.getElementById('longitude').value = position.lng.toFixed(6);
        updateJarakKampus(position.lat, position.lng);
        scheduleReverseGeocode(position.lat, position.lng);
    });
    
    // Update marker saat klik di peta
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        marker.setLatLng([lat, lng]);
        
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);
        updateJarakKampus(lat, lng);
        scheduleReverseGeocode(lat, lng);
        
        map.setView([lat, lng], map.getZoom());
    });
    
    // Deteksi Lokasi User
    document.getElementById('detectLocationBtn').addEventListener('click', function() {
        const btn = this;
        const originalHTML = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise spinner-border spinner-border-sm me-1"></i>Mendeteksi...';
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    document.getElementById('latitude').value = lat.toFixed(6);
                    document.getElementById('longitude').value = lng.toFixed(6);
                    
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 15);
                    updateJarakKampus(lat, lng);
                    scheduleReverseGeocode(lat, lng);
                    
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                    
                    alert('Lokasi berhasil terdeteksi!');
                },
                function(error) {
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                    
                    let errorMsg = 'Gagal mendeteksi lokasi: ';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMsg += 'Izin lokasi ditolak. Aktifkan izin lokasi di browser Anda.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMsg += 'Informasi lokasi tidak tersedia.';
                            break;
                        case error.TIMEOUT:
                            errorMsg += 'Waktu request habis.';
                            break;
                        default:
                            errorMsg += 'Terjadi kesalahan yang tidak diketahui.';
                    }
                    alert(errorMsg);
                }
            );
        } else {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            alert('Browser Anda tidak mendukung Geolocation.');
        }
    });

    // Sinkronkan marker saat koordinat diubah manual
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    function handleManualCoordinateInput() {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);

        if (Number.isNaN(lat) || Number.isNaN(lng)) {
            return;
        }

        if (lat < -90 || lat > 90 || lng < -180 || lng > 180) {
            return;
        }

        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], map.getZoom());
        updateJarakKampus(lat, lng);
        scheduleReverseGeocode(lat, lng);
    }

    if (alamatAutoBtn) {
        alamatAutoBtn.addEventListener('click', function () {
            let lat = lastLatLng?.lat;
            let lng = lastLatLng?.lng;

            if ((!lat || !lng) && marker) {
                const position = marker.getLatLng();
                lat = position.lat;
                lng = position.lng;
            }

            if (lat && lng) {
                isManualAddress = false;
                scheduleReverseGeocode(lat, lng, true);
            }
        });
    }

    latInput.addEventListener('change', handleManualCoordinateInput);
    lngInput.addEventListener('change', handleManualCoordinateInput);
    latInput.addEventListener('blur', handleManualCoordinateInput);
    lngInput.addEventListener('blur', handleManualCoordinateInput);

    // ========== FASILITAS DROPDOWN (MULTI-SELECT) ==========
    const fasilitasCheckboxes = document.querySelectorAll('.fasilitas-checkbox');
    const fasilitasInput = document.getElementById('fasilitas');
    const fasilitasSearch = document.getElementById('fasilitas_search');
    const fasilitasSelectedCount = document.getElementById('fasilitas_selected_count');
    const selectAllFasilitasBtn = document.getElementById('select_all_fasilitas');
    const clearAllFasilitasBtn = document.getElementById('clear_all_fasilitas');
    const fasilitasEmptyState = document.getElementById('fasilitas_empty_state');

    function updateFasilitasCounter() {
        const selectedCount = Array.from(fasilitasCheckboxes).filter(checkbox => checkbox.checked).length;
        fasilitasSelectedCount.textContent = `${selectedCount} dipilih`;
    }

    function filterFasilitasOptions() {
        const keyword = (fasilitasSearch.value || '').trim().toLowerCase();
        let visibleCount = 0;
        Array.from(fasilitasCheckboxes).forEach(checkbox => {
            const item = checkbox.closest('.fasilitas-option-item');
            const text = checkbox.value.toLowerCase();
            const isVisible = !(keyword !== '' && !text.includes(keyword));
            item.style.display = isVisible ? '' : 'none';
            if (isVisible) {
                visibleCount++;
            }
        });

        if (fasilitasEmptyState) {
            fasilitasEmptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    function syncFasilitasInput() {
        const selectedValues = Array.from(fasilitasCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value.trim())
            .filter(Boolean);
        fasilitasInput.value = selectedValues.join(', ');
        updateFasilitasCounter();
        if (!isRestoringDraft) {
            scheduleDraftSave();
        }
    }

    if (fasilitasCheckboxes.length > 0 && fasilitasInput) {
        Array.from(fasilitasCheckboxes).forEach(checkbox => {
            checkbox.addEventListener('change', syncFasilitasInput);
        });

        if (selectAllFasilitasBtn) {
            selectAllFasilitasBtn.addEventListener('click', function () {
                Array.from(fasilitasCheckboxes).forEach(checkbox => {
                    const item = checkbox.closest('.fasilitas-option-item');
                    if (!item || item.style.display !== 'none') {
                        checkbox.checked = true;
                    }
                });
                syncFasilitasInput();
            });
        }

        if (clearAllFasilitasBtn) {
            clearAllFasilitasBtn.addEventListener('click', function () {
                Array.from(fasilitasCheckboxes).forEach(checkbox => {
                    const item = checkbox.closest('.fasilitas-option-item');
                    if (!item || item.style.display !== 'none') {
                        checkbox.checked = false;
                    }
                });
                syncFasilitasInput();
            });
        }
        if (fasilitasSearch) {
            fasilitasSearch.addEventListener('input', filterFasilitasOptions);
        }
        syncFasilitasInput();
        filterFasilitasOptions();

        const laundryForm = fasilitasInput.closest('form');
        if (laundryForm) {
            laundryForm.addEventListener('submit', function (event) {
                syncFasilitasInput();
                if (!fasilitasInput.value.trim()) {
                    event.preventDefault();
                    alert('Pilih minimal satu fasilitas.');
                }
            });
        }
    }

    restoreDraft();
});

// Modal functions
function openImageModal(src) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    modal.style.display = 'block';
    modalImg.src = src;
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) closeImageModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeImageModal();
});
</script>

<style>
    .form-control:focus, .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
    .layanan-item {
        transition: all 0.3s ease;
    }
    .layanan-item:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .input-group-text { 
        transition: all 0.3s ease; 
    }
    .input-group:focus-within .input-group-text {
        border-color: #86b7fe;
        background-color: #e7f1ff;
    }
    .modal-fullscreen {
        position: fixed; 
        z-index: 9999; 
        left: 0; 
        top: 0;
        width: 100%; 
        height: 100%;
        background-color: rgba(0, 0, 0, 0.95);
    }
    .modal-close {
        position: absolute; 
        top: 20px; 
        right: 40px;
        color: #fff; 
        font-size: 40px; 
        cursor: pointer;
    }
    .modal-content-img {
        max-width: 90%; 
        max-height: 90%;
        position: absolute; 
        top: 50%; 
        left: 50%;
        transform: translate(-50%, -50%);
    }
    
    /* Leaflet Map Custom Styles */
    .leaflet-container {
        font-family: inherit;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .spinner-border {
        animation: spin 0.75s linear infinite;
    }

    .fasilitas-panel {
        max-height: 220px;
        overflow-y: auto;
        background: #f8faff !important;
    }

    .fasilitas-chip-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
    }

    .fasilitas-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.7rem;
        border: 1px solid #d8e2f1;
        border-radius: 999px;
        background: #ffffff;
        color: #2c3e50;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .fasilitas-chip:hover {
        border-color: #86b7fe;
        background: #eef5ff;
        transform: translateY(-1px);
    }

    .fasilitas-checkbox:checked + .fasilitas-chip {
        border-color: #0d6efd;
        background: #0d6efd;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(13, 110, 253, 0.25);
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TA\spk_kontrakan\resources\views/laundry/edit.blade.php ENDPATH**/ ?>