import '../config/app_config.dart';

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
  final String? foto;
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
    double jarak = 0;

    if (json['jarak_kampus'] != null) {
      jarak = double.tryParse(json['jarak_kampus'].toString()) ?? 0;
    } else if (json['jarak'] != null) {
      jarak = (double.tryParse(json['jarak'].toString()) ?? 0) / 1000;
    }

    final List<Galeri> galleryList = [];

    if (json['galeri'] != null && (json['galeri'] as List).isNotEmpty) {
      galleryList.addAll(
        (json['galeri'] as List).map((g) => Galeri.fromJson(g)).toList(),
      );
    }

    if (galleryList.isEmpty &&
        json['foto'] != null &&
        json['foto'].toString().isNotEmpty) {
      galleryList.add(
        Galeri(
          id: json['id'] ?? 0,
          foto: json['foto'].toString(),
          isPrimary: true,
          urutan: 0,
        ),
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
      foto: json['foto'],
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

  bool get isAvailable => status == 'available' || status == 'tersedia';

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

  String get primaryPhoto {
    if (foto != null && foto!.isNotEmpty) {
      if (foto!.startsWith('http')) {
        return foto!;
      }

      if (foto!.startsWith('uploads/')) {
        return '${AppConfig.serverUrl}/$foto';
      }

      return '${AppConfig.serverUrl}/uploads/kontrakan/$foto';
    }

    if (galeri.isNotEmpty) {
      final primary = galeri.firstWhere(
        (g) => g.isPrimary,
        orElse: () => galeri.first,
      );

      if (primary.foto.isNotEmpty && primary.foto.startsWith('http')) {
        return primary.foto;
      }

      if (primary.foto.isNotEmpty) {
        if (primary.foto.startsWith('uploads/')) {
          return '${AppConfig.serverUrl}/${primary.foto}';
        }

        return '${AppConfig.serverUrl}/uploads/kontrakan/${primary.foto}';
      }
    }

    return '';
  }

  String get formattedHarga {
    return 'Rp ${harga.toStringAsFixed(0).replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (Match m) => '${m[1]}.')}';
  }

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

  String get photoUrl {
    if (foto.startsWith('http')) {
      return foto;
    }

    if (foto.startsWith('uploads/')) {
      return '${AppConfig.serverUrl}/$foto';
    }

    return '${AppConfig.serverUrl}/uploads/kontrakan/$foto';
  }
}
