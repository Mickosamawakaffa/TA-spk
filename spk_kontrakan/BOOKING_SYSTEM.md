# Sistem Booking Kontrakan - Dokumentasi

## Ringkasan
Sistem ini memungkinkan pemilik kontrakan untuk melacak status penyewaan kontrakan mereka, termasuk siapa yang sedang menyewa, periode sewa, dan status pembayaran.

## Fitur Utama

### 1. Status Kontrakan
Setiap kontrakan memiliki status yang menunjukkan ketersediaan:
- **Tersedia (available)** - Kontrakan siap disewakan
- **Dipesan (booked)** - Kontrakan sudah dibooking tapi belum ditempati
- **Terisi (occupied)** - Kontrakan sedang ditempati penyewa
- **Pemeliharaan (maintenance)** - Kontrakan sedang dalam perbaikan

### 2. Manajemen Booking
Setiap booking memiliki siklus hidup:
1. **Pending** - Booking baru dibuat, menunggu konfirmasi
2. **Confirmed** - Booking dikonfirmasi, penyewa belum masuk
3. **Checked In** - Penyewa sudah masuk dan menempati kontrakan
4. **Completed** - Masa sewa selesai, penyewa sudah keluar
5. **Cancelled** - Booking dibatalkan

### 3. Pengecekan Konflik Otomatis
- Sistem mencegah double-booking dengan pengecekan overlap tanggal
- Menggunakan database transaction untuk menghindari race condition
- Validasi ketersediaan real-time via AJAX

### 4. Sinkronisasi Status Otomatis
- Status kontrakan otomatis berubah saat ada aksi booking
- Artisan command untuk sinkronisasi manual: `php artisan kontrakan:sync-status`

## Cara Penggunaan

### Membuat Booking Baru
1. Klik menu **Booking Kontrakan** di sidebar
2. Klik tombol **Buat Booking Baru**
3. Pilih kontrakan yang akan dibooking
4. Isi tanggal mulai dan selesai (sistem akan cek ketersediaan)
5. Isi data penyewa (nama, nomor HP)
6. Klik **Simpan Booking**

### Mengelola Booking
- **Konfirmasi**: Ubah status dari Pending ke Confirmed
- **Check-in**: Tandai penyewa sudah masuk (status kontrakan jadi "Terisi")
- **Check-out**: Tandai penyewa sudah keluar (status kontrakan kembali "Tersedia")
- **Batalkan**: Batalkan booking dengan alasan (opsional)
- **Tandai Lunas**: Catat pembayaran sudah lunas

### Melihat Riwayat Booking
- Dari halaman detail kontrakan, klik tombol **Riwayat Booking**
- Atau dari menu Booking → filter berdasarkan kontrakan

## Struktur Database

### Tabel `kontrakans` (diperbarui)
```sql
status ENUM('available', 'booked', 'occupied', 'maintenance') DEFAULT 'available'
occupied_until DATE NULL
```

### Tabel `bookings` (baru)
```sql
- id
- kontrakan_id (FK ke kontrakans)
- user_id (FK ke users, nullable)
- start_date
- end_date
- status (pending/confirmed/checked_in/completed/cancelled)
- amount (biaya sewa)
- payment_status (unpaid/paid/refunded)
- payment_method
- paid_at
- tenant_name
- tenant_phone
- notes
- confirmed_at
- checked_in_at
- checked_out_at
- cancelled_at
- cancellation_reason
- timestamps
```

## File-file Baru

### Migrations
- `database/migrations/2025_12_26_000001_add_status_to_kontrakans.php`
- `database/migrations/2025_12_26_000002_create_bookings_table.php`

### Models
- `app/Models/Booking.php`
- `app/Models/Kontrakan.php` (diperbarui)

### Controllers
- `app/Http/Controllers/BookingController.php`

### Views
- `resources/views/admin/bookings/index.blade.php` - Daftar booking
- `resources/views/admin/bookings/create.blade.php` - Form buat booking
- `resources/views/admin/bookings/show.blade.php` - Detail booking
- `resources/views/admin/bookings/edit.blade.php` - Edit booking
- `resources/views/admin/bookings/kontrakan-history.blade.php` - Riwayat booking per kontrakan

### Commands
- `app/Console/Commands/SyncKontrakanStatus.php` - Sinkronisasi status

## API Endpoint

### Cek Ketersediaan
```
GET /admin/bookings/check-availability
Parameters:
  - kontrakan_id: ID kontrakan
  - start_date: Tanggal mulai (YYYY-MM-DD)
  - end_date: Tanggal selesai (YYYY-MM-DD)
  - exclude_id: ID booking yang dikecualikan (untuk edit)

Response:
{
  "available": true/false,
  "conflicts": [...] // daftar booking yang bertabrakan
}
```

## Artisan Commands

### Sinkronisasi Status Manual
```bash
php artisan kontrakan:sync-status
```
Gunakan untuk memastikan status semua kontrakan sesuai dengan data booking.

## Tips untuk Sidang TA

### Poin-poin yang bisa disampaikan:
1. **Concurrency Handling** - Sistem menggunakan database transaction untuk mencegah race condition saat multiple user membuat booking bersamaan
2. **Conflict Detection** - Algoritma pengecekan overlap tanggal untuk mencegah double-booking
3. **State Machine** - Booking memiliki lifecycle yang terstruktur dengan validasi transisi status
4. **Real-time Validation** - AJAX untuk validasi ketersediaan sebelum submit
5. **Audit Trail** - Setiap aksi dicatat waktunya (confirmed_at, checked_in_at, dll)
6. **Soft Status Sync** - Status kontrakan otomatis mengikuti status booking aktif

### Demo yang bisa ditampilkan:
1. Buat booking baru dengan cek ketersediaan
2. Konfirmasi → Check-in → Check-out flow
3. Coba buat booking yang overlap (akan ditolak)
4. Batalkan booking dan lihat status kontrakan berubah
5. Jalankan artisan command untuk sync status

## Pengembangan Lanjutan (Future)
- Notifikasi via WhatsApp/Email saat booking berubah status
- Dashboard laporan pendapatan per bulan
- Integrasi payment gateway
- Calendar view untuk visualisasi booking
- Export laporan ke PDF/Excel
