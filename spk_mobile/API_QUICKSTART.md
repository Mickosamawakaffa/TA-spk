# API Quick Start Guide

Panduan cepat untuk memulai menggunakan API di Flutter app.

## Setup Awal

### 1. Update Base URL (PENTING!)
Edit file `lib/config/app_config.dart`:

```dart
class AppConfig {
  // Ganti sesuai IP komputer Anda
  static const String baseUrl = 'http://192.168.18.16:8000/api';
  static const String storageUrl = 'http://192.168.18.16:8000/storage';
}
```

Cek IP komputer Anda:
- Windows: Buka Command Prompt, ketik `ipconfig`, cari IPv4 Address
- Mac/Linux: Terminal, ketik `ifconfig`

### 2. Load Token on App Startup
Di `main.dart` atau `lib/main.dart`, tambahkan:

```dart
void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Load saved token jika ada
  final authService = AuthService();
  await authService.loadToken();
  
  runApp(const MyApp());
}
```

---

## Common API Usage Examples

### 1. Login User
```dart
import 'package:spk_mobile/services/auth_service.dart';

final authService = AuthService();

final result = await authService.login('user@email.com', 'password');

if (result['success']) {
  print('Login berhasil!');
  print('Token: ${authService.token}');
  print('User: ${authService.currentUser?.name}');
} else {
  print('Error: ${result['message']}');
}
```

### 2. Register User
```dart
final result = await authService.register(
  name: 'John Doe',
  email: 'john@email.com',
  password: 'password123',
  passwordConfirmation: 'password123',
  role: 'mahasiswa', // atau 'pemilik_kontrakan'
);

if (result['success']) {
  print('Register berhasil!');
} else {
  print('Error: ${result['message']}');
  print('Errors: ${result['errors']}');
}
```

### 3. Get All Kontrakan (with filters)
```dart
import 'package:spk_mobile/services/kontrakan_service.dart';

final kontrakanService = KontrakanService();

// Get semua kontrakan
List<Kontrakan> list = await kontrakanService.getKontrakan();

// Get dengan filter
list = await kontrakanService.getKontrakan(
  search: 'Kamar mandi pribadi',
  hargaMin: 500000,
  hargaMax: 1500000,
  jumlahKamar: 2,
);

for (var kontrakan in list) {
  print('${kontrakan.nama} - Rp ${kontrakan.harga}');
}
```

### 4. Get Kontrakan Detail
```dart
final detail = await kontrakanService.getKontrakanById(1);

if (detail != null) {
  print('Nama: ${detail.nama}');
  print('Harga: ${detail.hargaBulanan}');
  print('Deskripsi: ${detail.deskripsi}');
}
```

### 5. Get Kontrakan Reviews
```dart
final reviews = await kontrakanService.getReviews(1);

for (var review in reviews) {
  print('Rating: ${review['rating']}');
  print('Comment: ${review['comment']}');
  print('Author: ${review['user']['name']}');
}
```

### 6. Get SAW Recommendations
```dart
final recommendations = await kontrakanService.getRecommendations(
  hargaMax: 1000000,
  jarakMax: 5.0,
  fasilitas: 'wifi,ac',
);

// Gunakan recommendations untuk ranking
final ranked = recommendations['ranking'] as List;
for (var item in ranked) {
  print('${item['name']} - Score: ${item['score']}');
}
```

### 7. Create Booking
```dart
import 'package:spk_mobile/services/booking_service.dart';
import 'dart:io';

final bookingService = BookingService();

// Persiapkan file bukti pembayaran
final paymentFile = File('/path/to/payment_proof.jpg');

final result = await bookingService.createBooking(
  kontrakanId: 1,
  tanggalMulai: DateTime.now(),
  durasiBulan: 3,
  catatan: 'Butuh AC dan dekat kampus',
  paymentProof: paymentFile,
);

if (result['success']) {
  final booking = result['booking'] as Booking;
  print('Booking created! ID: ${booking.id}');
} else {
  print('Error: ${result['message']}');
}
```

### 8. Get Booking History
```dart
final bookings = await bookingService.getBookingHistory();

for (var booking in bookings) {
  print('ID: ${booking.id}');
  print('Kontrakan: ${booking.kontrakan['nama']}');
  print('Status: ${booking.status}');
  print('Total: Rp ${booking.totalBiaya}');
}
```

### 9. Cancel Booking
```dart
final result = await bookingService.cancelBooking(1);

if (result['success']) {
  print('Booking cancelled!');
} else {
  print('Error: ${result['message']}');
}
```

### 10. Add Review
```dart
import 'package:spk_mobile/services/review_service.dart';

final reviewService = ReviewService();

final result = await reviewService.addKontrakanReview(
  kontrakanId: 1,
  rating: 4.5,
  comment: 'Nyaman dan bersih, hanya aja AC agak berisik',
);

if (result['success']) {
  print('Review posted!');
} else {
  print('Error: ${result['message']}');
}
```

### 11. Toggle Favorite
```dart
import 'package:spk_mobile/services/favorite_service.dart';

final favoriteService = FavoriteService();

// Add/remove from favorites
final result = await favoriteService.toggleKontrakanFavorite(1);

if (result['success']) {
  print('Favorite status: ${result['isFavorite']}');
}

// Check if favorite
final isFav = await favoriteService.isKontrakanFavorite(1);
print('Is favorite: $isFav');
```

### 12. Get User's Favorites
```dart
final favorites = await favoriteService.getFavorites();

print('Kontrakan favorites: ${favorites['kontrakan']}');
print('Laundry favorites: ${favorites['laundry']}');
```

### 13. Get Laundry Services
```dart
import 'package:spk_mobile/services/laundry_service.dart';

final laundryService = LaundryService();

// Get semua laundry
List<Laundry> list = await laundryService.getLaundry();

// Dengan filter
list = await laundryService.getLaundry(
  search: 'express',
  hargaMax: 10000,
);

for (var laundry in list) {
  print('${laundry.nama} - Rp ${laundry.harga}');
}
```

### 14. Update User Profile
```dart
final result = await authService.updateProfile(
  name: 'John Doe',
  phone: '081234567890',
  address: 'Jalan ABC No 123',
);

if (result['success']) {
  print('Profile updated!');
}
```

### 15. Logout
```dart
await authService.logout();
print('Logged out. Token: ${authService.token}');
```

---

## Using in Widgets

### Example: Login Screen
```dart
import 'package:flutter/material.dart';
import 'package:spk_mobile/services/auth_service.dart';

class LoginScreen extends StatefulWidget {
  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _authService = AuthService();
  bool _isLoading = false;

  void _login() async {
    setState(() => _isLoading = true);

    final result = await _authService.login(
      _emailController.text,
      _passwordController.text,
    );

    setState(() => _isLoading = false);

    if (result['success']) {
      // Navigate to home
      Navigator.of(context).pushReplacementNamed('/home');
    } else {
      // Show error
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'])),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Login')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            TextField(
              controller: _emailController,
              decoration: InputDecoration(labelText: 'Email'),
            ),
            TextField(
              controller: _passwordController,
              decoration: InputDecoration(labelText: 'Password'),
              obscureText: true,
            ),
            SizedBox(height: 20),
            _isLoading
                ? CircularProgressIndicator()
                : ElevatedButton(
                    onPressed: _login,
                    child: Text('Login'),
                  ),
          ],
        ),
      ),
    );
  }
}
```

### Example: Kontrakan List Screen
```dart
import 'package:flutter/material.dart';
import 'package:spk_mobile/services/kontrakan_service.dart';
import 'package:spk_mobile/models/kontrakan.dart';

class KontrakanScreen extends StatefulWidget {
  @override
  State<KontrakanScreen> createState() => _KontrakanScreenState();
}

class _KontrakanScreenState extends State<KontrakanScreen> {
  late Future<List<Kontrakan>> _kontrakanFuture;
  final _kontrakanService = KontrakanService();

  @override
  void initState() {
    super.initState();
    _kontrakanFuture = _kontrakanService.getKontrakan();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Kontrakan')),
      body: FutureBuilder<List<Kontrakan>>(
        future: _kontrakanFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: CircularProgressIndicator());
          }

          if (snapshot.hasError) {
            return Center(
              child: Text('Error: ${snapshot.error}'),
            );
          }

          final kontrakan = snapshot.data ?? [];

          return ListView.builder(
            itemCount: kontrakan.length,
            itemBuilder: (context, index) {
              final item = kontrakan[index];
              return ListTile(
                title: Text(item.nama),
                subtitle: Text('Rp ${item.hargaBulanan}'),
                onTap: () {
                  // Navigate to detail
                  Navigator.of(context).pushNamed(
                    '/kontrakan-detail',
                    arguments: item.id,
                  );
                },
              );
            },
          );
        },
      ),
    );
  }
}
```

---

## Error Handling Best Practices

```dart
try {
  final result = await bookingService.createBooking(...);
  
  if (!result['success']) {
    // Handle API error
    final message = result['message'];
    final errors = result['errors'] as Map?;
    
    if (errors != null) {
      errors.forEach((key, value) {
        print('$key: $value');
      });
    }
    
    // Show error to user
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message)),
    );
  } else {
    // Handle success
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text('Success!')),
    );
  }
} catch (e) {
  // Handle exception
  print('Exception: $e');
  
  ScaffoldMessenger.of(context).showSnackBar(
    SnackBar(content: Text('Terjadi kesalahan: $e')),
  );
}
```

---

## Testing API Connection

Tambahkan di main.dart untuk test:

```dart
import 'package:spk_mobile/services/auth_service.dart';
import 'package:spk_mobile/services/kontrakan_service.dart';

void testAPIs() async {
  print('Testing API connections...');
  
  // Test 1: Load token
  final authService = AuthService();
  await authService.loadToken();
  print('Token loaded: ${authService.token?.substring(0, 10)}...');

  // Test 2: Get kontrakan
  final kontrakanService = KontrakanService();
  final kontrakan = await kontrakanService.getKontrakan();
  print('Kontrakan count: ${kontrakan.length}');

  // Test 3: Get first kontrakan detail
  if (kontrakan.isNotEmpty) {
    final firstId = kontrakan.first.id;
    final detail = await kontrakanService.getKontrakanById(firstId);
    print('First kontrakan: ${detail?.nama}');
  }

  print('API tests completed!');
}
```

---

## Checklist Sebelum Deploy

- [ ] Update `AppConfig.baseUrl` dengan IP server yang benar
- [ ] Pastikan backend API sudah running (`php artisan serve`)
- [ ] Test semua API endpoints
- [ ] Implement error handling di semua screens
- [ ] Add loading indicators saat fetch data
- [ ] Test dengan real device/emulator
- [ ] Verify CORS headers (sudah configured di backend)
- [ ] Test file uploads (payment proof)
- [ ] Test authentication flow
- [ ] Test favorites dan reviews

---

## Troubleshooting

### "Connection refused"
```
Problem: API tidak bisa terhubung
Solution: 
1. Pastikan backend running (php artisan serve)
2. Check baseUrl di AppConfig (harus sesuai IP)
3. Firewall settings - pastikan port 8000 terbuka
```

### "Unauthorized 401"
```
Problem: Token invalid atau expired
Solution:
1. Login lagi untuk mendapatkan token baru
2. Pastikan loadToken() dipanggil saat startup
3. Check token expiry time di backend
```

### "File not found"
```
Problem: Upload file gagal
Solution:
1. Pastikan file path benar
2. Check file permissions
3. Gunakan image_picker package untuk select file
```

### "CORS error"
```
Problem: Cross-origin request blocked
Solution:
Backend sudah configured CORS, tapi jika masih error:
1. Check konfigurasi di backend (config/cors.php)
2. Pastikan credentials disettings di request
```

---

## Useful Packages

Sudah ada di pubspec.yaml:
- `http` - HTTP requests
- `shared_preferences` - Local storage for token
- `image_picker` - Pick images dari gallery/camera
- `file` - File handling

Tambahan yang di-recommend:
```yaml
dependencies:
  dio: ^5.3.0  # Alternative HTTP client (lebih powerful)
  provider: ^6.0.0  # State management
  riverpod: ^2.4.0  # Better state management
  connectivity_plus: ^5.0.0  # Check internet
```

---

## API Response Examples

### Login Response (Success)
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "123456|abcdef...",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@email.com",
      "role": "mahasiswa"
    }
  }
}
```

### Get Kontrakan Response (Success)
```json
{
  "success": true,
  "message": "Data retrieved",
  "data": {
    "data": [
      {
        "id": 1,
        "nama": "Kontrakan Jaya",
        "harga_bulanan": 1000000,
        "deskripsi": "Nyaman dan strategis",
        "alamat": "Jalan ABC No 123"
      }
    ]
  }
}
```

### Error Response (Failed)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["Email sudah terdaftar"],
    "password": ["Password minimal 8 karakter"]
  }
}
```

---

Sudah siap menggunakan API! Jika ada pertanyaan, check `API_INTEGRATION_GUIDE.md` untuk dokumentasi lebih lengkap.
