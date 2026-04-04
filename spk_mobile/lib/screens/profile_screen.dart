import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import '../services/favorite_service.dart';
import '../services/booking_service.dart';
import '../models/user.dart';
import '../login.dart';
import 'edit_profile_screen.dart';
import 'change_password_screen.dart';
import 'booking_history_screen.dart';
import 'favorites_screen.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen>
    with SingleTickerProviderStateMixin {
  final _authService = AuthService();
  final _favoriteService = FavoriteService();
  final _bookingService = BookingService();
  User? _currentUser;
  bool _isLoading = true;
  int _totalFavorites = 0;
  int _totalBookings = 0;

  late AnimationController _animController;
  late Animation<double> _fadeAnim;

  @override
  void initState() {
    super.initState();
    _animController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 600),
    );
    _fadeAnim = CurvedAnimation(parent: _animController, curve: Curves.easeOut);
    _loadAll();
  }

  @override
  void dispose() {
    _animController.dispose();
    super.dispose();
  }

  Future<void> _loadAll() async {
    setState(() => _isLoading = true);
    try {
      await Future.wait([_loadUser(), _loadStats()]);
    } catch (_) {}
    if (!mounted) return;
    setState(() => _isLoading = false);
    _animController.forward(from: 0);
  }

  Future<void> _loadUser() async {
    try {
      final user = await _authService.getCurrentUser();
      if (!mounted) return;
      setState(() => _currentUser = user);
    } catch (e) {
      debugPrint('Load user error: $e');
    }
  }

  Future<void> _loadStats() async {
    try {
      final favResult = await _favoriteService.getFavoriteIds();
      final bookings = await _bookingService.getBookingHistory();
      if (!mounted) return;
      setState(() {
        _totalFavorites =
            (favResult['kontrakan']?.length ?? 0) +
            (favResult['laundry']?.length ?? 0);
        _totalBookings = bookings.length;
      });
    } catch (e) {
      debugPrint('Load stats error: $e');
    }
  }

  String _getInitials(String name) {
    if (name.isEmpty) return 'U';
    final parts = name.trim().split(' ');
    if (parts.length >= 2) {
      return '${parts[0][0]}${parts[1][0]}'.toUpperCase();
    }
    return parts[0][0].toUpperCase();
  }

  String _getMemberSince(DateTime? date) {
    if (date == null) return 'Baru bergabung';
    const months = [
      '',
      'Januari',
      'Februari',
      'Maret',
      'April',
      'Mei',
      'Juni',
      'Juli',
      'Agustus',
      'September',
      'Oktober',
      'November',
      'Desember',
    ];
    return '${months[date.month]} ${date.year}';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F6FA),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(color: Color(0xFF1565C0)),
            )
          : RefreshIndicator(
              onRefresh: _loadAll,
              color: const Color(0xFF1565C0),
              child: FadeTransition(
                opacity: _fadeAnim,
                child: CustomScrollView(
                  physics: const AlwaysScrollableScrollPhysics(),
                  slivers: [
                    // ==================== HEADER ====================
                    SliverToBoxAdapter(child: _buildHeader()),

                    // ==================== STATS ====================
                    SliverToBoxAdapter(
                      child: Transform.translate(
                        offset: const Offset(0, -30),
                        child: _buildStatsRow(),
                      ),
                    ),

                    // ==================== MENU SECTIONS ====================
                    SliverPadding(
                      padding: const EdgeInsets.fromLTRB(20, 0, 20, 20),
                      sliver: SliverList(
                        delegate: SliverChildListDelegate([
                          // --- Akun ---
                          _buildSectionTitle('Akun', Icons.person_rounded),
                          const SizedBox(height: 10),
                          _buildMenuCard([
                            _buildMenuItem(
                              icon: Icons.edit_rounded,
                              iconColor: const Color(0xFF1565C0),
                              iconBg: const Color(0xFF1565C0),
                              title: 'Edit Profil',
                              subtitle: 'Ubah nama, email, dan no. HP',
                              onTap: () async {
                                if (_currentUser == null) return;
                                final result = await Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (_) =>
                                        EditProfileScreen(user: _currentUser!),
                                  ),
                                );
                                if (result == true) _loadAll();
                              },
                            ),
                            _buildMenuItem(
                              icon: Icons.lock_rounded,
                              iconColor: const Color(0xFF6A1B9A),
                              iconBg: const Color(0xFF6A1B9A),
                              title: 'Ubah Password',
                              subtitle: 'Ganti password akun Anda',
                              onTap: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (_) =>
                                        const ChangePasswordScreen(),
                                  ),
                                );
                              },
                            ),
                          ]),

                          const SizedBox(height: 24),

                          // --- Aktivitas ---
                          _buildSectionTitle(
                            'Aktivitas',
                            Icons.timeline_rounded,
                          ),
                          const SizedBox(height: 10),
                          _buildMenuCard([
                            _buildMenuItem(
                              icon: Icons.receipt_long_rounded,
                              iconColor: const Color(0xFF00897B),
                              iconBg: const Color(0xFF00897B),
                              title: 'Booking Saya',
                              subtitle: '$_totalBookings booking tercatat',
                              onTap: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (_) =>
                                        const BookingHistoryScreen(),
                                  ),
                                );
                              },
                            ),
                            _buildMenuItem(
                              icon: Icons.favorite_rounded,
                              iconColor: const Color(0xFFE53935),
                              iconBg: const Color(0xFFE53935),
                              title: 'Favorit Saya',
                              subtitle: '$_totalFavorites item difavoritkan',
                              onTap: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (_) => const FavoritesScreen(),
                                  ),
                                );
                              },
                            ),
                          ]),

                          const SizedBox(height: 24),

                          // --- Lainnya ---
                          _buildSectionTitle(
                            'Lainnya',
                            Icons.more_horiz_rounded,
                          ),
                          const SizedBox(height: 10),
                          _buildMenuCard([
                            _buildMenuItem(
                              icon: Icons.info_rounded,
                              iconColor: const Color(0xFFF57C00),
                              iconBg: const Color(0xFFF57C00),
                              title: 'Tentang Aplikasi',
                              subtitle: 'Informasi & versi aplikasi',
                              onTap: _showAboutDialog,
                            ),
                          ]),

                          const SizedBox(height: 32),

                          // --- Logout ---
                          _buildLogoutButton(),

                          const SizedBox(height: 40),
                        ]),
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }

  // ==================== HEADER ====================
  Widget _buildHeader() {
    return Container(
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          colors: [Color(0xFF0D47A1), Color(0xFF1976D2), Color(0xFF42A5F5)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(36),
          bottomRight: Radius.circular(36),
        ),
      ),
      child: Stack(
        children: [
          // Decorative circles
          Positioned(
            top: -40,
            right: -30,
            child: Container(
              width: 150,
              height: 150,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: Colors.white.withValues(alpha: 0.06),
              ),
            ),
          ),
          Positioned(
            bottom: 20,
            left: -20,
            child: Container(
              width: 100,
              height: 100,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: Colors.white.withValues(alpha: 0.04),
              ),
            ),
          ),
          Positioned(
            top: 30,
            left: 50,
            child: Container(
              width: 60,
              height: 60,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: Colors.white.withValues(alpha: 0.03),
              ),
            ),
          ),

          // Content
          SafeArea(
            bottom: false,
            child: Padding(
              padding: const EdgeInsets.fromLTRB(24, 16, 24, 56),
              child: Column(
                children: [
                  // Title bar
                  const Row(
                    children: [
                      Icon(Icons.person_rounded, color: Colors.white, size: 24),
                      SizedBox(width: 10),
                      Text(
                        'Profil Saya',
                        style: TextStyle(
                          fontSize: 22,
                          fontWeight: FontWeight.w800,
                          color: Colors.white,
                          letterSpacing: 0.3,
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(height: 28),

                  // Avatar + Info
                  Row(
                    children: [
                      // Avatar with shadow and gradient ring
                      Container(
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withValues(alpha: 0.2),
                              blurRadius: 16,
                              offset: const Offset(0, 6),
                            ),
                          ],
                        ),
                        child: Container(
                          padding: const EdgeInsets.all(3),
                          decoration: const BoxDecoration(
                            shape: BoxShape.circle,
                            gradient: LinearGradient(
                              colors: [Colors.white, Color(0xFFBBDEFB)],
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight,
                            ),
                          ),
                          child: CircleAvatar(
                            radius: 40,
                            backgroundColor: const Color(0xFF0D47A1),
                            child: Text(
                              _getInitials(_currentUser?.name ?? ''),
                              style: const TextStyle(
                                fontSize: 28,
                                fontWeight: FontWeight.w800,
                                color: Colors.white,
                                letterSpacing: 1,
                              ),
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 18),

                      // Name, email, phone, member since
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              _currentUser?.name ?? 'User',
                              style: const TextStyle(
                                fontSize: 22,
                                fontWeight: FontWeight.w800,
                                color: Colors.white,
                                letterSpacing: 0.2,
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                            const SizedBox(height: 6),
                            _headerInfoRow(
                              Icons.email_outlined,
                              _currentUser?.email ?? '-',
                            ),
                            if (_currentUser?.phone != null &&
                                _currentUser!.phone!.isNotEmpty) ...[
                              const SizedBox(height: 4),
                              _headerInfoRow(
                                Icons.phone_outlined,
                                _currentUser!.phone!,
                              ),
                            ],
                            const SizedBox(height: 8),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 10,
                                vertical: 4,
                              ),
                              decoration: BoxDecoration(
                                color: Colors.white.withValues(alpha: 0.15),
                                borderRadius: BorderRadius.circular(20),
                              ),
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  Icon(
                                    Icons.calendar_month_rounded,
                                    size: 13,
                                    color: Colors.white.withValues(alpha: 0.85),
                                  ),
                                  const SizedBox(width: 5),
                                  Text(
                                    'Bergabung ${_getMemberSince(_currentUser?.createdAt)}',
                                    style: TextStyle(
                                      fontSize: 11,
                                      fontWeight: FontWeight.w600,
                                      color: Colors.white.withValues(
                                        alpha: 0.9,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _headerInfoRow(IconData icon, String text) {
    return Row(
      children: [
        Icon(icon, size: 14, color: Colors.white.withValues(alpha: 0.75)),
        const SizedBox(width: 6),
        Expanded(
          child: Text(
            text,
            style: TextStyle(
              fontSize: 13,
              color: Colors.white.withValues(alpha: 0.85),
              fontWeight: FontWeight.w500,
            ),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
        ),
      ],
    );
  }

  // ==================== STATS ROW ====================
  Widget _buildStatsRow() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 18, horizontal: 8),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF1565C0).withValues(alpha: 0.10),
              blurRadius: 20,
              offset: const Offset(0, 8),
            ),
          ],
        ),
        child: Row(
          children: [
            _buildStatItem(
              icon: Icons.favorite_rounded,
              color: const Color(0xFFE53935),
              value: '$_totalFavorites',
              label: 'Favorit',
            ),
            _buildStatDivider(),
            _buildStatItem(
              icon: Icons.receipt_long_rounded,
              color: const Color(0xFF00897B),
              value: '$_totalBookings',
              label: 'Booking',
            ),
            _buildStatDivider(),
            _buildStatItem(
              icon: Icons.verified_user_rounded,
              color: const Color(0xFF1565C0),
              value: _currentUser?.getRoleLabel() ?? 'Mahasiswa',
              label: 'Status',
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatItem({
    required IconData icon,
    required Color color,
    required String value,
    required String label,
  }) {
    return Expanded(
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: color.withValues(alpha: 0.1),
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: color, size: 22),
          ),
          const SizedBox(height: 8),
          Text(
            value,
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w800,
              color: Color(0xFF1A1A2E),
            ),
          ),
          const SizedBox(height: 2),
          Text(
            label,
            style: TextStyle(
              fontSize: 12,
              color: Colors.grey.shade500,
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatDivider() {
    return Container(width: 1, height: 45, color: Colors.grey.shade200);
  }

  // ==================== SECTION TITLE ====================
  Widget _buildSectionTitle(String text, IconData icon) {
    return Row(
      children: [
        Icon(icon, size: 18, color: Colors.grey.shade600),
        const SizedBox(width: 8),
        Text(
          text.toUpperCase(),
          style: TextStyle(
            fontSize: 12,
            fontWeight: FontWeight.w800,
            color: Colors.grey.shade600,
            letterSpacing: 1.3,
          ),
        ),
      ],
    );
  }

  // ==================== MENU CARD ====================
  Widget _buildMenuCard(List<Widget> items) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 12,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(18),
        child: Column(
          children: List.generate(items.length * 2 - 1, (index) {
            if (index.isOdd) {
              return Divider(
                height: 1,
                thickness: 0.5,
                indent: 72,
                color: Colors.grey.shade100,
              );
            }
            return items[index ~/ 2];
          }),
        ),
      ),
    );
  }

  Widget _buildMenuItem({
    required IconData icon,
    required Color iconColor,
    required Color iconBg,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
  }) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 15),
          child: Row(
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [
                      iconBg.withValues(alpha: 0.15),
                      iconBg.withValues(alpha: 0.08),
                    ],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(13),
                ),
                child: Icon(icon, color: iconColor, size: 22),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w700,
                        color: Color(0xFF1A1A2E),
                      ),
                    ),
                    const SizedBox(height: 3),
                    Text(
                      subtitle,
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors.grey.shade500,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.all(6),
                decoration: BoxDecoration(
                  color: Colors.grey.shade50,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Icon(
                  Icons.chevron_right_rounded,
                  color: Colors.grey.shade400,
                  size: 20,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // ==================== LOGOUT ====================
  Widget _buildLogoutButton() {
    return Container(
      width: double.infinity,
      height: 54,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.red.shade200, width: 1.5),
      ),
      child: Material(
        color: Colors.red.shade50.withValues(alpha: 0.5),
        borderRadius: BorderRadius.circular(16),
        child: InkWell(
          onTap: _handleLogout,
          borderRadius: BorderRadius.circular(16),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.logout_rounded, color: Colors.red.shade600, size: 20),
              const SizedBox(width: 10),
              Text(
                'Keluar dari Akun',
                style: TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.w700,
                  color: Colors.red.shade600,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // ==================== DIALOGS ====================
  void _showAboutDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        contentPadding: const EdgeInsets.fromLTRB(28, 28, 28, 16),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // App icon
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF0D47A1), Color(0xFF42A5F5)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: const Color(0xFF1565C0).withValues(alpha: 0.3),
                    blurRadius: 12,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: const Icon(
                Icons.home_work_rounded,
                color: Colors.white,
                size: 40,
              ),
            ),
            const SizedBox(height: 20),
            const Text(
              'Kontrak Kampus',
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.w800,
                color: Color(0xFF1A1A2E),
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 6),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
              decoration: BoxDecoration(
                color: const Color(0xFF1565C0).withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(20),
              ),
              child: const Text(
                'Versi 2.5.0',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w700,
                  color: Color(0xFF1565C0),
                ),
              ),
            ),
            const SizedBox(height: 18),
            Text(
              'Sistem Pendukung Keputusan untuk membantu mahasiswa Polije menemukan kontrakan dan laundry terbaik menggunakan metode SAW (Simple Additive Weighting).',
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 13,
                color: Colors.grey.shade600,
                height: 1.6,
              ),
            ),
            const SizedBox(height: 20),
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.grey.shade50,
                borderRadius: BorderRadius.circular(14),
              ),
              child: Column(
                children: [
                  _aboutDetailRow(Icons.code_rounded, 'Developer', 'Tim TA'),
                  const SizedBox(height: 10),
                  _aboutDetailRow(Icons.school_rounded, 'Institusi', 'Polije'),
                  const SizedBox(height: 10),
                  _aboutDetailRow(
                    Icons.calendar_today_rounded,
                    'Tahun',
                    '2025',
                  ),
                ],
              ),
            ),
          ],
        ),
        actions: [
          SizedBox(
            width: double.infinity,
            child: TextButton(
              onPressed: () => Navigator.pop(context),
              style: TextButton.styleFrom(
                foregroundColor: const Color(0xFF1565C0),
                padding: const EdgeInsets.symmetric(vertical: 12),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
              child: const Text(
                'Tutup',
                style: TextStyle(fontWeight: FontWeight.w700),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _aboutDetailRow(IconData icon, String label, String value) {
    return Row(
      children: [
        Icon(icon, size: 16, color: const Color(0xFF1565C0)),
        const SizedBox(width: 10),
        Text(
          label,
          style: TextStyle(
            fontSize: 13,
            color: Colors.grey.shade600,
            fontWeight: FontWeight.w500,
          ),
        ),
        const Spacer(),
        Text(
          value,
          style: const TextStyle(
            fontSize: 13,
            fontWeight: FontWeight.w700,
            color: Color(0xFF1A1A2E),
          ),
        ),
      ],
    );
  }

  Future<void> _handleLogout() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        contentPadding: const EdgeInsets.fromLTRB(28, 28, 28, 12),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.red.shade50,
                shape: BoxShape.circle,
              ),
              child: Icon(
                Icons.logout_rounded,
                color: Colors.red.shade600,
                size: 32,
              ),
            ),
            const SizedBox(height: 20),
            const Text(
              'Keluar dari Akun?',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w800,
                color: Color(0xFF1A1A2E),
              ),
            ),
            const SizedBox(height: 10),
            Text(
              'Anda harus login kembali untuk mengakses fitur aplikasi',
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 13,
                color: Colors.grey.shade600,
                height: 1.5,
              ),
            ),
          ],
        ),
        actions: [
          Row(
            children: [
              Expanded(
                child: OutlinedButton(
                  onPressed: () => Navigator.pop(context, false),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: Colors.grey.shade700,
                    side: BorderSide(color: Colors.grey.shade300),
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  child: const Text(
                    'Batal',
                    style: TextStyle(fontWeight: FontWeight.w600),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: ElevatedButton(
                  onPressed: () => Navigator.pop(context, true),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.red.shade600,
                    foregroundColor: Colors.white,
                    elevation: 0,
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  child: const Text(
                    'Keluar',
                    style: TextStyle(fontWeight: FontWeight.w700),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );

    if (confirm == true) {
      await _authService.logout();
      if (mounted) {
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(builder: (_) => const LoginScreen()),
          (route) => false,
        );
      }
    }
  }
}
