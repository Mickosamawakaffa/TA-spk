@extends('layouts.admin')

@section('title', 'Detail Kontrakan')

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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            <li class="breadcrumb-item"><a href="{{ route('kontrakan.index') }}">Kontrakan</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>

    <!-- Detail Header -->
    <div class="detail-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">
                    <i class="bi bi-houses me-2"></i>{{ $kontrakan->nama }}
                    <span class="badge {{ $kontrakan->status_badge_class }} fs-6 ms-2">{{ $kontrakan->status_label }}</span>
                </h2>
                <p class="mb-0 fs-6">Informasi lengkap dan detil kontrakan</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('admin.bookings.kontrakan-history', $kontrakan->id) }}" class="btn btn-outline-light me-2">
                    <i class="bi bi-calendar-check me-2"></i>Riwayat Booking
                </a>
                <a href="{{ route('kontrakan.edit', $kontrakan->id) }}" class="btn btn-edit me-2">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="{{ route('kontrakan.index') }}" class="btn btn-outline-light">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Quick Status Update --}}
    <div class="mx-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h6 class="mb-0"><i class="bi bi-toggle-on me-2"></i>Update Status Cepat</h6>
                        <small class="text-muted">Tandai status ketersediaan kontrakan</small>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex gap-2 flex-wrap justify-content-md-end">
                            <form action="{{ route('kontrakan.update-status', $kontrakan->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="available">
                                <button type="submit" class="btn btn-{{ $kontrakan->status === 'available' ? 'success' : 'outline-success' }} btn-sm" {{ $kontrakan->status === 'available' ? 'disabled' : '' }}>
                                    <i class="bi bi-check-circle me-1"></i>Tersedia
                                </button>
                            </form>
                            <form action="{{ route('kontrakan.update-status', $kontrakan->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="booked">
                                <button type="submit" class="btn btn-{{ $kontrakan->status === 'booked' ? 'warning' : 'outline-warning' }} btn-sm" {{ $kontrakan->status === 'booked' ? 'disabled' : '' }}>
                                    <i class="bi bi-clock me-1"></i>Dipesan
                                </button>
                            </form>
                            <form action="{{ route('kontrakan.update-status', $kontrakan->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="occupied">
                                <button type="submit" class="btn btn-{{ $kontrakan->status === 'occupied' ? 'danger' : 'outline-danger' }} btn-sm" {{ $kontrakan->status === 'occupied' ? 'disabled' : '' }}>
                                    <i class="bi bi-house-fill me-1"></i>Ditempati
                                </button>
                            </form>
                            <form action="{{ route('kontrakan.update-status', $kontrakan->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="maintenance">
                                <button type="submit" class="btn btn-{{ $kontrakan->status === 'maintenance' ? 'secondary' : 'outline-secondary' }} btn-sm" {{ $kontrakan->status === 'maintenance' ? 'disabled' : '' }}>
                                    <i class="bi bi-tools me-1"></i>Pemeliharaan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- FOTO SECTION -->
            @if($kontrakan->foto)
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="bi bi-camera me-2"></i>Foto Kontrakan
                </div>
                <div class="card-body p-0">
                    <div class="position-relative" style="height: 400px; overflow: hidden;">
                        <img 
                            src="{{ asset('uploads/kontrakan/' . $kontrakan->foto) }}" 
                            alt="{{ $kontrakan->nama }}" 
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
                </div>
            </div>
            @else
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="bi bi-camera me-2"></i>Foto Kontrakan
                </div>
                <div class="card-body p-0">
                    <div class="position-relative foto-placeholder" style="height: 400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <div class="decorative-circle" style="position: absolute; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.1); top: -50px; left: -50px;"></div>
                        <div class="decorative-circle" style="position: absolute; width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.1); bottom: -30px; right: -30px;"></div>
                        
                        <div class="text-center position-relative" style="z-index: 2;">
                            <div class="mb-3" style="animation: float 3s ease-in-out infinite;">
                                <i class="bi bi-image" style="font-size: 5rem; color: rgba(255,255,255,0.9); filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));"></i>
                            </div>
                            <h5 class="text-white mb-2 fw-bold">Foto Tidak Tersedia</h5>
                            <p class="text-white-50 small mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Belum ada foto yang diupload untuk kontrakan ini
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- ========================================
                 SECTION 1: GALERI MULTIPLE FOTO 
                 ======================================== -->
            <div class="detail-card">
                <div class="detail-card-header d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-images me-2"></i>Galeri Foto
                    </span>
                    <button type="button" class="btn btn-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;" data-bs-toggle="modal" data-bs-target="#uploadGaleriModal">
                        <i class="bi bi-upload me-1"></i>Upload
                    </button>
                </div>
                <div class="card-body">
                    @if($kontrakan->galeri->count() > 0)
                        <div class="row g-3">
                            @foreach($kontrakan->galeri as $foto)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="position-relative galeri-item">
                                    <img 
                                        src="{{ asset('uploads/galeri/kontrakan/' . $foto->foto) }}" 
                                        alt="Foto {{ $kontrakan->nama }}"
                                        class="img-fluid rounded shadow-sm"
                                        style="width: 100%; height: 200px; object-fit: cover; cursor: pointer;"
                                        onclick="openImageModal(this.src)"
                                    >
                                    
                                    @if($foto->is_primary)
                                    <span class="position-absolute top-0 start-0 m-2 badge bg-warning">
                                        <i class="bi bi-star-fill"></i> Utama
                                    </span>
                                    @endif

                                    <div class="position-absolute top-0 end-0 m-2">
                                        <div class="btn-group-vertical" role="group">
                                            @if(!$foto->is_primary)
                                            <form action="{{ route('galeri.set-primary', $foto->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning" title="Jadikan foto utama">
                                                    <i class="bi bi-star"></i>
                                                </button>
                                            </form>
                                            @endif
                                            
                                            <form action="{{ route('galeri.delete', $foto->id) }}" method="POST" 
                                                  onsubmit="return confirm('Yakin hapus foto ini?')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus foto">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <span class="position-absolute bottom-0 start-0 m-2 badge bg-dark bg-opacity-75">
                                        #{{ $foto->urutan }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-images text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">Belum ada foto di galeri</h5>
                            <p class="text-muted">Upload foto untuk menampilkan galeri</p>
                            <button type="button" class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;" data-bs-toggle="modal" data-bs-target="#uploadGaleriModal">
                                <i class="bi bi-upload me-1"></i>Upload Foto Pertama
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modal Upload Galeri -->
            <div class="modal fade" id="uploadGaleriModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-upload me-2"></i>Upload Foto Galeri
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('galeri.upload', $kontrakan->id) }}" 
                              method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Pilih Foto (bisa multiple)</label>
                                    <input type="file" name="fotos[]" id="galeriFileInput" class="form-control" multiple accept="image/*" required>
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Format: JPG, JPEG, PNG. Maksimal 2MB per foto
                                    </small>
                                </div>
                                
                                <div id="imagePreview" class="row g-2 mt-2"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                    <i class="bi bi-upload me-1"></i>Upload
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ========================================
                 SECTION 2: REVIEW & RATING 
                 ======================================== -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span>
                                <i class="bi bi-star-fill me-2"></i>Review & Rating
                            </span>
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <div class="d-flex align-items-center">
                                    <span class="fs-4 fw-bold me-2">{{ $kontrakan->average_rating }}</span>
                                    <div>
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $kontrakan->average_rating)
                                                <i class="bi bi-star-fill text-warning"></i>
                                            @else
                                                <i class="bi bi-star text-muted"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <span class="text-muted">({{ $kontrakan->total_reviews }} review)</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#reviewModal">
                            <i class="bi bi-pencil me-1"></i>Tulis Review
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($kontrakan->reviews->count() > 0)
                        <div class="review-list">
                            @foreach($kontrakan->reviews as $review)
                            <div class="review-item border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                             style="width: 40px; height: 40px; font-weight: bold;">
                                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $review->user->name }}</h6>
                                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="mb-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="bi bi-star-fill text-warning"></i>
                                                @else
                                                    <i class="bi bi-star text-muted"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        @if(auth()->id() === $review->user_id)
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary" 
                                                    onclick="editReview({{ $review->id }}, {{ $review->rating }}, '{{ addslashes($review->review ?? '') }}')">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" 
                                                  onsubmit="return confirm('Yakin hapus review ini?')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @if($review->review)
                                <p class="mb-0 ms-5">{{ $review->review }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-chat-left-text text-muted" style="font-size: 3rem;"></i>
                            <h6 class="text-muted mt-3">Belum ada review</h6>
                            <p class="text-muted">Jadilah yang pertama memberikan review!</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modal Review -->
            <div class="modal fade" id="reviewModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-star me-2"></i>
                                <span id="reviewModalTitle">Tulis Review</span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="reviewForm" method="POST">
                            @csrf
                            <input type="hidden" name="_method" id="reviewMethod" value="POST">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Rating</label>
                                    <div class="star-rating">
                                        <input type="radio" name="rating" value="5" id="star5" required>
                                        <label for="star5"><i class="bi bi-star-fill"></i></label>
                                        
                                        <input type="radio" name="rating" value="4" id="star4">
                                        <label for="star4"><i class="bi bi-star-fill"></i></label>
                                        
                                        <input type="radio" name="rating" value="3" id="star3">
                                        <label for="star3"><i class="bi bi-star-fill"></i></label>
                                        
                                        <input type="radio" name="rating" value="2" id="star2">
                                        <label for="star2"><i class="bi bi-star-fill"></i></label>
                                        
                                        <input type="radio" name="rating" value="1" id="star1">
                                        <label for="star1"><i class="bi bi-star-fill"></i></label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Komentar (opsional)</label>
                                    <textarea name="review" id="reviewTextarea" class="form-control" rows="4" 
                                              placeholder="Ceritakan pengalaman Anda..." maxlength="1000"></textarea>
                                    <small class="text-muted">Maksimal 1000 karakter</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-send me-1"></i>Kirim Review
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- MAPS SECTION -->
            @if($kontrakan->latitude && $kontrakan->longitude)
            <div class="detail-card">
                <div class="detail-card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <span>
                        <i class="bi bi-map me-2"></i>Lokasi di Peta
                    </span>
                    <a href="https://www.google.com/maps?q={{ $kontrakan->latitude }},{{ $kontrakan->longitude }}" 
                       target="_blank" 
                       class="btn btn-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Google Maps
                    </a>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 400px; width: 100%;"></div>
                </div>
            </div>
            @endif

            <!-- Informasi Utama Card -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="bi bi-info-circle me-2"></i>Informasi Utama
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Nama -->
                        <div class="col-md-12">
                            <div class="d-flex align-items-start">
                                <div class="rounded p-3 me-3" style="background: rgba(102, 126, 234, 0.1);">
                                    <i class="bi bi-building fs-4" style="color: #667eea;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Nama Kontrakan</small>
                                    <h5 class="mb-0 fw-semibold">{{ $kontrakan->nama }}</h5>
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
                                    <p class="mb-0">{{ $kontrakan->alamat }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- WhatsApp Section -->
                        @if($kontrakan->no_whatsapp)
                        <div class="col-md-12">
                            <div class="d-flex align-items-start">
                                <div class="bg-success bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-whatsapp text-success fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Kontak WhatsApp</small>
                                    <h5 class="mb-2 fw-semibold text-success">{{ $kontrakan->no_whatsapp }}</h5>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a 
                                            href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $kontrakan->no_whatsapp) }}" 
                                            target="_blank"
                                            class="btn btn-success btn-sm"
                                        >
                                            <i class="bi bi-whatsapp me-1"></i>Chat WhatsApp
                                        </a>
                                        <a 
                                            href="tel:{{ preg_replace('/[^0-9]/', '', $kontrakan->no_whatsapp) }}" 
                                            class="btn btn-outline-success btn-sm"
                                        >
                                            <i class="bi bi-telephone me-1"></i>Telepon
                                        </a>
                                        <button 
                                            type="button"
                                            class="btn btn-outline-secondary btn-sm"
                                            onclick="copyWhatsApp('{{ $kontrakan->no_whatsapp }}')"
                                        >
                                            <i class="bi bi-clipboard me-1"></i>Salin Nomor
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Harga -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-success bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-currency-dollar text-success fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Harga Sewa/Tahun</small>
                                    <h5 class="mb-0 fw-semibold text-success">
                                        Rp {{ number_format($kontrakan->harga, 0, ',', '.') }}
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <!-- Luas -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-warning bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-rulers text-warning fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Luas Bangunan</small>
                                    <h5 class="mb-0 fw-semibold text-warning">
                                        {{ $kontrakan->luas }} m²
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <!-- Jumlah Kamar Tidur -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-purple bg-opacity-10 rounded p-3 me-3" style="background-color: rgba(102, 126, 234, 0.1) !important;">
                                    <i class="bi bi-door-closed fs-4" style="color: #667eea;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Jumlah Kamar Tidur</small>
                                    <h5 class="mb-0 fw-semibold" style="color: #667eea;">
                                        {{ $kontrakan->jumlah_kamar ?? 1 }} kamar
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <!-- Jumlah Kamar Mandi -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-cyan bg-opacity-10 rounded p-3 me-3" style="background-color: rgba(23, 162, 184, 0.1) !important;">
                                    <i class="bi bi-droplet fs-4" style="color: #17a2b8;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Jumlah Kamar Mandi</small>
                                    <h5 class="mb-0 fw-semibold" style="color: #17a2b8;">
                                        {{ $kontrakan->bathroom_count ?? 1 }} kamar
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <!-- Jarak -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-info bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-pin-map text-info fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Jarak ke Kampus</small>
                                    <h5 class="mb-0 fw-semibold text-info">
                                        {{ number_format($kontrakan->jarak, 0, ',', '.') }} meter
                                    </h5>
                                    <small class="text-muted">≈ {{ round($kontrakan->jarak / 1000, 2) }} km</small>
                                </div>
                            </div>
                        </div>

                        <!-- Fasilitas -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-secondary bg-opacity-10 rounded p-3 me-3">
                                    <i class="bi bi-star text-secondary fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block mb-1">Fasilitas</small>
                                    @if($kontrakan->fasilitas)
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            @foreach(explode(',', $kontrakan->fasilitas) as $fasilitas)
                                                <span class="badge bg-secondary bg-opacity-10 text-dark px-3 py-2">
                                                    <i class="bi bi-check-circle me-1"></i>{{ trim($fasilitas) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="mb-0 text-muted">-</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Info -->
        <div class="col-lg-3">
            <!-- WhatsApp Quick Contact Card -->
            @if($kontrakan->no_whatsapp)
            <div class="detail-card" style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); border: none !important; color: white;">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <div class="bg-white bg-opacity-20 rounded-circle d-inline-flex p-3 mb-3">
                            <i class="bi bi-whatsapp" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Hubungi Pemilik</h5>
                        <p class="mb-0 small opacity-75">Tanya langsung via WhatsApp</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded p-3 mb-3 text-center">
                        <small class="d-block mb-1 opacity-75">Nomor WhatsApp</small>
                        <h4 class="mb-0 fw-bold">{{ $kontrakan->no_whatsapp }}</h4>
                    </div>
                    <a 
                        href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $kontrakan->no_whatsapp) }}?text=Halo,%20saya%20tertarik%20dengan%20kontrakan%20*{{ urlencode($kontrakan->nama) }}*%20yang%20terletak%20di%20{{ urlencode($kontrakan->alamat) }}.%20Apakah%20masih%20tersedia?" 
                        target="_blank"
                        class="btn btn-light w-100 fw-semibold"
                    >
                        <i class="bi bi-whatsapp me-2"></i>Chat Sekarang
                    </a>
                </div>
            </div>
            @endif

            <!-- ========================================
                 SECTION 3: FAVORITE BUTTON 
                 ======================================== -->
            <div class="detail-card">
                <div class="card-body p-4 text-center">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-heart me-2"></i>Simpan Favorit
                    </h6>
                    <p class="text-muted small mb-3">
                        Simpan kontrakan ini ke daftar favorit Anda untuk akses cepat
                    </p>
                    
                    @php
                        $isFavorited = auth()->check() && $kontrakan->isFavoritedBy(auth()->id());
                    @endphp
                    
                    <form action="{{ route('favorites.kontrakan.toggle', $kontrakan->id) }}" 
      method="POST" id="favoriteForm">
    @csrf
    <button type="submit" class="btn {{ $isFavorited ? 'btn-danger' : 'btn-outline-danger' }} w-100" 
            id="favoriteBtn">
        <i class="bi bi-heart{{ $isFavorited ? '-fill' : '' }} me-2" id="favoriteIcon"></i>
        <span id="favoriteBtnText">{{ $isFavorited ? 'Hapus dari Favorit' : 'Tambah ke Favorit' }}</span>
    </button>
</form>

<small class="text-muted d-block mt-2">
                    <i class="bi bi-people me-1"></i>
                    <span id="totalFavorites">{{ $kontrakan->total_favorites }}</span> orang menyukai ini
                </small>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="detail-card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-graph-up me-2"></i>Ringkasan
                </h6>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <span class="text-muted">Harga/Bulan</span>
                    <strong>Rp {{ number_format($kontrakan->harga / 12, 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <span class="text-muted">Harga/m²</span>
                    <strong>Rp {{ number_format($kontrakan->harga / $kontrakan->jumlah_kamar, 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <span class="text-muted">Status</span>
                    <span class="badge bg-success">Tersedia</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Kategori</span>
                    <strong>Kontrakan</strong>
                </div>
            </div>
        </div>

        <!-- Timestamp Info -->
        <div class="card border-0 bg-light mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-clock-history me-2" style="color: #667eea;"></i>Riwayat
                </h6>
                <div class="mb-3">
                    <small class="text-muted d-block">Dibuat</small>
                    <strong>{{ $kontrakan->created_at->format('d M Y, H:i') }} WIB</strong>
                </div>
                @if($kontrakan->updated_at != $kontrakan->created_at)
                <div>
                    <small class="text-muted d-block">Terakhir Diupdate</small>
                    <strong>{{ $kontrakan->updated_at->format('d M Y, H:i') }} WIB</strong>
                    <small class="text-muted d-block mt-1">
                        ({{ $kontrakan->updated_at->diffForHumans() }})
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
                    <a href="{{ route('kontrakan.edit', $kontrakan->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>Edit Data
                    </a>
                    
                    @if(auth()->user()->role == 'super_admin')
                    <form action="{{ route('kontrakan.destroy', $kontrakan->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kontrakan {{ $kontrakan->nama }}?')">
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
<!-- Toast Notification untuk Copy WhatsApp -->
<div id="copyToast" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11; display: none;">
    <div class="toast show" role="alert">
        <div class="toast-header bg-success text-white">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong class="me-auto">Berhasil!</strong>
            <button type="button" class="btn-close btn-close-white" onclick="hideToast()"></button>
        </div>
        <div class="toast-body">
            Nomor WhatsApp berhasil disalin ke clipboard!
        </div>
    </div>
</div>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
@if($kontrakan->latitude && $kontrakan->longitude)
// Initialize map
document.addEventListener('DOMContentLoaded', function() {
    const lat = {{ $kontrakan->latitude }};
    const lng = {{ $kontrakan->longitude }};
    
    const map = L.map('map').setView([lat, lng], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);
    
    const customIcon = L.divIcon({
        className: 'custom-marker',
        html: `
            <div style="position: relative;">
                <div style="
                    background-color: #dc3545;
                    width: 32px;
                    height: 32px;
                    border-radius: 50%;
                    border: 3px solid white;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 16px;
                ">🏠</div>
                <div style="
                    position: absolute;
                    bottom: -8px;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 0;
                    height: 0;
                    border-left: 8px solid transparent;
                    border-right: 8px solid transparent;
                    border-top: 8px solid #dc3545;
                "></div>
            </div>
        `,
        iconSize: [32, 40],
        iconAnchor: [16, 40],
        popupAnchor: [0, -40]
    });
    
    const marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);
    
    const popupContent = `
        <div class="p-2" style="min-width: 200px;">
            <h6 class="fw-bold mb-2">{{ $kontrakan->nama }}</h6>
            <p class="small text-muted mb-2">
                <i class="bi bi-geo-alt me-1"></i>{{ Str::limit($kontrakan->alamat, 50) }}
            </p>
            <div class="d-grid">
                <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" 
                   target="_blank" 
                   class="btn btn-sm btn-danger">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Buka di Google Maps
                </a>
            </div>
        </div>
    `;
    
    marker.bindPopup(popupContent, {
        maxWidth: 300,
        className: 'custom-popup'
    }).openPopup();
});
@endif

// Image modal functions
function openImageModal(src) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const caption = document.getElementById('modalCaption');
    
    modal.style.display = 'block';
    modalImg.src = src;
    caption.innerHTML = '{{ $kontrakan->nama }}';
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

// Copy WhatsApp number function
function copyWhatsApp(number) {
    navigator.clipboard.writeText(number).then(function() {
        showToast();
    }, function(err) {
        console.error('Gagal menyalin: ', err);
        alert('Gagal menyalin nomor WhatsApp');
    });
}

function showToast() {
    const toast = document.getElementById('copyToast');
    toast.style.display = 'block';
    setTimeout(function() {
        hideToast();
    }, 3000);
}

function hideToast() {
    const toast = document.getElementById('copyToast');
    toast.style.display = 'none';
}

// ========================================
// GALERI: Preview multiple images
// ========================================
document.getElementById('galeriFileInput')?.addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    [...e.target.files].forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-4';
            col.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" style="height: 100px; object-fit: cover;">`;
            preview.appendChild(col);
        }
        reader.readAsDataURL(file);
    });
});

// ========================================
// REVIEW: Form handler
// ========================================
const reviewForm = document.getElementById('reviewForm');
const reviewModal = document.getElementById('reviewModal');
let bsReviewModal;

if(reviewModal) {
    bsReviewModal = new bootstrap.Modal(reviewModal);
    reviewForm.action = "{{ route('reviews.kontrakan.store', $kontrakan->id) }}";
}

function editReview(id, rating, review) {
    document.getElementById('reviewModalTitle').textContent = 'Edit Review';
    document.getElementById('reviewMethod').value = 'PUT';
    reviewForm.action = `/review/${id}`;
    
    // Set rating
    document.getElementById(`star${rating}`).checked = true;
    
    // Set review text
    document.getElementById('reviewTextarea').value = review || '';
    
    bsReviewModal.show();
}

// Reset form when modal closed
reviewModal?.addEventListener('hidden.bs.modal', function() {
    document.getElementById('reviewModalTitle').textContent = 'Tulis Review';
    document.getElementById('reviewMethod').value = 'POST';
    reviewForm.action = "{{ route('reviews.kontrakan.store', $kontrakan->id) }}";
    reviewForm.reset();
});

// ========================================
// FAVORITE: Toggle with AJAX
// ========================================
document.getElementById('favoriteForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('favoriteBtn');
    const icon = document.getElementById('favoriteIcon');
    const text = document.getElementById('favoriteBtnText');
    const total = document.getElementById('totalFavorites');
    
    // Disable button sementara
    btn.disabled = true;
    
    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            if(data.status === 'added') {
                btn.classList.remove('btn-outline-danger');
                btn.classList.add('btn-danger');
                icon.classList.add('bi-heart-fill');
                icon.classList.remove('bi-heart');
                text.textContent = 'Hapus dari Favorit';
            } else {
                btn.classList.remove('btn-danger');
                btn.classList.add('btn-outline-danger');
                icon.classList.remove('bi-heart-fill');
                icon.classList.add('bi-heart');
                text.textContent = 'Tambah ke Favorit';
            }
            
            total.textContent = data.total_favorites;
            
            // Show success message
            showToast();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Fallback: submit form normally
        btn.disabled = false;
        this.submit();
    })
    .finally(() => {
        btn.disabled = false;
    });
});
</script>
<style>
    /* Float animation for empty photo state */
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
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

    /* Card hover effects */
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
    }

    /* Image modal styles */
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

    /* Custom Leaflet popup */
    .custom-popup .leaflet-popup-content-wrapper {
        border-radius: 8px;
        padding: 0;
    }

    .custom-popup .leaflet-popup-content {
        margin: 0;
    }

    .custom-marker {
        background: none;
        border: none;
    }

    /* Toast notification */
    .toast {
        min-width: 300px;
    }

    /* Responsive adjustments */
    @media (max-width: 767px) {
        .modal-close {
            top: 10px;
            right: 20px;
            font-size: 30px;
        }

        .modal-content-img {
            max-width: 95%;
            max-height: 85%;
        }

        .modal-caption {
            bottom: 10px;
            width: 90%;
            font-size: 0.9rem;
        }

        #map {
            height: 300px !important;
        }
    }

    /* Button hover effects */
    .btn {
        transition: all 0.2s ease;
    }

    .btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    /* ========================================
       GALERI STYLES
       ======================================== */
    .galeri-item {
        overflow: hidden;
        border-radius: 8px;
        transition: transform 0.3s;
    }

    .galeri-item:hover {
        transform: scale(1.05);
    }

    .galeri-item img {
        transition: transform 0.3s;
    }

    .galeri-item:hover img {
        transform: scale(1.1);
    }

    /* ========================================
       REVIEW STAR RATING
       ======================================== */
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        gap: 5px;
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        cursor: pointer;
        font-size: 2rem;
        color: #ddd;
        transition: color 0.2s;
    }

    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #ffc107;
    }

    .review-item:last-child {
        border-bottom: none !important;
        padding-bottom: 0 !important;
        margin-bottom: 0 !important;
    }

    /* ========================================
       FAVORITE BUTTON ANIMATION
       ======================================== */
    #favoriteBtn {
        transition: all 0.3s ease;
    }

    #favoriteBtn:hover:not(:disabled) {
        transform: scale(1.05);
    }

    #favoriteIcon {
        transition: all 0.3s ease;
    }
</style>
@endsection
