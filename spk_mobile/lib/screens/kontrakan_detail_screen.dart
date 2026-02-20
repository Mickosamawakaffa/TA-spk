import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../models/kontrakan.dart';
import '../services/location_service.dart';
import '../services/auth_service.dart';
import 'booking_form_screen.dart';

class KontrakanDetailScreen extends StatefulWidget {
  final Kontrakan kontrakan;

  const KontrakanDetailScreen({super.key, required this.kontrakan});

  @override
  State<KontrakanDetailScreen> createState() => _KontrakanDetailScreenState();
}

class _KontrakanDetailScreenState extends State<KontrakanDetailScreen> {
  double? userLat;
  double? userLng;
  double? distance;
  bool isLoadingLocation = false;
  String? locationError;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      appBar: AppBar(
        title: const Text('Detail Kontrakan'),
        backgroundColor: const Color(0xFF1565C0),
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Image Gallery
            SizedBox(
              height: 250,
              child: PageView.builder(
                itemCount: widget.kontrakan.galeri.isEmpty
                    ? 1
                    : widget.kontrakan.galeri.length,
                itemBuilder: (context, index) {
                  final imageUrl = widget.kontrakan.galeri.isEmpty
                      ? widget.kontrakan.primaryPhoto
                      : widget.kontrakan.galeri[index].foto;

                  return CachedNetworkImage(
                    imageUrl: imageUrl,
                    fit: BoxFit.cover,
                    placeholder: (context, url) => Container(
                      color: Colors.grey[300],
                      child: const Center(child: CircularProgressIndicator()),
                    ),
                    errorWidget: (context, url, error) => Container(
                      color: Colors.grey[300],
                      child: const Icon(Icons.image, size: 100),
                    ),
                  );
                },
              ),
            ),

            // Info Section
            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Title & Price
                  Text(
                    widget.kontrakan.nama,
                    style: const TextStyle(
                      fontSize: 22,
                      fontWeight: FontWeight.bold,
                      color: Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    '${widget.kontrakan.formattedHarga}/bulan',
                    style: const TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.w600,
                      color: Color(0xFF1565C0),
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Location
                  _buildInfoRow(Icons.location_on, widget.kontrakan.alamat),
                  _buildInfoRow(
                    Icons.directions_walk,
                    '${widget.kontrakan.jarakKampus} km dari kampus',
                  ),
                  _buildInfoRow(Icons.bed, '${widget.kontrakan.jumlahKamar} Kamar'),

                  const SizedBox(height: 20),

                  // Location Detection Card
                  if (widget.kontrakan.latitude != null && widget.kontrakan.longitude != null)
                    _buildLocationCard(),

                  const SizedBox(height: 20),

                  // Facilities
                  const Text(
                    'Fasilitas',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    children: widget.kontrakan.fasilitasList.map((f) {
                      return Chip(
                        label: Text(f),
                        backgroundColor: Colors.blue[50],
                      );
                    }).toList(),
                  ),

                  if (widget.kontrakan.deskripsi != null) ...[
                    const SizedBox(height: 20),
                    const Text(
                      'Deskripsi',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                        color: Colors.black87,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      widget.kontrakan.deskripsi!,
                      style: const TextStyle(fontSize: 14, height: 1.5),
                    ),
                  ],

                  const SizedBox(height: 80),
                ],
              ),
            ),
          ],
        ),
      ),
      bottomSheet: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.1),
              blurRadius: 8,
              offset: const Offset(0, -2),
            ),
          ],
        ),
        child: SafeArea(
          child: ElevatedButton(
            onPressed: widget.kontrakan.isAvailable
                ? () {
                    final authService = AuthService();
                    if (!authService.isAuthenticated) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(
                          content: Text('Silakan login terlebih dahulu untuk booking'),
                          backgroundColor: Colors.red,
                        ),
                      );
                      return;
                    }
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => BookingFormScreen(kontrakan: widget.kontrakan),
                      ),
                    );
                  }
                : null,
            style: ElevatedButton.styleFrom(
              backgroundColor: widget.kontrakan.isAvailable
                  ? const Color(0xFF667eea)
                  : Colors.grey,
              foregroundColor: Colors.white,
              disabledBackgroundColor: Colors.grey[400],
              disabledForegroundColor: Colors.white70,
              padding: const EdgeInsets.symmetric(vertical: 14),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8),
              ),
            ),
            child: Text(
              widget.kontrakan.isAvailable ? 'Booking Sekarang' : 'Kontrakan Tidak Tersedia',
              style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Row(
        children: [
          Icon(icon, size: 20, color: Colors.grey[600]),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              text,
              style: const TextStyle(fontSize: 14, color: Colors.black87),
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
                  Icon(
                    Icons.location_on,
                    color: Colors.purple,
                    size: 24,
                  ),
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
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.grey[600],
                        ),
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
                      style: const TextStyle(
                        fontSize: 13,
                        color: Colors.red,
                      ),
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
          widget.kontrakan.latitude!,
          widget.kontrakan.longitude!,
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
}
