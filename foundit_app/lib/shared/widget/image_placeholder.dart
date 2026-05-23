import 'package:flutter/material.dart';

import '../utils/utils.dart';

/// Reusable placeholder widget for missing images
class ImagePlaceholder extends StatelessWidget {
  final double size;
  final IconData icon;
  final double? iconSize;
  final BorderRadius? borderRadius;

  const ImagePlaceholder({
    super.key,
    this.size = 60,
    this.icon = Icons.image_outlined,
    this.iconSize,
    this.borderRadius,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: size,
      height: size,
      decoration: BoxDecoration(
        color: AppColors.surfaceAlt,
        borderRadius: borderRadius,
      ),
      child: Icon(
        icon,
        color: AppColors.textTertiary,
        size: iconSize ?? size * 0.4,
      ),
    );
  }
}
