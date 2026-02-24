import 'dart:convert';
import 'dart:io';
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
      // Error getting booking history silently
      return [];
    }
  }

  // Create booking (with required payment proof image)
  Future<Map<String, dynamic>> createBooking({
    required int kontrakanId,
    required DateTime tanggalMulai,
    required int durasiBulan,
    String? catatan,
    File? paymentProof,
  }) async {
    try {
      // Validate payment proof is provided
      if (paymentProof == null) {
        return {
          'success': false,
          'message': 'Bukti pembayaran wajib diunggah',
        };
      }

      // Use multipart request with payment proof
      final uri = Uri.parse('${AppConfig.baseUrl}/bookings');
      final request = http.MultipartRequest('POST', uri);

      if (_authService.token != null) {
        request.headers['Authorization'] = 'Bearer ${_authService.token}';
        request.headers['Accept'] = 'application/json';
      }

      request.fields['kontrakan_id'] = kontrakanId.toString();
      request.fields['tanggal_mulai'] = tanggalMulai.toIso8601String().split('T')[0];
      request.fields['durasi_bulan'] = durasiBulan.toString();
      if (catatan != null) request.fields['catatan'] = catatan;

      request.files.add(
        await http.MultipartFile.fromPath('payment_proof', paymentProof.path),
      );

      final streamedResponse = await request.send().timeout(const Duration(seconds: 30));
      final response = await http.Response.fromStream(streamedResponse);
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
      // Error getting booking detail silently
      return null;
    }
  }

  // Upload payment proof image
  Future<Map<String, dynamic>> uploadPaymentProof(
    int bookingId,
    File imageFile,
  ) async {
    try {
      final uri = Uri.parse(
        '${AppConfig.baseUrl}/bookings/$bookingId/payment-proof',
      );
      final request = http.MultipartRequest('POST', uri);

      // Add auth header
      if (_authService.token != null) {
        request.headers['Authorization'] = 'Bearer ${_authService.token}';
        request.headers['Accept'] = 'application/json';
      }

      // Attach image file
      request.files.add(
        await http.MultipartFile.fromPath('payment_proof', imageFile.path),
      );

      final streamedResponse = await request.send().timeout(
        const Duration(seconds: 30),
      );
      final response = await http.Response.fromStream(streamedResponse);
      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Bukti pembayaran berhasil diunggah',
          'booking': Booking.fromJson(data['data']),
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal mengunggah bukti pembayaran',
        };
      }
    } catch (e) {
      return {'success': false, 'message': 'Error: $e'};
    }
  }
}
