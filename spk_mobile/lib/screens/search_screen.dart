import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../models/kontrakan.dart';
import '../models/laundry.dart';
import '../services/kontrakan_service.dart';
import '../services/laundry_service.dart';
import 'kontrakan_detail_screen.dart';
import 'laundry_detail_screen.dart';

class SearchScreen extends StatefulWidget {
  const SearchScreen({super.key});

  @override
  State<SearchScreen> createState() => _SearchScreenState();
}

class _SearchScreenState extends State<SearchScreen> {
  final _kontrakanService = KontrakanService();
  final _laundryService = LaundryService();
  final _searchController = TextEditingController();

  List<Kontrakan> _allKontrakan = [];
  List<Kontrakan> _filteredKontrakan = [];
  List<Laundry> _allLaundry = [];
  List<Laundry> _filteredLaundry = [];
  bool _isLoading = true;
  String _selectedCategory = 'Kontrakan';
  String _selectedFilter = 'Semua';
  RangeValues _priceRange = const RangeValues(0, 20000000);

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);
    final kontrakan = await _kontrakanService.getKontrakan();
    final laundry = await _laundryService.getLaundry();
    setState(() {
      _allKontrakan = kontrakan;
      _filteredKontrakan = kontrakan;
      _allLaundry = laundry;
      _filteredLaundry = laundry;
      _isLoading = false;
    });
  }

  void _filterKontrakan(String query) {
    if (_selectedCategory == 'Kontrakan') {
      setState(() {
        if (query.isEmpty) {
          _filteredKontrakan = _allKontrakan;
        } else {
          _filteredKontrakan = _allKontrakan.where((kontrakan) {
            return kontrakan.nama.toLowerCase().contains(query.toLowerCase()) ||
                kontrakan.alamat.toLowerCase().contains(query.toLowerCase());
          }).toList();
        }
        _applyFilters();
      });
    } else {
      setState(() {
        if (query.isEmpty) {
          _filteredLaundry = _allLaundry;
        } else {
          _filteredLaundry = _allLaundry.where((laundry) {
            return laundry.nama.toLowerCase().contains(query.toLowerCase()) ||
                laundry.alamat.toLowerCase().contains(query.toLowerCase());
          }).toList();
        }
        _applyLaundryFilters();
      });
    }
  }

  void _applyFilters() {
    var filtered = List<Kontrakan>.from(_allKontrakan);

    // Apply search
    if (_searchController.text.isNotEmpty) {
      filtered = filtered.where((k) {
        return k.nama.toLowerCase().contains(
              _searchController.text.toLowerCase(),
            ) ||
            k.alamat.toLowerCase().contains(
              _searchController.text.toLowerCase(),
            );
      }).toList();
    }

    // Filter by status
    if (_selectedFilter != 'Semua') {
      filtered = filtered.where((k) {
        if (_selectedFilter == 'Tersedia') return k.status == 'available';
        if (_selectedFilter == 'Penuh') return k.status == 'occupied';
        return true;
      }).toList();
    }

    // Filter by price
    filtered = filtered.where((k) {
      return k.harga >= _priceRange.start && k.harga <= _priceRange.end;
    }).toList();

    setState(() => _filteredKontrakan = filtered);
  }

  void _applyLaundryFilters() {
    var filtered = List<Laundry>.from(_allLaundry);

    // Apply search
    if (_searchController.text.isNotEmpty) {
      filtered = filtered.where((l) {
        return l.nama.toLowerCase().contains(
              _searchController.text.toLowerCase(),
            ) ||
            l.alamat.toLowerCase().contains(
              _searchController.text.toLowerCase(),
            );
      }).toList();
    }

    // Filter by price
    filtered = filtered.where((l) {
      final harga = l.hargaKiloan * 10; // Approximate for 10kg
      return harga >= _priceRange.start && harga <= _priceRange.end;
    }).toList();

    setState(() => _filteredLaundry = filtered);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      body: SafeArea(
        child: Column(
          children: [
            // Header with Search Bar
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF1565C0), Color(0xFF1976D2)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.1),
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Column(
                children: [
                  Row(
                    children: [
                      const Icon(Icons.search, color: Colors.white, size: 28),
                      const SizedBox(width: 12),
                      const Text(
                        'Cari & Jelajahi',
                        style: TextStyle(
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                      ),
                      const Spacer(),
                      IconButton(
                        icon: const Icon(Icons.tune, color: Colors.white),
                        onPressed: _showFilterDialog,
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),

                  // Category Tabs
                  Row(
                    children: [
                      Expanded(
                        child: GestureDetector(
                          onTap: () =>
                              setState(() => _selectedCategory = 'Kontrakan'),
                          child: Container(
                            padding: const EdgeInsets.symmetric(vertical: 12),
                            decoration: BoxDecoration(
                              color: _selectedCategory == 'Kontrakan'
                                  ? Colors.white
                                  : Colors.white.withValues(alpha: 0.2),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(
                                  Icons.home_work,
                                  color: _selectedCategory == 'Kontrakan'
                                      ? const Color(0xFF1565C0)
                                      : Colors.white,
                                  size: 20,
                                ),
                                const SizedBox(width: 8),
                                Text(
                                  'Kontrakan',
                                  style: TextStyle(
                                    fontSize: 16,
                                    fontWeight: FontWeight.bold,
                                    color: _selectedCategory == 'Kontrakan'
                                        ? const Color(0xFF1565C0)
                                        : Colors.white,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: GestureDetector(
                          onTap: () =>
                              setState(() => _selectedCategory = 'Laundry'),
                          child: Container(
                            padding: const EdgeInsets.symmetric(vertical: 12),
                            decoration: BoxDecoration(
                              color: _selectedCategory == 'Laundry'
                                  ? Colors.white
                                  : Colors.white.withValues(alpha: 0.2),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(
                                  Icons.local_laundry_service,
                                  color: _selectedCategory == 'Laundry'
                                      ? const Color(0xFF00BCD4)
                                      : Colors.white,
                                  size: 20,
                                ),
                                const SizedBox(width: 8),
                                Text(
                                  'Laundry',
                                  style: TextStyle(
                                    fontSize: 16,
                                    fontWeight: FontWeight.bold,
                                    color: _selectedCategory == 'Laundry'
                                        ? const Color(0xFF00BCD4)
                                        : Colors.white,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(height: 12),
                  TextField(
                    controller: _searchController,
                    onChanged: _filterKontrakan,
                    style: const TextStyle(fontSize: 16),
                    decoration: InputDecoration(
                      hintText: _selectedCategory == 'Kontrakan'
                          ? 'Cari kontrakan...'
                          : 'Cari laundry...',
                      prefixIcon: const Icon(Icons.search),
                      suffixIcon: _searchController.text.isNotEmpty
                          ? IconButton(
                              icon: const Icon(Icons.clear),
                              onPressed: () {
                                _searchController.clear();
                                _filterKontrakan('');
                              },
                            )
                          : null,
                      filled: true,
                      fillColor: Colors.white,
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: BorderSide.none,
                      ),
                      contentPadding: const EdgeInsets.symmetric(
                        horizontal: 16,
                        vertical: 14,
                      ),
                    ),
                  ),
                  const SizedBox(height: 12),
                  // Quick Filters (only for Kontrakan)
                  if (_selectedCategory == 'Kontrakan')
                    SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: Row(
                        children: [
                          _buildFilterChip('Semua'),
                          const SizedBox(width: 8),
                          _buildFilterChip('Tersedia'),
                          const SizedBox(width: 8),
                          _buildFilterChip('Penuh'),
                        ],
                      ),
                    ),
                ],
              ),
            ),

            // Results Count
            if (!_isLoading)
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 16,
                  vertical: 12,
                ),
                color: Colors.white,
                child: Row(
                  children: [
                    Icon(Icons.filter_list, size: 18, color: Colors.grey[600]),
                    const SizedBox(width: 8),
                    Text(
                      _selectedCategory == 'Kontrakan'
                          ? 'Ditemukan ${_filteredKontrakan.length} kontrakan'
                          : 'Ditemukan ${_filteredLaundry.length} laundry',
                      style: TextStyle(
                        fontSize: 14,
                        color: Colors.grey[700],
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ),

            // List
            Expanded(
              child: _isLoading
                  ? const Center(child: CircularProgressIndicator())
                  : _selectedCategory == 'Kontrakan'
                  ? _buildKontrakanList()
                  : _buildLaundryList(),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildKontrakanList() {
    if (_filteredKontrakan.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.search_off, size: 80, color: Colors.grey[300]),
            const SizedBox(height: 16),
            Text(
              'Tidak ada hasil',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: Colors.grey[600],
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Coba kata kunci lain',
              style: TextStyle(fontSize: 14, color: Colors.grey[500]),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _filteredKontrakan.length,
      itemBuilder: (context, index) {
        return _buildKontrakanItem(_filteredKontrakan[index]);
      },
    );
  }

  Widget _buildLaundryList() {
    if (_filteredLaundry.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.search_off, size: 80, color: Colors.grey[300]),
            const SizedBox(height: 16),
            Text(
              'Tidak ada hasil',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: Colors.grey[600],
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Coba kata kunci lain',
              style: TextStyle(fontSize: 14, color: Colors.grey[500]),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _filteredLaundry.length,
      itemBuilder: (context, index) {
        return _buildLaundryItem(_filteredLaundry[index]);
      },
    );
  }

  Widget _buildFilterChip(String label) {
    final isSelected = _selectedFilter == label;
    return GestureDetector(
      onTap: () {
        setState(() => _selectedFilter = label);
        _applyFilters();
      },
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        decoration: BoxDecoration(
          color: isSelected
              ? Colors.white
              : Colors.white.withValues(alpha: 0.3),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(
            color: isSelected
                ? Colors.white
                : Colors.white.withValues(alpha: 0.5),
            width: 2,
          ),
        ),
        child: Text(
          label,
          style: TextStyle(
            color: isSelected ? const Color(0xFF1565C0) : Colors.white,
            fontWeight: isSelected ? FontWeight.bold : FontWeight.w600,
            fontSize: 14,
          ),
        ),
      ),
    );
  }

  Widget _buildKontrakanItem(Kontrakan kontrakan) {
    return GestureDetector(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => KontrakanDetailScreen(kontrakan: kontrakan),
          ),
        );
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.08),
              blurRadius: 10,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Image
            ClipRRect(
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(16),
                bottomLeft: Radius.circular(16),
              ),
              child:
                  kontrakan.fotoUtama != null && kontrakan.fotoUtama!.isNotEmpty
                  ? CachedNetworkImage(
                      imageUrl:
                          'http://192.168.18.16:8000/storage/${kontrakan.fotoUtama}',
                      width: 120,
                      height: 140,
                      fit: BoxFit.cover,
                      placeholder: (context, url) => Container(
                        width: 120,
                        height: 140,
                        color: Colors.grey[200],
                        child: const Center(
                          child: CircularProgressIndicator(strokeWidth: 2),
                        ),
                      ),
                      errorWidget: (context, url, error) => Container(
                        width: 120,
                        height: 140,
                        color: Colors.grey[200],
                        child: Icon(
                          Icons.home_work,
                          size: 40,
                          color: Colors.grey[400],
                        ),
                      ),
                    )
                  : Container(
                      width: 120,
                      height: 140,
                      color: Colors.grey[200],
                      child: Icon(
                        Icons.home_work,
                        size: 40,
                        color: Colors.grey[400],
                      ),
                    ),
            ),
            // Info
            Expanded(
              child: Padding(
                padding: const EdgeInsets.all(12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Expanded(
                          child: Text(
                            kontrakan.nama,
                            style: const TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                              color: Colors.black87,
                            ),
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color: kontrakan.status == 'available'
                                ? Colors.green
                                : Colors.orange,
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Text(
                            kontrakan.status == 'available'
                                ? 'Tersedia'
                                : 'Penuh',
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 10,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 6),
                    Row(
                      children: [
                        Icon(
                          Icons.location_on,
                          size: 14,
                          color: Colors.grey[600],
                        ),
                        const SizedBox(width: 4),
                        Expanded(
                          child: Text(
                            kontrakan.alamat,
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.grey[600],
                            ),
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Row(
                      children: [
                        Icon(Icons.bed, size: 14, color: Colors.grey[600]),
                        const SizedBox(width: 4),
                        Text(
                          '${kontrakan.jumlahKamar} Kamar',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[700],
                          ),
                        ),
                        const SizedBox(width: 12),
                        Icon(Icons.near_me, size: 14, color: Colors.grey[600]),
                        const SizedBox(width: 4),
                        Text(
                          '${kontrakan.jarakKampus.toStringAsFixed(1)} km',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[700],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        vertical: 6,
                        horizontal: 10,
                      ),
                      decoration: BoxDecoration(
                        color: const Color(0xFF1565C0).withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        'Rp ${(kontrakan.harga / 1000).toStringAsFixed(0)}K/bln',
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          color: Color(0xFF1565C0),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLaundryItem(Laundry laundry) {
    return GestureDetector(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => LaundryDetailScreen(laundry: laundry),
          ),
        );
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.05),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Image/Icon
            ClipRRect(
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(16),
                bottomLeft: Radius.circular(16),
              ),
              child: Container(
                width: 120,
                height: 140,
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [Colors.cyan[400]!, Colors.cyan[600]!],
                  ),
                ),
                child: const Icon(
                  Icons.local_laundry_service,
                  size: 50,
                  color: Colors.white,
                ),
              ),
            ),
            // Info
            Expanded(
              child: Padding(
                padding: const EdgeInsets.all(12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      laundry.nama,
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                        color: Colors.black87,
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 6),
                    Row(
                      children: [
                        Icon(
                          Icons.location_on,
                          size: 14,
                          color: Colors.grey[600],
                        ),
                        const SizedBox(width: 4),
                        Expanded(
                          child: Text(
                            laundry.alamat,
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.grey[600],
                            ),
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Row(
                      children: [
                        Icon(
                          Icons.access_time,
                          size: 14,
                          color: Colors.grey[600],
                        ),
                        const SizedBox(width: 4),
                        Text(
                          '${laundry.estimasiSelesai}jam',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[700],
                          ),
                        ),
                        const SizedBox(width: 12),
                        Icon(Icons.near_me, size: 14, color: Colors.grey[600]),
                        const SizedBox(width: 4),
                        Text(
                          '${laundry.jarak.toStringAsFixed(1)} km',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[700],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        vertical: 6,
                        horizontal: 10,
                      ),
                      decoration: BoxDecoration(
                        color: const Color(0xFF00BCD4).withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        'Rp ${(laundry.hargaKiloan / 1000).toStringAsFixed(0)}K/kg',
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          color: Color(0xFF00BCD4),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _showFilterDialog() {
    showDialog(
      context: context,
      builder: (context) => StatefulBuilder(
        builder: (context, setDialogState) => AlertDialog(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
          title: const Text(
            'Filter Harga',
            style: TextStyle(fontWeight: FontWeight.bold),
          ),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                'Rp ${(_priceRange.start / 1000000).toStringAsFixed(1)}jt - Rp ${(_priceRange.end / 1000000).toStringAsFixed(1)}jt',
                style: const TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF1565C0),
                ),
              ),
              const SizedBox(height: 8),
              RangeSlider(
                values: _priceRange,
                min: 0,
                max: 20000000,
                divisions: 20,
                activeColor: const Color(0xFF1565C0),
                labels: RangeLabels(
                  'Rp ${(_priceRange.start / 1000).toStringAsFixed(0)}K',
                  'Rp ${(_priceRange.end / 1000).toStringAsFixed(0)}K',
                ),
                onChanged: (values) {
                  setDialogState(() => _priceRange = values);
                },
              ),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () {
                setState(() {
                  _priceRange = const RangeValues(0, 20000000);
                });
                Navigator.pop(context);
                _applyFilters();
              },
              child: const Text('Reset'),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF1565C0),
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
              onPressed: () {
                Navigator.pop(context);
                _applyFilters();
              },
              child: const Text('Terapkan'),
            ),
          ],
        ),
      ),
    );
  }
}
