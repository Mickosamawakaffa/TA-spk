@extends('layouts.admin')

@section('title', 'Detail Booking #' . $booking->id)

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-calendar-check me-2 text-primary"></i>Detail Booking #{{ $booking->id }}
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Booking</a></li>
                    <li class="breadcrumb-item active">#{{ $booking->id }}</li>
                </ol>
            </nav>
        </div>
        <div>
            @if(in_array($booking->status, ['pending', 'confirmed']))
            <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            @endif
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Status Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Status Booking</h5>
                            <span class="badge {{ $booking->status_badge_class }} fs-6 px-3 py-2">
                                {{ $booking->status_label }}
                            </span>
                        </div>
                        <div>
                            <span class="badge {{ $booking->payment_status == 'paid' ? 'bg-success' : 'bg-secondary' }} fs-6 px-3 py-2">
                                {{ $booking->payment_status_label }}
                            </span>
                        </div>
                    </div>
                    
                    {{-- Timeline --}}
                    <div class="mt-4">
                        <div class="d-flex justify-content-between text-center">
                            <div class="flex-fill">
                                <div class="rounded-circle {{ $booking->created_at ? 'bg-success' : 'bg-secondary' }} text-white d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-plus-lg"></i>
                                </div>
                                <div class="small mt-2">Dibuat</div>
                                <div class="text-muted small">{{ $booking->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="flex-fill">
                                <div class="rounded-circle {{ $booking->confirmed_at ? 'bg-success' : 'bg-secondary' }} text-white d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <div class="small mt-2">Dikonfirmasi</div>
                                <div class="text-muted small">{{ $booking->confirmed_at?->format('d/m/Y H:i') ?? '-' }}</div>
                            </div>
                            <div class="flex-fill">
                                <div class="rounded-circle {{ $booking->checked_in_at ? 'bg-success' : 'bg-secondary' }} text-white d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-box-arrow-in-right"></i>
                                </div>
                                <div class="small mt-2">Check-in</div>
                                <div class="text-muted small">{{ $booking->checked_in_at?->format('d/m/Y H:i') ?? '-' }}</div>
                            </div>
                            <div class="flex-fill">
                                <div class="rounded-circle {{ $booking->checked_out_at ? 'bg-success' : 'bg-secondary' }} text-white d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-box-arrow-right"></i>
                                </div>
                                <div class="small mt-2">Check-out</div>
                                <div class="text-muted small">{{ $booking->checked_out_at?->format('d/m/Y H:i') ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Kontrakan --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="bi bi-house me-2"></i>Info Kontrakan</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex">
                        @if($booking->kontrakan->foto)
                        <img src="{{ asset('uploads/kontrakan/' . $booking->kontrakan->foto) }}" 
                            alt="{{ $booking->kontrakan->nama }}" 
                            class="rounded me-3" style="width: 120px; height: 90px; object-fit: cover;">
                        @else
                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 120px; height: 90px;">
                            <i class="bi bi-house text-muted fs-1"></i>
                        </div>
                        @endif
                        <div>
                            <h5 class="mb-1">
                                <a href="{{ route('kontrakan.show', $booking->kontrakan_id) }}" class="text-decoration-none">
                                    {{ $booking->kontrakan->nama }}
                                </a>
                            </h5>
                            <p class="text-muted mb-1">
                                <i class="bi bi-geo-alt me-1"></i>{{ $booking->kontrakan->alamat }}
                            </p>
                            <span class="badge {{ $booking->kontrakan->status_badge_class }}">
                                {{ $booking->kontrakan->status_label }}
                            </span>
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
                            <label class="text-muted small">Nama Penyewa</label>
                            <div class="fw-semibold">{{ $booking->tenant_name }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">No. HP/WhatsApp</label>
                            <div class="fw-semibold">
                                <a href="https://wa.me/62{{ ltrim($booking->tenant_phone, '0') }}" target="_blank" class="text-decoration-none">
                                    {{ $booking->tenant_phone }} <i class="bi bi-whatsapp text-success"></i>
                                </a>
                            </div>
                        </div>
                        @if($booking->user)
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Dibuat oleh</label>
                            <div class="fw-semibold">{{ $booking->user->name }}</div>
                        </div>
                        @endif
                        @if($booking->notes)
                        <div class="col-12">
                            <label class="text-muted small">Catatan</label>
                            <div class="bg-light p-3 rounded">{{ $booking->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Cancellation Info --}}
            @if($booking->status == 'cancelled' && $booking->cancellation_reason)
            <div class="card border-0 shadow-sm mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-x-circle me-2"></i>Info Pembatalan</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Dibatalkan pada:</strong> {{ $booking->cancelled_at->format('d M Y H:i') }}</p>
                    <p class="mb-0"><strong>Alasan:</strong> {{ $booking->cancellation_reason }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Periode & Biaya --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Periode & Biaya</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Periode Sewa</label>
                        <div class="fw-semibold">
                            {{ $booking->start_date->format('d M Y') }} - {{ $booking->end_date->format('d M Y') }}
                        </div>
                        <span class="badge bg-light text-dark">{{ $booking->duration_days }} hari ({{ $booking->duration_months }} bulan)</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Biaya Sewa:</span>
                        <span class="fw-bold fs-5 text-success">Rp {{ number_format($booking->amount, 0, ',', '.') }}</span>
                    </div>
                    @if($booking->payment_status == 'paid')
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Dibayar pada:</span>
                        <span>{{ $booking->paid_at?->format('d/m/Y') ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Metode:</span>
                        <span>{{ ucfirst($booking->payment_method ?? '-') }}</span>
                    </div>
                    @endif
                    @if($booking->payment_proof)
                    <div class="mt-3">
                        <p class="text-muted small mb-2"><i class="bi bi-image me-1"></i>Bukti Pembayaran:</p>
                        <a href="{{ asset('storage/' . $booking->payment_proof) }}" target="_blank">
                            <img src="{{ asset('storage/' . $booking->payment_proof) }}"
                                 alt="Bukti Pembayaran"
                                 class="img-fluid rounded border"
                                 style="max-height: 250px; object-fit: contain; width: 100%;">
                        </a>
                        <div class="text-center mt-1">
                            <a href="{{ asset('storage/' . $booking->payment_proof) }}" target="_blank"
                               class="btn btn-sm btn-outline-success mt-1">
                                <i class="bi bi-arrows-fullscreen me-1"></i>Lihat Full
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Aksi</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    {{-- Confirm --}}
                    @if($booking->status == 'pending')
                    <form action="{{ route('admin.bookings.confirm', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Konfirmasi booking ini?')">
                            <i class="bi bi-check-lg me-2"></i>Konfirmasi Booking
                        </button>
                    </form>
                    @endif

                    {{-- Check-in --}}
                    @if($booking->status == 'confirmed')
                    <form action="{{ route('admin.bookings.check-in', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-info w-100" onclick="return confirm('Check-in penyewa?')">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Check-in Penyewa
                        </button>
                    </form>
                    @endif

                    {{-- Check-out --}}
                    @if($booking->status == 'checked_in')
                    <form action="{{ route('admin.bookings.check-out', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Check-out penyewa?')">
                            <i class="bi bi-box-arrow-right me-2"></i>Check-out Penyewa
                        </button>
                    </form>
                    @endif

                    {{-- Toggle Payment Status --}}
                    @if(!in_array($booking->status, ['cancelled', 'completed']))
                    <form action="{{ route('admin.bookings.toggle-payment', $booking->id) }}" method="POST">
                        @csrf
                        @if($booking->payment_status != 'paid')
                        <div class="input-group">
                            <select name="payment_method" class="form-select">
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                                <option value="other">Lainnya</option>
                            </select>
                            <button type="submit" class="btn btn-outline-success" onclick="return confirm('Tandai pembayaran LUNAS?')">
                                <i class="bi bi-cash-coin me-1"></i>Lunas
                            </button>
                        </div>
                        @else
                        <button type="submit" class="btn btn-outline-warning w-100" onclick="return confirm('Ubah status menjadi BELUM LUNAS?')">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Tandai Belum Lunas
                        </button>
                        @endif
                    </form>
                    @endif

                    {{-- Cancel --}}
                    @if($booking->canBeCancelled())
                    <hr>
                    <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                        <i class="bi bi-x-circle me-2"></i>Batalkan Booking
                    </button>
                    @endif

                    {{-- Delete --}}
                    @if(in_array($booking->status, ['pending', 'cancelled']))
                    <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Hapus booking ini secara permanen?')">
                            <i class="bi bi-trash me-2"></i>Hapus Booking
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
@if($booking->canBeCancelled())
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Batalkan Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Tindakan ini tidak dapat dibatalkan. Status kontrakan akan dikembalikan ke "Tersedia".
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Pembatalan</label>
                        <textarea name="cancellation_reason" class="form-control" rows="3" placeholder="Masukkan alasan pembatalan (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
