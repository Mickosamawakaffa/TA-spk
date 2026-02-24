import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/app_config.dart';
import '../models/laundry.dart';
import 'auth_service.dart';

class LaundryService {
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

  // Get all laundry
  Future<List<Laundry>> getLaundry() async {
    try {
      final response = await http
          .get(Uri.parse('${AppConfig.baseUrl}/laundry'), headers: _headers)
          .timeout(AppConfig.connectionTimeout);

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          final List items = data['data']['data'] ?? data['data'] ?? [];
          return items.map((json) => Laundry.fromJson(json)).toList();
        }
      }
      return [];
    } catch (e) {
      // Error getting laundry silently
      return [];
    }
  }

  // Get laundry by ID
  Future<Laundry?> getLaundryById(int id) async {
    try {
      final response = await http
          .get(Uri.parse('${AppConfig.baseUrl}/laundry/$id'), headers: _headers)
          .timeout(AppConfig.connectionTimeout);

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          return Laundry.fromJson(data['data']);
        }
      }
      return null;
    } catch (e) {
      // Error getting laundry detail silently
      return null;
    }
  }

  // Get laundry recommendations using SAW
  Future<Map<String, dynamic>> getRecommendations({
    double? hargaMin,
    double? hargaMax,
    double? jarakMax,
    double? ratingMin,
  }) async {
    try {
      final body = <String, dynamic>{};

      if (hargaMin != null) body['harga_min'] = hargaMin;
      if (hargaMax != null) body['harga_max'] = hargaMax;
      if (jarakMax != null) body['jarak_max'] = jarakMax;
      if (ratingMin != null) body['rating_min'] = ratingMin;

      final response = await http
          .post(
            Uri.parse('${AppConfig.baseUrl}/saw/calculate/laundry'),
            headers: _headers,
            body: jsonEncode(body),
          )
          .timeout(AppConfig.connectionTimeout);

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          return {
            'success': true,
            'kriteria': data['data']['kriteria'],
            'hasil': data['data']['hasil'],
          };
        }
      }

      final data = jsonDecode(response.body);
      return {
        'success': false,
        'message': data['message'] ?? 'Gagal memuat rekomendasi',
      };
    } catch (e) {
      return {'success': false, 'message': 'Error: $e'};
    }
  }
}
