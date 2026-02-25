# Ringkasan Perbaikan Mobile & Backend - SPK Kontrakan/Laundry

## Tanggal: 31 Januari 2026

---

## 🔧 Perbaikan Web Admin Panel

### 1. Custom Pagination dengan Previous/Next
- **File**: `resources/views/vendor/pagination/custom.blade.php`
- **Perubahan**: Mengganti tombol panah kiri/kanan dengan tombol "Previous" dan "Next" yang lebih jelas
- **Fitur**: 
  - Menampilkan "Showing X to Y of Z results"
  - Tombol Previous/Next dengan ikon
  - Nomor halaman yang dapat diklik

### 2. Select All Checkbox Styling
- **File**: `resources/views/admin/Kontrakan/index.blade.php`
- **File**: `resources/views/admin/Laundry/index.blade.php`
- **Perubahan**: Enhanced checkbox styling dengan warna yang lebih terlihat
  - Hijau saat dipilih
  - Kuning saat indeterminate (sebagian dipilih)
  - Hover effects

### 3. Laundry Index Pagination
- **File**: `resources/views/admin/Laundry/index.blade.php`
- **Perubahan**: Menambahkan pagination yang sebelumnya tidak ada

---

## 🗃️ Perbaikan Database

### 1. Kriteria Seeder
- **File**: `database/seeders/KriteriaSeeder.php` (BARU)
- **Konten**: 8 kriteria untuk SAW calculation:

#### Kriteria Kontrakan:
| Nama Kriteria | Bobot | Tipe | Keterangan |
|--------------|-------|------|------------|
| harga | 0.30 | Cost | Harga sewa per tahun |
| jarak | 0.25 | Cost | Jarak ke kampus dalam meter |
| jumlah_kamar | 0.25 | Benefit | Jumlah kamar tersedia |
| fasilitas_count | 0.20 | Benefit | Jumlah fasilitas |

#### Kriteria Laundry:
| Nama Kriteria | Bobot | Tipe | Keterangan |
|--------------|-------|------|------------|
| harga | 0.25 | Cost | Harga per kilogram |
| jarak | 0.25 | Cost | Jarak ke kampus dalam meter |
| kecepatan_layanan | 0.25 | Benefit | Kecepatan layanan laundry |
| layanan | 0.25 | Benefit | Jumlah variasi layanan tersedia |

---

## 🔄 Perbaikan SAW Controller

### File: `app/Http/Controllers/Api/SAWController.php`

#### Masalah Sebelumnya:
- Menggunakan kolom `jenis` (tidak ada) → seharusnya `tipe_bisnis`
- Menggunakan kolom `kode` (tidak ada) → seharusnya `nama_kriteria`
- Query kontrakan hanya cek status 'tersedia' (database menggunakan 'available')

#### Perbaikan:
```php
// Field mapping yang benar
Kriteria::where('tipe_bisnis', 'kontrakan')  // bukan 'jenis'
$criteria[$k->nama_kriteria] = [...]          // bukan 'kode'

// Status handling yang benar
$q->where('status', 'tersedia')->orWhere('status', 'available');
```

---

## 📱 Perbaikan Mobile App

### 1. API URL Prefix
**Masalah**: `AppConfig.baseUrl` sudah mengandung `/api`, jadi URL menjadi `/api/api/saw/...`

**File yang diperbaiki:**
- `lib/services/kontrakan_service.dart`
- `lib/services/laundry_service.dart`
- `lib/screens/recommendation_screen.dart`
- `lib/screens/improved_home_screen.dart`

**Perbaikan:**
```dart
// SEBELUM (SALAH)
Uri.parse('${AppConfig.baseUrl}/api/saw/calculate/kontrakan')

// SESUDAH (BENAR)
Uri.parse('${AppConfig.baseUrl}/saw/calculate/kontrakan')
```

### 2. Model Field Mapping

#### Kontrakan Model (`lib/models/kontrakan.dart`)
```dart
// Handle kedua field name: jarak_kampus dan jarak
jarak: json['jarak_kampus'] != null 
    ? (double.tryParse(json['jarak_kampus'].toString()) ?? 0) / 1000
    : (json['jarak'] != null 
        ? (double.tryParse(json['jarak'].toString()) ?? 0) / 1000
        : 0.0),
```

#### Laundry Model (`lib/models/laundry.dart`)
- Fixed duplicate `primaryPhoto` getter
- Added improved field parsing untuk jarak dan harga

### 3. API Kontrakan Controller
**File**: `app/Http/Controllers/Api/KontrakanController.php`

**Perbaikan**: Handle kedua status value ('tersedia' dan 'available')
```php
if ($status === 'tersedia' || $status === 'available') {
    $query->where(function($q) {
        $q->where('status', 'tersedia')
          ->orWhere('status', 'available');
    });
}
```

---

## ✅ Status Testing

### API SAW Kontrakan
```json
{
  "success": true,
  "data": {
    "kriteria": [...], // 4 kriteria
    "hasil": [...] // 18 kontrakan dengan ranking
  }
}
```
- **Ranking #1**: Kontrakan Mewah Jl. Raya Surabaya (Skor: 0.79)

### API SAW Laundry
```json
{
  "success": true,
  "data": {
    "kriteria": [...], // 4 kriteria
    "hasil": [...] // 10 laundry dengan ranking
  }
}
```
- **Ranking #1**: Laundry Cepat Dekat Kampus (Skor: 0.85)

---

## 📋 Perintah untuk Testing

### Menjalankan Server Laravel
```bash
cd c:\laragon\www\TA\spk_kontrakan
php artisan serve --port=8000
```

### Test API SAW
```powershell
# Kontrakan
Invoke-WebRequest -Uri 'http://127.0.0.1:8000/api/saw/calculate/kontrakan' -Method POST -ContentType 'application/json' -Body '{}' -UseBasicParsing

# Laundry
Invoke-WebRequest -Uri 'http://127.0.0.1:8000/api/saw/calculate/laundry' -Method POST -ContentType 'application/json' -Body '{}' -UseBasicParsing
```

### Seed Kriteria (jika perlu reset)
```bash
php artisan db:seed --class=KriteriaSeeder
```

---

## 🎯 Fitur SAW yang Berfungsi

1. ✅ Menampilkan kriteria yang digunakan
2. ✅ Menghitung normalisasi berdasarkan tipe (Benefit/Cost)
3. ✅ Menghitung skor SAW dengan bobot
4. ✅ Mengurutkan berdasarkan skor (ranking)
5. ✅ Mengembalikan data lengkap dengan ranking
6. ✅ Support filter: harga_min, harga_max, jarak_max, jumlah_kamar, fasilitas

---

## 📝 Catatan untuk Sidang TA

1. **Algoritma SAW** sudah berfungsi penuh dengan:
   - Normalisasi matrix keputusan
   - Pembobotan kriteria
   - Perhitungan skor preferensi
   - Ranking alternatif

2. **Kriteria**:
   - Cost (semakin kecil semakin baik): harga, jarak, waktu_proses
   - Benefit (semakin besar semakin baik): jumlah_kamar, fasilitas_count, rating

3. **Konsistensi Data**:
   - Field `jarak` dalam meter di database
   - Ditampilkan dalam km di mobile (dibagi 1000)
   - Status kontrakan: 'available' (bukan 'tersedia')
   - Status laundry: 'buka'
