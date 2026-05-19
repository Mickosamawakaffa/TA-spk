# 🎯 QUICK START - PERBAIKAN MOBILE LOGIN & REGISTER

## ⚡ DALAM 5 MENIT

### Apa yang diperbaiki?
✅ Error handling di login & register
✅ Null safety checks
✅ Network error handling (timeout, connection failed)
✅ Better error messages for users
✅ Debug logging untuk troubleshooting

### File yang diperbaiki:
- ✅ `spk_mobile/lib/login.dart`
- ✅ `spk_mobile/lib/register.dart`
- ✅ `spk_mobile/lib/services/auth_service.dart`

---

## 🚀 CARA SETUP (COPY-PASTE)

### Terminal 1 - Update dependencies
```bash
cd c:\laragon\www\TA\spk_mobile
flutter pub get
flutter clean
```

### Terminal 2 - Run backend
```bash
cd c:\laragon\www\TA\spk_kontrakan
php artisan serve
```

### Terminal 3 - Run mobile
```bash
cd c:\laragon\www\TA\spk_mobile
flutter run
```

### Terminal 4 - Watch logs (optional)
```bash
cd c:\laragon\www\TA\spk_mobile
flutter logs
```

---

## 🧪 TEST

1. **Login Success:** Email valid + password → Navigate ke home
2. **Login Fail:** Email/password salah → Show error message
3. **Register:** Name + email baru + password → Success or error clear
4. **Network Error:** Matikan WiFi → Show "Gagal terhubung" message

---

## ❓ ERROR?

### "Gagal terhubung ke server"
→ Backend server belum running, jalankan `php artisan serve`

### "Login timeout"
→ Network slow, check WiFi connection atau restart backend

### "Invalid response format"
→ Backend error, check `spk_kontrakan/storage/logs/laravel.log`

### Lihat detail error
→ Buka `flutter logs` di terminal, cari "Login response status:"

---

## 📖 DOKUMENTASI LENGKAP

- **PERBAIKAN_LOGIN_REGISTER_LENGKAP.md** ← READ THIS FIRST
- **MOBILE_ERROR_DIAGNOSIS_AND_FIXES.md** ← Technical details
- **MOBILE_DEBUG_GUIDE.md** ← Troubleshooting guide

---

## ✨ PERUBAHAN UTAMA

| File | Perubahan |
|------|-----------|
| **login.dart** | ✅ Null safety, better errors, error borders |
| **register.dart** | ✅ Better error handling, navigate to login on success |
| **auth_service.dart** | ✅ Timeout, network error handling, debug logs |

---

## 💡 TIPS

- Jangan lupa `flutter pub get` setelah pull
- Backend must running before test mobile app
- IP address di AppConfig harus correct
- Device/emulator harus same WiFi sebagai backend

