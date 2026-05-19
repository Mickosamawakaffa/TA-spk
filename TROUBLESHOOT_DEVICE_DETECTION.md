# 🔧 Troubleshoot: Device Tidak Terdeteksi di Flutter

## Status Saat Ini
- ❌ Device fisik NOT detected di `flutter devices`
- ❌ Device fisik NOT detected di ADB
- ✅ Android SDK sudah terinstall
- 📱 HP Anda punya hotspot & sudah enabled

---

## Solusi Step-by-Step

### Step 1: Enable USB Debugging di HP Android
1. Buka **Settings** → **About Phone**
2. Tap **Build Number** sebanyak **7 kali** sampai muncul "You are now a developer"
3. Kembali ke Settings → **Developer Options** (muncul di bawah)
4. Cari **USB Debugging** dan nyalakan (toggle ON)
5. Jika muncul popup security, tap **Allow**

### Step 2: Hubungkan Device via USB
1. **Cabut kabel USB** dari HP (jika sudah terhubung)
2. Tunggu **5 detik**
3. Hubungkan kembali ke desktop dengan **mode MTP** atau **File Transfer**
   - Jika ada popup di HP, pilih "File Transfer" atau "MTP"
4. Desktop akan mendeteksi HP sebagai storage device

### Step 3: Izinkan USB Debugging
1. Di HP, jika ada popup **"Allow USB debugging from this computer?"**
   - Centang ✓ "Always allow from this computer"
   - Tap **Allow**
2. Jika tidak ada popup, Anda mungkin perlu:
   - Cabut USB lagi
   - Buka DevTools di HP
   - Hubungkan ulang USB
   - Popup akan muncul

### Step 4: Verify Device terdeteksi
Buka PowerShell dan jalankan:
```powershell
& "C:\Users\MICKO\AppData\Local\Android\sdk\platform-tools\adb.exe" devices
```

**Expected output:** Device ID Anda akan muncul:
```
List of devices attached
R58M80DLXXX      device
```

### Step 5: Jalankan Flutter App di Device
```bash
cd C:\laragon\www\TA\spk_mobile
flutter devices        # Verify device terdeteksi
flutter run -d <device-id>
```

---

## 🚨 Jika Masih Tidak Terdeteksi

### Cek USB Driver
1. Buka **Device Manager** (Win + X → Device Manager)
2. Cari device HP Anda (mungkin di "Other devices" atau "Portable Devices")
3. Jika ada **⚠️ warning icon**:
   - Right-click → Update driver
   - Pilih "Browse my computer for drivers"
   - Navigasi ke: `C:\Users\MICKO\AppData\Local\Android\sdk\usb_driver`
   - Klik Next dan install

### Reset ADB Connection
```powershell
& "C:\Users\MICKO\AppData\Local\Android\sdk\platform-tools\adb.exe" kill-server
& "C:\Users\MICKO\AppData\Local\Android\sdk\platform-tools\adb.exe" start-server
& "C:\Users\MICKO\AppData\Local\Android\sdk\platform-tools\adb.exe" devices
```

### Coba Port USB Lain
- Lepas USB dari port yang digunakan
- Coba port USB lain di desktop (preferably USB 3.0)
- Ulangi deteksi

### Wireless ADB (Jika USB tidak bisa)
Setelah device pernah terdeteksi via USB sekali:
```powershell
# Set device ke wireless mode (ganti XXX dengan device ID)
& "C:\Users\MICKO\AppData\Local\Android\sdk\platform-tools\adb.exe" tcpip 5555

# Disconnect USB

# Connect via IP (ganti 192.168.x.x dengan IP device di hotspot)
& "C:\Users\MICKO\AppData\Local\Android\sdk\platform-tools\adb.exe" connect 192.168.x.x:5555
```

---

## Hotspot Setup
- ✅ Device hotspot ON
- ✅ Desktop terkoneksi ke hotspot device
- ✅ Firewall tidak block port 5555 (untuk wireless ADB)

---

## 📞 Common Issues & Fixes

| Masalah | Solusi |
|---------|--------|
| Popup "Allow USB debugging" tidak muncul | Cabut USB, tunggu 10s, pasang ulang + buka Developer Options di HP dulu |
| USB Driver error | Install dari `C:\Users\MICKO\AppData\Local\Android\sdk\usb_driver` |
| Device sudah terdeteksi tapi app crash | Update Flutter: `flutter upgrade` & `flutter pub get` di spk_mobile folder |
| Koneksi timeout saat `flutter run` | Device & desktop harus di network sama, cek IP: `ipconfig` |

