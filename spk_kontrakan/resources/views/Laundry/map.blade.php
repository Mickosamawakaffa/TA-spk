@extends('layouts.admin')

@section('title', 'Peta Lokasi Laundry')

@section('content')
<div class="container-fluid px-4">
    <style>
        .header-laundry-map {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
        }
        
        .header-laundry-map h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .header-laundry-map p {
            opacity: 0.95;
            margin-bottom: 0;
        }
        
        .btn-map-action {
            background: white;
            color: #667eea;
            font-weight: 600;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-map-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
            color: #764ba2;
        }
    </style>
    
    <!-- Header Section -->
    <div class="header-laundry-map">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h2 class="mb-1">🗺️ Peta Lokasi Laundry</h2>
                <p class="mb-0">Lihat semua lokasi laundry di peta interaktif</p>
            </div>
            <div class="d-flex gap-2 w-100 w-md-auto">
                <a href="{{ route('laundry.index') }}" class="btn btn-map-action px-4 flex-fill flex-md-grow-0">
                    <i class="bi bi-list-ul me-2"></i>Lihat List
                </a>
                <a href="{{ route('laundry.create') }}" class="btn btn-map-action px-4 flex-fill flex-md-grow-0">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Laundry
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form action="{{ route('laundry.map') }}" method="GET" id="filterForm">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-search me-1"></i>Cari Nama/Alamat
                        </label>
                        <input 
                            type="text" 
                            name="search" 
                            class="form-control" 
                            placeholder="Ketik untuk mencari..."
                            value="{{ $filters['search'] ?? '' }}"
                        >
                    </div>

                    <!-- Filter Harga Min -->
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-currency-dollar me-1"></i>Harga Min
                        </label>
                        <input 
                            type="number" 
                            name="harga_min" 
                            class="form-control" 
                            placeholder="0"
                            value="{{ $filters['harga_min'] ?? '' }}"
                            min="0"
                        >
                    </div>

                    <!-- Filter Harga Max -->
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-currency-dollar me-1"></i>Harga Max
                        </label>
                        <input 
                            type="number" 
                            name="harga_max" 
                            class="form-control" 
                            placeholder="50000"
                            value="{{ $filters['harga_max'] ?? '' }}"
                            min="0"
                        >
                    </div>

                    <!-- Filter Jarak -->
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-pin-map me-1"></i>Jarak
                        </label>
                        <select name="jarak" class="form-select">
                            <option value="">Semua Jarak</option>
                            <option value="dekat" {{ ($filters['jarak'] ?? '') == 'dekat' ? 'selected' : '' }}>
                                < 500m (Dekat)
                            </option>
                            <option value="sedang" {{ ($filters['jarak'] ?? '') == 'sedang' ? 'selected' : '' }}>
                                500m - 1km (Sedang)
                            </option>
                            <option value="jauh" {{ ($filters['jarak'] ?? '') == 'jauh' ? 'selected' : '' }}>
                                > 1km (Jauh)
                            </option>
                        </select>
                    </div>

                    <!-- Filter Jenis Layanan -->
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">
                            <i class="bi bi-speedometer2 me-1"></i>Jenis Layanan
                        </label>
                        <select name="jenis_layanan" class="form-select">
                            <option value="">Semua Layanan</option>
                            <option value="express" {{ ($filters['jenis_layanan'] ?? '') == 'express' ? 'selected' : '' }}>
                                ⚡ Express
                            </option>
                            <option value="reguler" {{ ($filters['jenis_layanan'] ?? '') == 'reguler' ? 'selected' : '' }}>
                                🕐 Reguler
                            </option>
                            <option value="kilat" {{ ($filters['jenis_layanan'] ?? '') == 'kilat' ? 'selected' : '' }}>
                                🚀 Kilat
                            </option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel me-2"></i>Terapkan Filter
                        </button>
                        <a href="{{ route('laundry.map') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </a>
                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 ms-auto">
                            <i class="bi bi-pin-map-fill me-1"></i>
                            <strong>{{ $laundry->count() }}</strong> Lokasi Ditemukan
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Map Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0" style="height: 600px; position: relative;">
            <!-- Map Container -->
            <div id="map" style="width: 100%; height: 100%; border-radius: 0.375rem;"></div>
            
            <!-- Loading Overlay -->
            <div id="mapLoading" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.9); display: flex; align-items: center; justify-content: center; z-index: 1000; border-radius: 0.375rem;">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="text-muted">Memuat Peta...</h5>
                </div>
            </div>

            <!-- Map Legend -->
            <div style="position: absolute; top: 20px; right: 20px; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 999; max-width: 200px;">
                <h6 class="fw-bold mb-3" style="font-size: 14px;">
                    <i class="bi bi-info-circle me-2"></i>Legenda
                </h6>
                <div class="d-flex flex-column gap-2" style="font-size: 12px;">
                    <div class="d-flex align-items-center">
                        <div style="width: 20px; height: 20px; background: #198754; border-radius: 50%; margin-right: 10px;"></div>
                        <span>< 500m</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div style="width: 20px; height: 20px; background: #ffc107; border-radius: 50%; margin-right: 10px;"></div>
                        <span>500m - 1km</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div style="width: 20px; height: 20px; background: #dc3545; border-radius: 50%; margin-right: 10px;"></div>
                        <span>> 1km</span>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            @if($laundry->isEmpty())
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; z-index: 1001;">
                <i class="bi bi-inbox text-muted mb-3" style="font-size: 4rem;"></i>
                <h5 class="text-muted mb-2">Tidak ada lokasi ditemukan</h5>
                <p class="text-muted mb-3">
                    @if(request()->hasAny(['search', 'harga_min', 'harga_max', 'jarak', 'jenis_layanan']))
                        Coba ubah filter pencarian Anda
                    @else
                        Belum ada data laundry dengan koordinat
                    @endif
                </p>
                @if(request()->hasAny(['search', 'harga_min', 'harga_max', 'jarak', 'jenis_layanan']))
                    <a href="{{ route('laundry.map') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset Filter
                    </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        document.getElementById('mapLoading').style.display = 'none';
    }, 500);

    const locations = @json($laundry);

    let centerLat = -6.2088;
    let centerLng = 106.8456;
    let zoomLevel = 12;

    if (locations.length > 0 && locations[0].latitude && locations[0].longitude) {
        centerLat = parseFloat(locations[0].latitude);
        centerLng = parseFloat(locations[0].longitude);
        zoomLevel = 13;
    }

    const map = L.map('map').setView([centerLat, centerLng], zoomLevel);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    const markers = L.markerClusterGroup({
        chunkedLoading: true,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: true,
        zoomToBoundsOnClick: true,
        maxClusterRadius: 50
    });

    function getMarkerColor(jarak) {
        if (jarak < 500) return '#198754';
        if (jarak <= 1000) return '#ffc107';
        return '#dc3545';
    }

    function createCustomIcon(color) {
        return L.divIcon({
            className: 'custom-marker',
            html: `
                <div style="position: relative; width: 35px; height: 35px;">
                    <div style="width: 35px; height: 35px; background: ${color}; border: 3px solid white; border-radius: 50%; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-basket3-fill" style="color: white; font-size: 16px;"></i>
                    </div>
                    <div style="position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 8px solid ${color};"></div>
                </div>
            `,
            iconSize: [35, 43],
            iconAnchor: [17, 43],
            popupAnchor: [0, -43]
        });
    }

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    locations.forEach(function(location) {
        if (!location.latitude || !location.longitude) return;

        const lat = parseFloat(location.latitude);
        const lng = parseFloat(location.longitude);

        if (isNaN(lat) || isNaN(lng)) return;

        const markerColor = getMarkerColor(location.jarak);
        const customIcon = createCustomIcon(markerColor);
        const marker = L.marker([lat, lng], { icon: customIcon });

        let layananHtml = '';
        if (location.layanan && location.layanan.length > 0) {
            layananHtml = location.layanan.map(function(layanan) {
                const jenisIcon = {
                    'express': '⚡',
                    'reguler': '🕐',
                    'kilat': '🚀'
                };
                return `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; background: #f8f9fa; border-radius: 5px; margin-bottom: 5px;">
                        <div>
                            <strong style="color: #198754;">${jenisIcon[layanan.jenis_layanan] || ''} ${layanan.jenis_layanan.charAt(0).toUpperCase() + layanan.jenis_layanan.slice(1)}</strong>
                            <br>
                            <small style="color: #6c757d;">⏱️ ${layanan.kecepatan} ${layanan.satuan_kecepatan}</small>
                        </div>
                        <strong style="color: #198754; font-size: 16px;">${formatRupiah(layanan.harga)}/kg</strong>
                    </div>
                `;
            }).join('');
        } else {
            layananHtml = '<p style="color: #6c757d; font-style: italic;">Tidak ada layanan tersedia</p>';
        }

        let fotoHtml = '';
        if (location.foto) {
            fotoHtml = `
                <img 
                    src="/uploads/Laundry/${location.foto}" 
                    alt="${location.nama}"
                    style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 10px;"
                    onerror="this.style.display='none'"
                >
            `;
        }

        let fasilitasHtml = '';
        if (location.fasilitas) {
            fasilitasHtml = `
                <div style="margin-top: 10px; padding: 10px; background: #e7f3ff; border-left: 3px solid #0d6efd; border-radius: 5px;">
                    <strong style="color: #0d6efd;"><i class="bi bi-star-fill"></i> Fasilitas:</strong>
                    <p style="margin: 5px 0 0 0; color: #495057; font-size: 13px;">${location.fasilitas}</p>
                </div>
            `;
        }

        const popupContent = `
            <div style="min-width: 280px; max-width: 320px;">
                ${fotoHtml}
                
                <h5 style="margin: 0 0 5px 0; color: #212529; font-weight: 700;">
                    <i class="bi bi-basket3-fill" style="color: ${markerColor};"></i>
                    ${location.nama}
                </h5>
                
                <p style="margin: 0 0 10px 0; color: #6c757d; font-size: 13px;">
                    <i class="bi bi-geo-alt-fill" style="color: #dc3545;"></i>
                    ${location.alamat}
                </p>

                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <span style="background: ${markerColor}; color: white; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                        <i class="bi bi-pin-map-fill"></i> ${location.jarak}m
                    </span>
                </div>

                <div style="border-top: 2px solid #e9ecef; padding-top: 10px; margin-bottom: 10px;">
                    <strong style="color: #495057; font-size: 14px;">
                        <i class="bi bi-tag-fill" style="color: #198754;"></i> Layanan Tersedia:
                    </strong>
                    <div style="margin-top: 8px;">
                        ${layananHtml}
                    </div>
                </div>

                ${fasilitasHtml}

                <div style="display: flex; gap: 8px; margin-top: 15px;">
                    <a 
                        href="https://www.google.com/maps?q=${lat},${lng}" 
                        target="_blank"
                        class="btn btn-success btn-sm"
                        style="flex: 1; text-align: center; padding: 8px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 13px;"
                    >
                        <i class="bi bi-navigation-fill"></i> Navigasi
                    </a>
                    <a 
                        href="/laundry/${location.id}" 
                        class="btn btn-primary btn-sm"
                        style="flex: 1; text-align: center; padding: 8px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 13px;"
                    >
                        <i class="bi bi-eye-fill"></i> Detail
                    </a>
                </div>
            </div>
        `;

        marker.bindPopup(popupContent, {
            maxWidth: 350,
            className: 'custom-popup'
        });

        markers.addLayer(marker);
    });

    map.addLayer(markers);

    if (locations.length > 0) {
        const bounds = markers.getBounds();
        if (bounds.isValid()) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }

    L.control.scale({
        imperial: false,
        metric: true
    }).addTo(map);
});
</script>

<style>
.custom-marker {
    background: transparent;
    border: none;
}

.custom-popup .leaflet-popup-content-wrapper {
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    padding: 0;
}

.custom-popup .leaflet-popup-content {
    margin: 15px;
    line-height: 1.5;
}

.custom-popup .leaflet-popup-tip {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.marker-cluster-small {
    background-color: rgba(25, 135, 84, 0.6);
}

.marker-cluster-small div {
    background-color: rgba(25, 135, 84, 0.8);
    color: white;
    font-weight: bold;
}

.marker-cluster-medium {
    background-color: rgba(255, 193, 7, 0.6);
}

.marker-cluster-medium div {
    background-color: rgba(255, 193, 7, 0.8);
    color: white;
    font-weight: bold;
}

.marker-cluster-large {
    background-color: rgba(220, 53, 69, 0.6);
}

.marker-cluster-large div {
    background-color: rgba(220, 53, 69, 0.8);
    color: white;
    font-weight: bold;
}

@media (max-width: 768px) {
    #map {
        height: 400px !important;
    }
    
    .custom-popup .leaflet-popup-content {
        font-size: 12px;
    }
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}
</style>
@endsection
