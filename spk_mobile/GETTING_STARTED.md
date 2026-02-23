# 🚀 Getting Started - API Integration Ready!

**Backend API server sudah berjalan di `http://127.0.0.1:8000`**

Semua API services sudah siap. Inilah cara memulai menggunakan API di Flutter app Anda.

---

## ⚡ Quick Start (3 Langkah)

### Step 1: Update Base URL (PENTING!)

**File**: `lib/config/app_config.dart`

Cari line ini:
```dart
static const String baseUrl = 'http://192.168.18.16:8000/api';
```

**Ganti IP `192.168.18.16` dengan IP komputer Anda:**

Cek IP komputer:
- **Windows**: Buka Command Prompt, ketik `ipconfig`, cari "IPv4 Address"
- **MacOS/Linux**: Terminal, ketik `ifconfig`

Contoh hasil:
```
IPv4 Address          : 192.168.1.10
```

Jadi ubah menjadi:
```dart
static const String baseUrl = 'http://192.168.1.10:8000/api';
```

### Step 2: Load Token di Main

**File**: `lib/main.dart`

Tambahkan ini di `main()`:

```dart
import 'package:spk_mobile/services/auth_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Load saved token jika ada
  final authService = AuthService();
  await authService.loadToken();
  
  runApp(const MyApp());
}
```

### Step 3: Test API Connection

Ada 2 cara:

**Cara 1: Otomatis (Add to main.dart)**
```dart
import 'package:spk_mobile/test/api_test_helper.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  final authService = AuthService();
  await authService.loadToken();
  
  // Test API
  await APITestHelper.testGetKontrakan();
  
  runApp(const MyApp());
}
```

**Cara 2: Manual (Call method when needed)**
```dart
// Di button atau dialog
final helper = APITestHelper();
await helper.testGetKontrakan();
```

---

## 📚 Documentation Files

Setelah selesai dengan 3 langkah di atas, baca dokumentasi sesuai kebutuhan:

| File | Untuk Apa | Baca Kapan |
|------|-----------|-----------|
| [README_API_INTEGRATION.md](README_API_INTEGRATION.md) | Overview lengkap | Pertama kali |
| [API_QUICKSTART.md](API_QUICKSTART.md) | Contoh kode | Saat implementasi |
| [API_INTEGRATION_GUIDE.md](API_INTEGRATION_GUIDE.md) | Referensi length | Saat butuh detail |
| [SCREEN_IMPLEMENTATION_CHECKLIST.md](SCREEN_IMPLEMENTATION_CHECKLIST.md) | Guide per screen | Saat build screen |
| [API_STATUS.md](API_STATUS.md) | Status & checklist | QA & submission |

---

## 🎯 Langkah Berikutnya

### 1. Verify Setup Bekerja
```dart
// Run di app Anda
await APITestHelper.testGetKontrakan();
// Seharusnya melihat: "✓ Found X kontrakan"
```

### 2. Mulai Build Screens

Pilih salah satu screen dan follow guide:

**Option A: Start with Login (Recommended)**
- Buka `SCREEN_IMPLEMENTATION_CHECKLIST.md`
- Scroll ke "1️⃣ Login Screen"
- Copy dan implement code

**Option B: Start with Kontrakan List**
- Buka `SCREEN_IMPLEMENTATION_CHECKLIST.md`
- Scroll ke "3️⃣ Kontrakan List Screen"
- Copy dan implement code

**Option C: Quick Example**
- Buka `API_QUICKSTART.md`
- Lihat "Common API Usage Examples"
- Copy contoh kode

### 3. Implement Services

Setiap screen butuh service(s):

```dart
import 'package:spk_mobile/services/xxx_service.dart';

class MyScreen extends StatefulWidget {
  @override
  State<MyScreen> createState() => _MyScreenState();
}

class _MyScreenState extends State<MyScreen> {
  // Initialize service
  final _service = XxxService();
  
  // Data, loading, error states
  List<Data> _data = [];
  bool _isLoading = true;
  String? _error;
  
  @override
  void initState() {
    super.initState();
    _loadData();
  }
  
  void _loadData() async {
    try {
      setState(() {
        _isLoading = true;
        _error = null;
      });
      
      // Call API
      _data = await _service.getData();
      
      setState(() => _isLoading = false);
    } catch (e) {
      setState(() {
        _error = 'Error: $e';
        _isLoading = false;
      });
    }
  }
  
  @override
  Widget build(BuildContext context) {
    // Build UI dengan 3 states: loading, error, data
    if (_isLoading) return CircularProgressIndicator();
    if (_error != null) return Text(_error!);
    if (_data.isEmpty) return Text('No data');
    
    return ListView.builder(
      itemCount: _data.length,
      itemBuilder: (context, index) {
        return ListTile(title: Text(_data[index].toString()));
      },
    );
  }
}
```

---

## 💡 Available Services

### 1. AuthService
```dart
import 'package:spk_mobile/services/auth_service.dart';

final auth = AuthService();

// Login
final result = await auth.login('email@example.com', 'password');

// Register
await auth.register(
  name: 'John',
  email: 'john@example.com',
  password: 'password',
  passwordConfirmation: 'password',
  role: 'mahasiswa',
);

// Update profile
await auth.updateProfile(
  name: 'John Doe',
  phone: '081234567890',
);

// Logout
await auth.logout();

// Check auth status
print(auth.isAuthenticated);
print(auth.currentUser?.name);
```

### 2. KontrakanService
```dart
import 'package:spk_mobile/services/kontrakan_service.dart';

final service = KontrakanService();

// Get list
final list = await service.getKontrakan(
  search: 'nyaman',
  hargaMax: 1000000,
  jumlahKamar: 2,
);

// Get detail
final detail = await service.getKontrakanById(1);

// Get recommendations
final recommendations = await service.getRecommendations(
  hargaMax: 1500000,
);
```

### 3. BookingService
```dart
import 'package:spk_mobile/services/booking_service.dart';

final service = BookingService();

// Get history
final bookings = await service.getBookingHistory();

// Create booking
final result = await service.createBooking(
  kontrakanId: 1,
  tanggalMulai: DateTime.now(),
  durasiBulan: 3,
  paymentProof: File('/path/to/image.jpg'),
);

// Cancel
await service.cancelBooking(1);
```

### 4. ReviewService (NEW)
```dart
import 'package:spk_mobile/services/review_service.dart';

final service = ReviewService();

// Add review
await service.addKontrakanReview(
  kontrakanId: 1,
  rating: 4.5,
  comment: 'Bagus dan nyaman!',
);

// Update
await service.updateReview(
  reviewId: 1,
  rating: 5.0,
  comment: 'Updated comment',
);

// Delete
await service.deleteReview(1);
```

### 5. FavoriteService (NEW)
```dart
import 'package:spk_mobile/services/favorite_service.dart';

final service = FavoriteService();

// Toggle favorite
final result = await service.toggleKontrakanFavorite(1);
print('Is favorite: ${result['isFavorite']}');

// Check if favorite
final isFav = await service.isKontrakanFavorite(1);

// Get all favorites
final favs = await service.getFavorites();
print('Kontrakan favorites: ${favs['kontrakan']}');
```

### 6. LaundryService
```dart
import 'package:spk_mobile/services/laundry_service.dart';

final service = LaundryService();

// Get list
final list = await service.getLaundry(
  search: 'express',
  hargaMax: 10000,
);

// Get detail
final detail = await service.getLaundryById(1);
```

---

## ✅ Checklist Before Building

- [ ] Backend running? (`php artisan serve`)
- [ ] Base URL updated? (Check IP)
- [ ] Token loading added? (In main.dart)
- [ ] Test API working? (APITestHelper)
- [ ] Read docs? (Start with README_API_INTEGRATION.md)
- [ ] Ready to code? (Pick a screen, follow checklist)

---

## 🎯 Recommended Screen Implementation Order

1. **Login Screen** ← Start here (simplest)
2. **Kontrakan List** (use public API, no auth)
3. **Kontrakan Detail** (expand list functionality)
4. **Booking** (user interaction, file upload)
5. **Booking History** (auth required)
6. **Other Screens** (in any order)

---

## 🚨 Common Issues & Quick Fixes

### "Connection Refused"
```
Problem: API tidak bisa connect
Fix:
1. Check backend running: php artisan serve
2. Verify base URL is correct (check IP with ipconfig)
3. Firewall allow port 8000
```

### "Unauthorized 401"
```
Problem: Token invalid
Fix:
1. Login lagi
2. Check loadToken() called in main.dart
```

### "Method not found"
```
Problem: Service method tidak ada
Fix:
Check documentation di API_INTEGRATION_GUIDE.md
atau lihat list method di atas
```

### "Image upload fails"
```
Problem: File tidak upload
Fix:
1. Pastikan file path benar
2. Use image_picker package
3. Check file permissions
```

---

## 📊 Test Matrix

Sebelum push code, test:

| Feature | Test | Status |
|---------|------|--------|
| Login | Try login with correct credentials | [ ] |
| Register | Try register new user | [ ] |
| Browse Kontrakan | Load list, test filter, search | [ ] |
| View Detail | Open kontrakan detail | [ ] |
| Create Booking | Create booking dengan image | [ ] |
| Add Review | Add review dengan rating | [ ] |
| Favorite | Toggle favorite items | [ ] |
| Navigation | All buttons navigate correctly | [ ] |
| Error Handling | Test offline, invalid data | [ ] |
| Loading States | Show spinner while loading | [ ] |

---

## 🎓 Learning Resources in Project

### Code Examples
- `API_QUICKSTART.md` - Has 15+ code examples
- `SCREEN_IMPLEMENTATION_CHECKLIST.md` - Has template code for each screen
- `test/api_test_helper.dart` - Real usage examples

### API Reference
- `API_INTEGRATION_GUIDE.md` - Complete endpoint documentation
- `API_STATUS.md` - All endpoints listed

### Implementation Guides
- `SCREEN_IMPLEMENTATION_CHECKLIST.md` - Step by step per screen
- `README_API_INTEGRATION.md` - Overview and navigation

---

## 💻 Example: First Screen (Login)

```dart
import 'package:flutter/material.dart';
import 'package:spk_mobile/services/auth_service.dart';

class LoginScreen extends StatefulWidget {
  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _emailCtrl = TextEditingController();
  final _passCtrl = TextEditingController();
  final _auth = AuthService();
  bool _loading = false;
  String? _error;

  void _login() async {
    setState(() {
      _loading = true;
      _error = null;
    });

    final result = await _auth.login(
      _emailCtrl.text,
      _passCtrl.text,
    );

    setState(() => _loading = false);

    if (result['success']) {
      // Success - navigate
      Navigator.of(context).pushReplacementNamed('/home');
    } else {
      // Error
      setState(() => _error = result['message']);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'])),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Login')),
      body: SingleChildScrollView(
        padding: EdgeInsets.all(16),
        child: Column(
          children: [
            TextField(
              controller: _emailCtrl,
              decoration: InputDecoration(labelText: 'Email'),
            ),
            SizedBox(height: 16),
            TextField(
              controller: _passCtrl,
              decoration: InputDecoration(labelText: 'Password'),
              obscureText: true,
            ),
            SizedBox(height: 24),
            _loading
                ? CircularProgressIndicator()
                : ElevatedButton(
                    onPressed: _login,
                    child: Text('Login'),
                  ),
            if (_error != null) ...[
              SizedBox(height: 16),
              Text(_error!, style: TextStyle(color: Colors.red)),
            ],
          ],
        ),
      ),
    );
  }

  @override
  void dispose() {
    _emailCtrl.dispose();
    _passCtrl.dispose();
    super.dispose();
  }
}
```

Copy code di atas, adjust sesuai UI design Anda!

---

## 🎉 You're Ready!

**Sekarang Anda siap untuk:**
1. Update base URL ✓
2. Load token ✓
3. Test API ✓
4. Build screens with services ✓

---

## 📞 Need Help?

Dokumentasi tersedia:
- `README_API_INTEGRATION.md` - Overview
- `API_QUICKSTART.md` - Contoh kode
- `SCREEN_IMPLEMENTATION_CHECKLIST.md` - Detail per screen
- `API_INTEGRATION_GUIDE.md` - Referensi lengkap

---

**Happy Coding! 🚀**

*Last Updated: February 23, 2026*
*Status: Ready for Development*
