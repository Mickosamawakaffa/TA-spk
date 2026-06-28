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
import '../services/location_service.dart';
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

  // ============================================================================
  // QUESTIONNAIRE STATE (Smart Questions untuk mahasiswa)
  // ============================================================================
  bool _showQuestionnaire = false; // Show input view by default
  bool _loadingRange = true; // Loading range data

  // Range data dari database
  int _hargaMin = 0;
  int _hargaMax = 10000000;
  List<String> _availableFasilitas = [];
  // Laundry specific data
  List<String> _availableJenisLayanan = [];
  // rating removed by request

  // Q1: Budget
  double? _budgetSelected; // null = belum pilih

  // Q2: Required Facilities (checkbox)
  Set<String> _selectedFacilities =
      {}; // e.g. {'WiFi', 'AC', 'Dapur', 'Parkir', 'Air Saniter'}

  // Q3: Distance preference
  String _distancePreference =
      'sedang'; // 'dekat', 'sedang', 'jauh_ok', 'tidak_peduli'

  // Q4: Room preference
  String _roomPreference =
      'tidak_peduli'; // '1_kamar', '2_3_kamar', '4_plus_kamar', 'tidak_peduli'

  // Bobot values (percentage, total must = 100) - CALCULATED FROM QUESTIONNAIRE
  // Default: profil mahasiswa
  int _bobotHarga = 50;
  int _bobotJarak = 20;
  int _bobotKriteria3 = 15; // jumlah_kamar (kontrakan) or kecepatan (laundry)
  int _bobotKriteria4 = 15; // fasilitas (kontrakan) or layanan (laundry)

  // Jenis layanan selection for laundry
  String _selectedJenisLayanan = 'harian';
  // Laundry quick preset and antar/jemput
  String _selectedPreset = 'Normal';
  bool _antarJemput = false;

  // Location values untuk referensi jarak (deteksi lokasi user)
  double? _userLatitude;
  double? _userLongitude;
  bool _isDetectingLocation = false;

  String get _kriteria3Label =>
      widget.category == 'kontrakan' ? 'Jumlah Kamar' : 'Kecepatan Layanan';
  String get _kriteria4Label => 'Fasilitas';

  int get _totalBobot =>
      _bobotHarga + _bobotJarak + _bobotKriteria3 + _bobotKriteria4;

  Color get _categoryColor => widget.category == 'kontrakan'
      ? const Color(0xFF1565C0)
      : const Color(0xFF00897B);

  @override
  void initState() {
    super.initState();
    // Default bobot: kontrakan seimbang 25% tiap kriteria
    if (widget.category == 'kontrakan') {
      _bobotHarga = 25;
      _bobotJarak = 25;
      _bobotKriteria3 = 25;
      _bobotKriteria4 = 25;
    } else {
      _bobotHarga = 50;
      _bobotJarak = 20;
      _bobotKriteria3 = 15;
      _bobotKriteria4 = 15;
    }

    // Load user info
    _loadUser();

    // Load range data untuk questionnaire (kontrakan & laundry)
    if (widget.category == 'kontrakan' || widget.category == 'laundry') {
      _loadRangeData();

      // Safety fallback: jika API lambat atau menggantung, paksa fallback setelah 5s
      Future.delayed(const Duration(seconds: 5), () {
        if (!mounted) return;
        if (_loadingRange) {
          setState(() {
            _loadingRange = false;
            if (_availableFasilitas.isEmpty) {
              _availableFasilitas = [
                'WiFi',
                'Parkir',
                'Dapur Bersama',
                'Lemari',
                'Air Panas',
              ];
            }
            if (_hargaMin == _hargaMax) {
              _hargaMax = _hargaMin + 500000;
            }
          });
        }
      });
    }

    // Auto-detect lokasi untuk laundry
    if (widget.category == 'laundry') {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        _detectUserLocation();
      });
    }
  }

  Widget _buildBobotStackedBar() {
    final items = _getBobotItems();
    final values = items.map((e) => e['value'] as int).toList();

    final colors = [
      _categoryColor.withOpacity(1.0),
      _categoryColor.withOpacity(0.78),
      _categoryColor.withOpacity(0.56),
      _categoryColor.withOpacity(0.34),
    ];

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        ClipRRect(
          borderRadius: BorderRadius.circular(999),
          child: LayoutBuilder(
            builder: (ctx, constraints) {
              final fullWidth = constraints.maxWidth;
              final widths = <double>[];
              double used = 0;
              for (int i = 0; i < values.length; i++) {
                if (i == values.length - 1) {
                  widths.add((fullWidth - used).clamp(0, fullWidth));
                } else {
                  final w = fullWidth * (values[i] / 100.0);
                  widths.add(w);
                  used += w;
                }
              }

              return SizedBox(
                height: 14,
                child: Row(
                  children: List.generate(values.length, (i) {
                    final isFirst = i == 0;
                    return InkWell(
                      onTap: () => _showBobotPicker(i),
                      child: Container(
                        width: widths[i],
                        decoration: BoxDecoration(
                          color: colors[i],
                          border: isFirst
                              ? null
                              : const Border(
                                  left: BorderSide(color: Colors.white, width: 2),
                                ),
                        ),
                      ),
                    );
                  }),
                ),
              );
            },
          ),
        ),
        const SizedBox(height: 8),
        Text(
          'Ringkasan bobot (tap segmen untuk fokus slider)',
          style: TextStyle(fontSize: 11, color: Colors.grey[500]),
        ),
      ],
    );
  }

  Widget _buildBobotRows() {
    final items = _getBobotItems();

    return Column(
      children: List.generate(items.length, (i) {
        final item = items[i];
        return Padding(
          padding: EdgeInsets.only(bottom: i == items.length - 1 ? 0 : 10),
          child: _buildBobotRow(
            index: i,
            label: item['label'] as String,
            icon: item['icon'] as IconData,
            tipe: item['tipe'] as String,
            tipeDesc: item['tipeDesc'] as String,
            value: item['value'] as int,
          ),
        );
      }),
    );
  }

  Widget _buildBobotSlider(int index, int value, {VoidCallback? onChanged}) {
    final maxBobot = _getMaxBobot(index);
    final clampedValue = value.clamp(10, maxBobot);
    final divisions = maxBobot > 10 ? ((maxBobot - 10) ~/ 5) : null;

    return Column(
      children: [
        SliderTheme(
          data: SliderTheme.of(context).copyWith(
            activeTrackColor: _categoryColor,
            inactiveTrackColor: _categoryColor.withOpacity(0.12),
            thumbColor: _categoryColor,
            overlayColor: _categoryColor.withOpacity(0.15),
            trackHeight: 6,
            tickMarkShape: SliderTickMarkShape.noTickMark,
          ),
          child: Slider(
            value: clampedValue.toDouble(),
            min: 10,
            max: maxBobot.toDouble(),
            divisions: divisions,
            label: '$clampedValue%',
            onChanged: (v) {
              _updateBobot(index, v.round());
              onChanged?.call();
            },
          ),
        ),
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 4),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('10%', style: TextStyle(fontSize: 10, color: Colors.grey[500])),
              Text(
                '$maxBobot%',
                style: TextStyle(fontSize: 10, color: Colors.grey[500]),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildBobotRow({
    required int index,
    required String label,
    required IconData icon,
    required String tipe,
    required String tipeDesc,
    required int value,
  }) {
    final isCost = tipe.toLowerCase() == 'cost';
    final percent = value.clamp(0, 100);
    final priorityColor = _getPriorityColor(value);

    return Container(
      padding: const EdgeInsets.all(12),
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
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: _categoryColor.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(icon, size: 18, color: _categoryColor),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      label,
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                        color: Colors.grey[850],
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      tipeDesc,
                      style: TextStyle(fontSize: 11, color: Colors.grey[500]),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 8),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: _categoryColor.withOpacity(0.3)),
                ),
                child: Text(
                  '$percent%',
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w800,
                    color: _categoryColor,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: priorityColor.withOpacity(0.12),
                  borderRadius: BorderRadius.circular(999),
                ),
                child: Text(
                  'Prioritas ${_getPriorityLabel(value)}',
                  style: TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                    color: priorityColor,
                  ),
                ),
              ),
              const Spacer(),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                decoration: BoxDecoration(
                  color: isCost
                      ? Colors.orange.withOpacity(0.15)
                      : Colors.green.withOpacity(0.15),
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Text(
                  isCost ? 'Cost' : 'Benefit',
                  style: TextStyle(
                    fontSize: 10,
                    fontWeight: FontWeight.w700,
                    color: isCost ? Colors.orange[800] : Colors.green[800],
                  ),
                ),
              ),
            ],
          ),
          _buildBobotSlider(index, value),
        ],
      ),
    );
  }

  List<Map<String, Object>> _getBobotItems() {
    return [
      {
        'label': 'Harga',
        'icon': Icons.payments_outlined,
        'tipe': 'Cost',
        'tipeDesc': 'Semakin murah semakin baik',
        'value': _bobotHarga,
      },
      {
        'label': 'Jarak',
        'icon': Icons.location_on_outlined,
        'tipe': 'Cost',
        'tipeDesc': 'Semakin dekat semakin baik',
        'value': _bobotJarak,
      },
      {
        'label': _kriteria3Label,
        'icon': widget.category == 'kontrakan'
            ? Icons.bed_outlined
            : Icons.speed_outlined,
        'tipe': 'Benefit',
        'tipeDesc': widget.category == 'kontrakan'
            ? 'Semakin banyak semakin baik'
            : 'Semakin cepat semakin baik',
        'value': _bobotKriteria3,
      },
      {
        'label': _kriteria4Label,
        'icon': widget.category == 'kontrakan'
            ? Icons.wifi_outlined
            : Icons.local_laundry_service_outlined,
        'tipe': 'Benefit',
        'tipeDesc': widget.category == 'kontrakan'
            ? 'Semakin lengkap semakin baik'
            : 'Semakin lengkap semakin baik',
        'value': _bobotKriteria4,
      },
    ];
  }

  void _showBobotPicker(int index) {
    final items = _getBobotItems();
    final item = items[index];
    final label = item['label'] as String;
    final icon = item['icon'] as IconData;
    final tipeDesc = item['tipeDesc'] as String;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
      ),
      builder: (ctx) {
        return StatefulBuilder(
          builder: (ctx, setModalState) {
            final currentValues = [
              _bobotHarga,
              _bobotJarak,
              _bobotKriteria3,
              _bobotKriteria4,
            ];
            final selected = currentValues[index];

            return SafeArea(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(10),
                          decoration: BoxDecoration(
                            color: _categoryColor.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Icon(icon, color: _categoryColor),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                label,
                                style: const TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.w800,
                                ),
                              ),
                              const SizedBox(height: 2),
                              Text(
                                tipeDesc,
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Colors.grey[600],
                                ),
                              ),
                            ],
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 10,
                            vertical: 6,
                          ),
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(10),
                            border: Border.all(
                              color: _categoryColor.withOpacity(0.3),
                            ),
                          ),
                          child: Text(
                            '$selected%',
                            style: TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.w800,
                              color: _categoryColor,
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 10),
                    Text(
                      'Geser slider (10%–70%). Total otomatis diseimbangkan menjadi 100%.',
                      style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                    ),
                    _buildBobotSlider(
                      index,
                      selected,
                      onChanged: () => setModalState(() {}),
                    ),
                    const SizedBox(height: 8),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: () => Navigator.of(ctx).pop(),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: _categoryColor,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                        child: const Text(
                          'Selesai',
                          style: TextStyle(fontWeight: FontWeight.w700),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            );
          },
        );
      },
    );
  }

  /// Load range data (min/max harga, jarak, kamar) dari API
  Future<void> _loadRangeData() async {
    try {
      final endpoint = widget.category == 'kontrakan' ? '/kontrakan/range' : '/laundry/range';
      final url = Uri.parse('${AppConfig.baseUrl}$endpoint');
      final response = await http.get(url).timeout(const Duration(seconds: 10));

      if (response.statusCode == 200) {
        final json = jsonDecode(response.body);
        if (json['success'] == true) {
          final data = json['data'];
          if (mounted) {
            setState(() {
              _hargaMin = data['harga']['min'] ?? 0;
              _hargaMax = data['harga']['max'] ?? 10000000;
              if (widget.category != 'kontrakan') {
                _availableJenisLayanan = List<String>.from(data['jenis_layanan'] ?? []);
              }

              _availableFasilitas = List<String>.from(data['fasilitas'] ?? []);
              if (_availableFasilitas.isEmpty) {
                _availableFasilitas = ['Antar Jemput', 'Express', 'Paket Kiloan', 'Satuan'];
              }

              _loadingRange = false;
            });
          }
        }
      }
    } catch (e) {
      debugPrint('Load range data error: $e');
      if (mounted) {
        // Provide safe fallbacks so dropdowns appear even when the API fails
        setState(() {
          _loadingRange = false;
          if (_availableFasilitas.isEmpty) {
            _availableFasilitas = [
              'WiFi',
              'Parkir',
              'Dapur Bersama',
              'Lemari',
              'Air Panas',
            ];
          }
          if (_hargaMin == _hargaMax) {
            // ensure some sensible max for budget dropdown
            _hargaMax = _hargaMin + 500000; // add 500k fallback
          }
        });
      }
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

  /// ============================================================================
  /// AUTO-CALCULATE BOBOT FROM QUESTIONNAIRE ANSWERS
  /// ============================================================================
  /// Smart logic: infer user priority dari jawaban tanpa perlu slider
  /// IMPORTANT: Always ensure total = 100% (auto-normalize)
  void _calculateBobotFromQuestionnaire() {
    // Start with base values
    double hargaBobot = 50.0;
    double jarakBobot = 20.0;
    double fasilitasBobot = 15.0;
    double kamarBobot = 15.0;

    // Q1: Budget adjustment (sesuai data real dari database)
    if (_budgetSelected != null) {
      double budgetRange = (_hargaMax - _hargaMin).toDouble();
      double budgetPercent =
          (_budgetSelected! - _hargaMin.toDouble()) / budgetRange;

      if (budgetPercent < 0.3) {
        // Budget kecil (bottom 30%) → harga SUPER penting, tapi kamar tetap relevan
        hargaBobot = 70.0;
        fasilitasBobot = 15.0;
        jarakBobot = 10.0;
        kamarBobot = 5.0; // ← Kamar tetap penting (tidak 0%)
      } else if (budgetPercent < 0.7) {
        // Budget sedang (30-70%) → balanced
        hargaBobot = 50.0;
        fasilitasBobot = 30.0;
        jarakBobot = 15.0;
        kamarBobot = 5.0;
      } else {
        // Budget besar (top 30%) → fasilitas lebih penting
        hargaBobot = 30.0;
        fasilitasBobot = 35.0;
        jarakBobot = 20.0;
        kamarBobot = 15.0;
      }
    }

    // Q2: Facilities selected adjustment
    if (_selectedFacilities.isNotEmpty) {
      // Lebih banyak fasilitas critical → naikkan fasilitas weight
      if (_selectedFacilities.length >= 4) {
        fasilitasBobot += 10.0;
        // Kamar tetap penting, minimal 5%
        kamarBobot = (kamarBobot - 2.0).clamp(5.0, 40.0);
      }
    }

    // Q3: Distance preference adjustment
    if (_distancePreference == 'dekat') {
      jarakBobot += 15.0;
      // Jarak penting, tapi jangan reduce kamar terlalu banyak
      kamarBobot = (kamarBobot - 3.0).clamp(5.0, 40.0);
    } else if (_distancePreference == 'jauh_ok') {
      jarakBobot = (jarakBobot - 10.0).clamp(5.0, 40.0);
    }

    // Q4: Room preference adjustment
    // NEW LOGIC: 4+ kamar = very important, 2-3 kamar = medium, 1 kamar = low
    if (_roomPreference == '1_kamar') {
      // 1 kamar → kamar tidak terlalu penting (mahasiswa fokus hemat)
      kamarBobot = (kamarBobot - 5.0).clamp(5.0, 40.0);
      fasilitasBobot = (fasilitasBobot + 5.0).clamp(10.0, 50.0);
    } else if (_roomPreference == '2_3_kamar') {
      // 2-3 kamar → kamar agak penting
      kamarBobot = (kamarBobot + 10.0).clamp(5.0, 40.0);
      fasilitasBobot = (fasilitasBobot - 5.0).clamp(10.0, 40.0);
    } else if (_roomPreference == '4_plus_kamar') {
      // 4+ kamar → kamar SANGAT penting (premium selection)
      kamarBobot = (kamarBobot + 25.0).clamp(5.0, 40.0);
      fasilitasBobot = (fasilitasBobot - 15.0).clamp(10.0, 40.0);
    }

    // ============================================================
    // ENFORCE MINIMUM: Kamar ALWAYS >= 5% (penting untuk seleksi)
    // ============================================================
    kamarBobot = kamarBobot.clamp(5.0, 40.0);

    // ============================================================
    // NORMALIZE TO 100% - CRITICAL!
    // Simple proportional normalization (safest approach)
    // ============================================================
    double total = hargaBobot + jarakBobot + fasilitasBobot + kamarBobot;
    if (total > 0 && total != 100.0) {
      double factor = 100.0 / total;
      hargaBobot = (hargaBobot * factor).round().toDouble();
      jarakBobot = (jarakBobot * factor).round().toDouble();
      fasilitasBobot = (fasilitasBobot * factor).round().toDouble();

      // Last one gets exact remainder to guarantee total = 100
      double distributed = hargaBobot + jarakBobot + fasilitasBobot;
      kamarBobot = 100.0 - distributed;
    }

    // Final sanity check: ensure kamarBobot >= 5
    if (kamarBobot < 5.0) {
      // Adjust other bobot to accommodate minimum kamar
      double adjustment = 5.0 - kamarBobot;
      kamarBobot = 5.0;

      // Reduce harga first (usually most critical)
      if (hargaBobot > adjustment + 10.0) {
        hargaBobot -= adjustment;
      } else {
        // If can't reduce harga enough, reduce others
        hargaBobot = (hargaBobot - adjustment / 2.0).clamp(20.0, 80.0);
        fasilitasBobot = (fasilitasBobot - adjustment / 2.0).clamp(10.0, 50.0);
      }

      // Re-normalize to ensure total = 100
      double total2 = hargaBobot + jarakBobot + fasilitasBobot + kamarBobot;
      if (total2 != 100.0) {
        double factor2 = 100.0 / total2;
        hargaBobot = (hargaBobot * factor2).round().toDouble();
        jarakBobot = (jarakBobot * factor2).round().toDouble();
        fasilitasBobot = (fasilitasBobot * factor2).round().toDouble();

        double distributed2 = hargaBobot + jarakBobot + fasilitasBobot;
        kamarBobot = 100.0 - distributed2;
      }
    }

    // Final sanity check
    if (hargaBobot + jarakBobot + fasilitasBobot + kamarBobot != 100.0) {
      // If something weird happened, use defaults
      hargaBobot = 50.0;
      jarakBobot = 20.0;
      fasilitasBobot = 15.0;
      kamarBobot = 15.0;
    }

    setState(() {
      _bobotHarga = hargaBobot.toInt();
      _bobotJarak = jarakBobot.toInt();
      _bobotKriteria4 = fasilitasBobot.toInt(); // Fasilitas
      _bobotKriteria3 = kamarBobot.toInt(); // Kamar
    });
  }

  /// Check if questionnaire complete
  bool _isQuestionnaireComplete() {
    // Facilities optional: allow submit tanpa memilih fasilitas
    if (widget.category == 'kontrakan') {
      return _budgetSelected != null && _distancePreference.isNotEmpty && _roomPreference.isNotEmpty;
    } else {
      // Laundry: require jenis layanan + budget
      return _budgetSelected != null && _distancePreference.isNotEmpty && _selectedJenisLayanan.isNotEmpty;
    }
  }

  /// ============================================================================
  /// AUTO-BALANCE BOBOT (existing function - keep as is)
  /// ============================================================================
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

  /// Get the maximum slider value for a given bobot index (min 10, max 70).
  int _getMaxBobot(int index) {
    int othersMin = 0;
    for (int i = 0; i < 4; i++) {
      if (i != index) othersMin += 10; // minimum 10% each
    }
    return (100 - othersMin).clamp(10, 70); // max 70
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
        // CRITICAL: Send selected facilities to API for filtering
        bodyParams['selected_facilities'] = _selectedFacilities.toList();
      } else {
        final hasAntarJemput = _selectedFacilities.any(
          (f) => f.toLowerCase().contains('antar'),
        );
        bodyParams['bobot_kecepatan'] = _bobotKriteria3;
        bodyParams['bobot_layanan'] = _bobotKriteria4;
        // Choose selected jenis_layanan, or fall back to a sensible default
        final preferredJenis = _availableJenisLayanan.firstWhere(
          (e) => e.toLowerCase().contains('cuci'),
          orElse: () => _availableJenisLayanan.isNotEmpty ? _availableJenisLayanan.first : 'Cuci Baju');
        bodyParams['jenis_layanan'] = _selectedJenisLayanan.isNotEmpty ? _selectedJenisLayanan : preferredJenis;
        // optional: send selected facilities for laundry (e.g., express, antar)
        bodyParams['selected_facilities'] = _selectedFacilities.toList();
        // send preset and antar/jemput preferences
        bodyParams['preset'] = _selectedPreset;
        bodyParams['antar_jemput'] = hasAntarJemput ? 1 : 0;
        // rating filter removed
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
    if (_isDetectingLocation) return;

    setState(() => _isDetectingLocation = true);
    try {
      final locationService = LocationService();
      final position = await locationService.getCurrentLocation();

      if (position != null) {
        if (!mounted) return;
        setState(() {
          _userLatitude = position.latitude;
          _userLongitude = position.longitude;
        });
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Lokasi berhasil dideteksi!'),
            backgroundColor: Colors.green,
            duration: Duration(seconds: 2),
          ),
        );
        return;
      }

      if (!mounted) return;

      final permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied ||
          permission == LocationPermission.deniedForever) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              permission == LocationPermission.deniedForever
                  ? 'Izin lokasi ditolak permanen. Ubah di pengaturan aplikasi.'
                  : 'Izin lokasi ditolak. Aktifkan izin lokasi untuk melanjutkan.',
            ),
            backgroundColor: Colors.red,
            action: permission == LocationPermission.deniedForever
                ? SnackBarAction(
                    label: 'Buka',
                    onPressed: () => Geolocator.openAppSettings(),
                  )
                : null,
          ),
        );
        return;
      }

      final serviceEnabled = await Geolocator.isLocationServiceEnabled();
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            serviceEnabled
                ? 'Gagal mendapatkan lokasi. Coba lagi di area terbuka.'
                : 'GPS mati. Buka pengaturan lokasi?',
          ),
          backgroundColor: Colors.red,
          action: SnackBarAction(
            label: 'Buka',
            onPressed: () => Geolocator.openLocationSettings(),
          ),
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
    } finally {
      if (mounted) {
        setState(() => _isDetectingLocation = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final categoryTitle = widget.category == 'kontrakan'
        ? 'Rekomendasi Kontrakan'
        : 'Rekomendasi Laundry';

    // Show questionnaire first for kontrakan and laundry (only before calculation)
    if ((widget.category == 'kontrakan' || widget.category == 'laundry') && _showQuestionnaire && !_hasCalculated) {
      return _buildQuestionnaireView(categoryTitle);
    }

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

  /// ============================================================================
  /// QUESTIONNAIRE VIEW (Smart Questions untuk mahasiswa)
  /// ============================================================================
  /// ============================================================================
  /// DROPDOWN BUILDERS FOR QUESTIONNAIRE
  /// ============================================================================

  Widget _buildBudgetDropdown() {
    final range = (_hargaMax - _hargaMin).clamp(0, 100000000);
    final step = (range / 5).ceil().clamp(1, 100000000);
    List<double> budgetOptions = [];
    for (int i = 0; i <= 5; i++) {
      final value = _hargaMin + (i * step);
      budgetOptions.add(value.toDouble());
    }

    final label = _budgetSelected == null
        ? 'Pilih Budget...'
        : _formatCurrency(_budgetSelected!.toInt());

    return GestureDetector(
      onTap: () => _showBudgetModal(budgetOptions),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 14),
        decoration: BoxDecoration(
          border: Border.all(color: const Color(0xFFBDBDBD)),
          borderRadius: BorderRadius.circular(4),
          color: Colors.white,
        ),
        child: Row(
          children: [
            Expanded(child: Text(label, style: TextStyle(color: _budgetSelected == null ? const Color(0xFF999999) : Colors.black))),
            const SizedBox(width: 8),
            Icon(Icons.arrow_drop_down, color: Colors.grey[600]),
          ],
        ),
      ),
    );
  }

  Widget _buildFacilitiesDropdown() {
    if (_availableFasilitas.isEmpty) {
      return Padding(
        padding: const EdgeInsets.all(10),
        child: Text(
          'Fasilitas tidak tersedia',
          style: TextStyle(color: Colors.grey[600]),
        ),
      );
    }

    // Show as a single-line select field that opens a modal multi-select
    final placeholder = widget.category == 'laundry'
      ? 'Pilih Fasilitas (termasuk antar jemput)'
      : 'Pilih Fasilitas...';

    final summary = _selectedFacilities.isEmpty
      ? placeholder
      : _selectedFacilities.join(', ');

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        GestureDetector(
          onTap: _showFacilitiesModal,
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 14),
            decoration: BoxDecoration(
              border: Border.all(color: const Color(0xFFBDBDBD)),
              borderRadius: BorderRadius.circular(4),
              color: Colors.white,
            ),
            child: Row(
              children: [
                Expanded(
                  child: Text(
                    summary,
                    style: TextStyle(
                      color: _selectedFacilities.isEmpty
                          ? const Color(0xFF999999)
                          : Colors.black,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
                const SizedBox(width: 8),
                Icon(Icons.arrow_drop_down, color: Colors.grey[600]),
              ],
            ),
          ),
        ),
        if (_selectedFacilities.isNotEmpty) ...[
          const SizedBox(height: 12),
          Wrap(
            spacing: 8,
            runSpacing: 8,
            children: _selectedFacilities.map((facility) {
              return Chip(
                label: Text(facility, style: const TextStyle(fontSize: 12)),
                onDeleted: () {
                  setState(() => _selectedFacilities.remove(facility));
                },
                backgroundColor: _categoryColor.withOpacity(0.2),
                deleteIconColor: _categoryColor,
              );
            }).toList(),
          ),
        ],
      ],
    );
  }

  Widget _buildDistanceDropdown() {
    final distanceOptions = [
      {'label': 'Dekat (< 2km)', 'value': 'dekat'},
      {'label': 'Sedang (2-5km)', 'value': 'sedang'},
      {'label': 'Jauh OK (> 5km)', 'value': 'jauh_ok'},
      {'label': 'Tidak Peduli', 'value': 'tidak_peduli'},
    ];

    final label = distanceOptions
            .firstWhere((o) => o['value'] == _distancePreference, orElse: () => distanceOptions[1])['label'] ??
        'Pilih Jarak...';

    return GestureDetector(
      onTap: () => _showSingleChoiceModal('Pilih Jarak', distanceOptions,
          _distancePreference, (val) => setState(() => _distancePreference = val)),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 14),
        decoration: BoxDecoration(
          border: Border.all(color: const Color(0xFFBDBDBD)),
          borderRadius: BorderRadius.circular(4),
          color: Colors.white,
        ),
        child: Row(
          children: [
            Expanded(child: Text(label)),
            const SizedBox(width: 8),
            Icon(Icons.arrow_drop_down, color: Colors.grey[600]),
          ],
        ),
      ),
    );
  }

  Widget _buildRoomDropdown() {
    final roomOptions = [
      {'label': '1 Kamar (Hemat)', 'value': '1_kamar'},
      {'label': '2-3 Kamar (Sedang)', 'value': '2_3_kamar'},
      {'label': '4+ Kamar (Luas)', 'value': '4_plus_kamar'},
      {'label': 'Tidak Peduli', 'value': 'tidak_peduli'},
    ];

    final label = roomOptions
            .firstWhere((o) => o['value'] == _roomPreference, orElse: () => roomOptions[3])['label'] ??
        'Pilih Jumlah Kamar...';

    return GestureDetector(
      onTap: () => _showSingleChoiceModal('Pilih Jumlah Kamar', roomOptions,
          _roomPreference, (val) => setState(() => _roomPreference = val)),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 14),
        decoration: BoxDecoration(
          border: Border.all(color: const Color(0xFFBDBDBD)),
          borderRadius: BorderRadius.circular(4),
          color: Colors.white,
        ),
        child: Row(
          children: [
            Expanded(child: Text(label)),
            const SizedBox(width: 8),
            Icon(Icons.arrow_drop_down, color: Colors.grey[600]),
          ],
        ),
      ),
    );
  }

  Widget _buildJenisLaundryDropdown() {
    if (_availableJenisLayanan.isEmpty) {
      return Padding(
        padding: const EdgeInsets.all(10),
        child: Text(
          'Jenis layanan tidak tersedia',
          style: TextStyle(color: Colors.grey[600]),
        ),
      );
    }
    // Prefer a sensible default: any option containing 'cuci' (e.g., 'Cuci Baju')
    final preferred = _availableJenisLayanan.firstWhere(
        (e) => e.toLowerCase().contains('cuci'),
        orElse: () => _availableJenisLayanan.isNotEmpty ? _availableJenisLayanan.first : 'Cuci Baju');

    final displayValue = _selectedJenisLayanan.isNotEmpty ? _selectedJenisLayanan : preferred;

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 14),
      decoration: BoxDecoration(
        border: Border.all(color: const Color(0xFFBDBDBD)),
        borderRadius: BorderRadius.circular(4),
        color: Colors.white,
      ),
      child: Row(
        children: [
          Expanded(child: Text(displayValue, style: TextStyle(color: _selectedJenisLayanan.isEmpty ? const Color(0xFF666666) : Colors.black))),
          const SizedBox(width: 8),
          // Small edit affordance instead of full dropdown to keep default simple
          IconButton(
            padding: EdgeInsets.zero,
            constraints: const BoxConstraints(),
            icon: Icon(Icons.edit, color: Colors.grey[600]),
            onPressed: () => _showSingleChoiceModal('Pilih Jenis Layanan',
                _availableJenisLayanan.map((e) => {'label': e, 'value': e}).toList(), _selectedJenisLayanan,
                (val) => setState(() => _selectedJenisLayanan = val)),
          ),
        ],
      ),
    );
  }

  // rating UI removed per UX feedback

  Widget _buildLaundryPresets() {
    final presets = ['Normal', 'Cepat (Express)', 'Hemat'];
    return Row(
      children: presets.map((p) {
        final isSel = _selectedPreset == p;
        return Padding(
          padding: const EdgeInsets.only(right: 8),
          child: ChoiceChip(
            label: Text(p),
            selected: isSel,
            onSelected: (_) => setState(() => _selectedPreset = p),
            selectedColor: _categoryColor,
          ),
        );
      }).toList(),
    );
  }

  Widget _buildAntarJemputToggle() {
    return Row(
      children: [
        Switch(
          value: _antarJemput,
          onChanged: (v) => setState(() => _antarJemput = v),
          activeColor: _categoryColor,
        ),
        const SizedBox(width: 8),
        Text(_antarJemput ? 'Aktif' : 'Mati'),
      ],
    );
  }

  // Show a modal bottom sheet for multi-select facilities
  void _showFacilitiesModal() async {
    final selected = Set<String>.from(_selectedFacilities);

    await showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      builder: (ctx) {
        return StatefulBuilder(builder: (ctx2, setInner) {
          return Padding(
            padding: EdgeInsets.only(
                bottom: MediaQuery.of(ctx).viewInsets.bottom),
            child: Container(
              padding: const EdgeInsets.all(16),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Pilih Fasilitas', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                      TextButton(
                        onPressed: () {
                          setState(() {
                            _selectedFacilities = selected;
                          });
                          Navigator.of(ctx).pop();
                        },
                        child: const Text('Selesai'),
                      )
                    ],
                  ),
                  const SizedBox(height: 8),
                  Flexible(
                    child: SingleChildScrollView(
                      child: Column(
                        children: _availableFasilitas.map((f) {
                          final isSel = selected.contains(f);
                          return CheckboxListTile(
                            title: Text(f),
                            value: isSel,
                            onChanged: (v) => setInner(() => v == true ? selected.add(f) : selected.remove(f)),
                          );
                        }).toList(),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          );
        });
      },
    );
  }

  // Show a single-choice modal and call onSelected with chosen value
  void _showSingleChoiceModal(String title, List<Map<String, String>> options, String currentValue, Function(String) onSelected) async {
    String sel = currentValue;
    await showModalBottomSheet(
      context: context,
      builder: (ctx) {
        return StatefulBuilder(builder: (ctx2, setInner) {
          return Container(
            padding: const EdgeInsets.all(16),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(title, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                    TextButton(
                      onPressed: () {
                        onSelected(sel);
                        Navigator.of(ctx).pop();
                      },
                      child: const Text('OK'),
                    )
                  ],
                ),
                const SizedBox(height: 8),
                ...options.map((o) {
                  final v = o['value'] ?? '';
                  final label = o['label'] ?? v;
                  return RadioListTile<String>(
                    value: v,
                    groupValue: sel,
                    title: Text(label),
                    onChanged: (val) => setInner(() => sel = val ?? sel),
                  );
                }).toList(),
              ],
            ),
          );
        });
      },
    );
  }

  // Show budget modal with radio-like quick-picker and option list
  void _showBudgetModal(List<double> options) async {
    double sel = _budgetSelected ?? (options.isNotEmpty ? options[(options.length / 2).floor()] : 0);

    await showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      builder: (ctx) {
        return StatefulBuilder(builder: (ctx2, setInner) {
          return Padding(
            padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
            child: Container(
              padding: const EdgeInsets.all(16),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Pilih Budget', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                      TextButton(
                        onPressed: () {
                          setState(() => _budgetSelected = sel);
                          Navigator.of(ctx).pop();
                        },
                        child: const Text('Selesai'),
                      )
                    ],
                  ),
                  const SizedBox(height: 12),
                  Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    children: options.map((v) {
                      final label = _formatCurrency(v.toInt());
                      final isSel = sel == v;
                      return GestureDetector(
                        onTap: () => setInner(() => sel = v),
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                          decoration: BoxDecoration(
                            color: isSel ? _categoryColor : Colors.white,
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(color: isSel ? _categoryColor : const Color(0xFFE4EDF7)),
                          ),
                          child: Text(
                            label,
                            style: TextStyle(color: isSel ? Colors.white : Colors.black),
                          ),
                        ),
                      );
                    }).toList(),
                  ),
                  const SizedBox(height: 12),
                  // Also show full list as radio options for accessibility
                  ...options.map((v) {
                    return RadioListTile<double>(
                      value: v,
                      groupValue: sel,
                      title: Text(_formatCurrency(v.toInt())),
                      onChanged: (val) => setInner(() => sel = val ?? sel),
                    );
                  }).toList(),
                ],
              ),
            ),
          );
        });
      },
    );
  }

  /// ============================================================================
  /// QUESTIONNAIRE VIEW
  /// ============================================================================

  Widget _buildQuestionnaireView(String categoryTitle) {
    // Show loading state while fetching range data
    if (_loadingRange) {
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
        ),
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              CircularProgressIndicator(color: _categoryColor),
              const SizedBox(height: 16),
              const Text(
                'Mempersiapkan pertanyaan...',
                style: TextStyle(fontSize: 14, color: Colors.black54),
              ),
            ],
          ),
        ),
      );
    }

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
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header
            Container(
              padding: const EdgeInsets.all(16),
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
                      Icon(
                        Icons.lightbulb_rounded,
                        size: 20,
                        color: _categoryColor,
                      ),
                      const SizedBox(width: 8),
                      const Expanded(
                        child: Text(
                          'Ceritakan Kebutuhan Mu',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Text(
                    widget.category == 'kontrakan'
                        ? 'Kami akan cari kontrakan terbaik sesuai budget & kebutuhan mu'
                        : 'Kami akan cari laundry terbaik sesuai budget & kebutuhan mu',
                    style: TextStyle(
                      fontSize: 13,
                      color: Colors.black.withOpacity(0.6),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),
            // Questionnaire for kontrakan vs laundry
            if (widget.category == 'kontrakan') ...[
              _buildQuestionCard(
                title: '💰 Berapa Budget?',
                child: _buildBudgetDropdown(),
              ),
              const SizedBox(height: 16),
              _buildQuestionCard(
                title: '⭐ Fasilitas Wajib Ada?',
                child: _buildFacilitiesDropdown(),
              ),
              const SizedBox(height: 16),
              _buildQuestionCard(
                title: '📍 Jarak ke Kampus?',
                child: _buildDistanceDropdown(),
              ),
              const SizedBox(height: 16),
              _buildQuestionCard(
                title: '🛏️ Jumlah Kamar?',
                child: _buildRoomDropdown(),
              ),
            ] else ...[
              _buildQuestionCard(
                title: '🧾 Jenis Layanan',
                child: _buildJenisLaundryDropdown(),
              ),
              const SizedBox(height: 12),
              _buildQuestionCard(
                title: '⚡ Preset Cepat',
                child: _buildLaundryPresets(),
              ),
              const SizedBox(height: 12),
              _buildQuestionCard(
                title: '💰 Berapa Budget?',
                child: _buildBudgetDropdown(),
              ),
              const SizedBox(height: 16),
              _buildQuestionCard(
                title: '📍 Jarak ke Lokasi?',
                child: _buildDistanceDropdown(),
              ),
              const SizedBox(height: 16),
              // rating removed per UX feedback
              _buildQuestionCard(
                title: '🔸 Fasilitas (termasuk antar jemput)',
                child: _buildFacilitiesDropdown(),
              ),
            ],
            const SizedBox(height: 24),

            // Submit Button
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: _categoryColor,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10),
                  ),
                  disabledBackgroundColor: Colors.grey,
                ),
                onPressed: _isQuestionnaireComplete()
                    ? () {
                        _calculateBobotFromQuestionnaire();
                        setState(() => _hasCalculated = true);
                        // CRITICAL: Call API SAW to get recommendations with calculated bobot
                        _calculateSAW();
                      }
                    : null,
                child: Text(
                  widget.category == 'kontrakan' ? 'TEMUKAN KONTRAKAN' : 'TEMUKAN LAUNDRY',
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                    color: Colors.white,
                    letterSpacing: 0.5,
                  ),
                ),
              ),
            ),
            const SizedBox(height: 24),
          ],
        ),
      ),
    );
  }

  // Helper widgets untuk questionnaire
  Widget _buildQuestionCard({required String title, required Widget child}) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFE4EDF7)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 14),
          child,
        ],
      ),
    );
  }

  /// Helper: Format currency to readable format
  String _formatCurrency(int value) {
    if (value >= 1000000) {
      return 'Rp ${(value / 1000000).toStringAsFixed(1)}jt';
    } else if (value >= 1000) {
      return 'Rp ${(value / 1000).toStringAsFixed(0)}rb';
    } else {
      return 'Rp $value';
    }
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
    final kriteria4 = 'fasilitas';

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
            'Geser slider pada setiap kriteria (10%–70%). Total akan disesuaikan otomatis menjadi 100%.',
            style: TextStyle(fontSize: 11, color: Colors.grey[500]),
          ),
          const SizedBox(height: 12),
          _buildBobotStackedBar(),
          const SizedBox(height: 12),
          _buildBobotRows(),
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
                    selectedJenisLayanan: _selectedJenisLayanan,
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
