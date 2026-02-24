import 'package:flutter/material.dart';
import '../models/kontrakan.dart';
import '../models/laundry.dart';
import '../services/auth_service.dart';
import '../services/kontrakan_service.dart';
import '../services/laundry_service.dart';
import '../services/favorite_service.dart';
import 'search_screen.dart';
import 'booking_history_screen.dart';
import 'profile_screen.dart';
import 'recommendation_screen.dart';
import 'favorites_screen.dart';
import 'kontrakan_detail_screen.dart';
import 'laundry_detail_screen.dart';
import '../login.dart';
import 'package:cached_network_image/cached_network_image.dart';

class ImprovedHomeScreen extends StatefulWidget {
  const ImprovedHomeScreen({super.key});

  @override
  State<ImprovedHomeScreen> createState() => _ImprovedHomeScreenState();
}

class _ImprovedHomeScreenState extends State<ImprovedHomeScreen> {
  final _authService = AuthService();
  final _kontrakanService = KontrakanService();
  final _laundryService = LaundryService();
  final _favoriteService = FavoriteService();

  List<Kontrakan> _kontrakanList = [];
  List<Laundry> _laundryList = [];
  Set<int> _favKontrakanIds = {};
  Set<int> _favLaundryIds = {};
  bool _isLoading = true;
  int _selectedIndex = 0;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);
    await Future.wait([_loadKontrakan(), _loadLaundry(), _loadFavoriteIds()]);
    setState(() => _isLoading = false);
  }

  Future<void> _loadFavoriteIds() async {
    try {
      if (!_authService.isAuthenticated) return;
      final ids = await _favoriteService.getFavoriteIds();
      setState(() {
        _favKontrakanIds = (ids['kontrakan'] ?? []).toSet();
        _favLaundryIds = (ids['laundry'] ?? []).toSet();
      });
    } catch (e) {
      // Error loading favorite ids silently
    }
  }

  Future<void> _toggleKontrakanFav(int id) async {
    final wasFav = _favKontrakanIds.contains(id);
    setState(() {
      if (wasFav) {
        _favKontrakanIds.remove(id);
      } else {
        _favKontrakanIds.add(id);
      }
    });
    final result = await _favoriteService.toggleKontrakanFavorite(id);
    if (result['success'] != true && mounted) {
      setState(() {
        if (wasFav) {
          _favKontrakanIds.add(id);
        } else {
          _favKontrakanIds.remove(id);
        }
      });
    }
  }

  Future<void> _toggleLaundryFav(int id) async {
    final wasFav = _favLaundryIds.contains(id);
    setState(() {
      if (wasFav) {
        _favLaundryIds.remove(id);
      } else {
        _favLaundryIds.add(id);
      }
    });
    final result = await _favoriteService.toggleLaundryFavorite(id);
    if (result['success'] != true && mounted) {
      setState(() {
        if (wasFav) {
          _favLaundryIds.add(id);
        } else {
          _favLaundryIds.remove(id);
        }
      });
    }
  }

  Future<void> _loadKontrakan() async {
    try {
      final list = await _kontrakanService.getKontrakan();
      setState(() => _kontrakanList = list.take(6).toList());
    } catch (e) {
      // Error loading kontrakan silently
    }
  }

  Future<void> _loadLaundry() async {
    try {
      final list = await _laundryService.getLaundry();
      setState(() => _laundryList = list.take(6).toList());
    } catch (e) {
      // Error loading laundry silently
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF7F8FC),
      body: _buildBody(),
      bottomNavigationBar: _buildBottomNav(),
    );
  }

  Widget _buildBody() {
    switch (_selectedIndex) {
      case 0:
        return _buildHomeContent();
      case 1:
        return const SearchScreen();
      case 2:
        return const FavoritesScreen();
      case 3:
        return const BookingHistoryScreen();
      case 4:
        return const ProfileScreen();
      default:
        return _buildHomeContent();
    }
  }

  // ────────────── HOME CONTENT ──────────────
  Widget _buildHomeContent() {
    return RefreshIndicator(
      onRefresh: _loadData,
      child: CustomScrollView(
        slivers: [
          // ── Gradient Header ──
          SliverAppBar(
            expandedHeight: 190,
            floating: false,
            pinned: true,
            backgroundColor: const Color(0xFF1565C0),
            elevation: 0,
            flexibleSpace: FlexibleSpaceBar(
              background: Container(
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [
                      Color(0xFF1565C0),
                      Color(0xFF0D47A1),
                      Color(0xFF1A237E),
                    ],
                  ),
                ),
                child: SafeArea(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(20, 12, 20, 20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.all(10),
                              decoration: BoxDecoration(
                                color: Colors.white.withOpacity(0.15),
                                borderRadius: BorderRadius.circular(14),
                                border: Border.all(
                                  color: Colors.white.withOpacity(0.2),
                                ),
                              ),
                              child: const Icon(
                                Icons.school_rounded,
                                color: Colors.white,
                                size: 26,
                              ),
                            ),
                            const SizedBox(width: 14),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  const Text(
                                    'SPK Rekomendasi',
                                    style: TextStyle(
                                      color: Colors.white,
                                      fontSize: 22,
                                      fontWeight: FontWeight.w800,
                                      letterSpacing: 0.3,
                                    ),
                                  ),
                                  const SizedBox(height: 3),
                                  Text(
                                    'Halo, ${_authService.currentUser?.name ?? "User"} 👋',
                                    style: TextStyle(
                                      color: Colors.white.withOpacity(0.85),
                                      fontSize: 14,
                                      fontWeight: FontWeight.w400,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            Container(
                              decoration: BoxDecoration(
                                color: Colors.white.withOpacity(0.12),
                                borderRadius: BorderRadius.circular(12),
                              ),
                              child: IconButton(
                                icon: const Icon(
                                  Icons.logout_rounded,
                                  color: Colors.white,
                                  size: 22,
                                ),
                                onPressed: _handleLogout,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 18),
                        GestureDetector(
                          onTap: () => setState(() => _selectedIndex = 1),
                          child: Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 16,
                              vertical: 13,
                            ),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(14),
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.black.withOpacity(0.08),
                                  blurRadius: 12,
                                  offset: const Offset(0, 4),
                                ),
                              ],
                            ),
                            child: Row(
                              children: [
                                Icon(
                                  Icons.search_rounded,
                                  color: Colors.grey[400],
                                  size: 22,
                                ),
                                const SizedBox(width: 12),
                                Text(
                                  'Cari kontrakan atau laundry...',
                                  style: TextStyle(
                                    color: Colors.grey[400],
                                    fontSize: 14,
                                    fontWeight: FontWeight.w400,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ),

          // ── Recommendation CTA Banner ──
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(20, 24, 20, 8),
              child: Container(
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [Color(0xFF667EEA), Color(0xFF764BA2)],
                  ),
                  borderRadius: BorderRadius.circular(18),
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFF667EEA).withOpacity(0.3),
                      blurRadius: 16,
                      offset: const Offset(0, 6),
                    ),
                  ],
                ),
                child: Material(
                  color: Colors.transparent,
                  child: InkWell(
                    onTap: () => _showRecommendationChoice(),
                    borderRadius: BorderRadius.circular(18),
                    child: Padding(
                      padding: const EdgeInsets.all(20),
                      child: Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(14),
                            decoration: BoxDecoration(
                              color: Colors.white.withOpacity(0.2),
                              borderRadius: BorderRadius.circular(14),
                            ),
                            child: const Icon(
                              Icons.auto_awesome_rounded,
                              color: Colors.white,
                              size: 28,
                            ),
                          ),
                          const SizedBox(width: 16),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Text(
                                  'Cari Rekomendasi Terbaik',
                                  style: TextStyle(
                                    fontSize: 17,
                                    fontWeight: FontWeight.w700,
                                    color: Colors.white,
                                    letterSpacing: 0.2,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  'Atur preferensi harga, jarak, & fasilitas — sistem SAW akan meranking pilihan terbaik untuk Anda',
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: Colors.white.withOpacity(0.85),
                                    height: 1.4,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(width: 8),
                          Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: Colors.white.withOpacity(0.2),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: const Icon(
                              Icons.arrow_forward_rounded,
                              color: Colors.white,
                              size: 18,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),

          // ── Two Service Tiles (Integrated) ──
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(20, 16, 20, 8),
              child: Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.06),
                      blurRadius: 12,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Row(
                  children: [
                    Expanded(
                      child: _buildServiceTileIntegrated(
                        icon: Icons.home_work_rounded,
                        label: 'Rekomendasi\nKontrakan',
                        color: const Color(0xFF1565C0),
                        bgColor: const Color(0xFFE3F2FD),
                        isLast: false,
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => const RecommendationScreen(category: 'kontrakan'),
                            ),
                          );
                        },
                      ),
                    ),
                    Container(width: 1, height: 80, color: Colors.grey[200]),
                    Expanded(
                      child: _buildServiceTileIntegrated(
                        icon: Icons.local_laundry_service_rounded,
                        label: 'Rekomendasi\nLaundry',
                        color: const Color(0xFF00897B),
                        bgColor: const Color(0xFFE0F2F1),
                        isLast: true,
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => const RecommendationScreen(category: 'laundry'),
                            ),
                          );
                        },
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),

          // ── Kontrakan Section ──
          SliverToBoxAdapter(
            child: _buildSectionHeader(
              title: 'Kontrakan Tersedia',
              icon: Icons.home_work_rounded,
              iconBgColor: const Color(0xFFE3F2FD),
              iconColor: const Color(0xFF1565C0),
              onSeeAll: () => setState(() => _selectedIndex = 1),
            ),
          ),

          SliverToBoxAdapter(
            child: _isLoading
                ? const Padding(
                    padding: EdgeInsets.all(32),
                    child: Center(child: CircularProgressIndicator()),
                  )
                : _kontrakanList.isEmpty
                    ? _buildEmptyState('Belum ada data kontrakan', Icons.home_work_outlined)
                    : SizedBox(
                        height: 250,
                        child: ListView.builder(
                          scrollDirection: Axis.horizontal,
                          padding: const EdgeInsets.fromLTRB(20, 0, 8, 0),
                          itemCount: _kontrakanList.length,
                          itemBuilder: (context, index) {
                            return _buildKontrakanCard(_kontrakanList[index]);
                          },
                        ),
                      ),
          ),

          // ── Laundry Section ──
          SliverToBoxAdapter(
            child: _buildSectionHeader(
              title: 'Laundry Terdekat',
              icon: Icons.local_laundry_service_rounded,
              iconBgColor: const Color(0xFFE0F2F1),
              iconColor: const Color(0xFF00897B),
              onSeeAll: () => setState(() => _selectedIndex = 1),
            ),
          ),

          SliverToBoxAdapter(
            child: _isLoading
                ? const Padding(
                    padding: EdgeInsets.all(32),
                    child: Center(child: CircularProgressIndicator()),
                  )
                : _laundryList.isEmpty
                    ? _buildEmptyState('Belum ada data laundry', Icons.local_laundry_service_outlined)
                    : SizedBox(
                        height: 240,
                        child: ListView.builder(
                          scrollDirection: Axis.horizontal,
                          padding: const EdgeInsets.fromLTRB(20, 0, 8, 0),
                          itemCount: _laundryList.length,
                          itemBuilder: (context, index) {
                            return _buildLaundryCard(_laundryList[index]);
                          },
                        ),
                      ),
          ),

          // ── Quick Actions ──
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(20, 20, 20, 8),
              child: Text(
                'Akses Cepat',
                style: TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.w700,
                  color: Colors.grey[700],
                  letterSpacing: 0.2,
                ),
              ),
            ),
          ),

          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(20, 4, 20, 0),
              child: Row(
                children: [
                  Expanded(
                    child: _buildQuickAction(
                      icon: Icons.search_rounded,
                      label: 'Cari Semua',
                      color: const Color(0xFF5C6BC0),
                      onTap: () => setState(() => _selectedIndex = 1),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: _buildQuickAction(
                      icon: Icons.favorite_rounded,
                      label: 'Favorit',
                      color: const Color(0xFFE53935),
                      onTap: () => setState(() => _selectedIndex = 2),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: _buildQuickAction(
                      icon: Icons.receipt_long_rounded,
                      label: 'Booking',
                      color: const Color(0xFFEF6C00),
                      onTap: () => setState(() => _selectedIndex = 3),
                    ),
                  ),
                ],
              ),
            ),
          ),

          const SliverToBoxAdapter(child: SizedBox(height: 100)),
        ],
      ),
    );
  }

  // ────────────── RECOMMENDATION CHOICE ──────────────
  void _showRecommendationChoice() {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const SizedBox(height: 12),
            Container(
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: Colors.grey[300],
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            const SizedBox(height: 24),
            const Text(
              'Cari Rekomendasi',
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.w700,
                color: Color(0xFF1A1A2E),
              ),
            ),
            const SizedBox(height: 6),
            Text(
              'Pilih jenis layanan yang ingin Anda cari',
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey[500],
              ),
            ),
            const SizedBox(height: 24),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              child: Row(
                children: [
                  Expanded(
                    child: _buildChoiceCard(
                      icon: Icons.home_work_rounded,
                      label: 'Kontrakan',
                      description: 'Cari kontrakan terbaik sesuai budget dan kebutuhan',
                      color: const Color(0xFF1565C0),
                      bgColor: const Color(0xFFE3F2FD),
                      onTap: () {
                        Navigator.pop(context);
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => const RecommendationScreen(category: 'kontrakan'),
                          ),
                        );
                      },
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: _buildChoiceCard(
                      icon: Icons.local_laundry_service_rounded,
                      label: 'Laundry',
                      description: 'Cari laundry terbaik dari harga dan kecepatan',
                      color: const Color(0xFF00897B),
                      bgColor: const Color(0xFFE0F2F1),
                      onTap: () {
                        Navigator.pop(context);
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => const RecommendationScreen(category: 'laundry'),
                          ),
                        );
                      },
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 32),
          ],
        ),
      ),
    );
  }

  // ────────────── CHOICE CARD ──────────────
  Widget _buildChoiceCard({
    required IconData icon,
    required String label,
    required String description,
    required Color color,
    required Color bgColor,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: bgColor.withOpacity(0.5),
          borderRadius: BorderRadius.circular(18),
          border: Border.all(color: color.withOpacity(0.15)),
        ),
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: color, size: 30),
            ),
            const SizedBox(height: 14),
            Text(
              label,
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w700,
                color: color,
              ),
            ),
            const SizedBox(height: 6),
            Text(
              description,
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 11,
                color: Colors.grey[600],
                height: 1.4,
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ────────────── SERVICE TILE ──────────────
  Widget _buildServiceTile({
    required IconData icon,
    required String label,
    required Color color,
    required Color bgColor,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: color.withOpacity(0.12)),
          boxShadow: [
            BoxShadow(
              color: color.withOpacity(0.08),
              blurRadius: 12,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: bgColor,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(icon, color: color, size: 22),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                label,
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w700,
                  color: color,
                  height: 1.3,
                ),
              ),
            ),
            Icon(Icons.arrow_forward_ios_rounded, size: 14, color: color.withOpacity(0.5)),
          ],
        ),
      ),
    );
  }

  // ────────────── SERVICE TILE INTEGRATED ──────────────
  Widget _buildServiceTileIntegrated({
    required IconData icon,
    required String label,
    required Color color,
    required Color bgColor,
    required bool isLast,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 16),
        decoration: BoxDecoration(
          borderRadius: isLast
              ? const BorderRadius.only(topRight: Radius.circular(20), bottomRight: Radius.circular(20))
              : const BorderRadius.only(topLeft: Radius.circular(20), bottomLeft: Radius.circular(20)),
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: bgColor,
                borderRadius: BorderRadius.circular(14),
              ),
              child: Icon(icon, color: color, size: 28),
            ),
            const SizedBox(height: 12),
            Text(
              label,
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w600,
                color: color,
                height: 1.3,
              ),
            ),
            const SizedBox(height: 8),
            Icon(Icons.arrow_forward_rounded, size: 16, color: color.withOpacity(0.6)),
          ],
        ),
      ),
    );
  }

  // ────────────── SECTION HEADER ──────────────
  Widget _buildSectionHeader({
    required String title,
    required IconData icon,
    required Color iconBgColor,
    required Color iconColor,
    required VoidCallback onSeeAll,
  }) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 24, 16, 14),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: iconBgColor,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: iconColor, size: 20),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              title,
              style: const TextStyle(
                fontSize: 17,
                fontWeight: FontWeight.w700,
                color: Color(0xFF1A1A2E),
                letterSpacing: 0.2,
              ),
            ),
          ),
          TextButton(
            onPressed: onSeeAll,
            style: TextButton.styleFrom(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  'Semua',
                  style: TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                    color: iconColor,
                  ),
                ),
                const SizedBox(width: 2),
                Icon(Icons.arrow_forward_ios, size: 12, color: iconColor),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // ────────────── KONTRAKAN CARD (plain, no ranking) ──────────────
  Widget _buildKontrakanCard(Kontrakan kontrakan) {
    return GestureDetector(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => KontrakanDetailScreen(kontrakan: kontrakan),
          ),
        );
      },
      child: Container(
        width: 200,
        margin: const EdgeInsets.only(right: 14, bottom: 8),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.06),
              blurRadius: 12,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Image
            Stack(
              children: [
                ClipRRect(
                  borderRadius: const BorderRadius.vertical(top: Radius.circular(16)),
                  child: CachedNetworkImage(
                    imageUrl: kontrakan.primaryPhoto,
                    height: 120,
                    width: 200,
                    fit: BoxFit.cover,
                    placeholder: (_, __) => Container(
                      height: 120,
                      width: 200,
                      color: const Color(0xFFE3F2FD),
                      child: const Center(
                        child: Icon(Icons.home_work_rounded, size: 36, color: Color(0xFF90CAF9)),
                      ),
                    ),
                    errorWidget: (_, __, ___) => Container(
                      height: 120,
                      width: 200,
                      color: const Color(0xFFE3F2FD),
                      child: const Center(
                        child: Icon(Icons.home_work_rounded, size: 36, color: Color(0xFF90CAF9)),
                      ),
                    ),
                  ),
                ),
                // Status badge
                Positioned(
                  top: 8,
                  right: 8,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: kontrakan.isAvailable
                          ? const Color(0xFF2E7D32)
                          : const Color(0xFFF57C00),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      kontrakan.isAvailable ? 'Tersedia' : 'Penuh',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 10,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                ),
                // Favorite heart
                Positioned(
                  top: 8,
                  left: 8,
                  child: GestureDetector(
                    onTap: () => _toggleKontrakanFav(kontrakan.id),
                    child: Container(
                      padding: const EdgeInsets.all(6),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.9),
                        shape: BoxShape.circle,
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.1),
                            blurRadius: 4,
                          ),
                        ],
                      ),
                      child: Icon(
                        _favKontrakanIds.contains(kontrakan.id)
                            ? Icons.favorite_rounded
                            : Icons.favorite_border_rounded,
                        size: 16,
                        color: _favKontrakanIds.contains(kontrakan.id)
                            ? Colors.red
                            : Colors.grey[600],
                      ),
                    ),
                  ),
                ),
              ],
            ),
            // Info
            Padding(
              padding: const EdgeInsets.fromLTRB(12, 10, 12, 12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    kontrakan.nama,
                    style: const TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w700,
                      color: Color(0xFF1A1A2E),
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      Icon(Icons.location_on_rounded, size: 12, color: Colors.grey[400]),
                      const SizedBox(width: 3),
                      Expanded(
                        child: Text(
                          kontrakan.alamat,
                          style: TextStyle(fontSize: 11, color: Colors.grey[500]),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Expanded(
                        child: Row(
                          children: [
                            Flexible(
                              child: Text(
                                kontrakan.formattedHarga,
                                style: const TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w700,
                                  color: Color(0xFF1565C0),
                                ),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                            Text(
                              '/bln',
                              style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w500,
                                color: Colors.grey[500],
                              ),
                            ),
                          ],
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                        decoration: BoxDecoration(
                          color: const Color(0xFFE3F2FD),
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            const Icon(Icons.bed_rounded, size: 12, color: Color(0xFF1565C0)),
                            const SizedBox(width: 3),
                            Text(
                              '${kontrakan.jumlahKamar}',
                              style: const TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w600,
                                color: Color(0xFF1565C0),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 6),
                  Row(
                    children: [
                      Icon(Icons.directions_walk_rounded, size: 12, color: Colors.grey[400]),
                      const SizedBox(width: 3),
                      Text(
                        '${kontrakan.jarakKampus.toStringAsFixed(1)} km dari kampus',
                        style: TextStyle(fontSize: 10, color: Colors.grey[500]),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ────────────── LAUNDRY CARD (plain, no ranking) ──────────────
  Widget _buildLaundryCard(Laundry laundry) {
    return GestureDetector(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => LaundryDetailScreen(laundry: laundry),
          ),
        );
      },
      child: Container(
        width: 200,
        margin: const EdgeInsets.only(right: 14, bottom: 8),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.06),
              blurRadius: 12,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Gradient header
            Stack(
              children: [
                ClipRRect(
                  borderRadius: const BorderRadius.vertical(top: Radius.circular(16)),
                  child: Container(
                    height: 110,
                    width: 200,
                    decoration: const BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                        colors: [Color(0xFF26A69A), Color(0xFF00897B)],
                      ),
                    ),
                    child: const Center(
                      child: Icon(
                        Icons.local_laundry_service_rounded,
                        size: 40,
                        color: Colors.white70,
                      ),
                    ),
                  ),
                ),
                // Status badge
                Positioned(
                  top: 8,
                  right: 8,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: laundry.status == 'buka'
                          ? const Color(0xFF2E7D32)
                          : const Color(0xFFC62828),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      laundry.statusText,
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 10,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                ),
                // Favorite heart
                Positioned(
                  top: 8,
                  left: 8,
                  child: GestureDetector(
                    onTap: () => _toggleLaundryFav(laundry.id),
                    child: Container(
                      padding: const EdgeInsets.all(6),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.9),
                        shape: BoxShape.circle,
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.1),
                            blurRadius: 4,
                          ),
                        ],
                      ),
                      child: Icon(
                        _favLaundryIds.contains(laundry.id)
                            ? Icons.favorite_rounded
                            : Icons.favorite_border_rounded,
                        size: 16,
                        color: _favLaundryIds.contains(laundry.id)
                            ? Colors.red
                            : Colors.grey[600],
                      ),
                    ),
                  ),
                ),
              ],
            ),
            // Info
            Padding(
              padding: const EdgeInsets.fromLTRB(12, 10, 12, 12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    laundry.nama,
                    style: const TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w700,
                      color: Color(0xFF1A1A2E),
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      Icon(Icons.location_on_rounded, size: 12, color: Colors.grey[400]),
                      const SizedBox(width: 3),
                      Expanded(
                        child: Text(
                          laundry.alamat,
                          style: TextStyle(fontSize: 11, color: Colors.grey[500]),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Expanded(
                        child: Row(
                          children: [
                            Flexible(
                              child: Text(
                                laundry.formattedHarga,
                                style: const TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w700,
                                  color: Color(0xFF00897B),
                                ),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                            Text(
                              '/kg',
                              style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w500,
                                color: Colors.grey[500],
                              ),
                            ),
                          ],
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                        decoration: BoxDecoration(
                          color: const Color(0xFFE0F2F1),
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            const Icon(Icons.schedule_rounded, size: 12, color: Color(0xFF00897B)),
                            const SizedBox(width: 3),
                            Text(
                              '${laundry.waktuProses}h',
                              style: const TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w600,
                                color: Color(0xFF00897B),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 6),
                  Row(
                    children: [
                      Icon(Icons.access_time_rounded, size: 12, color: Colors.grey[400]),
                      const SizedBox(width: 3),
                      Text(
                        '${laundry.jamBuka} - ${laundry.jamTutup}',
                        style: TextStyle(fontSize: 10, color: Colors.grey[500]),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ────────────── QUICK ACTION BUTTON ──────────────
  Widget _buildQuickAction({
    required IconData icon,
    required String label,
    required Color color,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: const Color(0xFFEEEEEE)),
        ),
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: color, size: 22),
            ),
            const SizedBox(height: 8),
            Text(
              label,
              style: TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.w600,
                color: Colors.grey[700],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ────────────── EMPTY STATE ──────────────
  Widget _buildEmptyState(String message, IconData icon) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 24, horizontal: 20),
      child: Center(
        child: Column(
          children: [
            Icon(icon, size: 48, color: Colors.grey[300]),
            const SizedBox(height: 8),
            Text(message, style: TextStyle(fontSize: 13, color: Colors.grey[500])),
          ],
        ),
      ),
    );
  }

  // ────────────── BOTTOM NAV ──────────────
  Widget _buildBottomNav() {
    const items = [
      _NavItem(Icons.home_outlined, Icons.home_rounded, 'Beranda'),
      _NavItem(Icons.search_outlined, Icons.search_rounded, 'Cari'),
      _NavItem(Icons.favorite_outline_rounded, Icons.favorite_rounded, 'Favorit'),
      _NavItem(Icons.bookmark_outline_rounded, Icons.bookmark_rounded, 'Booking'),
      _NavItem(Icons.person_outline_rounded, Icons.person_rounded, 'Profil'),
    ];

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.06),
            blurRadius: 20,
            offset: const Offset(0, -4),
          ),
        ],
      ),
      child: SafeArea(
        top: false,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(8, 8, 8, 4),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: List.generate(items.length, (i) {
              final isSelected = _selectedIndex == i;
              return Expanded(
                child: GestureDetector(
                  onTap: () => setState(() => _selectedIndex = i),
                  behavior: HitTestBehavior.opaque,
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 250),
                    curve: Curves.easeInOut,
                    padding: const EdgeInsets.symmetric(vertical: 6),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        AnimatedContainer(
                          duration: const Duration(milliseconds: 250),
                          padding: EdgeInsets.symmetric(
                            horizontal: isSelected ? 20 : 12,
                            vertical: 6,
                          ),
                          decoration: BoxDecoration(
                            color: isSelected
                                ? const Color(0xFF1565C0).withOpacity(0.1)
                                : Colors.transparent,
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: Icon(
                            isSelected ? items[i].activeIcon : items[i].icon,
                            color: isSelected
                                ? const Color(0xFF1565C0)
                                : const Color(0xFF9E9E9E),
                            size: 24,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          items[i].label,
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: isSelected ? FontWeight.w600 : FontWeight.w400,
                            color: isSelected
                                ? const Color(0xFF1565C0)
                                : const Color(0xFF9E9E9E),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              );
            }),
          ),
        ),
      ),
    );
  }

  Future<void> _handleLogout() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text('Konfirmasi Logout'),
        content: const Text('Apakah Anda yakin ingin keluar?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red,
              foregroundColor: Colors.white,
            ),
            child: const Text('Logout'),
          ),
        ],
      ),
    );

    if (confirm == true) {
      await _authService.logout();
      if (mounted) {
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(builder: (context) => const LoginScreen()),
          (route) => false,
        );
      }
    }
  }
}

class _NavItem {
  final IconData icon;
  final IconData activeIcon;
  final String label;
  const _NavItem(this.icon, this.activeIcon, this.label);
}
