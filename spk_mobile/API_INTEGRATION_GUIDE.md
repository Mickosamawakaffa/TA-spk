# API Integration Guide - SPK Mobile

Dokumentasi lengkap tentang cara mengintegrasikan Flutter app dengan Laravel API backend.

## API Server Configuration

### Base URL Configuration
File: `lib/config/app_config.dart`

```dart
class AppConfig {
  // Base URL sesuai environment
  static const String baseUrl = 'http://192.168.18.16:8000/api';
  static const String storageUrl = 'http://192.168.18.16:8000/storage';
  
  // Timeout configuration
  static const Duration connectionTimeout = Duration(seconds: 10);
  static const Duration receiveTimeout = Duration(seconds: 10);
}
```

**Update URLs sesuai perangkat Anda:**
- **Windows Desktop/Android Emulator**: `http://10.0.2.2:8000/api`
- **iOS Simulator**: `http://localhost:8000/api`
- **Real Device**: `http://<YOUR_IP>:8000/api` (cek dengan `ipconfig`)

---

## Available API Services

### 1. Authentication Service
File: `lib/services/auth_service.dart`

**Methods:**
```dart
// Login
Future<Map<String, dynamic>> login(String email, String password)

// Register
Future<Map<String, dynamic>> register({
  required String name,
  required String email,
  required String password,
  required String passwordConfirmation,
  required String role,
})

// Logout
Future<void> logout()

// Update Profile
Future<Map<String, dynamic>> updateProfile({
  required String name,
  String? phone,
  String? address,
  String? profilePhoto,
})

// Load stored token
Future<void> loadToken()

// Get current user
User? get currentUser

// Check authentication status
bool get isAuthenticated
```

**Usage Example:**
```dart
final authService = AuthService();

// Login
final result = await authService.login('user@email.com', 'password');
if (result['success']) {
  // User logged in successfully
  print('Token: ${authService.token}');
}

// Logout
await authService.logout();
```

---

### 2. Kontrakan Service
File: `lib/services/kontrakan_service.dart`

**Methods:**
```dart
// Get all kontrakan with filters
Future<List<Kontrakan>> getKontrakan({
  String? search,
  double? hargaMin,
  double? hargaMax,
  int? jumlahKamar,
  String status = 'tersedia',
})

// Get kontrakan by ID
Future<Kontrakan?> getKontrakanById(int id)

// Get kontrakan gallery images
Future<List<Map<String, dynamic>>> getGaleri(int kontrakanId)

// Get kontrakan reviews
Future<List<Map<String, dynamic>>> getReviews(int kontrakanId)

// Get SAW recommendations
Future<Map<String, dynamic>> getRecommendations({
  double? hargaMin,
  double? hargaMax,
  int? jumlahKamar,
  double? jarakMax,
  String? fasilitas,
})
```

**Usage Example:**
```dart
final kontrakanService = KontrakanService();

// Get all kontrakan
final list = await kontrakanService.getKontrakan(
  hargaMax: 1000000,
  jumlahKamar: 3,
);

// Get specific kontrakan detail
final detail = await kontrakanService.getKontrakanById(1);
print(detail?.nama);

// Get recommendations (SAW algorithm)
final recommendations = await kontrakanService.getRecommendations(
  hargaMax: 1500000,
  jarakMax: 5.0,
);
```

---

### 3. Booking Service
File: `lib/services/booking_service.dart`

**Methods:**
```dart
// Get user's booking history
Future<List<Booking>> getBookingHistory()

// Get specific booking detail
Future<Booking?> getBookingById(int id)

// Create new booking with payment proof
Future<Map<String, dynamic>> createBooking({
  required int kontrakanId,
  required DateTime tanggalMulai,
  required int durasiBulan,
  String? catatan,
  File? paymentProof,
})

// Cancel booking
Future<Map<String, dynamic>> cancelBooking(int bookingId)

// Extend booking duration
Future<Map<String, dynamic>> extendBooking({
  required int bookingId,
  required int durationMonths,
  File? paymentProof,
})

// Upload payment proof for existing booking
Future<Map<String, dynamic>> uploadPaymentProof(
  int bookingId,
  File imageFile,
)
```

**Usage Example:**
```dart
final bookingService = BookingService();

// Get booking history
final bookings = await bookingService.getBookingHistory();

// Create booking with payment proof
final result = await bookingService.createBooking(
  kontrakanId: 1,
  tanggalMulai: DateTime.now(),
  durasiBulan: 3,
  catatan: 'Butuh AC dan wifi',
  paymentProof: File('/path/to/proof.jpg'),
);

if (result['success']) {
  print('Booking created: ${result['booking'].id}');
}

// Cancel booking
await bookingService.cancelBooking(1);

// Extend booking
await bookingService.extendBooking(
  bookingId: 1,
  durationMonths: 2,
  paymentProof: File('/path/to/proof.jpg'),
);
```

---

### 4. Laundry Service
File: `lib/services/laundry_service.dart`

**Methods:**
```dart
// Get all laundry services
Future<List<Laundry>> getLaundry({
  String? search,
  double? hargaMin,
  double? hargaMax,
  String status = 'aktif',
})

// Get laundry by ID
Future<Laundry?> getLaundryById(int id)

// Get laundry gallery
Future<List<Map<String, dynamic>>> getGaleri(int laundryId)

// Get laundry reviews
Future<List<Map<String, dynamic>>> getReviews(int laundryId)

// Get SAW recommendations for laundry
Future<Map<String, dynamic>> getRecommendations({
  double? hargaMin,
  double? hargaMax,
  double? jarakMax,
})
```

---

### 5. Review Service
File: `lib/services/review_service.dart`

**Methods:**
```dart
// Add review for kontrakan
Future<Map<String, dynamic>> addKontrakanReview({
  required int kontrakanId,
  required double rating,
  required String comment,
})

// Add review for laundry
Future<Map<String, dynamic>> addLaundryReview({
  required int laundryId,
  required double rating,
  required String comment,
})

// Update review
Future<Map<String, dynamic>> updateReview({
  required int reviewId,
  required double rating,
  required String comment,
})

// Delete review
Future<Map<String, dynamic>> deleteReview(int reviewId)
```

---

### 6. Favorite Service
File: `lib/services/favorite_service.dart`

**Methods:**
```dart
// Get user's favorite kontrakan
Future<List<int>> getFavoriteKontrakan()

// Get user's favorite laundry
Future<List<int>> getFavoriteLaundry()

// Toggle kontrakan favorite status
Future<Map<String, dynamic>> toggleKontrakanFavorite(int kontrakanId)

// Toggle laundry favorite status
Future<Map<String, dynamic>> toggleLaundryFavorite(int laundryId)

// Remove from favorites
Future<Map<String, dynamic>> removeFavorite(int favoriteId)
```

---

## API Endpoints Summary

### Authentication (No Auth Required)
```
POST /api/register              - Register user
POST /api/login                 - Login user
```

### Kontrakan (Public)
```
GET  /api/kontrakan             - Get all kontrakan (with filters)
GET  /api/kontrakan/{id}        - Get kontrakan detail
GET  /api/kontrakan/{id}/galeri - Get kontrakan gallery
GET  /api/kontrakan/{id}/reviews - Get kontrakan reviews
```

### Laundry (Public)
```
GET  /api/laundry               - Get all laundry services
GET  /api/laundry/{id}          - Get laundry detail
GET  /api/laundry/{id}/galeri   - Get laundry gallery
GET  /api/laundry/{id}/reviews  - Get laundry reviews
```

### SAW Calculation (Public)
```
GET  /api/saw/kriteria/kontrakan        - Get kontrakan criteria
POST /api/saw/calculate/kontrakan       - Calculate kontrakan recommendation
GET  /api/saw/kriteria/laundry          - Get laundry criteria
POST /api/saw/calculate/laundry         - Calculate laundry recommendation
```

### Protected Routes (Requires Auth Token)
```
GET  /api/user                          - Get current user profile
POST /api/logout                        - Logout user
PUT  /api/profile/update                - Update user profile

GET  /api/bookings                      - Get user's bookings
GET  /api/bookings/{id}                 - Get booking detail
POST /api/bookings                      - Create booking
POST /api/bookings/{id}/cancel          - Cancel booking
POST /api/bookings/{id}/extend          - Extend booking
POST /api/bookings/{id}/payment-proof   - Upload payment proof

POST /api/reviews/kontrakan/{id}        - Add kontrakan review
POST /api/reviews/laundry/{id}          - Add laundry review
PUT  /api/reviews/{id}                  - Update review
DELETE /api/reviews/{id}                - Delete review

GET  /api/favorites                     - Get user's favorites
POST /api/favorites/kontrakan/{id}      - Toggle kontrakan favorite
POST /api/favorites/laundry/{id}        - Toggle laundry favorite
DELETE /api/favorites/{id}              - Remove favorite
```

---

## Error Handling

All services follow this response format:

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {...}
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "errors": {...}
}
```

### Handling Errors in Code
```dart
try {
  final result = await bookingService.createBooking(...);
  
  if (result['success']) {
    // Handle success
    print(result['message']);
  } else {
    // Handle error
    print(result['message']);
    print(result['errors']);
  }
} catch (e) {
  // Handle exception
  print('Exception: $e');
}
```

---

## Authentication Token Management

Tokens are automatically managed by `AuthService`:

```dart
// Token is automatically saved after login
await authService.login(email, password);

// Token is automatically loaded on app startup
await authService.loadToken();

// Token is automatically included in all requests
// (handled by _headers getter in each service)

// Token is automatically cleared on logout
await authService.logout();
```

---

## Testing the API Integration

### 1. Test Login
```dart
final authService = AuthService();
final result = await authService.login('user@email.com', 'password');
print(result);
```

### 2. Test Getting Data
```dart
final kontrakanService = KontrakanService();
final kontrakan = await kontrakanService.getKontrakan();
print('Found ${kontrakan.length} kontrakan');
```

### 3. Test Creating Booking
```dart
final bookingService = BookingService();
final result = await bookingService.createBooking(
  kontrakanId: 1,
  tanggalMulai: DateTime.now(),
  durasiBulan: 3,
  paymentProof: File('/path/to/image.jpg'),
);
print(result);
```

---

## Important Notes

1. **Always call `await authService.loadToken()`** on app startup to restore session
2. **Payment proof is required** for creating bookings (must be image file)
3. **Update `AppConfig.baseUrl`** based on your environment
4. **Use token authentication** for protected routes (automatically handled)
5. **Handle errors gracefully** and show meaningful messages to users
6. **Store images in proper location** before uploading (usually from image picker)

---

## Common Issues & Solutions

### Issue: "Connection refused"
**Solution:** Check `AppConfig.baseUrl` matches your server IP/port

### Issue: "Unauthorized" error
**Solution:** Ensure token is loaded before making authenticated requests

### Issue: "File not found" for uploads
**Solution:** Verify file path exists before uploading

### Issue: CORS errors
**Solution:** Backend already has CORS configured for mobile apps

---

## Next Steps

1. Implement screens that use these services
2. Add state management (Riverpod/Provider)
3. Add error handling and validation
4. Test all endpoints with real data
5. Implement offline support if needed
