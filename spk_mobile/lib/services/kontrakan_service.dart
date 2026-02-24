import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/app_config.dart';
import '../models/kontrakan.dart';
import 'auth_service.dart';

class KontrakanService {
  final AuthService _authService = AuthService();

  // Get headers with token if available
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

  // Get all kontrakan
  Future<List<Kontrakan>> getKontrakan({
    String? search,
    double? hargaMin,
    double? hargaMax,
    int? jumlahKamar,
    String status = 'tersedia',
  }) async {
    try {
      var url = '${AppConfig.baseUrl}/kontrakan?status=$status';

      if (search != null && search.isNotEmpty) {
        url += '&search=$search';
      }
      if (hargaMin != null) {
        url += '&harga_min=$hargaMin';
      }
      if (hargaMax != null) {
        url += '&harga_max=$hargaMax';
      }
      if (jumlahKamar != null) {
        url += '&jumlah_kamar=$jumlahKamar';
      }

      final response = await http.get(Uri.parse(url), headers: _headers);

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          final List items = data['data']['data'] ?? data['data'];
          return items.map((json) => Kontrakan.fromJson(json)).toList();
        }
      }
      return [];
    } catch (e) {
      // Error getting kontrakan silently
      return [];
    }
  }

  // Get kontrakan by ID
  Future<Kontrakan?> getKontrakanById(int id) async {
    try {
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/kontrakan/$id'),
        headers: _headers,
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          return Kontrakan.fromJson(data['data']);
        }
      }
      return null;
    } catch (e) {
      // Error getting kontrakan detail silently
      return null;
    }
  }

  // Calculate SAW for kontrakan (recommendation)
  Future<Map<String, dynamic>> getRecommendations({
    double? hargaMin,
    double? hargaMax,
    int? jumlahKamar,
    double? jarakMax,
    String? fasilitas,
  }) async {
    try {
      final body = <String, dynamic>{};

      if (hargaMin != null) body['harga_min'] = hargaMin;
      if (hargaMax != null) body['harga_max'] = hargaMax;
      if (jumlahKamar != null) body['jumlah_kamar'] = jumlahKamar;
      if (jarakMax != null) body['jarak_max'] = jarakMax;
      if (fasilitas != null && fasilitas.isNotEmpty) {
        body['fasilitas'] = fasilitas;
      }

      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/saw/calculate/kontrakan'),
        headers: _headers,
        body: jsonEncode(body),
      );

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
        'message': data['message'] ?? 'Gagal menghitung SAW',
      };
    } catch (e) {
      return {'success': false, 'message': 'Error: $e'};
    }
  }
}
