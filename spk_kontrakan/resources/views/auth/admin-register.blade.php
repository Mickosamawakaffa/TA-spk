<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Sebagai Pemilik - SPK Kontrakan & Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .auth-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .auth-header h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }

        .auth-header p {
            margin: 8px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .auth-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            width: 100%;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .auth-footer {
            text-align: center;
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #e0e0e0;
        }

        .auth-footer p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        .auth-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .auth-footer a:hover {
            color: #764ba2;
        }
    </style>
</head>

<body>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2><i class="fas fa-store"></i> Daftar Sebagai Pemilik</h2>
            <p>Bergabung untuk mengelola bisnis Anda</p>
        </div>

        <!-- Info Box -->
        <div class="alert alert-info mx-3 mt-3 mb-0">
            <h6 class="fw-bold mb-2">🏢 Keuntungan Bergabung:</h6>
            <ul class="small mb-0 ps-3">
                <li>✅ Kelola data kontrakan/laundry Anda</li>
                <li>✅ Terima pelanggan dari sistem SPK</li>
                <li>✅ Update informasi bisnis kapan saja</li>
            </ul>
        </div>

        <div class="auth-body">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Terjadi Kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.register.post') }}">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label"><i class="fas fa-user"></i> Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan nama Anda" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email Anda" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Buat password yang kuat" required>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label"><i class="fas fa-check-circle"></i> Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi password Anda" required>
                </div>

                <button type="submit" class="btn-register">
                    <i class="fas fa-check me-2"></i>Daftar
                </button>
            </form>
        </div>

        <div class="auth-footer">
            <p>Sudah punya akun? <a href="{{ route('admin.login') }}">Login di sini</a></p>
            <p><a href="{{ route('admin.portal') }}">← Kembali ke Portal Pemilik</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
