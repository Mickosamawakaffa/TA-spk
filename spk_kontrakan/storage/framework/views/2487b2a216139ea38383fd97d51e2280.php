<?php $__env->startSection('title', 'Kelola Booking'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-calendar-check me-2" style="color: #667eea;"></i>Kelola Booking
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>" style="color: #667eea;">Dashboard</a></li>
                    <li class="breadcrumb-item active">Booking</li>
                </ol>
            </nav>
        </div>
        <a href="<?php echo e(route('admin.bookings.create')); ?>" class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <i class="bi bi-plus-lg me-1"></i>Booking Baru
        </a>
    </div>

    
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Menunggu Konfirmasi</option>
                        <option value="confirmed" <?php echo e(request('status') == 'confirmed' ? 'selected' : ''); ?>>Dikonfirmasi</option>
                        <option value="checked_in" <?php echo e(request('status') == 'checked_in' ? 'selected' : ''); ?>>Sedang Ditempati</option>
                        <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>Selesai</option>
                        <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Kontrakan</label>
                    <select name="kontrakan_id" class="form-select">
                        <option value="">Semua Kontrakan</option>
                        <?php $__currentLoopData = $kontrakans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k->id); ?>" <?php echo e(request('kontrakan_id') == $k->id ? 'selected' : ''); ?>><?php echo e($k->nama); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn me-2" style="border: 2px solid #667eea; color: #667eea; background: transparent;">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    <a href="<?php echo e(route('admin.bookings.index')); ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <?php
            $stats = [
                ['status' => 'pending', 'label' => 'Menunggu', 'color' => 'warning', 'icon' => 'hourglass-split'],
                ['status' => 'confirmed', 'label' => 'Dikonfirmasi', 'color' => 'info', 'icon' => 'check-circle'],
                ['status' => 'checked_in', 'label' => 'Ditempati', 'color' => 'success', 'icon' => 'house-door'],
                ['status' => 'completed', 'label' => 'Selesai', 'color' => 'secondary', 'icon' => 'check-all'],
            ];
        ?>
        <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-<?php echo e($stat['icon']); ?> text-<?php echo e($stat['color']); ?> fs-3"></i>
                    <h4 class="fw-bold mb-0 mt-2">
                        <?php echo e(\App\Models\Booking::where('status', $stat['status'])->count()); ?>

                    </h4>
                    <small class="text-muted"><?php echo e($stat['label']); ?></small>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pb-0">
            <form id="bulkDeleteForm" action="<?php echo e(route('admin.bookings.bulk-destroy')); ?>" method="POST" class="d-flex flex-wrap gap-2 align-items-center">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" id="bulkDeleteAction" value="selected">
                <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
                <input type="hidden" name="kontrakan_id" value="<?php echo e(request('kontrakan_id')); ?>">

                <button type="button" id="btnDeleteSelected" class="btn btn-danger btn-sm" disabled>
                    <i class="bi bi-trash me-1"></i>Hapus Terpilih
                </button>
                <?php if(auth()->user()->role === 'super_admin'): ?>
                <button type="button" id="btnDeleteAll" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash3 me-1"></i>Hapus Semua
                </button>
                <small class="text-muted">Hapus Semua akan mengikuti filter aktif.</small>
                <?php else: ?>
                <small class="text-muted">Admin hanya dapat menghapus booking status pending atau dibatalkan.</small>
                <?php endif; ?>
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
                        <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $canDeleteBooking = auth()->user()->role === 'super_admin' || in_array($booking->status, ['pending', 'cancelled']);
                        ?>
                        <tr>
                            <td class="ps-3">
                                <input type="checkbox" class="form-check-input booking-checkbox" name="booking_ids[]" value="<?php echo e($booking->id); ?>" form="bulkDeleteForm" <?php echo e($canDeleteBooking ? '' : 'disabled'); ?>>
                            </td>
                            <td class="ps-3">
                                <span class="badge bg-light text-dark">#<?php echo e($booking->id); ?></span>
                            </td>
                            <td>
                                <a href="<?php echo e(route('kontrakan.show', $booking->kontrakan_id)); ?>" class="text-decoration-none fw-semibold">
                                    <?php echo e($booking->kontrakan->nama ?? '-'); ?>

                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold"><?php echo e($booking->tenant_name); ?></div>
                                <small class="text-muted"><?php echo e($booking->tenant_phone); ?></small>
                            </td>
                            <td>
                                <div><?php echo e($booking->start_date->format('d M Y')); ?></div>
                                <small class="text-muted">s/d <?php echo e($booking->end_date->format('d M Y')); ?></small>
                                <br>
                                <span class="badge bg-light text-dark"><?php echo e($booking->duration_days); ?> hari</span>
                            </td>
                            <td>
                                <span class="fw-semibold">Rp <?php echo e(number_format($booking->amount, 0, ',', '.')); ?></span>
                            </td>
                            <td>
                                <span class="badge <?php echo e($booking->status_badge_class); ?>">
                                    <?php echo e($booking->status_label); ?>

                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo e($booking->payment_status == 'paid' ? 'bg-success' : 'bg-secondary'); ?>">
                                    <?php echo e($booking->payment_status_label); ?>

                                </span>
                                <?php if($booking->payment_proof): ?>
                                <a href="<?php echo e(asset('storage/' . $booking->payment_proof)); ?>" target="_blank" class="btn btn-sm btn-outline-success ms-1" title="Lihat Bukti Transfer">
                                    <i class="bi bi-image"></i>
                                </a>
                                <?php endif; ?>
                                <?php if($booking->booking_source == 'user'): ?>
                                <span class="badge bg-info ms-1" title="Booking dari User">
                                    <i class="bi bi-person"></i>
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group">
                                    <a href="<?php echo e(route('admin.bookings.show', $booking->id)); ?>" class="btn btn-sm btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if($booking->status == 'pending'): ?>
                                    <form action="<?php echo e(route('admin.bookings.confirm', $booking->id)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-success" title="Konfirmasi" onclick="return confirm('Konfirmasi booking ini?')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <?php if($booking->status == 'confirmed'): ?>
                                    <form action="<?php echo e(route('admin.bookings.check-in', $booking->id)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-info" title="Check-in" onclick="return confirm('Penyewa check-in?')">
                                            <i class="bi bi-box-arrow-in-right"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <?php if($booking->status == 'checked_in'): ?>
                                    <form action="<?php echo e(route('admin.bookings.check-out', $booking->id)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-warning" title="Check-out" onclick="return confirm('Penyewa check-out?')">
                                            <i class="bi bi-box-arrow-right"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <?php if(auth()->user()->role == 'super_admin' || in_array($booking->status, ['pending', 'cancelled'])): ?>
                                    <form action="<?php echo e(route('admin.bookings.destroy', $booking->id)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus booking ini? Tindakan ini tidak dapat dibatalkan!')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                Belum ada data booking
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($bookings->hasPages()): ?>
        <div class="card-footer bg-transparent">
            <?php echo e($bookings->withQueryString()->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
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
                const filterActive = <?php echo (request('status') || request('kontrakan_id')) ? 'true' : 'false'; ?>;
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TA\spk_kontrakan\resources\views/admin/bookings/index.blade.php ENDPATH**/ ?>