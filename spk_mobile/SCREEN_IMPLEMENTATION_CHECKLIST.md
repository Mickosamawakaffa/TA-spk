# Screen Implementation Checklist

Panduan lengkap untuk mengimplementasikan atau update setiap screen dengan API integration.

---

## 1️⃣ Login Screen

**File:** `lib/screens/login.dart` (atau nama yang sesuai)

**Required Services:**
- `AuthService`

**Implementation Checklist:**

```dart
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
  String? _error;

  void _handleLogin() async {
    // ✓ Validate input
    if (_emailController.text.isEmpty || _passwordController.text.isEmpty) {
      setState(() => _error = 'Email dan password harus diisi');
      return;
    }

    // ✓ Show loading
    setState(() {
      _isLoading = true;
      _error = null;
    });

    // ✓ Call API
    final result = await _authService.login(
      _emailController.text,
      _passwordController.text,
    );

    // ✓ Hide loading
    setState(() => _isLoading = false);

    // ✓ Handle response
    if (result['success']) {
      // ✓ Navigate to home
      Navigator.of(context).pushReplacementNamed('/home');
    } else {
      // ✓ Show error
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
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            // ✓ Email field
            TextField(
              controller: _emailController,
              decoration: InputDecoration(
                labelText: 'Email',
                border: OutlineInputBorder(),
                errorText: _error?.contains('Email') ? _error : null,
              ),
              keyboardType: TextInputType.emailAddress,
            ),
            SizedBox(height: 16),

            // ✓ Password field
            TextField(
              controller: _passwordController,
              decoration: InputDecoration(
                labelText: 'Password',
                border: OutlineInputBorder(),
                errorText: _error?.contains('Password') ? _error : null,
              ),
              obscureText: true,
            ),
            SizedBox(height: 24),

            // ✓ Loading indicator or button
            _isLoading
                ? CircularProgressIndicator()
                : ElevatedButton(
                    onPressed: _handleLogin,
                    child: Text('Login'),
                  ),
            
            // ✓ Register link
            TextButton(
              onPressed: () => Navigator.of(context).pushNamed('/register'),
              child: Text('Belum punya akun? Daftar di sini'),
            ),
          ],
        ),
      ),
    );
  }

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }
}
```

**Methods to Implement:**
- [ ] `_handleLogin()` - Call `AuthService.login()`
- [ ] Input validation
- [ ] Loading indicator
- [ ] Error handling and display
- [ ] Navigate on success
- [ ] Login link to register

**Test Cases:**
- [ ] Invalid email format
- [ ] Empty password
- [ ] Wrong credentials
- [ ] Successful login
- [ ] Navigate to home after login

---

## 2️⃣ Register Screen

**File:** `lib/screens/register.dart`

**Required Services:**
- `AuthService`

**Implementation Checklist:**

```dart
// ✓ TextField for: name, email, password, confirm password
// ✓ Role selection (dropdown): mahasiswa, pemilik_kontrakan
// ✓ Input validation
// ✓ Password confirmation check
// ✓ Loading state during registration
// ✓ Show validation errors
// ✓ Navigate to login on success
// ✓ Display error message on failure
```

**Methods to Implement:**
- [ ] `_handleRegister()` - Call `AuthService.register()`
- [ ] Role selection handling
- [ ] Password confirmation validation
- [ ] Email format validation
- [ ] Error handling
- [ ] Success navigation

---

## 3️⃣ Kontrakan List Screen

**File:** `lib/screens/kontrakan_list.dart` (atau nama yang sesuai)

**Required Services:**
- `KontrakanService`
- `FavoriteService` (optional, untuk favorite button)

**Implementation Checklist:**

```dart
class KontrakanListScreen extends StatefulWidget {
  @override
  State<KontrakanListScreen> createState() => _KontrakanListScreenState();
}

class _KontrakanListScreenState extends State<KontrakanListScreen> {
  final _kontrakanService = KontrakanService();
  final _favoriteService = FavoriteService();
  
  List<Kontrakan> _data = [];
  bool _isLoading = true;
  String? _error;

  // ✓ Filter properties
  String? _searchQuery;
  double? _priceFilter;
  int? _roomFilter;

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

      // ✓ Call API with filters
      _data = await _kontrakanService.getKontrakan(
        search: _searchQuery,
        hargaMax: _priceFilter,
        jumlahKamar: _roomFilter,
      );

      setState(() => _isLoading = false);
    } catch (e) {
      setState(() {
        _error = 'Error: $e';
        _isLoading = false;
      });
    }
  }

  void _handleFavoriteToggle(int id) async {
    // ✓ Call toggle favorite API
    final result = await _favoriteService.toggleKontrakanFavorite(id);
    if (result['success']) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            result['isFavorite'] ? 'Ditambahkan ke favorit' : 'Dihapus dari favorit',
          ),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Kontrakan'),
        // ✓ Search bar
      ),
      body: Column(
        children: [
          // ✓ Filter widgets (search, price, rooms)
          Expanded(
            child: _buildContent(),
          ),
        ],
      ),
    );
  }

  Widget _buildContent() {
    // ✓ Show loading
    if (_isLoading) {
      return Center(child: CircularProgressIndicator());
    }

    // ✓ Show error
    if (_error != null) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(_error!),
            ElevatedButton(
              onPressed: _loadData,
              child: Text('Retry'),
            ),
          ],
        ),
      );
    }

    // ✓ Show empty state
    if (_data.isEmpty) {
      return Center(child: Text('Tidak ada kontrakan'));
    }

    // ✓ Show list
    return ListView.builder(
      itemCount: _data.length,
      itemBuilder: (context, index) {
        final item = _data[index];
        return ListTile(
          leading: item.fotoUtama != null
              ? Image.network(item.fotoUtama!)
              : Icon(Icons.image_not_supported),
          title: Text(item.nama),
          subtitle: Text('Rp ${item.hargaBulanan}'),
          trailing: IconButton(
            icon: Icon(Icons.favorite_border),
            onPressed: () => _handleFavoriteToggle(item.id),
          ),
          onTap: () {
            // ✓ Navigate to detail
            Navigator.of(context).pushNamed(
              '/kontrakan-detail',
              arguments: item.id,
            );
          },
        );
      },
    );
  }
}
```

**Methods to Implement:**
- [ ] `_loadData()` - Fetch kontrakan list
- [ ] Filter/search functionality
- [ ] Favorite toggle
- [ ] Loading, error, and empty states
- [ ] Navigate to detail on tap
- [ ] Image loading
- [ ] Retry on error

**Test Cases:**
- [ ] Load kontrakan list
- [ ] Filter by price
- [ ] Filter by rooms
- [ ] Search functionality
- [ ] Toggle favorite
- [ ] Empty state
- [ ] Error state with retry

---

## 4️⃣ Kontrakan Detail Screen

**File:** `lib/screens/kontrakan_detail.dart`

**Required Services:**
- `KontrakanService`
- `FavoriteService`
- `ReviewService`

**Implementation Checklist:**

```dart
// ✓ Get kontrakan ID from arguments
// ✓ Load kontrakan detail with getKontrakanById()
// ✓ Display: name, price, description, address, facilities
// ✓ Show gallery images
// ✓ Show reviews/ratings
// ✓ Toggle favorite button
// ✓ Book button that navigates to booking screen
// ✓ Button to add review
// ✓ Handle loading, error states
```

**Methods to Implement:**
- [ ] `_loadDetail()` - Fetch detail data
- [ ] `_loadReviews()` - Fetch reviews
- [ ] Image gallery display
- [ ] Favorite toggle
- [ ] Add review button
- [ ] Book button

---

## 5️⃣ Booking Screen

**File:** `lib/screens/booking.dart`

**Required Services:**
- `BookingService`
- `AuthService`

**Implementation Checklist:**

```dart
// ✓ Get kontrakan ID from arguments
// ✓ Display kontrakan info
// ✓ Date picker for start date
// ✓ Duration selector (in months)
// ✓ Calculate total price
// ✓ Optional notes field
// ✓ Payment proof image picker
// ✓ Upload and create booking
// ✓ Show loading during upload
// ✓ Handle success/error
```

**Methods to Implement:**
- [ ] `_pickDate()` - Date picker
- [ ] `_pickImage()` - Image picker for payment proof
- [ ] `_calculatePrice()` - Calculate total based on duration
- [ ] `_handleCreateBooking()` - Call `BookingService.createBooking()`
- [ ] Validate all required fields
- [ ] Show upload progress

**Test Cases:**
- [ ] Select date
- [ ] Enter duration
- [ ] Pick image
- [ ] Calculate price correctly
- [ ] Submit booking
- [ ] Handle upload errors

---

## 6️⃣ Booking History Screen

**File:** `lib/screens/booking_history.dart`

**Required Services:**
- `BookingService`
- `AuthService`

**Implementation Checklist:**

```dart
// ✓ Load user's booking history on init
// ✓ Display list of bookings
// ✓ Show: kontrakan name, dates, status, total price
// ✓ Status badges (pending, confirmed, ongoing, cancelled)
// ✓ Tap to see detail
// ✓ Cancel booking option (if allowed)
// ✓ Extend booking option (if allowed)
// ✓ Handle empty state (no bookings)
// ✓ Handle loading, error states
```

**Methods to Implement:**
- [ ] `_loadBookings()` - Fetch booking history
- [ ] `_handleCancel()` - Cancel booking
- [ ] `_handleExtend()` - Extend booking
- [ ] Status display and styling
- [ ] Detail view on tap

---

## 7️⃣ Review Screen

**File:** `lib/screens/review.dart` (or add to detail screen)

**Required Services:**
- `ReviewService`
- `AuthService`

**Implementation Checklist:**

```dart
// ✓ Get kontrakan/laundry ID
// ✓ Star rating selector (1-5)
// ✓ Comment/review text field
// ✓ Submit button
// ✓ Validate required fields
// ✓ Show loading during submission
// ✓ Display success/error message
// ✓ Option to edit own review
// ✓ Option to delete own review
```

**Methods to Implement:**
- [ ] `_handleAddReview()` - Call `ReviewService.addKontrakanReview()`
- [ ] Star rating widget
- [ ] Input validation
- [ ] Success handling
- [ ] Error handling

---

## 8️⃣ Favorites Screen

**File:** `lib/screens/favorites.dart`

**Required Services:**
- `FavoriteService`
- `KontrakanService`
- `LaundryService`

**Implementation Checklist:**

```dart
// ✓ Load user's favorites on init
// ✓ Separate tabs/sections for kontrakan and laundry
// ✓ Display list of favorite items
// ✓ Remove from favorites option
// ✓ Tap to view detail
// ✓ Handle empty state
// ✓ Handle loading, error states
```

**Methods to Implement:**
- [ ] `_loadFavorites()` - Fetch favorites
- [ ] `_handleRemove()` - Remove from favorites
- [ ] Tab management (kontrakan/laundry)
- [ ] Empty state handling

---

## 9️⃣ Laundry List Screen

**File:** `lib/screens/laundry_list.dart`

**Required Services:**
- `LaundryService`
- `FavoriteService`

**Implementation Checklist:**

```dart
// ✓ Load laundry list on init
// ✓ Display laundry services
// ✓ Filter by price and search
// ✓ Show: name, price, rating, reviews count
// ✓ Favorite button/icon
// ✓ Tap to view detail
// ✓ Handle loading, error, empty states
```

**Methods to Implement:**
- [ ] `_loadLaundry()` - Fetch laundry list
- [ ] Filter and search
- [ ] Favorite toggle
- [ ] Error handling

---

## 🔟 Recommendations Screen (SAW)

**File:** `lib/screens/recommendations.dart`

**Required Services:**
- `KontrakanService` or `LaundryService`
- `AuthService` (optional, for user preferences)

**Implementation Checklist:**

```dart
// ✓ Load recommendations based on criteria
// ✓ Display ranked results
// ✓ Show score/reputation for each item
// ✓ Filter options (price, distance, etc.)
// ✓ Tap to view detail
// ✓ Display algorithm explanation
// ✓ Handle loading, error states
```

**Methods to Implement:**
- [ ] `_loadRecommendations()` - Call `getRecommendations()`
- [ ] Filter handling
- [ ] Ranking display
- [ ] Detail navigation

---

## 1️⃣1️⃣ Profile Screen

**File:** `lib/screens/profile.dart`

**Required Services:**
- `AuthService`

**Implementation Checklist:**

```dart
// ✓ Display current user info (from AuthService)
// ✓ Name field (editable)
// ✓ Email field (display only)
// ✓ Phone field (editable)
// ✓ Address field (editable)
// ✓ Profile photo (display and upload)
// ✓ Save button to update profile
// ✓ Logout button
// ✓ Show loading during update
// ✓ Show success/error messages
```

**Methods to Implement:**
- [ ] `_loadProfile()` - Get current user from AuthService
- [ ] `_handleUpdate()` - Call `AuthService.updateProfile()`
- [ ] Image picker for profile photo
- [ ] Update form validation
- [ ] Logout functionality

---

## 🚀 Quick Implementation Steps

### For Each Screen:

1. **Import Required Services**
   ```dart
   import 'package:spk_mobile/services/xxx_service.dart';
   ```

2. **Initialize Services**
   ```dart
   final _service = XxxService();
   ```

3. **Load Data in initState**
   ```dart
   @override
   void initState() {
     super.initState();
     _loadData();
   }
   ```

4. **Create Load Method**
   ```dart
   void _loadData() async {
     try {
       setState(() {
         _isLoading = true;
         _error = null;
       });
       // Call API
       setState(() => _isLoading = false);
     } catch (e) {
       setState(() {
         _error = 'Error: $e';
         _isLoading = false;
       });
     }
   }
   ```

5. **Build UI with States**
   ```dart
   if (_isLoading) return CircularProgressIndicator();
   if (_error != null) return ErrorWidget(_error, onRetry: _loadData);
   if (_data.isEmpty) return EmptyWidget();
   return ListView(...);
   ```

6. **Handle User Actions**
   ```dart
   ElevatedButton(
     onPressed: () => _handleAction(),
     child: Text('Action'),
   )
   ```

---

## ✅ Final Checklist (Per Screen)

Before marking a screen as complete:

- [ ] All API calls implemented
- [ ] Loading indicators shown
- [ ] Error states handled
- [ ] Empty states handled
- [ ] All buttons/actions working
- [ ] Navigation implemented
- [ ] Input validation done
- [ ] User feedback (SnackBar/dialogs)
- [ ] Images loading correctly
- [ ] Tested on device/emulator

---

## 📊 Implementation Progress Tracker

| Screen | Service | Status | Notes |
|--------|---------|--------|-------|
| Login | AuthService | [ ] | |
| Register | AuthService | [ ] | |
| Kontrakan List | KontrakanService | [ ] | |
| Kontrakan Detail | KontrakanService | [ ] | |
| Booking | BookingService | [ ] | |
| Booking History | BookingService | [ ] | |
| Reviews | ReviewService | [ ] | |
| Favorites | FavoriteService | [ ] | |
| Laundry List | LaundryService | [ ] | |
| Recommendations | KontrakanService | [ ] | |
| Profile | AuthService | [ ] | |

---

**Keep this file updated as you implement each screen!**
