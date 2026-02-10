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
  final List<Galeri> galeri;
  final double? avgRating;
  final int? totalReviews;

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
    this.galeri = const [],
    this.avgRating,
    this.totalReviews,
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
      galeri: json['galeri'] != null
          ? (json['galeri'] as List).map((g) => Galeri.fromJson(g)).toList()
          : [],
      avgRating: json['avg_rating'] != null
          ? double.tryParse(json['avg_rating'].toString())
          : null,
      totalReviews: json['total_reviews'],
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
}
