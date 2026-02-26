import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter_cache_manager/flutter_cache_manager.dart';
import '../models/kontrakan.dart';
import '../models/laundry.dart';
import '../services/favorite_service.dart';
import 'kontrakan_detail_screen.dart';
import 'laundry_detail_screen.dart';

class FavoritesScreen extends StatefulWidget {
  const FavoritesScreen({super.key});

  @override
  State<FavoritesScreen> createState() => _FavoritesScreenState();
}

class _FavoritesScreenState extends State<FavoritesScreen>
    with SingleTickerProviderStateMixin {
  final _favoriteService = FavoriteService();
  late TabController _tabController;

  List<Kontrakan> _kontrakanFavorites = [];
  List<Laundry> _laundryFavorites = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _loadFavorites();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadFavorites() async {
    setState(() => _isLoading = true);
    // Clear image cache agar foto terbaru selalu dimuat
    await DefaultCacheManager().emptyCache();
    PaintingBinding.instance.imageCache.clear();
    PaintingBinding.instance.imageCache.clearLiveImages();
    try {
      final result = await _favoriteService.getFavoritesWithModels();
      if (mounted) {
        setState(() {
          _kontrakanFavorites = (result['kontrakan'] as List<Kontrakan>?) ?? [];
          _laundryFavorites = (result['laundry'] as List<Laundry>?) ?? [];
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _removeFavorite(String type, int itemId) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Row(
          children: [
            Icon(Icons.favorite_rounded, color: Colors.red.shade400, size: 24),
            const SizedBox(width: 10),
            const Text(
              'Hapus Favorit',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.w600),
            ),
          ],
        ),
        content: const Text('Hapus dari daftar favorit Anda?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: Text('Batal', style: TextStyle(color: Colors.grey[600])),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(ctx, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red.shade500,
              foregroundColor: Colors.white,
              elevation: 0,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(10),
              ),
            ),
            child: const Text('Hapus'),
          ),
        ],
      ),
    );
    if (confirm != true) return;

    Map<String, dynamic> result;
    if (type == 'kontrakan') {
      result = await _favoriteService.toggleKontrakanFavorite(itemId);
    } else {
      result = await _favoriteService.toggleLaundryFavorite(itemId);
    }

    if (mounted && result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Row(
            children: [
              const Icon(
                Icons.check_circle_rounded,
                color: Colors.white,
                size: 20,
              ),
              const SizedBox(width: 10),
              Text(result['message'] ?? 'Dihapus dari favorit'),
            ],
          ),
          backgroundColor: const Color(0xFF2E7D32),
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          margin: const EdgeInsets.all(16),
        ),
      );
      _loadFavorites();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF7F8FC),
      body: SafeArea(
        child: Column(
          children: [
            // ── Header ──
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
                          Icons.favorite_rounded,
                          color: Colors.white,
                          size: 24,
                        ),
                      ),
                      const SizedBox(width: 14),
                      const Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Favorit Saya',
                              style: TextStyle(
                                fontSize: 22,
                                fontWeight: FontWeight.w700,
                                color: Colors.white,
                                letterSpacing: 0.3,
                              ),
                            ),
                            SizedBox(height: 2),
                            Text(
                              'Simpan pilihan terbaik Anda',
                              style: TextStyle(
                                fontSize: 13,
                                color: Colors.white70,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),
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
                      tabs: [
                        Tab(
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              const Icon(Icons.home_work_rounded, size: 18),
                              const SizedBox(width: 6),
                              Text('Kontrakan (${_kontrakanFavorites.length})'),
                            ],
                          ),
                        ),
                        Tab(
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              const Icon(
                                Icons.local_laundry_service_rounded,
                                size: 18,
                              ),
                              const SizedBox(width: 6),
                              Text('Laundry (${_laundryFavorites.length})'),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),

            // ── Content ──
            Expanded(
              child: _isLoading
                  ? const Center(
                      child: CircularProgressIndicator(
                        color: Color(0xFF1565C0),
                      ),
                    )
                  : TabBarView(
                      controller: _tabController,
                      children: [_buildKontrakanList(), _buildLaundryList()],
                    ),
            ),
          ],
        ),
      ),
    );
  }

  // ────────────── KONTRAKAN FAVORITES ──────────────
  Widget _buildKontrakanList() {
    if (_kontrakanFavorites.isEmpty) {
      return _buildEmptyState(
        icon: Icons.home_work_outlined,
        title: 'Belum ada kontrakan favorit',
        subtitle:
            'Tambahkan kontrakan ke favorit dari halaman detail\natau setelah mendapat hasil rekomendasi',
      );
    }

    return RefreshIndicator(
      onRefresh: _loadFavorites,
      child: ListView.builder(
        padding: const EdgeInsets.fromLTRB(20, 16, 20, 16),
        itemCount: _kontrakanFavorites.length,
        itemBuilder: (context, index) {
          return _buildKontrakanFavCard(_kontrakanFavorites[index]);
        },
      ),
    );
  }

  Widget _buildKontrakanFavCard(Kontrakan kontrakan) {
    return GestureDetector(
      onTap: () async {
        await Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => KontrakanDetailScreen(kontrakan: kontrakan),
          ),
        );
        _loadFavorites(); // Refresh after returning from detail
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(18),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 14,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Row(
          children: [
            // Image
            ClipRRect(
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(18),
                bottomLeft: Radius.circular(18),
              ),
              child: CachedNetworkImage(
                imageUrl: kontrakan.primaryPhoto,
                width: 110,
                height: 120,
                fit: BoxFit.cover,
                placeholder: (_, __) => Container(
                  width: 110,
                  height: 120,
                  color: const Color(0xFFE3F2FD),
                  child: const Center(
                    child: Icon(
                      Icons.home_work_rounded,
                      color: Color(0xFF90CAF9),
                      size: 32,
                    ),
                  ),
                ),
                errorWidget: (_, __, ___) => Container(
                  width: 110,
                  height: 120,
                  color: const Color(0xFFE3F2FD),
                  child: const Center(
                    child: Icon(
                      Icons.home_work_rounded,
                      color: Color(0xFF90CAF9),
                      size: 32,
                    ),
                  ),
                ),
              ),
            ),
            // Info
            Expanded(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(14, 12, 8, 12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      kontrakan.nama,
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w700,
                        color: Color(0xFF1A1A2E),
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        Icon(
                          Icons.location_on_rounded,
                          size: 13,
                          color: Colors.grey[400],
                        ),
                        const SizedBox(width: 3),
                        Expanded(
                          child: Text(
                            kontrakan.alamat,
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.grey[500],
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Row(
                      children: [
                        Text(
                          kontrakan.formattedHarga,
                          style: const TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w800,
                            color: Color(0xFF1565C0),
                          ),
                        ),
                        Text(
                          '/bln',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w500,
                            color: Colors.grey[500],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 6),
                    Row(
                      children: [
                        _buildInfoChip(
                          Icons.bed_rounded,
                          '${kontrakan.jumlahKamar} Kamar',
                          const Color(0xFF1565C0),
                        ),
                        const SizedBox(width: 8),
                        _buildInfoChip(
                          Icons.directions_walk_rounded,
                          '${kontrakan.jarakKampus.toStringAsFixed(1)} km',
                          const Color(0xFF5C6BC0),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
            // Favorite button
            Column(
              children: [
                IconButton(
                  onPressed: () => _removeFavorite('kontrakan', kontrakan.id),
                  icon: const Icon(
                    Icons.favorite_rounded,
                    color: Colors.red,
                    size: 24,
                  ),
                  splashRadius: 20,
                ),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 8,
                    vertical: 3,
                  ),
                  decoration: BoxDecoration(
                    color: kontrakan.isAvailable
                        ? const Color(0xFFE8F5E9)
                        : const Color(0xFFFFF3E0),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    kontrakan.isAvailable ? 'Tersedia' : 'Penuh',
                    style: TextStyle(
                      fontSize: 10,
                      fontWeight: FontWeight.w600,
                      color: kontrakan.isAvailable
                          ? const Color(0xFF2E7D32)
                          : const Color(0xFFF57C00),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(width: 8),
          ],
        ),
      ),
    );
  }

  // ────────────── LAUNDRY FAVORITES ──────────────
  Widget _buildLaundryList() {
    if (_laundryFavorites.isEmpty) {
      return _buildEmptyState(
        icon: Icons.local_laundry_service_outlined,
        title: 'Belum ada laundry favorit',
        subtitle:
            'Tambahkan laundry ke favorit dari halaman detail\natau setelah mendapat hasil rekomendasi',
      );
    }

    return RefreshIndicator(
      onRefresh: _loadFavorites,
      child: ListView.builder(
        padding: const EdgeInsets.fromLTRB(20, 16, 20, 16),
        itemCount: _laundryFavorites.length,
        itemBuilder: (context, index) {
          return _buildLaundryFavCard(_laundryFavorites[index]);
        },
      ),
    );
  }

  Widget _buildLaundryFavCard(Laundry laundry) {
    return GestureDetector(
      onTap: () async {
        await Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => LaundryDetailScreen(laundry: laundry),
          ),
        );
        _loadFavorites();
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(18),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 14,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Row(
          children: [
            // Gradient icon placeholder
            ClipRRect(
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(18),
                bottomLeft: Radius.circular(18),
              ),
              child: Container(
                width: 110,
                height: 120,
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
                    size: 36,
                    color: Colors.white70,
                  ),
                ),
              ),
            ),
            // Info
            Expanded(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(14, 12, 8, 12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      laundry.nama,
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w700,
                        color: Color(0xFF1A1A2E),
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        Icon(
                          Icons.location_on_rounded,
                          size: 13,
                          color: Colors.grey[400],
                        ),
                        const SizedBox(width: 3),
                        Expanded(
                          child: Text(
                            laundry.alamat,
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.grey[500],
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Row(
                      children: [
                        Text(
                          laundry.formattedHarga,
                          style: const TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w800,
                            color: Color(0xFF00897B),
                          ),
                        ),
                        Text(
                          '/kg',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w500,
                            color: Colors.grey[500],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 6),
                    Row(
                      children: [
                        _buildInfoChip(
                          Icons.schedule_rounded,
                          '${laundry.waktuProses}h proses',
                          const Color(0xFF00897B),
                        ),
                        const SizedBox(width: 8),
                        _buildInfoChip(
                          Icons.access_time_rounded,
                          '${laundry.jamBuka}-${laundry.jamTutup}',
                          const Color(0xFF5C6BC0),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
            // Favorite button
            Column(
              children: [
                IconButton(
                  onPressed: () => _removeFavorite('laundry', laundry.id),
                  icon: const Icon(
                    Icons.favorite_rounded,
                    color: Colors.red,
                    size: 24,
                  ),
                  splashRadius: 20,
                ),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 8,
                    vertical: 3,
                  ),
                  decoration: BoxDecoration(
                    color: laundry.status == 'buka'
                        ? const Color(0xFFE8F5E9)
                        : const Color(0xFFFFEBEE),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    laundry.statusText,
                    style: TextStyle(
                      fontSize: 10,
                      fontWeight: FontWeight.w600,
                      color: laundry.status == 'buka'
                          ? const Color(0xFF2E7D32)
                          : const Color(0xFFC62828),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(width: 8),
          ],
        ),
      ),
    );
  }

  // ────────────── HELPERS ──────────────
  Widget _buildInfoChip(IconData icon, String label, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: color.withOpacity(0.08),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 12, color: color),
          const SizedBox(width: 4),
          Text(
            label,
            style: TextStyle(
              fontSize: 10,
              fontWeight: FontWeight.w600,
              color: color,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState({
    required IconData icon,
    required String title,
    required String subtitle,
  }) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(40),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(28),
              decoration: BoxDecoration(
                color: const Color(0xFF1565C0).withOpacity(0.06),
                shape: BoxShape.circle,
              ),
              child: Icon(
                icon,
                size: 56,
                color: const Color(0xFF1565C0).withOpacity(0.3),
              ),
            ),
            const SizedBox(height: 24),
            Text(
              title,
              style: const TextStyle(
                fontSize: 17,
                fontWeight: FontWeight.w600,
                color: Color(0xFF1A1A2E),
              ),
            ),
            const SizedBox(height: 8),
            Text(
              subtitle,
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 13,
                color: Colors.grey[500],
                height: 1.5,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
