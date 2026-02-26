class Kontrakan {
  final int id;
  final String nama;
  final String alamat;
  final double harga;
  final int jumlahKamar;
  final double jarakKampus;
  final String fasilitas;
  final String? deskripsi;
  final String status;
  final String? fotoUtama;
  final String? foto; // Field foto dari API
  final String? noWhatsapp;
  final List<Galeri> galeri;
  final double? avgRating;
  final int? totalReviews;
  final double? latitude;
  final double? longitude;

  Kontrakan({
    required this.id,
    required this.nama,
    required this.alamat,
    required this.harga,
    required this.jumlahKamar,
    required this.jarakKampus,
    required this.fasilitas,
    this.deskripsi,
    required this.status,
    this.fotoUtama,
    this.foto,
    this.noWhatsapp,
    this.galeri = const [],
    this.avgRating,
    this.totalReviews,
    this.latitude,
    this.longitude,
  });

  factory Kontrakan.fromJson(Map<String, dynamic> json) {
    // Try to get jarak from different possible field names
    double jarak = 0;
    if (json['jarak_kampus'] != null) {
      jarak = double.tryParse(json['jarak_kampus'].toString()) ?? 0;
    } else if (json['jarak'] != null) {
      // Backend uses 'jarak' column, convert meters to km
      jarak = (double.tryParse(json['jarak'].toString()) ?? 0) / 1000;
    }

    // Get foto from API and build gallery list
    final List<Galeri> galleryList = [];
    
    // Parse galeri array if exists
    if (json['galeri'] != null && (json['galeri'] as List).isNotEmpty) {
      galleryList.addAll(
        (json['galeri'] as List).map((g) => Galeri.fromJson(g)).toList()
      );
    }
    
    // If gallery is empty but foto field exists, create a galeri item from it
    if (galleryList.isEmpty && json['foto'] != null && json['foto'].toString().isNotEmpty) {
      galleryList.add(
        Galeri(
          id: json['id'] ?? 0,
          foto: json['foto'].toString(),
          isPrimary: true,
          urutan: 0,
        )
      );
    }

    return Kontrakan(
      id: json['id'] ?? 0,
      nama: json['nama'] ?? '',
      alamat: json['alamat'] ?? '',
      harga: double.tryParse(json['harga']?.toString() ?? '0') ?? 0,
      jumlahKamar: json['jumlah_kamar'] ?? 0,
      jarakKampus: jarak,
      fasilitas: json['fasilitas'] ?? '',
      deskripsi: json['deskripsi'],
      status: json['status'] ?? 'tersedia',
      fotoUtama: json['foto_utama'],
      foto: json['foto'], // Store raw foto field
      noWhatsapp: json['no_whatsapp'],
      galeri: galleryList,
      avgRating: json['avg_rating'] != null
          ? double.tryParse(json['avg_rating'].toString())
          : null,
      totalReviews: json['total_reviews'],
      latitude: json['latitude'] != null
          ? double.tryParse(json['latitude'].toString())
          : null,
      longitude: json['longitude'] != null
          ? double.tryParse(json['longitude'].toString())
          : null,
    );
  }

  // Check if kontrakan is available
  bool get isAvailable => status == 'available' || status == 'tersedia';

  // Get primary photo URL
  // Prioritize 'foto' field (Admin-set primary photo) over galeri entries,
  // because galeri items may reference files that have been deleted/replaced.
  String get primaryPhoto {
    // 1. Prefer the explicit 'foto' field (most up-to-date primary photo)
    if (foto != null && foto!.isNotEmpty) {
      if (foto!.startsWith('http')) {
        return foto!;
      }
      const String baseUrl = 'http://192.168.18.16:8000';
      return '$baseUrl/uploads/Kontrakan/$foto';
    }
    // 2. Fall back to galeri items if foto is not set
    if (galeri.isNotEmpty) {
      final primary = galeri.firstWhere(
        (g) => g.isPrimary,
        orElse: () => galeri.first,
      );
      if (primary.foto.isNotEmpty && primary.foto.startsWith('http')) {
        return primary.foto;
      }
      if (primary.foto.isNotEmpty) {
        const String baseUrl = 'http://192.168.18.16:8000';
        return '$baseUrl/uploads/Kontrakan/${primary.foto}';
      }
    }
    return 'https://via.placeholder.com/300';
  }

  // Format harga dengan Rupiah
  String get formattedHarga {
    return 'Rp ${harga.toStringAsFixed(0).replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (Match m) => '${m[1]}.')}';
  }

  // Get fasilitas as list
  List<String> get fasilitasList {
    return fasilitas.split(',').map((f) => f.trim()).toList();
  }
}

class Galeri {
  final int id;
  final String foto;
  final bool isPrimary;
  final int urutan;

  Galeri({
    required this.id,
    required this.foto,
    required this.isPrimary,
    required this.urutan,
  });

  factory Galeri.fromJson(Map<String, dynamic> json) {
    return Galeri(
      id: json['id'] ?? 0,
      foto: json['foto'] ?? '',
      isPrimary: json['is_primary'] == 1 || json['is_primary'] == true,
      urutan: json['urutan'] ?? 0,
    );
  }

  // Get full photo URL
  String get photoUrl {
    // Check if it's already a full URL
    if (foto.startsWith('http')) {
      return foto;
    }
    // Build full URL from uploads
    const String baseUrl = 'http://192.168.18.16:8000';
    return '$baseUrl/uploads/Kontrakan/$foto';
  }
}
