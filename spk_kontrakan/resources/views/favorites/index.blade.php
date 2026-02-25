@extends('layouts.app')

@section('title', 'Kontrakan Favorit Saya')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <style>
        .favorites-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7ff 0%, #fff5f7 50%, #f0f4ff 100%);
            position: relative;
        }
        
        /* Animated Background Blobs */
        .favorites-page::before,
        .favorites-page::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.3;
            animation: blob-float 20s infinite ease-in-out;
            z-index: 0;
        }
        
        .favorites-page::before {
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            top: -100px;
            right: -100px;
        }
        
        .favorites-page::after {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #f093fb, #f5576c);
            bottom: -100px;
            left: -100px;
            animation-delay: -10s;
        }
        
        @keyframes blob-float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-30px, 30px) scale(0.9); }
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 24px;
            padding: 2.5rem;
            color: white;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.4);
            z-index: 1;
        }
        
        .page-header::before {
            content: '💖';
            position: absolute;
            font-size: 200px;
            opacity: 0.1;
            top: -50px;
            right: -20px;
            animation: pulse 3s infinite ease-in-out;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .page-header h2 {
            font-weight: 800;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
            font-size: 2rem;
        }
        
        .page-header p {
            opacity: 0.95;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
            font-size: 1.05rem;
        }
        
        .stats-badge {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 0.75rem 1.25rem;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            border: 2px solid rgba(255,255,255,0.3);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .filter-tabs {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }
        
        .filter-tab {
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            background: white;
            border: 2px solid #e9ecef;
            color: #6c757d;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .filter-tab:hover {
            border-color: #667eea;
            color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.2);
        }
        
        .filter-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        
        .favorite-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 1;
        }
        
        .favorite-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 24px;
            padding: 2px;
            background: linear-gradient(135deg, #667eea, #764ba2, #f093fb);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .favorite-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px rgba(102, 126, 234, 0.25);
        }
        
        .favorite-card:hover::before {
            opacity: 1;
        }
        
        .favorite-card .card-img-wrapper {
            position: relative;
            height: 200px;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        
        .favorite-card .card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }
        
        .favorite-card:hover .card-img-wrapper img {
            transform: scale(1.15) rotate(2deg);
        }
        
        .favorite-card .card-img-wrapper .overlay-gradient {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 70%;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        }
        
        .favorite-card .badge-type {
            position: absolute;
            top: 1rem;
            left: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            backdrop-filter: blur(10px);
            z-index: 2;
        }
        
        .badge-kontrakan {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.95), rgba(118, 75, 162, 0.95));
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .badge-laundry {
            background: linear-gradient(135deg, rgba(79, 172, 254, 0.95), rgba(0, 242, 254, 0.95));
            color: white;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
        }
        
        .btn-remove-favorite {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border: none;
            color: #dc3545;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            z-index: 2;
        }
        
        .btn-remove-favorite:hover {
            background: #dc3545;
            color: white;
            transform: scale(1.15) rotate(10deg);
            box-shadow: 0 6px 25px rgba(220, 53, 69, 0.4);
        }
        
        .favorite-card .price-tag {
            position: absolute;
            bottom: 1rem;
            left: 1rem;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1rem;
            border-radius: 12px;
            font-weight: 700;
            color: #28a745;
            font-size: 0.95rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            z-index: 2;
        }
        
        .favorite-card .card-body {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .favorite-card .card-title {
            font-weight: 800;
            font-size: 1.15rem;
            margin-bottom: 0.5rem;
            color: #1a1a2e;
            line-height: 1.3;
        }
        
        .favorite-card .card-text {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .favorite-card .info-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .favorite-card .info-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.4rem 0.75rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        
        .info-badge-distance {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.15));
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }
        
        .info-badge-room {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 193, 7, 0.15));
            color: #d97706;
            border: 1px solid rgba(255, 193, 7, 0.2);
        }
        
        .favorite-card .card-actions {
            margin-top: auto;
            display: flex;
            gap: 0.5rem;
        }
        
        .favorite-card .btn-action {
            flex: 1;
            padding: 0.65rem 0.75rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
        }
        
        .btn-detail {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(102, 126, 234, 0.15));
            color: #667eea;
        }
        
        .btn-detail:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-booking {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
        }
        
        .btn-booking:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }
        
        .btn-whatsapp {
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: white;
            width: 45px;
            flex: 0 0 45px;
        }
        
        .btn-whatsapp:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.4);
            color: white;
        }
        
        .btn-remove-full {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.15));
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.2);
            border-radius: 12px;
            padding: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-remove-full:hover {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(220, 53, 69, 0.3);
        }
        
        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            position: relative;
            z-index: 1;
            overflow: hidden;
        }
        
        .empty-state::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-radius: 50%;
        }
        
        .empty-state::after {
            content: '';
            position: absolute;
            bottom: -100px;
            left: -100px;
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, rgba(240, 147, 251, 0.1), rgba(245, 87, 108, 0.1));
            border-radius: 50%;
        }
        
        .empty-state-content {
            position: relative;
            z-index: 1;
        }
        
        .empty-state i {
            font-size: 6rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
        }
        
        .empty-state h4 {
            color: #1a1a2e;
            font-weight: 800;
            font-size: 1.75rem;
            margin-bottom: 0.75rem;
        }
        
        .empty-state p {
            color: #6c757d;
            margin-bottom: 2rem;
            font-size: 1.05rem;
        }
        
        .btn-browse {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.875rem 2rem;
            border-radius: 50px;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .btn-browse:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        @media (max-width: 768px) {
            .page-header {
                padding: 1.75rem;
                border-radius: 20px;
            }
            
            .page-header h2 {
                font-size: 1.5rem;
            }
            
            .filter-tabs {
                justify-content: center;
            }
            
            .favorite-card .card-img-wrapper {
                height: 170px;
            }
            
            .stats-badge {
                font-size: 0.875rem;
                padding: 0.6rem 1rem;
            }
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2>
                    <i class="bi bi-heart-fill me-2"></i>
                    Kontrakan Favorit Saya
                </h2>
                <p>✨ Daftar kontrakan & laundry yang kamu simpan untuk dilihat nanti</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('welcome') }}" class="btn btn-light btn-sm me-2" style="border-radius: 12px; font-weight: 600;">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <span class="stats-badge">
                    <i class="bi bi-bookmark-heart-fill"></i>
                    <strong>{{ $favorites->total() }}</strong> Item
                </span>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <a href="{{ route('favorites.index') }}" class="filter-tab {{ !$type ? 'active' : '' }}">
            <i class="bi bi-grid-fill me-1"></i> Semua
        </a>
        <a href="{{ route('favorites.index', ['type' => 'kontrakan']) }}" class="filter-tab {{ $type == 'kontrakan' ? 'active' : '' }}">
            <i class="bi bi-house-fill me-1"></i> Kontrakan
        </a>
        <a href="{{ route('favorites.index', ['type' => 'laundry']) }}" class="filter-tab {{ $type == 'laundry' ? 'active' : '' }}">
            <i class="bi bi-droplet-fill me-1"></i> Laundry
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Favorites Grid -->
    @if($favorites->count() > 0)
    <div class="row g-4">
        @foreach($favorites as $favorite)
        @php
            $item = $favorite->type == 'kontrakan' ? $favorite->kontrakan : $favorite->laundry;
        @endphp
        
        @if($item)
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="favorite-card">
                <div class="card-img-wrapper">
                    @if($favorite->type == 'kontrakan')
                        @if($item->foto)
                            <img src="{{ asset('uploads/kontrakan/' . $item->foto) }}" alt="{{ $item->nama }}">
                        @else
                            <img src="https://via.placeholder.com/400x200/667eea/ffffff?text={{ urlencode($item->nama) }}" alt="{{ $item->nama }}">
                        @endif
                    @else
                        @if($item->foto)
                            <img src="{{ asset('uploads/laundry/' . $item->foto) }}" alt="{{ $item->nama }}">
                        @else
                            <img src="https://via.placeholder.com/400x200/4facfe/ffffff?text={{ urlencode($item->nama) }}" alt="{{ $item->nama }}">
                        @endif
                    @endif
                    
                    <div class="overlay-gradient"></div>
                    
                    <span class="badge-type {{ $favorite->type == 'kontrakan' ? 'badge-kontrakan' : 'badge-laundry' }}">
                        <i class="bi {{ $favorite->type == 'kontrakan' ? 'bi-house' : 'bi-droplet' }} me-1"></i>
                        {{ ucfirst($favorite->type) }}
                    </span>
                    
                    <form action="{{ route('favorite.destroy', $favorite->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-remove-favorite" title="Hapus dari favorit" onclick="return confirm('Hapus dari favorit?')">
                            <i class="bi bi-heart-fill"></i>
                        </button>
                    </form>
                    
                    @if($favorite->type == 'kontrakan')
                        <div class="price-tag">
                            <i class="bi bi-cash me-1"></i>
                            Rp {{ number_format($item->harga, 0, ',', '.') }}/thn
                        </div>
                    @endif
                </div>
                
                <div class="card-body">
                    <h5 class="card-title">{{ $item->nama }}</h5>
                    <p class="card-text">
                        <i class="bi bi-geo-alt me-1 text-danger"></i>
                        {{ Str::limit($item->alamat, 50) }}
                    </p>
                    
                    @if($favorite->type == 'kontrakan')
                    <div class="info-badges">
                        <span class="info-badge info-badge-distance">
                            <i class="bi bi-pin-map me-1"></i>
                            {{ number_format($item->jarak / 1000, 1) }} km
                        </span>
                        <span class="info-badge info-badge-room">
                            <i class="bi bi-door-open me-1"></i>
                            {{ $item->jumlah_kamar }} Kamar
                        </span>
                    </div>
                    @endif
                    
                    <div class="card-actions">
                        @if($favorite->type == 'kontrakan')
                            {{-- Tombol Booking Online --}}
                            @if($item->status == 'available')
                                <a href="{{ route('user.booking.create', ['kontrakan_id' => $item->id]) }}" 
                                   class="btn btn-action btn-booking flex-grow-1">
                                    <i class="bi bi-calendar-check me-1"></i> Booking Online
                                </a>
                            @else
                                <button class="btn btn-action btn-secondary flex-grow-1" disabled>
                                    <i class="bi bi-x-circle me-1"></i> Tidak Tersedia
                                </button>
                            @endif
                            
                            {{-- Tombol WhatsApp --}}
                            @if($item->no_whatsapp)
                                @php
                                    $cleanNumber = preg_replace('/[^0-9]/', '', $item->no_whatsapp);
                                    if (substr($cleanNumber, 0, 1) === '0') {
                                        $cleanNumber = '62' . substr($cleanNumber, 1);
                                    }
                                    $message = urlencode("Halo, saya tertarik dengan kontrakan *{$item->nama}* di {$item->alamat}. Apakah masih tersedia?");
                                @endphp
                                <a href="https://wa.me/{{ $cleanNumber }}?text={{ $message }}" target="_blank" class="btn btn-action btn-whatsapp" title="Chat WhatsApp">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            @endif
                            
                            {{-- Tombol Lihat di Maps --}}
                            @if($item->latitude && $item->longitude)
                            <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank" class="btn btn-action btn-detail" title="Lihat Lokasi">
                                <i class="bi bi-geo-alt-fill"></i>
                            </a>
                            @endif
                        @else
                            {{-- Laundry --}}
                            @if($item->no_whatsapp)
                                @php
                                    $cleanNumber = preg_replace('/[^0-9]/', '', $item->no_whatsapp);
                                    if (substr($cleanNumber, 0, 1) === '0') {
                                        $cleanNumber = '62' . substr($cleanNumber, 1);
                                    }
                                    $message = urlencode("Halo, saya ingin tanya tentang layanan laundry *{$item->nama}*.");
                                @endphp
                                <a href="https://wa.me/{{ $cleanNumber }}?text={{ $message }}" target="_blank" class="btn btn-action btn-whatsapp flex-grow-1">
                                    <i class="bi bi-whatsapp me-1"></i> Hubungi
                                </a>
                            @else
                                <button class="btn btn-action btn-secondary flex-grow-1" disabled title="Pemilik belum menambahkan nomor WhatsApp">
                                    <i class="bi bi-telephone-x me-1"></i> No. WA Belum Tersedia
                                </button>
                            @endif
                            
                            @if($item->latitude && $item->longitude)
                            <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank" class="btn btn-action btn-detail" title="Lihat Lokasi">
                                <i class="bi bi-geo-alt-fill"></i>
                            </a>
                            @endif
                        @endif
                    </div>
                    
                    {{-- Tombol Hapus Favorit --}}
                    <div class="mt-2">
                        <form action="{{ route('favorite.destroy', $favorite->id) }}" method="POST" class="d-inline w-100">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-remove-full w-100" onclick="return confirm('Hapus dari favorit?')">
                                <i class="bi bi-trash me-1"></i> Hapus dari Favorit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>

    <!-- Pagination -->
    @if($favorites->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $favorites->withQueryString()->links() }}
    </div>
    @endif
    
    @else
    <!-- Empty State -->
    <div class="empty-state">
        <div class="empty-state-content">
            <i class="bi bi-heart"></i>
            <h4>Belum Ada Favorit</h4>
            <p>Kamu belum menyimpan kontrakan atau laundry ke daftar favorit.<br>Cari rekomendasi dan simpan yang kamu suka!</p>
            <a href="{{ route('user.preferensi') }}" class="btn btn-browse">
                <i class="bi bi-search me-2"></i>
                Cari Kontrakan Sekarang
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
