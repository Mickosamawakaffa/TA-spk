import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:async';
import 'dart:convert';
import 'package:geolocator/geolocator.dart';
import 'package:flutter_cache_manager/flutter_cache_manager.dart';
import '../config/app_config.dart';
import '../models/kontrakan.dart';
import '../models/laundry.dart';
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

  // Bobot values (percentage, total must = 100)
  // Default: profil mahasiswa
  int _bobotHarga = 50;
  int _bobotJarak = 20;
  int _bobotKriteria3 = 15; // jumlah_kamar (kontrakan) or kecepatan (laundry)
  int _bobotKriteria4 = 15; // fasilitas (kontrakan) or layanan (laundry)

  // Jenis layanan selection for laundry
  String _selectedJenisLayanan = 'reguler';

  // Location values
  String _referensiJarak = 'kampus';
  double? _userLatitude;
  double? _userLongitude;
  bool _isDetectingLocation = false;

  String get _kriteria3Label =>
      widget.category == 'kontrakan' ? 'Jumlah Kamar' : 'Kecepatan Layanan';
  String get _kriteria4Label =>
      widget.category == 'kontrakan' ? 'Fasilitas' : 'Variasi Layanan';
  // ignore: unused_element
  String get _kriteria3Key =>
      widget.category == 'kontrakan' ? 'jumlah_kamar' : 'kecepatan';
  // ignore: unused_element
  String get _kriteria4Key =>
      widget.category == 'kontrakan' ? 'fasilitas' : 'layanan';

  int get _totalBobot =>
      _bobotHarga + _bobotJarak + _bobotKriteria3 + _bobotKriteria4;

  Color get _categoryColor => widget.category == 'kontrakan'
      ? const Color(0xFF667eea)
      : const Color(0xFF764ba2);

  @override
  void initState() {
    super.initState();
    // Default bobot: profil mahasiswa
    _bobotHarga = 50;
    _bobotJarak = 20;
    _bobotKriteria3 = 15;
    _bobotKriteria4 = 15;
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

      // Final safety check: if sum != 100 due to clamping, adjust the largest
      int total = bobots.reduce((a, b) => a + b);
      if (total != 100) {
        int diff = 100 - total;
        int adjustIdx = otherIdx.reduce(
          (a, b) => bobots[a] >= bobots[b] ? a : b,
        );
        int adjusted = (bobots[adjustIdx] + diff).clamp(10, 70);
        // Snap to nearest multiple of 5
        adjusted = ((adjusted / 5).round() * 5).clamp(10, 70);
        bobots[adjustIdx] = adjusted;
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

    setState(() {
      _isLoading = true;
      _errorMessage = null;
      _noData = false;
    });

    // Clear image cache agar foto terbaru selalu dimuat
    await DefaultCacheManager().emptyCache();
    PaintingBinding.instance.imageCache.clear();
    PaintingBinding.instance.imageCache.clearLiveImages();

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

      if (widget.category == 'laundry' && _referensiJarak == 'user') {
        if (_userLatitude != null && _userLongitude != null) {
          bodyParams['user_lat'] = _userLatitude;
          bodyParams['user_lng'] = _userLongitude;
        }
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
      setState(() {
        _errorMessage =
            'Tidak dapat terhubung ke server (${AppConfig.baseUrl}). Periksa koneksi internet Anda dan coba lagi.\n\nDetail: $e';
        _hasCalculated = true;
      });
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  Future<void> _detectUserLocation() async {
    setState(() => _isDetectingLocation = true);
    try {
      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Aktifkan GPS Anda terlebih dahulu'),
              backgroundColor: Colors.red,
            ),
          );
        }
        setState(() {
          _referensiJarak = 'kampus';
          _isDetectingLocation = false;
        });
        return;
      }

      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
        if (permission == LocationPermission.denied) {
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(
                content: Text('Izin lokasi ditolak'),
                backgroundColor: Colors.red,
              ),
            );
          }
          setState(() {
            _referensiJarak = 'kampus';
            _isDetectingLocation = false;
          });
          return;
        }
      }

      if (permission == LocationPermission.deniedForever) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text(
                'Izin lokasi ditolak permanen. Ubah di pengaturan.',
              ),
              backgroundColor: Colors.red,
            ),
          );
        }
        setState(() {
          _referensiJarak = 'kampus';
          _isDetectingLocation = false;
        });
        return;
      }

      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      );
      setState(() {
        _userLatitude = position.latitude;
        _userLongitude = position.longitude;
        _isDetectingLocation = false;
      });

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Lokasi berhasil dideteksi!'),
            backgroundColor: Colors.green,
            duration: Duration(seconds: 2),
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Gagal mendeteksi lokasi: $e'),
            backgroundColor: Colors.red,
          ),
        );
      }
      setState(() {
        _referensiJarak = 'kampus';
        _isDetectingLocation = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final categoryTitle = widget.category == 'kontrakan'
        ? 'SPK Rekomendasi Kontrakan'
        : 'SPK Rekomendasi Laundry';

    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
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

  Widget _buildMethodInfoCard() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [_categoryColor, _categoryColor.withOpacity(0.8)],
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
                'Metode SAW',
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
            'Simple Additive Weighting (SAW) menghitung skor rekomendasi berdasarkan bobot kriteria yang Anda tentukan.',
            style: TextStyle(
              color: Colors.white.withOpacity(0.9),
              fontSize: 13,
            ),
          ),
          const SizedBox(height: 8),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.2),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Text(
              'Vi = Σ(Wj × Rij)',
              style: TextStyle(
                color: Colors.white.withOpacity(0.95),
                fontSize: 14,
                fontWeight: FontWeight.w600,
                fontFamily: 'monospace',
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildJenisLayananSection() {
    final jenisOptions = [
      {
        'value': 'reguler',
        'label': 'Reguler',
        'icon': Icons.schedule,
        'desc': 'Layanan standar dengan harga terjangkau',
      },
      {
        'value': 'express',
        'label': 'Express',
        'icon': Icons.flash_on,
        'desc': 'Layanan cepat dengan waktu lebih singkat',
      },
      {
        'value': 'kilat',
        'label': 'Kilat',
        'icon': Icons.bolt,
        'desc': 'Layanan tercepat, selesai dalam hitungan jam',
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
    // Ensure current value is in options list
    final safeValue = options.contains(value)
        ? value
        : options.reduce(
            (a, b) => (a - value).abs() < (b - value).abs() ? a : b,
          );
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.grey[50],
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey[200]!),
      ),
      child: Row(
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
                        tipe,
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
                        style: TextStyle(fontSize: 10, color: Colors.grey[500]),
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
            padding: const EdgeInsets.symmetric(horizontal: 8),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(8),
              border: Border.all(color: _categoryColor.withOpacity(0.3)),
            ),
            child: DropdownButtonHideUnderline(
              child: DropdownButton<int>(
                value: safeValue,
                isDense: true,
                icon: Icon(Icons.arrow_drop_down, color: _categoryColor),
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                  color: _categoryColor,
                ),
                items: options
                    .map(
                      (int val) => DropdownMenuItem<int>(
                        value: val,
                        child: Text('$val%'),
                      ),
                    )
                    .toList(),
                onChanged: onChanged,
              ),
            ),
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
              Text(
                'Referensi Jarak',
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
            'Tentukan titik referensi untuk perhitungan jarak',
            style: TextStyle(fontSize: 12, color: Colors.grey[500]),
          ),
          const SizedBox(height: 12),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12),
            decoration: BoxDecoration(
              color: Colors.grey[50],
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: Colors.grey[200]!),
            ),
            child: DropdownButtonHideUnderline(
              child: DropdownButton<String>(
                value: _referensiJarak,
                isExpanded: true,
                icon: Icon(Icons.arrow_drop_down, color: _categoryColor),
                items: [
                  DropdownMenuItem(
                    value: 'kampus',
                    child: Row(
                      children: [
                        Icon(Icons.school, size: 18, color: _categoryColor),
                        const SizedBox(width: 10),
                        const Text('Dari Kampus Polije'),
                      ],
                    ),
                  ),
                  DropdownMenuItem(
                    value: 'user',
                    child: Row(
                      children: [
                        Icon(
                          Icons.location_on,
                          size: 18,
                          color: Colors.green[700],
                        ),
                        const SizedBox(width: 10),
                        const Text('Dari Lokasi Saya'),
                      ],
                    ),
                  ),
                ],
                onChanged: (val) {
                  setState(() {
                    _referensiJarak = val!;
                  });
                  if (val == 'user' && _userLatitude == null)
                    _detectUserLocation();
                },
              ),
            ),
          ),
          if (_referensiJarak == 'user') ...[
            const SizedBox(height: 10),
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
                    Text(
                      'Mendeteksi lokasi...',
                      style: TextStyle(fontSize: 12),
                    ),
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
                    const Icon(
                      Icons.check_circle,
                      size: 16,
                      color: Colors.green,
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        'Lokasi: ${_userLatitude!.toStringAsFixed(6)}, ${_userLongitude!.toStringAsFixed(6)}',
                        style: TextStyle(
                          fontSize: 11,
                          color: Colors.green[700],
                        ),
                      ),
                    ),
                    InkWell(
                      onTap: _detectUserLocation,
                      child: Icon(
                        Icons.refresh,
                        size: 16,
                        color: Colors.green[700],
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
                    'Menghitung SAW...',
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
        if (widget.category == 'laundry' &&
            _referensiJarak == 'user' &&
            _userLatitude != null)
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
              _buildBobotMiniChip('Harga', _bobotHarga, 'Cost'),
              const SizedBox(width: 6),
              _buildBobotMiniChip('Jarak', _bobotJarak, 'Cost'),
              const SizedBox(width: 6),
              _buildBobotMiniChip(_kriteria3Label, _bobotKriteria3, 'Benefit'),
              const SizedBox(width: 6),
              _buildBobotMiniChip(_kriteria4Label, _bobotKriteria4, 'Benefit'),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildBobotMiniChip(String label, int value, String tipe) {
    final isCost = tipe == 'Cost';
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
      case 'reguler':
        return 'Reguler';
      case 'express':
        return 'Express';
      case 'kilat':
        return 'Kilat';
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
            Text('Menghitung rekomendasi SAW...'),
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
                    : 'Tidak Ada Hasil',
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
                ElevatedButton.icon(
                  onPressed: () => setState(() {
                    _hasCalculated = false;
                    _errorMessage = null;
                    _noData = false;
                  }),
                  icon: const Icon(Icons.tune),
                  label: const Text('Ubah Bobot Kriteria'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: _categoryColor,
                    foregroundColor: Colors.white,
                  ),
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
                : LaundryCard(laundry: Laundry.fromJson(itemData)),
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
