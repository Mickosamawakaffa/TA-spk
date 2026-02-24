import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../models/laundry.dart';
import '../services/location_service.dart';
import '../services/favorite_service.dart';

class LaundryDetailScreen extends StatefulWidget {
  final Laundry laundry;

  const LaundryDetailScreen({super.key, required this.laundry});

  @override
  State<LaundryDetailScreen> createState() => _LaundryDetailScreenState();
}

class _LaundryDetailScreenState extends State<LaundryDetailScreen> {
  double? userLat;
  double? userLng;
  double? distance;
  bool isLoadingLocation = false;
  String? locationError;
  bool _isFavorite = false;
  bool _isFavLoading = false;
  final _favoriteService = FavoriteService();

  @override
  void initState() {
    super.initState();
    _checkFavorite();
  }

  Future<void> _checkFavorite() async {
    final result = await _favoriteService.isLaundryFavorite(widget.laundry.id);
    if (mounted) setState(() => _isFavorite = result);
  }

  Future<void> _toggleFavorite() async {
    setState(() => _isFavLoading = true);
    final result = await _favoriteService.toggleLaundryFavorite(widget.laundry.id);
    if (mounted) {
      setState(() {
        _isFavLoading = false;
        if (result['success'] == true) {
          _isFavorite = result['isFavorite'] ?? !_isFavorite;
        }
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Row(
            children: [
              Icon(
                _isFavorite ? Icons.favorite_rounded : Icons.favorite_border_rounded,
                color: Colors.white, size: 20,
              ),
              const SizedBox(width: 10),
              Text(result['message'] ?? 'Status favorit diubah'),
            ],
          ),
          backgroundColor: _isFavorite ? const Color(0xFF00897B) : Colors.grey[700],
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          margin: const EdgeInsets.all(16),
          duration: const Duration(seconds: 2),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF7F8FC),
      body: CustomScrollView(
        slivers: [
          // App Bar with gradient
          SliverAppBar(
            expandedHeight: 200,
            pinned: true,
            backgroundColor: const Color(0xFF00897B),
            actions: [
              _isFavLoading
                  ? const Padding(
                      padding: EdgeInsets.all(14),
                      child: SizedBox(
                        width: 20,
                        height: 20,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          color: Colors.white,
                        ),
                      ),
                    )
                  : IconButton(
                      onPressed: _toggleFavorite,
                      icon: Icon(
                        _isFavorite ? Icons.favorite_rounded : Icons.favorite_border_rounded,
                        color: _isFavorite ? Colors.redAccent : Colors.white,
                        size: 26,
                      ),
                    ),
            ],
            flexibleSpace: FlexibleSpaceBar(
              background: Container(
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [Color(0xFF00897B), Color(0xFF00695C)],
                  ),
                ),
                child: SafeArea(
                  child: Padding(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.end,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.all(16),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(16),
                                boxShadow: [
                                  BoxShadow(
                                    color: Colors.black.withValues(alpha: 0.2),
                                    blurRadius: 10,
                                    offset: const Offset(0, 4),
                                  ),
                                ],
                              ),
                              child: const Icon(
                                Icons.local_laundry_service,
                                size: 40,
                                color: Color(0xFF00897B),
                              ),
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    widget.laundry.nama,
                                    style: const TextStyle(
                                      fontSize: 24,
                                      fontWeight: FontWeight.bold,
                                      color: Colors.white,
                                    ),
                                  ),
                                  const SizedBox(height: 4),
                                  Row(
                                    children: [
                                      Icon(
                                        Icons.star,
                                        size: 20,
                                        color: Colors.amber[300],
                                      ),
                                      const SizedBox(width: 6),
                                      Text(
                                        widget.laundry.rating.toStringAsFixed(
                                          1,
                                        ),
                                        style: TextStyle(
                                          fontSize: 16,
                                          fontWeight: FontWeight.w600,
                                          color: Colors.white.withValues(
                                            alpha: 0.95,
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ),

          // Content
          SliverPadding(
            padding: const EdgeInsets.all(16),
            sliver: SliverList(
              delegate: SliverChildListDelegate([
                // Status Card
                _buildStatusCard(),
                const SizedBox(height: 16),

                // Location Detection Card
                if (widget.laundry.latitude != null &&
                    widget.laundry.longitude != null)
                  _buildLocationCard(),
                const SizedBox(height: 16),

                // Info Section
                _buildSection(
                  icon: Icons.info_outline,
                  title: 'Informasi',
                  child: Column(
                    children: [
                      _buildInfoRow(
                        Icons.location_on,
                        'Alamat',
                        widget.laundry.alamat,
                      ),
                      _buildInfoRow(
                        Icons.access_time,
                        'Jam Operasional',
                        '${widget.laundry.jamBuka} - ${widget.laundry.jamTutup}',
                      ),
                      _buildInfoRow(
                        Icons.schedule,
                        'Estimasi Selesai',
                        '${widget.laundry.estimasiSelesai} jam',
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 16),

                // Pricing Section
                _buildSection(
                  icon: Icons.price_check,
                  title: 'Harga',
                  child: Column(
                    children: [
                      _buildPriceCard(
                        'Laundry Kiloan',
                        widget.laundry.formattedHargaKiloan,
                        Icons.scale,
                        const Color(0xFF00897B),
                      ),
                      const SizedBox(height: 12),
                      _buildPriceCard(
                        'Laundry Satuan',
                        widget.laundry.formattedHargaSatuan,
                        Icons.checkroom,
                        Colors.purple,
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 24),

                // Action Buttons
                if (widget.laundry.noWhatsapp != null) ...[
                  SizedBox(
                    width: double.infinity,
                    height: 50,
                    child: ElevatedButton.icon(
                      onPressed: () =>
                          _launchWhatsApp(widget.laundry.noWhatsapp!),
                      icon: const Icon(Icons.message, size: 24),
                      label: const Text(
                        'Hubungi via WhatsApp',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF25D366),
                        foregroundColor: Colors.white,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 12),
                ],

                if (widget.laundry.latitude != null &&
                    widget.laundry.longitude != null)
                  SizedBox(
                    width: double.infinity,
                    height: 50,
                    child: OutlinedButton.icon(
                      onPressed: () => _launchMaps(
                        widget.laundry.latitude!,
                        widget.laundry.longitude!,
                      ),
                      icon: const Icon(Icons.map, size: 24),
                      label: const Text(
                        'Lihat di Maps',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      style: OutlinedButton.styleFrom(
                        foregroundColor: const Color(0xFF00897B),
                        side: const BorderSide(
                          color: Color(0xFF00897B),
                          width: 2,
                        ),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                    ),
                  ),

                const SizedBox(height: 24),
              ]),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatusCard() {
    Color statusColor = widget.laundry.status == 'buka'
        ? Colors.green
        : Colors.red;
    if (widget.laundry.status == 'buka' && !widget.laundry.isOpen) {
      statusColor = Colors.orange;
    }

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: statusColor.withValues(alpha: 0.08),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: statusColor.withOpacity(0.3), width: 1.5),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: statusColor.withOpacity(0.15),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(Icons.store_rounded, color: statusColor, size: 24),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Status Toko',
                  style: TextStyle(fontSize: 14, color: Colors.grey[700]),
                ),
                const SizedBox(height: 4),
                Text(
                  widget.laundry.statusText,
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    color: statusColor,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLocationCard() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.purple.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: Colors.purple.withValues(alpha: 0.3),
          width: 1,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Icon(Icons.location_on, color: Colors.purple, size: 24),
                  const SizedBox(width: 12),
                  Text(
                    'Deteksi Lokasi Saya',
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: Colors.black87,
                    ),
                  ),
                ],
              ),
              if (isLoadingLocation)
                SizedBox(
                  width: 20,
                  height: 20,
                  child: CircularProgressIndicator(
                    strokeWidth: 2,
                    valueColor: AlwaysStoppedAnimation<Color>(Colors.purple),
                  ),
                ),
            ],
          ),
          const SizedBox(height: 12),
          if (distance != null) ...[
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.green.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Row(
                children: [
                  Icon(Icons.check_circle, color: Colors.green, size: 20),
                  const SizedBox(width: 10),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Jarak dari Lokasi Saya',
                        style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                      ),
                      Text(
                        distance! < 1
                            ? '${(distance! * 1000).toStringAsFixed(0)} m'
                            : '${distance!.toStringAsFixed(2)} km',
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color: Colors.green,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ] else if (locationError != null) ...[
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.red.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Row(
                children: [
                  Icon(Icons.error, color: Colors.red, size: 20),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Text(
                      locationError!,
                      style: const TextStyle(fontSize: 13, color: Colors.red),
                    ),
                  ),
                ],
              ),
            ),
          ],
          const SizedBox(height: 12),
          SizedBox(
            width: double.infinity,
            height: 44,
            child: ElevatedButton.icon(
              onPressed: isLoadingLocation ? null : _detectLocation,
              icon: Icon(
                isLoadingLocation ? Icons.hourglass_bottom : Icons.my_location,
                size: 20,
              ),
              label: Text(
                isLoadingLocation ? 'Mendeteksi...' : 'Deteksi Lokasi Saya',
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                ),
              ),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.purple,
                foregroundColor: Colors.white,
                disabledBackgroundColor: Colors.grey,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Future<void> _detectLocation() async {
    setState(() {
      isLoadingLocation = true;
      locationError = null;
    });

    try {
      final locationService = LocationService();
      final isEnabled = await locationService.isLocationServiceEnabled();

      if (!isEnabled) {
        setState(() {
          locationError = 'Layanan lokasi tidak aktif';
          isLoadingLocation = false;
        });
        return;
      }

      final position = await locationService.getCurrentLocation();

      if (position != null) {
        final dist = LocationService.calculateDistance(
          position.latitude,
          position.longitude,
          widget.laundry.latitude!,
          widget.laundry.longitude!,
        );

        setState(() {
          userLat = position.latitude;
          userLng = position.longitude;
          distance = dist;
          isLoadingLocation = false;
        });
      } else {
        setState(() {
          locationError = 'Gagal mendapatkan lokasi Anda';
          isLoadingLocation = false;
        });
      }
    } catch (e) {
      setState(() {
        locationError = 'Error: ${e.toString()}';
        isLoadingLocation = false;
      });
    }
  }

  Widget _buildSection({
    required IconData icon,
    required String title,
    required Widget child,
  }) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: const Color(0xFF00897B).withOpacity(0.1),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(icon, color: const Color(0xFF00897B), size: 20),
              ),
              const SizedBox(width: 10),
              Text(
                title,
                style: const TextStyle(
                  fontSize: 17,
                  fontWeight: FontWeight.w700,
                  color: Color(0xFF1A1A2E),
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          child,
        ],
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 14),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(6),
            decoration: BoxDecoration(
              color: const Color(0xFF00897B).withOpacity(0.08),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(icon, size: 18, color: const Color(0xFF00897B)),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: TextStyle(fontSize: 12, color: Colors.grey[500], fontWeight: FontWeight.w500),
                ),
                const SizedBox(height: 3),
                Text(
                  value,
                  style: const TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w600,
                    color: Color(0xFF1A1A2E),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPriceCard(
    String title,
    String price,
    IconData icon,
    Color color,
  ) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: color.withValues(alpha: 0.3)),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: color,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: Colors.white, size: 24),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: TextStyle(fontSize: 14, color: Colors.grey[700]),
                ),
                const SizedBox(height: 4),
                Text(
                  price,
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    color: color,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Future<void> _launchWhatsApp(String phone) async {
    final url = 'https://wa.me/$phone';
    if (await canLaunchUrl(Uri.parse(url))) {
      await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
    }
  }

  Future<void> _launchMaps(double lat, double lng) async {
    final url = 'https://www.google.com/maps/search/?api=1&query=$lat,$lng';
    if (await canLaunchUrl(Uri.parse(url))) {
      await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
    }
  }
}
