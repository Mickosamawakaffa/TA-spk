class Booking {
  final int id;
  final int userId;
  final int kontrakanId;
  final DateTime tanggalMulai;
  final DateTime tanggalSelesai;
  final double totalBiaya;
  final String status;
  final String? catatan;
  final dynamic kontrakan;

  // Pembeda pengajuan survei dan sewa.
  final String jenisPengajuan;
  final DateTime? tanggalSurvei;
  final String? jamSurvei;
  final DateTime? surveyFollowUpExpiresAt;

  // Pembayaran hanya digunakan pada pengajuan sewa.
  final String paymentStatus;
  final String? paymentProof;
  final String? paymentRejectionReason;

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
    this.jenisPengajuan = 'sewa',
    this.tanggalSurvei,
    this.jamSurvei,
    this.surveyFollowUpExpiresAt,
    this.paymentStatus = 'unpaid',
    this.paymentProof,
    this.paymentRejectionReason,
  });

  static DateTime? _parseDate(dynamic value) {
    if (value == null) return null;
    return DateTime.tryParse(value.toString());
  }

  factory Booking.fromJson(Map<String, dynamic> json) {
    final startDate =
        json['start_date'] ?? json['tanggal_mulai'] ?? json['tanggal_survei'];
    final endDate =
        json['end_date'] ?? json['tanggal_selesai'] ?? json['tanggal_survei'];
    final amount = json['amount'] ?? json['total_biaya'];
    final notes = json['notes'] ?? json['catatan'];

    final jenis = (json['jenis_pengajuan'] ?? 'sewa').toString().toLowerCase();

    final parsedSurveyDate = _parseDate(json['tanggal_survei']);
    final parsedStartDate =
        _parseDate(startDate) ?? parsedSurveyDate ?? DateTime.now();
    final parsedEndDate = _parseDate(endDate) ?? parsedStartDate;

    return Booking(
      id: int.tryParse(json['id']?.toString() ?? '0') ?? 0,
      userId: int.tryParse(json['user_id']?.toString() ?? '0') ?? 0,
      kontrakanId: int.tryParse(json['kontrakan_id']?.toString() ?? '0') ?? 0,
      tanggalMulai: parsedStartDate,
      tanggalSelesai: parsedEndDate,
      totalBiaya: double.tryParse(amount?.toString() ?? '0') ?? 0,
      status: json['status']?.toString() ?? 'pending',
      catatan: notes?.toString(),
      kontrakan: json['kontrakan'],
      jenisPengajuan: jenis,
      tanggalSurvei: parsedSurveyDate,
      jamSurvei: json['jam_survei']?.toString(),
      surveyFollowUpExpiresAt: _parseDate(
        json['survey_follow_up_expires_at'],
      ),
      paymentStatus: json['payment_status']?.toString() ?? 'unpaid',
      paymentProof: json['payment_proof']?.toString(),
      paymentRejectionReason: json['payment_rejection_reason']?.toString(),
    );
  }

  bool get isSurvei => jenisPengajuan == 'survei';

  bool get isSewa => !isSurvei;

  bool get isSurveyFollowUpActive =>
      isSurvei && status.toLowerCase() == 'confirmed';

  String get jenisLabel {
    return isSurvei ? 'Pengajuan Survei' : 'Pengajuan Sewa';
  }

  bool get canUploadPaymentProof {
    return isSewa &&
        status.toLowerCase() == 'confirmed' &&
        paymentStatus == 'unpaid';
  }

  bool get isWaitingPaymentVerification {
    return isSewa && paymentStatus == 'verification';
  }

  String get formattedTotalBiaya {
    if (isSurvei) return '-';

    final nominal = totalBiaya.toStringAsFixed(0).replaceAllMapped(
      RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
      (Match match) => '${match[1]}.',
    );

    return 'Rp $nominal';
  }

  int get durasiBulan {
    if (isSurvei) return 0;

    final totalBulan =
        (tanggalSelesai.year - tanggalMulai.year) * 12 +
        tanggalSelesai.month -
        tanggalMulai.month;

    return totalBulan.clamp(1, 99).toInt();
  }

  String get statusColor {
    switch (status.toLowerCase()) {
      case 'confirmed':
        return 'blue';
      case 'checked_in':
      case 'active':
        return 'green';
      case 'completed':
      case 'expired':
        return 'gray';
      case 'cancelled':
        return 'red';
      default:
        return 'orange';
    }
  }

  String get statusLabel {
    if (isSurvei) {
      switch (status.toLowerCase()) {
        case 'pending':
          return 'Menunggu Konfirmasi Survei';
        case 'confirmed':
          return 'Survei Disetujui';
        case 'completed':
          return 'Survei Selesai';
        case 'cancelled':
          return 'Tidak Jadi Sewa';
        case 'expired':
          return 'Masa Tindak Lanjut Berakhir';
        default:
          return status;
      }
    }

    switch (status.toLowerCase()) {
      case 'pending':
        return 'Menunggu Persetujuan Sewa';
      case 'confirmed':
        switch (paymentStatus.toLowerCase()) {
          case 'paid':
            return 'Sewa Disetujui';
          case 'verification':
            return 'Menunggu Verifikasi Pembayaran';
          default:
            return 'Menunggu Pembayaran';
        }
      case 'checked_in':
      case 'active':
        return 'Sedang Ditempati';
      case 'completed':
        return 'Sewa Selesai';
      case 'cancelled':
        return 'Pengajuan Sewa Dibatalkan';
      default:
        return status;
    }
  }

  String get paymentStatusLabel {
    if (isSurvei) return '-';

    switch (paymentStatus.toLowerCase()) {
      case 'paid':
        return 'Lunas';
      case 'verification':
        return 'Menunggu Verifikasi';
      case 'refunded':
        return 'Dikembalikan';
      default:
        return 'Belum Bayar';
    }
  }
}
