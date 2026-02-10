# Dokumentasi Pemisahan Web & Mobile SPK Kontrakan

## 📋 Overview

Project SPK Kontrakan telah direstrukturisasi menjadi 2 bagian terpisah:

### 1. **spk_kontrakan** (Web Laravel)
   - **Fungsi**: Dashboard Admin/Pemilik Kontrakan
   - **Pengguna**: Admin, Pemilik
   - **Akses**: `http://localhost/spk_kontrakan`
   - **Fitur**: 
     - Manajemen Kontrakan & Laundry
     - Dashboard statistik
     - Perhitungan SAW
     - Manajemen Booking
     - Activity Logs
     - User Management
     - Export Data (PDF/Excel)
     - Backup & Restore

### 2. **spk_mobile** (Flutter)
   - **Fungsi**: Aplikasi Mobile untuk User/Pencari Kontrakan
   - **Pengguna**: User Umum (Pencari Kontrakan)
   - **Fitur**:
     - Register & Login User
     - Pencarian Kontrakan & Laundry dengan SAW
     - Detail Kontrakan dengan Galeri
     - Booking Kontrakan
     - Review & Rating
     - Favorit Kontrakan/Laundry
     - History Booking

---

## 🚀 Cara Menjalankan

### Web (spk_kontrakan)

1. **Start Laravel Server**
   ```bash
   cd spk_kontrakan
   php artisan serve
   ```

2. **Akses Admin Portal**
   - URL: `http://localhost:8000/admin-portal`
   - Login Admin: `http://localhost:8000/admin/login`

### Mobile (spk_mobile)

1. **Install Dependencies**
   ```bash
   cd spk_mobile
   flutter pub get
   ```

2. **Jalankan di Emulator/Device**
   ```bash
   flutter run
   ```

3. **Konfigurasi API Base URL**
   - Edit file config untuk mengarah ke Laravel API
   - Base URL: `http://10.0.2.2:8000/api` (untuk Android Emulator)
   - Base URL: `http://localhost:8000/api` (untuk iOS Simulator)

---

## 🔗 API Endpoints untuk Mobile

Base URL: `http://localhost:8000/api`

### Authentication (Public)
```
POST /api/register                 - Register user baru
POST /api/login                    - Login user
```

### Kontrakan (Public)
```
GET  /api/kontrakan               - List semua kontrakan
GET  /api/kontrakan/{id}          - Detail kontrakan
GET  /api/kontrakan/{id}/galeri   - Galeri foto kontrakan
GET  /api/kontrakan/{id}/reviews  - Review kontrakan
```

### Laundry (Public)
```
GET  /api/laundry                 - List semua laundry
GET  /api/laundry/{id}            - Detail laundry
GET  /api/laundry/{id}/galeri     - Galeri foto laundry
GET  /api/laundry/{id}/reviews    - Review laundry
```

### SAW Calculation (Public)
```
GET  /api/saw/kriteria/kontrakan      - Get kriteria kontrakan
POST /api/saw/calculate/kontrakan     - Hitung SAW kontrakan
GET  /api/saw/kriteria/laundry        - Get kriteria laundry
POST /api/saw/calculate/laundry       - Hitung SAW laundry
```

### Protected Endpoints (Butuh Token)

**User Profile**
```
GET  /api/user                    - Get user info
POST /api/logout                  - Logout
PUT  /api/profile/update          - Update profile
```

**Booking**
```
GET  /api/bookings                - History booking
GET  /api/bookings/{id}           - Detail booking
POST /api/bookings                - Create booking
POST /api/bookings/{id}/cancel    - Cancel booking
POST /api/bookings/{id}/extend    - Perpanjang booking
```

**Reviews**
```
POST   /api/reviews/kontrakan/{id}  - Buat review kontrakan
POST   /api/reviews/laundry/{id}    - Buat review laundry
PUT    /api/reviews/{id}            - Update review
DELETE /api/reviews/{id}            - Hapus review
```

**Favorites**
```
GET    /api/favorites                  - List favorites
POST   /api/favorites/kontrakan/{id}  - Toggle favorite kontrakan
POST   /api/favorites/laundry/{id}    - Toggle favorite laundry
DELETE /api/favorites/{id}            - Hapus favorite
```

---

## 🔐 Authentication (Mobile)

Mobile app menggunakan **Laravel Sanctum** untuk authentication.

### Login Flow
1. User login → POST `/api/login`
2. Server return `token`
3. Simpan token di local storage
4. Kirim token di header untuk protected endpoints:
   ```
   Authorization: Bearer {token}
   ```

### Request Example
```dart
// Login
final response = await http.post(
  Uri.parse('http://10.0.2.2:8000/api/login'),
  body: {
    'email': 'user@example.com',
    'password': 'password123',
  },
);

// Get token
final token = json.decode(response.body)['data']['token'];

// Use token for protected endpoint
final bookingsResponse = await http.get(
  Uri.parse('http://10.0.2.2:8000/api/bookings'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
  },
);
```

---

## 📦 Response Format

Semua API endpoint mengembalikan response dalam format JSON:

### Success Response
```json
{
  "success": true,
  "message": "Operasi berhasil",
  "data": {
    // data object or array
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    // validation errors (optional)
  }
}
```

---

## 🗄️ Database

Kedua aplikasi menggunakan database yang sama:
- **Database**: `spk_kontrakan`
- **Tables**: users, kontrakan, laundry, kriteria, bookings, reviews, favorites, dll.

### User Roles
- `admin` - Akses web dashboard
- `user` - Akses mobile app

---

## ⚙️ Setup Laravel Sanctum

Jika belum diinstall, jalankan:

```bash
cd spk_kontrakan

# Install Sanctum (biasanya sudah terinstall di Laravel 11)
composer require laravel/sanctum

# Publish config
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Migrate
php artisan migrate

# Add Sanctum middleware ke api kernel
```

Pastikan di `config/sanctum.php`:
```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
    Sanctum::currentApplicationUrlWithPort()
))),
```

---

## 📱 Mobile App Structure

```
spk_mobile/lib/
├── main.dart              # Entry point
├── login.dart             # Login screen
├── register.dart          # Register screen
├── screens/               # Screens lainnya
│   ├── home_screen.dart
│   ├── search_screen.dart
│   ├── detail_screen.dart
│   ├── booking_screen.dart
│   └── profile_screen.dart
├── services/              # API Services
│   ├── api_service.dart
│   ├── auth_service.dart
│   └── saw_service.dart
└── models/                # Data models
    ├── kontrakan.dart
    ├── booking.dart
    └── user.dart
```

---

## 🔧 Troubleshooting

### CORS Error
Tambahkan di `config/cors.php`:
```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['*'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

### Connection Refused (Android Emulator)
Gunakan `10.0.2.2` instead of `localhost`:
```dart
final baseUrl = 'http://10.0.2.2:8000/api';
```

### 419 CSRF Token Error
Pastikan Sanctum sudah terkonfigurasi dengan benar dan gunakan `Accept: application/json` header.

---

## 📝 Catatan Penting

1. **Web Dashboard** hanya untuk Admin - tidak ada akses user
2. **Mobile App** hanya untuk User - tidak ada akses admin
3. API menggunakan **JSON** untuk semua komunikasi
4. Pastikan **Laravel server running** sebelum menjalankan mobile app
5. Gunakan **Sanctum token** untuk protected endpoints

---

## 🎯 Next Steps

### Untuk Web:
- [ ] Tambah fitur notifikasi booking baru
- [ ] Tambah dashboard analytics lebih detail
- [ ] Implementasi role permissions (owner vs superadmin)

### Untuk Mobile:
- [ ] Implementasi push notifications
- [ ] Tambah filter advanced di search
- [ ] Implementasi Google Maps untuk lokasi
- [ ] Tambah payment gateway integration
- [ ] Dark mode support

---

## 📞 Support

Jika ada pertanyaan atau masalah, hubungi developer team.

**Terakhir diupdate**: 30 Januari 2026
