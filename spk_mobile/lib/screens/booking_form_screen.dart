import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:intl/intl.dart' as intl;

import '../models/kontrakan.dart';
import '../services/auth_service.dart';
import '../services/booking_service.dart';
import 'improved_home_screen.dart';

class BookingFormScreen extends StatefulWidget {
  final Kontrakan kontrakan;

  // survei = hanya meminta jadwal kunjungan.
  // sewa = meminta tanggal mulai tinggal dan durasi sewa.
  final String jenisPengajuan;

  const BookingFormScreen({
    super.key,
    required this.kontrakan,
    this.jenisPengajuan = 'sewa',
  });

  @override
  State<BookingFormScreen> createState() => _BookingFormScreenState();
}

class _BookingFormScreenState extends State<BookingFormScreen> {
  final _bookingService = BookingService();
  final _authService = AuthService();
  final _catatanController = TextEditingController();
  final _imagePicker = ImagePicker();

  DateTime? _tanggal;
  TimeOfDay? _jamSurvei;
  int _durasiBulan = 6;
  bool _isSubmitting = false;
  File? _ktpPhoto;

  // Rate limiting: cooldown 10 detik antar submit
  DateTime? _lastSubmitTime;
  static const _cooldownDuration = Duration(seconds: 10);

  final _currencyFormat = intl.NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 0,
  );

  bool get _isSurvei => widget.jenisPengajuan.toLowerCase() == 'survei';

  double get _totalBiaya => widget.kontrakan.harga * (_durasiBulan / 12);

  String get _durasiLabel => _durasiBulan == 12 ? '1 tahun' : '6 bulan';

  String get _judulForm => _isSurvei ? 'Ajukan Survei' : 'Ajukan Sewa';

  // Sanitasi input untuk mencegah XSS
  String _sanitizeInput(String input) {
    // Hapus karakter HTML berbahaya
    return input
        .replaceAll(RegExp(r'<[^>]*>'), '') // Hapus HTML tags
        .replaceAll(RegExp(r'&[^;]+;'), '') // Hapus HTML entities
        .replaceAll(RegExp(r'javascript:'), '') // Hapus javascript protocol
        .replaceAll(RegExp(r'on\w+\s*='), '') // Hapus event handlers
        .trim();
  }

  @override
  void initState() {
    super.initState();
    initializeDateFormatting('id_ID', null);
  }

  @override
  void dispose() {
    _catatanController.dispose();
    super.dispose();
  }

  Future<void> _selectDate() async {
    final now = DateTime.now();
    final maxDate = now.add(const Duration(days: 180)); // 6 bulan ke depan

    final picked = await showDatePicker(
      context: context,
      initialDate: _tanggal ?? now.add(const Duration(days: 1)),
      firstDate: now.add(const Duration(days: 1)),
      lastDate: maxDate,
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(
              primary: Color(0xFF667EEA),
              onPrimary: Colors.white,
              surface: Colors.white,
              onSurface: Colors.black87,
            ),
          ),
          child: child!,
        );
      },
    );

    if (picked != null && mounted) {
      setState(() => _tanggal = picked);
    }
  }

  Future<void> _selectSurveyTime() async {
    final picked = await showTimePicker(
      context: context,
      initialTime: _jamSurvei ?? const TimeOfDay(hour: 10, minute: 0),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(
              primary: Color(0xFF667EEA),
              onPrimary: Colors.white,
              surface: Colors.white,
              onSurface: Colors.black87,
            ),
          ),
          child: child!,
        );
      },
    );

    if (picked != null && mounted) {
      setState(() => _jamSurvei = picked);
    }
  }

  String _formatJam(TimeOfDay value) {
    final hour = value.hour.toString().padLeft(2, '0');
    final minute = value.minute.toString().padLeft(2, '0');
    return '$hour:$minute';
  }

  Future<void> _pickKtpPhoto() async {
    final source = await showModalBottomSheet<ImageSource>(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
      ),
      builder: (sheetContext) => SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Text(
                'Upload Foto KTP',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 8),
              const Text(
                'Pilih foto KTP untuk melengkapi pengajuan sewa',
                textAlign: TextAlign.center,
                style: TextStyle(color: Colors.grey),
              ),
              const SizedBox(height: 14),
              ListTile(
                leading: const Icon(
                  Icons.photo_library_rounded,
                  color: Color(0xFF667EEA),
                ),
                title: const Text('Pilih dari Galeri'),
                onTap: () => Navigator.pop(sheetContext, ImageSource.gallery),
              ),
              ListTile(
                leading: const Icon(
                  Icons.camera_alt_rounded,
                  color: Color(0xFF667EEA),
                ),
                title: const Text('Ambil Foto'),
                onTap: () => Navigator.pop(sheetContext, ImageSource.camera),
              ),
            ],
          ),
        ),
      ),
    );

    if (source == null) return;

    try {
      final picked = await _imagePicker.pickImage(
        source: source,
        maxWidth: 1920,
        maxHeight: 1920,
        imageQuality: 85,
      );

      if (picked != null && mounted) {
        final file = File(picked.path);

        // Validasi ukuran file (max 5MB)
        final fileSize = await file.length();
        const maxSize = 5 * 1024 * 1024; // 5MB in bytes

        if (fileSize > maxSize) {
          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Ukuran foto KTP terlalu besar. Maksimal 5MB.'),
              backgroundColor: Colors.red,
            ),
          );
          return;
        }

        // Validasi tipe file (hanya jpeg/png)
        final extension = picked.path.toLowerCase();
        if (!extension.endsWith('.jpg') &&
            !extension.endsWith('.jpeg') &&
            !extension.endsWith('.png')) {
          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text(
                'Format file tidak didukung. Gunakan JPG atau PNG.',
              ),
              backgroundColor: Colors.red,
            ),
          );
          return;
        }

        setState(() => _ktpPhoto = file);
      }
    } catch (e) {
      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Gagal memilih foto KTP: $e'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<void> _submitPengajuan() async {
    // Rate limiting check
    if (_lastSubmitTime != null) {
      final timeSinceLastSubmit = DateTime.now().difference(_lastSubmitTime!);
      if (timeSinceLastSubmit < _cooldownDuration) {
        final remainingSeconds =
            _cooldownDuration.inSeconds - timeSinceLastSubmit.inSeconds;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              'Mohon tunggu $remainingSeconds detik sebelum mengirim lagi.',
            ),
            backgroundColor: Colors.orange,
          ),
        );
        return;
      }
    }

    if (_tanggal == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            _isSurvei
                ? 'Pilih tanggal survei terlebih dahulu.'
                : 'Pilih tanggal mulai sewa terlebih dahulu.',
          ),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    if (_isSurvei && _jamSurvei == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Pilih jam survei terlebih dahulu.'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    if (!_authService.isAuthenticated) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Anda harus login terlebih dahulu.'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    // Set timestamp submit
    _lastSubmitTime = DateTime.now();

    final confirmed = await showDialog<bool>(
      context: context,
      builder: (dialogContext) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Row(
          children: [
            Icon(
              _isSurvei ? Icons.event_available : Icons.home_work_outlined,
              color: const Color(0xFF667EEA),
            ),
            const SizedBox(width: 8),
            Expanded(
              child: Text(
                _isSurvei ? 'Konfirmasi Survei' : 'Konfirmasi Pengajuan Sewa',
              ),
            ),
          ],
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildConfirmRow('Kontrakan', widget.kontrakan.nama),
            const SizedBox(height: 8),
            _buildConfirmRow(
              _isSurvei ? 'Tanggal Survei' : 'Mulai Menempati',
              intl.DateFormat('dd MMMM yyyy', 'id_ID').format(_tanggal!),
            ),
            if (_isSurvei) ...[
              const SizedBox(height: 8),
              _buildConfirmRow('Jam Survei', _formatJam(_jamSurvei!)),
            ] else ...[
              const SizedBox(height: 8),
              _buildConfirmRow('Durasi Sewa', _durasiLabel),
              const SizedBox(height: 8),
              _buildConfirmRow(
                'Estimasi Biaya',
                _currencyFormat.format(_totalBiaya),
              ),
            ],
            if (_catatanController.text.trim().isNotEmpty) ...[
              const SizedBox(height: 8),
              _buildConfirmRow('Catatan', _catatanController.text.trim()),
            ],
            if (!_isSurvei && _ktpPhoto != null) ...[
              const SizedBox(height: 8),
              _buildConfirmRow('Foto KTP', 'Foto terlampir'),
            ],
            if (!_isSurvei) ...[
              const SizedBox(height: 12),
              const Text(
                'Pembayaran dilakukan setelah pengajuan sewa disetujui pemilik kontrakan.',
                style: TextStyle(
                  fontSize: 12,
                  color: Color(0xFF5A6B85),
                  height: 1.4,
                ),
              ),
            ],
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(dialogContext, false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(dialogContext, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF667EEA),
              foregroundColor: Colors.white,
            ),
            child: const Text('Kirim'),
          ),
        ],
      ),
    );

    if (confirmed != true || !mounted) return;

    setState(() => _isSubmitting = true);

    try {
      final result = _isSurvei
          ? await _bookingService.createSurvey(
              kontrakanId: widget.kontrakan.id,
              tanggalSurvei: _tanggal!,
              jamSurvei: _formatJam(_jamSurvei!),
              catatan: _catatanController.text.trim().isEmpty
                  ? null
                  : _sanitizeInput(_catatanController.text.trim()),
            )
          : await _bookingService.createSewa(
              kontrakanId: widget.kontrakan.id,
              tanggalMulai: _tanggal!,
              durasiBulan: _durasiBulan,
              catatan: _catatanController.text.trim().isEmpty
                  ? null
                  : _sanitizeInput(_catatanController.text.trim()),
              ktpPhoto: _ktpPhoto,
            );

      if (!mounted) return;
      setState(() => _isSubmitting = false);

      if (result['success'] == true) {
        await showDialog<void>(
          context: context,
          barrierDismissible: false,
          builder: (dialogContext) => AlertDialog(
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(16),
            ),
            content: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Colors.green.withOpacity(0.10),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.check_circle,
                    color: Colors.green,
                    size: 64,
                  ),
                ),
                const SizedBox(height: 16),
                Text(
                  _isSurvei
                      ? 'Pengajuan Survei Terkirim'
                      : 'Pengajuan Sewa Terkirim',
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  _isSurvei
                      ? 'Silakan tunggu konfirmasi jadwal dari pemilik kontrakan.'
                      : 'Silakan tunggu persetujuan pemilik. Pembayaran dilakukan setelah pengajuan disetujui.',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 14,
                    color: Colors.grey[600],
                    height: 1.4,
                  ),
                ),
              ],
            ),
            actions: [
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () => Navigator.pop(dialogContext),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF667EEA),
                    foregroundColor: Colors.white,
                  ),
                  child: const Text('OK'),
                ),
              ),
            ],
          ),
        );

        if (!mounted) return;
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(builder: (_) => const ImprovedHomeScreen()),
          (route) => false,
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(result['message'] ?? 'Gagal mengirim pengajuan.'),
            backgroundColor: Colors.red,
          ),
        );
      }
    } catch (e) {
      if (!mounted) return;
      setState(() => _isSubmitting = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Terjadi kesalahan: $e'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Widget _buildConfirmRow(String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          width: 108,
          child: Text(
            label,
            style: TextStyle(fontSize: 13, color: Colors.grey[600]),
          ),
        ),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
          ),
        ),
      ],
    );
  }

  Widget _buildChoiceField({
    required IconData icon,
    required String value,
    required VoidCallback onTap,
    String? hint,
  }) {
    final isSelected = hint == null;

    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        decoration: BoxDecoration(
          color: Colors.grey[100],
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: isSelected ? const Color(0xFF667EEA) : Colors.grey[300]!,
          ),
        ),
        child: Row(
          children: [
            Icon(
              icon,
              color: isSelected ? const Color(0xFF667EEA) : Colors.grey[400],
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                hint ?? value,
                style: TextStyle(
                  fontSize: 15,
                  color: isSelected ? Colors.black87 : Colors.grey[500],
                  fontWeight: isSelected ? FontWeight.w600 : FontWeight.normal,
                ),
              ),
            ),
            const Icon(Icons.arrow_drop_down, color: Color(0xFF667EEA)),
          ],
        ),
      ),
    );
  }

  Widget _buildFormCard({
    required IconData icon,
    required String title,
    required String subtitle,
    required Widget child,
  }) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.08),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(icon, size: 20, color: const Color(0xFF667EEA)),
              const SizedBox(width: 8),
              Text(
                title,
                style: TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.bold,
                  color: Colors.grey[800],
                ),
              ),
            ],
          ),
          const SizedBox(height: 4),
          Text(
            subtitle,
            style: TextStyle(fontSize: 12, color: Colors.grey[500]),
          ),
          const SizedBox(height: 12),
          child,
        ],
      ),
    );
  }

  Widget _buildBiayaRow(String label, String value) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: TextStyle(fontSize: 14, color: Colors.grey[600])),
        Text(
          value,
          style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500),
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    final dateText = _tanggal == null
        ? 'Pilih tanggal...'
        : intl.DateFormat('dd MMMM yyyy', 'id_ID').format(_tanggal!);

    final timeText = _jamSurvei == null
        ? 'Pilih jam...'
        : _formatJam(_jamSurvei!);

    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      appBar: AppBar(
        title: Text(_judulForm),
        backgroundColor: const Color(0xFF667EEA),
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  colors: [Color(0xFF667EEA), Color(0xFF764BA2)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    widget.kontrakan.nama,
                    style: const TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    widget.kontrakan.alamat,
                    style: const TextStyle(fontSize: 13, color: Colors.white70),
                  ),
                  const SizedBox(height: 10),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 6,
                    ),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.20),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      '${widget.kontrakan.formattedHarga}/tahun',
                      style: const TextStyle(
                        fontWeight: FontWeight.bold,
                        color: Colors.white,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                children: [
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: _isSurvei
                          ? const Color(0xFFE8F5E9)
                          : const Color(0xFFE3F2FD),
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(
                        color: _isSurvei
                            ? const Color(0xFF43A047)
                            : const Color(0xFF1565C0),
                      ),
                    ),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Icon(
                          _isSurvei
                              ? Icons.event_available
                              : Icons.home_work_outlined,
                          color: _isSurvei
                              ? const Color(0xFF2E7D32)
                              : const Color(0xFF1565C0),
                        ),
                        const SizedBox(width: 10),
                        Expanded(
                          child: Text(
                            _isSurvei
                                ? 'Survei digunakan untuk melihat kontrakan terlebih dahulu. Tidak ada pembayaran pada tahap ini.'
                                : 'Pengajuan sewa digunakan saat Anda sudah ingin menempati kontrakan. Pembayaran dilakukan setelah disetujui pemilik.',
                            style: const TextStyle(fontSize: 13, height: 1.4),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 16),
                  _buildFormCard(
                    icon: Icons.calendar_today,
                    title: _isSurvei ? 'Tanggal Survei' : 'Tanggal Mulai Sewa',
                    subtitle: _isSurvei
                        ? 'Pilih tanggal untuk melihat kontrakan'
                        : 'Pilih tanggal mulai menempati kontrakan',
                    child: _buildChoiceField(
                      icon: Icons.date_range,
                      value: dateText,
                      hint: _tanggal == null ? dateText : null,
                      onTap: _selectDate,
                    ),
                  ),
                  if (_isSurvei) ...[
                    const SizedBox(height: 16),
                    _buildFormCard(
                      icon: Icons.access_time,
                      title: 'Jam Survei',
                      subtitle: 'Pilih perkiraan waktu kunjungan',
                      child: _buildChoiceField(
                        icon: Icons.schedule,
                        value: timeText,
                        hint: _jamSurvei == null ? timeText : null,
                        onTap: _selectSurveyTime,
                      ),
                    ),
                  ] else ...[
                    const SizedBox(height: 16),
                    _buildFormCard(
                      icon: Icons.timelapse,
                      title: 'Durasi Sewa',
                      subtitle: 'Pilih durasi sewa yang diinginkan',
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        decoration: BoxDecoration(
                          color: Colors.grey[100],
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: const Color(0xFF667EEA).withOpacity(0.30),
                          ),
                        ),
                        child: DropdownButtonHideUnderline(
                          child: DropdownButton<int>(
                            value: _durasiBulan,
                            isExpanded: true,
                            items: const [
                              DropdownMenuItem(
                                value: 6,
                                child: Text('6 bulan'),
                              ),
                              DropdownMenuItem(
                                value: 12,
                                child: Text('1 tahun'),
                              ),
                            ],
                            onChanged: (value) {
                              if (value != null) {
                                setState(() => _durasiBulan = value);
                              }
                            },
                          ),
                        ),
                      ),
                    ),
                  ],
                  if (!_isSurvei) ...[
                    const SizedBox(height: 16),
                    _buildFormCard(
                      icon: Icons.badge_outlined,
                      title: 'Upload Foto KTP',
                      subtitle:
                          'Pilih foto KTP untuk melengkapi pengajuan sewa',
                      child: Column(
                        children: [
                          if (_ktpPhoto != null) ...[
                            Stack(
                              children: [
                                ClipRRect(
                                  borderRadius: BorderRadius.circular(12),
                                  child: Image.file(
                                    _ktpPhoto!,
                                    width: double.infinity,
                                    height: 180,
                                    fit: BoxFit.cover,
                                  ),
                                ),
                                Positioned(
                                  top: 8,
                                  right: 8,
                                  child: InkWell(
                                    onTap: () {
                                      setState(() => _ktpPhoto = null);
                                    },
                                    child: Container(
                                      padding: const EdgeInsets.all(6),
                                      decoration: const BoxDecoration(
                                        color: Colors.red,
                                        shape: BoxShape.circle,
                                      ),
                                      child: const Icon(
                                        Icons.close,
                                        color: Colors.white,
                                        size: 18,
                                      ),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 12),
                          ],
                          SizedBox(
                            width: double.infinity,
                            child: OutlinedButton.icon(
                              onPressed: _pickKtpPhoto,
                              icon: Icon(
                                _ktpPhoto == null
                                    ? Icons.upload_file_rounded
                                    : Icons.change_circle_rounded,
                              ),
                              label: Text(
                                _ktpPhoto == null
                                    ? 'Pilih Foto KTP'
                                    : 'Ganti Foto KTP',
                              ),
                              style: OutlinedButton.styleFrom(
                                foregroundColor: const Color(0xFF667EEA),
                                side: const BorderSide(
                                  color: Color(0xFF667EEA),
                                ),
                                padding: const EdgeInsets.symmetric(
                                  vertical: 13,
                                ),
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(12),
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                  const SizedBox(height: 16),
                  _buildFormCard(
                    icon: Icons.note_alt_outlined,
                    title: 'Catatan (Opsional)',
                    subtitle: _isSurvei
                        ? 'Contoh: Saya ingin survei bersama orang tua.'
                        : 'Contoh: Saya ingin mulai menempati bulan depan.',
                    child: TextField(
                      controller: _catatanController,
                      maxLines: 3,
                      decoration: InputDecoration(
                        hintText: 'Tulis catatan untuk pemilik kontrakan...',
                        filled: true,
                        fillColor: Colors.grey[100],
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                          borderSide: BorderSide(color: Colors.grey[300]!),
                        ),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                          borderSide: BorderSide(color: Colors.grey[300]!),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                          borderSide: const BorderSide(
                            color: Color(0xFF667EEA),
                          ),
                        ),
                      ),
                    ),
                  ),
                  if (!_isSurvei) ...[
                    const SizedBox(height: 16),
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(
                          color: const Color(0xFF667EEA).withOpacity(0.20),
                        ),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Ringkasan Estimasi Sewa',
                            style: TextStyle(
                              fontWeight: FontWeight.bold,
                              fontSize: 15,
                            ),
                          ),
                          const SizedBox(height: 12),
                          _buildBiayaRow(
                            'Harga per tahun',
                            widget.kontrakan.formattedHarga,
                          ),
                          const SizedBox(height: 6),
                          _buildBiayaRow('Durasi', _durasiLabel),
                          const Divider(height: 22),
                          _buildBiayaRow(
                            'Estimasi biaya',
                            _currencyFormat.format(_totalBiaya),
                          ),
                        ],
                      ),
                    ),
                  ],
                  const SizedBox(height: 24),
                  SizedBox(
                    width: double.infinity,
                    height: 52,
                    child: ElevatedButton(
                      onPressed: _isSubmitting ? null : _submitPengajuan,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF667EEA),
                        foregroundColor: Colors.white,
                        disabledBackgroundColor: Colors.grey[400],
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      child: _isSubmitting
                          ? const Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                SizedBox(
                                  width: 20,
                                  height: 20,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2,
                                    color: Colors.white,
                                  ),
                                ),
                                SizedBox(width: 12),
                                Text('Mengirim...'),
                              ],
                            )
                          : Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(
                                  _isSurvei
                                      ? Icons.event_available
                                      : Icons.home_work_outlined,
                                ),
                                const SizedBox(width: 8),
                                Text(
                                  _isSurvei
                                      ? 'Kirim Pengajuan Survei'
                                      : 'Kirim Pengajuan Sewa',
                                  style: const TextStyle(
                                    fontSize: 16,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              ],
                            ),
                    ),
                  ),
                  const SizedBox(height: 16),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
