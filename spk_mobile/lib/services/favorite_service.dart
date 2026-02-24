import 'dart:convert';
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

  // Get all user's favorites with full data
  Future<Map<String, dynamic>> getFavorites() async {
    try {
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/favorites'),
        headers: _headers,
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'kontrakan': data['data']['kontrakan'] ?? [],
          'laundry': data['data']['laundry'] ?? [],
        };
      } else {
        return {
          'success': false,
          'kontrakan': [],
          'laundry': [],
        };
      }
    } catch (e) {
      // Error getting favorites silently
      return {
        'success': false,
        'kontrakan': [],
        'laundry': [],
      };
    }
  }

  // Get favorites with parsed models
  Future<Map<String, dynamic>> getFavoritesWithModels() async {
    try {
      final result = await getFavorites();
      if (result['success'] != true) return result;

      final kontrakanList = (result['kontrakan'] as List)
          .map((json) => Kontrakan.fromJson(json as Map<String, dynamic>))
          .toList();

      final laundryList = (result['laundry'] as List)
          .map((json) => Laundry.fromJson(json as Map<String, dynamic>))
          .toList();

      return {
        'success': true,
        'kontrakan': kontrakanList,
        'laundry': laundryList,
      };
    } catch (e) {
      // Error parsing favorites silently
      return {
        'success': false,
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

      return {
        'kontrakan': (result['kontrakan'] as List)
            .map<int>((e) => (e as Map<String, dynamic>)['id'] as int? ?? 0)
            .toList(),
        'laundry': (result['laundry'] as List)
            .map<int>((e) => (e as Map<String, dynamic>)['id'] as int? ?? 0)
            .toList(),
      };
    } catch (e) {
      return {'kontrakan': [], 'laundry': []};
    }
  }

  // Toggle kontrakan favorite status
  Future<Map<String, dynamic>> toggleKontrakanFavorite(int kontrakanId) async {
    try {
      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/favorites/kontrakan/$kontrakanId'),
        headers: _headers,
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Status favorit berhasil diubah',
          'isFavorite': data['data']['is_favorite'] ?? false,
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal mengubah status favorit',
        };
      }
    } catch (e) {
      return {'success': false, 'message': 'Error: $e'};
    }
  }

  // Toggle laundry favorite status
  Future<Map<String, dynamic>> toggleLaundryFavorite(int laundryId) async {
    try {
      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/favorites/laundry/$laundryId'),
        headers: _headers,
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Status favorit berhasil diubah',
          'isFavorite': data['data']['is_favorite'] ?? false,
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal mengubah status favorit',
        };
      }
    } catch (e) {
      return {'success': false, 'message': 'Error: $e'};
    }
  }

  // Remove favorite
  Future<Map<String, dynamic>> removeFavorite(int favoriteId) async {
    try {
      final response = await http.delete(
        Uri.parse('${AppConfig.baseUrl}/favorites/$favoriteId'),
        headers: _headers,
      );

      final data = jsonDecode(response.body);

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
      return {'success': false, 'message': 'Error: $e'};
    }
  }

  // Check if kontrakan is favorite
  Future<bool> isKontrakanFavorite(int kontrakanId) async {
    try {
      final favorites = await getFavorites();
      final kontrakanList = favorites['kontrakan'] as List<int>? ?? [];
      return kontrakanList.contains(kontrakanId);
    } catch (e) {
      // Error checking favorite silently
      return false;
    }
  }

  // Check if laundry is favorite
  Future<bool> isLaundryFavorite(int laundryId) async {
    try {
      final favorites = await getFavorites();
      final laundryList = favorites['laundry'] as List<int>? ?? [];
      return laundryList.contains(laundryId);
    } catch (e) {
      // Error checking favorite silently
      return false;
    }
  }
}
