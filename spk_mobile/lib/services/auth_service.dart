import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'dart:math' as math;
import 'package:flutter/foundation.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';
import '../services/server_discovery_service.dart';
import '../models/user.dart';

class AuthService {
  // Singleton pattern
  static final AuthService _instance = AuthService._internal();
  factory AuthService() => _instance;

  late String? _token;
  late User? _currentUser;

  // HttpClient - lazy initialization
  HttpClient? _httpClient;

  // âœ… Secure storage for sensitive data (token & user)
  static const FlutterSecureStorage _secureStorage = FlutterSecureStorage(
    aOptions: AndroidOptions(encryptedSharedPreferences: true),
    iOptions: IOSOptions(
      accessibility: KeychainAccessibility.first_unlock_this_device,
    ),
  );

  AuthService._internal() {
    _token = null;
    _currentUser = null;
  }

  /// Initialize HttpClient secara lazy
  HttpClient _getHttpClient() {
    if (_httpClient == null) {
      _httpClient = HttpClient();

      // âœ… Security: NEVER bypass certificate validation in release builds.
      // In debug builds, allow self-signed certs only for local dev hosts.
      if (kDebugMode) {
        _httpClient!.badCertificateCallback = (cert, host, port) {
          return host == 'localhost' ||
              host == '127.0.0.1' ||
              host.startsWith('192.168.') ||
              host.startsWith('10.') ||
              host.startsWith('172.16.') ||
              host.startsWith('172.17.') ||
              host.startsWith('172.18.') ||
              host.startsWith('172.19.') ||
              host.startsWith('172.2') ||
              host.startsWith('172.30.') ||
              host.startsWith('172.31.');
        };
      }

      _httpClient!.connectionTimeout = const Duration(seconds: 30);
    }
    return _httpClient!;
  }

  String _redactSensitive(String input) {
    // Redact common sensitive fields from JSON-ish strings.
    var out = input;
    out = out.replaceAll(
      RegExp(r'("token"\s*:\s*")([^"]+)(")', caseSensitive: false),
      r'$1***$3',
    );
    out = out.replaceAll(
      RegExp(r'("password"\s*:\s*")([^"]+)(")', caseSensitive: false),
      r'$1***$3',
    );
    out = out.replaceAll(
      RegExp(r'("authorization"\s*:\s*")([^"]+)(")', caseSensitive: false),
      r'$1***$3',
    );
    return out;
  }

  String? get token => _token;
  User? get currentUser => _currentUser;
  bool get isAuthenticated => _token != null;

  // Load token from local storage
  Future<void> loadToken() async {
    try {
      _token = await _secureStorage.read(key: AppConfig.tokenKey);
      final userJson = await _secureStorage.read(key: AppConfig.userKey);
      if (userJson != null) {
        _currentUser = User.fromJson(jsonDecode(userJson));
      }

      // âœ… Migration: if secure storage empty, try legacy SharedPreferences once.
      if (_token == null) {
        final prefs = await SharedPreferences.getInstance();
        final legacyToken = prefs.getString(AppConfig.tokenKey);
        final legacyUserJson = prefs.getString(AppConfig.userKey);

        if (legacyToken != null) {
          await _secureStorage.write(
            key: AppConfig.tokenKey,
            value: legacyToken,
          );
          await prefs.remove(AppConfig.tokenKey);
          _token = legacyToken;
        }

        if (legacyUserJson != null) {
          await _secureStorage.write(
            key: AppConfig.userKey,
            value: legacyUserJson,
          );
          await prefs.remove(AppConfig.userKey);
          try {
            _currentUser = User.fromJson(jsonDecode(legacyUserJson));
          } catch (_) {
            // ignore parse errors
          }
        }
      }
    } catch (e) {
      debugPrint('Failed to load auth session: $e');
      _token = null;
      _currentUser = null;
    }
  }

  // Save token to local storage
  Future<void> _saveToken(String token, User user) async {
    try {
      await _secureStorage.write(key: AppConfig.tokenKey, value: token);
      await _secureStorage.write(
        key: AppConfig.userKey,
        value: jsonEncode(user.toJson()),
      );
      _token = token;
      _currentUser = user;
    } catch (e) {
      debugPrint('Failed to save auth session: $e');
      _token = token;
      _currentUser = user;
    }
  }

  // Clear token (logout)
  Future<void> clearToken() async {
    try {
      await _secureStorage.delete(key: AppConfig.tokenKey);
      await _secureStorage.delete(key: AppConfig.userKey);
      await _secureStorage.delete(key: AppConfig.deviceTokenKey);
    } catch (e) {
      debugPrint('Failed to clear auth session: $e');
    } finally {
      _token = null;
      _currentUser = null;
    }
  }

  Future<void> saveDeviceToken(String token) async {
    await _secureStorage.write(key: AppConfig.deviceTokenKey, value: token);
  }

  Future<String?> loadDeviceToken() async {
    return _secureStorage.read(key: AppConfig.deviceTokenKey);
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
    if (kDebugMode) {
      // Avoid printing secrets in logs.
      debugPrint('[HTTP] Headers: $headers');
      debugPrint('[HTTP] Body: (redacted)');
    }

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
      if (kDebugMode) {
        final preview = responseBody.substring(
          0,
          math.min(responseBody.length, 500),
        );
        debugPrint('[HTTP] Response: ${_redactSensitive(preview)}');
      }

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
    bool allowRetry = true,
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
      if (kDebugMode) {
        final preview = response.body.substring(
          0,
          math.min(response.body.length, 300),
        );
        debugPrint('Register response preview: ${_redactSensitive(preview)}');
      }

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

      // Coba temukan server lagi lalu retry sekali.
      if (allowRetry && await _rediscoverServer()) {
        try {
          return await register(
            name: name,
            email: email,
            password: password,
            passwordConfirmation: passwordConfirmation,
            phone: phone,
            allowRetry: false,
          );
        } catch (_) {}
      }

      return {
        'success': false,
        'message': 'Gagal terhubung ke server. Periksa koneksi internet.',
      };
    } on TimeoutException catch (e) {
      debugPrint('TimeoutException in register: $e');

      if (allowRetry && await _rediscoverServer()) {
        try {
          return await register(
            name: name,
            email: email,
            password: password,
            passwordConfirmation: passwordConfirmation,
            phone: phone,
            allowRetry: false,
          );
        } catch (_) {}
      }

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
    bool allowRetry = true,
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
      if (kDebugMode) {
        final preview = response.body.substring(
          0,
          math.min(response.body.length, 300),
        );
        debugPrint('Login response preview: ${_redactSensitive(preview)}');
      }

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
      if (allowRetry && await _rediscoverServer()) {
        return login(email: email, password: password, allowRetry: false);
      }

      return {
        'success': false,
        'message': 'Gagal terhubung ke server. Periksa koneksi internet.',
      };
    } on TimeoutException {
      if (allowRetry && await _rediscoverServer()) {
        return login(email: email, password: password, allowRetry: false);
      }

      return {
        'success': false,
        'message': 'Login timeout. Server tidak merespons.',
      };
    } catch (e) {
      debugPrint('Login exception: $e');
      return {'success': false, 'message': 'Terjadi kesalahan: $e'};
    }
  }

  /// Coba temukan kembali server lokal/LAN jika URL tersimpan sudah tidak valid.
  Future<bool> _rediscoverServer() async {
    try {
      await ServerDiscoveryService.resetCache();
      return await ServerDiscoveryService.discover();
    } catch (e) {
      debugPrint('Server rediscovery failed: $e');
      return false;
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
  // Get current user
  Future<User?> getCurrentUser() async {
    if (_token == null) {
      return _currentUser;
    }

    try {
      final response = await http
          .get(
            Uri.parse('${AppConfig.baseUrl}/user'),
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'Authorization': 'Bearer $_token',
            },
          )
          .timeout(AppConfig.connectionTimeout);

      if (response.statusCode == 200 && response.body.isNotEmpty) {
        final decoded = jsonDecode(response.body);

        // Mendukung respons API langsung atau respons yang dibungkus data/user.
        Map<String, dynamic>? userData;

        if (decoded is Map<String, dynamic>) {
          if (decoded['data'] is Map) {
            userData = Map<String, dynamic>.from(decoded['data']);
          } else if (decoded['user'] is Map) {
            userData = Map<String, dynamic>.from(decoded['user']);
          } else {
            userData = decoded;
          }
        }

        if (userData != null) {
          final user = User.fromJson(userData);

          // Jangan menimpa data lokal dengan user kosong dari respons yang tidak valid.
          if (user.name.trim().isNotEmpty || user.email.trim().isNotEmpty) {
            _currentUser = user;

            await _secureStorage.write(
              key: AppConfig.userKey,
              value: jsonEncode(user.toJson()),
            );

            return user;
          }
        }
      }

      // Tetap tampilkan data yang tersimpan bila server tidak memberi profil valid.
      return _currentUser;
    } catch (e) {
      return _currentUser;
    }
  }

  /// Mengirim ulang email verifikasi
  Future<Map<String, dynamic>> resendVerificationEmail() async {
    if (_token == null) {
      return {'success': false, 'message': 'Sesi telah habis. Silakan login kembali.'};
    }

    try {
      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}/email/verification-notification'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $_token',
        },
      ).timeout(AppConfig.connectionTimeout);

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200) {
        return {
          'success': true,
          'message': decoded['message'] ?? 'Tautan verifikasi terkirim!'
        };
      } else {
        return {
          'success': false,
          'message': decoded['message'] ?? 'Gagal mengirim email verifikasi.'
        };
      }
    } catch (e) {
      return {'success': false, 'message': 'Koneksi bermasalah: $e'};
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
        await _secureStorage.write(
          key: AppConfig.userKey,
          value: jsonEncode(user.toJson()),
        );
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
