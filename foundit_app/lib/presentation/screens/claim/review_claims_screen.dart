import 'package:flutter/material.dart';

import '../../../data/model/claim_model.dart';
import '../../../data/repository/claim_repository.dart';
import '../../../shared/utils/utils.dart';
import '../../../shared/widget/widgets.dart';

/// Screen untuk review klaim yang masuk ke barang milik user
class ReviewClaimsScreen extends StatefulWidget {
  final int itemId;
  final String itemTitle;
  final bool isLost;

  const ReviewClaimsScreen({
    super.key,
    required this.itemId,
    required this.itemTitle,
    this.isLost = false,
  });

  @override
  State<ReviewClaimsScreen> createState() => _ReviewClaimsScreenState();
}

class _ReviewClaimsScreenState extends State<ReviewClaimsScreen> {
  final ClaimRepository _claimRepository = ClaimRepository();

  bool _isLoading = true;
  String? _errorMessage;
  List<ClaimModel> _claims = [];

  @override
  void initState() {
    super.initState();
    _loadClaims();
  }

  Future<void> _loadClaims() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final claims = await _claimRepository.getItemClaims(widget.itemId);
      if (mounted) {
        setState(() {
          _claims = claims;
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

  Future<void> _handleApprove(ClaimModel claim) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Setujui Klaim?'),
        content: Text(
          'Anda yakin ingin menyetujui klaim dari ${claim.claimerName ?? 'pengklaim'}?\n\n'
          'Setelah disetujui, klaim lainnya akan otomatis ditolak dan barang akan ditandai sebagai "diklaim".',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          FilledButton(
            onPressed: () => Navigator.pop(context, true),
            style: FilledButton.styleFrom(backgroundColor: AppColors.success),
            child: const Text('Setujui'),
          ),
        ],
      ),
    );

    if (confirmed == true && mounted) {
      setState(() => _isLoading = true);

      try {
        await _claimRepository.approveClaim(claim.id);
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Klaim berhasil disetujui'),
              backgroundColor: AppColors.success,
            ),
          );
          // Reload claims to get updated statuses
          _loadClaims();
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
  }

  Future<void> _handleReject(ClaimModel claim) async {
    final reasonController = TextEditingController();
    final formKey = GlobalKey<FormState>();

    final reason = await showDialog<String>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Tolak Klaim?'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Berikan alasan kenapa Anda menolak klaim dari ${claim.claimerName ?? 'pengklaim'}:',
            ),
            const SizedBox(height: AppSpacing.md),
            Form(
              key: formKey,
              child: TextFormField(
                controller: reasonController,
                decoration: const InputDecoration(
                  hintText: 'Contoh: Bukti kepemilikan kurang jelas',
                  border: OutlineInputBorder(),
                  contentPadding: EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 12,
                  ),
                ),
                maxLines: 3,
                validator: (value) {
                  if (value == null || value.trim().length < 5) {
                    return 'Alasan minimal 5 karakter';
                  }
                  return null;
                },
              ),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Batal'),
          ),
          FilledButton(
            onPressed: () {
              if (formKey.currentState!.validate()) {
                Navigator.pop(context, reasonController.text.trim());
              }
            },
            style: FilledButton.styleFrom(backgroundColor: AppColors.error),
            child: const Text('Tolak'),
          ),
        ],
      ),
    );

    if (reason != null && mounted) {
      setState(() => _isLoading = true);

      try {
        await _claimRepository.rejectClaim(claim.id, reason);
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Klaim berhasil ditolak'),
              backgroundColor: AppColors.error,
            ),
          );
          // Reload claims to get updated statuses
          _loadClaims();
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
  }

  @override
  Widget build(BuildContext context) {
    final pendingClaims = _claims.where((c) => c.isPending).toList();
    final processedClaims = _claims.where((c) => !c.isPending).toList();

    return Scaffold(
      appBar: AppBar(
        title: const Text('Review Klaim'),
        backgroundColor: AppColors.surface,
      ),
      body: _buildBody(pendingClaims, processedClaims),
    );
  }

  Widget _buildBody(
    List<ClaimModel> pendingClaims,
    List<ClaimModel> processedClaims,
  ) {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_errorMessage != null) {
      return _buildErrorState();
    }

    if (_claims.isEmpty) {
      return _buildEmptyState();
    }

    return RefreshIndicator(
      onRefresh: _loadClaims,
      child: ListView(
        padding: const EdgeInsets.all(AppSpacing.md),
        children: [
          // Item info
          _buildItemInfo(),
          const SizedBox(height: AppSpacing.lg),

          // Pending Claims
          if (pendingClaims.isNotEmpty) ...[
            SectionHeader(
              title: 'Menunggu Review',
              count: pendingClaims.length,
              color: AppColors.warning,
            ),
            const SizedBox(height: AppSpacing.sm),
            ...pendingClaims.map((claim) => _buildClaimCard(claim)),
            const SizedBox(height: AppSpacing.lg),
          ],

          // Processed Claims
          if (processedClaims.isNotEmpty) ...[
            SectionHeader(
              title: 'Sudah Diproses',
              count: processedClaims.length,
              color: AppColors.textSecondary,
            ),
            const SizedBox(height: AppSpacing.sm),
            ...processedClaims.map((claim) => _buildClaimCard(claim)),
          ],
        ],
      ),
    );
  }

  Widget _buildErrorState() {
    return ErrorState(message: _errorMessage, onRetry: _loadClaims);
  }

  Widget _buildEmptyState() {
    return const EmptyState(
      icon: Icons.inbox_outlined,
      title: 'Belum ada klaim',
      subtitle: 'Klaim untuk barang ini akan muncul di sini',
    );
  }

  Widget _buildItemInfo() {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        color: AppColors.primary.withAlpha(26),
        borderRadius: BorderRadius.circular(AppRadius.md),
        border: Border.all(color: AppColors.primary.withAlpha(51)),
      ),
      child: Row(
        children: [
          Icon(Icons.inventory_2_outlined, color: AppColors.primary, size: 24),
          const SizedBox(width: AppSpacing.md),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Barang:',
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
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildClaimCard(ClaimModel claim) {
    return Container(
      margin: const EdgeInsets.only(bottom: AppSpacing.md),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadius.md),
        border: Border.all(
          color: claim.isApproved
              ? AppColors.success.withAlpha(128)
              : claim.isRejected
              ? AppColors.error.withAlpha(128)
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
          // Header with status
          Container(
            padding: const EdgeInsets.all(AppSpacing.md),
            decoration: BoxDecoration(
              color: claim.isApproved
                  ? AppColors.success.withAlpha(26)
                  : claim.isRejected
                  ? AppColors.error.withAlpha(26)
                  : AppColors.surfaceAlt,
              borderRadius: const BorderRadius.vertical(
                top: Radius.circular(AppRadius.md),
              ),
            ),
            child: Row(
              children: [
                // Avatar
                CircleAvatar(
                  radius: 20,
                  backgroundColor: AppColors.primary.withAlpha(51),
                  child: Text(
                    claim.claimerInitial,
                    style: AppTextStyles.body.copyWith(
                      fontWeight: FontWeight.w600,
                      color: AppColors.primary,
                    ),
                  ),
                ),
                const SizedBox(width: AppSpacing.md),
                // Claimer info
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        claim.claimerName ?? 'Pengklaim',
                        style: AppTextStyles.body.copyWith(
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                      if (claim.claimerProdiUnit != null)
                        Text(
                          claim.claimerProdiUnit!,
                          style: AppTextStyles.caption.copyWith(
                            color: AppColors.textSecondary,
                          ),
                        ),
                    ],
                  ),
                ),
                // Status badge
                _buildStatusBadge(claim),
              ],
            ),
          ),

          // Reason
          Padding(
            padding: const EdgeInsets.all(AppSpacing.md),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Alasan Klaim:',
                  style: AppTextStyles.caption.copyWith(
                    color: AppColors.textSecondary,
                    fontWeight: FontWeight.w500,
                  ),
                ),
                const SizedBox(height: AppSpacing.xs),
                Text(claim.reason, style: AppTextStyles.body),
                const SizedBox(height: AppSpacing.md),
                const SizedBox(height: AppSpacing.md),

                // Rejection Reason
                if (claim.isRejected && claim.rejectionReason != null) ...[
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(AppSpacing.md),
                    decoration: BoxDecoration(
                      color: AppColors.error.withAlpha(15),
                      borderRadius: BorderRadius.circular(AppRadius.sm),
                      border: Border.all(color: AppColors.error.withAlpha(30)),
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

                // Time
                Row(
                  children: [
                    Icon(
                      Icons.access_time,
                      size: 14,
                      color: AppColors.textSecondary,
                    ),
                    const SizedBox(width: AppSpacing.xs),
                    Text(
                      _formatTime(claim.createdAt),
                      style: AppTextStyles.caption.copyWith(
                        color: AppColors.textSecondary,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),

          // Action buttons (only for pending)
          if (claim.isPending) ...[
            const Divider(height: 1),
            Padding(
              padding: const EdgeInsets.all(AppSpacing.md),
              child: Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: () => _handleReject(claim),
                      icon: const Icon(Icons.close, size: 18),
                      label: const Text('Tolak'),
                      style: OutlinedButton.styleFrom(
                        foregroundColor: AppColors.error,
                        side: const BorderSide(color: AppColors.error),
                        minimumSize: const Size(0, 48),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(AppRadius.md),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: AppSpacing.md),
                  Expanded(
                    child: FilledButton.icon(
                      onPressed: () => _handleApprove(claim),
                      icon: const Icon(Icons.check, size: 18),
                      label: const Text('Setujui'),
                      style: FilledButton.styleFrom(
                        backgroundColor: AppColors.success,
                        minimumSize: const Size(0, 48),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(AppRadius.md),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],

          // Contact info for approved claims
          if (claim.isApproved && claim.claimerPhone != null) ...[
            const Divider(height: 1),
            Container(
              padding: const EdgeInsets.all(AppSpacing.md),
              decoration: BoxDecoration(
                color: AppColors.success.withAlpha(13),
                borderRadius: const BorderRadius.vertical(
                  bottom: Radius.circular(AppRadius.md),
                ),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Icon(Icons.phone, size: 16, color: AppColors.success),
                      const SizedBox(width: AppSpacing.xs),
                      Text(
                        widget.isLost ? 'Kontak Penemu:' : 'Kontak Pemilik:',
                        style: AppTextStyles.caption.copyWith(
                          color: AppColors.success,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: AppSpacing.xs),
                  Text(
                    claim.claimerPhone!,
                    style: AppTextStyles.body.copyWith(
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(height: AppSpacing.sm),
                  Row(
                    children: [
                      Expanded(
                        child: OutlinedButton.icon(
                          onPressed: () => UrlLauncherHelper.launchWhatsApp(
                            context: context,
                            phone: claim.claimerPhone!,
                            message: widget.isLost
                                ? 'Halo, saya pemilik barang "${widget.itemTitle}" di FoundIt. Kapan bisa kita koordinasi pengembalian?'
                                : 'Halo, saya menemukan barang "${widget.itemTitle}" yang Anda klaim di FoundIt. Kapan bisa kita koordinasi pengambilan?',
                          ),
                          icon: const Icon(Icons.chat, size: 16),
                          label: const Text('WhatsApp'),
                          style: OutlinedButton.styleFrom(
                            foregroundColor: const Color(0xFF25D366),
                            side: const BorderSide(color: Color(0xFF25D366)),
                            padding: const EdgeInsets.symmetric(vertical: 8),
                          ),
                        ),
                      ),
                      const SizedBox(width: AppSpacing.sm),
                      Expanded(
                        child: OutlinedButton.icon(
                          onPressed: () => UrlLauncherHelper.launchPhone(
                            context: context,
                            phone: claim.claimerPhone!,
                          ),
                          icon: const Icon(Icons.phone, size: 16),
                          label: const Text('Telepon'),
                          style: OutlinedButton.styleFrom(
                            foregroundColor: AppColors.primary,
                            side: const BorderSide(color: AppColors.primary),
                            padding: const EdgeInsets.symmetric(vertical: 8),
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildStatusBadge(ClaimModel claim) {
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
        horizontal: AppSpacing.sm,
        vertical: AppSpacing.xs,
      ),
      decoration: BoxDecoration(
        color: color.withAlpha(26),
        borderRadius: BorderRadius.circular(AppRadius.full),
        border: Border.all(color: color.withAlpha(77)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: color),
          const SizedBox(width: 4),
          Text(
            text,
            style: AppTextStyles.caption.copyWith(
              color: color,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      ),
    );
  }

  String _formatTime(DateTime dateTime) {
    final now = DateTime.now();
    final difference = now.difference(dateTime);

    if (difference.inMinutes < 60) {
      return '${difference.inMinutes} menit lalu';
    } else if (difference.inHours < 24) {
      return '${difference.inHours} jam lalu';
    } else {
      return '${difference.inDays} hari lalu';
    }
  }
}
