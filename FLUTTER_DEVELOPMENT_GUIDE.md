## 📋 FLUTTER DEVELOPMENT SETUP CHECKLIST

Sebelum menjalankan `flutter run`, pastikan semua ini sudah dilakukan:

### ✅ BACKEND SETUP

- [ ] **Jalankan Backend dengan Network Access**
  ```bash
  # Pilih salah satu:
  # Option 1: Pakai script (RECOMMENDED)
  cd c:\laragon\www\TA\spk_kontrakan
  start start-backend-network.bat
  
  # Option 2: Manual command
  php artisan serve --host=0.0.0.0 --port=8000
  ```
  
  Expected output:
  ```
  Starting Laravel development server: http://0.0.0.0:8000
  ```

- [ ] **Verifikasi Port 8000 Running**
  ```bash
  netstat -ano | findstr :8000
  # Harusnya ada: TCP 0.0.0.0:8000 LISTENING
  ```

### 🛡️ FIREWALL SETUP

- [ ] **Allow Port 8000 di Windows Firewall**
  1. Buka "Windows Defender Firewall with Advanced Security"
  2. Klik "Inbound Rules" → "New Rule"
  3. Port → Specific port: 8000 → Allow
  4. Atau: Allow app "php.exe" melalui firewall

### 📱 DEVICE SETUP

- [ ] **Device Terhubung ke Network Yang Sama**
  - Device USB atau Emulator terhubung ke WiFi/network sama dengan komputer

- [ ] **Cek IP Komputer**
  ```bash
  ipconfig | findstr IPv4
  # Catat IP yang dimulai dengan 192.168.x.x
  ```

### 🔧 FLUTTER CONFIG

- [ ] **Update app_config.dart dengan IP yang Benar**
  ```dart
  // File: lib/config/app_config.dart
  static const String baseUrl = 'http://[YOUR-IP]:8000/api';
  static const String storageUrl = 'http://[YOUR-IP]:8000/storage';
  ```

### 🚀 FLUTTER RUN

- [ ] **Clean & Run**
  ```bash
  cd c:\laragon\www\TA\spk_mobile
  flutter clean
  flutter run
  ```

---

## 🆘 TROUBLESHOOTING

### ❌ "TimeoutException" - Tidak Bisa Terhubung ke Backend

**Checklist:**
1. ✅ Apakah backend running? `netstat -ano | findstr :8000`
2. ✅ Apakah firewall allow port 8000? 
3. ✅ Apakah device satu network? Ping ke IP:
   ```bash
   ping 192.168.18.16
   ```
4. ✅ Apakah IP di app_config.dart sesuai?
5. ✅ Coba akses via browser dulu: `http://192.168.18.16:8000`

### ✅ Akses OK di Browser tapi Flutter Gagal

- Kemungkinan: CORS issu atau Android Network Security
- Solusi: Cek di backend apakah CORS sudah enabled

### 🚫 "Connection Refused"

- Backend tidak running
- Solusi: Jalankan `start-backend-network.bat` terlebih dahulu

---

## 💡 BEST PRACTICES

1. **Selalu Jalankan Backend Terlebih Dahulu**
   - Backend harus ready sebelum Flutter app Connect

2. **Simpan IP di Environment Variable (tidak hardcode)**
   - Sudah tersedia di `environment.dart`

3. **Gunakan Script start-backend-network.bat**
   - Lebih mudah dari mengetik command panjang

4. **Test dengan Browser Dulu**
   - Akses `http://192.168.18.16:8000/api/kontrakan`
   - Harus return JSON jika API berjalan
