import 'dart:convert';
import 'dart:io';
import 'dart:typed_data';

import 'package:http/http.dart' as http;

import '../config/app_config.dart';
import '../models/booking.dart';
import 'auth_service.dart';

class BookingService {
  final AuthService _authService = AuthService();

  Map<String, String> get _headers {
    final headers = <String, String>{
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };

    if (_authService.token != null) {
      headers['Authorization'] = 'Bearer ${_authService.token}';
    }

    return headers;
  }

  Future<List<Booking>> getBookingHistory() async {
    try {
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/bookings'),
        headers: _headers,
      );

      if (response.statusCode == 401) {
        await _authService.handleUnauthorized(response.statusCode);
        return [];
      }

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        if (data['success'] == true) {
          final List items = data['data']['data'] ?? data['data'] ?? [];

          return items
              .map(
                (item) =>
                    Booking.fromJson(Map<String, dynamic>.from(item as Map)),
              )
              .toList();
        }
      }

      return [];
    } catch (_) {
      return [];
    }
  }

  Future<Map<String, dynamic>> _createPengajuan(
    Map<String, dynamic> body,
  ) async {
    try {
      final response = await http
          .post(
            Uri.parse('${AppConfig.baseUrl}/bookings'),
            headers: _headers,
            body: jsonEncode(body),
          )
          .timeout(const Duration(seconds: 30));

      final data = jsonDecode(response.body);

      if (response.statusCode == 401) {
        await _authService.handleUnauthorized(response.statusCode);
        return {
          'success': false,
          'message': 'Sesi habis, silakan login ulang.',
        };
      }

      if (response.statusCode == 201 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'],
          'booking': Booking.fromJson(
            Map<String, dynamic>.from(data['data'] as Map),
          ),
        };
      }

      return {
        'success': false,
        'message': data['message'] ?? 'Gagal mengirim pengajuan.',
        'errors': data['errors'],
      };
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan: $e'};
    }
  }

  Future<Map<String, dynamic>> createSurvey({
    required int kontrakanId,
    required DateTime tanggalSurvei,
    required String jamSurvei,
    String? catatan,
  }) async {
    final body = <String, dynamic>{
      'kontrakan_id': kontrakanId,
      'jenis_pengajuan': 'survei',
      'tanggal_survei': tanggalSurvei.toIso8601String().split('T')[0],
      'jam_survei': jamSurvei,
    };

    if (catatan != null && catatan.trim().isNotEmpty) {
      body['catatan'] = catatan.trim();
    }

    return _createPengajuan(body);
  }

  Future<Map<String, dynamic>> createSewa({
    required int kontrakanId,
    required DateTime tanggalMulai,
    required int durasiBulan,
    String? catatan,
    File? ktpPhoto,
  }) async {
    try {
      final request = http.MultipartRequest(
        'POST',
        Uri.parse('${AppConfig.baseUrl}/bookings'),
      );

      if (_authService.token != null) {
        request.headers['Authorization'] = 'Bearer ${_authService.token}';
      }

      request.headers['Accept'] = 'application/json';

      request.fields['kontrakan_id'] = kontrakanId.toString();
      request.fields['jenis_pengajuan'] = 'sewa';
      request.fields['tanggal_mulai'] = tanggalMulai.toIso8601String().split(
        'T',
      )[0];
      request.fields['durasi_bulan'] = durasiBulan.toString();

      if (catatan != null && catatan.trim().isNotEmpty) {
        request.fields['catatan'] = catatan.trim();
      }

      if (ktpPhoto != null) {
        request.files.add(
          await http.MultipartFile.fromPath('ktp_photo', ktpPhoto.path),
        );
      }

      final streamedResponse = await request.send().timeout(
        const Duration(seconds: 30),
      );

      final response = await http.Response.fromStream(streamedResponse);

      final data = response.body.isNotEmpty
          ? jsonDecode(response.body)
          : <String, dynamic>{};

      if (response.statusCode == 401) {
        await _authService.handleUnauthorized(response.statusCode);
        return {
          'success': false,
          'message': 'Sesi habis, silakan login ulang.',
        };
      }

      if (response.statusCode == 201 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'],
          'booking': Booking.fromJson(data['data']),
        };
      }

      return {
        'success': false,
        'message': data['message'] ?? 'Gagal mengirim pengajuan sewa.',
        'errors': data['errors'],
      };
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan: $e'};
    }
  }

  Future<Map<String, dynamic>> createBooking({
    required int kontrakanId,
    required DateTime tanggalMulai,
    required int durasiBulan,
    String? catatan,
    File? paymentProof,
  }) {
    return createSewa(
      kontrakanId: kontrakanId,
      tanggalMulai: tanggalMulai,
      durasiBulan: durasiBulan,
      catatan: catatan,
    );
  }

  Future<Map<String, dynamic>> cancelBooking(int bookingId) async {
    try {
      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/bookings/$bookingId/cancel'),
        headers: _headers,
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 401) {
        await _authService.handleUnauthorized(response.statusCode);
        return {
          'success': false,
          'message': 'Sesi habis, silakan login ulang.',
        };
      }

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'message': data['message']};
      }

      return {
        'success': false,
        'message': data['message'] ?? 'Gagal membatalkan pengajuan.',
      };
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan: $e'};
    }
  }

  Future<Booking?> getBookingById(int id) async {
    try {
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/bookings/$id'),
        headers: _headers,
      );

      if (response.statusCode == 401) {
        await _authService.handleUnauthorized(response.statusCode);
        return null;
      }

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        if (data['success'] == true) {
          return Booking.fromJson(
            Map<String, dynamic>.from(data['data'] as Map),
          );
        }
      }

      return null;
    } catch (_) {
      return null;
    }
  }

  Future<Map<String, dynamic>> uploadPaymentProof(
    int bookingId,
    File imageFile,
  ) async {
    try {
      final request = http.MultipartRequest(
        'POST',
        Uri.parse('${AppConfig.baseUrl}/bookings/$bookingId/payment-proof'),
      );

      if (_authService.token != null) {
        request.headers['Authorization'] = 'Bearer ${_authService.token}';
        request.headers['Accept'] = 'application/json';
      }

      request.files.add(
        await http.MultipartFile.fromPath('payment_proof', imageFile.path),
      );

      final streamedResponse = await request.send().timeout(
        const Duration(seconds: 30),
      );

      final response = await http.Response.fromStream(streamedResponse);
      final data = jsonDecode(response.body);

      if (response.statusCode == 401) {
        await _authService.handleUnauthorized(response.statusCode);
        return {
          'success': false,
          'message': 'Sesi habis, silakan login ulang.',
        };
      }

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Bukti pembayaran berhasil diunggah.',
          'booking': Booking.fromJson(
            Map<String, dynamic>.from(data['data'] as Map),
          ),
        };
      }

      return {
        'success': false,
        'message': data['message'] ?? 'Gagal mengunggah bukti pembayaran.',
      };
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan: $e'};
    }
  }

  Future<Uint8List?> getPaymentProofBytes(int bookingId) async {
    try {
      final headers = <String, String>{'Accept': 'image/*'};

      if (_authService.token != null) {
        headers['Authorization'] = 'Bearer ${_authService.token}';
      }

      final response = await http
          .get(
            Uri.parse('${AppConfig.baseUrl}/bookings/$bookingId/payment-proof'),
            headers: headers,
          )
          .timeout(const Duration(seconds: 30));

      if (response.statusCode == 401) {
        await _authService.handleUnauthorized(response.statusCode);
        return null;
      }

      if (response.statusCode == 200) {
        return response.bodyBytes;
      }

      return null;
    } catch (_) {
      return null;
    }
  }
}
