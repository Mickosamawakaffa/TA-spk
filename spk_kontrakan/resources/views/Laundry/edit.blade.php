@extends('layouts.admin')

@section('title', 'Edit Laundry')

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
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('laundry.index') }}">Laundry</a></li>
            <li class="breadcrumb-item active">Edit Data</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="page-header">
        <h2 class="mb-0">✏️ Edit Laundry</h2>
        <p class="mb-0 opacity-95">Update informasi laundry <strong>{{ $laundry->nama }}</strong></p>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-12">
            <div class="card form-card">
                <div class="card-body p-4">
                    <form action="{{ route('laundry.update', $laundry->id) }}" method="POST" enctype="multipart/form-data" id="laundryForm">
                        @csrf
                        @method('PUT')
                        
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
                                            class="form-control border-start-0 @error('nama') is-invalid @enderror" 
                                            id="nama" 
                                            placeholder="Contoh: Laundry Express 88"
                                            value="{{ old('nama', $laundry->nama) }}"
                                            required
                                        >
                                        @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                            class="form-control border-start-0 @error('alamat') is-invalid @enderror" 
                                            id="alamat" 
                                            rows="3"
                                            required
                                        >{{ old('alamat', $laundry->alamat) }}</textarea>
                                        @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                            class="form-control border-start-0 @error('jarak') is-invalid @enderror" 
                                            id="jarak" 
                                            placeholder="Otomatis dihitung"
                                            value="{{ old('jarak', $laundry->jarak) }}"
                                            min="0"
                                            readonly
                                            required
                                        >
                                        <span class="input-group-text bg-light">meter</span>
                                        @error('jarak')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Jarak otomatis dihitung dari koordinat laundry ke kampus
                                    </small>
                                </div>

                                <!-- Fasilitas -->
                                <div class="col-md-6">
                                    <label for="fasilitas" class="form-label fw-semibold">
                                        Fasilitas <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-list-check text-primary"></i>
                                        </span>
                                        <input 
                                            type="text" 
                                            name="fasilitas" 
                                            class="form-control border-start-0 @error('fasilitas') is-invalid @enderror" 
                                            id="fasilitas" 
                                            placeholder="Cuci + Setrika"
                                            value="{{ old('fasilitas', $laundry->fasilitas) }}"
                                            required
                                        >
                                        @error('fasilitas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                        <strong>Tips:</strong> Klik pada peta untuk memperbarui lokasi, atau gunakan tombol "Deteksi Lokasi Saya" untuk mendapatkan koordinat otomatis.
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
                                            value="{{ old('latitude', $laundry->latitude) }}"
                                            required
                                            readonly
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
                                            value="{{ old('longitude', $laundry->longitude) }}"
                                            required
                                            readonly
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
                                @if($laundry->layanan && $laundry->layanan->count() > 0)
                                    @foreach($laundry->layanan as $index => $layanan)
                                    <div class="layanan-item card border mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0 fw-semibold text-success">Layanan #{{ $index + 1 }}</h6>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-layanan" style="{{ $laundry->layanan->count() > 1 ? '' : 'display: none;' }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                            
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        Jenis Layanan <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="layanan[{{ $index }}][jenis_layanan]" class="form-select" required>
                                                        <option value="">-- Pilih Jenis --</option>
                                                        <option value="reguler" {{ $layanan->jenis_layanan == 'reguler' ? 'selected' : '' }}>🕐 Reguler (Normal)</option>
                                                        <option value="express" {{ $layanan->jenis_layanan == 'express' ? 'selected' : '' }}>⚡ Express (Cepat)</option>
                                                        <option value="kilat" {{ $layanan->jenis_layanan == 'kilat' ? 'selected' : '' }}>🚀 Kilat (Super Cepat)</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        Nama Paket <span class="text-danger">*</span>
                                                    </label>
                                                    <input 
                                                        type="text" 
                                                        name="layanan[{{ $index }}][nama_paket]" 
                                                        class="form-control" 
                                                        placeholder="Paket Reguler"
                                                        value="{{ $layanan->nama_paket }}"
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
                                                            name="layanan[{{ $index }}][harga]" 
                                                            class="form-control" 
                                                            placeholder="7000"
                                                            value="{{ $layanan->harga }}"
                                                            min="0"
                                                            required
                                                        >
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        Estimasi Selesai (Jam) <span class="text-danger">*</span>
                                                    </label>
                                                    <input 
                                                        type="number" 
                                                        name="layanan[{{ $index }}][estimasi_selesai]" 
                                                        class="form-control" 
                                                        placeholder="24"
                                                        value="{{ $layanan->estimasi_selesai }}"
                                                        min="1"
                                                        required
                                                    >
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label fw-semibold">
                                                        Deskripsi <span class="text-muted">(Opsional)</span>
                                                    </label>
                                                    <textarea 
                                                        name="layanan[{{ $index }}][deskripsi]" 
                                                        class="form-control" 
                                                        rows="2"
                                                        placeholder="Deskripsi singkat paket ini..."
                                                    >{{ $layanan->deskripsi }}</textarea>
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label fw-semibold">
                                                        Status <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="layanan[{{ $index }}][status]" class="form-select" required>
                                                        <option value="aktif" {{ $layanan->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                                        <option value="nonaktif" {{ $layanan->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
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
                                                        <option value="reguler">🕐 Reguler (Normal)</option>
                                                        <option value="express">⚡ Express (Cepat)</option>
                                                        <option value="kilat">🚀 Kilat (Super Cepat)</option>
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
                                                        Kecepatan <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input 
                                                            type="number" 
                                                            name="layanan[0][kecepatan]" 
                                                            class="form-control" 
                                                            placeholder="24"
                                                            min="1"
                                                            required
                                                        >
                                                        <select name="layanan[0][satuan_kecepatan]" class="form-select" style="max-width: 100px;">
                                                            <option value="jam">Jam</option>
                                                            <option value="hari">Hari</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @error('layanan')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
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
                                                @if($laundry->foto)
                                                    <img 
                                                        src="{{ asset('uploads/Laundry/' . $laundry->foto) }}" 
                                                        alt="{{ $laundry->nama }}" 
                                                        class="img-fluid rounded"
                                                        style="max-height: 150px; cursor: pointer;"
                                                        onclick="openImageModal(this.src)"
                                                        id="current-foto"
                                                    >
                                                @else
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
                                                @endif
                                                                                                
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
                                                    class="form-control form-control-sm @error('foto') is-invalid @enderror" 
                                                    id="foto" 
                                                    accept="image/jpeg,image/png,image/jpg"
                                                >
                                                <small class="text-muted d-block mt-1">Format: JPG, PNG, JPEG (Max 2MB)</small>
                                                @error('foto')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                
                                                @if($laundry->foto)
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
                                                @endif
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
                                Anda sedang mengedit data laundry <strong>{{ $laundry->nama }}</strong>. 
                                Pastikan semua perubahan sudah benar sebelum menyimpan.
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                            <a href="{{ route('laundry.index') }}" class="btn btn-light px-4">
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
                                <strong>Dibuat:</strong> {{ $laundry->created_at->format('d M Y, H:i') }} WIB
                            </p>
                            @if($laundry->updated_at != $laundry->created_at)
                            <p class="mb-0 small text-muted">
                                <strong>Terakhir diupdate:</strong> {{ $laundry->updated_at->format('d M Y, H:i') }} WIB
                            </p>
                            @endif
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
    
    // ========== LAYANAN MANAGEMENT ==========
    let layananCount = {{ $laundry->layanan ? $laundry->layanan->count() : 1 }};
    const container = document.getElementById('layananContainer');
    const addButton = document.getElementById('addLayanan');
    
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
                            <option value="reguler">🕐 Reguler (Normal)</option>
                            <option value="express">⚡ Express (Cepat)</option>
                            <option value="kilat">🚀 Kilat (Super Cepat)</option>
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
                            placeholder="Paket Reguler"
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
                            Estimasi Selesai (Jam) <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="layanan[${layananCount}][estimasi_selesai]" 
                            class="form-control" 
                            placeholder="24"
                            min="1"
                            required
                        >
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

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Rating (0-5) <span class="text-muted">(Opsional)</span>
                        </label>
                        <input 
                            type="number" 
                            name="layanan[${layananCount}][rating]" 
                            class="form-control" 
                            placeholder="4.5"
                            min="0"
                            max="5"
                            step="0.1"
                        >
                        <small class="text-muted">Rating rata-rata paket ini (0-5)</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Waktu Proses (Jam) <span class="text-muted">(Opsional)</span>
                        </label>
                        <input 
                            type="number" 
                            name="layanan[${layananCount}][waktu_proses]" 
                            class="form-control" 
                            placeholder="24"
                            min="1"
                        >
                        <small class="text-muted">Waktu rata-rata proses paket ini</small>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select name="layanan[${layananCount}][status]" class="form-select" required>
                            <option value="aktif" selected>Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(newItem);
        layananCount++;
        updateRemoveButtons();
    });
    
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-layanan')) {
            e.target.closest('.layanan-item').remove();
            updateRemoveButtons();
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
    const existingLat = {{ $laundry->latitude ?? '-7.797068' }};
    const existingLng = {{ $laundry->longitude ?? '110.370529' }};
    
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
    });
    
    // Update marker saat klik di peta
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        marker.setLatLng([lat, lng]);
        
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);
        updateJarakKampus(lat, lng);
        
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
</style>
@endsection
