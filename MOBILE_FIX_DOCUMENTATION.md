# Perbaikan Sistem Mobile SPK Kontrakan

## Masalah yang Ditemukan
1. Data di mobile tidak sesuai dengan data di web
2. Endpoint API tidak benar (menggunakan GET dengan query parameters, harusnya POST dengan body)
3. Model data tidak sesuai dengan response API dari backend
4. Tampilan tidak menunjukkan sistem rekomendasi dengan ranking dan scoring
5. Tidak ada tampilan untuk top recommendations di home screen

## Perbaikan yang Dilakukan

### 1. Perbaikan API Endpoints
**File: `lib/screens/recommendation_screen.dart`**
- ✅ Mengubah endpoint dari `/api/saw/kontrakan` menjadi `/api/saw/calculate/kontrakan`
- ✅ Mengubah method dari GET (dengan query params) menjadi POST (dengan body)
- ✅ Mengirim parameters dalam request body menggunakan JSON

**File: `lib/services/kontrakan_service.dart`**
- ✅ Menambahkan method `getRecommendations()` untuk mendapatkan rekomendasi SAW
- ✅ Menggunakan POST method dengan JSON body sesuai dengan API backend

**File: `lib/services/laundry_service.dart`**
- ✅ Menambahkan method `getRecommendations()` untuk laundry
- ✅ Implementasi yang sama dengan kontrakan service

### 2. Perbaikan Model Data
**File: `lib/models/laundry.dart`**
- ✅ Mengubah nama field dari `jarak` menjadi `jarakKampus` (sesuai API)
- ✅ Mengubah `hargaKiloan` dan `hargaSatuan` menjadi `hargaPerKg` (sesuai API)
- ✅ Mengubah `estimasiSelesai` menjadi `waktuProses` (sesuai API)
- ✅ Menambahkan field `deskripsi`, `fotoUtama`, `galeri` yang hilang
- ✅ Menambahkan `avgRating` dan `totalReviews` untuk rating display
- ✅ Menambahkan class `GaleriLaundry` untuk galeri photos
- ✅ Menambahkan getter `primaryPhoto` untuk mendapatkan foto utama
- ✅ Memperbaiki getter `formattedHarga` untuk format Rupiah

**File: `lib/models/kontrakan.dart`**
- ✅ Sudah sesuai dengan API response

### 3. Pembuatan Widget Komponen
**File: `lib/widgets/kontrakan_card.dart` (BARU)**
- ✅ Widget card untuk menampilkan kontrakan
- ✅ Mendukung tampilan ranking dan skor (untuk sistem rekomendasi)
- ✅ Menampilkan badge ranking dengan warna berbeda (emas, perak, perunggu)
- ✅ Menampilkan persentase skor SAW
- ✅ Tampilan foto, harga, jumlah kamar, jarak kampus, dan rating
- ✅ Clickable untuk navigasi ke detail screen

**File: `lib/widgets/laundry_card.dart` (BARU)**
- ✅ Widget card untuk menampilkan laundry
- ✅ Sama seperti kontrakan card dengan design yang konsisten
- ✅ Menampilkan harga per kg dan waktu proses

### 4. Improved Home Screen
**File: `lib/screens/improved_home_screen.dart` (BARU)**
- ✅ Home screen baru yang fokus pada sistem rekomendasi
- ✅ Menampilkan "Top 5 Kontrakan Terbaik" berdasarkan SAW
- ✅ Setiap kartu menampilkan ranking dan skor
- ✅ Quick action button untuk laundry
- ✅ Pull-to-refresh untuk reload rekomendasi
- ✅ Bottom navigation yang konsisten
- ✅ Design modern dengan gradient header
- ✅ Loading state yang informatif

### 5. Update Entry Points
**File: `lib/main.dart`**
- ✅ Menggunakan `ImprovedHomeScreen` sebagai home default
- ✅ Splash screen tetap berfungsi untuk auth check

**File: `lib/login.dart`**
- ✅ Navigate ke `ImprovedHomeScreen` setelah login sukses
- ✅ Import statement sudah diupdate

## Struktur Sistem Rekomendasi

### Flow Data:
```
1. Mobile App → POST /api/saw/calculate/kontrakan (dengan filter opsional)
2. Backend → Proses SAW → Return ranked results dengan skor
3. Mobile App → Parse results → Tampilkan dengan ranking & skor
```

### Response Format dari API:
```json
{
  "success": true,
  "data": {
    "kriteria": [...],
    "hasil": [
      {
        "id": 1,
        "nama": "Kontrakan ABC",
        "ranking": 1,
        "skor": 0.95,
        "nilai": {...},
        "normalisasi": {...},
        "data": {
          "id": 1,
          "nama": "Kontrakan ABC",
          "harga": 500000,
          ...
        }
      }
    ]
  }
}
```

### Tampilan di Mobile:
- **Home Screen**: Top 5 rekomendasi dengan badge ranking (🏆 #1, #2, #3, dll)
- **Recommendation Screen**: Full list dengan filter dan ranking
- **Card**: Menampilkan ranking badge + skor persentase di bagian atas card

## Cara Menggunakan

### 1. Pastikan Backend Running
```bash
cd spk_kontrakan
php artisan serve
```

### 2. Cek Database
Pastikan data kontrakan dan laundry sudah ada di database dengan field yang benar:
- `kontrakan`: harga, jumlah_kamar, jarak_kampus, fasilitas
- `laundry`: harga_per_kg, jarak_kampus, waktu_proses

### 3. Konfigurasi URL
**File: `lib/config/app_config.dart`**
```dart
// Untuk Android Emulator
static const String baseUrl = 'http://10.0.2.2:8000/api';

// Untuk Real Device (ganti dengan IP komputer Anda)
static const String baseUrl = 'http://192.168.1.100:8000/api';
```

### 4. Run Flutter App
```bash
cd spk_mobile
flutter pub get
flutter run
```

## Testing Checklist

- [ ] Login berhasil → Redirect ke Improved Home Screen
- [ ] Home screen menampilkan Top 5 Kontrakan dengan ranking
- [ ] Setiap card menampilkan badge ranking (🏆 #1, #2, #3)
- [ ] Setiap card menampilkan skor dalam persentase
- [ ] Pull-to-refresh berfungsi reload data
- [ ] Click card → Navigate ke detail screen
- [ ] Button "Lihat Semua" → Navigate ke Recommendation Screen
- [ ] Recommendation Screen menampilkan semua hasil dengan filter
- [ ] Filter berfungsi (harga, jarak, kamar, fasilitas)
- [ ] Laundry button navigate ke Laundry List
- [ ] Bottom navigation berfungsi semua tab

## Keunggulan Sistem Baru

1. **Data Konsisten**: Mobile dan web menggunakan API dan logika yang sama
2. **Visual Ranking**: User langsung melihat mana kontrakan/laundry terbaik
3. **Sistem Scoring**: Transparansi dengan menampilkan skor SAW
4. **User Experience**: Design modern, intuitive, dan informatif
5. **Performance**: Caching dengan CachedNetworkImage untuk loading foto lebih cepat

## Troubleshooting

### Data tidak muncul?
1. Cek API response di console: `flutter run --verbose`
2. Pastikan baseUrl benar di `app_config.dart`
3. Test API menggunakan Postman: `POST http://localhost:8000/api/saw/calculate/kontrakan`

### Foto tidak muncul?
1. Pastikan foto ada di folder `public/storage` backend
2. Jalankan: `php artisan storage:link`
3. Cek URL foto di CachedNetworkImage (ada prefix storage/)

### Ranking tidak sesuai?
1. Cek bobot kriteria di database `kriteria` table
2. Pastikan metode SAW berjalan dengan benar di backend
3. Test SAW calculation manual di web

## Next Steps (Opsional)

1. **Favorites**: Implementasi favorite kontrakan/laundry
2. **Reviews**: Tampilkan dan submit review dari mobile
3. **Booking**: Integrasi booking flow yang lengkap
4. **Maps**: Tampilkan lokasi di Google Maps
5. **Notifications**: Push notification untuk booking updates
6. **Offline Mode**: Cache data untuk offline viewing

---

**Dibuat oleh**: GitHub Copilot
**Tanggal**: 31 Januari 2026
**Versi**: 1.0
