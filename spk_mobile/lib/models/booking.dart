class Booking {
  final int id;
  final int userId;
  final int kontrakanId;
  final DateTime tanggalMulai;
  final DateTime tanggalSelesai;
  final int durasiBulan;
  final double totalBiaya;
  final String status;
  final String? catatan;
  final dynamic kontrakan; // Can be Map or Kontrakan object

  Booking({
    required this.id,
    required this.userId,
    required this.kontrakanId,
    required this.tanggalMulai,
    required this.tanggalSelesai,
    required this.durasiBulan,
    required this.totalBiaya,
    required this.status,
    this.catatan,
    this.kontrakan,
  });

  factory Booking.fromJson(Map<String, dynamic> json) {
    return Booking(
      id: json['id'] ?? 0,
      userId: json['user_id'] ?? 0,
      kontrakanId: json['kontrakan_id'] ?? 0,
      tanggalMulai: DateTime.parse(json['tanggal_mulai']),
      tanggalSelesai: DateTime.parse(json['tanggal_selesai']),
      durasiBulan: json['durasi_bulan'] ?? 0,
      totalBiaya: double.tryParse(json['total_biaya']?.toString() ?? '0') ?? 0,
      status: json['status'] ?? 'pending',
      catatan: json['catatan'],
      kontrakan: json['kontrakan'],
    );
  }

  // Format total biaya
  String get formattedTotalBiaya {
    return 'Rp ${totalBiaya.toStringAsFixed(0).replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (Match m) => '${m[1]}.')}';
  }

  // Status badge color
  String get statusColor {
    switch (status.toLowerCase()) {
      case 'confirmed':
        return 'green';
      case 'active':
        return 'blue';
      case 'completed':
        return 'gray';
      case 'cancelled':
        return 'red';
      default:
        return 'orange';
    }
  }

  // Status label Indonesia
  String get statusLabel {
    switch (status.toLowerCase()) {
      case 'pending':
        return 'Menunggu';
      case 'confirmed':
        return 'Dikonfirmasi';
      case 'active':
        return 'Aktif';
      case 'completed':
        return 'Selesai';
      case 'cancelled':
        return 'Dibatalkan';
      default:
        return status;
    }
  }
}
