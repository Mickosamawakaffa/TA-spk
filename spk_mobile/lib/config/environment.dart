// Environment configuration yang dapat berubah runtime
class Environment {
  // 💻 For Development
  // Android Emulator = http://10.0.2.2:8000
  // iOS Simulator = http://localhost:8000  
  // Real Device = Ganti dengan IP komputer Anda
  
  // Pilih sesuai platform dan environment saat development
  static const String apiBaseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://192.168.1.154:8000', // Default IP lokal
  );

  static const String storageBaseUrl = String.fromEnvironment(
    'STORAGE_BASE_URL',
    defaultValue: 'http://192.168.1.154:8000/storage',
  );

  // Mode debugging
  static const bool isDebugMode = true;
}

// Catatan: Untuk production:
// flutter run --dart-define=API_BASE_URL=https://api.production.com
