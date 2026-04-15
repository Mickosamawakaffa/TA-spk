@extends('layouts.admin')

@section('title', 'Kelola Booking')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-calendar-check me-2" style="color: #667eea;"></i>Kelola Booking
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color: #667eea;">Dashboard</a></li>
                    <li class="breadcrumb-item active">Booking</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.bookings.create') }}" class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <i class="bi bi-plus-lg me-1"></i>Booking Baru
        </a>
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

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                        <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Sedang Ditempati</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Kontrakan</label>
                    <select name="kontrakan_id" class="form-select">
                        <option value="">Semua Kontrakan</option>
                        @foreach($kontrakans as $k)
                        <option value="{{ $k->id }}" {{ request('kontrakan_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn me-2" style="border: 2px solid #667eea; color: #667eea; background: transparent;">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        @php
            $stats = [
                ['status' => 'pending', 'label' => 'Menunggu', 'color' => 'warning', 'icon' => 'hourglass-split'],
                ['status' => 'confirmed', 'label' => 'Dikonfirmasi', 'color' => 'info', 'icon' => 'check-circle'],
                ['status' => 'checked_in', 'label' => 'Ditempati', 'color' => 'success', 'icon' => 'house-door'],
                ['status' => 'completed', 'label' => 'Selesai', 'color' => 'secondary', 'icon' => 'check-all'],
            ];
        @endphp
        @foreach($stats as $stat)
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-{{ $stat['icon'] }} text-{{ $stat['color'] }} fs-3"></i>
                    <h4 class="fw-bold mb-0 mt-2">
                        {{ \App\Models\Booking::where('status', $stat['status'])->count() }}
                    </h4>
                    <small class="text-muted">{{ $stat['label'] }}</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pb-0">
            <form id="bulkDeleteForm" action="{{ route('admin.bookings.bulk-destroy') }}" method="POST" class="d-flex flex-wrap gap-2 align-items-center">
                @csrf
                <input type="hidden" name="action" id="bulkDeleteAction" value="selected">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="kontrakan_id" value="{{ request('kontrakan_id') }}">

                <button type="button" id="btnDeleteSelected" class="btn btn-danger btn-sm" disabled>
                    <i class="bi bi-trash me-1"></i>Hapus Terpilih
                </button>
                @if(auth()->user()->role === 'super_admin')
                <button type="button" id="btnDeleteAll" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash3 me-1"></i>Hapus Semua
                </button>
                <small class="text-muted">Hapus Semua akan mengikuti filter aktif.</small>
                @else
                <small class="text-muted">Admin hanya dapat menghapus booking status pending atau dibatalkan.</small>
                @endif
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3" style="width: 44px;">
                                <input type="checkbox" class="form-check-input" id="selectAllBookings" title="Pilih semua di halaman ini">
                            </th>
                            <th class="ps-3">ID</th>
                            <th>Kontrakan</th>
                            <th>Penyewa</th>
                            <th>Periode</th>
                            <th>Biaya</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        @php
                            $canDeleteBooking = auth()->user()->role === 'super_admin' || in_array($booking->status, ['pending', 'cancelled']);
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <input type="checkbox" class="form-check-input booking-checkbox" name="booking_ids[]" value="{{ $booking->id }}" form="bulkDeleteForm" {{ $canDeleteBooking ? '' : 'disabled' }}>
                            </td>
                            <td class="ps-3">
                                <span class="badge bg-light text-dark">#{{ $booking->id }}</span>
                            </td>
                            <td>
                                <a href="{{ route('kontrakan.show', $booking->kontrakan_id) }}" class="text-decoration-none fw-semibold">
                                    {{ $booking->kontrakan->nama ?? '-' }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $booking->tenant_name }}</div>
                                <small class="text-muted">{{ $booking->tenant_phone }}</small>
                            </td>
                            <td>
                                <div>{{ $booking->start_date->format('d M Y') }}</div>
                                <small class="text-muted">s/d {{ $booking->end_date->format('d M Y') }}</small>
                                <br>
                                <span class="badge bg-light text-dark">{{ $booking->duration_days }} hari</span>
                            </td>
                            <td>
                                <span class="fw-semibold">Rp {{ number_format($booking->amount, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $booking->status_badge_class }}">
                                    {{ $booking->status_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $booking->payment_status == 'paid' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $booking->payment_status_label }}
                                </span>
                                @if($booking->payment_proof)
                                <a href="{{ asset('storage/' . $booking->payment_proof) }}" target="_blank" class="btn btn-sm btn-outline-success ms-1" title="Lihat Bukti Transfer">
                                    <i class="bi bi-image"></i>
                                </a>
                                @endif
                                @if($booking->booking_source == 'user')
                                <span class="badge bg-info ms-1" title="Booking dari User">
                                    <i class="bi bi-person"></i>
                                </span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group">
                                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($booking->status == 'pending')
                                    <form action="{{ route('admin.bookings.confirm', $booking->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Konfirmasi" onclick="return confirm('Konfirmasi booking ini?')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if($booking->status == 'confirmed')
                                    <form action="{{ route('admin.bookings.check-in', $booking->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-info" title="Check-in" onclick="return confirm('Penyewa check-in?')">
                                            <i class="bi bi-box-arrow-in-right"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if($booking->status == 'checked_in')
                                    <form action="{{ route('admin.bookings.check-out', $booking->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning" title="Check-out" onclick="return confirm('Penyewa check-out?')">
                                            <i class="bi bi-box-arrow-right"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if(auth()->user()->role == 'super_admin' || in_array($booking->status, ['pending', 'cancelled']))
                                    <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus booking ini? Tindakan ini tidak dapat dibatalkan!')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                Belum ada data booking
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($bookings->hasPages())
        <div class="card-footer bg-transparent">
            {{ $bookings->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('selectAllBookings');
        const checkboxes = Array.from(document.querySelectorAll('.booking-checkbox'));
        const selectableCheckboxes = checkboxes.filter(item => !item.disabled);
        const btnDeleteSelected = document.getElementById('btnDeleteSelected');
        const btnDeleteAll = document.getElementById('btnDeleteAll');
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');
        const bulkDeleteAction = document.getElementById('bulkDeleteAction');

        function showWarning(message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: message,
                    confirmButtonColor: '#d33'
                });
                return;
            }

            alert(message);
        }

        function confirmDelete(message, onConfirm) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Konfirmasi Hapus',
                    text: message,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        onConfirm();
                    }
                });
                return;
            }

            if (confirm(message)) {
                onConfirm();
            }
        }

        function checkedCount() {
            return selectableCheckboxes.filter(item => item.checked).length;
        }

        function refreshSelectedState() {
            const count = checkedCount();
            btnDeleteSelected.disabled = count === 0;
            btnDeleteSelected.innerHTML = count > 0
                ? `<i class="bi bi-trash me-1"></i>Hapus Terpilih (${count})`
                : '<i class="bi bi-trash me-1"></i>Hapus Terpilih';

            if (selectAll) {
                selectAll.checked = selectableCheckboxes.length > 0 && count === selectableCheckboxes.length;
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                selectableCheckboxes.forEach(item => {
                    item.checked = selectAll.checked;
                });
                refreshSelectedState();
            });
        }

        checkboxes.forEach(item => {
            item.addEventListener('change', refreshSelectedState);
        });

        btnDeleteSelected.addEventListener('click', function () {
            const count = checkedCount();

            if (count === 0) {
                showWarning('Pilih minimal 1 booking yang ingin dihapus.');
                return;
            }

            confirmDelete(`Yakin ingin menghapus ${count} booking terpilih? Tindakan ini tidak dapat dibatalkan.`, function () {
                bulkDeleteAction.value = 'selected';
                bulkDeleteForm.submit();
            });
        });

        if (btnDeleteAll) {
            btnDeleteAll.addEventListener('click', function () {
                const filterActive = {!! (request('status') || request('kontrakan_id')) ? 'true' : 'false' !!};
                const message = filterActive
                    ? 'Yakin ingin menghapus semua booking sesuai filter saat ini? Tindakan ini tidak dapat dibatalkan.'
                    : 'Yakin ingin menghapus semua data booking? Tindakan ini tidak dapat dibatalkan.';

                confirmDelete(message, function () {
                    bulkDeleteAction.value = 'all';
                    bulkDeleteForm.submit();
                });
            });
        }

        refreshSelectedState();
    });
</script>
@endsection
