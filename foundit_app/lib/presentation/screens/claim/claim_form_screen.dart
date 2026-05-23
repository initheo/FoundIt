import 'package:flutter/material.dart';

import '../../../data/model/item_model.dart';
import '../../../data/repository/claim_repository.dart';
import '../../../shared/utils/utils.dart';
import '../../../shared/widget/widgets.dart';

/// Screen untuk mengklaim barang (temuan/hilang)
class ClaimFormScreen extends StatefulWidget {
  final ItemModel item;

  const ClaimFormScreen({super.key, required this.item});

  @override
  State<ClaimFormScreen> createState() => _ClaimFormScreenState();
}

class _ClaimFormScreenState extends State<ClaimFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _reasonController = TextEditingController();
  final _claimRepository = ClaimRepository();
  bool _isLoading = false;

  @override
  void dispose() {
    _reasonController.dispose();
    super.dispose();
  }

  Future<void> _handleSubmit() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isLoading = true);

    try {
      await _claimRepository.submitClaim(
        itemId: widget.item.id,
        reason: _reasonController.text.trim(),
      );

      if (!mounted) return;
      setState(() => _isLoading = false);

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            widget.item.isLost
                ? 'Laporan temuan berhasil dikirim!'
                : 'Klaim berhasil dikirim!',
          ),
          backgroundColor: AppColors.success,
        ),
      );

      Navigator.pop(context, true);
    } catch (e) {
      if (!mounted) return;
      setState(() => _isLoading = false);

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(e.toString().replaceFirst('Exception: ', '')),
          backgroundColor: AppColors.error,
        ),
      );
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
          widget.item.isLost ? 'Laporkan Temuan' : 'Klaim Barang',
          style: TextStyle(
            color: AppColors.textPrimary,
            fontSize: 18,
            fontWeight: FontWeight.w600,
          ),
        ),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.md),
          child: Form(
            key: _formKey,
            autovalidateMode: AutovalidateMode.onUserInteraction,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Item Preview Card
                _buildItemPreview(),
                const SizedBox(height: AppSpacing.lg),

                // Claim Reason Field
                _buildReasonField(),
                const SizedBox(height: AppSpacing.md),

                // Info Notice
                _buildInfoNotice(),
                const SizedBox(height: AppSpacing.xl),

                // Submit Button
                PrimaryButton(
                  text: widget.item.isLost ? 'Laporkan Temuan' : 'Kirim Klaim',
                  onPressed: _handleSubmit,
                  isLoading: _isLoading,
                ),
                const SizedBox(height: AppSpacing.lg),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildItemPreview() {
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
      child: Row(
        children: [
          // Item Image
          ClipRRect(
            borderRadius: BorderRadius.circular(AppRadius.md),
            child: Container(
              width: 80,
              height: 80,
              color: AppColors.surfaceAlt,
              child: widget.item.photoUrl != null
                  ? Image.network(
                      AppConstants.getFullImageUrl(widget.item.photoUrl)!,
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) {
                        return _buildPlaceholderImage();
                      },
                    )
                  : _buildPlaceholderImage(),
            ),
          ),
          const SizedBox(width: AppSpacing.md),
          // Item Info
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  widget.item.title,
                  style: AppTextStyles.h3,
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: AppSpacing.xs),
                Row(
                  children: [
                    Icon(
                      Icons.category_outlined,
                      size: 14,
                      color: AppColors.textTertiary,
                    ),
                    const SizedBox(width: 4),
                    Text(
                      widget.item.categoryName ?? 'Uncategorized',
                      style: AppTextStyles.caption.copyWith(
                        color: AppColors.textTertiary,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 4),
                Row(
                  children: [
                    Icon(
                      Icons.location_on_outlined,
                      size: 14,
                      color: AppColors.textTertiary,
                    ),
                    const SizedBox(width: 4),
                    Expanded(
                      child: Text(
                        widget.item.location,
                        style: AppTextStyles.caption.copyWith(
                          color: AppColors.textTertiary,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPlaceholderImage() {
    return Center(
      child: Icon(
        Icons.inventory_2_outlined,
        size: 32,
        color: AppColors.textTertiary,
      ),
    );
  }

  Widget _buildReasonField() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text('Alasan Klaim', style: AppTextStyles.h3),
        const SizedBox(height: AppSpacing.xs),
        Text(
          widget.item.isLost
              ? 'Jelaskan dimana dan bagaimana kamu menemukannya'
              : 'Jelaskan kenapa barang ini milik kamu',
          style: AppTextStyles.bodySmall.copyWith(
            color: AppColors.textSecondary,
          ),
        ),
        const SizedBox(height: AppSpacing.md),
        TextFormField(
          controller: _reasonController,
          maxLines: 6,
          style: AppTextStyles.input,
          decoration: InputDecoration(
            hintText: widget.item.isLost
                ? 'Contoh: Saya menemukan barang ini di Gedung A lantai 2 dekat toilet sekitar jam 10 pagi. Barang dalam kondisi baik dan saya simpan di satpam.'
                : 'Contoh: Ini adalah iPhone saya yang hilang di Gedung A minggu lalu. Ciri-ciri: case warna biru, ada goresan kecil di pojok kanan bawah. Lock screen berupa foto kucing saya.',
            hintMaxLines: 4,
            alignLabelWithHint: true,
          ),
          validator: (value) {
            if (value == null || value.isEmpty) {
              return 'Alasan klaim wajib diisi';
            }
            if (value.length < 20) {
              return 'Alasan klaim minimal 20 karakter';
            }
            return null;
          },
        ),
      ],
    );
  }

  Widget _buildInfoNotice() {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        color: AppColors.warning.withAlpha(25),
        borderRadius: BorderRadius.circular(AppRadius.md),
        border: Border.all(color: AppColors.warning.withAlpha(76)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(Icons.info_outline, color: AppColors.warning, size: 20),
          const SizedBox(width: AppSpacing.sm),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Informasi Penting',
                  style: AppTextStyles.bodySmall.copyWith(
                    color: AppColors.warning,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  widget.item.isLost
                      ? 'Pemilik barang akan melihat laporan temuan kamu untuk verifikasi. Pastikan kamu memberikan detail lokasi dan kondisi barang saat ditemukan.'
                      : 'Penemu akan melihat alasan klaim kamu untuk memverifikasi kepemilikan. Pastikan kamu memberikan ciri-ciri spesifik yang hanya pemilik asli yang tahu.',
                  style: AppTextStyles.caption.copyWith(
                    color: AppColors.textSecondary,
                    height: 1.4,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
