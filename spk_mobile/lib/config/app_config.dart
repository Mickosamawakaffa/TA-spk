class AppConfig {
  // ============================================================================
  // 🔧 BASE URL CONFIGURATION - AUTO-DETECTED AT RUNTIME
  // ============================================================================
  // URL ini otomatis dideteksi saat app startup via ServerDiscoveryService.
  // Fallback default: IP terakhir yang berhasil terhubung.
  //
  // ✅ Tidak perlu update manual lagi!
  // ============================================================================

  // Default fallback (dipakai jika auto-detect gagal)
  static const String _defaultServer = 'http://192.168.18.16:8000';

  // Runtime values — diupdate otomatis oleh ServerDiscoveryService
  static String _serverUrl = _defaultServer;

  static String get serverUrl => _serverUrl;
  static String get baseUrl => '$_serverUrl/api';
  static String get storageUrl => '$_serverUrl/storage';

  /// Dipanggil oleh ServerDiscoveryService setelah server ditemukan
  static void setServerUrl(String url) {
    _serverUrl = url;
  }

  // Timeouts
  static const Duration connectionTimeout = Duration(seconds: 10);
  static const Duration receiveTimeout = Duration(seconds: 10);

  // Local Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
}
