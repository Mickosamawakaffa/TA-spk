<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Berhasil Diverifikasi</title>
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            max-width: 480px;
            text-align: center;
        }

        /* Glassmorphism Card */
        .card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 40px 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Checkmark Animation */
        .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #22c55e 0%, #15803d 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 10px 25px rgba(34, 197, 94, 0.3);
            animation: popIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s both;
        }

        .icon-wrapper svg {
            width: 40px;
            height: 40px;
            color: white;
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: drawCheck 0.8s ease-in-out 0.8s both;
        }

        h1 {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        p {
            font-size: 15px;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 24px;
            font-weight: 400;
        }

        .user-badge {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 30px;
            display: inline-block;
            max-width: 100%;
        }

        .user-name {
            font-size: 14px;
            font-weight: 700;
            color: #38bdf8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-email {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .footer-note {
            font-size: 12px;
            color: #64748b;
            margin-top: 10px;
        }

        .glow-button {
            display: block;
            width: 100%;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            text-decoration: none;
            padding: 14px 24px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .glow-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
            background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%);
        }

        .glow-button:active {
            transform: translateY(0);
        }

        /* Animations */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes popIn {
            from {
                opacity: 0;
                transform: scale(0.6);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes drawCheck {
            to {
                stroke-dashoffset: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="icon-wrapper">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h1>Email Terverifikasi!</h1>
            <p>Terima kasih. Email Anda telah berhasil diverifikasi secara aman. Akun Anda kini telah aktif sepenuhnya.</p>
            
            <div class="user-badge">
                <div class="user-name">{{ $name }}</div>
                <div class="user-email">{{ $email }}</div>
            </div>

            <button class="glow-button" onclick="closeWindow()">Kembali ke Aplikasi</button>
            <div class="footer-note">Anda dapat menutup halaman browser ini sekarang.</div>
        </div>
    </div>

    <script>
        function closeWindow() {
            // Coba menutup tab secara otomatis (hanya bekerja di browser mobile tertentu)
            window.close();
            // Alternatif jika close() di-block browser
            alert("Email Anda sudah terverifikasi. Silakan buka kembali aplikasi mobile Kontrak Kampus Anda!");
        }
    </script>
</body>
</html>
