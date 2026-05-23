import 'package:flutter/material.dart';

import '../../../data/model/item_model.dart';
import '../../../data/repository/claim_repository.dart';
import '../../../data/repository/item_repository.dart';
import '../../../shared/utils/utils.dart';
import '../../../shared/widget/widgets.dart';
import '../claim/my_claim_history_screen.dart';
import '../item/item_detail_screen.dart';

/// Screen untuk menampilkan daftar barang dan klaim milik user
/// Terdiri dari 2 tab: Laporan Saya dan Klaim Saya
class MyItemsScreen extends StatefulWidget {
  final int initialMainTabIndex; // 0 = Laporan, 1 = Klaim Saya

  const MyItemsScreen({super.key, this.initialMainTabIndex = 0});

  @override
  State<MyItemsScreen> createState() => MyItemsScreenState();
}

class MyItemsScreenState extends State<MyItemsScreen>
    with SingleTickerProviderStateMixin {
  late TabController _mainTabController;
  final ItemRepository _itemRepository = ItemRepository();
  final ClaimRepository _claimRepository = ClaimRepository();

  bool _isLoading = true;
  String? _errorMessage;
  List<ItemModel> _myItems = [];
  List<dynamic> _myClaims = [];
  bool _isLoadingClaims = true;
  int _selectedReportTab = 0; // 0 = Hilang, 1 = Temuan

  @override
  void initState() {
    super.initState();
    _mainTabController = TabController(
      length: 2,
      vsync: this,
      initialIndex: widget.initialMainTabIndex,
    );
    _loadMyItems();
    _loadMyClaims();
  }

  @override
  void dispose() {
    _mainTabController.dispose();
    super.dispose();
  }

  void refreshData() {
    _loadMyItems();
    _loadMyClaims();
  }

  Future<void> _loadMyItems() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final items = await _itemRepository.getMyItems();
      if (mounted) {
        setState(() {
          _myItems = items;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _errorMessage = e.toString().replaceFirst('Exception: ', '');
          _isLoading = false;
        });
      }
    }
  }

  Future<void> _loadMyClaims() async {
    setState(() {
      _isLoadingClaims = true;
    });

    try {
      final claims = await _claimRepository.myClaims();
      if (mounted) {
        setState(() {
          _myClaims = claims;
          _isLoadingClaims = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isLoadingClaims = false;
        });
      }
    }
  }

  List<ItemModel> get _lostItems =>
      _myItems.where((item) => item.isLost).toList();

  List<ItemModel> get _foundItems =>
      _myItems.where((item) => item.isFound).toList();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        title: const Text(
          'Barang Saya',
          style: TextStyle(
            color: AppColors.textPrimary,
            fontSize: 18,
            fontWeight: FontWeight.w600,
          ),
        ),
        centerTitle: true,
        automaticallyImplyLeading: false,
        bottom: TabBar(
          controller: _mainTabController,
          labelColor: AppColors.primary,
          unselectedLabelColor: AppColors.textSecondary,
          indicatorColor: AppColors.primary,
          indicatorWeight: 3,
          labelStyle: AppTextStyles.tab.copyWith(fontWeight: FontWeight.w600),
          unselectedLabelStyle: AppTextStyles.tab,
          tabs: const [
            Tab(text: 'Laporan'),
            Tab(text: 'Klaim Saya'),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _errorMessage != null
          ? _buildErrorState()
          : TabBarView(
              controller: _mainTabController,
              children: [_buildReportsTab(), _buildClaimsTab()],
            ),
    );
  }

  Widget _buildErrorState() {
    return ErrorState(message: _errorMessage, onRetry: _loadMyItems);
  }

  Widget _buildReportsTab() {
    return Column(
      children: [
        // Segmented tabs for Lost/Found
        _buildSegmentedTabs(),
        // Content
        Expanded(
          child: _selectedReportTab == 0
              ? _buildItemsList(_lostItems, 'Barang Hilang')
              : _buildItemsList(_foundItems, 'Barang Temuan'),
        ),
      ],
    );
  }

  Widget _buildSegmentedTabs() {
    return Padding(
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Container(
        padding: const EdgeInsets.all(4),
        decoration: BoxDecoration(
          color: AppColors.surfaceAlt,
          borderRadius: BorderRadius.circular(AppRadius.full),
        ),
        child: Row(
          children: [
            _buildSegmentedTabButton(
              'Hilang',
              0,
              _lostItems.length,
              AppColors.lostBadge,
            ),
            _buildSegmentedTabButton(
              'Temuan',
              1,
              _foundItems.length,
              AppColors.foundBadge,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSegmentedTabButton(
    String text,
    int index,
    int count,
    Color dotColor,
  ) {
    final isSelected = _selectedReportTab == index;

    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => _selectedReportTab = index),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          padding: const EdgeInsets.symmetric(vertical: AppSpacing.sm + 2),
          decoration: BoxDecoration(
            color: isSelected ? AppColors.primary : Colors.transparent,
            borderRadius: BorderRadius.circular(AppRadius.full),
            boxShadow: isSelected
                ? [
                    BoxShadow(
                      color: AppColors.primary.withAlpha(77),
                      blurRadius: 8,
                      offset: const Offset(0, 2),
                    ),
                  ]
                : null,
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                width: 8,
                height: 8,
                decoration: BoxDecoration(
                  color: isSelected ? Colors.white : dotColor,
                  shape: BoxShape.circle,
                ),
              ),
              const SizedBox(width: 6),
              Text(
                '$text ($count)',
                style: AppTextStyles.body.copyWith(
                  color: isSelected ? Colors.white : AppColors.textSecondary,
                  fontWeight: isSelected ? FontWeight.w600 : FontWeight.normal,
                  fontSize: 14,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildItemsList(List<ItemModel> items, String emptyTitle) {
    if (items.isEmpty) {
      return _buildEmptyState(
        icon: Icons.inventory_2_outlined,
        title: 'Belum ada $emptyTitle',
        subtitle: 'Laporan $emptyTitle akan muncul di sini',
      );
    }

    return RefreshIndicator(
      onRefresh: _loadMyItems,
      child: ListView.builder(
        padding: const EdgeInsets.all(AppSpacing.md),
        itemCount: items.length,
        itemBuilder: (context, index) => _buildMyItemCard(items[index]),
      ),
    );
  }

  Widget _buildClaimsTab() {
    if (_isLoadingClaims) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_myClaims.isEmpty) {
      return _buildEmptyState(
        icon: Icons.assignment_outlined,
        title: 'Belum ada klaim',
        subtitle: 'Klaim yang kamu ajukan akan muncul di sini',
      );
    }

    // Group claims by item
    final groupedClaims = <int, List<dynamic>>{};
    for (final claim in _myClaims) {
      if (!groupedClaims.containsKey(claim.itemId)) {
        groupedClaims[claim.itemId] = [];
      }
      groupedClaims[claim.itemId]!.add(claim);
    }

    // Sort each group by date (newest first) and prepare display data
    final itemSummaries = groupedClaims.entries.map((entry) {
      final claims = entry.value;
      claims.sort((a, b) => b.createdAt.compareTo(a.createdAt));
      final latestClaim = claims.first;
      return {
        'itemId': entry.key,
        'claims': claims,
        'latestClaim': latestClaim,
        'claimCount': claims.length,
        'itemTitle': latestClaim.itemTitle ?? 'Barang',
        'itemCategory': latestClaim.itemCategory,
        'itemPhotoUrl': latestClaim.itemPhotoUrl,
      };
    }).toList();

    // Sort by latest claim date
    itemSummaries.sort(
      (a, b) => (b['latestClaim'] as dynamic).createdAt.compareTo(
        (a['latestClaim'] as dynamic).createdAt,
      ),
    );

    return RefreshIndicator(
      onRefresh: _loadMyClaims,
      child: ListView.builder(
        padding: const EdgeInsets.all(AppSpacing.md),
        itemCount: itemSummaries.length,
        itemBuilder: (context, index) =>
            _buildClaimItemCard(itemSummaries[index]),
      ),
    );
  }

  Widget _buildClaimItemCard(Map<String, dynamic> summary) {
    final latestClaim = summary['latestClaim'] as dynamic;
    final claimCount = summary['claimCount'] as int;
    final hasMultipleClaims = claimCount > 1;

    // Determine overall status
    final hasApproved = (summary['claims'] as List).any((c) => c.isApproved);
    final hasPending = (summary['claims'] as List).any((c) => c.isPending);
    final allRejected = (summary['claims'] as List).every((c) => c.isRejected);

    Color borderColor = AppColors.border;
    if (hasApproved) {
      borderColor = AppColors.success.withAlpha(128);
    } else if (hasPending) {
      borderColor = AppColors.warning.withAlpha(128);
    } else if (allRejected) {
      borderColor = AppColors.error.withAlpha(128);
    }

    return GestureDetector(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => MyClaimHistoryScreen(
              itemId: summary['itemId'] as int,
              itemTitle: summary['itemTitle'] as String,
              itemPhotoUrl: summary['itemPhotoUrl'] as String?,
            ),
          ),
        );
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: AppSpacing.md),
        padding: const EdgeInsets.all(AppSpacing.md),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(AppRadius.md),
          border: Border.all(color: borderColor),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withAlpha(13),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Photo
            ClipRRect(
              borderRadius: BorderRadius.circular(AppRadius.sm),
              child: summary['itemPhotoUrl'] != null
                  ? Image.network(
                      AppConstants.getFullImageUrl(summary['itemPhotoUrl'])!,
                      width: 70,
                      height: 70,
                      fit: BoxFit.cover,
                      errorBuilder: (c, e, s) => _buildClaimPlaceholder(),
                    )
                  : _buildClaimPlaceholder(),
            ),
            const SizedBox(width: AppSpacing.md),

            // Info
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Title
                  Text(
                    summary['itemTitle'] as String,
                    style: AppTextStyles.body.copyWith(
                      fontWeight: FontWeight.w600,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: AppSpacing.xs),

                  // Category
                  if (summary['itemCategory'] != null)
                    Text(
                      summary['itemCategory'] as String,
                      style: AppTextStyles.caption.copyWith(
                        color: AppColors.textSecondary,
                      ),
                    ),
                  const SizedBox(height: AppSpacing.xs),

                  // Status badges
                  Row(
                    children: [
                      _buildStatusBadgeForClaim(latestClaim),
                      if (hasMultipleClaims) ...[
                        const SizedBox(width: AppSpacing.xs),
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: AppSpacing.xs,
                            vertical: 2,
                          ),
                          decoration: BoxDecoration(
                            color: AppColors.textTertiary.withAlpha(26),
                            borderRadius: BorderRadius.circular(AppRadius.sm),
                          ),
                          child: Text(
                            '$claimCount klaim',
                            style: AppTextStyles.caption.copyWith(
                              color: AppColors.textSecondary,
                              fontWeight: FontWeight.w500,
                              fontSize: 10,
                            ),
                          ),
                        ),
                      ],
                    ],
                  ),
                  const SizedBox(height: AppSpacing.xs),

                  // Time
                  Row(
                    children: [
                      const Icon(
                        Icons.access_time,
                        size: 12,
                        color: AppColors.textTertiary,
                      ),
                      const SizedBox(width: 4),
                      Text(
                        _formatTimeAgo(latestClaim.createdAt),
                        style: AppTextStyles.caption.copyWith(
                          color: AppColors.textTertiary,
                          fontSize: 11,
                        ),
                      ),
                      const Spacer(),
                      const Icon(
                        Icons.chevron_right,
                        size: 18,
                        color: AppColors.textTertiary,
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

  Widget _buildStatusBadgeForClaim(dynamic claim) {
    Color color;
    String text;
    IconData icon;

    if (claim.isApproved) {
      color = AppColors.success;
      text = 'Disetujui';
      icon = Icons.check_circle;
    } else if (claim.isRejected) {
      color = AppColors.error;
      text = 'Ditolak';
      icon = Icons.cancel;
    } else {
      color = AppColors.warning;
      text = 'Pending';
      icon = Icons.schedule;
    }

    return Container(
      padding: const EdgeInsets.symmetric(
        horizontal: AppSpacing.xs,
        vertical: 2,
      ),
      decoration: BoxDecoration(
        color: color.withAlpha(26),
        borderRadius: BorderRadius.circular(AppRadius.sm),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 10, color: color),
          const SizedBox(width: 2),
          Text(
            text,
            style: AppTextStyles.caption.copyWith(
              color: color,
              fontWeight: FontWeight.w600,
              fontSize: 10,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildClaimPlaceholder() {
    return Container(
      width: 70,
      height: 70,
      color: AppColors.surfaceAlt,
      child: const Icon(
        Icons.image_outlined,
        color: AppColors.textTertiary,
        size: 28,
      ),
    );
  }

  Widget _buildEmptyState({
    required IconData icon,
    required String title,
    required String subtitle,
  }) {
    return EmptyState(icon: icon, title: title, subtitle: subtitle);
  }

  Widget _buildMyItemCard(ItemModel item) {
    return GestureDetector(
      onTap: () async {
        final result = await Navigator.push(
          context,
          MaterialPageRoute(builder: (context) => ItemDetailScreen(item: item)),
        );
        if ((result == true || result == 'deleted') && mounted) {
          _loadMyItems();
        }
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: AppSpacing.md),
        padding: const EdgeInsets.all(AppSpacing.md),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(AppRadius.md),
          border: Border.all(color: AppColors.border),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withAlpha(13),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Row(
          children: [
            // Photo
            ClipRRect(
              borderRadius: BorderRadius.circular(AppRadius.sm),
              child: item.photoUrl != null
                  ? Image.network(
                      AppConstants.getFullImageUrl(item.photoUrl)!,
                      width: 70,
                      height: 70,
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) =>
                          const ImagePlaceholder(size: 70),
                    )
                  : const ImagePlaceholder(size: 70),
            ),
            const SizedBox(width: AppSpacing.md),

            // Info
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    item.title,
                    style: AppTextStyles.body.copyWith(
                      fontWeight: FontWeight.w600,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: AppSpacing.xs),
                  Row(
                    children: [
                      _buildStatusBadge(item.status),
                      const SizedBox(width: AppSpacing.sm),
                      Expanded(
                        child: Text(
                          '• ${_formatTimeAgo(item.createdAt)}',
                          style: AppTextStyles.caption.copyWith(
                            color: AppColors.textSecondary,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                  if (item.isFound &&
                      item.claimsCount != null &&
                      item.claimsCount! > 0) ...[
                    const SizedBox(height: AppSpacing.xs),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: AppSpacing.sm,
                        vertical: 2,
                      ),
                      decoration: BoxDecoration(
                        color: AppColors.warning.withAlpha(26),
                        borderRadius: BorderRadius.circular(AppRadius.sm),
                      ),
                      child: Text(
                        '${item.claimsCount} klaim masuk',
                        style: AppTextStyles.caption.copyWith(
                          color: AppColors.warning,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ),
                  ],
                ],
              ),
            ),

            // Arrow
            Icon(Icons.chevron_right, color: AppColors.textSecondary),
          ],
        ),
      ),
    );
  }

  Widget _buildStatusBadge(String status) {
    Color color;
    String text;

    switch (status) {
      case 'active':
        color = AppColors.warning;
        text = 'Aktif';
        break;
      case 'claimed':
        color = AppColors.primary;
        text = 'Diklaim';
        break;
      case 'returned':
        color = AppColors.success;
        text = 'Returned';
        break;
      default:
        color = AppColors.textSecondary;
        text = status;
    }

    return Container(
      padding: const EdgeInsets.symmetric(
        horizontal: AppSpacing.sm,
        vertical: 2,
      ),
      decoration: BoxDecoration(
        color: color.withAlpha(26),
        borderRadius: BorderRadius.circular(AppRadius.full),
      ),
      child: Text(
        text,
        style: AppTextStyles.caption.copyWith(
          color: color,
          fontWeight: FontWeight.w600,
        ),
      ),
    );
  }

  String _formatTimeAgo(DateTime dateTime) {
    final now = DateTime.now();
    final difference = now.difference(dateTime);

    if (difference.inDays > 7) {
      return '${difference.inDays ~/ 7} minggu lalu';
    } else if (difference.inDays > 0) {
      return '${difference.inDays} hari lalu';
    } else if (difference.inHours > 0) {
      return '${difference.inHours} jam lalu';
    } else if (difference.inMinutes > 0) {
      return '${difference.inMinutes} menit lalu';
    } else {
      return 'Baru saja';
    }
  }
}
