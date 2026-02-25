@extends('layouts.admin')

@section('title', 'Edit Booking #' . $booking->id)

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Booking #{{ $booking->id }}
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Booking</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.show', $booking->id) }}">#{{ $booking->id }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST" id="bookingForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                {{-- Info Kontrakan (readonly) --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0"><i class="bi bi-house me-2"></i>Kontrakan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            @if($booking->kontrakan->foto)
                            <img src="{{ asset('uploads/kontrakan/' . $booking->kontrakan->foto) }}" 
                                alt="{{ $booking->kontrakan->nama }}" 
                                class="rounded me-3" style="width: 100px; height: 75px; object-fit: cover;">
                            @else
                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 75px;">
                                <i class="bi bi-house text-muted fs-3"></i>
                            </div>
                            @endif
                            <div>
                                <h6 class="mb-1">{{ $booking->kontrakan->nama }}</h6>
                                <p class="text-muted mb-0 small">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $booking->kontrakan->alamat }}
                                </p>
                                <p class="mb-0 small">
                                    Harga: <strong class="text-success">Rp {{ number_format($booking->kontrakan->harga, 0, ',', '.') }}/bulan</strong>
                                </p>
                            </div>
                        </div>
                        <input type="hidden" name="kontrakan_id" value="{{ $booking->kontrakan_id }}">
                    </div>
                </div>

                {{-- Periode Sewa --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0"><i class="bi bi-calendar-range me-2"></i>Periode Sewa</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" 
                                    name="start_date" 
                                    class="form-control @error('start_date') is-invalid @enderror" 
                                    value="{{ old('start_date', $booking->start_date->format('Y-m-d')) }}"
                                    id="startDate"
                                    required>
                                @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" 
                                    name="end_date" 
                                    class="form-control @error('end_date') is-invalid @enderror" 
                                    value="{{ old('end_date', $booking->end_date->format('Y-m-d')) }}"
                                    id="endDate"
                                    required>
                                @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        {{-- Availability Check Result --}}
                        <div id="availabilityResult" class="mt-3" style="display: none;"></div>

                        {{-- Duration Display --}}
                        <div class="bg-light rounded p-3 mt-3">
                            <div class="row text-center">
                                <div class="col">
                                    <div class="small text-muted">Durasi</div>
                                    <div class="fw-bold" id="durationDays">{{ $booking->duration_days }} hari</div>
                                </div>
                                <div class="col">
                                    <div class="small text-muted">Setara</div>
                                    <div class="fw-bold" id="durationMonths">{{ $booking->duration_months }} bulan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info Penyewa --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Info Penyewa</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Penyewa <span class="text-danger">*</span></label>
                                <input type="text" 
                                    name="tenant_name" 
                                    class="form-control @error('tenant_name') is-invalid @enderror" 
                                    value="{{ old('tenant_name', $booking->tenant_name) }}"
                                    placeholder="Nama lengkap penyewa"
                                    required>
                                @error('tenant_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. HP/WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" 
                                    name="tenant_phone" 
                                    class="form-control @error('tenant_phone') is-invalid @enderror" 
                                    value="{{ old('tenant_phone', $booking->tenant_phone) }}"
                                    placeholder="081234567890"
                                    required>
                                @error('tenant_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Catatan</label>
                                <textarea name="notes" 
                                    class="form-control @error('notes') is-invalid @enderror" 
                                    rows="3" 
                                    placeholder="Catatan tambahan (opsional)">{{ old('notes', $booking->notes) }}</textarea>
                                @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Status Info --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">Status Booking</label>
                            <div>
                                <span class="badge {{ $booking->status_badge_class }} fs-6 px-3 py-2">
                                    {{ $booking->status_label }}
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Status Pembayaran</label>
                            <div>
                                <span class="badge {{ $booking->payment_status == 'paid' ? 'bg-success' : 'bg-secondary' }} fs-6 px-3 py-2">
                                    {{ $booking->payment_status_label }}
                                </span>
                            </div>
                        </div>
                        <hr>
                        <div class="text-muted small">
                            <p class="mb-1"><i class="bi bi-clock me-1"></i>Dibuat: {{ $booking->created_at->format('d/m/Y H:i') }}</p>
                            <p class="mb-0"><i class="bi bi-arrow-repeat me-1"></i>Update: {{ $booking->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Biaya --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Biaya Sewa</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Biaya Total <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" 
                                    name="amount" 
                                    class="form-control @error('amount') is-invalid @enderror" 
                                    value="{{ old('amount', $booking->amount) }}"
                                    id="amount"
                                    min="0"
                                    required>
                            </div>
                            @error('amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Harga per bulan:</span>
                            <span id="pricePerMonth">Rp {{ number_format($booking->kontrakan->harga, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Estimasi:</span>
                            <span id="estimatedTotal" class="fw-bold text-success">Rp {{ number_format($booking->amount, 0, ',', '.') }}</span>
                        </div>
                        
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-2" id="recalcBtn">
                            <i class="bi bi-calculator me-1"></i>Hitung Ulang Otomatis
                        </button>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const amount = document.getElementById('amount');
    const durationDays = document.getElementById('durationDays');
    const durationMonths = document.getElementById('durationMonths');
    const estimatedTotal = document.getElementById('estimatedTotal');
    const availabilityResult = document.getElementById('availabilityResult');
    const submitBtn = document.getElementById('submitBtn');
    const recalcBtn = document.getElementById('recalcBtn');
    
    const pricePerMonth = {{ $booking->kontrakan->harga }};
    const kontrakanId = {{ $booking->kontrakan_id }};
    const currentBookingId = {{ $booking->id }};
    
    function formatRupiah(num) {
        return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    function calculateDuration() {
        if (startDate.value && endDate.value) {
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const months = Math.ceil(diffDays / 30);
            
            durationDays.textContent = diffDays + ' hari';
            durationMonths.textContent = months + ' bulan';
            
            const estimated = months * pricePerMonth;
            estimatedTotal.textContent = formatRupiah(estimated);
            
            return { days: diffDays, months: months, estimated: estimated };
        }
        return null;
    }
    
    function checkAvailability() {
        if (!startDate.value || !endDate.value) return;
        
        availabilityResult.style.display = 'block';
        availabilityResult.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div> Memeriksa ketersediaan...';
        
        fetch(`/admin/bookings/check-availability?kontrakan_id=${kontrakanId}&start_date=${startDate.value}&end_date=${endDate.value}&exclude_id=${currentBookingId}`)
            .then(response => response.json())
            .then(data => {
                if (data.available) {
                    availabilityResult.innerHTML = '<div class="alert alert-success mb-0"><i class="bi bi-check-circle me-2"></i>Tanggal tersedia untuk booking</div>';
                    submitBtn.disabled = false;
                } else {
                    let conflicts = data.conflicts.map(c => 
                        `<li>${c.tenant_name}: ${c.start_date} - ${c.end_date}</li>`
                    ).join('');
                    availabilityResult.innerHTML = `
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-exclamation-circle me-2"></i>Tanggal bertabrakan dengan booking lain:
                            <ul class="mb-0 mt-2">${conflicts}</ul>
                        </div>
                    `;
                    submitBtn.disabled = true;
                }
            })
            .catch(error => {
                availabilityResult.innerHTML = '<div class="alert alert-warning mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Gagal memeriksa ketersediaan</div>';
            });
    }
    
    startDate.addEventListener('change', function() {
        calculateDuration();
        checkAvailability();
    });
    
    endDate.addEventListener('change', function() {
        calculateDuration();
        checkAvailability();
    });
    
    recalcBtn.addEventListener('click', function() {
        const calc = calculateDuration();
        if (calc) {
            amount.value = calc.estimated;
        }
    });
});
</script>
@endpush
@endsection
