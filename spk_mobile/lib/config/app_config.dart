import 'package:flutter/foundation.dart';

class AppConfig {
  // ============================================================================
  // BASE URL CONFIGURATION
  // ============================================================================

  /// Host server untuk production (override via --dart-define=PROD_SERVER_URL=...).
  static const String _productionServer = String.fromEnvironment(
    'PROD_SERVER_URL',
    // defaultValue: 'http://10.133.61.99:8000',
    defaultValue: 'https://spk-kontrakan.taskbuddy.web.id',
  );

  /// Host server untuk development (override via --dart-define=DEV_SERVER_URL=...).
  static const String _developmentServer = String.fromEnvironment(
    'DEV_SERVER_URL',
    // defaultValue: 'http://10.133.61.99:8000',
    defaultValue: 'https://spk-kontrakan.taskbuddy.web.id',
  );

  static String _serverUrl = _normalizeServerUrl(
    kReleaseMode ? _productionServer : _developmentServer,
  );

  static String get serverUrl => _serverUrl;

  /// API Laravel
  static String get baseUrl => '$_serverUrl/api';

  /// URL gambar/file public
  static String get storageUrl => _serverUrl;

  /// Override server URL jika perlu
  static void setServerUrl(String url) {
    _serverUrl = _normalizeServerUrl(url);
  }

  /// Normalisasi URL agar selalu konsisten untuk request API.
  static String _normalizeServerUrl(String url) {
    final raw = url.trim();
    if (raw.isEmpty) return _developmentServer;

    var normalized = raw;

    // Default ke HTTP jika skema tidak dicantumkan.
    if (!normalized.startsWith('http://') &&
        !normalized.startsWith('https://')) {
      normalized = 'http://$normalized';
    }

    // Hilangkan trailing slash agar baseUrl tidak menjadi //api.
    while (normalized.endsWith('/')) {
      normalized = normalized.substring(0, normalized.length - 1);
    }

    final uri = Uri.tryParse(normalized);
    if (uri == null) return _developmentServer;

    // Untuk host lokal/IP tanpa port, default ke 8000.
    if (!uri.hasPort &&
        (uri.host == 'localhost' ||
            RegExp(r'^\d+\.\d+\.\d+\.\d+$').hasMatch(uri.host))) {
      normalized = '${uri.scheme}://${uri.host}:8000';
    }

    return normalized;
  }

  // Timeouts
  static const Duration connectionTimeout = Duration(seconds: 30);
  static const Duration receiveTimeout = Duration(seconds: 30);

  // Local Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
  static const String deviceTokenKey = 'device_token';
}
