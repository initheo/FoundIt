import 'package:flutter/material.dart';

/// Color constants for FoundIt app
class AppColors {
  // ============ BRAND COLORS ============
  /// UISI Red - Primary brand color (soft version)
  static const Color primary = Color(0xFFE53E3E);

  /// Darker shade of primary for pressed/active states
  static const Color primaryDark = Color(0xFFC53030);

  /// Secondary color for headers and important text
  static const Color secondary = Color(0xFF2D3748);

  // ============ BACKGROUNDS ============
  /// Main background - neutral, comfortable for eyes
  static const Color background = Color(0xFFFAFAFA);

  /// Surface color for cards, inputs, modals
  static const Color surface = Color(0xFFFFFFFF);

  /// Alternative surface for secondary backgrounds
  static const Color surfaceAlt = Color(0xFFF7FAFC);

  /// Border and divider color
  static const Color border = Color(0xFFE2E8F0);

  // ============ TEXT COLORS ============
  /// Primary text - high contrast for readability
  static const Color textPrimary = Color(0xFF1A202C);

  /// Secondary text for subtitles and hints
  static const Color textSecondary = Color(0xFF718096);

  /// Tertiary text for placeholders and disabled
  static const Color textTertiary = Color(0xFFA0AEC0);

  // ============ STATUS COLORS ============
  /// Success state - returned, approved
  static const Color success = Color(0xFF38A169);

  /// Warning state - pending
  static const Color warning = Color(0xFFDD6B20);

  /// Error state - rejected, error messages
  static const Color error = Color(0xFFE53E3E);

  // ============ BADGE COLORS ============
  /// Found item badge - Blue
  static const Color foundBadge = Color(0xFF3182CE);

  /// Lost item badge - UISI Red
  static const Color lostBadge = Color(0xFFE53E3E);

  /// Pending status badge - Orange
  static const Color pendingBadge = Color(0xFFDD6B20);

  /// Approved/Returned status badge - Green
  static const Color approvedBadge = Color(0xFF38A169);

  // ============ UTILITY COLORS ============
  /// White color
  static const Color white = Color(0xFFFFFFFF);

  /// Transparent color
  static const Color transparent = Colors.transparent;
}
