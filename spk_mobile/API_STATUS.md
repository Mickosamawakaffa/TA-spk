# API Integration Status & Checklist

Status: ✅ FULLY INTEGRATED - Semua API services sudah siap digunakan!

---

## 📦 Available API Services

### ✅ 1. Authentication Service
**File:** `lib/services/auth_service.dart`

Features:
- ✅ User login
- ✅ User registration
- ✅ Auto token management
- ✅ Profile update
- ✅ Logout
- ✅ Persistent token storage

**Key Methods:**
```dart
login(email, password)
register({name, email, password, role})
updateProfile({name, phone, address})
logout()
loadToken()  // Call on app startup!
```

**Status:** Ready to use ✓

---

### ✅ 2. Kontrakan Service
**File:** `lib/services/kontrakan_service.dart`

Features:
- ✅ Get all kontrakan with pagination
- ✅ Get kontrakan by ID
- ✅ Filter by: price range, number of rooms, search keyword
- ✅ Get gallery images
- ✅ Get reviews
- ✅ SAW recommendations (ranking algorithm)

**Key Methods:**
```dart
getKontrakan({search, hargaMin, hargaMax, jumlahKamar})
getKontrakanById(id)
getGaleri(id)
getReviews(id)
getRecommendations({hargaMin, hargaMax, jarakMax})
```

**Status:** Ready to use ✓

---

### ✅ 3. Booking Service
**File:** `lib/services/booking_service.dart`

Features:
- ✅ Create booking with payment proof upload
- ✅ Get user's booking history
- ✅ Get booking detail
- ✅ Cancel booking
- ✅ Extend booking duration
- ✅ Upload payment proof

**Key Methods:**
```dart
getBookingHistory()
getBookingById(id)
createBooking({kontrakanId, tanggalMulai, durasiBulan, paymentProof})
cancelBooking(id)
extendBooking({id, durationMonths, paymentProof})
uploadPaymentProof(id, file)
```

**Status:** Ready to use ✓

---

### ✅ 4. Laundry Service
**File:** `lib/services/laundry_service.dart`

Features:
- ✅ Get all laundry services
- ✅ Get laundry by ID
- ✅ Filter by price and search
- ✅ Get gallery images
- ✅ Get reviews
- ✅ SAW recommendations

**Key Methods:**
```dart
getLaundry({search, hargaMin, hargaMax})
getLaundryById(id)
getGaleri(id)
getReviews(id)
getRecommendations({hargaMin, hargaMax, jarakMax})
```

**Status:** Ready to use ✓

---

### ✅ 5. Review Service
**File:** `lib/services/review_service.dart`

Features:
- ✅ Add kontrakan review
- ✅ Add laundry review
- ✅ Update review
- ✅ Delete review
- ✅ Rating and comment

**Key Methods:**
```dart
addKontrakanReview({kontrakanId, rating, comment})
addLaundryReview({laundryId, rating, comment})
updateReview({reviewId, rating, comment})
deleteReview(id)
```

**Authorization:** Requires login ✓

**Status:** Ready to use ✓

---

### ✅ 6. Favorite Service
**File:** `lib/services/favorite_service.dart`

Features:
- ✅ Get user's favorites
- ✅ Toggle kontrakan favorite
- ✅ Toggle laundry favorite
- ✅ Remove from favorites
- ✅ Check if item is favorite

**Key Methods:**
```dart
getFavorites()
toggleKontrakanFavorite(id)
toggleLaundryFavorite(id)
removeFavorite(id)
isKontrakanFavorite(id)
isLaundryFavorite(id)
```

**Authorization:** Requires login ✓

**Status:** Ready to use ✓

---

## 🎯 Backend API Endpoints Status

### Public Endpoints (No Auth)
```
✅ POST   /api/register              - User registration
✅ POST   /api/login                 - User login
✅ GET    /api/kontrakan             - Get kontrakan list
✅ GET    /api/kontrakan/{id}        - Get kontrakan detail
✅ GET    /api/kontrakan/{id}/galeri - Get kontrakan gallery
✅ GET    /api/kontrakan/{id}/reviews - Get kontrakan reviews
✅ GET    /api/laundry               - Get laundry list
✅ GET    /api/laundry/{id}          - Get laundry detail
✅ GET    /api/laundry/{id}/galeri   - Get laundry gallery
✅ GET    /api/laundry/{id}/reviews  - Get laundry reviews
✅ GET    /api/saw/kriteria/kontrakan - Get SAW criteria
✅ POST   /api/saw/calculate/kontrakan - Calculate recommendations
✅ GET    /api/saw/kriteria/laundry  - Get laundry criteria
✅ POST   /api/saw/calculate/laundry - Calculate recommendations
```

### Protected Endpoints (Requires Auth)
```
✅ GET    /api/user                        - Get current user
✅ POST   /api/logout                      - Logout
✅ PUT    /api/profile/update              - Update profile
✅ GET    /api/bookings                    - Get bookings
✅ GET    /api/bookings/{id}               - Get booking detail
✅ POST   /api/bookings                    - Create booking
✅ POST   /api/bookings/{id}/cancel        - Cancel booking
✅ POST   /api/bookings/{id}/extend        - Extend booking
✅ POST   /api/bookings/{id}/payment-proof - Upload payment proof
✅ POST   /api/reviews/kontrakan/{id}      - Add kontrakan review
✅ POST   /api/reviews/laundry/{id}        - Add laundry review
✅ PUT    /api/reviews/{id}                - Update review
✅ DELETE /api/reviews/{id}                - Delete review
✅ GET    /api/favorites                   - Get favorites
✅ POST   /api/favorites/kontrakan/{id}    - Toggle kontrakan favorite
✅ POST   /api/favorites/laundry/{id}      - Toggle laundry favorite
✅ DELETE /api/favorites/{id}              - Delete favorite
```

---

## 📋 Implementation Checklist

### Setup & Configuration
- [ ] Update `lib/config/app_config.dart` with correct base URL
- [ ] Add `await authService.loadToken()` in main.dart
- [ ] Ensure backend API is running (`php artisan serve`)
- [ ] Test API connection with test helper

### Screen Implementation Guide
- [ ] Login/Register Screen -> Use `AuthService`
- [ ] Kontrakan List Screen -> Use `KontrakanService.getKontrakan()`
- [ ] Kontrakan Detail Screen -> Use `KontrakanService.getKontrakanById()`
- [ ] Booking Screen -> Use `BookingService.createBooking()`
- [ ] Booking History Screen -> Use `BookingService.getBookingHistory()`
- [ ] Review Screen -> Use `ReviewService`
- [ ] Favorites Screen -> Use `FavoriteService`
- [ ] Laundry List Screen -> Use `LaundryService`
- [ ] Recommendations Screen -> Use `KontrakanService.getRecommendations()`

### Error Handling
- [ ] Handle network errors
- [ ] Handle 401 Unauthorized (redirect to login)
- [ ] Handle validation errors
- [ ] Show user-friendly error messages
- [ ] Implement retry mechanism

### UI/UX Features
- [ ] Add loading indicators while fetching
- [ ] Add empty state when no data
- [ ] Add error state with retry button
- [ ] Implement pagination/infinite scroll
- [ ] Add image caching for gallery

### Testing
- [ ] Test login/logout flow
- [ ] Test kontrakan listing and filtering
- [ ] Test booking creation with payment upload
- [ ] Test favorite toggle
- [ ] Test review submission
- [ ] Test offline handling
- [ ] Test on real device

### Optimization
- [ ] Implement state management (Provider/Riverpod)
- [ ] Add local caching for frequently accessed data
- [ ] Optimize image loading
- [ ] Implement pagination for large lists
- [ ] Add connectivity check before API calls

---

## 🚀 Quick Start Steps

### 1. Immediate Tasks
```
1. Check if backend is running
   Run: cd c:\laragon\www\TA\spk_kontrakan && php artisan serve

2. Update base URL in app_config.dart
   Check your IP: ipconfig (Windows)
   Update: static const String baseUrl = 'http://<YOUR_IP>:8000/api';

3. Test API connection
   Add this to main.dart:
   await authService.loadToken();

4. Start building screens using services
```

### 2. Example Screen Structure
```dart
import 'package:spk_mobile/services/kontrakan_service.dart';

class ExampleScreen extends StatefulWidget {
  @override
  State<ExampleScreen> createState() => _ExampleScreenState();
}

class _ExampleScreenState extends State<ExampleScreen> {
  final _service = KontrakanService();
  bool _isLoading = true;
  List<Kontrakan> _data = [];
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  void _loadData() async {
    try {
      setState(() => _isLoading = true);
      _data = await _service.getKontrakan();
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
    if (_isLoading) return CircularProgressIndicator();
    if (_error != null) return Text(_error!);
    if (_data.isEmpty) return Text('No data');
    
    return ListView.builder(
      itemCount: _data.length,
      itemBuilder: (context, index) {
        return ListTile(
          title: Text(_data[index].nama),
          subtitle: Text('Rp ${_data[index].hargaBulanan}'),
        );
      },
    );
  }
}
```

### 3. Test API Integration
```dart
// Add to main.dart for testing
void testAPIs() async {
  print('Testing APIs...');
  await APITestHelper.runAllTests(
    testEmail: 'test@example.com',
    testPassword: 'password123',
  );
}
```

---

## 📚 Documentation Files

- `API_INTEGRATION_GUIDE.md` - Full API reference
- `API_QUICKSTART.md` - Quick examples and patterns
- `test/api_test_helper.dart` - Test utility class
- `API_STATUS.md` - This file

---

## ⚠️ Important Notes

1. **Always Load Token on Startup**
   ```dart
   void main() async {
     WidgetsFlutterBinding.ensureInitialized();
     final authService = AuthService();
     await authService.loadToken();
     runApp(const MyApp());
   }
   ```

2. **Update API Base URL**
   - Check your system IP
   - Update in `lib/config/app_config.dart`
   - Different for emulator/simulator/real device

3. **File Uploads**
   - Payment proof must be image file
   - Use `image_picker` package for file selection
   - Ensure file exists before uploading

4. **Authentication**
   - Token automatically saved after login
   - Token automatically included in requests
   - Token automatically cleared on logout

5. **Error Handling**
   - Always check `result['success']` first
   - Get error message from `result['message']`
   - Get validation errors from `result['errors']`

---

## 🔧 Troubleshooting

### "Connection Refused"
```
→ Backend not running
→ Fix: php artisan serve
→ Check firewall port 8000
```

### "Unauthorized 401"
```
→ Token invalid/expired
→ Fix: Login again
→ Check loadToken() called on startup
```

### "CORS Error"
```
→ Backend already configured CORS
→ If still happening, check config/cors.php
```

### "Image Upload Failed"
```
→ File path doesn't exist
→ Use image_picker to select file
→ Check file permissions
```

---

## 📞 Support

For issues or questions:
1. Check `API_INTEGRATION_GUIDE.md` for detailed docs
2. Check `API_QUICKSTART.md` for examples
3. Test endpoints with `APITestHelper`
4. Verify backend is running
5. Check base URL configuration

---

## ✅ Final Checklist

- [ ] Backend API is running on port 8000
- [ ] Base URL updated with correct IP
- [ ] All services imported in widgets
- [ ] Error handling implemented
- [ ] Loading states added
- [ ] Empty states handled
- [ ] Token loaded on app startup
- [ ] Tested all main features
- [ ] Ready for production build

---

**Status: READY FOR DEVELOPMENT** ✅

All API services are fully integrated and ready to use. Start building your screens!
