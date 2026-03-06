import 'dart:async';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';

/// Service untuk mendeteksi IP server Laravel secara otomatis.
/// Urutan pencarian:
///   1. IP tersimpan di cache (paling cepat jika masih valid)
///   2. Android Emulator (10.0.2.2)
///   3. Localhost (desktop / iOS simulator)
///   4. Scan subnet lokal HP secara paralel
class ServerDiscoveryService {
  static const int _port = 8000;
  static const String _cacheKey = 'discovered_server_url';
  static const Duration _probeTimeout = Duration(seconds: 2);

  // ─── PUBLIC ENTRY POINT ─────────────────────────────────────────────────────

  /// Temukan server dan update [AppConfig] secara otomatis.
  /// Kembalikan `true` jika server ditemukan, `false` jika tidak.
  static Future<bool> discover({
    void Function(String status)? onStatus,
  }) async {
    onStatus?.call('Mencari server...');

    // 1. Coba cache terlebih dahulu
    final cached = await _getCachedUrl();
    if (cached != null) {
      onStatus?.call('Mencoba koneksi tersimpan...');
      if (await _isAlive(cached)) {
        AppConfig.setServerUrl(cached);
        return true;
      }
    }

    // 2. Android Emulator
    const emulatorUrl = 'http://10.0.2.2:$_port';
    if (await _isAlive(emulatorUrl)) {
      await _saveCache(emulatorUrl);
      AppConfig.setServerUrl(emulatorUrl);
      return true;
    }

    // 3. Localhost (Windows/iOS)
    const localhostUrl = 'http://localhost:$_port';
    if (await _isAlive(localhostUrl)) {
      await _saveCache(localhostUrl);
      AppConfig.setServerUrl(localhostUrl);
      return true;
    }

    // 4. Scan subnet lokal HP
    onStatus?.call('Memindai jaringan lokal...');
    final subnets = await _getLocalSubnets();
    if (subnets.isNotEmpty) {
      final found = await _scanSubnets(subnets, onStatus: onStatus);
      if (found != null) {
        await _saveCache(found);
        AppConfig.setServerUrl(found);
        return true;
      }
    }

    // Gagal — tetap pakai nilai AppConfig yang lama (last known)
    return false;
  }

  // ─── HELPERS ────────────────────────────────────────────────────────────────

  /// Cek apakah server ada di URL tersebut (respons apapun = server hidup).
  static Future<bool> _isAlive(String serverUrl) async {
    try {
      final response = await http
          .get(
            Uri.parse('$serverUrl/api'),
            headers: {'Accept': 'application/json'},
          )
          .timeout(_probeTimeout);
      // Respons 4xx/5xx tetap berarti server bisa dijangkau
      return response.statusCode > 0;
    } catch (_) {
      return false;
    }
  }

  /// Ambil semua subnet IPv4 milik device (e.g., "192.168.1").
  static Future<List<String>> _getLocalSubnets() async {
    final subnets = <String>[];
    try {
      final interfaces = await NetworkInterface.list(
        type: InternetAddressType.IPv4,
        includeLinkLocal: false,
      );
      for (final iface in interfaces) {
        for (final addr in iface.addresses) {
          final parts = addr.address.split('.');
          if (parts.length == 4 && parts[0] != '127') {
            final subnet = '${parts[0]}.${parts[1]}.${parts[2]}';
            if (!subnets.contains(subnet)) subnets.add(subnet);
          }
        }
      }
    } catch (_) {}
    return subnets;
  }

  /// Scan semua host di subnet secara paralel, kembalikan URL pertama yang aktif.
  static Future<String?> _scanSubnets(
    List<String> subnets, {
    void Function(String)? onStatus,
  }) async {
    final completer = Completer<String?>();
    int remaining = subnets.length * 254;

    if (remaining == 0) return null;

    for (final subnet in subnets) {
      for (int i = 1; i <= 254; i++) {
        final url = 'http://$subnet.$i:$_port';
        _isAlive(url).then((alive) {
          if (alive && !completer.isCompleted) {
            completer.complete(url);
          }
          remaining--;
          if (remaining <= 0 && !completer.isCompleted) {
            completer.complete(null);
          }
        });
      }
    }

    return completer.future;
  }

  static Future<String?> _getCachedUrl() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_cacheKey);
  }

  static Future<void> _saveCache(String serverUrl) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_cacheKey, serverUrl);
  }
}
