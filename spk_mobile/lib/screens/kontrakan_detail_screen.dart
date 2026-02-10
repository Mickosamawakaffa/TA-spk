import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../models/kontrakan.dart';

class KontrakanDetailScreen extends StatelessWidget {
  final Kontrakan kontrakan;

  const KontrakanDetailScreen({super.key, required this.kontrakan});

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
                itemCount: kontrakan.galeri.isEmpty
                    ? 1
                    : kontrakan.galeri.length,
                itemBuilder: (context, index) {
                  final imageUrl = kontrakan.galeri.isEmpty
                      ? kontrakan.primaryPhoto
                      : kontrakan.galeri[index].foto;

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
                    kontrakan.nama,
                    style: const TextStyle(
                      fontSize: 22,
                      fontWeight: FontWeight.bold,
                      color: Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    '${kontrakan.formattedHarga}/bulan',
                    style: const TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.w600,
                      color: Color(0xFF1565C0),
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Location
                  _buildInfoRow(Icons.location_on, kontrakan.alamat),
                  _buildInfoRow(
                    Icons.directions_walk,
                    '${kontrakan.jarakKampus} km dari kampus',
                  ),
                  _buildInfoRow(Icons.bed, '${kontrakan.jumlahKamar} Kamar'),

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
                    children: kontrakan.fasilitasList.map((f) {
                      return Chip(
                        label: Text(f),
                        backgroundColor: Colors.blue[50],
                      );
                    }).toList(),
                  ),

                  if (kontrakan.deskripsi != null) ...[
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
                      kontrakan.deskripsi!,
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
              color: Colors.black.withOpacity(0.1),
              blurRadius: 8,
              offset: const Offset(0, -2),
            ),
          ],
        ),
        child: SafeArea(
          child: ElevatedButton(
            onPressed: () {
              // TODO: Navigate to booking screen
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('Fitur booking segera hadir')),
              );
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF1565C0),
              foregroundColor: Colors.white,
              padding: const EdgeInsets.symmetric(vertical: 14),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8),
              ),
            ),
            child: const Text(
              'Booking Sekarang',
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
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
}
