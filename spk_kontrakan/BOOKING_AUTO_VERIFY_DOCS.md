# Auto-Verify Bookings Scheduler - Dokumentasi

## Tujuan
Sistem otomatis yang menjalankan verifikasi booking setiap hari tengah malam. Ketika booking sudah mencapai check-in date dan pembayaran sudah lunas, maka status booking otomatis diubah menjadi "checked_in" dan kontrakan menjadi "occupied".

## Cara Kerja

### Flow Sistem
```
Setiap hari jam 00:00 (tengah malam):

1. Scheduler jalan otomatis
2. Query semua booking:
   - Payment status = PAID (sudah bayar)
   - Status = PENDING atau CONFIRMED (belum checked_in)
   - Start date <= hari ini (check-in date sudah tiba)
   - Checked_in_at = NULL (belum ada check-in)

3. Untuk setiap booking yang match:
   - Update status = "checked_in"
   - Update checked_in_at = sekarang
   - Update kontrakan status = "occupied"

4. Log hasil ke system logs

5. Notif/email bisa ditambah di masa depan
```

### Contoh
```
Booking:
- Mahasiswa: Andi
- Kontrakan: Rumah Nyaman
- Check-in date: 7 Mar 2026
- Payment status: PAID
- Status: CONFIRMED
- checked_in_at: NULL

Hasil setelah scheduler jalan (7 Mar, 00:00):
- Status: checked_in ✓
- checked_in_at: 7 Mar 2026 00:00:00
- Kontrakan status: occupied ✓
```

## File Yang Ditambah

### 1. AutoVerifyBookings Command
**File:** `app/Console/Commands/AutoVerifyBookings.php`

Command ini yang melakukan pengecekan dan update. Bisa dijalankan manual dengan:
```bash
php artisan bookings:auto-verify
```

### 2. Console Kernel
**File:** `app/Console/Kernel.php`

File ini yang mendefinisikan jadwal scheduler. Scheduler akan otomatis jalan setiap hari jam 00:00.

### 3. Booking Model Update
**File:** `app/Models/Booking.php`

Tambah scope `readyForAutoVerify()` untuk query booking yang siap di-verify.

## Setup untuk Production

### Requirement: Laravel Task Scheduler (Laravel Scheduler)
Scheduler memerlukan cronjob yang berjalan di server. Tambahkan satu line ke crontab:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Ini menjalankan `schedule:run` setiap menit, yang kemudian akan menjalankan command yang dijadwalkan.

### Step by Step Setup:

1. **Pastikan migration sudah berjalan (opsional):**
```bash
php artisan migrate
```

2. **Test command manual (testing dulu):**
```bash
# Test tanpa production
php artisan bookings:auto-verify
```

Output akan seperti:
```
🚀 Memulai auto-verify bookings...
✅ Booking #1 (Andi) auto-verified
✅ Booking #5 (Budi) auto-verified
✨ Total 2 bookings berhasil di-verify
```

3. **Setup cronjob di server:**

Akses server via SSH, edit crontab:
```bash
crontab -e
```

Tambahkan line:
```bash
* * * * * cd /var/www/spk_kontrakan && php artisan schedule:run >> /dev/null 2>&1
```

4. **Verify sudah berjalan:**
```bash
# Di server
tail -f storage/logs/laravel.log
```

Harusnya ada log seperti:
```
[2026-03-07 00:00:15] local.INFO: Auto-verify bookings executed
[2026-03-07 00:00:15] local.INFO: ✅ Booking #1 (Andi) auto-verified
```

## Database Fields yang Digunakan

Dari `bookings` table:
- `payment_status` - 'paid' / 'unpaid'
- `status` - 'pending', 'confirmed', 'checked_in', 'completed', 'cancelled'
- `start_date` - Tanggal check-in
- `checked_in_at` - Timestamp check-in (diupdate oleh scheduler)
- Relasi: `kontrakan_id` untuk update status kontrakan

## Monitoring & Troubleshooting

### Logs
Lihat log di: `storage/logs/laravel.log`

### Manual Test
```bash
# Test tanpa harus tunggu jam 12 malam
php artisan bookings:auto-verify
```

### Debugging
Jika ada issue, tambah debug di command:
```bash
php artisan bookings:auto-verify --verbose
```

### Jika Server Tidak Ada Cron
Jika hosting tidak support cron, alternatif:
1. Pakai external service seperti cron-job.org
2. Buat manual trigger via controller (kurang ideal)
3. Upgrade hosting yang support cron

## Future Improvements

- [ ] Tambah notification email saat booking auto-verified
- [ ] Dashboard admin untuk monitor scheduler runs
- [ ] Grace period (jika booking cancel before check-in date)
- [ ] Webhook notification ke mobile app
- [ ] Activity logging untuk audit trail

## Kesimpulan

Dengan scheduler ini:
✅ No more manual update status  
✅ Kontrakan otomatis marked as occupied  
✅ No double-booking bisa terjadi  
✅ Admin zero effort  
✅ Aman & automated
