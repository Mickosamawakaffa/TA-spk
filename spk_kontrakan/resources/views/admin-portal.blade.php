@extends('layouts.app')

@section('title', 'Portal Pemilik - Kelola Kontrakan & Laundry Anda')

@section('content')
<style>
    /* ===== ADMIN PORTAL STYLES - Professional & Corporate ===== */
    
    .admin-portal-hero {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #7e22ce 100%);
        background-size: 200% 200%;
        animation: gradient-shift 15s ease infinite;
        color: white;
        padding: 80px 40px 120px;
        margin: 0;
        margin-left: calc(-50vw + 50%);
        margin-right: calc(-50vw + 50%);
        width: 100vw;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(30, 60, 114, 0.4);
    }
    
    @keyframes gradient-shift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .admin-portal-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .admin-portal-hero::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 150px;
        background: linear-gradient(to top, rgba(255,255,255,0.1) 0%, transparent 100%);
    }

    .portal-content {
        position: relative;
        z-index: 1;
        max-width: 1200px;
        margin: 0 auto;
    }

    .portal-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        padding: 8px 20px;
        border-radius: 30px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 20px;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .portal-title {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 25px;
        line-height: 1.2;
        text-shadow: 2px 2px 20px rgba(0,0,0,0.3);
    }

    .portal-subtitle {
        font-size: 1.4rem;
        margin-bottom: 40px;
        opacity: 0.95;
        font-weight: 300;
        max-width: 700px;
    }

    .portal-buttons {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .btn-portal-primary {
        background: white;
        color: #1e3c72;
        padding: 16px 40px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-portal-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        color: #1e3c72;
    }

    .btn-portal-secondary {
        background: transparent;
        color: white;
        padding: 16px 40px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        border: 2px solid white;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-portal-secondary:hover {
        background: white;
        color: #1e3c72;
        transform: translateY(-3px);
    }

    /* Features Section */
    .features-admin {
        padding: 80px 0;
        background: #f8fafc;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 50px;
    }

    .feature-card-admin {
        background: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
    }

    .feature-card-admin:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .feature-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        font-size: 2rem;
    }

    .feature-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: #1e293b;
    }

    .feature-description {
        color: #64748b;
        line-height: 1.7;
        font-size: 1rem;
    }

    /* Stats Section */
    .stats-section {
        padding: 80px 0;
        background: white;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 40px;
        margin-top: 50px;
    }

    .stat-card {
        text-align: center;
        padding: 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        color: white;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .stat-number {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .stat-label {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    /* CTA Section */
    .cta-section {
        padding: 100px 40px;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        margin-left: calc(-50vw + 50%);
        margin-right: calc(-50vw + 50%);
        width: 100vw;
        color: white;
        text-align: center;
    }

    .cta-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 20px;
    }

    .cta-subtitle {
        font-size: 1.3rem;
        margin-bottom: 40px;
        opacity: 0.9;
    }

    @media (max-width: 768px) {
        .portal-title {
            font-size: 2.5rem;
        }
        
        .portal-subtitle {
            font-size: 1.1rem;
        }
        
        .portal-buttons {
            flex-direction: column;
        }
        
        .btn-portal-primary,
        .btn-portal-secondary {
            width: 100%;
            text-align: center;
        }
    }
</style>

<!-- Hero Section -->
<div class="admin-portal-hero">
    <div class="portal-content">
        <span class="portal-badge">
            🏢 Portal Khusus Pemilik
        </span>
        <h1 class="portal-title">
            Kelola Properti Anda<br>dengan Mudah & Efisien
        </h1>
        <p class="portal-subtitle">
            Platform manajemen kontrakan dan laundry yang profesional. Pantau bisnis Anda, kelola pemesanan, dan tingkatkan pendapatan dengan sistem yang terintegrasi.
        </p>
        <div class="portal-buttons">
            <a href="{{ route('admin.login') }}" class="btn-portal-primary">
                🔐 Login Pemilik
            </a>
            <a href="{{ route('admin.register') }}" class="btn-portal-secondary">
                📝 Daftar Sebagai Pemilik
            </a>
        </div>
    </div>
</div>

<div class="container">
    <!-- Features Section -->
    <section class="features-admin">
        <div class="text-center">
            <h2 style="font-size: 2.5rem; font-weight: 800; color: #1e293b; margin-bottom: 15px;">
                Fitur Lengkap untuk Pemilik
            </h2>
            <p style="font-size: 1.2rem; color: #64748b;">
                Semua yang Anda butuhkan untuk mengelola bisnis properti Anda
            </p>
        </div>

        <div class="features-grid">
            <div class="feature-card-admin">
                <div class="feature-icon">🏠</div>
                <h3 class="feature-title">Manajemen Kontrakan</h3>
                <p class="feature-description">
                    Kelola data kontrakan, upload foto, atur harga, dan update informasi dengan mudah. Sistem lengkap dengan galeri foto.
                </p>
            </div>

            <div class="feature-card-admin">
                <div class="feature-icon">👕</div>
                <h3 class="feature-title">Kelola Laundry</h3>
                <p class="feature-description">
                    Tambahkan layanan laundry, atur harga per layanan, dan kelola pesanan pelanggan secara real-time.
                </p>
            </div>

            <div class="feature-card-admin">
                <div class="feature-icon">📅</div>
                <h3 class="feature-title">Sistem Booking</h3>
                <p class="feature-description">
                    Pantau pemesanan masuk, konfirmasi booking, dan kelola jadwal ketersediaan properti Anda.
                </p>
            </div>

            <div class="feature-card-admin">
                <div class="feature-icon">⭐</div>
                <h3 class="feature-title">Review & Rating</h3>
                <p class="feature-description">
                    Lihat ulasan pelanggan, tingkatkan kualitas layanan, dan bangun reputasi bisnis yang lebih baik.
                </p>
            </div>

            <div class="feature-card-admin">
                <div class="feature-icon">📊</div>
                <h3 class="feature-title">Dashboard Analytics</h3>
                <p class="feature-description">
                    Pantau performa bisnis dengan statistik lengkap, grafik penjualan, dan laporan yang detail.
                </p>
            </div>

            <div class="feature-card-admin">
                <div class="feature-icon">📱</div>
                <h3 class="feature-title">Responsive Design</h3>
                <p class="feature-description">
                    Kelola bisnis dari mana saja, kapan saja. Akses dashboard dari smartphone, tablet, atau komputer.
                </p>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="text-center">
            <h2 style="font-size: 2.5rem; font-weight: 800; color: #1e293b; margin-bottom: 15px;">
                Platform Terpercaya
            </h2>
            <p style="font-size: 1.2rem; color: #64748b;">
                Bergabunglah dengan pemilik lainnya yang sudah merasakan kemudahan
            </p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="stat-owners">{{ $totalAdmins }}+</div>
                <div class="stat-label">Pemilik Terdaftar</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="stat-properties">{{ $totalProperti }}+</div>
                <div class="stat-label">Properti Dikelola</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="stat-bookings">{{ $totalBookings }}+</div>
                <div class="stat-label">Booking Sukses</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $avgRating }}⭐</div>
                <div class="stat-label">Rating Rata-rata</div>
            </div>
        </div>
    </section>
</div>

<!-- CTA Section -->
<section class="cta-section">
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 class="cta-title">Siap Memulai?</h2>
        <p class="cta-subtitle">
            Daftar sekarang dan dapatkan akses penuh ke dashboard manajemen properti
        </p>
        <a href="{{ route('admin.register') }}" class="btn-portal-primary" style="display: inline-block;">
            Daftar Gratis Sekarang
        </a>
    </div>
</section>

<script>
    // Animate numbers on scroll - UPDATED WITH REAL DATA
    const animateValue = (element, start, end, duration, suffix = '+') => {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const value = Math.floor(progress * (end - start) + start);
            element.textContent = value + suffix;
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    };

    // Observer for stats animation
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Gunakan data real dari backend
                animateValue(document.getElementById('stat-owners'), 0, {{ $totalAdmins }}, 2000);
                animateValue(document.getElementById('stat-properties'), 0, {{ $totalProperti }}, 2000);
                animateValue(document.getElementById('stat-bookings'), 0, {{ $totalBookings }}, 2000);
                statsObserver.unobserve(entry.target);
            }
        });
    });

    const statsSection = document.querySelector('.stats-section');
    if (statsSection) {
        statsObserver.observe(statsSection);
    }
</script>

@endsection
