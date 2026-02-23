// API Test Helper - Gunakan ini untuk test semua API endpoints
// File: test/api_test_helper.dart

import 'package:spk_mobile/services/auth_service.dart';
import 'package:spk_mobile/services/booking_service.dart';
import 'package:spk_mobile/services/kontrakan_service.dart';
import 'package:spk_mobile/services/laundry_service.dart';
import 'package:spk_mobile/services/review_service.dart';
import 'package:spk_mobile/services/favorite_service.dart';

class APITestHelper {
  static final _authService = AuthService();
  static final _kontrakanService = KontrakanService();
  static final _bookingService = BookingService();
  static final _laundryService = LaundryService();
  static final _reviewService = ReviewService();
  static final _favoriteService = FavoriteService();

  // Test 1: Load existing token
  static Future<void> testLoadToken() async {
    print('\n=== TEST 1: Load Token ===');
    try {
      await _authService.loadToken();
      if (_authService.token != null) {
        print('✓ Token loaded: ${_authService.token!.substring(0, 20)}...');
        print('✓ Current user: ${_authService.currentUser?.name}');
      } else {
        print('ℹ No saved token found');
      }
    } catch (e) {
      print('✗ Error: $e');
    }
  }

  // Test 2: Login
  static Future<void> testLogin({
    required String email,
    required String password,
  }) async {
    print('\n=== TEST 2: Login ===');
    try {
      final result = await _authService.login(email: email, password: password);

      if (result['success']) {
        print('✓ Login successful');
        print('✓ Token: ${_authService.token!.substring(0, 20)}...');
        print('✓ User: ${_authService.currentUser?.name}');
      } else {
        print('✗ Login failed: ${result['message']}');
        if (result['errors'] != null) {
          print('✗ Errors: ${result['errors']}');
        }
      }
    } catch (e) {
      print('✗ Exception: $e');
    }
  }

  // Test 3: Get all kontrakan
  static Future<void> testGetKontrakan() async {
    print('\n=== TEST 3: Get All Kontrakan ===');
    try {
      final kontrakan = await _kontrakanService.getKontrakan();
      print('✓ Found ${kontrakan.length} kontrakan');
      if (kontrakan.isNotEmpty) {
        final first = kontrakan.first;
        print('  - First: ${first.nama} (Rp ${first.harga})');
      }
    } catch (e) {
      print('✗ Exception: $e');
    }
  }

  // Test 4: Get specific kontrakan detail
  static Future<void> testGetKontrakanDetail(int id) async {
    print('\n=== TEST 4: Get Kontrakan Detail (ID: $id) ===');
    try {
      final kontrakan = await _kontrakanService.getKontrakanById(id);
      if (kontrakan != null) {
        print('✓ Found kontrakan: ${kontrakan.nama}');
        print('  - Harga: Rp ${kontrakan.harga}');
        print('  - Kamar: ${kontrakan.jumlahKamar}');
        print('  - Alamat: ${kontrakan.alamat}');
      } else {
        print('✗ Kontrakan not found');
      }
    } catch (e) {
      print('✗ Exception: $e');
    }
  }

  // Test 5: Search kontrakan
  static Future<void> testSearchKontrakan({
    String? search,
    double? hargaMax,
    int? jumlahKamar,
  }) async {
    print('\n=== TEST 5: Search Kontrakan ===');
    print('  Search: $search');
    print('  Harga Max: $hargaMax');
    print('  Jumlah Kamar: $jumlahKamar');
    try {
      final results = await _kontrakanService.getKontrakan(
        search: search,
        hargaMax: hargaMax,
        jumlahKamar: jumlahKamar,
      );
      print('✓ Found ${results.length} results');
      for (var k in results.take(3)) {
        print('  - ${k.nama} (Rp ${k.harga})');
      }
    } catch (e) {
      print('✗ Exception: $e');
    }
  }

  // Test 6: Get SAW recommendations
  static Future<void> testGetRecommendations({
    double? hargaMax,
    double? jarakMax,
  }) async {
    print('\n=== TEST 6: Get SAW Recommendations ===');
    print('  Harga Max: $hargaMax');
    print('  Jarak Max: $jarakMax');
    try {
      final result = await _kontrakanService.getRecommendations(
        hargaMax: hargaMax,
        jarakMax: jarakMax,
      );
      if (result['success']) {
        print('✓ Recommendations retrieved');
        final ranking = result['ranking'] as List?;
        if (ranking != null && ranking.isNotEmpty) {
          print('  Top 3 recommendations:');
          for (var item in ranking.take(3)) {
            print(
              '  - ${item['name']} (Score: ${item['score']})',
            );
          }
        }
      } else {
        print('✗ Failed: ${result['message']}');
      }
    } catch (e) {
      print('✗ Exception: $e');
    }
  }

  // Test 7: Get laundry list
  static Future<void> testGetLaundry() async {
    print('\n=== TEST 7: Get Laundry Services ===');
    try {
      final laundry = await _laundryService.getLaundry();
      print('✓ Found ${laundry.length} laundry services');
      if (laundry.isNotEmpty) {
        final first = laundry.first;
        print('  - First: ${first.nama} (Rp ${first.hargaPerKg})');
      }
    } catch (e) {
      print('✗ Exception: $e');
    }
  }

  // Test 8: Get user's booking history
  static Future<void> testGetBookingHistory() async {
    print('\n=== TEST 8: Get Booking History ===');
    if (!_authService.isAuthenticated) {
      print('✗ Not authenticated. Please login first.');
      return;
    }

    try {
      final bookings = await _bookingService.getBookingHistory();
      print('✓ Found ${bookings.length} bookings');
      for (var booking in bookings.take(3)) {
        print(
          '  - Booking #${booking.id}: ${booking.status} (${booking.totalBiaya})',
        );
      }
    } catch (e) {
      print('✗ Exception: $e');
    }
  }

  // Test 9: Get specific booking detail
  static Future<void> testGetBookingDetail(int id) async {
    print('\n=== TEST 9: Get Booking Detail (ID: $id) ===');
    if (!_authService.isAuthenticated) {
      print('✗ Not authenticated. Please login first.');
      return;
    }

    try {
      final booking = await _bookingService.getBookingById(id);
      if (booking != null) {
        print('✓ Found booking:');
        print('  - Status: ${booking.status}');
        print('  - Check-in: ${booking.tanggalMulai}');
        print('  - Check-out: ${booking.tanggalSelesai}');
        print('  - Total: Rp ${booking.totalBiaya}');
      } else {
        print('✗ Booking not found');
      }
    } catch (e) {
      print('✗ Exception: $e');
    }
  }

  // Test 10: Toggle favorite
  static Future<void> testToggleFavorite(int kontrakanId) async {
    print('\n=== TEST 10: Toggle Favorite ===');
    if (!_authService.isAuthenticated) {
      print('✗ Not authenticated. Please login first.');
      return;
    }

    try {
      final result = await _favoriteService.toggleKontrakanFavorite(
        kontrakanId,
      );
      if (result['success']) {
        print('✓ Favorite status updated');
        print('  - Is favorite: ${result['isFavorite']}');
      } else {
        print('✗ Failed: ${result['message']}');
      }
    } catch (e) {
      print('✗ Exception: $e');
    }
  }

  // Test 11: Get favorites
  static Future<void> testGetFavorites() async {
    print('\n=== TEST 11: Get Favorites ===');
    if (!_authService.isAuthenticated) {
      print('✗ Not authenticated. Please login first.');
      return;
    }

    try {
      final favorites = await _favoriteService.getFavorites();
      if (favorites['success']) {
        final kontrakanIds = favorites['kontrakan'] as List<int>;
        final laundryIds = favorites['laundry'] as List<int>;
        print('✓ Favorites retrieved');
        print('  - Kontrakan: ${kontrakanIds.length} items');
        print('  - Laundry: ${laundryIds.length} items');
      } else {
        print('✗ Failed to get favorites');
      }
    } catch (e) {
      print('✗ Exception: $e');
    }
  }

  // Test 12: Add review (requires authentication)
  static Future<void> testAddReview({
    required int kontrakanId,
    required double rating,
    required String comment,
  }) async {
    print('\n=== TEST 12: Add Review ===');
    if (!_authService.isAuthenticated) {
      print('✗ Not authenticated. Please login first.');
      return;
    }

    try {
      final result = await _reviewService.addKontrakanReview(
        kontrakanId: kontrakanId,
        rating: rating,
        comment: comment,
      );
      if (result['success']) {
        print('✓ Review posted successfully');
      } else {
        print('✗ Failed: ${result['message']}');
      }
    } catch (e) {
      print('✗ Exception: $e');
    }
  }

  // Test 13: Logout
  static Future<void> testLogout() async {
    print('\n=== TEST 13: Logout ===');
    try {
      await _authService.logout();
      print('✓ Logged out successfully');
      print('  - Token cleared: ${_authService.token == null}');
    } catch (e) {
      print('✗ Exception: $e');
    }
  }

  // Run all tests
  static Future<void> runAllTests({
    String? testEmail,
    String? testPassword,
  }) async {
    print('\n╔════════════════════════════════════╗');
    print('║   SPK MOBILE API TEST SUITE        ║');
    print('╚════════════════════════════════════╝');

    // Test public endpoints (no auth needed)
    await testLoadToken();
    await testGetKontrakan();
    await testGetKontrakanDetail(1);
    await testSearchKontrakan(
      hargaMax: 1000000,
      jumlahKamar: 2,
    );
    await testGetRecommendations(hargaMax: 1500000);
    await testGetLaundry();

    // Test protected endpoints (need login)
    if (testEmail != null && testPassword != null) {
      await testLogin(
        email: testEmail,
        password: testPassword,
      );
      await testGetBookingHistory();
      await testToggleFavorite(1);
      await testGetFavorites();
      await testAddReview(
        kontrakanId: 1,
        rating: 4.5,
        comment: 'Bagus dan nyaman!',
      );
    }

    print('\n╔════════════════════════════════════╗');
    print('║   TEST SUITE COMPLETED             ║');
    print('╚════════════════════════════════════╝\n');
  }
}

// Example usage in main:
/*
void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Run API tests
  await APITestHelper.runAllTests(
    testEmail: 'user@example.com',
    testPassword: 'password123',
  );

  runApp(const MyApp());
}

// Or test individual endpoints:
void testSpecificEndpoint() async {
  await APITestHelper.testGetKontrakan();
  await APITestHelper.testLogin(
    email: 'user@example.com',
    password: 'password123',
  );
}
*/
