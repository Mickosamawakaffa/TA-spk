class Booking {
  final int id;
  final int userId;
  final int kontrakanId;
  final DateTime tanggalMulai;
  final DateTime tanggalSelesai;
  final double totalBiaya;
  final String status;
  final String? catatan;
  final dynamic kontrakan; // Can be Map or Kontrakan object
  final String paymentStatus;
  final String? paymentProof;

  Booking({
    required this.id,
    required this.userId,
    required this.kontrakanId,
    required this.tanggalMulai,
    required this.tanggalSelesai,
    required this.totalBiaya,
    required this.status,
    this.catatan,
    this.kontrakan,
    this.paymentStatus = 'unpaid',
    this.paymentProof,
  });

  factory Booking.fromJson(Map<String, dynamic> json) {
    // Support both old field names and actual DB column names
    final startDate = json['start_date'] ?? json['tanggal_mulai'];
    final endDate = json['end_date'] ?? json['tanggal_selesai'];
    final amount = json['amount'] ?? json['total_biaya'];
    final notes = json['notes'] ?? json['catatan'];

    return Booking(
      id: int.tryParse(json['id']?.toString() ?? '0') ?? 0,
      userId: int.tryParse(json['user_id']?.toString() ?? '0') ?? 0,
      kontrakanId: int.tryParse(json['kontrakan_id']?.toString() ?? '0') ?? 0,
      tanggalMulai: DateTime.parse(startDate),
      tanggalSelesai: DateTime.parse(endDate),
      totalBiaya: double.tryParse(amount?.toString() ?? '0') ?? 0,
      status: json['status'] ?? 'pending',
      catatan: notes,
      kontrakan: json['kontrakan'],
      paymentStatus: json['payment_status'] ?? 'unpaid',
      paymentProof: json['payment_proof'],
    );
  }

  // Format total biaya
  String get formattedTotalBiaya {
    return 'Rp ${totalBiaya.toStringAsFixed(0).replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (Match m) => '${m[1]}.')}';
  }

  // Computed duration in months from start and end date
  int get durasiBulan {
    return ((tanggalSelesai.year - tanggalMulai.year) * 12 +
            tanggalSelesai.month -
            tanggalMulai.month)
        .clamp(1, 99);
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
