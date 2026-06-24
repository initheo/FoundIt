import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import '../../../data/model/claim_model.dart';
import '../../../data/model/item_model.dart';
import '../../../data/repository/claim_repository.dart';
import '../../../data/repository/item_repository.dart';
import '../../../shared/utils/utils.dart';
import '../../../shared/widget/widgets.dart';
import '../item/item_detail_screen.dart';

/// Screen untuk menampilkan riwayat klaim user untuk satu item
class MyClaimHistoryScreen extends StatefulWidget {
  final int itemId;
  final String itemTitle;
  final String? itemPhotoUrl;

  const MyClaimHistoryScreen({
    super.key,
    required this.itemId,
    required this.itemTitle,
    this.itemPhotoUrl,
  });

  @override
  State<MyClaimHistoryScreen> createState() => _MyClaimHistoryScreenState();
}

class _MyClaimHistoryScreenState extends State<MyClaimHistoryScreen> {
  final ClaimRepository _claimRepository = ClaimRepository();
  final ItemRepository _itemRepository = ItemRepository();
  bool _isLoading = true;
  List<ClaimModel> _claims = [];
  ItemModel? _itemData;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    await Future.wait([_loadClaims(), _loadItemData()]);
  }

  Future<void> _loadClaims() async {
    try {
      final allClaims = await _claimRepository.myClaims();
      // Filter claims for this item
      final itemClaims = allClaims
          .where((c) => c.itemId == widget.itemId)
          .toList();
      // Sort by createdAt descending (newest first)
      itemClaims.sort((a, b) => b.createdAt.compareTo(a.createdAt));

      if (mounted) {
        setState(() {
          _claims = itemClaims;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(e.toString().replaceFirst('Exception: ', '')),
            backgroundColor: AppColors.error,
          ),
        );
      }
    }
  }

  Future<void> _loadItemData() async {
    try {
      final item = await _itemRepository.getItem(widget.itemId);
      if (mounted) {
        setState(() {
          _itemData = item;
        });
      }
    } catch (e) {
      // Ignore error - item data is optional for contact info
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: AppColors.textPrimary),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Riwayat Klaim',
          style: AppTextStyles.h3.copyWith(color: AppColors.textPrimary),
        ),
        centerTitle: false,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _claims.isEmpty
          ? _buildEmptyState()
          : _buildClaimsList(),
    );
  }

  Widget _buildEmptyState() {
    return const EmptyState(
      icon: Icons.history_outlined,
      title: 'Belum Ada Riwayat',
      subtitle: 'Riwayat klaim untuk barang ini akan muncul di sini',
    );
  }

  Widget _buildClaimsList() {
    // Check if any claim is approved
    final hasApprovedClaim = _claims.any((c) => c.isApproved);

    return RefreshIndicator(
      onRefresh: _loadData,
      child: ListView(
        padding: const EdgeInsets.all(AppSpacing.md),
        children: [
          _buildItemInfo(),

          // Show finder contact if claim is approved
          if (hasApprovedClaim && _itemData != null) ...[
            const SizedBox(height: AppSpacing.md),
            _buildFinderContactSection(),
          ],

          const SizedBox(height: AppSpacing.lg),

          // Section header with colored bar
          SectionHeader(
            title: 'Riwayat Klaim',
            count: _claims.length,
            color: AppColors.primary,
          ),
          const SizedBox(height: AppSpacing.md),

          // Claims list as timeline
          ...List.generate(_claims.length, (index) {
            final claim = _claims[index];
            final isLast = index == _claims.length - 1;
            return _buildClaimTimelineItem(claim, isLast, index);
          }),
        ],
      ),
    );
  }

  Widget _buildItemInfo() {
    return GestureDetector(
      onTap: () {
        if (_itemData != null) {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => ItemDetailScreen(item: _itemData!),
            ),
          );
        }
      },
      child: Container(
        padding: const EdgeInsets.all(AppSpacing.md),
        decoration: BoxDecoration(
          color: AppColors.primary.withAlpha(26),
          borderRadius: BorderRadius.circular(AppRadius.md),
          border: Border.all(color: AppColors.primary.withAlpha(51)),
        ),
        child: Row(
          children: [
            // Item photo
            ClipRRect(
              borderRadius: BorderRadius.circular(AppRadius.sm),
              child: widget.itemPhotoUrl != null
                  ? Image.network(
                      AppConstants.getFullImageUrl(widget.itemPhotoUrl)!,
                      width: 50,
                      height: 50,
                      fit: BoxFit.cover,
                      errorBuilder: (c, e, s) =>
                          const ImagePlaceholder(size: 50),
                    )
                  : const ImagePlaceholder(size: 50),
            ),
            const SizedBox(width: AppSpacing.md),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    _itemData?.isLost == true
                        ? 'Barang Ditemukan:'
                        : 'Barang Diklaim:',
                    style: AppTextStyles.caption.copyWith(
                      color: AppColors.textSecondary,
                    ),
                  ),
                  Text(
                    widget.itemTitle,
                    style: AppTextStyles.body.copyWith(
                      fontWeight: FontWeight.w600,
                      color: AppColors.primary,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
            ),
            const Icon(Icons.chevron_right, color: AppColors.primary),
          ],
        ),
      ),
    );
  }

  Widget _buildFinderContactSection() {
    final approvedClaim = _claims.firstWhere((c) => c.isApproved);
    final verificationCode = approvedClaim.verificationCode;

    return Container(
      decoration: BoxDecoration(
        color: AppColors.success.withAlpha(26),
        borderRadius: BorderRadius.circular(AppRadius.md),
        border: Border.all(color: AppColors.success.withAlpha(77)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Success header
          Padding(
            padding: const EdgeInsets.all(AppSpacing.md),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(6),
                  decoration: BoxDecoration(
                    color: AppColors.success.withAlpha(51),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.check,
                    color: AppColors.success,
                    size: 16,
                  ),
                ),
                const SizedBox(width: AppSpacing.sm),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Klaim Diterima!',
                        style: AppTextStyles.body.copyWith(
                          color: AppColors.success,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                      Text(
                        _itemData?.isLost == true
                            ? 'Hubungi pemilik untuk menyerahkan barang'
                            : 'Hubungi penemu untuk mengambil barang',
                        style: AppTextStyles.caption.copyWith(
                          color: AppColors.success.withAlpha(179),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),

          // Divider
          Container(height: 1, color: AppColors.success.withAlpha(51)),

          // Kode verifikasi hanya ditampilkan kepada Pemilik Barang (Claimer untuk barang 'found')
          if (_itemData?.isLost == false && verificationCode != null && verificationCode.isNotEmpty) ...[
            Container(
              margin: const EdgeInsets.all(AppSpacing.md),
              padding: const EdgeInsets.all(AppSpacing.md),
              decoration: BoxDecoration(
                color: AppColors.primary.withAlpha(15),
                borderRadius: BorderRadius.circular(AppRadius.sm),
                border: Border.all(color: AppColors.primary.withAlpha(51)),
              ),
              child: Column(
                children: [
                  Text(
                    'KODE VERIFIKASI PENGEMBALIAN',
                    style: AppTextStyles.caption.copyWith(
                      color: AppColors.primary,
                      fontWeight: FontWeight.w700,
                      fontSize: 10,
                      letterSpacing: 1.2,
                    ),
                  ),
                  const SizedBox(height: AppSpacing.xs),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(
                        verificationCode,
                        style: AppTextStyles.h1.copyWith(
                          color: AppColors.primary,
                          fontSize: 24,
                          fontWeight: FontWeight.w800,
                          letterSpacing: 4,
                        ),
                      ),
                      const SizedBox(width: AppSpacing.sm),
                      IconButton(
                        icon: const Icon(Icons.copy, size: 20, color: AppColors.primary),
                        onPressed: () {
                          Clipboard.setData(ClipboardData(text: verificationCode));
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(
                              content: Text('Kode verifikasi disalin'),
                              duration: Duration(seconds: 1),
                            ),
                          );
                        },
                        constraints: const BoxConstraints(),
                        padding: EdgeInsets.zero,
                      ),
                    ],
                  ),
                  const SizedBox(height: AppSpacing.xs),
                  Text(
                    'Tunjukkan kode ini saat melakukan serah-terima barang',
                    style: AppTextStyles.caption.copyWith(
                      color: AppColors.textSecondary,
                      fontSize: 10,
                    ),
                    textAlign: TextAlign.center,
                  ),
                ],
              ),
            ),
            Container(height: 1, color: AppColors.success.withAlpha(51)),
          ],

          // Contact info
          Container(
            padding: const EdgeInsets.all(AppSpacing.md),
            decoration: const BoxDecoration(
              color: AppColors.surface,
              borderRadius: BorderRadius.only(
                bottomLeft: Radius.circular(AppRadius.md),
                bottomRight: Radius.circular(AppRadius.md),
              ),
            ),
            child: Column(
              children: [
                // Name row
                Row(
                  children: [
                    Container(
                      width: 36,
                      height: 36,
                      decoration: BoxDecoration(
                        color: AppColors.primary.withAlpha(26),
                        borderRadius: BorderRadius.circular(AppRadius.sm),
                      ),
                      child: const Icon(
                        Icons.person_outline,
                        color: AppColors.primary,
                        size: 20,
                      ),
                    ),
                    const SizedBox(width: AppSpacing.sm),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            _itemData?.isLost == true
                                ? 'Nama Pemilik'
                                : 'Nama Penemu',
                            style: AppTextStyles.caption.copyWith(
                              color: AppColors.textTertiary,
                              fontSize: 11,
                            ),
                          ),
                          Text(
                            _itemData?.userName ??
                                (_itemData?.isLost == true
                                    ? 'Pemilik'
                                    : 'Penemu'),
                            style: AppTextStyles.body.copyWith(
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: AppSpacing.md),

                // Phone row
                Row(
                  children: [
                    Container(
                      width: 36,
                      height: 36,
                      decoration: BoxDecoration(
                        color: AppColors.success.withAlpha(26),
                        borderRadius: BorderRadius.circular(AppRadius.sm),
                      ),
                      child: const Icon(
                        Icons.phone_outlined,
                        color: AppColors.success,
                        size: 20,
                      ),
                    ),
                    const SizedBox(width: AppSpacing.sm),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Nomor Telepon',
                            style: AppTextStyles.caption.copyWith(
                              color: AppColors.textTertiary,
                              fontSize: 11,
                            ),
                          ),
                          Text(
                            _itemData?.userPhone ?? 'Tidak tersedia',
                            style: AppTextStyles.body.copyWith(
                              fontWeight: FontWeight.w600,
                              color: _itemData?.userPhone != null
                                  ? AppColors.textPrimary
                                  : AppColors.textTertiary,
                            ),
                          ),
                        ],
                      ),
                    ),
                    if (_itemData?.userPhone != null)
                      Material(
                        color: Colors.transparent,
                        child: InkWell(
                          onTap: () {
                            Clipboard.setData(
                              ClipboardData(text: _itemData!.userPhone!),
                            );
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(
                                content: Text('Nomor telepon disalin'),
                                duration: Duration(seconds: 1),
                              ),
                            );
                          },
                          borderRadius: BorderRadius.circular(AppRadius.sm),
                          child: Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: AppSpacing.sm,
                              vertical: AppSpacing.xs,
                            ),
                            decoration: BoxDecoration(
                              color: AppColors.primary.withAlpha(26),
                              borderRadius: BorderRadius.circular(AppRadius.sm),
                            ),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                const Icon(
                                  Icons.copy,
                                  size: 14,
                                  color: AppColors.primary,
                                ),
                                const SizedBox(width: 4),
                                Text(
                                  'Salin',
                                  style: AppTextStyles.caption.copyWith(
                                    color: AppColors.primary,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                  ],
                ),

                // WhatsApp and Phone buttons
                if (_itemData?.userPhone != null) ...[
                  const SizedBox(height: AppSpacing.md),
                  Row(
                    children: [
                      Expanded(
                        child: OutlinedButton.icon(
                          onPressed: () => UrlLauncherHelper.launchWhatsApp(
                            context: context,
                            phone: _itemData!.userPhone!,
                            message: _itemData?.isLost == true
                                ? 'Halo, saya menemukan barang "${_itemData?.title ?? 'barang'}" yang Anda laporkan hilang di FoundIt. Kapan bisa saya serahkan?'
                                : 'Halo, saya ingin mengambil barang "${_itemData?.title ?? 'barang'}" yang sudah diklaim di FoundIt.',
                          ),
                          icon: const Icon(Icons.chat, size: 18),
                          label: const Text('WhatsApp'),
                          style: OutlinedButton.styleFrom(
                            foregroundColor: const Color(0xFF25D366),
                            side: const BorderSide(color: Color(0xFF25D366)),
                            padding: const EdgeInsets.symmetric(vertical: 10),
                          ),
                        ),
                      ),
                      const SizedBox(width: AppSpacing.sm),
                      Expanded(
                        child: OutlinedButton.icon(
                          onPressed: () => UrlLauncherHelper.launchPhone(
                            context: context,
                            phone: _itemData!.userPhone!,
                          ),
                          icon: const Icon(Icons.phone, size: 18),
                          label: const Text('Telepon'),
                          style: OutlinedButton.styleFrom(
                            foregroundColor: AppColors.primary,
                            side: const BorderSide(color: AppColors.primary),
                            padding: const EdgeInsets.symmetric(vertical: 10),
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildClaimTimelineItem(ClaimModel claim, bool isLast, int index) {
    final statusInfo = _getStatusInfo(claim);
    final isLatest = index == 0;

    return IntrinsicHeight(
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Timeline line and dot
          SizedBox(
            width: 24,
            child: Column(
              children: [
                Container(
                  width: 12,
                  height: 12,
                  decoration: BoxDecoration(
                    color: statusInfo.color,
                    shape: BoxShape.circle,
                  ),
                ),
                if (!isLast)
                  Expanded(child: Container(width: 2, color: AppColors.border)),
              ],
            ),
          ),
          const SizedBox(width: AppSpacing.sm),

          // Claim card
          Expanded(
            child: Container(
              margin: EdgeInsets.only(bottom: isLast ? 0 : AppSpacing.md),
              padding: const EdgeInsets.all(AppSpacing.md),
              decoration: BoxDecoration(
                color: isLatest
                    ? statusInfo.color.withAlpha(13)
                    : AppColors.surface,
                borderRadius: BorderRadius.circular(AppRadius.md),
                border: Border.all(
                  color: isLatest
                      ? statusInfo.color.withAlpha(77)
                      : AppColors.border,
                ),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withAlpha(13),
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Status and date
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: AppSpacing.sm,
                          vertical: 2,
                        ),
                        decoration: BoxDecoration(
                          color: statusInfo.color.withAlpha(26),
                          borderRadius: BorderRadius.circular(AppRadius.full),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(
                              statusInfo.icon,
                              size: 12,
                              color: statusInfo.color,
                            ),
                            const SizedBox(width: 4),
                            Text(
                              statusInfo.text,
                              style: AppTextStyles.caption.copyWith(
                                color: statusInfo.color,
                                fontWeight: FontWeight.w600,
                                fontSize: 11,
                              ),
                            ),
                          ],
                        ),
                      ),
                      if (isLatest)
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: AppSpacing.xs,
                            vertical: 2,
                          ),
                          decoration: BoxDecoration(
                            color: AppColors.primary.withAlpha(26),
                            borderRadius: BorderRadius.circular(AppRadius.sm),
                          ),
                          child: Text(
                            'Terbaru',
                            style: AppTextStyles.caption.copyWith(
                              color: AppColors.primary,
                              fontWeight: FontWeight.w600,
                              fontSize: 10,
                            ),
                          ),
                        ),
                    ],
                  ),
                  const SizedBox(height: AppSpacing.sm),

                  // Reason
                  Text(
                    claim.reason,
                    style: AppTextStyles.body.copyWith(fontSize: 13),
                    maxLines: 3,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: AppSpacing.sm),

                  // Rejection Reason
                  if (claim.isRejected && claim.rejectionReason != null) ...[
                    Container(
                      width: double.infinity,
                      margin: const EdgeInsets.only(top: AppSpacing.sm),
                      padding: const EdgeInsets.all(AppSpacing.md),
                      decoration: BoxDecoration(
                        color: AppColors.error.withAlpha(15),
                        borderRadius: BorderRadius.circular(AppRadius.sm),
                        border: Border.all(
                          color: AppColors.error.withAlpha(30),
                        ),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              const Icon(
                                Icons.info_outline,
                                size: 14,
                                color: AppColors.error,
                              ),
                              const SizedBox(width: 6),
                              Text(
                                'Alasan Penolakan:',
                                style: AppTextStyles.caption.copyWith(
                                  color: AppColors.error,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 6),
                          Text(
                            claim.rejectionReason!,
                            style: AppTextStyles.body.copyWith(
                              fontSize: 13,
                              color: AppColors.textPrimary,
                              height: 1.4,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: AppSpacing.md),
                  ],

                  // Date
                  Text(
                    DateFormatters.formatTimeAgo(claim.createdAt),
                    style: AppTextStyles.caption.copyWith(
                      color: AppColors.textTertiary,
                      fontSize: 11,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  ({Color color, IconData icon, String text}) _getStatusInfo(ClaimModel claim) {
    if (claim.isApproved) {
      return (
        color: AppColors.success,
        icon: Icons.check_circle,
        text: 'Disetujui',
      );
    } else if (claim.isRejected) {
      return (color: AppColors.error, icon: Icons.cancel, text: 'Ditolak');
    }
    return (color: AppColors.warning, icon: Icons.schedule, text: 'Menunggu');
  }
}
