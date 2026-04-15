import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../config/app_config.dart';
import '../models/kontrakan.dart';
import '../models/laundry.dart';
import 'auth_service.dart';

class FavoriteService {
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

  /// Pastikan token ada sebelum request
  bool _ensureAuthenticated() {
    if (_authService.token == null) {
      debugPrint('[FAV] ❌ Token NULL — user belum login!');
      return false;
    }
    return true;
  }

  // Get all user's favorites with full data (raw JSON)
  Future<Map<String, dynamic>> getFavorites() async {
    if (!_ensureAuthenticated()) {
      return {'success': false, 'message': 'Belum login', 'kontrakan': [], 'laundry': []};
    }

    try {
      final url = '${AppConfig.baseUrl}/favorites';
      debugPrint('[FAV] GET $url');
      debugPrint('[FAV] Token: ${_authService.token!.substring(0, 10)}...');

      final response = await http
          .get(Uri.parse(url), headers: _headers)
          .timeout(AppConfig.connectionTimeout);

      debugPrint('[FAV] GET Status: ${response.statusCode}');
      debugPrint('[FAV] GET Body: ${response.body.length > 300 ? response.body.substring(0, 300) : response.body}');

      if (response.statusCode == 401) {
        await _authService.handleUnauthorized(response.statusCode);
        debugPrint('[FAV] ❌ 401 Unauthorized — token expired/invalid');
        return {'success': false, 'message': 'Sesi expired, silakan login ulang', 'kontrakan': [], 'laundry': []};
      }

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'kontrakan': data['data']?['kontrakan'] ?? [],
          'laundry': data['data']?['laundry'] ?? [],
        };
      } else {
        debugPrint('[FAV] ❌ Response not success: ${response.statusCode} ${data['message']}');
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal memuat favorit',
          'kontrakan': [],
          'laundry': [],
        };
      }
    } catch (e, stack) {
      debugPrint('[FAV] ❌ Exception in getFavorites: $e');
      debugPrint('[FAV] Stack: $stack');
      return {
        'success': false,
        'message': 'Gagal terhubung ke server: $e',
        'kontrakan': [],
        'laundry': [],
      };
    }
  }

  // Get favorites with parsed Kontrakan/Laundry models
  Future<Map<String, dynamic>> getFavoritesWithModels() async {
    final result = await getFavorites();

    if (result['success'] != true) {
      // Return typed empty lists to avoid cast errors downstream
      return {
        'success': false,
        'message': result['message'] ?? 'Gagal memuat favorit',
        'kontrakan': <Kontrakan>[],
        'laundry': <Laundry>[],
      };
    }

    try {
      final rawKontrakan = result['kontrakan'] as List? ?? [];
      final rawLaundry = result['laundry'] as List? ?? [];

      debugPrint('[FAV] Parsing ${rawKontrakan.length} kontrakan, ${rawLaundry.length} laundry');

      final kontrakanList = <Kontrakan>[];
      for (final json in rawKontrakan) {
        try {
          kontrakanList.add(Kontrakan.fromJson(json as Map<String, dynamic>));
        } catch (e) {
          debugPrint('[FAV] ⚠ Skip kontrakan parse error: $e — data: $json');
        }
      }

      final laundryList = <Laundry>[];
      for (final json in rawLaundry) {
        try {
          laundryList.add(Laundry.fromJson(json as Map<String, dynamic>));
        } catch (e) {
          debugPrint('[FAV] ⚠ Skip laundry parse error: $e — data: $json');
        }
      }

      debugPrint('[FAV] ✅ Parsed ${kontrakanList.length} kontrakan, ${laundryList.length} laundry');

      return {
        'success': true,
        'kontrakan': kontrakanList,
        'laundry': laundryList,
      };
    } catch (e, stack) {
      debugPrint('[FAV] ❌ Exception parsing favorites: $e');
      debugPrint('[FAV] Stack: $stack');
      return {
        'success': false,
        'message': 'Gagal memproses data favorit',
        'kontrakan': <Kontrakan>[],
        'laundry': <Laundry>[],
      };
    }
  }

  // Get favorite IDs only (for checking if something is favorited)
  Future<Map<String, List<int>>> getFavoriteIds() async {
    try {
      final result = await getFavorites();
      if (result['success'] != true) {
        return {'kontrakan': [], 'laundry': []};
      }

      final kontrakanIds = <int>[];
      for (final e in (result['kontrakan'] as List? ?? [])) {
        final id = (e as Map<String, dynamic>?)?['id'];
        if (id != null) kontrakanIds.add(id is int ? id : int.tryParse(id.toString()) ?? 0);
      }

      final laundryIds = <int>[];
      for (final e in (result['laundry'] as List? ?? [])) {
        final id = (e as Map<String, dynamic>?)?['id'];
        if (id != null) laundryIds.add(id is int ? id : int.tryParse(id.toString()) ?? 0);
      }

      debugPrint('[FAV] IDs → kontrakan: $kontrakanIds, laundry: $laundryIds');
      return {'kontrakan': kontrakanIds, 'laundry': laundryIds};
    } catch (e) {
      debugPrint('[FAV] ❌ Exception in getFavoriteIds: $e');
      return {'kontrakan': [], 'laundry': []};
    }
  }

  // Toggle kontrakan favorite status
  Future<Map<String, dynamic>> toggleKontrakanFavorite(int kontrakanId) async {
    if (!_ensureAuthenticated()) {
      return {'success': false, 'message': 'Belum login'};
    }

    try {
      final url = '${AppConfig.baseUrl}/favorites/kontrakan/$kontrakanId';
      debugPrint('[FAV] POST $url');

      final response = await http
          .post(Uri.parse(url), headers: _headers)
          .timeout(AppConfig.connectionTimeout);

      debugPrint('[FAV] Toggle kontrakan → ${response.statusCode}: ${response.body}');

      if (response.statusCode == 401) {
        await _authService.handleUnauthorized(response.statusCode);
        return {'success': false, 'message': 'Sesi expired, silakan login ulang'};
      }

      final data = jsonDecode(response.body);
      final statusOk = response.statusCode == 200 || response.statusCode == 201;

      if (statusOk && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Status favorit berhasil diubah',
          'isFavorite': data['is_favorited'] ?? false,
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal mengubah status favorit (${response.statusCode})',
        };
      }
    } catch (e) {
      debugPrint('[FAV] ❌ Exception toggleKontrakan: $e');
      return {'success': false, 'message': 'Gagal terhubung ke server'};
    }
  }

  // Toggle laundry favorite status
  Future<Map<String, dynamic>> toggleLaundryFavorite(int laundryId) async {
    if (!_ensureAuthenticated()) {
      return {'success': false, 'message': 'Belum login'};
    }

    try {
      final url = '${AppConfig.baseUrl}/favorites/laundry/$laundryId';
      debugPrint('[FAV] POST $url');

      final response = await http
          .post(Uri.parse(url), headers: _headers)
          .timeout(AppConfig.connectionTimeout);

      debugPrint('[FAV] Toggle laundry → ${response.statusCode}: ${response.body}');

      if (response.statusCode == 401) {
        await _authService.handleUnauthorized(response.statusCode);
        return {'success': false, 'message': 'Sesi expired, silakan login ulang'};
      }

      final data = jsonDecode(response.body);
      final statusOk = response.statusCode == 200 || response.statusCode == 201;

      if (statusOk && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Status favorit berhasil diubah',
          'isFavorite': data['is_favorited'] ?? false,
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal mengubah status favorit (${response.statusCode})',
        };
      }
    } catch (e) {
      debugPrint('[FAV] ❌ Exception toggleLaundry: $e');
      return {'success': false, 'message': 'Gagal terhubung ke server'};
    }
  }

  // Remove favorite
  Future<Map<String, dynamic>> removeFavorite(int favoriteId) async {
    if (!_ensureAuthenticated()) {
      return {'success': false, 'message': 'Belum login'};
    }

    try {
      final response = await http
          .delete(
            Uri.parse('${AppConfig.baseUrl}/favorites/$favoriteId'),
            headers: _headers,
          )
          .timeout(AppConfig.connectionTimeout);

      final data = jsonDecode(response.body);

      if (response.statusCode == 401) {
        await _authService.handleUnauthorized(response.statusCode);
        return {'success': false, 'message': 'Sesi expired, silakan login ulang'};
      }

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Favorit berhasil dihapus',
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal menghapus favorit',
        };
      }
    } catch (e) {
      debugPrint('[FAV] ❌ Exception removeFavorite: $e');
      return {'success': false, 'message': 'Gagal terhubung ke server'};
    }
  }

  // Check if kontrakan is favorite
  Future<bool> isKontrakanFavorite(int kontrakanId) async {
    try {
      final ids = await getFavoriteIds();
      return (ids['kontrakan'] ?? []).contains(kontrakanId);
    } catch (e) {
      debugPrint('[FAV] ❌ Exception isKontrakanFavorite: $e');
      return false;
    }
  }

  // Check if laundry is favorite
  Future<bool> isLaundryFavorite(int laundryId) async {
    try {
      final ids = await getFavoriteIds();
      return (ids['laundry'] ?? []).contains(laundryId);
    } catch (e) {
      debugPrint('[FAV] ❌ Exception isLaundryFavorite: $e');
      return false;
    }
  }
}
