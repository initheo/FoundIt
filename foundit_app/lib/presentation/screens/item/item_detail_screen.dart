import 'package:flutter/material.dart';
import 'package:foundit_app/presentation/screens/claim/my_claim_history_screen.dart';
import 'package:intl/intl.dart';

import '../../../data/model/item_model.dart';
import '../../../data/repository/auth_repository.dart';
import '../../../data/repository/claim_repository.dart';
import '../../../data/repository/item_repository.dart';
import '../../../shared/utils/utils.dart';
import '../../../shared/widget/widgets.dart';
import '../claim/claim_form_screen.dart';
import '../claim/review_claims_screen.dart';
import '../main_navigation_screen.dart';
import 'edit_item_screen.dart';
import 'photo_viewer_screen.dart';

class ItemDetailScreen extends StatefulWidget {
  final ItemModel item;

  const ItemDetailScreen({super.key, required this.item});

  @override
  State<ItemDetailScreen> createState() => _ItemDetailScreenState();
}

class _ItemDetailScreenState extends State<ItemDetailScreen> {
  int _currentPhotoIndex = 0;
  final PageController _pageController = PageController();
  bool _isOwner = false;
  late ItemModel _currentItem;
  bool _wasEdited = false;

  // Claim status tracking
  final ClaimRepository _claimRepository = ClaimRepository();
  String? _userClaimStatus; // 'pending', 'approved', 'rejected', or null
  bool _isLoadingClaimStatus = true;

  // Use allPhotos from ItemModel for multiple photo support
  List<String> get _photos => _currentItem.allPhotos.isNotEmpty
      ? _currentItem.allPhotos
      : ['https://via.placeholder.com/400x300?text=No+Photo'];

  @override
  void initState() {
    super.initState();
    _currentItem = widget.item;
    _checkOwnership();
    _loadUserClaimStatus();
  }

  Future<void> _checkOwnership() async {
    final authRepo = AuthRepository();
    final user = await authRepo.getCurrentUser();
    if (mounted && user != null) {
      setState(() {
        _isOwner = user.id == _currentItem.userId;
      });
    }
  }

  Future<void> _loadUserClaimStatus() async {
    try {
      final myClaims = await _claimRepository.myClaims();
      // Find claim for this item, or null if not found
      final claimsForItem = myClaims.where((c) => c.itemId == _currentItem.id);

      if (mounted) {
        setState(() {
          if (claimsForItem.isNotEmpty) {
            final claim = claimsForItem.first;
            if (claim.isApproved) {
              _userClaimStatus = 'approved';
            } else if (claim.isRejected) {
              _userClaimStatus = 'rejected';
            } else {
              _userClaimStatus = 'pending';
            }
          } else {
            _userClaimStatus = null;
          }
          _isLoadingClaimStatus = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoadingClaimStatus = false);
      }
    }
  }

  @override
  void dispose() {
    _pageController.dispose();
    super.dispose();
  }

  Future<void> _navigateToEditScreen() async {
    final result = await Navigator.push<ItemModel>(
      context,
      MaterialPageRoute(
        builder: (context) => EditItemScreen(item: _currentItem),
      ),
    );

    if (result != null && mounted) {
      // Update current item display
      setState(() {
        _currentItem = result;
        _wasEdited = true;
      });
    }
  }

  Future<void> _showDeleteConfirmation() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus Barang'),
        content: Text(
          'Apakah kamu yakin ingin menghapus "${_currentItem.title}"? Tindakan ini tidak dapat dibatalkan.',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          FilledButton(
            onPressed: () => Navigator.pop(context, true),
            style: FilledButton.styleFrom(backgroundColor: AppColors.error),
            child: const Text('Hapus'),
          ),
        ],
      ),
    );

    if (confirmed == true && mounted) {
      await _deleteItem();
    }
  }

  Future<void> _deleteItem() async {
    try {
      // Show loading
      showDialog(
        context: context,
        barrierDismissible: false,
        builder: (context) => const Center(child: CircularProgressIndicator()),
      );

      final itemRepository = ItemRepository();
      await itemRepository.deleteItem(_currentItem.id);

      if (!mounted) return;
      Navigator.pop(context); // Close loading

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Barang berhasil dihapus'),
          backgroundColor: AppColors.success,
        ),
      );

      // Pop back with special flag
      Navigator.pop(context, 'deleted');
    } catch (e) {
      if (!mounted) return;
      Navigator.pop(context); // Close loading

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            'Gagal menghapus: ${e.toString().replaceFirst("Exception: ", "")}',
          ),
          backgroundColor: AppColors.error,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (didPop, result) {
        if (!didPop) {
          Navigator.pop(context, _wasEdited);
        }
      },
      child: Scaffold(
        backgroundColor: AppColors.background,
        body: CustomScrollView(
          slivers: [
            // AppBar with Photo Slider
            _buildSliverAppBar(),

            // Content
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.all(AppSpacing.md),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Title and Badge
                    _buildTitleSection(),
                    const SizedBox(height: AppSpacing.lg),

                    // Info Card
                    _buildInfoCard(),
                    const SizedBox(height: AppSpacing.lg),

                    // Description
                    _buildDescriptionSection(),
                    const SizedBox(height: AppSpacing.lg),

                    // Reporter Section
                    _buildReporterSection(),
                    const SizedBox(height: AppSpacing.lg),

                    // Claim Button (show if active OR if user has a claim status)
                    if (!_isOwner &&
                        (_currentItem.isActive || _userClaimStatus != null))
                      _buildClaimButton(),

                    // Review Claims Button (owner only)
                    if (_isOwner) _buildReviewClaimsButton(),

                    // Mark as Returned Button (for claimed items)
                    // Show to the person who has the item:
                    // - Lost item: claimer (finder) has the item
                    // - Found item: owner (finder/poster) has the item
                    if (_currentItem.status == 'claimed') ...[
                      const SizedBox(height: AppSpacing.sm),
                      if (_currentItem.isLost && _userClaimStatus == 'approved')
                        _buildMarkAsReturnedButton(),
                      if (!_currentItem.isLost && _isOwner)
                        _buildMarkAsReturnedButton(),
                    ],

                    const SizedBox(height: AppSpacing.lg),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSliverAppBar() {
    return SliverAppBar(
      expandedHeight: 280,
      pinned: true,
      backgroundColor: AppColors.surface,
      leading: GestureDetector(
        onTap: () => Navigator.pop(context, _wasEdited),
        child: Container(
          margin: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: Colors.black.withAlpha(77),
            shape: BoxShape.circle,
          ),
          child: const Icon(Icons.arrow_back, color: Colors.white),
        ),
      ),
      actions: _isOwner
          ? [
              GestureDetector(
                onTap: _navigateToEditScreen,
                child: Container(
                  margin: const EdgeInsets.all(8),
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: Colors.black.withAlpha(77),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(Icons.edit, color: Colors.white, size: 20),
                ),
              ),
              GestureDetector(
                onTap: _showDeleteConfirmation,
                child: Container(
                  margin: const EdgeInsets.only(right: 8),
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: Colors.black.withAlpha(77),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.delete,
                    color: Colors.white,
                    size: 20,
                  ),
                ),
              ),
            ]
          : null,
      flexibleSpace: FlexibleSpaceBar(
        background: Stack(
          children: [
            // Photo Slider
            PageView.builder(
              controller: _pageController,
              onPageChanged: (index) {
                setState(() => _currentPhotoIndex = index);
              },
              itemCount: _photos.length,
              itemBuilder: (context, index) {
                return GestureDetector(
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => PhotoViewerScreen(
                          photoUrls: _photos,
                          initialIndex: index,
                          title: _currentItem.title,
                        ),
                      ),
                    );
                  },
                  child: Image.network(
                    AppConstants.getFullImageUrl(_photos[index])!,
                    fit: BoxFit.cover,
                    errorBuilder: (context, error, stackTrace) {
                      return Container(
                        color: AppColors.surfaceAlt,
                        child: const Center(
                          child: Icon(
                            Icons.image_not_supported,
                            size: 64,
                            color: AppColors.textSecondary,
                          ),
                        ),
                      );
                    },
                  ),
                );
              },
            ),

            // Photo Indicators
            if (_photos.length > 1)
              Positioned(
                bottom: 16,
                left: 0,
                right: 0,
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: List.generate(_photos.length, (index) {
                    return Container(
                      margin: const EdgeInsets.symmetric(horizontal: 4),
                      width: _currentPhotoIndex == index ? 24 : 8,
                      height: 8,
                      decoration: BoxDecoration(
                        color: _currentPhotoIndex == index
                            ? Colors.white
                            : Colors.white.withAlpha(128),
                        borderRadius: BorderRadius.circular(4),
                      ),
                    );
                  }),
                ),
              ),
          ],
        ),
      ),
    );
  }

  Widget _buildTitleSection() {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(_currentItem.title, style: AppTextStyles.h1),
              const SizedBox(height: AppSpacing.xs),
              Text(
                _currentItem.categoryName ?? 'Uncategorized',
                style: AppTextStyles.bodySmall.copyWith(
                  color: AppColors.textSecondary,
                ),
              ),
            ],
          ),
        ),
        _buildStatusBadge(),
      ],
    );
  }

  Widget _buildStatusBadge() {
    final isLost = _currentItem.isLost;
    final color = isLost ? AppColors.lostBadge : AppColors.foundBadge;
    final label = isLost ? 'HILANG' : 'TEMUAN';
    final icon = isLost ? Icons.search_off : Icons.search;

    return Container(
      padding: const EdgeInsets.symmetric(
        horizontal: AppSpacing.md,
        vertical: AppSpacing.sm,
      ),
      decoration: BoxDecoration(
        color: color.withAlpha(25),
        borderRadius: BorderRadius.circular(AppRadius.full),
        border: Border.all(color: color.withAlpha(76)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, color: color, size: 16),
          const SizedBox(width: AppSpacing.xs),
          Text(
            label,
            style: AppTextStyles.caption.copyWith(
              color: color,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoCard() {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadius.lg),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(15),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        children: [
          _buildInfoRow(
            icon: Icons.location_on,
            label: 'Lokasi',
            value: _currentItem.location,
          ),
          if (_currentItem.locationDetail != null &&
              _currentItem.locationDetail!.isNotEmpty) ...[
            const Divider(height: 24),
            _buildInfoRow(
              icon: Icons.edit_location_alt_outlined,
              label: 'Detail Lokasi',
              value: _currentItem.locationDetail!,
            ),
          ],
          const Divider(height: 24),
          _buildInfoRow(
            icon: Icons.calendar_today,
            label: _currentItem.isLost ? 'Tanggal Hilang' : 'Tanggal Ditemukan',
            value: DateFormat('dd MMMM yyyy').format(_currentItem.dateTime),
          ),
          const Divider(height: 24),
          _buildInfoRow(
            icon: Icons.access_time,
            label: _currentItem.isLost ? 'Waktu Hilang' : 'Waktu Ditemukan',
            value: DateFormat('HH:mm').format(_currentItem.dateTime),
          ),
          if (_currentItem.storageInfo != null) ...[
            const Divider(height: 24),
            _buildInfoRow(
              icon: Icons.inventory_2,
              label: 'Disimpan di',
              value: _currentItem.storageInfo!,
            ),
          ],
          const Divider(height: 24),
          _buildInfoRow(
            icon: Icons.info_outline,
            label: 'Status',
            value: _getStatusLabel(_currentItem.status),
            valueColor: _getStatusColor(_currentItem.status),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoRow({
    required IconData icon,
    required String label,
    required String value,
    Color? valueColor,
  }) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: AppColors.surfaceAlt,
            borderRadius: BorderRadius.circular(8),
          ),
          child: Icon(icon, size: 20, color: AppColors.textSecondary),
        ),
        const SizedBox(width: AppSpacing.md),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: AppTextStyles.caption.copyWith(
                  color: AppColors.textSecondary,
                ),
              ),
              const SizedBox(height: 2),
              Text(
                value,
                style: AppTextStyles.body.copyWith(
                  color: valueColor ?? AppColors.textPrimary,
                  fontWeight: valueColor != null
                      ? FontWeight.w600
                      : FontWeight.normal,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildDescriptionSection() {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadius.lg),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(15),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Icon(
                Icons.description,
                size: 20,
                color: AppColors.textSecondary,
              ),
              const SizedBox(width: AppSpacing.sm),
              Text('Deskripsi', style: AppTextStyles.h3),
            ],
          ),
          const SizedBox(height: AppSpacing.md),
          Text(
            _currentItem.description,
            style: AppTextStyles.body.copyWith(
              color: AppColors.textSecondary,
              height: 1.6,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildReporterSection() {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadius.lg),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(15),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        children: [
          Row(
            children: [
              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  color: _currentItem.userPhotoUrl == null
                      ? AppColors.primary.withAlpha(25)
                      : null,
                  shape: BoxShape.circle,
                ),
                child: ClipOval(
                  child: _currentItem.userPhotoUrl != null
                      ? Image.network(
                          AppConstants.getFullImageUrl(
                            _currentItem.userPhotoUrl,
                          )!,
                          fit: BoxFit.cover,
                          errorBuilder: (context, error, stackTrace) =>
                              Container(
                                color: AppColors.primary.withAlpha(25),
                                child: Center(
                                  child: Text(
                                    (_currentItem.userName ?? 'U')[0]
                                        .toUpperCase(),
                                    style: AppTextStyles.h2.copyWith(
                                      color: AppColors.primary,
                                    ),
                                  ),
                                ),
                              ),
                        )
                      : Center(
                          child: Text(
                            (_currentItem.userName ?? 'U')[0].toUpperCase(),
                            style: AppTextStyles.h2.copyWith(
                              color: AppColors.primary,
                            ),
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
                      _currentItem.isLost ? 'Dicari oleh' : 'Ditemukan oleh',
                      style: AppTextStyles.caption.copyWith(
                        color: AppColors.textSecondary,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      _currentItem.userName ?? 'Unknown User',
                      style: AppTextStyles.body.copyWith(
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ),
              Text(
                _getTimeAgo(_currentItem.createdAt),
                style: AppTextStyles.caption.copyWith(
                  color: AppColors.textSecondary,
                ),
              ),
            ],
          ),
          // Contact buttons removed to avoid duplication with "Klaim Diterima" card
        ],
      ),
    );
  }

  Widget _buildClaimButton() {
    if (_isLoadingClaimStatus) {
      return const SizedBox(
        height: 52,
        child: Center(child: CircularProgressIndicator()),
      );
    }

    // Already approved - show button to claim history
    if (_userClaimStatus == 'approved') {
      return SizedBox(
        width: double.infinity, // Ensure full width
        child: PrimaryButton(
          text: 'Lihat Riwayat Klaim',
          onPressed: () {
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (context) => MyClaimHistoryScreen(
                  itemId: _currentItem.id,
                  itemTitle: _currentItem.title,
                  itemPhotoUrl: _photos.isNotEmpty ? _photos.first : null,
                ),
              ),
            );
          },
        ),
      );
    }

    // Already rejected - allow to claim again
    if (_userClaimStatus == 'rejected') {
      return Column(
        children: [
          Container(
            height: 40,
            decoration: BoxDecoration(
              color: AppColors.error.withAlpha(26),
              borderRadius: BorderRadius.circular(AppRadius.md),
              border: Border.all(color: AppColors.error),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.cancel, color: AppColors.error, size: 16),
                const SizedBox(width: 6),
                Text(
                  'Klaim sebelumnya ditolak',
                  style: AppTextStyles.caption.copyWith(
                    color: AppColors.error,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: AppSpacing.sm),
          PrimaryButton(
            text: 'Ajukan Klaim Lagi',
            onPressed: () async {
              final result = await Navigator.push<bool>(
                context,
                MaterialPageRoute(
                  builder: (context) => ClaimFormScreen(item: _currentItem),
                ),
              );
              if (result == true && mounted) {
                await _loadUserClaimStatus();
              }
            },
          ),
        ],
      );
    }

    // Pending claim - tappable to go to Klaim Saya
    if (_userClaimStatus == 'pending') {
      return GestureDetector(
        onTap: () async {
          // Get current user from auth repository
          final authRepo = AuthRepository();
          final user = await authRepo.getCurrentUser();
          if (!mounted || user == null) return;

          // Navigate to MainNavigationScreen with MyItems tab (2) and Klaim Saya sub-tab (1)
          Navigator.pushAndRemoveUntil(
            context,
            MaterialPageRoute(
              builder: (context) => MainNavigationScreen(
                currentUser: user,
                initialTabIndex: 2, // MyItem tab
                myItemsSubTabIndex: 1, // Klaim Saya sub-tab
              ),
            ),
            (route) => false,
          );
        },
        child: Container(
          width: double.infinity,
          height: AppSpacing.buttonHeight,
          decoration: BoxDecoration(
            color: AppColors.warning.withAlpha(26),
            borderRadius: BorderRadius.circular(AppRadius.md),
            border: Border.all(color: AppColors.warning),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.schedule, color: AppColors.warning, size: 20),
              const SizedBox(width: 8),
              Text(
                'Menunggu Review',
                style: AppTextStyles.body.copyWith(
                  color: AppColors.warning,
                  fontWeight: FontWeight.w600,
                ),
              ),
              const SizedBox(width: 8),
              const Icon(
                Icons.arrow_forward_ios,
                color: AppColors.warning,
                size: 14,
              ),
            ],
          ),
        ),
      );
    }

    // No claim yet - show submit button
    final buttonText = _currentItem.isLost
        ? 'Saya Menemukan Barang Ini!'
        : 'Ini Barang Saya!';
    return PrimaryButton(
      text: buttonText,
      onPressed: () async {
        final result = await Navigator.push<bool>(
          context,
          MaterialPageRoute(
            builder: (context) => ClaimFormScreen(item: _currentItem),
          ),
        );
        // Reload claim status after returning from form
        if (result == true && mounted) {
          await _loadUserClaimStatus();
        }
      },
    );
  }

  Widget _buildReviewClaimsButton() {
    return SizedBox(
      width: double.infinity,
      child: PrimaryButton(
        text: 'Lihat Klaim Masuk',
        onPressed: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => ReviewClaimsScreen(
                itemId: _currentItem.id,
                itemTitle: _currentItem.title,
                isLost: _currentItem.isLost,
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildMarkAsReturnedButton() {
    return FilledButton.icon(
      onPressed: _showMarkAsReturnedConfirmation,
      icon: const Icon(Icons.check_circle_outline),
      label: const Text('Tandai Sudah Dikembalikan'),
      style: FilledButton.styleFrom(
        backgroundColor: AppColors.success,
        foregroundColor: Colors.white,
        minimumSize: const Size(double.infinity, AppSpacing.buttonHeight),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.md),
        ),
      ),
    );
  }

  Future<void> _showMarkAsReturnedConfirmation() async {
    final codeController = TextEditingController();
    final formKey = GlobalKey<FormState>();

    final confirmMessage = _currentItem.isLost
        ? 'Masukkan kode verifikasi dari pemilik barang untuk menyelesaikan serah-terima.'
        : 'Masukkan kode verifikasi dari pemilik barang untuk mengonfirmasi penyerahan barang.';

    final verificationCode = await showDialog<String>(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        title: const Text('Masukkan Kode Verifikasi'),
        content: Form(
          key: formKey,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                confirmMessage,
                style: AppTextStyles.bodySmall.copyWith(color: AppColors.textSecondary),
              ),
              const SizedBox(height: AppSpacing.md),
              TextFormField(
                controller: codeController,
                textCapitalization: TextCapitalization.characters,
                decoration: const InputDecoration(
                  labelText: 'Kode Verifikasi',
                  hintText: 'Contoh: ABCDEFGH',
                  border: OutlineInputBorder(),
                  prefixIcon: Icon(Icons.vpn_key),
                ),
                validator: (value) {
                  if (value == null || value.trim().isEmpty) {
                    return 'Kode verifikasi tidak boleh kosong';
                  }
                  return null;
                },
              ),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Batal'),
          ),
          FilledButton(
            onPressed: () {
              if (formKey.currentState!.validate()) {
                Navigator.pop(context, codeController.text.trim().toUpperCase());
              }
            },
            style: FilledButton.styleFrom(backgroundColor: AppColors.success),
            child: const Text('Konfirmasi'),
          ),
        ],
      ),
    );

    if (verificationCode != null && verificationCode.isNotEmpty && mounted) {
      await _markAsReturned(verificationCode);
    }
  }

  Future<void> _markAsReturned(String verificationCode) async {
    try {
      // Show loading
      showDialog(
        context: context,
        barrierDismissible: false,
        builder: (context) => const Center(child: CircularProgressIndicator()),
      );

      final itemRepository = ItemRepository();
      await itemRepository.updateStatus(
        _currentItem.id, 
        'returned', 
        verificationCode: verificationCode,
      );

      if (!mounted) return;
      Navigator.pop(context); // Close loading

      // Update local state
      setState(() {
        _currentItem = _currentItem.copyWith(status: 'returned');
        _wasEdited = true;
      });

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Barang berhasil ditandai sebagai dikembalikan'),
          backgroundColor: AppColors.success,
        ),
      );
    } catch (e) {
      if (!mounted) return;
      Navigator.pop(context); // Close loading

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            'Gagal: ${e.toString().replaceFirst("Exception: ", "")}',
          ),
          backgroundColor: AppColors.error,
        ),
      );
    }
  }

  String _getStatusLabel(String status) {
    switch (status) {
      case 'active':
        return 'Aktif';
      case 'claimed':
        return 'Diklaim';
      case 'returned':
        return 'Dikembalikan';
      default:
        return status;
    }
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'active':
        return AppColors.warning;
      case 'claimed':
        return AppColors.primary;
      case 'returned':
        return AppColors.success;
      default:
        return AppColors.textSecondary;
    }
  }

  String _getTimeAgo(DateTime dateTime) {
    final now = DateTime.now();
    final difference = now.difference(dateTime);

    if (difference.inDays > 30) {
      return DateFormat('dd MMM yyyy').format(dateTime);
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
