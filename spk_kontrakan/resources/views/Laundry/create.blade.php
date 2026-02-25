@extends('layouts.admin')

@section('title', 'Tambah Laundry')

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
            <li class="breadcrumb-item active">Tambah Data</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="page-header">
        <h2 class="mb-0">🧺 Tambah Laundry Baru</h2>
        <p class="mb-0 opacity-95">Lengkapi formulir di bawah untuk menambahkan data laundry</p>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-12">
            <div class="card form-card">
                <div class="card-body p-4">
                    <form action="{{ route('laundry.store') }}" method="POST" enctype="multipart/form-data" id="laundryForm">
                        @csrf
                        
                        <!-- Informasi Dasar Section -->
                        <div class="mb-4">
                            <h5 class="section-header mb-0">
                                <i class="bi bi-info-circle me-2" style="color: #667eea;"></i>Informasi Dasar
                            </h5>
                            
                            <div class="row g-3">
                                <!-- Nama Laundry -->
                                <div class="col-md-12">
                                    <label for="nama" class="form-label fw-semibold">
                                        Nama Laundry <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-basket3" style="color: #667eea;"></i>
                                        </span>
                                        <input 
                                            type="text" 
                                            name="nama" 
                                            class="form-control border-start-0 @error('nama') is-invalid @enderror" 
                                            id="nama" 
                                            placeholder="Contoh: Laundry Express 88"
                                            value="{{ old('nama') }}"
                                            required
                                        >
                                        @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Masukkan nama laundry yang mudah dikenali</small>
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
                                            placeholder="Contoh: Jl. Gejayan No. 45, Condongcatur, Sleman, Yogyakarta"
                                            required
                                        >{{ old('alamat') }}</textarea>
                                        @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Alamat lengkap beserta patokan jika ada</small>
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
                                            value="{{ old('jarak') }}"
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
                                            placeholder="Contoh: Cuci + Setrika"
                                            value="{{ old('fasilitas') }}"
                                            required
                                        >
                                        @error('fasilitas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Fasilitas yang disediakan laundry ini</small>
                                </div>
                            </div>
                        </div>

                        <!-- Lokasi Koordinat Section -->
                        <div class="mb-4">
                            <h5 class="section-header mb-0">
                                <i class="bi bi-pin-map-fill text-danger me-2"></i>Lokasi & Koordinat
                            </h5>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Tips:</strong> Klik pada peta untuk menentukan lokasi, atau gunakan tombol "Deteksi Lokasi Saya" untuk mendapatkan koordinat otomatis.
                            </div>

                            <div class="row g-3">
                                <!-- Latitude -->
                                <div class="col-md-6">
                                    <label for="latitude" class="form-label fw-semibold">
                                        Latitude <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-geo text-danger"></i>
                                        </span>
                                        <input 
                                            type="text" 
                                            name="latitude" 
                                            class="form-control @error('latitude') is-invalid @enderror" 
                                            id="latitude" 
                                            placeholder="-7.7828012"
                                            value="{{ old('latitude') }}"
                                            readonly
                                            required
                                        >
                                        @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Longitude -->
                                <div class="col-md-6">
                                    <label for="longitude" class="form-label fw-semibold">
                                        Longitude <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-geo text-danger"></i>
                                        </span>
                                        <input 
                                            type="text" 
                                            name="longitude" 
                                            class="form-control @error('longitude') is-invalid @enderror" 
                                            id="longitude" 
                                            placeholder="110.4086598"
                                            value="{{ old('longitude') }}"
                                            readonly
                                            required
                                        >
                                        @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Tombol Deteksi Lokasi -->
                                <div class="col-12">
                                    <button type="button" id="detectLocation" class="btn btn-outline-primary">
                                        <i class="bi bi-crosshair me-2"></i>Deteksi Lokasi Saya
                                    </button>
                                    <small class="text-muted d-block mt-2">Atau klik langsung pada peta di bawah</small>
                                </div>

                                <!-- Map Container -->
                                <div class="col-12">
                                    <div id="map" style="height: 400px; border-radius: 8px; border: 2px solid #dee2e6;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Layanan Section (Dynamic) -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom-2" style="border-bottom: 2px solid #667eea;">
                                <h5 class="section-header mb-0">
                                    <i class="bi bi-gear text-primary me-2"></i>Jenis Layanan
                                </h5>
                                <button type="button" class="btn btn-sm btn-primary" id="addLayanan">
                                    <i class="bi bi-plus-circle me-1"></i>Tambah Layanan
                                </button>
                            </div>
                            
                            <div id="layananContainer">
                                <!-- Layanan Item 1 (Default) -->
                                <div class="layanan-item card border mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0 fw-semibold text-success">Layanan #1</h6>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-layanan" style="display: none;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <div class="row g-3">
                                            <!-- Jenis Layanan -->
                                            <div class="col-md-6">
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

                                            <!-- Nama Paket -->
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">
                                                    Nama Paket <span class="text-danger">*</span>
                                                </label>
                                                <input 
                                                    type="text" 
                                                    name="layanan[0][nama_paket]" 
                                                    class="form-control" 
                                                    placeholder="Paket Reguler"
                                                    required
                                                >
                                            </div>

                                            <!-- Harga -->
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">
                                                    Harga (Rp) <span class="text-danger">*</span>
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
                                                </div>
                                            </div>

                                            <!-- Estimasi Selesai -->
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">
                                                    Estimasi Selesai (Jam) <span class="text-danger">*</span>
                                                </label>
                                                <input 
                                                    type="number" 
                                                    name="layanan[0][estimasi_selesai]" 
                                                    class="form-control" 
                                                    placeholder="24"
                                                    min="1"
                                                    required
                                                >
                                            </div>

                                            <!-- Deskripsi -->
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

                                            <!-- Status -->
                                            <div class="col-md-12">
                                                <label class="form-label fw-semibold">
                                                    Status <span class="text-danger">*</span>
                                                </label>
                                                <select name="layanan[0][status]" class="form-select" required>
                                                    <option value="aktif" selected>Aktif</option>
                                                    <option value="nonaktif">Nonaktif</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @error('layanan')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Upload Foto Section -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 pb-2 border-bottom">
                                <i class="bi bi-camera text-success me-2"></i>Upload Foto
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="foto" class="form-label fw-semibold">
                                        Foto Laundry <span class="text-muted">(Opsional)</span>
                                    </label>
                                    <input 
                                        type="file" 
                                        name="foto" 
                                        class="form-control @error('foto') is-invalid @enderror" 
                                        id="foto" 
                                        accept="image/jpeg,image/png,image/jpg"
                                    >
                                    <small class="text-muted">Format: JPG, PNG, JPEG (Maksimal 2MB). Kosongkan jika tidak ingin upload foto.</small>
                                    @error('foto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Preview Foto -->
                                <div class="col-md-12">
                                    <div id="preview-foto-container" style="display: none;">
                                        <label class="form-label fw-semibold">Preview Foto:</label>
                                        <div class="border rounded p-2 bg-light">
                                            <img id="preview-foto" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 8px; display: block; margin: 0 auto;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                            <a href="{{ route('laundry.index') }}" class="btn btn-light px-4">
                                <i class="bi bi-arrow-left me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check-circle me-2"></i>Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card border-0 bg-light mt-3">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="bi bi-info-circle text-success" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-2">Tips Pengisian:</h6>
                            <ul class="mb-0 small text-muted">
                                <li>Minimal harus ada 1 jenis layanan</li>
                                <li>Klik pada peta untuk menentukan lokasi laundry</li>
                                <li>Koordinat akan terisi otomatis saat klik peta</li>
                                <li>Reguler: Layanan normal dengan harga standar</li>
                                <li>Express: Layanan cepat dengan harga lebih tinggi</li>
                                <li>Kilat: Layanan super cepat dengan harga premium</li>
                            </ul>
                        </div>
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
    
    // ========== MAP FUNCTIONALITY ==========
    // Default center: Semarang, Central Java
    const defaultLat = -6.966667;
    const defaultLng = 110.416664;
    
    // Initialize map
    const map = L.map('map').setView([defaultLat, defaultLng], 13);
    
    // Add tile layer (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Marker variable
    let marker;
    
    // Function to add/update marker
    function addMarker(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);
            
            // Update coordinates when marker is dragged
            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                updateCoordinates(position.lat, position.lng);
            });
        }
        
        // Center map to marker
        map.setView([lat, lng], 15);
        
        // Update input fields
        updateCoordinates(lat, lng);
    }
    
    // Function to update coordinate inputs
    function updateCoordinates(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
        
        // Hitung jarak ke kampus otomatis
        updateJarakKampus(lat, lng);
    }
    
    // Click on map to add marker
    map.on('click', function(e) {
        addMarker(e.latlng.lat, e.latlng.lng);
    });
    
    // Detect user location button
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
                    
                    addMarker(lat, lng);
                    
                    button.disabled = false;
                    button.innerHTML = originalHTML;
                    
                    // Show success message
                    alert('Lokasi berhasil terdeteksi!');
                },
                function(error) {
                    button.disabled = false;
                    button.innerHTML = originalHTML;
                    
                    let errorMsg = 'Gagal mendeteksi lokasi. ';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMsg += 'Izinkan akses lokasi di browser Anda.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMsg += 'Informasi lokasi tidak tersedia.';
                            break;
                        case error.TIMEOUT:
                            errorMsg += 'Waktu permintaan habis.';
                            break;
                        default:
                            errorMsg += 'Terjadi kesalahan.';
                    }
                    alert(errorMsg);
                }
            );
        } else {
            button.disabled = false;
            button.innerHTML = originalHTML;
            alert('Browser Anda tidak mendukung Geolocation.');
        }
    });
    
    // ========== LAYANAN FUNCTIONALITY ==========
    let layananCount = 1;
    const container = document.getElementById('layananContainer');
    const addButton = document.getElementById('addLayanan');
    
    // Tambah Layanan
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
    
    // Hapus Layanan
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-layanan')) {
            e.target.closest('.layanan-item').remove();
            updateRemoveButtons();
        }
    });
    
    // Update visibility tombol hapus
    function updateRemoveButtons() {
        const items = container.querySelectorAll('.layanan-item');
        items.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-layanan');
            if (items.length > 1) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }
    
    // ========== FOTO PREVIEW ==========
    const fotoInput = document.getElementById('foto');
    const previewFoto = document.getElementById('preview-foto');
    const previewContainer = document.getElementById('preview-foto-container');
    
    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            if (file.size > 2097152) {
                alert('Ukuran file maksimal 2MB!');
                this.value = '';
                previewContainer.style.display = 'none';
                return;
            }
            
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                alert('Hanya file JPG, PNG, dan JPEG yang diperbolehkan!');
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
    
    /* Leaflet map styling */
    .leaflet-container {
        font-family: inherit;
    }
    
    .leaflet-popup-content-wrapper {
        border-radius: 8px;
    }
</style>
@endsection