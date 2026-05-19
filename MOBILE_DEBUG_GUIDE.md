# PANDUAN DEBUGGING LOGIN & REGISTER ERROR - SPK MOBILE

## 🔍 LANGKAH DEBUGGING

### 1. PERIKSA BACKEND SERVER
```bash
# Cek apakah backend running di IP yang benar
# Default: http://10.21.24.99:41197

# Di Windows, buka PowerShell dan test:
curl http://10.21.24.99:41197/api/login -X POST -H "Content-Type: application/json" -d '{"email":"test@test.com","password":"123456"}'
```

### 2. LIHAT LOGS APLIKASI MOBILE
```bash
# Terminal baru, jalankan:
flutter logs

# Ini akan menampilkan semua debug print dari aplikasi
# Cari pesan yang dimulai dengan "Login response status:" atau "Register response status:"
```

### 3. PERIKSA KONEKSI INTERNET DEVICE
- Pastikan mobile device terhubung ke WiFi yang sama dengan backend server
- Cek IP address backend server benar di `lib/config/app_config.dart`

### 4. RESET DAN REBUILD APLIKASI
```bash
# Terminal di folder spk_mobile:
flutter clean
flutter pub get
flutter run
```

---

## 🎯 ERROR MESSAGES & SOLUSI

### Error: "Gagal terhubung ke server. Periksa koneksi internet."
**Penyebab:** Network unreachable atau server down
**Solusi:**
- [ ] Cek backend server running (di terminal backend, harus ada "Server listening on...")
- [ ] Cek WiFi device connected ke network yang sama
- [ ] Verify IP address di AppConfig correct
- [ ] Test: `ping 10.21.24.99` dari device/computer

### Error: "Login timeout. Server tidak merespons."
**Penyebab:** Server slow atau tidak respond
**Solusi:**
- [ ] Backend process hang, restart dengan `php artisan serve`
- [ ] Check database connection OK
- [ ] Check server logs: `storage/logs/laravel.log`

### Error: "Invalid response format from server"
**Penyebab:** Server return response yang tidak sesuai format
**Solusi:**
- [ ] Check backend controller return JSON response yang benar
- [ ] Verify response mengandung: `success`, `data`, `message`
- [ ] Test endpoint dengan Postman/curl

### Error: "Email tidak valid" atau field validation errors
**Penyebab:** Input validation gagal di mobile
**Solusi:**
- [ ] Email harus mengandung @
- [ ] Password minimal 6 karakter
- [ ] Nama tidak boleh kosong
- [ ] Konfirmasi password harus sama

---

## 🔧 DEBUGGING TIPS

### Lihat Response dari Server
Di file `auth_service.dart`, debug message sudah ada:
```
debugPrint('Login response status: 200');
debugPrint('Login response body: {...}');
```

Buka `flutter logs` dan cari pesan ini untuk lihat response lengkap.

### Enable Network Logging
Tambahkan di `main.dart` untuk debug HTTP requests:
```dart
// Di main() sebelum runApp:
// Hanya untuk development!
HttpClient.enableTimelineLogging = true;
```

### Test Di Emulator vs Device
- Emulator: IP `10.0.2.2` mungkin diperlukan untuk localhost
- Device real: IP address sesuai network

---

## 📋 CHECKLIST SEBELUM SUBMIT

- [ ] `flutter analyze` - no errors
- [ ] `flutter pub get` - berhasil
- [ ] Backend server running dan responsive
- [ ] Device connect ke network yang sama dengan backend
- [ ] Test login dengan credential yang benar
- [ ] Test register dengan email baru
- [ ] Check `flutter logs` untuk debug messages
- [ ] Verify tidak ada "Unhandled Exception"

---

## 🚀 JIKA SEMUA MASIH ERROR

Buat issue dengan info:
1. **Flutter version:** `flutter --version`
2. **Device/Emulator:** iOS/Android, version
3. **Backend URL:** yang sedang dipakai
4. **Error message:** lengkap dari flutter logs
5. **Steps to reproduce:** apa yg dilakukan sebelum error

---

## 📞 QUICK REFERENCE

| Issue | Likely Cause | Quick Fix |
|-------|--------------|-----------|
| Connection refused | Backend not running | Run `php artisan serve` |
| Timeout | Network slow | Check WiFi, restart app |
| Invalid JSON | Bad response | Check backend controller |
| Field validation | Wrong input | Enter valid email/password |
| 401 Unauthorized | Invalid token | Token expired, login again |
| 422 Validation error | Server validation fail | Check error details in logs |

