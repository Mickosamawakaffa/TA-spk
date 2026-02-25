@extends('layouts.admin')

@section('title', 'Riwayat Booking - ' . $kontrakan->nama)

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Booking
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kontrakan.index') }}">Kontrakan</a></li>
                    <li class="breadcrumb-item active">Riwayat Booking</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.bookings.create') }}?kontrakan_id={{ $kontrakan->id }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Buat Booking
            </a>
            <a href="{{ route('kontrakan.show', $kontrakan->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Info Kontrakan --}}
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    @if($kontrakan->foto)
                    <img src="{{ asset('uploads/kontrakan/' . $kontrakan->foto) }}" 
                        alt="{{ $kontrakan->nama }}" 
                        class="img-fluid rounded mb-3" style="width: 100%; height: 200px; object-fit: cover;">
                    @else
                    <div class="bg-light rounded mb-3 d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="bi bi-house text-muted" style="font-size: 4rem;"></i>
                    </div>
                    @endif
                    
                    <h5 class="mb-2">{{ $kontrakan->nama }}</h5>
                    <p class="text-muted mb-2">
                        <i class="bi bi-geo-alt me-1"></i>{{ $kontrakan->alamat }}
                    </p>
                    
                    <div class="mb-3">
                        <span class="badge {{ $kontrakan->status_badge_class }} fs-6 px-3 py-2">
                            {{ $kontrakan->status_label }}
                        </span>
                    </div>
                    
                    <hr>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 mb-0 text-primary">{{ $bookings->total() }}</div>
                            <div class="small text-muted">Total Booking</div>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-0 text-success">{{ $bookings->where('status', 'completed')->count() + $bookings->where('status', 'checked_in')->count() }}</div>
                            <div class="small text-muted">Berhasil</div>
                        </div>
                    </div>
                    
                    @if($kontrakan->occupied_until)
                    <hr>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-calendar-check me-2"></i>
                        <strong>Terisi sampai:</strong><br>
                        {{ \Carbon\Carbon::parse($kontrakan->occupied_until)->format('d M Y') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Booking History --}}
        <div class="col-lg-8">
            @if($bookings->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Penyewa</th>
                                    <th>Periode</th>
                                    <th>Biaya</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td><span class="badge bg-light text-dark">#{{ $booking->id }}</span></td>
                                    <td>
                                        <div class="fw-semibold">{{ $booking->tenant_name }}</div>
                                        <small class="text-muted">{{ $booking->tenant_phone }}</small>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <i class="bi bi-calendar me-1"></i>
                                            {{ $booking->start_date->format('d M Y') }}
                                        </div>
                                        <div class="small text-muted">
                                            <i class="bi bi-arrow-right me-1"></i>
                                            {{ $booking->end_date->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-success">Rp {{ number_format($booking->amount, 0, ',', '.') }}</span>
                                        <div>
                                            @if($booking->payment_status == 'paid')
                                            <span class="badge bg-success-subtle text-success">Lunas</span>
                                            @else
                                            <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($booking->payment_status) }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $booking->status_badge_class }}">
                                            {{ $booking->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if($bookings->hasPages())
                <div class="card-footer bg-transparent">
                    {{ $bookings->links() }}
                </div>
                @endif
            </div>
            @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Belum Ada Riwayat Booking</h5>
                    <p class="text-muted">Kontrakan ini belum pernah dibooking sebelumnya.</p>
                    <a href="{{ route('admin.bookings.create') }}?kontrakan_id={{ $kontrakan->id }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Buat Booking Pertama
                    </a>
                </div>
            </div>
            @endif

            {{-- Calendar View --}}
            @if($bookings->count() > 0)
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Timeline Booking</h5>
                </div>
                <div class="card-body">
                    @foreach($bookings->take(5) as $booking)
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0 me-3">
                            <div class="rounded-circle bg-{{ $booking->status == 'checked_in' ? 'success' : ($booking->status == 'completed' ? 'primary' : ($booking->status == 'cancelled' ? 'danger' : 'secondary')) }} text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                @if($booking->status == 'checked_in')
                                <i class="bi bi-house-door-fill"></i>
                                @elseif($booking->status == 'completed')
                                <i class="bi bi-check-circle-fill"></i>
                                @elseif($booking->status == 'cancelled')
                                <i class="bi bi-x-circle-fill"></i>
                                @else
                                <i class="bi bi-clock"></i>
                                @endif
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $booking->tenant_name }}</h6>
                                    <p class="text-muted mb-0 small">
                                        {{ $booking->start_date->format('d M Y') }} - {{ $booking->end_date->format('d M Y') }}
                                        <span class="badge {{ $booking->status_badge_class }} ms-2">{{ $booking->status_label }}</span>
                                    </p>
                                </div>
                                <span class="text-success fw-semibold">Rp {{ number_format($booking->amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    @if(!$loop->last)
                    <hr class="my-2">
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1) !important;
}
.bg-secondary-subtle {
    background-color: rgba(108, 117, 125, 0.1) !important;
}
</style>
@endsection
