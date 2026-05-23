import 'package:flutter/material.dart';

import '../utils/app_colors.dart';
import '../utils/app_spacing.dart';
import '../utils/app_text_styles.dart';

/// Badge widget to display item type (lost or found)
///
/// Usage:
/// ```dart
/// ItemTypeBadge(isLost: true)
/// ItemTypeBadge(isLost: false)
/// ```
class ItemTypeBadge extends StatelessWidget {
  final bool isLost;

  const ItemTypeBadge({super.key, required this.isLost});

  @override
  Widget build(BuildContext context) {
    final color = isLost ? AppColors.lostBadge : AppColors.foundBadge;
    final icon = isLost ? Icons.search_off : Icons.search;
    final text = isLost ? 'Barang Hilang' : 'Barang Temuan';

    return Container(
      padding: const EdgeInsets.symmetric(
        horizontal: AppSpacing.md,
        vertical: AppSpacing.sm,
      ),
      decoration: BoxDecoration(
        color: color.withAlpha(25),
        borderRadius: BorderRadius.circular(AppRadius.md),
        border: Border.all(color: color.withAlpha(76)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, color: color, size: 20),
          const SizedBox(width: AppSpacing.sm),
          Text(
            text,
            style: AppTextStyles.bodySmall.copyWith(
              color: color,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      ),
    );
  }
}
