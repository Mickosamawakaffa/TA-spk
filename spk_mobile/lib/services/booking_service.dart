import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/app_config.dart';
import '../models/booking.dart';
import 'auth_service.dart';

class BookingService {
  final AuthService _authService = AuthService();

  Map<String, String> get _headers {
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };

    if (_authService.token != null) {
      headers['Authorization'] = 'Bearer ${_authService.token}';
    }

    return headers;
  }

  // Get booking history
  Future<List<Booking>> getBookingHistory() async {
    try {
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/bookings'),
        headers: _headers,
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          final List items = data['data']['data'] ?? data['data'];
          return items.map((json) => Booking.fromJson(json)).toList();
        }
      }
      return [];
    } catch (e) {
      print('Error getting booking history: $e');
      return [];
    }
  }

  // Create booking
  Future<Map<String, dynamic>> createBooking({
    required int kontrakanId,
    required DateTime tanggalMulai,
    required int durasiBulan,
    String? catatan,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/bookings'),
        headers: _headers,
        body: jsonEncode({
          'kontrakan_id': kontrakanId,
          'tanggal_mulai': tanggalMulai.toIso8601String().split('T')[0],
          'durasi_bulan': durasiBulan,
          'catatan': catatan,
        }),
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 201 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'],
          'booking': Booking.fromJson(data['data']),
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal membuat booking',
          'errors': data['errors'],
        };
      }
    } catch (e) {
      return {'success': false, 'message': 'Error: $e'};
    }
  }

  // Cancel booking
  Future<Map<String, dynamic>> cancelBooking(int bookingId) async {
    try {
      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/bookings/$bookingId/cancel'),
        headers: _headers,
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'message': data['message']};
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal membatalkan booking',
        };
      }
    } catch (e) {
      return {'success': false, 'message': 'Error: $e'};
    }
  }

  // Get booking detail
  Future<Booking?> getBookingById(int id) async {
    try {
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/bookings/$id'),
        headers: _headers,
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          return Booking.fromJson(data['data']);
        }
      }
      return null;
    } catch (e) {
      print('Error getting booking detail: $e');
      return null;
    }
  }
}
