import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:geolocator/geolocator.dart';
import '../config/app_config.dart';
import '../models/kontrakan.dart';
import '../models/laundry.dart';
import '../models/user.dart';
import '../services/auth_service.dart';
import '../services/server_discovery_service.dart';
import '../widgets/kontrakan_card.dart';
import '../widgets/laundry_card.dart';

class RecommendationScreen extends StatefulWidget {
  final String category;

  const RecommendationScreen({Key? key, required this.category})
    : super(key: key);

  @override
  State<RecommendationScreen> createState() => _RecommendationScreenState();
}

class _RecommendationScreenState extends State<RecommendationScreen> {
  bool _isLoading = false;
  bool _hasCalculated = false;
  List<dynamic> _recommendations = [];
  String? _errorMessage;
  bool _noData =
      false; // true = memang tidak ada data, false = filter terlalu ketat

  // User info
  final _authService = AuthService();
  User? _currentUser;

  // Bobot values (percentage, total must = 100)
  // Default: profil mahasiswa
  int _bobotHarga = 50;
  int _bobotJarak = 20;
  int _bobotKriteria3 = 15; // jumlah_kamar (kontrakan) or kecepatan (laundry)
  int _bobotKriteria4 = 15; // fasilitas (kontrakan) or layanan (laundry)

  // Jenis layanan selection for laundry
  String _selectedJenisLayanan = 'harian';

  // Location values untuk referensi jarak (deteksi lokasi user)
  double? _userLatitude;
  double? _userLongitude;
  bool _isDetectingLocation = false;

  String get _kriteria3Label =>
      widget.category == 'kontrakan' ? 'Jumlah Kamar' : 'Kecepatan Layanan';
  String get _kriteria4Label =>
      widget.category == 'kontrakan' ? 'Fasilitas' : 'Variasi Layanan';

  int get _totalBobot =>
      _bobotHarga + _bobotJarak + _bobotKriteria3 + _bobotKriteria4;

  Color get _categoryColor => widget.category == 'kontrakan'
      ? const Color(0xFF1565C0)
      : const Color(0xFF00897B);

  @override
  void initState() {
    super.initState();
    // Default bobot: profil mahasiswa
    _bobotHarga = 50;
    _bobotJarak = 20;
    _bobotKriteria3 = 15;
    _bobotKriteria4 = 15;

    // Load user info
    _loadUser();

    // Auto-detect lokasi untuk laundry
    if (widget.category == 'laundry') {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        _detectUserLocation();
      });
    }
  }

  Future<void> _loadUser() async {
    try {
      // Use cached user first (no API call needed)
      if (_authService.currentUser != null) {
        if (!mounted) return;
        setState(() => _currentUser = _authService.currentUser);
        return;
      }
      // Fallback: load from SharedPreferences via API
      await _authService.loadToken();
      if (!mounted) return;
      setState(() => _currentUser = _authService.currentUser);
    } catch (e) {
      debugPrint('Load user error: $e');
    }
  }

  /// Auto-balance: when one bobot changes, redistribute the remaining
  /// percentage proportionally among the other three (min 10% each),
  /// always snapping to multiples of 5 so displayed values match actual sum.
  void _updateBobot(int index, int newValue) {
    setState(() {
      List<int> bobots = [
        _bobotHarga,
        _bobotJarak,
        _bobotKriteria3,
        _bobotKriteria4,
      ];
      if (newValue == bobots[index]) return;

      // Clamp newValue to valid range and snap to multiple of 5
      newValue = ((newValue.clamp(10, 70)) ~/ 5) * 5;
      bobots[index] = newValue;

      int remaining = 100 - newValue;
      List<int> otherIdx = [0, 1, 2, 3].where((i) => i != index).toList();
      int otherSum = otherIdx.fold(0, (sum, i) => sum + bobots[i]);

      List<int> newOther = List.filled(otherIdx.length, 0);

      if (otherSum == 0) {
        // Edge case: distribute equally in steps of 5
        int share = ((remaining / otherIdx.length / 5).round() * 5).clamp(
          10,
          70,
        );
        for (int i = 0; i < otherIdx.length; i++) {
          newOther[i] = share;
        }
      } else {
        // Proportional redistribution snapped to nearest multiple of 5
        for (int i = 0; i < otherIdx.length - 1; i++) {
          double proportional = bobots[otherIdx[i]] * remaining / otherSum;
          newOther[i] = ((proportional / 5).round() * 5).clamp(10, 70);
        }
        // Last one gets exact remainder to guarantee total = 100
        int distributed = newOther
            .take(newOther.length - 1)
            .fold(0, (a, b) => a + b);
        int lastVal = remaining - distributed;
        newOther[newOther.length - 1] = lastVal.clamp(10, 70);
      }

      // Apply computed values
      for (int i = 0; i < otherIdx.length; i++) {
        bobots[otherIdx[i]] = newOther[i];
      }

      // Final safety: force total == 100 by adjusting the largest other bobot
      // without snapping to 5 (exact correction takes priority over pretty numbers)
      int total = bobots.reduce((a, b) => a + b);
      if (total != 100) {
        int diff = 100 - total;
        int adjustIdx = otherIdx.reduce(
          (a, b) => bobots[a] >= bobots[b] ? a : b,
        );
        bobots[adjustIdx] = (bobots[adjustIdx] + diff).clamp(5, 75);
      }

      _bobotHarga = bobots[0];
      _bobotJarak = bobots[1];
      _bobotKriteria3 = bobots[2];
      _bobotKriteria4 = bobots[3];
    });
  }

  /// Get the maximum dropdown value for a given bobot index
  int _getMaxBobot(int index) {
    int othersMin = 0;
    for (int i = 0; i < 4; i++) {
      if (i != index) othersMin += 10; // minimum 10% each
    }
    return (100 - othersMin).clamp(10, 70); // max 70
  }

  List<int> _getBobotOptionsFor(int index) {
    int max = _getMaxBobot(index);
    return List.generate(((max - 10) ~/ 5) + 1, (i) => 10 + i * 5);
  }

  int _closestOption(List<int> options, int target) {
    return options.reduce(
      (a, b) => (a - target).abs() <= (b - target).abs() ? a : b,
    );
  }

  String _getPriorityLabel(int value) {
    if (value <= 20) return 'Rendah';
    if (value <= 35) return 'Sedang';
    if (value <= 50) return 'Tinggi';
    return 'Prioritas';
  }

  Color _getPriorityColor(int value) {
    if (value <= 20) return const Color(0xFF8E8E93);
    if (value <= 35) return const Color(0xFF00A389);
    if (value <= 50) return const Color(0xFF2F80ED);
    return const Color(0xFF7B61FF);
  }

  bool _isConnectivityError(Object error) {
    final msg = error.toString().toLowerCase();
    return error is TimeoutException ||
        error is SocketException ||
        error is http.ClientException ||
        msg.contains('future not completed') ||
        msg.contains('connection') ||
        msg.contains('timed out');
  }

  Future<void> _resetConnectionAndRetry() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
      _noData = false;
    });

    try {
      await ServerDiscoveryService.resetCache();
      final found = await ServerDiscoveryService.discover();

      if (!mounted) return;

      if (found) {
        await _calculateSAW();
      } else {
        setState(() {
          _isLoading = false;
          _hasCalculated = true;
          _errorMessage =
              'Reset koneksi selesai, tetapi server belum ditemukan. Pastikan backend Laravel aktif di jaringan yang sama.';
        });
      }
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _isLoading = false;
        _hasCalculated = true;
        _errorMessage = 'Gagal reset koneksi: $e';
      });
    }
  }

  Future<void> _calculateSAW() async {
    if (_totalBobot != 100) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Total bobot harus 100%! Saat ini: $_totalBobot%'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    // Validate location for laundry
    if (widget.category == 'laundry' && _userLatitude == null) {
      // Auto-detect location first, then re-calculate
      await _detectUserLocation();
      if (!mounted) return;
      // If still no location after detection attempt, warn and stop
      if (_userLatitude == null || _userLongitude == null) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text(
              'Lokasi belum terdeteksi. Aktifkan GPS dan coba lagi.',
            ),
            backgroundColor: Colors.orange,
          ),
        );
        return;
      }
    }

    setState(() {
      _isLoading = true;
      _errorMessage = null;
      _noData = false;
    });

    final endpoint = widget.category == 'kontrakan'
        ? '/saw/calculate/kontrakan'
        : '/saw/calculate/laundry';

    try {
      final bodyParams = <String, dynamic>{};
      bodyParams['bobot_harga'] = _bobotHarga;
      bodyParams['bobot_jarak'] = _bobotJarak;

      if (widget.category == 'kontrakan') {
        bodyParams['bobot_jumlah_kamar'] = _bobotKriteria3;
        bodyParams['bobot_fasilitas'] = _bobotKriteria4;
      } else {
        bodyParams['bobot_kecepatan'] = _bobotKriteria3;
        bodyParams['bobot_layanan'] = _bobotKriteria4;
        bodyParams['jenis_layanan'] = _selectedJenisLayanan;
      }

      if (widget.category == 'laundry' &&
          _userLatitude != null &&
          _userLongitude != null) {
        bodyParams['user_lat'] = _userLatitude;
        bodyParams['user_lng'] = _userLongitude;
      }

      final response = await http
          .post(
            Uri.parse('${AppConfig.baseUrl}$endpoint'),
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
            },
            body: json.encode(bodyParams),
          )
          .timeout(const Duration(seconds: 15));

      if (!mounted) return;

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true) {
          setState(() {
            _recommendations = data['data']['hasil'] ?? [];
            _hasCalculated = true;
          });
        } else {
          setState(() {
            _errorMessage = data['message'] ?? 'Terjadi kesalahan';
            _hasCalculated = true;
          });
        }
      } else if (response.statusCode == 404) {
        final data = json.decode(response.body);
        setState(() {
          _errorMessage = data['message'] ?? 'Tidak ada data ditemukan';
          _noData = data['no_data'] == true;
          _recommendations = [];
          _hasCalculated = true;
        });
      } else {
        setState(() {
          _errorMessage = 'Gagal memuat data (${response.statusCode})';
          _hasCalculated = true;
        });
      }
    } catch (e) {
      debugPrint('SAW API Error: $e');
      debugPrint('URL: ${AppConfig.baseUrl}$endpoint');
      if (!mounted) return;
      final canReset = _isConnectivityError(e);
      setState(() {
        _errorMessage = canReset
            ? 'Tidak dapat terhubung ke server (${AppConfig.baseUrl}). Silakan tekan tombol Reset Koneksi.\n\nDetail: $e'
            : 'Tidak dapat terhubung ke server (${AppConfig.baseUrl}). Periksa koneksi internet Anda dan coba lagi.\n\nDetail: $e';
        _hasCalculated = true;
      });
    } finally {
      if (mounted) {
        setState(() {
          _isLoading = false;
        });
      }
    }
  }

  Future<void> _detectUserLocation() async {
    // Guard: prevent re-entrant / duplicate calls
    if (_isDetectingLocation) return;

    setState(() => _isDetectingLocation = true);
    try {
      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Aktifkan GPS Anda terlebih dahulu'),
            backgroundColor: Colors.red,
          ),
        );
        setState(() => _isDetectingLocation = false);
        return;
      }

      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
        if (permission == LocationPermission.denied) {
          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Izin lokasi ditolak'),
              backgroundColor: Colors.red,
            ),
          );
          setState(() => _isDetectingLocation = false);
          return;
        }
      }

      if (permission == LocationPermission.deniedForever) {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Izin lokasi ditolak permanen. Ubah di pengaturan.'),
            backgroundColor: Colors.red,
          ),
        );
        setState(() => _isDetectingLocation = false);
        return;
      }

      Position position =
          await Geolocator.getCurrentPosition(
            desiredAccuracy: LocationAccuracy.high,
          ).timeout(
            const Duration(seconds: 15),
            onTimeout: () => throw TimeoutException('Deteksi lokasi timeout'),
          );

      if (!mounted) return;
      setState(() {
        _userLatitude = position.latitude;
        _userLongitude = position.longitude;
        _isDetectingLocation = false;
      });

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Lokasi berhasil dideteksi!'),
          backgroundColor: Colors.green,
          duration: Duration(seconds: 2),
        ),
      );
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            'Gagal mendeteksi lokasi: ${e.toString().replaceAll('Exception: ', '')}',
          ),
          backgroundColor: Colors.red,
        ),
      );
      setState(() => _isDetectingLocation = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final categoryTitle = widget.category == 'kontrakan'
        ? 'Rekomendasi Kontrakan'
        : 'Rekomendasi Laundry';

    return Scaffold(
      backgroundColor: const Color(0xFFF3F7FB),
      appBar: AppBar(
        backgroundColor: _categoryColor,
        foregroundColor: Colors.white,
        elevation: 0,
        title: Text(
          categoryTitle,
          style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
        ),
        actions: [
          if (_hasCalculated)
            IconButton(
              icon: const Icon(Icons.refresh),
              tooltip: 'Hitung Ulang',
              onPressed: () => setState(() {
                _hasCalculated = false;
                _recommendations = [];
                _errorMessage = null;
                _noData = false;
              }),
            ),
        ],
      ),
      body: _hasCalculated ? _buildResultView() : _buildInputView(),
    );
  }

  // ===================== INPUT VIEW =====================
  Widget _buildInputView() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildUserInfoCard(),
          const SizedBox(height: 16),
          _buildSimpleStepCard(),
          const SizedBox(height: 16),
          _buildMethodInfoCard(),
          const SizedBox(height: 16),
          if (widget.category == 'laundry') ...[
            _buildJenisLayananSection(),
            const SizedBox(height: 16),
          ],
          _buildBobotSection(),
          const SizedBox(height: 16),
          if (widget.category == 'laundry') ...[
            _buildLocationSection(),
            const SizedBox(height: 16),
          ],
          _buildCalculateButton(),
          const SizedBox(height: 24),
        ],
      ),
    );
  }

  Widget _buildSimpleStepCard() {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFE4EDF7)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(Icons.route_rounded, size: 18, color: _categoryColor),
              const SizedBox(width: 8),
              const Text(
                'Langkah Cepat',
                style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700),
              ),
            ],
          ),
          const SizedBox(height: 10),
          _buildStepLine(
            '1',
            'Pilih prioritas yang paling penting untuk Anda.',
          ),
          const SizedBox(height: 6),
          _buildStepLine('2', 'Pastikan total prioritas 100%.'),
          const SizedBox(height: 6),
          _buildStepLine('3', 'Tekan Hitung, lalu pilih hasil teratas.'),
        ],
      ),
    );
  }

  Widget _buildStepLine(String number, String text) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          width: 20,
          height: 20,
          decoration: BoxDecoration(
            color: _categoryColor.withOpacity(0.12),
            shape: BoxShape.circle,
          ),
          child: Center(
            child: Text(
              number,
              style: TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.w700,
                color: _categoryColor,
              ),
            ),
          ),
        ),
        const SizedBox(width: 8),
        Expanded(
          child: Text(
            text,
            style: TextStyle(
              fontSize: 12,
              color: Colors.grey[700],
              height: 1.35,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildUserInfoCard() {
    final name = _currentUser?.name ?? '';
    final email = _currentUser?.email ?? '';
    final initials = name.isEmpty
        ? 'U'
        : name.trim().split(' ').length >= 2
        ? '${name.trim().split(' ')[0][0]}${name.trim().split(' ')[1][0]}'
              .toUpperCase()
        : name.trim()[0].toUpperCase();

    final hour = DateTime.now().hour;
    final greeting = hour < 11
        ? 'Selamat Pagi'
        : hour < 15
        ? 'Selamat Siang'
        : hour < 18
        ? 'Selamat Sore'
        : 'Selamat Malam';

    final categoryLabel = widget.category == 'kontrakan'
        ? 'kontrakan'
        : 'laundry';

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Row(
        children: [
          // Avatar
          Container(
            width: 52,
            height: 52,
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [_categoryColor, _categoryColor.withValues(alpha: 0.7)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              shape: BoxShape.circle,
              boxShadow: [
                BoxShadow(
                  color: _categoryColor.withValues(alpha: 0.3),
                  blurRadius: 8,
                  offset: const Offset(0, 3),
                ),
              ],
            ),
            child: Center(
              child: Text(
                initials,
                style: const TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.w800,
                  color: Colors.white,
                ),
              ),
            ),
          ),
          const SizedBox(width: 14),
          // Info
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  '$greeting,',
                  style: TextStyle(
                    fontSize: 12,
                    color: Colors.grey.shade500,
                    fontWeight: FontWeight.w500,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  name.isEmpty ? 'Pengguna' : name,
                  style: const TextStyle(
                    fontSize: 17,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFF1A1A2E),
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                if (email.isNotEmpty) ...[
                  const SizedBox(height: 2),
                  Text(
                    email,
                    style: TextStyle(fontSize: 12, color: Colors.grey.shade400),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ],
            ),
          ),
          // Category badge
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
            decoration: BoxDecoration(
              color: _categoryColor.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Icon(
                  widget.category == 'kontrakan'
                      ? Icons.home_work_rounded
                      : Icons.local_laundry_service_rounded,
                  size: 14,
                  color: _categoryColor,
                ),
                const SizedBox(width: 5),
                Text(
                  categoryLabel,
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w700,
                    color: _categoryColor,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMethodInfoCard() {
    final kriteria3 = widget.category == 'kontrakan'
        ? 'jumlah kamar'
        : 'kecepatan layanan';
    final kriteria4 = widget.category == 'kontrakan'
        ? 'fasilitas'
        : 'variasi layanan';

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [_categoryColor, _categoryColor.withValues(alpha: 0.8)],
        ),
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: _categoryColor.withOpacity(0.3),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Row(
            children: [
              Icon(Icons.analytics, color: Colors.white, size: 24),
              SizedBox(width: 8),
              Text(
                'Cara Rekomendasi Bekerja',
                style: TextStyle(
                  color: Colors.white,
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Text(
            'Pilih mana yang paling penting untuk Anda. Sistem akan mengurutkan hasil dari yang paling cocok.',
            style: TextStyle(
              color: Colors.white.withOpacity(0.9),
              fontSize: 13,
            ),
          ),
          const SizedBox(height: 8),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.2),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    const Icon(Icons.adjust, size: 14, color: Colors.white),
                    const SizedBox(width: 6),
                    Expanded(
                      child: Text(
                        '1. Atur prioritas harga, jarak, $kriteria3, dan $kriteria4.',
                        style: TextStyle(
                          color: Colors.white.withOpacity(0.95),
                          fontSize: 12,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 6),
                Row(
                  children: [
                    const Icon(Icons.adjust, size: 14, color: Colors.white),
                    const SizedBox(width: 6),
                    Expanded(
                      child: Text(
                        '2. Total bobot harus 100% (otomatis diseimbangkan).',
                        style: TextStyle(
                          color: Colors.white.withOpacity(0.95),
                          fontSize: 12,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 6),
                Row(
                  children: [
                    const Icon(Icons.adjust, size: 14, color: Colors.white),
                    const SizedBox(width: 6),
                    Expanded(
                      child: Text(
                        '3. Lihat urutan hasil, lalu pilih yang paling sesuai.',
                        style: TextStyle(
                          color: Colors.white.withOpacity(0.95),
                          fontSize: 12,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'Tips: Fokuskan 1-2 prioritas utama agar hasil lebih akurat dan mudah dipilih.',
            style: TextStyle(
              color: Colors.white.withOpacity(0.9),
              fontSize: 12,
              fontStyle: FontStyle.italic,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildJenisLayananSection() {
    final jenisOptions = [
      {
        'value': 'harian',
        'label': 'Harian',
        'icon': Icons.today,
        'desc': 'Paket selesai harian dengan biaya lebih hemat',
      },
      {
        'value': 'jam',
        'label': 'Jam',
        'icon': Icons.schedule,
        'desc': 'Paket selesai dalam hitungan jam (lebih cepat)',
      },
    ];

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(
                Icons.local_laundry_service_outlined,
                color: _categoryColor,
                size: 20,
              ),
              const SizedBox(width: 8),
              Text(
                'Jenis Layanan',
                style: TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.bold,
                  color: Colors.grey[800],
                ),
              ),
            ],
          ),
          const SizedBox(height: 4),
          Text(
            'Pilih jenis layanan laundry yang ingin dibandingkan',
            style: TextStyle(fontSize: 12, color: Colors.grey[500]),
          ),
          const SizedBox(height: 12),
          ...jenisOptions.map((opt) {
            final isSelected = _selectedJenisLayanan == opt['value'];
            return Padding(
              padding: const EdgeInsets.only(bottom: 8),
              child: InkWell(
                onTap: () => setState(
                  () => _selectedJenisLayanan = opt['value'] as String,
                ),
                borderRadius: BorderRadius.circular(12),
                child: Container(
                  padding: const EdgeInsets.symmetric(
                    vertical: 12,
                    horizontal: 14,
                  ),
                  decoration: BoxDecoration(
                    color: isSelected
                        ? _categoryColor.withOpacity(0.1)
                        : Colors.grey[50],
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(
                      color: isSelected ? _categoryColor : Colors.grey[300]!,
                      width: isSelected ? 2 : 1,
                    ),
                  ),
                  child: Row(
                    children: [
                      Icon(
                        opt['icon'] as IconData,
                        size: 22,
                        color: isSelected ? _categoryColor : Colors.grey[500],
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              opt['label'] as String,
                              style: TextStyle(
                                fontSize: 14,
                                fontWeight: isSelected
                                    ? FontWeight.bold
                                    : FontWeight.w500,
                                color: isSelected
                                    ? _categoryColor
                                    : Colors.grey[800],
                              ),
                            ),
                            const SizedBox(height: 2),
                            Text(
                              opt['desc'] as String,
                              style: TextStyle(
                                fontSize: 11,
                                color: Colors.grey[500],
                              ),
                            ),
                          ],
                        ),
                      ),
                      if (isSelected)
                        Icon(
                          Icons.check_circle,
                          color: _categoryColor,
                          size: 20,
                        ),
                    ],
                  ),
                ),
              ),
            );
          }).toList(),
        ],
      ),
    );
  }

  Widget _buildBobotSection() {
    final isValid = _totalBobot == 100;
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(Icons.tune, color: _categoryColor, size: 20),
              const SizedBox(width: 8),
              Text(
                'Bobot Kriteria',
                style: TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.bold,
                  color: Colors.grey[800],
                ),
              ),
              const Spacer(),
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 10,
                  vertical: 4,
                ),
                decoration: BoxDecoration(
                  color: isValid
                      ? Colors.green.withOpacity(0.1)
                      : Colors.red.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(
                    color: isValid
                        ? Colors.green.withOpacity(0.5)
                        : Colors.red.withOpacity(0.5),
                  ),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(
                      isValid ? Icons.check_circle : Icons.warning,
                      size: 14,
                      color: isValid ? Colors.green : Colors.red,
                    ),
                    const SizedBox(width: 4),
                    Text(
                      'Total: $_totalBobot%',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: isValid ? Colors.green[700] : Colors.red[700],
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 4),
          Text(
            'Bobot otomatis disesuaikan agar total selalu 100%',
            style: TextStyle(fontSize: 11, color: Colors.grey[500]),
          ),
          const SizedBox(height: 10),
          _buildPriorityGuide(),
          const SizedBox(height: 16),
          _buildBobotDropdown(
            label: 'Harga',
            icon: Icons.payments_outlined,
            tipe: 'Cost',
            tipeDesc: 'Semakin murah semakin baik',
            value: _bobotHarga,
            options: _getBobotOptionsFor(0),
            onChanged: (val) => _updateBobot(0, val!),
          ),
          const SizedBox(height: 12),
          _buildBobotDropdown(
            label: 'Jarak',
            icon: Icons.location_on_outlined,
            tipe: 'Cost',
            tipeDesc: 'Semakin dekat semakin baik',
            value: _bobotJarak,
            options: _getBobotOptionsFor(1),
            onChanged: (val) => _updateBobot(1, val!),
          ),
          const SizedBox(height: 12),
          _buildBobotDropdown(
            label: _kriteria3Label,
            icon: widget.category == 'kontrakan'
                ? Icons.bed_outlined
                : Icons.speed_outlined,
            tipe: 'Benefit',
            tipeDesc: widget.category == 'kontrakan'
                ? 'Semakin banyak semakin baik'
                : 'Semakin cepat semakin baik',
            value: _bobotKriteria3,
            options: _getBobotOptionsFor(2),
            onChanged: (val) => _updateBobot(2, val!),
          ),
          const SizedBox(height: 12),
          _buildBobotDropdown(
            label: _kriteria4Label,
            icon: widget.category == 'kontrakan'
                ? Icons.wifi_outlined
                : Icons.local_laundry_service_outlined,
            tipe: 'Benefit',
            tipeDesc: widget.category == 'kontrakan'
                ? 'Semakin lengkap semakin baik'
                : 'Semakin bervariasi semakin baik',
            value: _bobotKriteria4,
            options: _getBobotOptionsFor(3),
            onChanged: (val) => _updateBobot(3, val!),
          ),
          if (!isValid) ...[
            const SizedBox(height: 12),
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: Colors.red[50],
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: Colors.red[200]!),
              ),
              child: Row(
                children: [
                  const Icon(Icons.info_outline, color: Colors.red, size: 16),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      _totalBobot > 100
                          ? 'Total bobot kelebihan ${_totalBobot - 100}%. Kurangi salah satu bobot.'
                          : 'Total bobot kurang ${100 - _totalBobot}%. Tambah salah satu bobot.',
                      style: TextStyle(fontSize: 12, color: Colors.red[700]),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildPriorityGuide() {
    final items = [
      {
        'label': 'Rendah',
        'range': '10-20%',
        'desc': 'Pengaruh kecil pada hasil.',
        'color': const Color(0xFF8E8E93),
      },
      {
        'label': 'Sedang',
        'range': '25-35%',
        'desc': 'Cukup penting.',
        'color': const Color(0xFF00A389),
      },
      {
        'label': 'Tinggi',
        'range': '40-50%',
        'desc': 'Sangat berpengaruh pada hasil.',
        'color': const Color(0xFF2F80ED),
      },
      {
        'label': 'Prioritas',
        'range': '55-70%',
        'desc': 'Faktor utama penentu hasil.',
        'color': const Color(0xFF7B61FF),
      },
    ];

    return Container(
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: Colors.blueGrey.withOpacity(0.06),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: Colors.blueGrey.withOpacity(0.16)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Arti level prioritas',
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w700,
              color: Colors.grey[800],
            ),
          ),
          const SizedBox(height: 8),
          ...items.map((item) {
            final color = item['color'] as Color;
            return Padding(
              padding: const EdgeInsets.only(bottom: 6),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    margin: const EdgeInsets.only(top: 2),
                    width: 8,
                    height: 8,
                    decoration: BoxDecoration(
                      color: color,
                      shape: BoxShape.circle,
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: RichText(
                      text: TextSpan(
                        style: TextStyle(fontSize: 11, color: Colors.grey[700]),
                        children: [
                          TextSpan(
                            text: '${item['label']} (${item['range']}): ',
                            style: const TextStyle(fontWeight: FontWeight.w700),
                          ),
                          TextSpan(text: item['desc'] as String),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            );
          }),
        ],
      ),
    );
  }

  Widget _buildBobotDropdown({
    required String label,
    required IconData icon,
    required String tipe,
    required String tipeDesc,
    required int value,
    required List<int> options,
    required ValueChanged<int?> onChanged,
  }) {
    final isCost = tipe.toLowerCase() == 'cost';
    final safeValue = options.contains(value)
        ? value
        : _closestOption(options, value);
    final priorityColor = _getPriorityColor(safeValue);

    final quickTargets = [
      {'label': 'Rendah', 'value': 15},
      {'label': 'Sedang', 'value': 25},
      {'label': 'Tinggi', 'value': 40},
      {'label': 'Prioritas', 'value': 55},
    ];
    final quickPresets = <Map<String, dynamic>>[];
    final seenValues = <int>{};
    for (final item in quickTargets) {
      final mapped = _closestOption(options, item['value'] as int);
      if (seenValues.add(mapped)) {
        quickPresets.add({'label': item['label'], 'value': mapped});
      }
    }

    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.grey[50],
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey[200]!),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: _categoryColor.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Icon(icon, size: 20, color: _categoryColor),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      label,
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w600,
                        color: Colors.grey[800],
                      ),
                    ),
                    const SizedBox(height: 2),
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 6,
                            vertical: 1,
                          ),
                          decoration: BoxDecoration(
                            color: isCost
                                ? Colors.orange.withOpacity(0.15)
                                : Colors.green.withOpacity(0.15),
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: Text(
                            isCost ? 'Minimalkan' : 'Utamakan',
                            style: TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              color: isCost
                                  ? Colors.orange[800]
                                  : Colors.green[800],
                            ),
                          ),
                        ),
                        const SizedBox(width: 6),
                        Expanded(
                          child: Text(
                            tipeDesc,
                            style: TextStyle(
                              fontSize: 10,
                              color: Colors.grey[500],
                            ),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 8),
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 10,
                  vertical: 6,
                ),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: _categoryColor.withOpacity(0.3)),
                ),
                child: Text(
                  '$safeValue%',
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                    color: _categoryColor,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: priorityColor.withOpacity(0.12),
                  borderRadius: BorderRadius.circular(999),
                ),
                child: Text(
                  'Prioritas ${_getPriorityLabel(safeValue)}',
                  style: TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                    color: priorityColor,
                  ),
                ),
              ),
              const Spacer(),
              Text(
                '${options.first}% - ${options.last}%',
                style: TextStyle(fontSize: 11, color: Colors.grey[500]),
              ),
            ],
          ),
          SliderTheme(
            data: SliderTheme.of(context).copyWith(
              activeTrackColor: _categoryColor,
              inactiveTrackColor: _categoryColor.withOpacity(0.2),
              thumbColor: _categoryColor,
              overlayColor: _categoryColor.withOpacity(0.15),
              trackHeight: 4,
            ),
            child: Slider(
              min: options.first.toDouble(),
              max: options.last.toDouble(),
              divisions: options.length > 1 ? options.length - 1 : null,
              value: safeValue.toDouble(),
              label: '$safeValue%',
              onChanged: (double newValue) {
                onChanged(_closestOption(options, newValue.round()));
              },
            ),
          ),
          Wrap(
            spacing: 8,
            runSpacing: 8,
            children: quickPresets.map((preset) {
              final presetValue = preset['value'] as int;
              final selected = presetValue == safeValue;
              return ChoiceChip(
                label: Text('${preset['label']} ($presetValue%)'),
                selected: selected,
                onSelected: (_) => onChanged(presetValue),
                selectedColor: _categoryColor.withOpacity(0.15),
                side: BorderSide(
                  color: selected
                      ? _categoryColor.withOpacity(0.5)
                      : Colors.grey[300]!,
                ),
                labelStyle: TextStyle(
                  fontSize: 11,
                  color: selected ? _categoryColor : Colors.grey[700],
                  fontWeight: selected ? FontWeight.w600 : FontWeight.w500,
                ),
              );
            }).toList(),
          ),
        ],
      ),
    );
  }

  Widget _buildLocationSection() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(Icons.my_location, color: _categoryColor, size: 20),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  'Lokasi Saya',
                  style: TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.bold,
                    color: Colors.grey[800],
                  ),
                ),
              ),
              if (_userLatitude != null && !_isDetectingLocation)
                InkWell(
                  onTap: _detectUserLocation,
                  borderRadius: BorderRadius.circular(12),
                  child: Padding(
                    padding: const EdgeInsets.all(4),
                    child: Icon(Icons.refresh, size: 18, color: _categoryColor),
                  ),
                ),
            ],
          ),
          const SizedBox(height: 4),
          Text(
            'Jarak dihitung dari lokasi Anda saat ini',
            style: TextStyle(fontSize: 12, color: Colors.grey[500]),
          ),
          const SizedBox(height: 12),
          if (_isDetectingLocation)
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: Colors.blue[50],
                borderRadius: BorderRadius.circular(8),
              ),
              child: const Row(
                children: [
                  SizedBox(
                    width: 16,
                    height: 16,
                    child: CircularProgressIndicator(strokeWidth: 2),
                  ),
                  SizedBox(width: 10),
                  Text('Mendeteksi lokasi...', style: TextStyle(fontSize: 12)),
                ],
              ),
            )
          else if (_userLatitude != null && _userLongitude != null)
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: Colors.green[50],
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: Colors.green[200]!),
              ),
              child: Row(
                children: [
                  const Icon(Icons.check_circle, size: 16, color: Colors.green),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      'Lokasi terdeteksi: ${_userLatitude!.toStringAsFixed(6)}, ${_userLongitude!.toStringAsFixed(6)}',
                      style: TextStyle(fontSize: 11, color: Colors.green[700]),
                    ),
                  ),
                ],
              ),
            )
          else
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: Colors.orange[50],
                borderRadius: BorderRadius.circular(8),
              ),
              child: Row(
                children: [
                  const Icon(Icons.warning, size: 16, color: Colors.orange),
                  const SizedBox(width: 8),
                  const Expanded(
                    child: Text(
                      'Lokasi belum terdeteksi',
                      style: TextStyle(fontSize: 12),
                    ),
                  ),
                  TextButton(
                    onPressed: _detectUserLocation,
                    child: const Text(
                      'Deteksi',
                      style: TextStyle(fontSize: 12),
                    ),
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildCalculateButton() {
    final isValid = _totalBobot == 100;
    return SizedBox(
      width: double.infinity,
      child: ElevatedButton(
        onPressed: isValid && !_isLoading ? _calculateSAW : null,
        style: ElevatedButton.styleFrom(
          backgroundColor: _categoryColor,
          disabledBackgroundColor: Colors.grey[300],
          padding: const EdgeInsets.symmetric(vertical: 16),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
          elevation: isValid ? 4 : 0,
          shadowColor: _categoryColor.withOpacity(0.4),
        ),
        child: _isLoading
            ? const Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                      color: Colors.white,
                    ),
                  ),
                  SizedBox(width: 12),
                  Text(
                    'Menyiapkan rekomendasi...',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              )
            : const Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.calculate, color: Colors.white, size: 22),
                  SizedBox(width: 10),
                  Text(
                    'Hitung Rekomendasi',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              ),
      ),
    );
  }

  // ===================== RESULT VIEW =====================
  Widget _buildResultView() {
    return Column(
      children: [
        _buildBobotSummary(),
        if (widget.category == 'laundry' && _userLatitude != null)
          Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            color: Colors.green[50],
            child: Row(
              children: [
                Icon(Icons.location_on, size: 16, color: Colors.green[700]),
                const SizedBox(width: 8),
                Text(
                  'Jarak dihitung dari lokasi Anda',
                  style: TextStyle(fontSize: 12, color: Colors.green[700]),
                ),
              ],
            ),
          ),
        Expanded(child: _buildResultContent()),
      ],
    );
  }

  Widget _buildBobotSummary() {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [_categoryColor.withOpacity(0.05), Colors.white],
        ),
        border: Border(bottom: BorderSide(color: Colors.grey[200]!)),
      ),
      child: Column(
        children: [
          Row(
            children: [
              Icon(Icons.tune, size: 14, color: _categoryColor),
              const SizedBox(width: 6),
              Text(
                'Bobot yang digunakan:',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                  color: Colors.grey[700],
                ),
              ),
              if (widget.category == 'laundry') ...[
                const SizedBox(width: 8),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 8,
                    vertical: 2,
                  ),
                  decoration: BoxDecoration(
                    color: _categoryColor.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Text(
                    _getJenisLayananLabel(_selectedJenisLayanan),
                    style: TextStyle(
                      fontSize: 10,
                      fontWeight: FontWeight.bold,
                      color: _categoryColor,
                    ),
                  ),
                ),
              ],
              const Spacer(),
              InkWell(
                onTap: () => setState(() {
                  _hasCalculated = false;
                  _recommendations = [];
                  _errorMessage = null;
                  _noData = false;
                }),
                child: Row(
                  children: [
                    Icon(Icons.edit, size: 14, color: _categoryColor),
                    const SizedBox(width: 4),
                    Text(
                      'Ubah',
                      style: TextStyle(
                        fontSize: 12,
                        color: _categoryColor,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Row(
            children: [
              _buildBobotMiniChip('Harga', _bobotHarga, true),
              const SizedBox(width: 6),
              _buildBobotMiniChip('Jarak', _bobotJarak, true),
              const SizedBox(width: 6),
              _buildBobotMiniChip(_kriteria3Label, _bobotKriteria3, false),
              const SizedBox(width: 6),
              _buildBobotMiniChip(_kriteria4Label, _bobotKriteria4, false),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildBobotMiniChip(String label, int value, bool isCost) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 6, horizontal: 4),
        decoration: BoxDecoration(
          color: isCost
              ? Colors.orange.withOpacity(0.08)
              : Colors.green.withOpacity(0.08),
          borderRadius: BorderRadius.circular(8),
          border: Border.all(
            color: isCost
                ? Colors.orange.withOpacity(0.2)
                : Colors.green.withOpacity(0.2),
          ),
        ),
        child: Column(
          children: [
            Text(
              label,
              style: TextStyle(fontSize: 9, color: Colors.grey[600]),
              textAlign: TextAlign.center,
              overflow: TextOverflow.ellipsis,
            ),
            const SizedBox(height: 2),
            Text(
              '$value%',
              style: TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.bold,
                color: isCost ? Colors.orange[700] : Colors.green[700],
              ),
            ),
          ],
        ),
      ),
    );
  }

  String _getJenisLayananLabel(String key) {
    switch (key) {
      case 'harian':
        return 'Harian';
      case 'jam':
        return 'Jam';
      case 'reguler':
        return 'Harian';
      case 'express':
        return 'Jam';
      default:
        return key;
    }
  }

  Widget _buildResultContent() {
    if (_isLoading) {
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            CircularProgressIndicator(),
            SizedBox(height: 16),
            Text('Sedang menyiapkan rekomendasi terbaik...'),
          ],
        ),
      );
    }

    if (_errorMessage != null && _recommendations.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(32),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                _noData
                    ? (widget.category == 'kontrakan'
                          ? Icons.home_outlined
                          : Icons.local_laundry_service_outlined)
                    : Icons.search_off,
                size: 64,
                color: Colors.grey[400],
              ),
              const SizedBox(height: 16),
              Text(
                _noData
                    ? (widget.category == 'kontrakan'
                          ? 'Belum Ada Kontrakan Tersedia'
                          : 'Belum Ada Laundry Tersedia')
                    : 'Belum Ada Hasil yang Cocok',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: Colors.grey[700],
                ),
              ),
              const SizedBox(height: 8),
              Text(
                _errorMessage!,
                textAlign: TextAlign.center,
                style: TextStyle(color: Colors.grey[500], fontSize: 13),
              ),
              const SizedBox(height: 24),
              if (!_noData)
                Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    ElevatedButton.icon(
                      onPressed: () => setState(() {
                        _hasCalculated = false;
                        _errorMessage = null;
                        _noData = false;
                      }),
                      icon: const Icon(Icons.tune),
                      label: const Text('Atur Prioritas Lagi'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: _categoryColor,
                        foregroundColor: Colors.white,
                      ),
                    ),
                    if (_errorMessage != null &&
                        (_errorMessage!.toLowerCase().contains(
                              'tidak dapat terhubung ke server',
                            ) ||
                            _errorMessage!.toLowerCase().contains('timeout')))
                      Padding(
                        padding: const EdgeInsets.only(top: 10),
                        child: OutlinedButton.icon(
                          onPressed: _resetConnectionAndRetry,
                          icon: const Icon(Icons.restart_alt),
                          label: const Text('Reset Koneksi'),
                          style: OutlinedButton.styleFrom(
                            foregroundColor: _categoryColor,
                            side: BorderSide(color: _categoryColor),
                          ),
                        ),
                      ),
                  ],
                )
              else
                OutlinedButton.icon(
                  onPressed: () => setState(() {
                    _hasCalculated = false;
                    _errorMessage = null;
                    _noData = false;
                  }),
                  icon: const Icon(Icons.refresh),
                  label: const Text('Coba Lagi'),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: _categoryColor,
                    side: BorderSide(color: _categoryColor),
                  ),
                ),
            ],
          ),
        ),
      );
    }

    if (_recommendations.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(32),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                widget.category == 'kontrakan'
                    ? Icons.home_outlined
                    : Icons.local_laundry_service_outlined,
                size: 64,
                color: Colors.grey[400],
              ),
              const SizedBox(height: 16),
              Text(
                'Tidak ada hasil',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: Colors.grey[700],
                ),
              ),
            ],
          ),
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _calculateSAW,
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: _recommendations.length,
        itemBuilder: (context, index) {
          final item = _recommendations[index];
          return _buildResultCard(item, index);
        },
      ),
    );
  }

  Widget _buildResultCard(Map<String, dynamic> item, int index) {
    final ranking = item['ranking'] ?? (index + 1);
    final skor = (item['skor'] ?? item['skor_akhir'] ?? 0).toDouble();
    final normalisasi = item['normalisasi'] != null
        ? Map<String, dynamic>.from(item['normalisasi'])
        : <String, dynamic>{};
    final nilai = item['nilai'] != null
        ? Map<String, dynamic>.from(item['nilai'])
        : <String, dynamic>{};
    final itemData = item['data'] ?? item;

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: ranking <= 3
                ? _getRankingColor(ranking).withOpacity(0.2)
                : Colors.grey.withOpacity(0.1),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
        border: ranking <= 3
            ? Border.all(
                color: _getRankingColor(ranking).withOpacity(0.3),
                width: 1.5,
              )
            : null,
      ),
      child: Column(
        children: [
          // Ranking Header
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [
                  _getRankingColor(ranking).withOpacity(0.1),
                  Colors.transparent,
                ],
              ),
              borderRadius: const BorderRadius.vertical(
                top: Radius.circular(16),
              ),
            ),
            child: Row(
              children: [
                Container(
                  width: 36,
                  height: 36,
                  decoration: BoxDecoration(
                    color: _getRankingColor(ranking),
                    shape: BoxShape.circle,
                    boxShadow: [
                      BoxShadow(
                        color: _getRankingColor(ranking).withOpacity(0.4),
                        blurRadius: 6,
                        offset: const Offset(0, 2),
                      ),
                    ],
                  ),
                  child: Center(
                    child: ranking <= 3
                        ? Icon(
                            _getRankingIcon(ranking),
                            color: Colors.white,
                            size: 18,
                          )
                        : Text(
                            '#$ranking',
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        item['nama'] ?? 'N/A',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          color: Colors.grey[800],
                        ),
                      ),
                      Text(
                        'Peringkat $ranking',
                        style: TextStyle(fontSize: 11, color: Colors.grey[500]),
                      ),
                    ],
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 6,
                  ),
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      colors: [_categoryColor, _categoryColor.withOpacity(0.8)],
                    ),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    'Skor: ${skor.toStringAsFixed(4)}',
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ],
            ),
          ),

          // SAW Detail
          if (normalisasi.isNotEmpty)
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              child: _buildNormalizationDetail(normalisasi, nilai),
            ),

          // Score Progress Bar
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      'Skor Akhir (Vi)',
                      style: TextStyle(
                        fontSize: 11,
                        color: Colors.grey[600],
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                    Text(
                      '${(skor * 100).toStringAsFixed(2)}%',
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: _categoryColor,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 4),
                ClipRRect(
                  borderRadius: BorderRadius.circular(4),
                  child: LinearProgressIndicator(
                    value: skor.clamp(0.0, 1.0),
                    backgroundColor: Colors.grey[200],
                    valueColor: AlwaysStoppedAnimation<Color>(_categoryColor),
                    minHeight: 6,
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 8),
          const Divider(height: 1),

          // Item Card
          Padding(
            padding: const EdgeInsets.all(8),
            child: widget.category == 'kontrakan'
                ? KontrakanCard(kontrakan: Kontrakan.fromJson(itemData))
                : LaundryCard(
                    laundry: Laundry.fromJson(itemData),
                    userLatitude: _userLatitude,
                    userLongitude: _userLongitude,
                  ),
          ),
        ],
      ),
    );
  }

  Widget _buildNormalizationDetail(
    Map<String, dynamic> normalisasi,
    Map<String, dynamic> nilai,
  ) {
    return ExpansionTile(
      title: Row(
        children: [
          Icon(Icons.table_chart, size: 16, color: _categoryColor),
          const SizedBox(width: 8),
          Text(
            'Detail Perhitungan SAW',
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              color: Colors.grey[700],
            ),
          ),
        ],
      ),
      tilePadding: EdgeInsets.zero,
      childrenPadding: EdgeInsets.zero,
      initiallyExpanded: false,
      children: [
        Container(
          decoration: BoxDecoration(
            color: Colors.grey[50],
            borderRadius: BorderRadius.circular(8),
          ),
          child: Table(
            border: TableBorder.all(
              color: Colors.grey[300]!,
              width: 0.5,
              borderRadius: BorderRadius.circular(8),
            ),
            columnWidths: const {
              0: FlexColumnWidth(2.5),
              1: FlexColumnWidth(1.5),
              2: FlexColumnWidth(1.5),
              3: FlexColumnWidth(1.5),
            },
            children: [
              TableRow(
                decoration: BoxDecoration(
                  color: _categoryColor.withOpacity(0.1),
                  borderRadius: const BorderRadius.vertical(
                    top: Radius.circular(8),
                  ),
                ),
                children: [
                  _tableHeader('Kriteria'),
                  _tableHeader('Nilai'),
                  _tableHeader('Normalisasi'),
                  _tableHeader('Bobot'),
                ],
              ),
              ...normalisasi.entries.map((entry) {
                final key = entry.key;
                final norm = (entry.value is num)
                    ? entry.value.toDouble()
                    : 0.0;
                final nilaiVal = nilai[key] ?? 0;
                final bobot = _getBobotForKey(key);
                return TableRow(
                  children: [
                    _tableCell(_getKriteriaDisplayName(key)),
                    _tableCell(
                      nilaiVal is double
                          ? nilaiVal.toStringAsFixed(2)
                          : nilaiVal.toString(),
                    ),
                    _tableCell(
                      norm is double
                          ? norm.toStringAsFixed(4)
                          : norm.toString(),
                    ),
                    _tableCell('${(bobot * 100).toInt()}%'),
                  ],
                );
              }),
            ],
          ),
        ),
      ],
    );
  }

  Widget _tableHeader(String text) => Padding(
    padding: const EdgeInsets.all(6),
    child: Text(
      text,
      style: TextStyle(
        fontSize: 10,
        fontWeight: FontWeight.bold,
        color: _categoryColor,
      ),
      textAlign: TextAlign.center,
    ),
  );
  Widget _tableCell(String text) => Padding(
    padding: const EdgeInsets.all(6),
    child: Text(
      text,
      style: TextStyle(fontSize: 10, color: Colors.grey[700]),
      textAlign: TextAlign.center,
    ),
  );

  String _getKriteriaDisplayName(String key) {
    switch (key) {
      case 'harga':
        return 'Harga';
      case 'jarak':
        return 'Jarak';
      case 'jumlah_kamar':
        return 'Jml Kamar';
      case 'fasilitas_count':
        return 'Fasilitas';
      case 'kecepatan_layanan':
        return 'Kecepatan';
      case 'layanan':
        return 'Layanan';
      default:
        return key;
    }
  }

  double _getBobotForKey(String key) {
    switch (key) {
      case 'harga':
        return _bobotHarga / 100;
      case 'jarak':
        return _bobotJarak / 100;
      case 'jumlah_kamar':
      case 'kecepatan_layanan':
        return _bobotKriteria3 / 100;
      case 'fasilitas_count':
      case 'layanan':
        return _bobotKriteria4 / 100;
      default:
        return 0;
    }
  }

  Color _getRankingColor(int rank) {
    switch (rank) {
      case 1:
        return const Color(0xFFFFD700);
      case 2:
        return const Color(0xFFC0C0C0);
      case 3:
        return const Color(0xFFCD7F32);
      default:
        return _categoryColor;
    }
  }

  IconData _getRankingIcon(int rank) {
    switch (rank) {
      case 1:
        return Icons.emoji_events;
      case 2:
        return Icons.workspace_premium;
      case 3:
        return Icons.military_tech;
      default:
        return Icons.star;
    }
  }
}
