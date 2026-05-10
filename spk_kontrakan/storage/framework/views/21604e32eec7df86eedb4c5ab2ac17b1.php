

<?php $__env->startSection('title', 'Profil Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2 px-md-4">
    <style>
        .profile-hero {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.95) 0%, rgba(118, 75, 162, 0.95) 100%);
            color: white;
            border-radius: 18px;
            padding: 2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 16px 32px rgba(102, 126, 234, 0.25);
            position: relative;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .profile-hero::after {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 40px;
            transform: rotate(18deg);
        }

        .profile-hero-main {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            position: relative;
            z-index: 1;
        }

        .profile-avatar {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .profile-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            position: relative;
            z-index: 1;
        }

        .profile-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.85rem;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .profile-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        }

        .profile-card .card-header {
            background: rgba(102, 126, 234, 0.08);
            border-bottom: 2px solid rgba(102, 126, 234, 0.2);
            font-weight: 700;
            color: #4f46e5;
        }

        .profile-list {
            display: grid;
            gap: 1rem;
        }

        .profile-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 0.85rem 1rem;
            background: rgba(248, 250, 252, 0.9);
            border-radius: 12px;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .profile-row span {
            color: #64748b;
            font-weight: 600;
        }

        .profile-row strong {
            color: #1f2937;
            font-weight: 700;
            text-align: right;
        }

        .profile-note {
            background: rgba(14, 116, 144, 0.08);
            border-left: 4px solid #0ea5e9;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            color: #0f172a;
        }

        @media (max-width: 768px) {
            .profile-hero {
                padding: 1.5rem;
            }

            .profile-avatar {
                width: 64px;
                height: 64px;
            }

            .profile-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .profile-row strong {
                text-align: left;
            }
        }
    </style>

    <div class="profile-hero">
        <div class="profile-hero-main">
            <div class="profile-avatar"><?php echo e(strtoupper(substr($user->name, 0, 1))); ?></div>
            <div>
                <h3 class="mb-1 fw-bold"><?php echo e($user->name); ?></h3>
                <div class="opacity-75"><?php echo e($user->email); ?></div>
            </div>
        </div>
        <div class="profile-meta">
            <div class="profile-chip"><i class="bi bi-briefcase"></i><?php echo e($user->getRoleLabel()); ?></div>
            <div class="profile-chip"><i class="bi bi-calendar-event"></i><?php echo e(now()->format('d M Y')); ?></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card profile-card h-100">
                <div class="card-header py-3 px-4">Perbarui Profil</div>
                <div class="card-body p-4">
                    <form method="POST" action="<?php echo e(route('admin.profile.update')); ?>" class="d-grid gap-3">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div>
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $user->name)); ?>" required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <small class="text-danger"><?php echo e($message); ?></small>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $user->email)); ?>" required>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <small class="text-danger"><?php echo e($message); ?></small>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="form-label fw-semibold">Nomor Telepon</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone', $user->phone)); ?>" placeholder="Opsional">
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <small class="text-danger"><?php echo e($message); ?></small>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="form-label fw-semibold">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak mengganti">
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <small class="text-danger"><?php echo e($message); ?></small>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="form-label fw-semibold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                        </div>

                        <button type="submit" class="btn btn-admin-solid w-100 py-3">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card profile-card h-100">
                <div class="card-header py-3 px-4">Ringkasan</div>
                <div class="card-body p-4 d-flex flex-column gap-3">
                    <div class="profile-note">
                        Kelola data kontrakan dan booking dengan cepat melalui menu sidebar.
                    </div>
                    <div class="profile-note">
                        Gunakan menu dropdown kanan atas untuk logout atau kembali ke halaman user.
                    </div>
                    <div class="profile-row">
                        <span>Role</span>
                        <strong><?php echo e($user->getRoleLabel()); ?></strong>
                    </div>
                    <div class="profile-row">
                        <span>Status</span>
                        <strong>Aktif</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\TA\spk_kontrakan\resources\views/admin/profile.blade.php ENDPATH**/ ?>