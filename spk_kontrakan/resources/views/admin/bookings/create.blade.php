@extends('layouts.admin')

@section('title', 'Buat Booking Baru')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-1">
            <i class="bi bi-calendar-plus me-2 text-primary"></i>Buat Booking Baru
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Booking</a></li>
                <li class="breadcrumb-item active">Buat Baru</li>
            </ol>
        </nav>
    </div>

    {{-- Alert --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>
        <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('admin.bookings.store') }}" method="POST">
        @csrf
        <div class="row">
            {{-- Form Utama --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0"><i class="bi bi-house me-2"></i>Pilih Kontrakan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Kontrakan <span class="text-danger">*</span></label>
                            <select name="kontrakan_id" id="kontrakan_id" class="form-select form-select-lg @error('kontrakan_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kontrakan --</option>
                                @foreach($kontrakans as $k)
                                <option value="{{ $k->id }}" 
                                    data-harga="{{ $k->harga }}"
                                    {{ (old('kontrakan_id', $selectedKontrakan?->id) == $k->id) ? 'selected' : '' }}>
                                    {{ $k->nama }} - Rp {{ number_format($k->harga, 0, ',', '.') }}/bulan
                                </option>
                                @endforeach
                            </select>
                            @error('kontrakan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Info Kontrakan Terpilih --}}
                        <div id="kontrakan-info" class="alert alert-info d-none">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle fs-4 me-3"></i>
                                <div>
                                    <strong id="info-nama"></strong>
                                    <div class="small">Harga: <span id="info-harga"></span>/bulan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0"><i class="bi bi-calendar-range me-2"></i>Periode Sewa</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" 
                                    class="form-control @error('start_date') is-invalid @enderror" 
                                    value="{{ old('start_date', date('Y-m-d')) }}" 
                                    min="{{ date('Y-m-d') }}" required>
                                @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" 
                                    class="form-control @error('end_date') is-invalid @enderror" 
                                    value="{{ old('end_date', date('Y-m-d', strtotime('+1 month'))) }}" required>
                                @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Availability Check Result --}}
                        <div id="availability-result" class="d-none"></div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Data Penyewa</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Penyewa <span class="text-danger">*</span></label>
                                <input type="text" name="tenant_name" class="form-control @error('tenant_name') is-invalid @enderror" 
                                    value="{{ old('tenant_name') }}" placeholder="Nama lengkap penyewa" required>
                                @error('tenant_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. HP/WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" name="tenant_phone" class="form-control @error('tenant_phone') is-invalid @enderror" 
                                    value="{{ old('tenant_phone') }}" placeholder="08xxxxxxxxxx" required>
                                @error('tenant_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan (opsional)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Ringkasan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Biaya Sewa</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="amount" id="amount" class="form-control" 
                                    value="{{ old('amount') }}" placeholder="Otomatis dari harga kontrakan">
                            </div>
                            <small class="text-muted">Kosongkan untuk menggunakan harga default kontrakan</small>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Durasi:</span>
                            <span id="duration-display" class="fw-semibold">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Estimasi:</span>
                            <span id="total-display" class="fw-bold text-success fs-5">-</span>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                                <i class="bi bi-check-lg me-2"></i>Buat Booking
                            </button>
                            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kontrakanSelect = document.getElementById('kontrakan_id');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const amountInput = document.getElementById('amount');
    const durationDisplay = document.getElementById('duration-display');
    const totalDisplay = document.getElementById('total-display');
    const availabilityResult = document.getElementById('availability-result');
    const kontrakanInfo = document.getElementById('kontrakan-info');
    const submitBtn = document.getElementById('submit-btn');

    let selectedHarga = 0;

    // Update kontrakan info
    kontrakanSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
            selectedHarga = parseFloat(option.dataset.harga) || 0;
            document.getElementById('info-nama').textContent = option.text.split(' - ')[0];
            document.getElementById('info-harga').textContent = 'Rp ' + selectedHarga.toLocaleString('id-ID');
            kontrakanInfo.classList.remove('d-none');
            
            if (!amountInput.value) {
                amountInput.placeholder = selectedHarga.toLocaleString('id-ID');
            }
        } else {
            kontrakanInfo.classList.add('d-none');
            selectedHarga = 0;
        }
        updateCalculations();
        checkAvailability();
    });

    // Update end date min
    startDate.addEventListener('change', function() {
        endDate.min = this.value;
        if (endDate.value < this.value) {
            endDate.value = this.value;
        }
        updateCalculations();
        checkAvailability();
    });

    endDate.addEventListener('change', function() {
        updateCalculations();
        checkAvailability();
    });

    amountInput.addEventListener('input', updateCalculations);

    function updateCalculations() {
        if (startDate.value && endDate.value) {
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            const diffMonths = (diffDays / 30).toFixed(1);
            
            durationDisplay.textContent = diffDays + ' hari (' + diffMonths + ' bulan)';
            
            const amount = parseFloat(amountInput.value) || selectedHarga;
            const totalEstimate = amount * Math.ceil(diffDays / 30);
            totalDisplay.textContent = 'Rp ' + totalEstimate.toLocaleString('id-ID');
        }
    }

    function checkAvailability() {
        const kontrakanId = kontrakanSelect.value;
        const start = startDate.value;
        const end = endDate.value;

        if (!kontrakanId || !start || !end) {
            availabilityResult.classList.add('d-none');
            return;
        }

        fetch('{{ route("admin.bookings.check-availability") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                kontrakan_id: kontrakanId,
                start_date: start,
                end_date: end
            })
        })
        .then(response => response.json())
        .then(data => {
            availabilityResult.classList.remove('d-none');
            if (data.available) {
                availabilityResult.className = 'alert alert-success';
                availabilityResult.innerHTML = '<i class="bi bi-check-circle me-2"></i>Kontrakan tersedia untuk periode ini!';
                submitBtn.disabled = false;
            } else {
                availabilityResult.className = 'alert alert-danger';
                let html = '<i class="bi bi-exclamation-circle me-2"></i><strong>Tidak tersedia!</strong> Ada booking lain yang bentrok:';
                html += '<ul class="mb-0 mt-2">';
                data.conflicts.forEach(c => {
                    html += `<li>${c.tenant_name}: ${c.start_date} - ${c.end_date} (${c.status})</li>`;
                });
                html += '</ul>';
                availabilityResult.innerHTML = html;
                submitBtn.disabled = true;
            }
        })
        .catch(err => {
            console.error('Error checking availability:', err);
        });
    }

    // Initial calculation
    if (kontrakanSelect.value) {
        kontrakanSelect.dispatchEvent(new Event('change'));
    }
    updateCalculations();
});
</script>
@endpush
@endsection
