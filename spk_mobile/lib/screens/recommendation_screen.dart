import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:geolocator/geolocator.dart';
import '../config/app_config.dart';
import '../models/kontrakan.dart';
import '../models/laundry.dart';
import '../widgets/kontrakan_card.dart';
import '../widgets/laundry_card.dart';

class RecommendationScreen extends StatefulWidget {
  final String category; // 'kontrakan' atau 'laundry'

  const RecommendationScreen({Key? key, required this.category})
      : super(key: key);

  @override
  State<RecommendationScreen> createState() => _RecommendationScreenState();
}

class _RecommendationScreenState extends State<RecommendationScreen> {
  bool _isLoading = false;
  List<dynamic> _recommendations = [];
  String? _errorMessage;

  // Filter values
  double? _hargaMin;
  double? _hargaMax;
  double? _jarakMax;
  int? _jumlahKamar;
  String? _fasilitas;
  
  // Location values (for laundry)
  bool _useUserLocation = false;
  double? _userLatitude;
  double? _userLongitude;

  @override
  void initState() {
    super.initState();
    _loadRecommendations();
  }

  Future<void> _loadRecommendations() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final endpoint = widget.category == 'kontrakan'
          ? '/saw/calculate/kontrakan'
          : '/saw/calculate/laundry';

      // Build body parameters
      final bodyParams = <String, dynamic>{};

      if (_hargaMin != null) bodyParams['harga_min'] = _hargaMin;
      if (_hargaMax != null) bodyParams['harga_max'] = _hargaMax;
      if (_jarakMax != null) bodyParams['jarak_max'] = _jarakMax;
      if (_jumlahKamar != null) bodyParams['jumlah_kamar'] = _jumlahKamar;
      if (_fasilitas != null && _fasilitas!.isNotEmpty) {
        bodyParams['fasilitas'] = _fasilitas;
      }
      // Add user location if enabled (for laundry only)
      if (widget.category == 'laundry' && _useUserLocation) {
        if (_userLatitude != null && _userLongitude != null) {
          bodyParams['user_lat'] = _userLatitude;
          bodyParams['user_lng'] = _userLongitude;
        }
      }

      final response = await http.post(
        Uri.parse('${AppConfig.baseUrl}$endpoint'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: json.encode(bodyParams),
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true) {
          setState(() {
            _recommendations = data['data']['hasil'] ?? [];
          });
        } else {
          setState(() {
            _errorMessage = data['message'] ?? 'Terjadi kesalahan';
          });
        }
      } else {
        setState(() {
          _errorMessage = 'Gagal memuat data. Coba lagi nanti.';
        });
      }
    } catch (e) {
      setState(() {
        _errorMessage = 'Tidak dapat terhubung ke server';
      });
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final categoryTitle = widget.category == 'kontrakan' 
        ? 'Rekomendasi Kontrakan' 
        : 'Rekomendasi Laundry';
    
    final categoryIcon = widget.category == 'kontrakan' 
        ? Icons.home 
        : Icons.local_laundry_service;
        
    final categoryColor = widget.category == 'kontrakan'
        ? const Color(0xFF1565C0)
        : const Color(0xFF00ACC1);

    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      appBar: AppBar(
        backgroundColor: categoryColor,
        foregroundColor: Colors.white,
        elevation: 0,
        title: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(categoryIcon, size: 20),
            const SizedBox(width: 8),
            Text(
              categoryTitle,
              style: const TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: _showFilterDialog,
          ),
        ],
      ),
      body: Column(
        children: [
          // Location Status Banner (only for laundry)
          if (widget.category == 'laundry' && _useUserLocation)
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF4CAF50), Color(0xFF66BB6A)],
                ),
                boxShadow: [
                  BoxShadow(
                    color: Colors.green.withOpacity(0.3),
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: const Icon(
                      Icons.my_location,
                      color: Colors.white,
                      size: 20,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          '📍 Menggunakan Lokasi Anda',
                          style: TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.bold,
                            fontSize: 13,
                          ),
                        ),
                        if (_userLatitude != null && _userLongitude != null)
                          Text(
                            'Lat: ${_userLatitude!.toStringAsFixed(4)}, Lng: ${_userLongitude!.toStringAsFixed(4)}',
                            style: TextStyle(
                              color: Colors.white.withOpacity(0.9),
                              fontSize: 10,
                            ),
                          ),
                      ],
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close, color: Colors.white, size: 18),
                    padding: EdgeInsets.zero,
                    constraints: const BoxConstraints(),
                    onPressed: () {
                      setState(() {
                        _useUserLocation = false;
                        _userLatitude = null;
                        _userLongitude = null;
                      });
                      _loadRecommendations();
                    },
                  ),
                ],
              ),
            ),
          
          // Filter Summary
          if (_hasActiveFilters()) _buildFilterSummary(categoryColor),
          
          // Content
          Expanded(
            child: _buildContent(),
          ),
        ],
      ),
    );
  }

  bool _hasActiveFilters() {
    return _hargaMin != null ||
        _hargaMax != null ||
        _jarakMax != null ||
        _jumlahKamar != null ||
        (_fasilitas != null && _fasilitas!.isNotEmpty) ||
        _useUserLocation;
  }

  Widget _buildFilterSummary(Color categoryColor) {
    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: categoryColor.withOpacity(0.1),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: categoryColor.withOpacity(0.3)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(
                Icons.filter_alt,
                color: categoryColor,
                size: 16,
              ),
              const SizedBox(width: 8),
              Text(
                'Filter Aktif:',
                style: TextStyle(
                  fontWeight: FontWeight.bold,
                  color: categoryColor,
                  fontSize: 12,
                ),
              ),
              const Spacer(),
              GestureDetector(
                onTap: _clearFilters,
                child: Text(
                  'Hapus Semua',
                  style: TextStyle(
                    color: categoryColor,
                    fontSize: 12,
                    fontWeight: FontWeight.w500,
                    decoration: TextDecoration.underline,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Wrap(
            spacing: 8,
            runSpacing: 4,
            children: [
              if (_hargaMin != null || _hargaMax != null)
                _buildFilterChip(
                  'Harga: ${_formatPrice(_hargaMin)} - ${_formatPrice(_hargaMax)}',
                  categoryColor,
                ),
              if (_jarakMax != null)
                _buildFilterChip(
                  'Jarak: ≤ ${_jarakMax!.toStringAsFixed(1)} km',
                  categoryColor,
                ),
              if (_jumlahKamar != null)
                _buildFilterChip(
                  'Kamar: $_jumlahKamar',
                  categoryColor,
                ),
              if (_fasilitas != null && _fasilitas!.isNotEmpty)
                _buildFilterChip(
                  'Fasilitas: $_fasilitas',
                  categoryColor,
                ),
              if (_useUserLocation)
                _buildFilterChip(
                  '📍 Dari Lokasi Saya',
                  Colors.green,
                ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildFilterChip(String text, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: color.withOpacity(0.3)),
      ),
      child: Text(
        text,
        style: TextStyle(
          fontSize: 11,
          color: color,
          fontWeight: FontWeight.w500,
        ),
      ),
    );
  }

  String _formatPrice(double? price) {
    if (price == null) return 'N/A';
    if (price >= 1000000) {
      return '${(price / 1000000).toStringAsFixed(0)}jt';
    } else if (price >= 1000) {
      return '${(price / 1000).toStringAsFixed(0)}rb';
    } else {
      return price.toStringAsFixed(0);
    }
  }

  void _clearFilters() {
    setState(() {
      _hargaMin = null;
      _hargaMax = null;
      _jarakMax = null;
      _jumlahKamar = null;
      _fasilitas = null;
      _useUserLocation = false;
      _userLatitude = null;
      _userLongitude = null;
    });
    _loadRecommendations();
  }

  Widget _buildContent() {
    if (_isLoading) {
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            CircularProgressIndicator(),
            SizedBox(height: 16),
            Text('Memuat rekomendasi terbaik...'),
          ],
        ),
      );
    }

    if (_errorMessage != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(32),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                Icons.error_outline,
                size: 64,
                color: Colors.grey[400],
              ),
              const SizedBox(height: 16),
              Text(
                'Oops!',
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
                style: TextStyle(
                  color: Colors.grey[600],
                ),
              ),
              const SizedBox(height: 24),
              ElevatedButton.icon(
                onPressed: _loadRecommendations,
                icon: const Icon(Icons.refresh),
                label: const Text('Coba Lagi'),
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
                'Tidak Ada Hasil',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: Colors.grey[700],
                ),
              ),
              const SizedBox(height: 8),
              Text(
                'Tidak ditemukan ${widget.category} yang sesuai dengan kriteria Anda.',
                textAlign: TextAlign.center,
                style: TextStyle(
                  color: Colors.grey[600],
                ),
              ),
              const SizedBox(height: 24),
              ElevatedButton.icon(
                onPressed: () {
                  _clearFilters();
                },
                icon: const Icon(Icons.clear_all),
                label: const Text('Reset Filter'),
              ),
            ],
          ),
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _loadRecommendations,
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: _recommendations.length,
        itemBuilder: (context, index) {
          final item = _recommendations[index];
          final itemData = item['data'] ?? item;
          
          // Add ranking badge
          return Container(
            margin: const EdgeInsets.only(bottom: 16),
            child: Stack(
              children: [
                // Main card
                widget.category == 'kontrakan'
                    ? KontrakanCard(kontrakan: Kontrakan.fromJson(itemData))
                    : LaundryCard(laundry: Laundry.fromJson(itemData)),
                
                // Ranking badge
                Positioned(
                  top: 8,
                  left: 8,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: _getRankingColor(index + 1),
                      borderRadius: BorderRadius.circular(12),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.2),
                          blurRadius: 4,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(
                          _getRankingIcon(index + 1),
                          color: Colors.white,
                          size: 14,
                        ),
                        const SizedBox(width: 4),
                        Text(
                          '#${index + 1}',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  Color _getRankingColor(int rank) {
    switch (rank) {
      case 1:
        return const Color(0xFFFFD700); // Gold
      case 2:
        return const Color(0xFFC0C0C0); // Silver
      case 3:
        return const Color(0xFFCD7F32); // Bronze
      default:
        return const Color(0xFF1565C0); // Blue
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

  void _showFilterDialog() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => StatefulBuilder(
        builder: (BuildContext context, StateSetter setModalState) {
          return Container(
            decoration: const BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
            ),
            padding: EdgeInsets.only(
              bottom: MediaQuery.of(ctx).viewInsets.bottom,
              left: 20,
              right: 20,
              top: 20,
            ),
            child: SingleChildScrollView(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Header
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          Icon(
                            Icons.filter_list,
                            color: widget.category == 'kontrakan'
                                ? const Color(0xFF1565C0)
                                : const Color(0xFF00ACC1),
                          ),
                          const SizedBox(width: 8),
                          Text(
                            'Filter Pencarian',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                              color: Colors.grey[800],
                            ),
                      ),
                    ],
                  ),
                  IconButton(
                    icon: const Icon(Icons.close),
                    onPressed: () => Navigator.pop(ctx),
                  ),
                ],
              ),
              const SizedBox(height: 20),

              // Harga Range
              _buildFilterSection(
                'Rentang Harga',
                Icons.attach_money,
                [
                  Row(
                    children: [
                      Expanded(
                        child: TextFormField(
                          decoration: const InputDecoration(
                            labelText: 'Harga Min',
                            prefixText: 'Rp ',
                            border: OutlineInputBorder(),
                            contentPadding: EdgeInsets.symmetric(
                              horizontal: 12,
                              vertical: 8,
                            ),
                          ),
                          keyboardType: TextInputType.number,
                          onChanged: (value) {
                            _hargaMin = double.tryParse(value);
                          },
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: TextFormField(
                          decoration: const InputDecoration(
                            labelText: 'Harga Max',
                            prefixText: 'Rp ',
                            border: OutlineInputBorder(),
                            contentPadding: EdgeInsets.symmetric(
                              horizontal: 12,
                              vertical: 8,
                            ),
                          ),
                          keyboardType: TextInputType.number,
                          onChanged: (value) {
                            _hargaMax = double.tryParse(value);
                          },
                        ),
                      ),
                    ],
                  ),
                ],
              ),

              // Jarak Maksimal
              _buildFilterSection(
                'Jarak Maksimal dari Kampus',
                Icons.location_on,
                [
                  TextFormField(
                    decoration: const InputDecoration(
                      labelText: 'Jarak Maksimal (km)',
                      suffixText: 'km',
                      border: OutlineInputBorder(),
                      contentPadding: EdgeInsets.symmetric(
                        horizontal: 12,
                        vertical: 8,
                      ),
                    ),
                    keyboardType: const TextInputType.numberWithOptions(decimal: true),
                    onChanged: (value) {
                      _jarakMax = double.tryParse(value);
                    },
                  ),
                ],
              ),

              // Jumlah Kamar (hanya untuk kontrakan)
              if (widget.category == 'kontrakan')
                _buildFilterSection(
                  'Jumlah Kamar',
                  Icons.hotel,
                  [
                    TextFormField(
                      decoration: const InputDecoration(
                        labelText: 'Jumlah Kamar Minimum',
                        suffixText: 'kamar',
                        border: OutlineInputBorder(),
                        contentPadding: EdgeInsets.symmetric(
                          horizontal: 12,
                          vertical: 8,
                        ),
                      ),
                      keyboardType: TextInputType.number,
                      onChanged: (value) {
                        _jumlahKamar = int.tryParse(value);
                      },
                    ),
                  ],
                ),

              // Fasilitas
              _buildFilterSection(
                'Fasilitas',
                Icons.wifi,
                [
                  TextFormField(
                    decoration: const InputDecoration(
                      labelText: 'Cari fasilitas...',
                      hintText: 'WiFi, AC, Parkir, dll',
                      border: OutlineInputBorder(),
                      contentPadding: EdgeInsets.symmetric(
                        horizontal: 12,
                        vertical: 8,
                      ),
                    ),
                    onChanged: (value) {
                      _fasilitas = value.isEmpty ? null : value;
                    },
                  ),
                ],
              ),

              // Lokasi User (hanya untuk Laundry)
              if (widget.category == 'laundry')
                _buildFilterSection(
                  'Referensi Jarak',
                  Icons.my_location,
                  [
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: Colors.cyan.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(
                          color: Colors.cyan.withOpacity(0.3),
                        ),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Icon(
                                _useUserLocation
                                    ? Icons.location_on
                                    : Icons.business,
                                size: 20,
                                color: const Color(0xFF00ACC1),
                              ),
                              const SizedBox(width: 8),
                              Expanded(
                                child: Text(
                                  _useUserLocation
                                      ? 'Dari Lokasi Saya'
                                      : 'Dari Kampus',
                                  style: const TextStyle(
                                    fontWeight: FontWeight.bold,
                                    fontSize: 14,
                                  ),
                                ),
                              ),
                              Switch(
                                value: _useUserLocation,
                                onChanged: (value) {
                                  setModalState(() {
                                    setState(() {
                                      _useUserLocation = value;
                                      if (value) {
                                        _detectUserLocation();
                                      } else {
                                        _userLatitude = null;
                                        _userLongitude = null;
                                      }
                                    });
                                  });
                                },
                                activeColor: const Color(0xFF00ACC1),
                              ),
                            ],
                          ),
                          const SizedBox(height: 8),
                          Text(
                            _useUserLocation
                                ? 'Jarak dihitung dari lokasi Anda saat ini'
                                : 'Jarak dihitung dari Kampus Polije',
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.grey[600],
                            ),
                          ),
                          if (_useUserLocation &&
                              _userLatitude != null &&
                              _userLongitude != null)
                            Padding(
                              padding: const EdgeInsets.only(top: 8),
                              child: Container(
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  color: Colors.green.withOpacity(0.1),
                                  borderRadius: BorderRadius.circular(6),
                                ),
                                child: Row(
                                  children: [
                                    const Icon(
                                      Icons.check_circle,
                                      size: 16,
                                      color: Colors.green,
                                    ),
                                    const SizedBox(width: 6),
                                    Expanded(
                                      child: Text(
                                        'Lokasi terdeteksi: ${_userLatitude!.toStringAsFixed(6)}, ${_userLongitude!.toStringAsFixed(6)}',
                                        style: const TextStyle(
                                          fontSize: 11,
                                          color: Colors.green,
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                        ],
                      ),
                    ),
                  ],
                ),

              // Action Buttons
              const SizedBox(height: 20),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () {
                        _clearFilters();
                        Navigator.pop(ctx);
                      },
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        side: BorderSide(
                          color: widget.category == 'kontrakan'
                              ? const Color(0xFF1565C0)
                              : const Color(0xFF00ACC1),
                        ),
                      ),
                      child: Text(
                        'Reset',
                        style: TextStyle(
                          color: widget.category == 'kontrakan'
                              ? const Color(0xFF1565C0)
                              : const Color(0xFF00ACC1),
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    flex: 2,
                    child: ElevatedButton(
                      onPressed: () {
                        Navigator.pop(ctx);
                        _loadRecommendations();
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: widget.category == 'kontrakan'
                            ? const Color(0xFF1565C0)
                            : const Color(0xFF00ACC1),
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8),
                        ),
                      ),
                      child: const Text(
                        'Terapkan Filter',
                        style: TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 20),
            ],
          ),
        ),
      );
        },
      ),
    );
  }

  Widget _buildFilterSection(
    String title,
    IconData icon,
    List<Widget> children,
  ) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Icon(
              icon,
              size: 16,
              color: widget.category == 'kontrakan'
                  ? const Color(0xFF1565C0)
                  : const Color(0xFF00ACC1),
            ),
            const SizedBox(width: 8),
            Text(
              title,
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w600,
                color: Colors.grey[700],
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        ...children,
        const SizedBox(height: 20),
      ],
    );
  }

  Future<void> _detectUserLocation() async {
    try {
      // Check if location service is enabled
      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Layanan lokasi tidak aktif. Aktifkan GPS Anda.'),
              backgroundColor: Colors.red,
            ),
          );
        }
        setState(() => _useUserLocation = false);
        return;
      }

      // Check permission
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
          setState(() => _useUserLocation = false);
          return;
        }
      }

      if (permission == LocationPermission.deniedForever) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Izin lokasi ditolak permanen. Ubah di pengaturan.'),
              backgroundColor: Colors.red,
            ),
          );
        }
        setState(() => _useUserLocation = false);
        return;
      }

      // Get position
      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      );

      setState(() {
        _userLatitude = position.latitude;
        _userLongitude = position.longitude;
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
      setState(() => _useUserLocation = false);
    }
  }
}
