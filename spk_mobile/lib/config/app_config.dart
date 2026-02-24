class AppConfig {
  // Base URL - Ganti sesuai environment
  // Windows Desktop: http://localhost:8000
  // Android Emulator: http://10.0.2.2:8000
  // iOS Simulator: http://localhost:8000
  // Real Device: http://192.168.18.16:8000 (IP komputer Anda)

  // CATATAN: Untuk real device, ganti IP sesuai dengan IP komputer Anda
  // Cek IP dengan: ipconfig (di Windows) atau ifconfig (di Linux/Mac)
  static const String baseUrl = 'http://192.168.18.16:8000/api';
  static const String storageUrl = 'http://192.168.18.16:8000/storage';

  // Timeouts
  static const Duration connectionTimeout = Duration(seconds: 10);
  static const Duration receiveTimeout = Duration(seconds: 10);

  // Local Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
}
