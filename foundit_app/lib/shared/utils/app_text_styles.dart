import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

import 'app_colors.dart';

/// Typography constants for FoundIt app
class AppTextStyles {
  // ============ HEADINGS (Poppins) ============
  /// H1 - Screen titles
  /// Size: 28px, Weight: Bold (700)
  static TextStyle get h1 => GoogleFonts.poppins(
    fontSize: 28,
    fontWeight: FontWeight.w700,
    color: AppColors.textPrimary,
    height: 1.3,
  );

  /// H2 - Section headers
  /// Size: 22px, Weight: SemiBold (600)
  static TextStyle get h2 => GoogleFonts.poppins(
    fontSize: 22,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
    height: 1.3,
  );

  /// H3 - Card titles
  /// Size: 18px, Weight: SemiBold (600)
  static TextStyle get h3 => GoogleFonts.poppins(
    fontSize: 18,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
    height: 1.3,
  );

  // ============ BODY TEXT (Inter) ============
  /// Body - Main body text
  /// Size: 16px, Weight: Regular (400)
  static TextStyle get body => GoogleFonts.inter(
    fontSize: 16,
    fontWeight: FontWeight.w400,
    color: AppColors.textPrimary,
    height: 1.5,
  );

  /// Body Secondary - Subtitles with secondary color
  /// Size: 16px, Weight: Regular (400)
  static TextStyle get bodySecondary => GoogleFonts.inter(
    fontSize: 16,
    fontWeight: FontWeight.w400,
    color: AppColors.textSecondary,
    height: 1.5,
  );

  /// Body Small - Descriptions
  /// Size: 14px, Weight: Regular (400)
  static TextStyle get bodySmall => GoogleFonts.inter(
    fontSize: 14,
    fontWeight: FontWeight.w400,
    color: AppColors.textPrimary,
    height: 1.5,
  );

  /// Body Small Secondary
  /// Size: 14px, Weight: Regular (400)
  static TextStyle get bodySmallSecondary => GoogleFonts.inter(
    fontSize: 14,
    fontWeight: FontWeight.w400,
    color: AppColors.textSecondary,
    height: 1.5,
  );

  // ============ CAPTION & LABELS ============
  /// Caption - Timestamps, hints
  /// Size: 12px, Weight: Regular (400)
  static TextStyle get caption => GoogleFonts.inter(
    fontSize: 12,
    fontWeight: FontWeight.w400,
    color: AppColors.textTertiary,
    height: 1.4,
  );

  // ============ BUTTON & NAV (Poppins) ============
  /// Button - Button labels
  /// Size: 16px, Weight: SemiBold (600)
  static TextStyle get button => GoogleFonts.poppins(
    fontSize: 16,
    fontWeight: FontWeight.w600,
    color: AppColors.white,
    height: 1.2,
  );

  /// Tab - Tab labels
  /// Size: 14px, Weight: Medium (500)
  static TextStyle get tab => GoogleFonts.inter(
    fontSize: 14,
    fontWeight: FontWeight.w500,
    color: AppColors.textSecondary,
    height: 1.2,
  );

  // ============ INPUT & FORM ============
  /// Input - Input field text
  /// Size: 16px, Weight: Regular (400)
  static TextStyle get input => GoogleFonts.inter(
    fontSize: 16,
    fontWeight: FontWeight.w400,
    color: AppColors.textPrimary,
    height: 1.4,
  );

  /// Input Hint - Placeholder text
  /// Size: 16px, Weight: Regular (400)
  static TextStyle get inputHint => GoogleFonts.inter(
    fontSize: 16,
    fontWeight: FontWeight.w400,
    color: AppColors.textTertiary,
    height: 1.4,
  );

  /// Input Label - Form labels
  /// Size: 14px, Weight: Medium (500)
  static TextStyle get inputLabel => GoogleFonts.inter(
    fontSize: 14,
    fontWeight: FontWeight.w500,
    color: AppColors.textPrimary,
    height: 1.4,
  );

  /// Input Error - Error message text
  /// Size: 12px, Weight: Regular (400)
  static TextStyle get inputError => GoogleFonts.inter(
    fontSize: 12,
    fontWeight: FontWeight.w400,
    color: AppColors.error,
    height: 1.4,
  );

  // ============ BADGE ============
  /// Badge - Status badge text
  /// Size: 12px, Weight: SemiBold (600)
  static TextStyle get badge => GoogleFonts.inter(
    fontSize: 12,
    fontWeight: FontWeight.w600,
    color: AppColors.white,
    height: 1.2,
  );
}
