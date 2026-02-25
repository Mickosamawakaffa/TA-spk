<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Admin Panel - SPK Kontrakan & Laundry'); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --topbar-height: 70px;
            
            /* Admin Theme - Purple Professional */
            --admin-primary: #667eea;
            --admin-primary-dark: #764ba2;
            --admin-primary-light: #818cf8;
            --admin-secondary: #7c3aed;
            --admin-accent: #8b5cf6;
            
            /* Kontrakan Theme - Purple Theme (Konsisten) */
            --kontrakan-primary: #667eea;
            --kontrakan-secondary: #764ba2;
            --kontrakan-accent: #5b21b6;
            --kontrakan-light: #e0e7ff;
            --kontrakan-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            
            /* Laundry Theme - Purple Theme (Konsisten) */
            --laundry-primary: #667eea;
            --laundry-secondary: #764ba2;
            --laundry-accent: #818cf8;
            --laundry-light: #e0e7ff;
            --laundry-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-card: #ffffff;
            --text-primary: #1c1917;
            --text-secondary: #78716c;
            --border-color: #e7e5e4;
            
            --topbar-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --sidebar-bg: #1e1b4b;
            --sidebar-hover: #312e81;
            
            --transition-fast: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-normal: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* Admin Topbar with Purple Theme */
        .admin-topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--topbar-height);
            background: var(--topbar-bg);
            backdrop-filter: blur(12px);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.2);
            z-index: 1030;
            display: flex;
            align-items: center;
            padding: 0 28px;
        }

        .admin-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: 900;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .admin-brand i {
            font-size: 1.8rem;
            background: rgba(255,255,255,0.2);
            padding: 10px;
            border-radius: 12px;
            backdrop-filter: blur(8px);
        }

        .admin-brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .admin-brand-title {
            font-size: 1.5rem;
            font-weight: 900;
        }

        .admin-brand-subtitle {
            font-size: 0.7rem;
            font-weight: 500;
            opacity: 0.9;
            letter-spacing: 1px;
        }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .admin-user {
            color: white;
            display: flex;
            align-items: center;
            gap: 14px;
            cursor: pointer;
            padding: 12px 20px;
            border-radius: 14px;
            transition: var(--transition-normal);
            backdrop-filter: blur(8px);
            border: 2px solid rgba(255,255,255,0.2);
        }

        .admin-user:hover {
            background: rgba(255,255,255,0.15);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: white;
            color: #667eea;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.2rem;
            box-shadow: var(--shadow-md);
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .user-name {
            font-weight: 700;
            font-size: 0.95rem;
            line-height: 1.2;
        }

        .user-role {
            font-size: 0.75rem;
            opacity: 0.9;
            background: rgba(255,255,255,0.2);
            padding: 2px 10px;
            border-radius: 20px;
            margin-top: 3px;
        }

        /* Dark Sidebar for Admin */
        .admin-sidebar {
            position: fixed;
            top: var(--topbar-height);
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--topbar-height));
            background: var(--sidebar-bg);
            box-shadow: var(--shadow-xl);
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1020;
            transition: var(--transition-normal);
        }

        .sidebar-menu {
            padding: 28px 0;
        }

        .menu-section {
            padding: 0 20px;
            margin-bottom: 32px;
        }

        .menu-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #a8a29e;
            font-weight: 800;
            margin-bottom: 14px;
            letter-spacing: 1.2px;
            padding-left: 8px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 14px 18px;
            color: #d6d3d1;
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 6px;
            transition: var(--transition-normal);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .menu-item:hover {
            background: var(--sidebar-hover);
            color: #818cf8;
            transform: translateX(6px);
        }

        .menu-item.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            transform: translateX(4px);
        }

        /* Kontrakan Menu - Purple Theme */
        .menu-item.menu-kontrakan.active {
            background: var(--kontrakan-gradient);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .menu-item.menu-kontrakan:hover {
            color: #818cf8;
        }
        
        .menu-item.menu-kontrakan.active i {
            color: white;
        }
        
        /* Laundry Menu - Purple Theme */
        .menu-item.menu-laundry.active {
            background: var(--laundry-gradient);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .menu-item.menu-laundry:hover {
            color: #818cf8;
        }
        
        .menu-item.menu-laundry.active i {
            color: white;
        }

        .menu-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: #c4b5fd;
            border-radius: 0 4px 4px 0;
        }
        
        /* Kontrakan accent indicator */
        .menu-item.menu-kontrakan.active::before {
            background: #e0e7ff;
        }
        
        /* Laundry accent indicator */
        .menu-item.menu-laundry.active::before {
            background: #e0e7ff;
        }

        .menu-item i {
            width: 22px;
            margin-right: 14px;
            font-size: 1.2rem;
            text-align: center;
        }

        /* Main Content */
        .admin-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 36px;
            min-height: calc(100vh - var(--topbar-height));
            background: var(--bg-primary);
        }

        /* Admin Badge */
        .admin-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        /* Mobile Responsive */
        @media (max-width: 1024px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }

            .admin-sidebar.show {
                transform: translateX(0);
                box-shadow: 0 0 40px rgba(0,0,0,0.5);
            }

            .admin-content {
                margin-left: 0;
                padding: 20px;
            }

            .user-info {
                display: none;
            }
        }

        /* Scrollbar Styling for Dark Sidebar */
        .admin-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .admin-sidebar::-webkit-scrollbar-track {
            background: #1c1917;
        }

        .admin-sidebar::-webkit-scrollbar-thumb {
            background: #57534e;
            border-radius: 10px;
        }

        .admin-sidebar::-webkit-scrollbar-thumb:hover {
            background: #78716c;
        }

        /* Menu Toggle Button */
        .menu-toggle {
            display: none;
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 10px 14px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1.3rem;
            transition: var(--transition-fast);
        }

        .menu-toggle:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.05);
        }

        @media (max-width: 1024px) {
            .menu-toggle {
                display: block;
            }
        }

        /* Overlay for Mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: var(--topbar-height);
            left: 0;
            width: 100%;
            height: calc(100vh - var(--topbar-height));
            background: rgba(0,0,0,0.7);
            z-index: 1019;
            backdrop-filter: blur(4px);
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* Custom Buttons for Admin - Konsistensi Warna Purple Theme */
        .btn-admin-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 10px;
            transition: var(--transition-normal);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-admin-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        /* Admin Outline Button */
        .btn-admin-outline {
            color: #667eea;
            border: 2px solid #667eea;
            background: transparent;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            transition: var(--transition-normal);
        }
        
        .btn-admin-outline:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        }
        
        /* Admin Solid Button */
        .btn-admin-solid {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            transition: var(--transition-normal);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .btn-admin-solid:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        /* Admin Text Color */
        .text-admin-primary {
            color: #667eea !important;
        }
        
        /* Admin Light Background */
        .bg-admin-light {
            background: rgba(102, 126, 234, 0.1) !important;
        }
        
        /* Admin Border */
        .border-admin {
            border-color: #667eea !important;
        }
        
        /* Form Control Focus - Purple */
        .admin-content .form-control:focus,
        .admin-content .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        /* Badge Admin */
        .badge-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
        }
        
        /* Card Header Admin */
        .card-header-admin {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-bottom: 2px solid #667eea;
            color: #667eea;
            font-weight: 700;
        }
        
        /* Pagination Admin */
        .admin-content .page-link {
            color: #667eea;
            border-color: #dee2e6;
        }
        
        .admin-content .page-link:hover {
            background-color: #667eea;
            border-color: #667eea;
            color: white;
        }
        
        .admin-content .page-item.active .page-link {
            background-color: #667eea;
            border-color: #667eea;
            color: white;
        }
        
        /* Alert Admin Info */
        .alert-admin-info {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border: none;
            border-left: 4px solid #667eea;
            color: #333;
        }
        
        .alert-admin-info i {
            color: #667eea;
        }
        
        /* Link Admin */
        .link-admin {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition-fast);
        }
        
        .link-admin:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        /* ========================================
           KONTRAKAN THEME UTILITIES 
           Purple Theme (Konsisten)
        ======================================== */
        .btn-kontrakan {
            background: var(--kontrakan-gradient);
            border: none;
            color: white;
            font-weight: 600;
        }
        
        .btn-kontrakan:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-kontrakan-outline {
            color: #667eea;
            border: 2px solid #667eea;
            background: transparent;
        }
        
        .btn-kontrakan-outline:hover {
            background: var(--kontrakan-gradient);
            color: white;
            border-color: #667eea;
        }
        
        .text-kontrakan {
            color: #667eea !important;
        }
        
        .bg-kontrakan-light {
            background: #e0e7ff !important;
        }
        
        .border-kontrakan {
            border-color: #667eea !important;
        }
        
        /* ========================================
           LAUNDRY THEME UTILITIES 
           Purple Theme (Konsisten)
        ======================================== */
        .btn-laundry-theme {
            background: var(--laundry-gradient);
            border: none;
            color: white;
            font-weight: 600;
        }
        
        .btn-laundry-theme:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-laundry-outline {
            color: #667eea;
            border: 2px solid #667eea;
            background: transparent;
        }
        
        .btn-laundry-outline:hover {
            background: var(--laundry-gradient);
            color: white;
            border-color: #667eea;
        }
        
        .text-laundry {
            color: #667eea !important;
        }
        
        .bg-laundry-light {
            background: #e0e7ff !important;
        }
        
        .border-laundry {
            border-color: #667eea !important;
        }
        
        /* Badge Variants */
        .badge-kontrakan {
            background: var(--kontrakan-gradient);
            color: white;
        }
        
        .badge-laundry {
            background: var(--laundry-gradient);
            color: white;
        }
        
        /* Card Header Variants */
        .card-header-kontrakan {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-bottom: 2px solid #667eea;
            color: #667eea;
        }
        
        .card-header-laundry {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-bottom: 2px solid #667eea;
            color: #667eea;
        }
    </style>

    <?php echo $__env->yieldContent('styles'); ?>
</head>
<body>

    <!-- Admin Topbar -->
    <div class="admin-topbar">
        <button class="menu-toggle" id="menuToggle">
            <i class="bi bi-list"></i>
        </button>

        <a href="<?php echo e(route('dashboard')); ?>" class="admin-brand">
            <i class="bi bi-building"></i>
            <div class="admin-brand-text">
                <span class="admin-brand-title">ADMIN PANEL</span>
                <span class="admin-brand-subtitle">PEMILIK BISNIS</span>
            </div>
        </a>

        <div class="topbar-right">
            <div class="dropdown">
                <div class="admin-user" data-bs-toggle="dropdown">
                    <div class="user-avatar">
                        <?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?>

                    </div>
                    <div class="user-info">
                        <span class="user-name"><?php echo e(Auth::user()->name); ?></span>
                        <span class="user-role">
                            <i class="bi bi-briefcase me-1"></i><?php echo e(ucfirst(Auth::user()->role)); ?>

                        </span>
                    </div>
                    <i class="bi bi-chevron-down ms-2"></i>
                </div>

                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3" style="min-width: 220px;">
                    <li class="px-3 py-2 border-bottom">
                        <small class="text-muted">Masuk sebagai</small>
                        <div class="fw-bold"><?php echo e(Auth::user()->name); ?></div>
                        <small class="text-muted"><?php echo e(Auth::user()->email); ?></small>
                    </li>
                    <li><a class="dropdown-item py-2" href="<?php echo e(route('welcome')); ?>"><i class="bi bi-house me-2"></i>Lihat Halaman User</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="<?php echo e(route('admin.logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="dropdown-item text-danger py-2">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Admin Sidebar -->
    <div class="admin-sidebar" id="sidebar">
        <div class="sidebar-menu">
            <!-- Dashboard Section -->
            <div class="menu-section">
                <div class="menu-section-title">Dashboard</div>
                <a href="<?php echo e(route('dashboard')); ?>" class="menu-item <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard Utama</span>
                </a>
            </div>

            <!-- Bisnis Management -->
            <div class="menu-section">
                <div class="menu-section-title">Kelola Bisnis</div>
                <a href="<?php echo e(route('kontrakan.index')); ?>" class="menu-item menu-kontrakan <?php echo e(request()->routeIs('kontrakan.*') ? 'active' : ''); ?>">
                    <i class="bi bi-building"></i>
                    <span>Data Kontrakan</span>
                </a>
                <a href="<?php echo e(route('laundry.index')); ?>" class="menu-item menu-laundry <?php echo e(request()->routeIs('laundry.*') ? 'active' : ''); ?>">
                    <i class="bi bi-water"></i>
                    <span>Data Laundry</span>
                </a>
                <a href="<?php echo e(route('admin.bookings.index')); ?>" class="menu-item <?php echo e(request()->routeIs('admin.bookings.*') ? 'active' : ''); ?>">
                    <i class="bi bi-calendar-check"></i>
                    <span>Data Booking</span>
                </a>
            </div>

            <!-- Kriteria & SAW -->
            <div class="menu-section">
                <div class="menu-section-title">Sistem SPK</div>
                <a href="<?php echo e(route('kriteria.index')); ?>" class="menu-item <?php echo e(request()->routeIs('kriteria.*') ? 'active' : ''); ?>">
                    <i class="bi bi-list-check"></i>
                    <span>Kriteria</span>
                </a>
                <a href="<?php echo e(route('saw.index')); ?>" class="menu-item <?php echo e(request()->routeIs('saw.index') || request()->routeIs('saw.hasil') ? 'active' : ''); ?>">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Hasil Perhitungan</span>
                </a>
            </div>

            <?php if(Auth::user()->role === 'super_admin'): ?>
            <!-- Super Admin Only -->
            <div class="menu-section">
                <div class="menu-section-title">Super Admin</div>
                <a href="<?php echo e(route('admin.users.index')); ?>" class="menu-item <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
                    <i class="bi bi-people"></i>
                    <span>Manajemen User</span>
                </a>
                <a href="<?php echo e(route('admin.activity-logs.index')); ?>" class="menu-item <?php echo e(request()->routeIs('admin.activity-logs.*') ? 'active' : ''); ?>">
                    <i class="bi bi-clock-history"></i>
                    <span>Activity Logs</span>
                </a>
                <a href="<?php echo e(route('admin.backup.index')); ?>" class="menu-item <?php echo e(request()->routeIs('admin.backup.*') ? 'active' : ''); ?>">
                    <i class="bi bi-database"></i>
                    <span>Backup Database</span>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div class="admin-content">
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

        <?php if(session('warning')): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?php echo e(session('warning')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>

    <!-- Menu Toggle Script -->
    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        menuToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        });

        sidebarOverlay?.addEventListener('click', () => {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    </script>

    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\TA\spk_kontrakan\resources\views/layouts/admin.blade.php ENDPATH**/ ?>