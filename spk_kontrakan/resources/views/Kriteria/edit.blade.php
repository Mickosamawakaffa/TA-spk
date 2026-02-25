@extends('layouts.admin')

@section('title', 'Edit Kriteria')

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
    </style>
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kriteria.index') }}">Kriteria</a></li>
            <li class="breadcrumb-item active">Edit Data</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header mb-4">
        <h2 class="mb-2">
            <i class="bi bi-pencil-square me-2"></i>Edit Kriteria SAW
        </h2>
        <p class="mb-0 fs-6">Update informasi kriteria <strong>{{ $kriteria->nama_kriteria }}</strong></p>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-12">
            <div class="card form-card">
                <div class="card-body p-4">
                    <form action="{{ route('kriteria.update', $kriteria->id) }}" method="POST" id="kriteriaForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Informasi Kriteria Section -->
                        <div class="mb-4">
                            <h5 class="section-header">
                                <i class="bi bi-star me-2"></i>Informasi Kriteria
                            </h5>
                            
                            <div class="row g-3">
                                <!-- Tipe Bisnis -->
                                <div class="col-md-12">
                                    <label for="tipe_bisnis" class="form-label fw-semibold">
                                        Tipe Bisnis <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-building text-primary"></i>
                                        </span>
                                        <select 
                                            name="tipe_bisnis" 
                                            class="form-select border-start-0 @error('tipe_bisnis') is-invalid @enderror" 
                                            id="tipe_bisnis"
                                            required
                                        >
                                            <option value="">-- Pilih Tipe Bisnis --</option>
                                            <option value="kontrakan" {{ old('tipe_bisnis', $kriteria->tipe_bisnis) == 'kontrakan' ? 'selected' : '' }}>
                                                🏠 Kontrakan
                                            </option>
                                            <option value="laundry" {{ old('tipe_bisnis', $kriteria->tipe_bisnis) == 'laundry' ? 'selected' : '' }}>
                                                👕 Laundry
                                            </option>
                                        </select>
                                        @error('tipe_bisnis')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Pilih tipe bisnis untuk kriteria ini</small>
                                </div>

                                <!-- Nama Kriteria -->
                                <div class="col-md-12">
                                    <label for="nama_kriteria" class="form-label fw-semibold">
                                        Nama Kriteria <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-bookmark text-primary"></i>
                                        </span>
                                        <input 
                                            type="text" 
                                            name="nama_kriteria" 
                                            class="form-control border-start-0 @error('nama_kriteria') is-invalid @enderror" 
                                            id="nama_kriteria" 
                                            placeholder="Contoh: Harga Sewa, Jarak, Fasilitas"
                                            value="{{ old('nama_kriteria', $kriteria->nama_kriteria) }}"
                                            required
                                        >
                                        @error('nama_kriteria')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Masukkan nama kriteria yang jelas dan spesifik</small>
                                </div>

                                <!-- Bobot -->
                                <div class="col-md-6">
                                    <label for="bobot" class="form-label fw-semibold">
                                        Bobot Kriteria <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-speedometer text-info"></i>
                                        </span>
                                        <input 
                                            type="number" 
                                            step="0.01"
                                            name="bobot" 
                                            class="form-control border-start-0 @error('bobot') is-invalid @enderror" 
                                            id="bobot" 
                                            placeholder="0.25"
                                            value="{{ old('bobot', $kriteria->bobot) }}"
                                            min="0"
                                            max="1"
                                            required
                                        >
                                        @error('bobot')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Nilai bobot antara 0 - 1 (contoh: 0.25 atau 0.3)</small>
                                </div>

                                <!-- Tipe -->
                                <div class="col-md-6">
                                    <label for="tipe" class="form-label fw-semibold">
                                        Tipe Kriteria <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-tags text-warning"></i>
                                        </span>
                                        <select 
                                            name="tipe" 
                                            class="form-select border-start-0 @error('tipe') is-invalid @enderror" 
                                            id="tipe"
                                            required
                                        >
                                            <option value="">-- Pilih Tipe --</option>
                                            <option value="Benefit" {{ old('tipe', $kriteria->tipe) == 'Benefit' ? 'selected' : '' }}>
                                                ⬆️ Benefit (Semakin tinggi semakin baik)
                                            </option>
                                            <option value="Cost" {{ old('tipe', $kriteria->tipe) == 'Cost' ? 'selected' : '' }}>
                                                ⬇️ Cost (Semakin rendah semakin baik)
                                            </option>
                                        </select>
                                        @error('tipe')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted" id="tipeHelp">Pilih tipe sesuai karakteristik kriteria</small>
                                </div>

                                <!-- Keterangan -->
                                <div class="col-md-12">
                                    <label for="keterangan" class="form-label fw-semibold">
                                        Keterangan <span class="text-muted">(Opsional)</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 align-items-start pt-2">
                                            <i class="bi bi-chat-left-text text-secondary"></i>
                                        </span>
                                        <textarea 
                                            name="keterangan" 
                                            class="form-control border-start-0 @error('keterangan') is-invalid @enderror" 
                                            id="keterangan" 
                                            rows="3"
                                            placeholder="Contoh: Kriteria untuk menilai harga sewa per bulan dalam rupiah"
                                        >{{ old('keterangan', $kriteria->keterangan) }}</textarea>
                                        @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Deskripsi singkat tentang kriteria ini</small>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Changes -->
                        <div class="alert alert-info border-info mb-4">
                            <h6 class="fw-bold mb-2">
                                <i class="bi bi-info-circle me-2"></i>Info Perubahan
                            </h6>
                            <p class="mb-0 small">
                                Anda sedang mengedit kriteria <strong>{{ $kriteria->nama_kriteria }}</strong> untuk tipe bisnis 
                                <span class="badge bg-{{ $kriteria->tipe_bisnis == 'kontrakan' ? 'primary' : 'success' }}">
                                    {{ ucfirst($kriteria->tipe_bisnis) }}
                                </span>. 
                                Pastikan semua perubahan sudah benar sebelum menyimpan.
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                            <a href="{{ route('kriteria.index') }}" class="btn btn-light px-4">
                                <i class="bi bi-arrow-left me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="row mt-3 g-3">
                <div class="col-lg-6">
                    <div class="card border-0 bg-success bg-opacity-10">
                        <div class="card-body">
                            <h6 class="fw-bold mb-2 text-success">
                                <i class="bi bi-arrow-up-circle me-2"></i>Kriteria Benefit
                            </h6>
                            <p class="mb-0 small text-muted">
                                Nilai yang lebih tinggi lebih disukai. 
                                <br>Contoh: Fasilitas, Jumlah Kamar, Kecepatan Layanan
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 bg-danger bg-opacity-10">
                        <div class="card-body">
                            <h6 class="fw-bold mb-2 text-danger">
                                <i class="bi bi-arrow-down-circle me-2"></i>Kriteria Cost
                            </h6>
                            <p class="mb-0 small text-muted">
                                Nilai yang lebih rendah lebih disukai.
                                <br>Contoh: Harga, Jarak, Waktu Tempuh
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data History Card -->
            <div class="card border-0 bg-light mt-3">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="bi bi-clock-history text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-2">Riwayat Data:</h6>
                            <p class="mb-1 small text-muted">
                                <strong>Dibuat:</strong> {{ $kriteria->created_at->format('d M Y, H:i') }} WIB
                            </p>
                            @if($kriteria->updated_at != $kriteria->created_at)
                            <p class="mb-0 small text-muted">
                                <strong>Terakhir diupdate:</strong> {{ $kriteria->updated_at->format('d M Y, H:i') }} WIB
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript untuk Helper & Validation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('kriteriaForm');
    const tipeSelect = document.getElementById('tipe');
    const bobotInput = document.getElementById('bobot');
    const tipeHelp = document.getElementById('tipeHelp');
    
    // Update help text based on tipe selection
    function updateTipeHelp() {
        const tipe = tipeSelect.value;
        if (tipe === 'Benefit') {
            tipeHelp.innerHTML = '<strong>Benefit:</strong> Semakin tinggi nilai semakin baik (contoh: Fasilitas, Jumlah Kamar)';
            tipeHelp.classList.remove('text-muted');
            tipeHelp.classList.add('text-success');
        } else if (tipe === 'Cost') {
            tipeHelp.innerHTML = '<strong>Cost:</strong> Semakin rendah nilai semakin baik (contoh: Harga, Jarak)';
            tipeHelp.classList.remove('text-muted');
            tipeHelp.classList.add('text-danger');
        } else {
            tipeHelp.innerHTML = 'Pilih tipe sesuai karakteristik kriteria';
            tipeHelp.classList.remove('text-success', 'text-danger');
            tipeHelp.classList.add('text-muted');
        }
    }
    
    // Initialize on page load
    updateTipeHelp();
    
    tipeSelect.addEventListener('change', updateTipeHelp);
    
    // Validate bobot range
    bobotInput.addEventListener('blur', function() {
        const val = parseFloat(this.value);
        if (val < 0) this.value = 0;
        if (val > 1) this.value = 1;
    });
    
    // Form validation animation
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        } else {
            const confirmed = confirm('Simpan perubahan kriteria ini?');
            if (!confirmed) {
                e.preventDefault();
                e.stopPropagation();
            }
        }
        form.classList.add('was-validated');
    });
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
    .form-select:focus + .input-group-text,
    .input-group:focus-within .input-group-text {
        border-color: #86b7fe;
        background-color: #e7f1ff;
    }
    
    .card {
        transition: box-shadow 0.3s ease;
    }
    
    .was-validated .form-control:invalid,
    .was-validated .form-select:invalid {
        border-color: #dc3545;
    }
    
    .was-validated .form-control:valid,
    .was-validated .form-select:valid {
        border-color: #198754;
    }
</style>
@endsection
