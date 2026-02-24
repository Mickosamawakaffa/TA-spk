import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../config/app_config.dart';
import '../services/booking_service.dart';
import '../models/booking.dart';

class BookingHistoryScreen extends StatefulWidget {
  const BookingHistoryScreen({super.key});

  @override
  State<BookingHistoryScreen> createState() => _BookingHistoryScreenState();
}

class _BookingHistoryScreenState extends State<BookingHistoryScreen>
    with SingleTickerProviderStateMixin {
  final _bookingService = BookingService();
  final _imagePicker = ImagePicker();
  late TabController _tabController;

  List<Booking> _activeBookings = [];
  List<Booking> _pastBookings = [];
  bool _isLoading = true;
  int? _uploadingBookingId;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _loadBookings();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadBookings() async {
    setState(() => _isLoading = true);
    final bookings = await _bookingService.getBookingHistory();
    setState(() {
      _activeBookings = bookings
          .where((b) => b.status == 'confirmed' || b.status == 'pending')
          .toList();
      _pastBookings = bookings
          .where((b) => b.status == 'completed' || b.status == 'cancelled')
          .toList();
      _isLoading = false;
    });
  }

  Future<void> _cancelBooking(int bookingId) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Row(
          children: [
            Icon(Icons.warning_amber_rounded, color: Colors.red.shade600, size: 24),
            const SizedBox(width: 10),
            const Text('Batalkan Booking',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.w600)),
          ],
        ),
        content: const Text('Apakah Anda yakin ingin membatalkan booking ini?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: Text('Tidak', style: TextStyle(color: Colors.grey[600])),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(ctx, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red.shade600,
              foregroundColor: Colors.white,
              elevation: 0,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(10),
              ),
            ),
            child: const Text('Ya, Batalkan'),
          ),
        ],
      ),
    );
    if (confirm != true) return;
    final result = await _bookingService.cancelBooking(bookingId);
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Row(
            children: [
              Icon(
                result['success'] == true ? Icons.check_circle_rounded : Icons.error_outline_rounded,
                color: Colors.white, size: 20,
              ),
              const SizedBox(width: 10),
              Expanded(child: Text(result['message'] ?? '')),
            ],
          ),
          backgroundColor: result['success'] == true
              ? const Color(0xFF2E7D32)
              : const Color(0xFFC62828),
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          margin: const EdgeInsets.all(16),
        ),
      );
      if (result['success'] == true) _loadBookings();
    }
  }

  Future<void> _uploadPaymentProof(Booking booking) async {
    // Show source dialog
    final source = await showModalBottomSheet<ImageSource>(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (ctx) => SafeArea(
        child: Padding(
          padding: const EdgeInsets.fromLTRB(20, 12, 20, 16),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(
                child: Container(
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(
                    color: Colors.grey[300],
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
              ),
              const SizedBox(height: 20),
              const Text(
                'Unggah Bukti Pembayaran',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.w700,
                  color: Color(0xFF1A1A2E),
                ),
              ),
              const SizedBox(height: 6),
              Text(
                'Pilih foto struk transfer atau bukti pembayaran',
                style: TextStyle(color: Colors.grey[500], fontSize: 13),
              ),
              const SizedBox(height: 20),
              ListTile(
                contentPadding: const EdgeInsets.symmetric(horizontal: 4),
                leading: Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: const Color(0xFF1565C0).withOpacity(0.08),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Icon(
                    Icons.photo_library_rounded,
                    color: Color(0xFF1565C0),
                    size: 22,
                  ),
                ),
                title: const Text('Pilih dari Galeri',
                    style: TextStyle(fontWeight: FontWeight.w600)),
                onTap: () => Navigator.pop(ctx, ImageSource.gallery),
              ),
              ListTile(
                contentPadding: const EdgeInsets.symmetric(horizontal: 4),
                leading: Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: const Color(0xFF1565C0).withOpacity(0.08),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Icon(
                    Icons.camera_alt_rounded,
                    color: Color(0xFF1565C0),
                    size: 22,
                  ),
                ),
                title: const Text('Ambil Foto',
                    style: TextStyle(fontWeight: FontWeight.w600)),
                onTap: () => Navigator.pop(ctx, ImageSource.camera),
              ),
            ],
          ),
        ),
      ),
    );
    if (source == null) return;

    final picked = await _imagePicker.pickImage(
      source: source,
      maxWidth: 1920,
      maxHeight: 1920,
      imageQuality: 85,
    );
    if (picked == null) return;

    setState(() => _uploadingBookingId = booking.id);
    final result = await _bookingService.uploadPaymentProof(
      booking.id,
      File(picked.path),
    );
    if (mounted) {
      setState(() => _uploadingBookingId = null);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Row(
            children: [
              Icon(
                result['success'] == true ? Icons.check_circle_rounded : Icons.error_outline_rounded,
                color: Colors.white, size: 20,
              ),
              const SizedBox(width: 10),
              Expanded(child: Text(result['message'] ?? '')),
            ],
          ),
          backgroundColor: result['success'] == true
              ? const Color(0xFF2E7D32)
              : const Color(0xFFC62828),
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          margin: const EdgeInsets.all(16),
          duration: const Duration(seconds: 3),
        ),
      );
      if (result['success'] == true) _loadBookings();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF7F8FC),
      body: SafeArea(
        child: Column(
          children: [
            // Header - consistent blue theme
            Container(
              padding: const EdgeInsets.fromLTRB(20, 16, 20, 0),
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  colors: [Color(0xFF1565C0), Color(0xFF0D47A1)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.only(
                  bottomLeft: Radius.circular(24),
                  bottomRight: Radius.circular(24),
                ),
              ),
              child: Column(
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.15),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: const Icon(
                          Icons.receipt_long_rounded,
                          color: Colors.white,
                          size: 24,
                        ),
                      ),
                      const SizedBox(width: 14),
                      const Text(
                        'Booking Saya',
                        style: TextStyle(
                          fontSize: 22,
                          fontWeight: FontWeight.w700,
                          color: Colors.white,
                          letterSpacing: 0.3,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),
                  // Tabs
                  Container(
                    margin: const EdgeInsets.only(bottom: 16),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(14),
                    ),
                    padding: const EdgeInsets.all(4),
                    child: TabBar(
                      controller: _tabController,
                      indicator: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(11),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.08),
                            blurRadius: 4,
                            offset: const Offset(0, 2),
                          ),
                        ],
                      ),
                      dividerColor: Colors.transparent,
                      labelColor: const Color(0xFF1565C0),
                      unselectedLabelColor: Colors.white.withOpacity(0.85),
                      labelStyle: const TextStyle(
                        fontWeight: FontWeight.w700,
                        fontSize: 14,
                      ),
                      unselectedLabelStyle: const TextStyle(
                        fontWeight: FontWeight.w500,
                        fontSize: 14,
                      ),
                      tabs: const [
                        Tab(text: 'Aktif'),
                        Tab(text: 'Riwayat'),
                      ],
                    ),
                  ),
                ],
              ),
            ),

            // Content
            Expanded(
              child: _isLoading
                  ? const Center(
                      child: CircularProgressIndicator(
                        color: Color(0xFF1565C0),
                      ),
                    )
                  : TabBarView(
                      controller: _tabController,
                      children: [
                        _buildBookingList(_activeBookings, true),
                        _buildBookingList(_pastBookings, false),
                      ],
                    ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBookingList(List<Booking> bookings, bool isActive) {
    if (bookings.isEmpty) {
      return RefreshIndicator(
        onRefresh: _loadBookings,
        child: ListView(
          physics: const AlwaysScrollableScrollPhysics(),
          children: [
            SizedBox(height: MediaQuery.of(context).size.height * 0.2),
            Center(
              child: Container(
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                  color: const Color(0xFF1565C0).withOpacity(0.06),
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  isActive ? Icons.bookmark_outline_rounded : Icons.history_rounded,
                  size: 56,
                  color: const Color(0xFF1565C0).withOpacity(0.3),
                ),
              ),
            ),
            const SizedBox(height: 20),
            Center(
              child: Text(
                isActive ? 'Belum ada booking aktif' : 'Belum ada riwayat',
                style: const TextStyle(
                  fontSize: 17,
                  fontWeight: FontWeight.w600,
                  color: Color(0xFF1A1A2E),
                ),
              ),
            ),
            const SizedBox(height: 8),
            Center(
              child: Text(
                isActive
                    ? 'Booking kontrakan Anda akan muncul di sini'
                    : 'Riwayat booking sebelumnya akan muncul di sini',
                style: TextStyle(fontSize: 13, color: Colors.grey[500]),
                textAlign: TextAlign.center,
              ),
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _loadBookings,
      child: ListView.builder(
        physics: const AlwaysScrollableScrollPhysics(),
        padding: const EdgeInsets.fromLTRB(20, 16, 20, 16),
        itemCount: bookings.length,
        itemBuilder: (context, index) {
          return _buildBookingCard(bookings[index]);
        },
      ),
    );
  }

  Widget _buildBookingCard(Booking booking) {
    Color statusColor;
    String statusText;
    IconData statusIcon;

    switch (booking.status) {
      case 'pending':
        statusColor = const Color(0xFFF57C00);
        statusText = 'Menunggu';
        statusIcon = Icons.schedule_rounded;
        break;
      case 'confirmed':
        statusColor = const Color(0xFF2E7D32);
        statusText = 'Dikonfirmasi';
        statusIcon = Icons.check_circle_rounded;
        break;
      case 'completed':
        statusColor = const Color(0xFF1565C0);
        statusText = 'Selesai';
        statusIcon = Icons.done_all_rounded;
        break;
      case 'cancelled':
        statusColor = const Color(0xFFC62828);
        statusText = 'Dibatalkan';
        statusIcon = Icons.cancel_rounded;
        break;
      default:
        statusColor = Colors.grey;
        statusText = 'Unknown';
        statusIcon = Icons.help_rounded;
    }

    // Get kontrakan name if available
    String kontrakanName = 'Kontrakan';
    if (booking.kontrakan is Map) {
      kontrakanName = booking.kontrakan['nama'] ?? 'Kontrakan';
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 12,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        children: [
          // Card Header with status
          Container(
            padding: const EdgeInsets.fromLTRB(16, 14, 16, 12),
            decoration: BoxDecoration(
              color: statusColor.withOpacity(0.04),
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(18),
                topRight: Radius.circular(18),
              ),
            ),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: const Color(0xFF1565C0).withOpacity(0.08),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: const Icon(
                    Icons.home_rounded,
                    size: 20,
                    color: Color(0xFF1565C0),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        kontrakanName,
                        style: const TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.w700,
                          color: Color(0xFF1A1A2E),
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 2),
                      Text(
                        'ID: #${booking.id}',
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.grey[500],
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ],
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 10,
                    vertical: 5,
                  ),
                  decoration: BoxDecoration(
                    color: statusColor.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(statusIcon, size: 14, color: statusColor),
                      const SizedBox(width: 4),
                      Text(
                        statusText,
                        style: TextStyle(
                          color: statusColor,
                          fontWeight: FontWeight.w600,
                          fontSize: 12,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),

          // Card Body
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 16),
            child: Column(
              children: [
                // Date info in a compact row
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF7F8FC),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Column(
                    children: [
                      _buildDateRow(
                        Icons.login_rounded,
                        'Mulai',
                        _formatDate(booking.tanggalMulai),
                      ),
                      Padding(
                        padding: const EdgeInsets.symmetric(vertical: 8),
                        child: Divider(
                          height: 1,
                          color: Colors.grey[200],
                        ),
                      ),
                      _buildDateRow(
                        Icons.logout_rounded,
                        'Selesai',
                        _formatDate(booking.tanggalSelesai),
                      ),
                      Padding(
                        padding: const EdgeInsets.symmetric(vertical: 8),
                        child: Divider(
                          height: 1,
                          color: Colors.grey[200],
                        ),
                      ),
                      _buildDateRow(
                        Icons.timelapse_rounded,
                        'Durasi',
                        '${booking.durasiBulan} Bulan',
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 14),

                // Price and Payment
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Total Harga',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[500],
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          'Rp ${_formatPrice(booking.totalBiaya)}',
                          style: const TextStyle(
                            fontSize: 20,
                            fontWeight: FontWeight.w800,
                            color: Color(0xFF1565C0),
                          ),
                        ),
                      ],
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 12,
                        vertical: 6,
                      ),
                      decoration: BoxDecoration(
                        color: booking.paymentStatus == 'paid'
                            ? const Color(0xFFE8F5E9)
                            : const Color(0xFFFFF3E0),
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(
                            booking.paymentStatus == 'paid'
                                ? Icons.check_circle_rounded
                                : Icons.pending_rounded,
                            size: 14,
                            color: booking.paymentStatus == 'paid'
                                ? const Color(0xFF2E7D32)
                                : const Color(0xFFF57C00),
                          ),
                          const SizedBox(width: 5),
                          Text(
                            booking.paymentStatus == 'paid' ? 'Lunas' : 'Belum Bayar',
                            style: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.w600,
                              color: booking.paymentStatus == 'paid'
                                  ? const Color(0xFF2E7D32)
                                  : const Color(0xFFF57C00),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),

                // Payment proof
                if (booking.paymentProof != null) ...[
                  const SizedBox(height: 12),
                  GestureDetector(
                    onTap: () {
                      final url = '${AppConfig.storageUrl}/${booking.paymentProof}';
                      showDialog(
                        context: context,
                        builder: (ctx) => Dialog(
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                          child: Column(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Container(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 16,
                                  vertical: 12,
                                ),
                                decoration: const BoxDecoration(
                                  color: Color(0xFF1565C0),
                                  borderRadius: BorderRadius.only(
                                    topLeft: Radius.circular(16),
                                    topRight: Radius.circular(16),
                                  ),
                                ),
                                child: Row(
                                  children: [
                                    const Icon(Icons.receipt_long_rounded,
                                        color: Colors.white, size: 20),
                                    const SizedBox(width: 10),
                                    const Expanded(
                                      child: Text(
                                        'Bukti Pembayaran',
                                        style: TextStyle(
                                          color: Colors.white,
                                          fontWeight: FontWeight.w600,
                                          fontSize: 16,
                                        ),
                                      ),
                                    ),
                                    GestureDetector(
                                      onTap: () => Navigator.pop(ctx),
                                      child: Container(
                                        padding: const EdgeInsets.all(4),
                                        decoration: BoxDecoration(
                                          color: Colors.white.withOpacity(0.2),
                                          borderRadius: BorderRadius.circular(8),
                                        ),
                                        child: const Icon(Icons.close_rounded,
                                            color: Colors.white, size: 18),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              ClipRRect(
                                borderRadius: const BorderRadius.only(
                                  bottomLeft: Radius.circular(16),
                                  bottomRight: Radius.circular(16),
                                ),
                                child: Image.network(url, fit: BoxFit.contain),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                    child: Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 12,
                        vertical: 10,
                      ),
                      decoration: BoxDecoration(
                        color: const Color(0xFFE8F5E9),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(6),
                            decoration: BoxDecoration(
                              color: const Color(0xFF2E7D32).withOpacity(0.1),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: const Icon(Icons.image_rounded,
                                size: 16, color: Color(0xFF2E7D32)),
                          ),
                          const SizedBox(width: 10),
                          const Expanded(
                            child: Text(
                              'Bukti pembayaran diunggah',
                              style: TextStyle(
                                fontSize: 13,
                                color: Color(0xFF2E7D32),
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                          ),
                          const Icon(Icons.chevron_right_rounded,
                              color: Color(0xFF2E7D32), size: 20),
                        ],
                      ),
                    ),
                  ),
                ],

                // Actions
                if (booking.status == 'pending') ...[
                  const SizedBox(height: 14),
                  SizedBox(
                    width: double.infinity,
                    child: OutlinedButton.icon(
                      onPressed: () => _cancelBooking(booking.id),
                      icon: const Icon(Icons.close_rounded, size: 18),
                      label: const Text('Batalkan Booking'),
                      style: OutlinedButton.styleFrom(
                        foregroundColor: const Color(0xFFC62828),
                        side: const BorderSide(color: Color(0xFFEF9A9A)),
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                    ),
                  ),
                ],
                if (booking.status == 'confirmed' &&
                    booking.paymentStatus == 'unpaid') ...[
                  const SizedBox(height: 14),
                  SizedBox(
                    width: double.infinity,
                    child: _uploadingBookingId == booking.id
                        ? const Center(
                            child: Padding(
                              padding: EdgeInsets.symmetric(vertical: 12),
                              child: CircularProgressIndicator(
                                color: Color(0xFF1565C0),
                              ),
                            ),
                          )
                        : ElevatedButton.icon(
                            onPressed: () => _uploadPaymentProof(booking),
                            icon: const Icon(Icons.upload_rounded, size: 18),
                            label: Text(
                              booking.paymentProof == null
                                  ? 'Upload Bukti Pembayaran'
                                  : 'Ganti Bukti',
                            ),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xFF1565C0),
                              foregroundColor: Colors.white,
                              elevation: 0,
                              padding: const EdgeInsets.symmetric(vertical: 12),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(12),
                              ),
                            ),
                          ),
                  ),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDateRow(IconData icon, String label, String value) {
    return Row(
      children: [
        Icon(icon, size: 16, color: const Color(0xFF1565C0).withOpacity(0.6)),
        const SizedBox(width: 10),
        Text(
          label,
          style: TextStyle(
            fontSize: 13,
            color: Colors.grey[500],
            fontWeight: FontWeight.w500,
          ),
        ),
        const Spacer(),
        Text(
          value,
          style: const TextStyle(
            fontSize: 13,
            fontWeight: FontWeight.w600,
            color: Color(0xFF1A1A2E),
          ),
        ),
      ],
    );
  }

  String _formatDate(DateTime date) {
    const months = [
      'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
      'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des',
    ];
    return '${date.day} ${months[date.month - 1]} ${date.year}';
  }

  String _formatPrice(double price) {
    return price
        .toStringAsFixed(0)
        .replaceAllMapped(
          RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
          (Match m) => '${m[1]}.',
        );
  }
}
