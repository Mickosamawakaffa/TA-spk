import '../config/app_config.dart';

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
  final double hargaKiloan;
  final double hargaHarian;
  final double hargaJam;
  final int waktuProses; // dalam jam
  final int waktuProsesHarian;
  final int waktuProsesJam;
  final String? deskripsi;
  final String? fotoUtama;
  final String? foto; // Field foto dari API
  final String? fotoUrl; // Full absolute URL dari API (foto_url)
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
    required this.hargaKiloan,
    required this.hargaHarian,
    required this.hargaJam,
    required this.waktuProses,
    required this.waktuProsesHarian,
    required this.waktuProsesJam,
    this.deskripsi,
    this.fotoUtama,
    this.foto,
    this.fotoUrl,
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
    double hargaKiloan = 0;

    if (json['harga_per_kg'] != null) {
      harga = double.tryParse(json['harga_per_kg'].toString()) ?? 0;
    }

    if (json['harga_kiloan'] != null) {
      hargaKiloan = double.tryParse(json['harga_kiloan'].toString()) ?? 0;
    }

    double hargaHarian = 0;
    double hargaJam = 0;

    if (json['layanan'] != null &&
        (json['layanan'] as List).isNotEmpty) {
      final layananList = json['layanan'] as List;
      harga = harga > 0
          ? harga
          : double.tryParse(layananList.first['harga']?.toString() ?? '0') ?? 0;
      for (final item in layananList) {
        final jenis = item['jenis_layanan']?.toString().toLowerCase() ?? '';
        final itemHarga =
            double.tryParse(item['harga']?.toString() ?? '0') ?? 0;
        
        if (jenis == 'harian' || jenis == 'kiloan' || jenis == 'reguler') {
          hargaHarian = itemHarga;
          hargaKiloan = itemHarga;
        } else if (jenis == 'jam' || jenis == 'express' || jenis == 'kilat' || jenis == 'satuan') {
          hargaJam = itemHarga;
        }
      }
    }

    // Fallback if not populated
    if (hargaHarian == 0) hargaHarian = hargaKiloan > 0 ? hargaKiloan : harga;
    if (hargaJam == 0) hargaJam = harga;

    int waktuProsesHarian = 0;
    int waktuProsesJam = 0;

    if (json['layanan'] != null &&
        (json['layanan'] as List).isNotEmpty) {
      for (final item in json['layanan'] as List) {
        final jenis = item['jenis_layanan']?.toString().toLowerCase() ?? '';
        final estimasi = int.tryParse(item['estimasi_selesai']?.toString() ?? '0') ?? 0;
        final waktu = int.tryParse(item['waktu_proses']?.toString() ?? '0') ?? 0;
        final actualWaktu = waktu > 0 ? waktu : (estimasi > 0 ? estimasi : 24);
        
        if (jenis == 'harian' || jenis == 'kiloan' || jenis == 'reguler') {
          waktuProsesHarian = actualWaktu;
        } else if (jenis == 'jam' || jenis == 'express' || jenis == 'kilat' || jenis == 'satuan') {
          waktuProsesJam = actualWaktu;
        }
      }
    }

    if (waktuProsesHarian == 0) waktuProsesHarian = json['waktu_proses'] ?? 24;
    if (waktuProsesJam == 0) waktuProsesJam = json['waktu_proses'] ?? 24;



    // Get foto from API and build gallery list
    final List<GaleriLaundry> galleryList = [];

    // Parse galeri array if exists
    if (json['galeri'] != null && (json['galeri'] as List).isNotEmpty) {
      galleryList.addAll(
        (json['galeri'] as List).map((g) => GaleriLaundry.fromJson(g)).toList(),
      );
    }

    // If gallery is empty but foto field exists, create a galeri item from it
    if (galleryList.isEmpty &&
        json['foto'] != null &&
        json['foto'].toString().isNotEmpty) {
      final resolved = json['foto_url'] != null && json['foto_url'].toString().isNotEmpty
          ? json['foto_url'].toString()
          : (json['foto'].toString().startsWith('http')
              ? json['foto'].toString()
              : '${AppConfig.serverUrl}/uploads/Laundry/${json['foto']}');
      galleryList.add(
        GaleriLaundry(
          id: json['id'] ?? 0,
          foto: resolved,
          isPrimary: true,
          urutan: 0,
        ),
      );
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
      jamBuka: _parseTime(json['jam_buka'], '08:00'),
      jamTutup: _parseTime(json['jam_tutup'], '20:00'),
      hargaPerKg: harga,
      hargaKiloan: hargaKiloan,
      hargaHarian: hargaHarian,
      hargaJam: hargaJam,
      waktuProses: json['waktu_proses'] ?? 24,
      waktuProsesHarian: waktuProsesHarian,
      waktuProsesJam: waktuProsesJam,
      deskripsi: json['deskripsi'],
      fotoUtama: json['foto_utama'],
      foto: json['foto'], // Store raw foto field
      fotoUrl: json['foto_url'], // Full URL dari API
      galeri: galleryList,
      avgRating: json['avg_rating'] != null
          ? double.tryParse(json['avg_rating'].toString())
          : null,
      totalReviews: json['total_reviews'],
      status: json['status'] ?? 'buka',
    );
  }

  // Parse time string, stripping seconds if present (e.g. "08:00:00" -> "08:00")
  static String _parseTime(dynamic value, String fallback) {
    if (value == null) return fallback;
    final str = value.toString();
    if (str.length >= 5) return str.substring(0, 5);
    return fallback;
  }

  bool get hasPhoto {
    bool isValidUrl(String? url) {
      if (url == null || url.isEmpty) return false;
      final lower = url.toLowerCase();
      if (lower.contains('via.placeholder.com')) return false;
      return true;
    }

    if (isValidUrl(foto)) return true;
    if (galeri.any((g) => isValidUrl(g.foto))) return true;
    return false;
  }

  // Get primary photo URL
  // Priority: foto_url (full URL from API) > foto field > galeri
  String get primaryPhoto {
    // 1. Gunakan foto_url dari API jika sudah full URL (paling akurat)
    if (fotoUrl != null && fotoUrl!.isNotEmpty) {
      return fotoUrl!;
    }
    // 2. Construct URL dari nama file foto
    if (foto != null && foto!.isNotEmpty) {
      if (foto!.startsWith('http')) {
        return foto!;
      }
      return '${AppConfig.serverUrl}/uploads/Laundry/$foto';
    }
    // 3. Fall back to galeri items if foto is not set
    if (galeri.isNotEmpty) {
      final primary = galeri.firstWhere(
        (g) => g.isPrimary,
        orElse: () => galeri.first,
      );
      if (primary.foto.isNotEmpty && primary.foto.startsWith('http')) {
        return primary.foto;
      }
      if (primary.foto.isNotEmpty) {
        return primary.photoUrl;
      }
    }
    return '';
  }

  // Format harga dengan Rupiah
  String get formattedHarga {
    return 'Rp ${_formatRupiah(hargaHarian > 0 ? hargaHarian : hargaPerKg)}';
  }

  // Format harga sesuai jenis layanan (harian / jam)
  String formattedHargaFor(String? jenisLayanan) {
    if (jenisLayanan == null || jenisLayanan.isEmpty) {
      return formattedHarga;
    }
    
    final target = jenisLayanan.toLowerCase();
    if (target == 'jam' || target == 'express' || target == 'kilat' || target == 'satuan') {
      final val = hargaJam > 0 ? hargaJam : hargaPerKg;
      return 'Rp ${_formatRupiah(val)}';
    } else {
      final val = hargaHarian > 0 ? hargaHarian : hargaPerKg;
      return 'Rp ${_formatRupiah(val)}';
    }
  }

  // Format waktu proses sesuai jenis layanan (harian / jam)
  int waktuProsesFor(String? jenisLayanan) {
    if (jenisLayanan == null || jenisLayanan.isEmpty) {
      return waktuProsesHarian > 0 ? waktuProsesHarian : waktuProses;
    }
    
    final target = jenisLayanan.toLowerCase();
    if (target == 'jam' || target == 'express' || target == 'kilat' || target == 'satuan') {
      return waktuProsesJam > 0 ? waktuProsesJam : waktuProses;
    } else {
      return waktuProsesHarian > 0 ? waktuProsesHarian : waktuProses;
    }
  }

  String _formatRupiah(double value) {
    return value
        .toStringAsFixed(0)
        .replaceAllMapped(
          RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
          (Match m) => '${m[1]}.',
        );
  }

  String get formattedHargaKiloan {
    final value = hargaKiloan > 0 ? hargaKiloan : hargaPerKg;
    return 'Rp ${_formatRupiah(value)}';
  }

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

  // Get full photo URL
  String get photoUrl {
    if (foto.isEmpty) return '';

    if (foto.startsWith('http')) {
      return foto;
    }

    if (foto.startsWith('uploads/')) {
      return '${AppConfig.serverUrl}/$foto';
    }

    return '${AppConfig.serverUrl}/uploads/galeri/laundry/$foto';
  }
}
