@extends('layouts.admin')

@section('title', 'Detail Laundry')

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
        
        .detail-header {
            background: linear-gradient(135deg, #818cf8 0%, #667eea 50%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.25);
        }
        
        .detail-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .detail-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .detail-card-header {
            color: #667eea;
            font-size: 1.1rem;
            font-weight: 700;
            padding: 1.25rem;
            border-bottom: 2px solid #667eea;
            background: #f8f9fa;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #818cf8 0%, #667eea 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('laundry.index') }}">Laundry</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="detail-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">🧺 {{ $laundry->nama }}</h2>
                <p class="mb-0 opacity-95">Informasi lengkap laundry</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('laundry.edit', $laundry->id) }}" class="btn btn-edit">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="{{ route('laundry.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Info Card -->
        <div class="col-lg-9">
            <!-- FOTO SECTION (UPDATED) -->
            <div class="card detail-card">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-camera" style="color: #667eea;"></i> Foto Laundry
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($laundry->foto)
                        <!-- FOTO ADA -->
                        <div class="position-relative" style="height: 400px; overflow: hidden;">
                            <img 
                                src="{{ asset('uploads/laundry/' . $laundry->foto) }}" 
                                alt="{{ $laundry->nama }}" 
                                class="w-100 h-100"
                                style="object-fit: cover; cursor: pointer;"
                                onclick="openImageModal(this.src)"
                            >
                            <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-50 text-white p-3">
                                <small>
                                    <i class="bi bi-info-circle me-2"></i>
                                    Klik foto untuk melihat ukuran penuh
                                </small>
                            </div>
                        </div>
                    @else
                        <!-- PLACEHOLDER FOTO KOSONG (UPDATED - Purple Theme) -->
                        <div class="position-relative foto-placeholder" style="height: 400px; background: linear-gradient(135deg, #818cf8 0%, #667eea 50%, #764ba2 100%); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <!-- Decorative circles -->
                            <div class="decorative-circle" style="position: absolute; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.1); top: -50px; left: -50px;"></div>
                            <div class="decorative-circle" style="position: absolute; width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.1); bottom: -30px; right: -30px;"></div>
                            
                            <!-- Content -->
                            <div class="text-center position-relative" style="z-index: 2;">
                                <div class="mb-3" style="animation: float 3s ease-in-out infinite;">
                                    <i class="bi bi-image" style="font-size: 5rem; color: rgba(255,255,255,0.9); filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));"></i>
                                </div>
                                <h5 class="text-white mb-2 fw-bold">Foto Tidak Tersedia</h5>
                                <p class="text-white-50 small mb-0">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Belum ada foto yang diupload untuk laundry ini
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- MAPS SECTION -->
            @if($laundry->latitude && $laundry->longitude)
            <div class="card detail-card">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-map text-danger me-2"></i>Lokasi di Peta
                    </h5>
                    <a href="https://www.google.com/maps?q={{ $laundry->latitude }},{{ $laundry->longitude }}" 
                       target="_blank" 
                       class="btn btn-sm btn-danger">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Buka di Google Maps
                    </a>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 400px; width: 100%;"></div>
                </div>
            </div>
            @endif

            <!-- Informasi Utama Card -->
            <div class="card detail-card">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-info-circle text-success me-2"></i>Informasi Utama
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Nama -->
                        <div class="col-md-12">
                            <div class="d-flex align-items-start">
                                <div class="bg-success bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-basket3 text-success fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Nama Laundry</small>
                                    <h5 class="mb-0 fw-semibold">{{ $laundry->nama }}</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="col-md-12">
                            <div class="d-flex align-items-start">
                                <div class="bg-danger bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-geo-alt text-danger fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Alamat Lengkap</small>
                                    <p class="mb-0">{{ $laundry->alamat }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Jarak -->
                        <div class="col-md-12">
                            <div class="d-flex align-items-start">
                                <div class="bg-info bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-pin-map text-info fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Jarak ke Kampus</small>
                                    <h5 class="mb-0 fw-semibold text-info">
                                        {{ number_format($laundry->jarak, 0, ',', '.') }} meter
                                    </h5>
                                    <small class="text-muted">≈ {{ round($laundry->jarak / 1000, 2) }} km</small>
                                </div>
                            </div>
                        </div>

                        <!-- Layanan & Harga -->
                        <div class="col-md-12">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-list-check text-primary fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Jenis Layanan & Harga</small>
                                    @if($laundry->layanan && $laundry->layanan->isNotEmpty())
                                        <div class="table-responsive mt-2">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Jenis Layanan</th>
                                                        <th>Harga/kg</th>
                                                        <th>Kecepatan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($laundry->layanan as $layanan)
                                                    <tr>
                                                        <td>
                                                            <span class="badge bg-primary">
                                                                {{ ucfirst($layanan->jenis_layanan) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong class="text-success">
                                                                Rp {{ number_format($layanan->harga, 0, ',', '.') }}
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-warning text-dark">
                                                                {{ $layanan->kecepatan }} {{ $layanan->satuan_kecepatan }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="mb-0 text-muted">Belum ada layanan</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Fasilitas -->
                        @if($laundry->fasilitas)
                        <div class="col-md-12">
                            <div class="d-flex align-items-start">
                                <div class="bg-secondary bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-star text-secondary fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Fasilitas Tambahan</small>
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @foreach(explode(',', $laundry->fasilitas) as $fasilitas)
                                            <span class="badge bg-secondary bg-opacity-10 text-dark px-3 py-2">
                                                <i class="bi bi-check-circle me-1"></i>{{ trim($fasilitas) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Info -->
        <div class="col-lg-3">
            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-graph-up text-success me-2"></i>Ringkasan
                    </h6>
                    @if($laundry->layanan && $laundry->layanan->isNotEmpty())
                        @php
                            $minHarga = $laundry->layanan->min('harga');
                            $maxHarga = $laundry->layanan->max('harga');
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <span class="text-muted">Range Harga</span>
                            <strong class="text-success">
                                Rp {{ number_format($minHarga, 0, ',', '.') }} - Rp {{ number_format($maxHarga, 0, ',', '.') }}
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <span class="text-muted">Jumlah Layanan</span>
                            <span class="badge bg-primary">{{ $laundry->layanan->count() }} Jenis</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted">Status</span>
                        <span class="badge bg-success">Buka</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Kategori</span>
                        <strong>Laundry</strong>
                    </div>
                </div>
            </div>

            <!-- Timestamp Info -->
            <div class="card border-0 bg-light mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-clock-history text-success me-2"></i>Riwayat
                    </h6>
                    <div class="mb-3">
                        <small class="text-muted d-block">Dibuat</small>
                        <strong>{{ $laundry->created_at->format('d M Y, H:i') }} WIB</strong>
                    </div>
                    @if($laundry->updated_at != $laundry->created_at)
                    <div>
                        <small class="text-muted d-block">Terakhir Diupdate</small>
                        <strong>{{ $laundry->updated_at->format('d M Y, H:i') }} WIB</strong>
                        <small class="text-muted d-block mt-1">
                            ({{ $laundry->updated_at->diffForHumans() }})
                        </small>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-gear text-secondary me-2"></i>Aksi
                    </h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('laundry.edit', $laundry->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>Edit Data
                        </a>
                        
                        @if(auth()->user()->role == 'super_admin')
                        <form action="{{ route('laundry.destroy', $laundry->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus laundry {{ $laundry->nama }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash me-2"></i>Hapus Data
                            </button>
                        </form>
                        @else
                        <button type="button" class="btn btn-secondary w-100" disabled title="Hanya Super Admin yang dapat menghapus data">
                            <i class="bi bi-lock me-2"></i>Hapus Data (Terbatas)
                        </button>
                        @endif
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
    <div class="modal-caption" id="modalCaption"></div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
@if($laundry->latitude && $laundry->longitude)
// Initialize map
document.addEventListener('DOMContentLoaded', function() {
    const lat = {{ $laundry->latitude }};
    const lng = {{ $laundry->longitude }};
    
    const map = L.map('map').setView([lat, lng], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    const marker = L.marker([lat, lng]).addTo(map);
    marker.bindPopup('<b>{{ $laundry->nama }}</b><br>{{ $laundry->alamat }}').openPopup();
});
@endif

function openImageModal(src) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const caption = document.getElementById('modalCaption');
    
    modal.style.display = 'block';
    modalImg.src = src;
    caption.innerHTML = '{{ $laundry->nama }}';
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

document.getElementById('imageModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>

<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
    }

    /* ========== FOTO PLACEHOLDER ANIMATION ========== */
    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    .foto-placeholder {
        position: relative;
        animation: gradientShift 10s ease infinite;
        background-size: 200% 200%;
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .decorative-circle {
        animation: pulse 4s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 0.3;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.5;
        }
    }

    /* ========== MODAL STYLES ========== */
    .modal-fullscreen {
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.95);
        animation: fadeIn 0.3s;
    }

    .modal-close {
        position: absolute;
        top: 20px;
        right: 40px;
        color: #fff;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 10000;
        transition: 0.3s;
    }

    .modal-close:hover,
    .modal-close:focus {
        color: #bbb;
    }

    .modal-content-img {
        margin: auto;
        display: block;
        max-width: 90%;
        max-height: 90%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        animation: zoomIn 0.3s;
    }

    .modal-caption {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
        text-align: center;
        color: #ccc;
        padding: 10px 0;
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
    }

    @keyframes fadeIn {
        from {opacity: 0;}
        to {opacity: 1;}
    }

    @keyframes zoomIn {
        from {transform: translate(-50%, -50%) scale(0.5);}
        to {transform: translate(-50%, -50%) scale(1);}
    }
</style>
@endsection
