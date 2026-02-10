# SPK Mobile - Aplikasi Rekomendasi Tempat Tinggal dan Laundry

Aplikasi Android SPK (Sistem Pendukung Keputusan) untuk rekomendasi tempat tinggal dan laundry di area kampus menggunakan metode SAW.

## Getting Started

### Prerequisites

- Flutter SDK (versi terbaru)
- Android Studio / VS Code dengan Flutter extension
- Android device dengan Android 5.0 (API 21) atau lebih tinggi
- USB cable untuk menghubungkan device ke komputer

### Menjalankan Aplikasi di Device Android via USB

#### 1. Persiapan Device Android

**Aktifkan Developer Options:**
- Buka **Settings** → **About Phone**
- Ketuk **Build Number** sebanyak 7 kali
- Akan muncul notifikasi "You are now a developer"

**Aktifkan USB Debugging:**
- Buka **Settings** → **Developer Options**
- Aktifkan **USB Debugging**
- Aktifkan **Install via USB** (opsional)

#### 2. Sambungkan Device ke Komputer

- Sambungkan Android device ke komputer menggunakan USB cable
- Pilih mode **File Transfer** atau **MTP** saat muncul notifikasi di device
- Jika muncul popup "Allow USB debugging?", pilih **Allow** dan centang **Always allow from this computer**

#### 3. Verifikasi Device Terdeteksi

Buka terminal/command prompt di folder project dan jalankan:

```bash
flutter devices
```

Pastikan device Android Anda muncul dalam daftar, contoh:
```
2 connected devices:

sdk gphone64 arm64 (mobile) • emulator-5554 • android-arm64  • Android 13 (API 33)
SM A525F (mobile)           • R58M30XXXXX   • android-arm64 • Android 13 (API 33)
```

#### 4. Menjalankan Aplikasi

**Cara 1: Run ke semua device yang terdeteksi**
```bash
flutter run
```

**Cara 2: Run ke device tertentu**
```bash
flutter run -d <device-id>
```

Contoh:
```bash
flutter run -d R58M30XXXXX
```

**Cara 3: Run dengan hot reload (untuk development)**
```bash
flutter run --hot
```

#### 5. Troubleshooting

**Device tidak terdeteksi:**
- Install USB driver untuk device Anda (Samsung USB Driver, Xiaomi USB Driver, dll)
- Coba gunakan kabel USB lain
- Restart ADB:
  ```bash
  adb kill-server
  adb start-server
  ```

**Error "unauthorized":**
- Di device, izinkan USB debugging saat muncul popup
- Centang "Always allow from this computer"

**Error "insufficient permissions" (Linux/Mac):**
- Tambahkan udev rules untuk device Anda
- Atau jalankan dengan sudo (tidak disarankan)

**Device terdeteksi tapi tidak bisa install:**
- Pastikan device memiliki ruang penyimpanan yang cukup
- Pastikan "Install via USB" sudah diaktifkan di Developer Options
- Coba restart device dan komputer

### Menjalankan di Emulator

Jika tidak memiliki device fisik, Anda bisa menggunakan emulator:

1. Buka Android Studio
2. Tools → Device Manager
3. Create Virtual Device
4. Pilih device dan system image
5. Start emulator
6. Jalankan `flutter run`

### Build APK untuk Install Manual

Jika ingin membuat file APK untuk diinstall manual:

```bash
flutter build apk
```

File APK akan berada di: `build/app/outputs/flutter-apk/app-release.apk`

## Struktur Project

```
lib/
├── main.dart          # Halaman utama (HomeScreen)
├── login.dart         # Halaman login
└── register.dart      # Halaman register
```

## Fitur

- ✅ Login & Register (UI only)
- ✅ Pilih Tipe: Kontrakan / Laundry
- ✅ Pilih Bobot: Harga, Luas, Jarak dari Kampus
- ✅ Sistem Rekomendasi menggunakan metode SAW

## Teknologi

- Flutter 3.10.3+
- Material Design 3
- Dart 3.10.3+

## Author

SPK Mobile App
