import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'dart:math' as math;
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';
import '../models/user.dart';

class AuthService {
  // Singleton pattern
  static final AuthService _instance = AuthService._internal();
  factory AuthService() => _instance;

  late String? _token;
  late User? _currentUser;

  // HttpClient - lazy initialization
  HttpClient? _httpClient;

  AuthService._internal() {
    _token = null;
    _currentUser = null;
  }

  /// Initialize HttpClient secara lazy
  HttpClient _getHttpClient() {
    if (_httpClient == null) {
      _httpClient = HttpClient();
      _httpClient!.badCertificateCallback = (cert, host, port) =>
          true; // Accept all certs (development only)
      _httpClient!.connectionTimeout = const Duration(seconds: 30);
    }
    return _httpClient!;
  }

  String? get token => _token;
  User? get currentUser => _currentUser;
  bool get isAuthenticated => _token != null;

  // Load token from local storage
  Future<void> loadToken() async {
    final prefs = await SharedPreferences.getInstance();
    _token = prefs.getString(AppConfig.tokenKey);

    final userJson = prefs.getString(AppConfig.userKey);
    if (userJson != null) {
      _currentUser = User.fromJson(jsonDecode(userJson));
    }
  }

  // Save token to local storage
  Future<void> _saveToken(String token, User user) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(AppConfig.tokenKey, token);
    await prefs.setString(AppConfig.userKey, jsonEncode(user.toJson()));
    _token = token;
    _currentUser = user;
  }

  // Clear token (logout)
  Future<void> clearToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(AppConfig.tokenKey);
    await prefs.remove(AppConfig.userKey);
    await prefs.remove(AppConfig.deviceTokenKey);
    _token = null;
    _currentUser = null;
  }

  Future<void> saveDeviceToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(AppConfig.deviceTokenKey, token);
  }

  Future<String?> loadDeviceToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(AppConfig.deviceTokenKey);
  }

  /// Tangani response 401 secara terpusat.
  /// Token lokal dibersihkan agar app tidak terus memakai token invalid.
  Future<bool> handleUnauthorized(int statusCode) async {
    if (statusCode == 401) {
      await clearToken();
      return true;
    }
    return false;
  }

  /// Custom HTTP POST dengan HttpClient yang accept semua cert
  Future<http.Response> _customPost(
    Uri uri, {
    required Map<String, String> headers,
    required String body,
  }) async {
    debugPrint('[HTTP] POST $uri');
    debugPrint('[HTTP] Headers: $headers');
    debugPrint('[HTTP] Body: $body');

    try {
      final client = _getHttpClient();
      final request = await client.postUrl(uri);
      headers.forEach((key, value) {
        request.headers.add(key, value);
      });
      request.write(body);

      final response = await request.close().timeout(
        const Duration(seconds: 60),
      );
      final responseBody = await response.transform(utf8.decoder).join();

      debugPrint('[HTTP] Status: ${response.statusCode}');
      debugPrint('[HTTP] Response: $responseBody');

      return http.Response(responseBody, response.statusCode);
    } on SocketException catch (e) {
      debugPrint('[HTTP] SocketException: $e');
      rethrow;
    } on TimeoutException catch (e) {
      debugPrint('[HTTP] TimeoutException: $e');
      rethrow;
    } catch (e) {
      debugPrint('[HTTP] Exception: $e');
      rethrow;
    }
  }

  // Register
  Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
    String? phone,
  }) async {
    try {
      final url = '${AppConfig.baseUrl}/register';
      debugPrint('[REGISTER] Attempting POST to: $url');

      final response = await _customPost(
        Uri.parse(url),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'name': name,
          'email': email,
          'password': password,
          'password_confirmation': passwordConfirmation,
          'phone': phone,
        }),
      );

      debugPrint('Register response status: ${response.statusCode}');
      debugPrint(
        'Register response body: ${response.body.substring(0, math.min(response.body.length, 300))}',
      );

      Map<String, dynamic>? data;
      String? rawBody;
      try {
        final decoded = jsonDecode(response.body);
        if (decoded is Map) {
          data = Map<String, dynamic>.from(decoded);
        } else {
          rawBody = response.body;
        }
      } catch (_) {
        rawBody = response.body;
      }

      if (response.statusCode == 201 &&
          data != null &&
          (data['success'] ?? false) == true) {
        try {
          final token = data['data']?['token'];
          final userData = data['data']?['user'];

          if (token == null || userData == null) {
            return {
              'success': false,
              'message': 'Invalid response format from server',
            };
          }

          final user = User.fromJson(userData);
          await _saveToken(token, user);
          return {
            'success': true,
            'message': data['message'] ?? 'Registrasi berhasil',
          };
        } catch (e) {
          debugPrint('Error parsing registration user data: $e');
          return {'success': false, 'message': 'Error parsing user data'};
        }
      }

      final preview = response.body.length > 300
          ? response.body.substring(0, 300)
          : response.body;
      debugPrint('Register failed: ${response.statusCode} $preview');

      return {
        'success': false,
        'message': data?['message'] ?? 'Registrasi gagal',
        'errors': data?['errors'],
        'status': response.statusCode,
        'error_code': data?['error_code'],
        'raw_body': rawBody,
      };
    } on SocketException catch (e) {
      debugPrint('SocketException in register: $e');
      return {
        'success': false,
        'message': 'Gagal terhubung ke server. Periksa koneksi internet.',
      };
    } on TimeoutException catch (e) {
      debugPrint('TimeoutException in register: $e');
      return {
        'success': false,
        'message': 'Registrasi timeout. Server tidak merespons.',
      };
    } catch (e) {
      debugPrint('Register exception type: ${e.runtimeType}');
      debugPrint('Register exception: $e');
      debugPrint('Register stack trace: ${StackTrace.current}');
      return {'success': false, 'message': 'Error: ${e.runtimeType} - $e'};
    }
  }

  // Login
  Future<Map<String, dynamic>> login({
    required String email,
    required String password,
  }) async {
    try {
      final url = '${AppConfig.baseUrl}/login';
      debugPrint('[LOGIN] Attempting POST to: $url');

      final response = await _customPost(
        Uri.parse(url),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({'email': email, 'password': password}),
      );

      debugPrint('Login response status: ${response.statusCode}');
      debugPrint(
        'Login response body: ${response.body.substring(0, math.min(response.body.length, 300))}',
      );

      Map<String, dynamic>? data;
      try {
        final decoded = jsonDecode(response.body);
        if (decoded is Map) {
          data = Map<String, dynamic>.from(decoded);
        }
      } catch (e) {
        debugPrint('Failed to parse login response: $e');
      }

      if (response.statusCode == 200 &&
          data != null &&
          (data['success'] ?? false) == true) {
        try {
          final token = data['data']?['token'];
          final userData = data['data']?['user'];

          if (token == null || userData == null) {
            return {
              'success': false,
              'message': 'Invalid response format from server',
            };
          }

          final user = User.fromJson(userData);
          await _saveToken(token, user);
          return {
            'success': true,
            'message': data['message'] ?? 'Login berhasil',
          };
        } catch (e) {
          debugPrint('Error parsing user data: $e');
          return {'success': false, 'message': 'Error parsing user data'};
        }
      } else {
        final errorMessage =
            data?['message'] ?? data?['error'] ?? 'Login gagal';

        return {
          'success': false,
          'message': errorMessage,
          'status': response.statusCode,
        };
      }
    } on SocketException {
      return {
        'success': false,
        'message': 'Gagal terhubung ke server. Periksa koneksi internet.',
      };
    } on TimeoutException {
      return {
        'success': false,
        'message': 'Login timeout. Server tidak merespons.',
      };
    } catch (e) {
      debugPrint('Login exception: $e');
      return {'success': false, 'message': 'Terjadi kesalahan: $e'};
    }
  }

  // Logout
  Future<Map<String, dynamic>> logout() async {
    try {
      if (_token == null) {
        await clearToken();
        return {'success': true};
      }

      final deviceToken = await loadDeviceToken();
      if (deviceToken != null) {
        await unregisterDeviceToken(deviceToken);
      }

      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/logout'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $_token',
        },
      );

      await clearToken();

      if (response.statusCode == 200) {
        return {'success': true, 'message': 'Logout berhasil'};
      } else {
        return {'success': true}; // Still clear local token
      }
    } catch (e) {
      await clearToken(); // Clear local token anyway
      return {'success': true};
    }
  }

  // Get current user
  Future<User?> getCurrentUser() async {
    if (_token == null) return null;

    try {
      final response = await http.get(
        Uri.parse('${AppConfig.baseUrl}/user'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $_token',
        },
      );

      if (response.statusCode == 200) {
        final user = User.fromJson(jsonDecode(response.body));
        _currentUser = user;
        return user;
      }
      return null;
    } catch (e) {
      return null;
    }
  }

  // Update profile
  Future<Map<String, dynamic>> updateProfile({
    required String name,
    required String email,
    String? phone,
  }) async {
    try {
      final response = await http.put(
        Uri.parse('${AppConfig.baseUrl}/profile/update'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $_token',
        },
        body: jsonEncode({'name': name, 'email': email, 'phone': phone}),
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        final user = User.fromJson(data['data']);
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString(AppConfig.userKey, jsonEncode(user.toJson()));
        _currentUser = user;
        return {'success': true, 'message': data['message']};
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal update profil',
          'errors': data['errors'],
        };
      }
    } catch (e) {
      return {'success': false, 'message': 'Error: $e'};
    }
  }

  // Change password
  Future<Map<String, dynamic>> changePassword({
    required String password,
    required String passwordConfirmation,
  }) async {
    if (_currentUser == null || _token == null) {
      return {'success': false, 'message': 'Silakan login ulang'};
    }
    try {
      final response = await http.put(
        Uri.parse('${AppConfig.baseUrl}/profile/update'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $_token',
        },
        body: jsonEncode({
          'name': _currentUser!.name,
          'email': _currentUser!.email,
          'password': password,
          'password_confirmation': passwordConfirmation,
        }),
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        return {'success': true, 'message': 'Password berhasil diubah'};
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal mengubah password',
          'errors': data['errors'],
        };
      }
    } catch (e) {
      return {'success': false, 'message': 'Error: $e'};
    }
  }

  Future<void> registerDeviceToken({
    required String token,
    String? platform,
  }) async {
    if (_token == null) return;

    try {
      await http.post(
        Uri.parse('${AppConfig.baseUrl}/device-tokens'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $_token',
        },
        body: jsonEncode({
          'token': token,
          if (platform != null) 'platform': platform,
        }),
      );
      await saveDeviceToken(token);
    } catch (_) {
      // Non-blocking: ignore errors
    }
  }

  Future<void> unregisterDeviceToken(String token) async {
    if (_token == null) return;

    try {
      await http.delete(
        Uri.parse('${AppConfig.baseUrl}/device-tokens'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $_token',
        },
        body: jsonEncode({'token': token}),
      );
    } catch (_) {
      // Non-blocking: ignore errors
    }
  }
}
