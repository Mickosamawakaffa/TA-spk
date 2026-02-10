class Laundry {
  final int id;
  final String nama;
  final String alamat;
  final String? noWhatsapp;
  final double? latitude;
  final double? longitude;
  final double jarakKampus; // jarak ke kampus dalam km
  final String jamBuka;
  final String jamTutup;
  final double hargaPerKg;
  final int waktuProses; // dalam jam
  final String? deskripsi;
  final String? fotoUtama;
  final List<GaleriLaundry> galeri;
  final double? avgRating;
  final int? totalReviews;
  final String status;

  Laundry({
    required this.id,
    required this.nama,
    required this.alamat,
    this.noWhatsapp,
    this.latitude,
    this.longitude,
    required this.jarakKampus,
    required this.jamBuka,
    required this.jamTutup,
    required this.hargaPerKg,
    required this.waktuProses,
    this.deskripsi,
    this.fotoUtama,
    this.galeri = const [],
    this.avgRating,
    this.totalReviews,
    required this.status,
  });

  factory Laundry.fromJson(Map<String, dynamic> json) {
    // Try to get jarak from different possible field names
    double jarak = 0;
    if (json['jarak_kampus'] != null) {
      jarak = double.tryParse(json['jarak_kampus'].toString()) ?? 0;
    } else if (json['jarak'] != null) {
      // Backend uses 'jarak' column, convert meters to km
      jarak = (double.tryParse(json['jarak'].toString()) ?? 0) / 1000;
    }

    // Get harga from layanan if harga_per_kg is not set directly
    double harga = 0;
    if (json['harga_per_kg'] != null) {
      harga = double.tryParse(json['harga_per_kg'].toString()) ?? 0;
    } else if (json['layanan'] != null &&
        (json['layanan'] as List).isNotEmpty) {
      final layananList = json['layanan'] as List;
      harga =
          double.tryParse(layananList.first['harga']?.toString() ?? '0') ?? 0;
    }

    return Laundry(
      id: json['id'] ?? 0,
      nama: json['nama'] ?? '',
      alamat: json['alamat'] ?? '',
      noWhatsapp: json['no_whatsapp'],
      latitude: json['latitude'] != null
          ? double.tryParse(json['latitude'].toString())
          : null,
      longitude: json['longitude'] != null
          ? double.tryParse(json['longitude'].toString())
          : null,
      jarakKampus: jarak,
      jamBuka: json['jam_buka'] ?? '08:00',
      jamTutup: json['jam_tutup'] ?? '17:00',
      hargaPerKg: harga,
      waktuProses: json['waktu_proses'] ?? 24,
      deskripsi: json['deskripsi'],
      fotoUtama: json['foto_utama'],
      galeri: json['galeri'] != null
          ? (json['galeri'] as List)
                .map((g) => GaleriLaundry.fromJson(g))
                .toList()
          : [],
      avgRating: json['avg_rating'] != null
          ? double.tryParse(json['avg_rating'].toString())
          : null,
      totalReviews: json['total_reviews'],
      status: json['status'] ?? 'buka',
    );
  }

  // Get primary photo URL
  String get primaryPhoto {
    if (galeri.isNotEmpty) {
      final primary = galeri.firstWhere(
        (g) => g.isPrimary,
        orElse: () => galeri.first,
      );
      return primary.foto;
    }
    return fotoUtama ?? 'https://via.placeholder.com/300';
  }

  // Format harga dengan Rupiah
  String get formattedHarga {
    return 'Rp ${hargaPerKg.toStringAsFixed(0).replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (Match m) => '${m[1]}.')}';
  }

  // Legacy getters for backward compatibility
  String get formattedHargaKiloan => formattedHarga;
  String get formattedHargaSatuan => formattedHarga;
  double get hargaKiloan => hargaPerKg;
  double get jarak => jarakKampus; // alias for jarak field
  double get rating => avgRating ?? 0.0;
  String get estimasiSelesai => '$waktuProses jam';

  // Check if currently open
  bool get isOpen {
    if (status != 'buka') return false;

    final now = DateTime.now();
    final currentTime =
        '${now.hour.toString().padLeft(2, '0')}:${now.minute.toString().padLeft(2, '0')}';

    return currentTime.compareTo(jamBuka) >= 0 &&
        currentTime.compareTo(jamTutup) <= 0;
  }

  // Get status text
  String get statusText {
    if (status == 'tutup') return 'Tutup';
    if (isOpen) return 'Buka';
    return 'Tutup Sementara';
  }
}

class GaleriLaundry {
  final int id;
  final String foto;
  final bool isPrimary;
  final int urutan;

  GaleriLaundry({
    required this.id,
    required this.foto,
    required this.isPrimary,
    required this.urutan,
  });

  factory GaleriLaundry.fromJson(Map<String, dynamic> json) {
    return GaleriLaundry(
      id: json['id'] ?? 0,
      foto: json['foto'] ?? '',
      isPrimary: json['is_primary'] == 1 || json['is_primary'] == true,
      urutan: json['urutan'] ?? 0,
    );
  }
}
