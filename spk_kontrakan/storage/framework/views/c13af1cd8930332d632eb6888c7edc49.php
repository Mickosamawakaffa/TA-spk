<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'SPK Kontrakan & Laundry'); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --topbar-height: 65px;
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --border-color: #e2e8f0;
            --topbar-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --sidebar-bg: #ffffff;
            --transition-fast: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-normal: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            
            /* Primary Colors */
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --primary-color-rgb: 102, 126, 234;
            --secondary-color-rgb: 118, 75, 162;
            
            /* Accent Colors */
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --accent-color: #f59e0b;
        }

        /* Dark Mode Theme */
        html.dark-mode {
            --bg-primary: #1a202c;
            --bg-secondary: #2d3748;
            --text-primary: #f7fafc;
            --text-secondary: #cbd5e0;
            --border-color: #4a5568;
            --sidebar-bg: #2d3748;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Topbar */
        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--topbar-height);
            background: var(--topbar-bg);
            backdrop-filter: blur(12px);
            box-shadow: var(--shadow-lg);
            z-index: 1030;
            display: flex;
            align-items: center;
            padding: 0 24px;
            transition: var(--transition-normal);
        }

        .topbar-brand {
            color: white;
            font-size: 1.4rem;
            font-weight: 800;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: var(--transition-fast);
        }

        .topbar-brand:hover {
            color: #f0f0f0;
            transform: scale(1.02);
        }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-user {
            color: white;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 10px 16px;
            border-radius: 12px;
            transition: var(--transition-normal);
            backdrop-filter: blur(8px);
        }

        .topbar-user:hover {
            background: rgba(255,255,255,0.15);
            transform: translateY(-1px);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fff 0%, #f0f0f0 100%);
            color: #667eea;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: var(--shadow-sm);
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: var(--topbar-height);
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--topbar-height));
            background: var(--sidebar-bg);
            box-shadow: var(--shadow-lg);
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1020;
            transition: var(--transition-normal);
            color: var(--text-primary);
            border-right: 1px solid var(--border-color);
            backdrop-filter: blur(12px);
        }

        .sidebar-menu {
            padding: 24px 0;
        }

        .menu-section {
            padding: 0 24px;
            margin-bottom: 32px;
        }

        .menu-section-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-secondary);
            font-weight: 700;
            margin-bottom: 16px;
            letter-spacing: 0.8px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 14px 24px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 6px;
            transition: var(--transition-normal);
            position: relative;
            font-weight: 500;
            overflow: hidden;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            transition: var(--transition-normal);
            z-index: -1;
        }

        .menu-item:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
            color: #667eea;
            transform: translateX(6px);
        }

        .menu-item:hover::before {
            width: 100%;
        }

        .menu-item.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: var(--shadow-md);
            transform: translateX(4px);
        }

        .menu-item.active::after {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: rgba(255,255,255,0.8);
            border-radius: 0 4px 4px 0;
        }

        .menu-item i {
            width: 20px;
            margin-right: 16px;
            font-size: 1.1rem;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 32px;
            min-height: calc(100vh - var(--topbar-height));
            background: var(--bg-primary);
            transition: var(--transition-normal);
        }

        /* Mobile & Tablet Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }

            .sidebar.show {
                transform: translateX(0);
                box-shadow: var(--shadow-xl);
            }

            .main-content {
                margin-left: 0;
                padding: 24px 20px;
            }

            .mobile-toggle {
                display: flex !important;
            }

            .menu-item {
                padding: 16px 24px;
                font-size: 1.05rem;
            }

            .topbar {
                padding: 0 16px;
            }

            .topbar-brand {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 768px) {
            :root {
                --sidebar-width: 280px;
                --topbar-height: 60px;
            }

            .main-content {
                padding: 16px;
            }

            .topbar {
                padding: 0 12px;
            }

            .topbar-brand {
                font-size: 1.2rem;
            }

            .topbar-user {
                padding: 8px 12px;
                gap: 8px;
            }

            .user-avatar {
                width: 36px;
                height: 36px;
                font-size: 1rem;
            }

            .menu-section {
                padding: 0 20px;
                margin-bottom: 24px;
            }

            .menu-item {
                padding: 14px 20px;
                margin-bottom: 4px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 12px;
            }

            .topbar-brand {
                font-size: 1.1rem;
            }

            .sidebar {
                width: 100vw;
            }

            .menu-item {
                padding: 16px 20px;
                font-size: 1.1rem;
            }
        }

        /* Mobile Toggle Button */
        .mobile-toggle {
            display: none;
            background: rgba(255,255,255,0.15);
            border: none;
            color: white;
            padding: 10px 12px;
            border-radius: 10px;
            cursor: pointer;
            transition: var(--transition-normal);
            backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
        }

        .mobile-toggle:hover {
            background: rgba(255,255,255,0.25);
            transform: scale(1.05);
        }

        .mobile-toggle i {
            font-size: 1.2rem;
        }

        /* Dropdown Menu */
        .dropdown-menu {
            border: none;
            box-shadow: var(--shadow-xl);
            border-radius: 16px;
            margin-top: 12px;
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            padding: 8px;
            backdrop-filter: blur(12px);
        }

        .dropdown-item {
            padding: 12px 16px;
            transition: var(--transition-normal);
            color: var(--text-primary);
            background-color: transparent;
            border-radius: 10px;
            margin-bottom: 2px;
            font-weight: 500;
        }

        .dropdown-item:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
            color: #667eea;
            transform: translateX(4px);
        }

        .dropdown-item.dropdown-divider {
            border-color: var(--border-color);
            margin: 8px 0;
        }

        /* Custom Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.3) 0%, rgba(118, 75, 162, 0.3) 100%);
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.5) 0%, rgba(118, 75, 162, 0.5) 100%);
        }

        /* Guest Layout */
        .guest-layout {
            margin-left: 0 !important;
            padding: 0 !important;
        }

        /* Enhanced Breadcrumb */
        .breadcrumb {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            padding: 16px 24px;
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 24px;
            border: 1px solid var(--border-color);
        }

        .breadcrumb-item {
            font-size: 0.95rem;
            font-weight: 600;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "→";
            color: #667eea;
            font-weight: bold;
            margin: 0 8px;
        }

        .breadcrumb-item.active {
            color: #667eea;
        }

        /* Mobile Overlay */
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1015;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition-normal);
            backdrop-filter: blur(4px);
        }

        .mobile-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        @media (min-width: 1025px) {
            .mobile-overlay {
                display: none;
            }
        }

        /* Additional Mobile Enhancements */
        .table-responsive {
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition-normal);
        }

        .card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .btn {
            border-radius: 10px;
            font-weight: 600;
            transition: var(--transition-normal);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid var(--border-color);
            transition: var(--transition-normal);
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        /* Loading States */
        .btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn.loading::after {
            content: '';
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 8px;
            display: inline-block;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .breadcrumb-item a {
            color: #667eea;
            text-decoration: none;
            transition: all 0.3s;
            padding: 4px 8px;
            border-radius: 5px;
        }

        .breadcrumb-item a:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #764ba2;
            transform: translateX(3px);
        }

        .breadcrumb-item.active {
            color: #555;
            font-weight: 600;
        }

        /* Custom SweetAlert2 Styling */
        .swal2-popup {
            border-radius: 15px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .swal2-title {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .swal2-confirm {
            border-radius: 8px;
            padding: 10px 30px;
        }

        .swal2-cancel {
            border-radius: 8px;
            padding: 10px 30px;
        }
    </style>
</head>

<body>
    <?php
        // Detect if current page is a user/public page (not admin panel)
        $isUserPage = request()->routeIs('user.*') || 
                      request()->routeIs('welcome') || 
                      request()->routeIs('favorites.*') ||
                      request()->routeIs('laundry.index') && !auth()->check();
    ?>

    <!-- Topbar -->
    <div class="topbar">
        <?php if(!$isUserPage): ?>
        <button class="mobile-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <?php endif; ?>

        <a href="<?php echo e(route('welcome')); ?>" class="topbar-brand">
            <i class="bi bi-house-heart"></i>
            <span>SPK Kontrakan & Laundry</span>
        </a>

        <div class="topbar-right">
            <?php if($isUserPage): ?>
                <!-- Tombol kembali ke Home untuk halaman user -->
                <a href="<?php echo e(route('welcome')); ?>" class="btn btn-light btn-sm me-2" style="border-radius: 8px;">
                    <i class="bi bi-house me-1"></i><span class="d-none d-md-inline">Beranda</span>
                </a>
                <?php if(auth()->guard()->check()): ?>
                    <?php if(auth()->user()->role === 'user'): ?>
                        
                        <!-- Tombol Riwayat Booking -->
                        <a href="<?php echo e(route('user.booking.history')); ?>" class="btn btn-light btn-sm me-2" style="border-radius: 8px;" title="Riwayat Booking Saya">
                            <i class="bi bi-calendar-check text-success me-1"></i>
                            <span class="d-none d-md-inline">Booking</span>
                            <?php
                                $bookingCount = \App\Models\Booking::where('user_id', auth()->id())->whereIn('status', ['pending', 'confirmed'])->count();
                            ?>
                            <?php if($bookingCount > 0): ?>
                            <span class="badge bg-success ms-1"><?php echo e($bookingCount); ?></span>
                            <?php endif; ?>
                        </a>
                        <!-- Tombol Favorit -->
                        <a href="<?php echo e(route('favorites.index')); ?>" class="btn btn-light btn-sm me-2" style="border-radius: 8px;" title="Kontrakan Favorit Saya">
                            <i class="bi bi-heart-fill text-danger me-1"></i>
                            <span class="d-none d-md-inline">Favorit</span>
                            <?php
                                $favCount = \App\Models\Favorite::where('user_id', auth()->id())->count();
                            ?>
                            <?php if($favCount > 0): ?>
                            <span class="badge bg-danger ms-1"><?php echo e($favCount); ?></span>
                            <?php endif; ?>
                        </a>
                        <!-- Dropdown User -->
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" style="border-radius: 8px;">
                                <i class="bi bi-person-circle me-1"></i><?php echo e(auth()->user()->name); ?>

                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo e(route('user.booking.history')); ?>"><i class="bi bi-clock-history me-2"></i>Riwayat Booking</a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('favorites.index')); ?>"><i class="bi bi-heart me-2"></i>Favorit Saya</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="<?php echo e(route('user.logout')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        
                        <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-primary btn-sm" style="border-radius: 8px;">
                            <i class="bi bi-speedometer2 me-1"></i><span class="d-none d-md-inline">Dashboard Admin</span>
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    
                    <a href="<?php echo e(route('user.login')); ?>" class="btn btn-light btn-sm me-2" style="border-radius: 8px;" title="Login untuk booking & simpan favorit">
                        <i class="bi bi-person me-1"></i><span class="d-none d-md-inline">Login</span>
                    </a>
                <?php endif; ?>
            <?php else: ?>
                
            <?php endif; ?>

            <?php if(!$isUserPage): ?>
            <?php if(auth()->guard()->check()): ?>
                <!-- Dark Mode Toggle Button -->
                <button id="darkModeToggle" class="btn btn-light btn-sm" title="Toggle Dark Mode" style="border-radius: 8px; padding: 8px 12px;">
                    <i class="bi bi-moon-stars"></i>
                </button>

                <div class="dropdown">
                    <div class="topbar-user" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            <?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?>

                        </div>
                        <div class="d-none d-md-block">
                            <div style="font-size: 0.9rem; font-weight: 600;"><?php echo e(Auth::user()->name); ?></div>
                            <div style="font-size: 0.75rem; opacity: 0.8;">Administrator</div>
                        </div>
                        <i class="bi bi-chevron-down"></i>
                    </div>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('dashboard')); ?>">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        </li>
                        <?php if(auth()->user()->isSuperAdmin()): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('admin.users.index')); ?>">
                                <i class="bi bi-people-fill me-2"></i>Kelola User
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('admin.activity-logs.index')); ?>">
                                <i class="bi bi-clock-history me-2"></i>Activity Log
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('admin.backup.index')); ?>">
                                <i class="bi bi-cloud-arrow-down me-2"></i>Backup & Restore
                            </a>
                        </li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="<?php echo e(route('admin.logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if(auth()->guard()->check()): ?>
    <?php if(!$isUserPage): ?>
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-menu">
            <!-- Main Menu -->
            <div class="menu-section">
                <div class="menu-section-title">Menu Utama</div>
                <a href="<?php echo e(route('dashboard')); ?>" class="menu-item <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <!-- Data Management -->
            <div class="menu-section">
                <div class="menu-section-title">Manajemen Data</div>
                <a href="<?php echo e(route('kontrakan.index')); ?>" class="menu-item <?php echo e(request()->routeIs('kontrakan.*') ? 'active' : ''); ?>">
                    <i class="bi bi-building"></i>
                    <span>Data Kontrakan</span>
                </a>
                <a href="<?php echo e(route('laundry.index')); ?>" class="menu-item <?php echo e(request()->routeIs('laundry.*') ? 'active' : ''); ?>">
                    <i class="bi bi-basket3"></i>
                    <span>Data Laundry</span>
                </a>
                <a href="<?php echo e(route('admin.bookings.index')); ?>" class="menu-item <?php echo e(request()->routeIs('admin.bookings.*') ? 'active' : ''); ?>">
                    <i class="bi bi-calendar-check"></i>
                    <span>Booking Kontrakan</span>
                </a>
                <a href="<?php echo e(route('kriteria.index')); ?>" class="menu-item <?php echo e(request()->routeIs('kriteria.*') ? 'active' : ''); ?>">
                    <i class="bi bi-list-check"></i>
                    <span>Data Kriteria</span>
                </a>
            </div>

            <!-- SPK System -->
            <div class="menu-section">
                <div class="menu-section-title">Sistem Analisis</div>
                <a href="<?php echo e(route('saw.index')); ?>" class="menu-item <?php echo e(request()->routeIs('saw.*') ? 'active' : ''); ?>">
                    <i class="bi bi-calculator"></i>
                    <span>Sistem Rekomendasi</span>
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="main-content <?php echo e(Auth::guest() || $isUserPage ? 'guest-layout' : ''); ?>">
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <!-- 🆕 Toast Notifications Component -->
    <?php echo $__env->make('components.toast-notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    
    <script>
        // Enhanced Sidebar toggle for mobile with improved UX
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobileOverlay');

        // Toggle sidebar
        function toggleSidebar() {
            if (!sidebar) return;
            const isShowing = sidebar.classList.contains('show');
            
            if (isShowing) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }

        function openSidebar() {
            if (!sidebar) return;
            sidebar.classList.add('show');
            if (mobileOverlay) {
                mobileOverlay.classList.add('show');
            }
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeSidebar() {
            if (!sidebar) return;
            sidebar.classList.remove('show');
            if (mobileOverlay) {
                mobileOverlay.classList.remove('show');
            }
            document.body.style.overflow = ''; // Restore scrolling
        }

        // Event listeners
        sidebarToggle?.addEventListener('click', toggleSidebar);
        mobileOverlay?.addEventListener('click', closeSidebar);

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (sidebar && sidebarToggle && window.innerWidth <= 1024) {
                if (!sidebar.contains(event.target) && 
                    !sidebarToggle.contains(event.target) && 
                    sidebar.classList.contains('show')) {
                    closeSidebar();
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 1024) {
                closeSidebar();
            }
        });

        // Enhanced form handling
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.classList.contains('loading')) {
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                    
                    // Reset after 5 seconds (fallback)
                    setTimeout(() => {
                        submitBtn.classList.remove('loading');
                        submitBtn.disabled = false;
                    }, 5000);
                }
            });
        });

        // Show success/error messages with SweetAlert2
        <?php if(session('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?php echo e(session('success')); ?>',
                timer: 4000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        <?php endif; ?>

        <?php if(session('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '<?php echo e(session('error')); ?>',
                timer: 4000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        <?php endif; ?>

        // Enhanced confirmation for delete actions
        function confirmDelete(form, itemName) {
            event.preventDefault();
            
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus ${itemName || 'data ini'}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Add loading state
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Sedang memproses permintaan Anda',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        timer: 2000
                    });
                    form.submit();
                }
            });
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Add loading animation to navigation links
        const navLinks = document.querySelectorAll('.menu-item');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (!this.classList.contains('active')) {
                    this.style.opacity = '0.7';
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.classList.add('fa-spin');
                    }
                }
            });
        });

        // Enhanced table responsiveness
        const tables = document.querySelectorAll('table');
        tables.forEach(table => {
            if (!table.closest('.table-responsive')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'table-responsive';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
        });

        // Tooltip initialization
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        // Toast notification helper
        function showToast(type, message) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: type,
                title: message
            });
        }

        // ========== DARK MODE FUNCTIONALITY ==========
        function initializeDarkMode() {
            const darkModeToggle = document.getElementById('darkModeToggle');
            const htmlElement = document.documentElement;
            
            // Check localStorage for saved preference
            const isDarkMode = localStorage.getItem('darkMode') === 'enabled';
            
            // Apply saved preference on page load
            if (isDarkMode) {
                htmlElement.classList.add('dark-mode');
                updateDarkModeIcon(true);
            }
            
            // Toggle dark mode
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', function() {
                    htmlElement.classList.toggle('dark-mode');
                    const isNowDark = htmlElement.classList.contains('dark-mode');
                    
                    // Save preference
                    if (isNowDark) {
                        localStorage.setItem('darkMode', 'enabled');
                    } else {
                        localStorage.setItem('darkMode', 'disabled');
                    }
                    
                    updateDarkModeIcon(isNowDark);
                });
            }
        }
        
        function updateDarkModeIcon(isDark) {
            const icon = document.querySelector('#darkModeToggle i');
            if (icon) {
                if (isDark) {
                    icon.classList.remove('bi-moon-stars');
                    icon.classList.add('bi-sun');
                } else {
                    icon.classList.remove('bi-sun');
                    icon.classList.add('bi-moon-stars');
                }
            }
        }
        
        // Initialize dark mode on page load
        document.addEventListener('DOMContentLoaded', initializeDarkMode);
    </script>

    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html><?php /**PATH C:\laragon\www\TA\spk_kontrakan\resources\views/layouts/app.blade.php ENDPATH**/ ?>