import 'dart:async';

import 'package:flutter/material.dart';

import '../../../data/model/category_model.dart';
import '../../../data/model/item_model.dart';
import '../../../data/model/user_model.dart';
import '../../../data/repository/auth_repository.dart';
import '../../../data/repository/category_repository.dart';
import '../../../data/repository/item_repository.dart';
import '../../../shared/utils/utils.dart';
import '../../../shared/widget/widgets.dart';
import '../activity/activity_history_screen.dart';
import '../auth/login_screen.dart';
import '../item/item_detail_screen.dart';

class HomeScreen extends StatefulWidget {
  final UserModel currentUser;

  const HomeScreen({super.key, required this.currentUser});

  @override
  State<HomeScreen> createState() => HomeScreenState();
}

class HomeScreenState extends State<HomeScreen> {
  final AuthRepository _authRepository = AuthRepository();
  final ItemRepository _itemRepository = ItemRepository();
  final CategoryRepository _categoryRepository = CategoryRepository();

  bool _isLoggingOut = false;
  bool _isLoading = true;
  String? _error;
  int _selectedTabIndex = 0;
  int? _selectedCategoryId;
  String _searchQuery = '';
  final TextEditingController _searchController = TextEditingController();
  Timer? _debounceTimer;

  List<ItemModel> _allItems = [];
  List<ItemModel> _filteredItems = [];
  List<CategoryModel> _categories = [];

  // Stats
  int _lostCount = 0;
  int _foundCount = 0;
  int _returnedCount = 0;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  /// Public method to refresh data from outside
  void refreshData() {
    _loadData();
  }

  Future<void> _loadData() async {
    await Future.wait([_loadItems(), _loadCategories(), _loadStatistics()]);
  }

  Future<void> _loadCategories() async {
    try {
      final categories = await _categoryRepository.getCategories();
      setState(() {
        _categories = categories;
      });
    } catch (e) {
      // Ignore error, categories are optional
    }
  }

  Future<void> _loadStatistics() async {
    try {
      final stats = await _itemRepository.getStatistics();
      setState(() {
        _lostCount = stats['lost'] ?? 0;
        _foundCount = stats['found'] ?? 0;
        _returnedCount = stats['returned'] ?? 0;
      });
    } catch (e) {
      // Ignore error, stats will be 0
    }
  }

  Future<void> _loadItems() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final items = await _itemRepository.getItems();
      setState(() {
        _allItems = items;
        _filterItems();
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString().replaceAll('Exception: ', '');
        _isLoading = false;
      });
    }
  }

  void _filterItems() {
    List<ItemModel> result = _allItems;

    // Filter by tab (type)
    switch (_selectedTabIndex) {
      case 1: // Lost
        result = result.where((item) => item.isLost).toList();
        break;
      case 2: // Found
        result = result.where((item) => item.isFound).toList();
        break;
    }

    // Filter by category
    if (_selectedCategoryId != null) {
      result = result
          .where((item) => item.categoryId == _selectedCategoryId)
          .toList();
    }

    _filteredItems = result;
  }

  void _onTabChanged(int index) {
    setState(() {
      _selectedTabIndex = index;
      _filterItems();
    });
  }

  void _onSearchChanged(String query) {
    setState(() => _searchQuery = query);

    // Cancel previous timer
    _debounceTimer?.cancel();

    // Debounce search for 500ms
    _debounceTimer = Timer(const Duration(milliseconds: 500), () {
      _executeSearch(query);
    });
  }

  void _clearSearch() {
    _searchController.clear();
    setState(() => _searchQuery = '');
    _executeSearch('');
  }

  Future<void> _executeSearch(String query) async {
    setState(() => _isLoading = true);

    try {
      // Fetch items with search query
      final items = await _itemRepository.getItems(
        search: query.isEmpty ? null : query,
      );
      setState(() {
        _allItems = items;
        _filterItems();
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString().replaceAll('Exception: ', '');
        _isLoading = false;
      });
    }
  }

  @override
  void dispose() {
    _searchController.dispose();
    _debounceTimer?.cancel();
    super.dispose();
  }

  Future<void> _handleLogout() async {
    // Show confirmation dialog
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Keluar'),
        content: const Text('Apakah kamu yakin ingin keluar dari aplikasi?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          FilledButton(
            onPressed: () => Navigator.pop(context, true),
            style: FilledButton.styleFrom(backgroundColor: AppColors.error),
            child: const Text('Keluar'),
          ),
        ],
      ),
    );

    if (confirmed != true) return;

    setState(() => _isLoggingOut = true);

    try {
      await _authRepository.logout();
      if (!mounted) return;

      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (context) => const LoginScreen()),
        (route) => false,
      );
    } catch (error) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(error.toString().replaceAll('Exception: ', '')),
          backgroundColor: AppColors.error,
        ),
      );
      setState(() => _isLoggingOut = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: _loadData,
          color: AppColors.primary,
          child: CustomScrollView(
            slivers: [
              SliverToBoxAdapter(child: _buildHeader()),
              SliverToBoxAdapter(child: _buildHeroBanner()),
              SliverToBoxAdapter(child: _buildStatsSection()),
              SliverToBoxAdapter(child: _buildSearchBar()),
              SliverToBoxAdapter(child: _buildSegmentedTabs()),
              _buildItemList(),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Padding(
      padding: const EdgeInsets.all(AppSpacing.screenPadding),
      child: Row(
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              gradient: widget.currentUser.photoUrl == null
                  ? LinearGradient(
                      colors: [AppColors.primary, AppColors.primaryDark],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                    )
                  : null,
              shape: BoxShape.circle,
              boxShadow: [
                BoxShadow(
                  color: AppColors.primary.withValues(alpha: 0.3),
                  blurRadius: 12,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: ClipOval(
              child: widget.currentUser.photoUrl != null
                  ? Image.network(
                      AppConstants.getFullImageUrl(
                        widget.currentUser.photoUrl,
                      )!,
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) => Container(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            colors: [AppColors.primary, AppColors.primaryDark],
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight,
                          ),
                        ),
                        child: Center(
                          child: Text(
                            widget.currentUser.name
                                .substring(0, 1)
                                .toUpperCase(),
                            style: AppTextStyles.h2.copyWith(
                              color: Colors.white,
                            ),
                          ),
                        ),
                      ),
                    )
                  : Center(
                      child: Text(
                        widget.currentUser.name.substring(0, 1).toUpperCase(),
                        style: AppTextStyles.h2.copyWith(color: Colors.white),
                      ),
                    ),
            ),
          ),
          const SizedBox(width: AppSpacing.md),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Halo, ${widget.currentUser.name.split(' ').first}!',
                  style: AppTextStyles.h3,
                ),
                Text(
                  'Apa yang kamu cari?',
                  style: AppTextStyles.caption.copyWith(
                    color: AppColors.textSecondary,
                  ),
                ),
              ],
            ),
          ),

          _buildIconButton(
            icon: Icons.history,
            onTap: () => Navigator.push(
              context,
              MaterialPageRoute(
                builder: (context) => const ActivityHistoryScreen(),
              ),
            ),
            showBadge: true,
          ),
          const SizedBox(width: AppSpacing.sm),
          _buildIconButton(
            icon: Icons.logout_rounded,
            onTap: _isLoggingOut ? null : _handleLogout,
            isLoading: _isLoggingOut,
          ),
        ],
      ),
    );
  }

  Widget _buildIconButton({
    required IconData icon,
    VoidCallback? onTap,
    bool showBadge = false,
    bool isLoading = false,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 44,
        height: 44,
        decoration: BoxDecoration(
          color: AppColors.surface,
          shape: BoxShape.circle,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.05),
              blurRadius: 8,
            ),
          ],
        ),
        child: isLoading
            ? Center(
                child: SizedBox(
                  width: 18,
                  height: 18,
                  child: CircularProgressIndicator(
                    strokeWidth: 2,
                    color: AppColors.primary,
                  ),
                ),
              )
            : Stack(
                children: [
                  Center(
                    child: Icon(icon, color: AppColors.textSecondary, size: 20),
                  ),
                  if (showBadge)
                    Positioned(
                      top: 10,
                      right: 10,
                      child: Container(
                        width: 8,
                        height: 8,
                        decoration: BoxDecoration(
                          color: AppColors.primary,
                          shape: BoxShape.circle,
                        ),
                      ),
                    ),
                ],
              ),
      ),
    );
  }

  Widget _buildHeroBanner() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: AppSpacing.screenPadding),
      child: Container(
        height: 140,
        decoration: BoxDecoration(
          gradient: LinearGradient(
            colors: [AppColors.primary, AppColors.primaryDark],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          borderRadius: BorderRadius.circular(AppRadius.xl),
          boxShadow: [
            BoxShadow(
              color: AppColors.primary.withValues(alpha: 0.4),
              blurRadius: 20,
              offset: const Offset(0, 8),
            ),
          ],
        ),
        child: Stack(
          children: [
            Positioned(
              right: -20,
              top: -20,
              child: Container(
                width: 120,
                height: 120,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: Colors.white.withValues(alpha: 0.1),
                ),
              ),
            ),
            Positioned(
              right: 40,
              bottom: -30,
              child: Container(
                width: 80,
                height: 80,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: Colors.white.withValues(alpha: 0.1),
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(AppSpacing.lg),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(
                    'Kehilangan barang?',
                    style: AppTextStyles.h2.copyWith(color: Colors.white),
                  ),
                  const SizedBox(height: AppSpacing.xs),
                  Text(
                    'Laporkan sekarang atau cari\ndi daftar barang temuan!',
                    style: AppTextStyles.bodySmall.copyWith(
                      color: Colors.white.withValues(alpha: 0.9),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatsSection() {
    return Padding(
      padding: const EdgeInsets.all(AppSpacing.screenPadding),
      child: Row(
        children: [
          Expanded(
            child: StatCard(
              icon: Icons.search,
              value: _lostCount.toString(),
              label: 'Barang Hilang',
              color: AppColors.lostBadge,
            ),
          ),
          const SizedBox(width: AppSpacing.md),
          Expanded(
            child: StatCard(
              icon: Icons.inventory_2_outlined,
              value: _foundCount.toString(),
              label: 'Barang Temuan',
              color: AppColors.foundBadge,
            ),
          ),
          const SizedBox(width: AppSpacing.md),
          Expanded(
            child: StatCard(
              icon: Icons.check_circle_outline,
              value: _returnedCount.toString(),
              label: 'Dikembalikan',
              color: AppColors.success,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSearchBar() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: AppSpacing.screenPadding),
      child: Container(
        height: 52,
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(AppRadius.lg),
          border: Border.all(color: AppColors.border),
        ),
        child: Row(
          children: [
            const SizedBox(width: AppSpacing.md),
            Icon(Icons.search, color: AppColors.textTertiary),
            const SizedBox(width: AppSpacing.sm),
            Expanded(
              child: TextField(
                controller: _searchController,
                decoration: InputDecoration(
                  hintText: 'Cari barang...',
                  hintStyle: AppTextStyles.body.copyWith(
                    color: AppColors.textTertiary,
                  ),
                  border: InputBorder.none,
                  enabledBorder: InputBorder.none,
                  focusedBorder: InputBorder.none,
                  contentPadding: const EdgeInsets.symmetric(vertical: 14),
                  isDense: true,
                ),
                style: AppTextStyles.body,
                onChanged: _onSearchChanged,
              ),
            ),
            if (_searchQuery.isNotEmpty)
              GestureDetector(
                onTap: _clearSearch,
                child: Padding(
                  padding: const EdgeInsets.symmetric(
                    horizontal: AppSpacing.sm,
                  ),
                  child: Icon(
                    Icons.close,
                    color: AppColors.textTertiary,
                    size: 20,
                  ),
                ),
              ),
            GestureDetector(
              onTap: _showFilterBottomSheet,
              child: Container(
                margin: const EdgeInsets.all(AppSpacing.sm),
                padding: const EdgeInsets.all(AppSpacing.sm),
                decoration: BoxDecoration(
                  color: _selectedCategoryId != null
                      ? AppColors.primary
                      : AppColors.primary,
                  borderRadius: BorderRadius.circular(AppRadius.md),
                ),
                child: Stack(
                  children: [
                    Icon(Icons.tune, color: Colors.white, size: 18),
                    if (_selectedCategoryId != null)
                      Positioned(
                        top: -2,
                        right: -2,
                        child: Container(
                          width: 8,
                          height: 8,
                          decoration: BoxDecoration(
                            color: AppColors.success,
                            shape: BoxShape.circle,
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

  void _showFilterBottomSheet() {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      isScrollControlled: true,
      builder: (context) => Container(
        constraints: BoxConstraints(
          maxHeight: MediaQuery.of(context).size.height * 0.6,
        ),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: const BorderRadius.only(
            topLeft: Radius.circular(24),
            topRight: Radius.circular(24),
          ),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Handle
            Container(
              margin: const EdgeInsets.only(top: 12),
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: AppColors.border,
                borderRadius: BorderRadius.circular(2),
              ),
            ),

            // Title
            Padding(
              padding: const EdgeInsets.all(AppSpacing.lg),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text('Filter Kategori', style: AppTextStyles.h2),
                  if (_selectedCategoryId != null)
                    GestureDetector(
                      onTap: () {
                        setState(() {
                          _selectedCategoryId = null;
                          _filterItems();
                        });
                        Navigator.pop(context);
                      },
                      child: Text(
                        'Reset',
                        style: AppTextStyles.body.copyWith(
                          color: AppColors.primary,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                ],
              ),
            ),

            // Category List - Scrollable
            Flexible(
              child: ListView.builder(
                shrinkWrap: true,
                itemCount: _categories.length + 1, // +1 for "Semua Kategori"
                itemBuilder: (context, index) {
                  // First item is "Semua Kategori"
                  if (index == 0) {
                    final isSelected = _selectedCategoryId == null;
                    return ListTile(
                      onTap: () {
                        setState(() {
                          _selectedCategoryId = null;
                          _filterItems();
                        });
                        Navigator.pop(context);
                      },
                      leading: Container(
                        width: 40,
                        height: 40,
                        decoration: BoxDecoration(
                          color: isSelected
                              ? AppColors.primary.withValues(alpha: 0.1)
                              : AppColors.surfaceAlt,
                          shape: BoxShape.circle,
                        ),
                        child: Icon(
                          Icons.all_inclusive,
                          color: isSelected
                              ? AppColors.primary
                              : AppColors.textTertiary,
                          size: 20,
                        ),
                      ),
                      title: Text(
                        'Semua Kategori',
                        style: AppTextStyles.body.copyWith(
                          color: isSelected
                              ? AppColors.primary
                              : AppColors.textPrimary,
                          fontWeight: isSelected
                              ? FontWeight.w600
                              : FontWeight.normal,
                        ),
                      ),
                      trailing: isSelected
                          ? Icon(Icons.check_circle, color: AppColors.primary)
                          : null,
                    );
                  }

                  final category = _categories[index - 1];
                  final isSelected = _selectedCategoryId == category.id;

                  return ListTile(
                    onTap: () {
                      setState(() {
                        _selectedCategoryId = category.id;
                        _filterItems();
                      });
                      Navigator.pop(context);
                    },
                    leading: Container(
                      width: 40,
                      height: 40,
                      decoration: BoxDecoration(
                        color: isSelected
                            ? AppColors.primary.withValues(alpha: 0.1)
                            : AppColors.surfaceAlt,
                        shape: BoxShape.circle,
                      ),
                      child: Icon(
                        _getCategoryIcon(category.name),
                        color: isSelected
                            ? AppColors.primary
                            : AppColors.textTertiary,
                        size: 20,
                      ),
                    ),
                    title: Text(
                      category.name,
                      style: AppTextStyles.body.copyWith(
                        color: isSelected
                            ? AppColors.primary
                            : AppColors.textPrimary,
                        fontWeight: isSelected
                            ? FontWeight.w600
                            : FontWeight.normal,
                      ),
                    ),
                    trailing: isSelected
                        ? Icon(Icons.check_circle, color: AppColors.primary)
                        : null,
                  );
                },
              ),
            ),

            const SizedBox(height: AppSpacing.lg),
          ],
        ),
      ),
    );
  }

  Widget _buildSegmentedTabs() {
    return Padding(
      padding: const EdgeInsets.all(AppSpacing.screenPadding),
      child: Container(
        padding: const EdgeInsets.all(4),
        decoration: BoxDecoration(
          color: AppColors.surfaceAlt,
          borderRadius: BorderRadius.circular(AppRadius.full),
        ),
        child: Row(
          children: [
            _buildTabButton('Semua', 0),
            _buildTabButton('Hilang', 1),
            _buildTabButton('Temuan', 2),
          ],
        ),
      ),
    );
  }

  Widget _buildTabButton(String text, int index) {
    final isSelected = _selectedTabIndex == index;

    return Expanded(
      child: GestureDetector(
        onTap: () => _onTabChanged(index),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          padding: const EdgeInsets.symmetric(vertical: AppSpacing.sm + 2),
          decoration: BoxDecoration(
            color: isSelected ? AppColors.primary : Colors.transparent,
            borderRadius: BorderRadius.circular(AppRadius.full),
            boxShadow: isSelected
                ? [
                    BoxShadow(
                      color: AppColors.primary.withValues(alpha: 0.3),
                      blurRadius: 8,
                      offset: const Offset(0, 2),
                    ),
                  ]
                : null,
          ),
          child: Text(
            text,
            style: AppTextStyles.body.copyWith(
              color: isSelected ? Colors.white : AppColors.textSecondary,
              fontWeight: isSelected ? FontWeight.w600 : FontWeight.normal,
            ),
            textAlign: TextAlign.center,
          ),
        ),
      ),
    );
  }

  Widget _buildItemList() {
    if (_isLoading) {
      return SliverFillRemaining(
        hasScrollBody: false,
        child: Center(
          child: CircularProgressIndicator(color: AppColors.primary),
        ),
      );
    }

    if (_error != null) {
      return SliverFillRemaining(
        hasScrollBody: false,
        child: _buildErrorState(),
      );
    }

    if (_filteredItems.isEmpty) {
      return SliverFillRemaining(
        hasScrollBody: false,
        child: _buildEmptyState(),
      );
    }

    return SliverPadding(
      padding: const EdgeInsets.symmetric(horizontal: AppSpacing.screenPadding),
      sliver: SliverList(
        delegate: SliverChildBuilderDelegate((context, index) {
          final item = _filteredItems[index];
          return Padding(
            padding: const EdgeInsets.only(bottom: AppSpacing.md),
            child: ItemCard(
              item: item,
              onTap: () async {
                final result = await Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => ItemDetailScreen(item: item),
                  ),
                );
                if ((result == true || result == 'deleted') && mounted) {
                  _loadData();
                }
              },
            ),
          );
        }, childCount: _filteredItems.length),
      ),
    );
  }

  Widget _buildErrorState() {
    return ErrorState(message: _error, onRetry: _loadItems);
  }

  Widget _buildEmptyState() {
    String title;
    String subtitle;
    IconData icon;

    switch (_selectedTabIndex) {
      case 1:
        title = 'Belum ada barang hilang';
        subtitle = 'Semoga barangmu aman selalu!';
        icon = Icons.search_off_rounded;
        break;
      case 2:
        title = 'Belum ada barang temuan';
        subtitle = 'Belum ada yang melaporkan temuan';
        icon = Icons.inventory_2_outlined;
        break;
      default:
        title = 'Belum ada laporan';
        subtitle = 'Laporan barang akan muncul di sini';
        icon = Icons.inbox_outlined;
    }

    return EmptyState(icon: icon, title: title, subtitle: subtitle);
  }

  IconData _getCategoryIcon(String categoryName) {
    switch (categoryName.toLowerCase()) {
      case 'elektronik':
        return Icons.phone_android;
      case 'dokumen':
        return Icons.description_outlined;
      case 'aksesoris':
        return Icons.watch_outlined;
      case 'tas & dompet':
        return Icons.wallet_outlined;
      case 'kunci':
        return Icons.key_outlined;
      case 'pakaian':
        return Icons.checkroom_outlined;
      case 'lainnya':
        return Icons.more_horiz;
      default:
        return Icons.category_outlined;
    }
  }
}
