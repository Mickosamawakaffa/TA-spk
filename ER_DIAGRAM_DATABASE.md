# ER Diagram Database - SPK Kontrakan & Laundry

## 1. Diagram Entity Relationship (Mermaid) - Core Tables

```mermaid
erDiagram
    USERS ||--o{ BOOKINGS : "1:M"
    USERS ||--o{ REVIEWS : "1:M"
    USERS ||--o{ FAVORITES : "1:M"
    KONTRAKANS ||--o{ BOOKINGS : "1:M"
    KONTRAKANS ||--o{ REVIEWS : "1:M"
    KONTRAKANS ||--o{ FAVORITES : "1:M"
    KONTRAKANS ||--o{ GALERI : "1:M"
    LAUNDRY ||--o{ LAYANAN_LAUNDRY : "1:M"
    LAUNDRY ||--o{ REVIEWS : "1:M"
    LAUNDRY ||--o{ FAVORITES : "1:M"
    LAUNDRY ||--o{ GALERI : "1:M"
    BOOKINGS ||--|| USERS : "M:1"
    BOOKINGS ||--|| KONTRAKANS : "M:1"

    USERS : PK id
    USERS : string name
    USERS : string email
    USERS : enum role

    KONTRAKANS : PK id
    KONTRAKANS : string nama
    KONTRAKANS : string alamat
    KONTRAKANS : int harga
    KONTRAKANS : decimal jarak
    KONTRAKANS : enum status

    BOOKINGS : PK id
    BOOKINGS : FK kontrakan_id
    BOOKINGS : FK user_id
    BOOKINGS : date start_date
    BOOKINGS : date end_date
    BOOKINGS : enum status

    LAUNDRY : PK id
    LAUNDRY : string nama
    LAUNDRY : string alamat
    LAUNDRY : int harga

    LAYANAN_LAUNDRY : PK id
    LAYANAN_LAUNDRY : FK laundry_id
    LAYANAN_LAUNDRY : string nama_paket

    REVIEWS : PK id
    REVIEWS : FK user_id
    REVIEWS : string type
    REVIEWS : int item_id
    REVIEWS : int rating

    FAVORITES : PK id
    FAVORITES : FK user_id
    FAVORITES : string type
    FAVORITES : int item_id

    GALERI : PK id
    GALERI : string type
    GALERI : int item_id
    GALERI : string foto
```

---

## 2. Daftar Tabel & Penjelasan

### **USERS** - Tabel Pengguna
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint (PK) | ID pengguna unik |
| name | varchar(255) | Nama lengkap |
| email | varchar(255) | Email (unique) |
| password | varchar(255) | Password terenkripsi |
| phone | varchar(20) | Nomor telepon |
| role | enum | super_admin, admin, user |
| email_verified_at | timestamp | Verifikasi email |
| deleted_at | timestamp | Soft delete |
| created_at, updated_at | timestamp | Audit trail |

**Relasi:**
- `1:M` → BOOKINGS (seorang user bisa booking multiple kontrakan)
- `1:M` → REVIEWS (seorang user bisa review multiple item)
- `1:M` → FAVORITES (seorang user bisa favorite multiple item)
- `1:M` → ACTIVITY_LOGS (log semua aksi user)
- `1:M` → PERSONAL_ACCESS_TOKENS (API auth tokens)

---

### **KONTRAKANS** - Tabel Kontrakan
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint (PK) | ID kontrakan unik |
| nama | varchar(255) | Nama/jenis kontrakan |
| alamat | text | Alamat lengkap |
| no_whatsapp | varchar(20) | WhatsApp owner |
| latitude, longitude | decimal(10,8) | Koordinat GPS |
| harga | int | Harga per bulan (Rp) |
| jarak | decimal(5,2) | Jarak dari kampus (km) |
| fasilitas | text | Daftar fasilitas |
| jumlah_kamar | int | Jumlah kamar |
| bathroom_count | int | Jumlah kamar mandi |
| foto | varchar(255) | Foto primary |
| status | enum | available, booked, occupied |
| occupied_until | date | Tanggal kontrakan berakhir |
| created_at, updated_at | timestamp | Audit trail |

**Relasi:**
- `1:M` → BOOKINGS (1 kontrakan bisa di-booking multiple times)
- `1:M` → REVIEWS (banyak review untuk 1 kontrakan)
- `1:M` → FAVORITES (banyak user bisa favorite 1 kontrakan)
- `1:M` → GALERI (banyak foto untuk 1 kontrakan)
- `1:M` → KRITERIA (difilter berdasarkan kriteria)

**Status Flow:**
```
available → booked (ada booking confirmed/pending)
         → occupied (ada booking checked_in)
         → available (booking dibatalkan/selesai)
```

---

### **BOOKINGS** - Tabel Pemesanan (Core Business Logic)
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint (PK) | ID booking unik |
| kontrakan_id | bigint (FK) | Referensi kontrakan |
| user_id | bigint (FK) | Referensi penyewa |
| start_date | date | Tanggal check-in |
| end_date | date | Tanggal check-out |
| status | enum | pending, confirmed, checked_in, completed, cancelled |
| amount | decimal(12,2) | Total harga booking (Rp) |
| payment_status | enum | unpaid, paid, refunded |
| payment_method | varchar(50) | transfer, cod, dll |
| payment_proof | varchar(255) | Bukti transfer (file path) |
| paid_at | timestamp | Kapan pembayaran |
| notes | text | Catatan khusus |
| booking_source | varchar(50) | api, web, mobile |
| tenant_name | varchar(255) | Nama penyewa (dari API) |
| tenant_phone | varchar(20) | Telepon penyewa |
| confirmed_at, checked_in_at, checked_out_at | timestamp | Audit timestamps |
| cancelled_at | timestamp | Kapan dibatalkan |
| cancellation_reason | text | Alasan pembatalan |
| created_at, updated_at | timestamp | Audit trail |

**Relasi:**
- `M:1` → USERS (banyak booking dari 1 user)
- `M:1` → KONTRAKANS (banyak booking untuk 1 kontrakan)

**Auto-Sync Status Kontrakan:**
```
Saat booking dihapus/dibatalkan:
  ↓
  Cek booking lain untuk kontrakan yang sama
  ↓
  - Ada booking checked_in? → status = "occupied"
  - Ada booking confirmed/pending? → status = "booked"
  - Tidak ada? → status = "available" + clear occupied_until
```

---

### **REVIEWS** (Polymorphic) - Tabel Review/Rating
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint (PK) | ID review unik |
| type | varchar(50) | "kontrakan" atau "laundry" |
| item_id | int | ID kontrakan atau laundry |
| user_id | bigint (FK) | Siapa yang review |
| rating | int | 1-5 stars |
| review | text | Isi review |
| created_at, updated_at | timestamp | Audit trail |

**Relasi:**
- `M:1` → USERS (banyak review dari 1 user)
- `M:1→` KONTRAKANS (via polymorphic: type='kontrakan' + item_id)
- `M:1→` LAUNDRY (via polymorphic: type='laundry' + item_id)

**Contoh Data:**
```
| id | type | item_id | user_id | rating | review |
| 1  | kontrakan | 5 | 3 | 5 | Bagus banget! |
| 2  | laundry | 2 | 4 | 4 | Cepat tapi mahal |
```

---

### **FAVORITES** (Polymorphic) - Tabel Favorit
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint (PK) | ID favorite unik |
| type | varchar(50) | "kontrakan" atau "laundry" |
| item_id | int | ID kontrakan atau laundry |
| user_id | bigint (FK) | User yang favorite |
| created_at, updated_at | timestamp | Audit trail |

**Relasi:**
- `M:1` → USERS (banyak favorite dari 1 user)
- `M:1→` KONTRAKANS (via polymorphic)
- `M:1→` LAUNDRY (via polymorphic)

**Constraint:** UNIQUE(user_id, type, item_id) - 1 user tidak bisa favorite item yang sama 2x

---

### **GALERI** (Polymorphic) - Tabel Foto/Gallery
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint (PK) | ID foto unik |
| type | varchar(50) | "kontrakan" atau "laundry" |
| item_id | int | ID kontrakan atau laundry |
| foto | varchar(255) | File path foto |
| urutan | int | Urutan display (1, 2, 3, ...) |
| is_primary | boolean | Apakah foto utama |
| caption | text | Deskripsi foto |
| created_at, updated_at | timestamp | Audit trail |

**Relasi:**
- `M:1→` KONTRAKANS (via polymorphic)
- `M:1→` LAUNDRY (via polymorphic)

---

### **LAUNDRY** - Tabel Laundry
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint (PK) | ID laundry unik |
| nama | varchar(255) | Nama laundry |
| alamat | text | Alamat |
| no_whatsapp | varchar(20) | WhatsApp laundry |
| latitude, longitude | decimal(10,8) | Koordinat GPS |
| jarak | decimal(5,2) | Jarak dari kampus (km) |
| fasilitas | text | Daftar fasilitas |
| jam_buka | time | Jam buka (HH:mm) |
| jam_tutup | time | Jam tutup (HH:mm) |
| status | enum | active, inactive |
| foto | varchar(255) | Foto primary |
| created_at, updated_at | timestamp | Audit trail |

**Relasi:**
- `1:M` → LAYANAN_LAUNDRY (1 laundry punya banyak layanan)
- `1:M` → REVIEWS (banyak review)
- `1:M` → FAVORITES (banyak user favorite)
- `1:M` → GALERI (banyak foto)

---

### **LAYANAN_LAUNDRY** - Tabel Jenis/Paket Layanan
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint (PK) | ID layanan unik |
| laundry_id | bigint (FK) | Laundry mana |
| jenis_layanan | enum | regular, express, kilat |
| nama_paket | varchar(255) | Nama paket (e.g., "Reguler 5kg") |
| harga | decimal(10,2) | Harga layanan (Rp) |
| estimasi_selesai | varchar(50) | Estimasi waktu ("2 hari", "24 jam") |
| deskripsi | text | Detail layanan |
| status | enum | active, inactive |
| rating | decimal(3,2) | Rating layanan |
| waktu_proses | int | Waktu dalam jam |
| created_at, updated_at | timestamp | Audit trail |

**Relasi:**
- `M:1` → LAUNDRY (banyak layanan dari 1 laundry)

---

### **KRITERIA** - Tabel Filter/SPK Kriteria
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint (PK) | ID kriteria unik |
| tipe_bisnis | enum | "kontrakan", "laundry" |
| nama_kriteria | varchar(255) | Nama kriteria filter |
| bobot | int | Bobot/priority (untuk SPK) |
| tipe | enum | numeric, categorical, boolean |
| keterangan | text | Penjelasan kriteria |
| created_at, updated_at | timestamp | Audit trail |

**Contoh untuk Kontrakan:**
```
| id | tipe_bisnis | nama_kriteria | bobot | tipe | keterangan |
| 1  | kontrakan | Harga | 30 | numeric | Harga sewa per bulan |
| 2  | kontrakan | Jarak Kampus | 25 | numeric | Jarak dalam KM |
| 3  | kontrakan | Jumlah Kamar | 20 | numeric | Banyak kamar |
| 4  | kontrakan | Fasilitas | 25 | categorical | Kelengkapan fasilitas |
```

---

### **ACTIVITY_LOGS** - Tabel Audit Log
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint (PK) | ID log unik |
| user_id | bigint (FK) | User yang melakukan aksi |
| action | varchar(50) | create, update, delete, login, dll |
| description | text | Deskripsi aksi |
| model_type | varchar(50) | Model apa yang di-akses (User, Booking, dll) |
| model_id | int | ID model yang di-akses |
| old_values | json | Nilai lama (untuk update/delete) |
| new_values | json | Nilai baru (untuk create/update) |
| ip_address | varchar(45) | IP address user |
| user_agent | text | Browser info |
| created_at | timestamp | Kapan terjadi |

**Relasi:**
- `M:1` → USERS (audit log user)
- `Polymorphic` → Berbagai models (via model_type + model_id)

**Contoh Data:**
```
{
  "action": "update",
  "description": "Update harga kontrakan",
  "model_type": "Kontrakan",
  "model_id": 5,
  "old_values": {"harga": 2000000},
  "new_values": {"harga": 2500000}
}
```

---

### **PERSONAL_ACCESS_TOKENS** - Tabel API Tokens
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint (PK) | ID token unik |
| user_id | bigint (FK) | User pemilik token |
| name | varchar(255) | Nama token (e.g., "Mobile App") |
| token | varchar(80) | Hash token |
| abilities | json | Izin apa saja (e.g., ["*"]) |
| last_used_at | timestamp | Terakhir dipakai |
| expires_at | timestamp | Kapan expired |
| created_at, updated_at | timestamp | Audit trail |

**Relasi:**
- `M:1` → USERS (banyak token per user)

**Gunanya:**
- Mobile app authentication
- Third-party API access
- Stateless authentication dengan Laravel Sanctum

---

## 3. Summary Relasi

| Relasi | From | To | Tipe | Keterangan |
|--------|------|-----|------|-----------|
| User → Booking | 1 | M | Parent | Seorang user bisa booking multiple kontrakan |
| Kontrakan → Booking | 1 | M | Parent | 1 kontrakan bisa di-booking multiple times |
| User → Review | 1 | M | Parent | Seorang user bisa review multiple item |
| Review → Kontrakan/Laundry | M | 1 | Polymorphic | Review bisa untuk kontrakan atau laundry |
| User → Favorite | 1 | M | Parent | Seorang user bisa favorite multiple item |
| Favorite → Kontrakan/Laundry | M | 1 | Polymorphic | Favorite bisa untuk kontrakan atau laundry |
| Kontrakan → Galeri | 1 | M | Parent | 1 kontrakan punya banyak foto |
| Laundry → Galeri | 1 | M | Parent | 1 laundry punya banyak foto |
| Galeri → Kontrakan/Laundry | M | 1 | Polymorphic | Foto bisa untuk kontrakan atau laundry |
| Laundry → Layanan | 1 | M | Parent | 1 laundry punya banyak jenis layanan |
| User → ActivityLog | 1 | M | Parent | Setiap aksi user dicatat |
| ActivityLog → Models | M | 1 | Polymorphic | Log bisa untuk berbagai model |
| User → PersonalAccessToken | 1 | M | Parent | User bisa punya multiple API tokens |

---

## 4. Key Features

### **Auto-Sync Booking → Kontrakan Status**
```php
// Saat booking dihapus/dibatalkan:
$booking = Booking::find(1);
$kontrakanId = $booking->kontrakan_id;
$booking->delete(); // Trigger: Booking::syncKontrakanStatus($kontrakanId)

// Otomatis mengupdate status kontrakan berdasarkan booking aktif
```

### **Polymorphic Relationships**
```php
// Review bisa untuk kontrakan atau laundry:
$review = Review::create([
    'type' => 'kontrakan',  // atau 'laundry'
    'item_id' => 5,
    'user_id' => 1,
    'rating' => 5,
    'review' => 'Bagus!'
]);

// Query dengan polymorphic:
$reviews = Review::forItem('kontrakan', 5)->get();
```

### **GPS Distance Calculation**
```php
// Hitung jarak dari koordinat user menggunakan Haversine formula
$kontrakan->calculateDistance($userLat, $userLng);
// Return: 2.35 (km)
```

### **Activity Logging**
```php
// Semua aksi user terekam di database untuk audit trail
ActivityLog::log(
    action: 'update',
    description: 'Update booking status',
    modelType: 'Booking',
    modelId: 1,
    oldValues: ['status' => 'pending'],
    newValues: ['status' => 'confirmed']
);
```

---

## 5. Constraints & Rules

### **Primary Keys**
- Semua tabel memiliki `id` bigint unsigned sebagai PK
- Auto-increment

### **Foreign Keys**
- BOOKINGS.kontrakan_id → KONTRAKANS.id
- BOOKINGS.user_id → USERS.id
- REVIEWS.user_id → USERS.id
- FAVORITES.user_id → USERS.id
- LAYANAN_LAUNDRY.laundry_id → LAUNDRY.id
- ACTIVITY_LOGS.user_id → USERS.id (nullable)
- PERSONAL_ACCESS_TOKENS.user_id → USERS.id

### **Unique Constraints**
- USERS.email - unique
- FAVORITES(user_id, type, item_id) - unique (1 user tidak bisa favorite item sama 2x)

### **Enums**
- USERS.role: super_admin, admin, user
- BOOKINGS.status: pending, confirmed, checked_in, completed, cancelled
- BOOKINGS.payment_status: unpaid, paid, refunded
- KONTRAKANS.status: available, booked, occupied
- REVIEWS.type, FAVORITES.type, GALERI.type: kontrakan, laundry
- LAUNDRY.status: active, inactive
- KRITERIA.tipe_bisnis: kontrakan, laundry
- LAYANAN_LAUNDRY.jenis_layanan: regular, express, kilat

### **Soft Deletes**
- USERS - menggunakan soft delete (deleted_at)

### **Timestamps**
- created_at, updated_at otomatis di semua tabel

---

## 6. Normalisasi Database

✅ **Fully Normalized to 3NF:**
- Tidak ada redundansi data
- Setiap atribut non-key bergantung pada key
- Tidak ada transitive dependency

✅ **Efficient Indexing:**
- Foreign keys sudah di-index
- search columns (nama, alamat) direkomendasikan di-index

---

Dokumentasi ini dapat digunakan langsung untuk laporan sidang! 📊
