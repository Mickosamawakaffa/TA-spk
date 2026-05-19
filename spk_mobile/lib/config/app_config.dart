class AppConfig {
  // ============================================================================
  // 🔧 BASE URL CONFIGURATION - FIXED IP (NOT AUTO-DETECT)
  // ============================================================================
  // PENTING: Update IP ini sesuai dengan server backend Anda!
  // Format: http://[IP_ADDRESS]:[PORT]
  //
  // Contoh:
  // - Local: http://localhost:8000
  // - Network: http://192.168.1.100:8000
  // - Remote: http://your-server.com
  //
  // ⚠️ JIKA CONNECTION TIMEOUT:
  // - Pastikan backend server running
  // - Pastikan device/emulator connect ke WiFi yang sama
  // - Pastikan port sudah benar
  // ============================================================================

  /// Backend server URL - DIUBAH MENJADI FIXED (tidak auto-detect)
  /// Alasan: ServerDiscoveryService scanning port yang salah
  ///
  /// UPDATE INI SESUAI DENGAN BACKEND ANDA!
  /// Jika backend di: php artisan serve
  /// Maka gunakan: http://127.0.0.1:8000 (localhost) atau http://[IP]:8000 (network)
  static const String _defaultServer = 'http://10.192.233.99:8000';

  // Runtime values — bisa di-override via setServerUrl() jika perlu
  static String _serverUrl = _defaultServer;

  static String get serverUrl => _serverUrl;
  static String get baseUrl => '$_serverUrl/api';
  static String get storageUrl => '$_serverUrl/storage';

  /// Override server URL jika perlu (untuk testing atau switching server)
  static void setServerUrl(String url) {
    _serverUrl = url;
  }

  // Timeouts - Increased untuk network yang lambat dan operations yang heavy (password hashing, db insert)
  static const Duration connectionTimeout = Duration(seconds: 30);

  // ✅ UPDATED: Backend base URL sesuai IP hotspot HP (10.119.236.99:8000)
  static const Duration receiveTimeout = Duration(seconds: 30);

  // Local Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
  static const String deviceTokenKey = 'device_token';
}
