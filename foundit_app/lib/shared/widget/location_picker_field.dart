import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';

import '../../presentation/screens/location/location_picker_screen.dart';
import '../utils/app_colors.dart';
import '../utils/app_spacing.dart';
import '../utils/app_text_styles.dart';

/// A reusable location picker field that opens LocationPickerScreen
///
/// Usage:
/// ```dart
/// LocationPickerField(
///   label: 'Lokasi Ditemukan',
///   selectedAddress: _locationController.text,
///   initialLocation: _selectedLocation != null
///       ? LatLng(_selectedLocation!.latitude, _selectedLocation!.longitude)
///       : null,
///   onLocationSelected: (location) {
///     setState(() {
///       _selectedLocation = location;
///       _locationController.text = location.address;
///     });
///   },
/// )
/// ```
class LocationPickerField extends StatelessWidget {
  final String label;
  final String selectedAddress;
  final LatLng? initialLocation;
  final String? initialAddress;
  final ValueChanged<SelectedLocation> onLocationSelected;

  const LocationPickerField({
    super.key,
    required this.label,
    required this.selectedAddress,
    this.initialLocation,
    this.initialAddress,
    required this.onLocationSelected,
  });

  @override
  Widget build(BuildContext context) {
    final isEmpty = selectedAddress.isEmpty;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: AppTextStyles.inputLabel),
        const SizedBox(height: AppSpacing.sm),
        GestureDetector(
          onTap: () async {
            final result = await Navigator.push<SelectedLocation>(
              context,
              MaterialPageRoute(
                builder: (context) => LocationPickerScreen(
                  initialLocation: initialLocation,
                  initialAddress: initialAddress ?? selectedAddress,
                ),
              ),
            );
            if (result != null) {
              onLocationSelected(result);
            }
          },
          child: Container(
            padding: const EdgeInsets.all(AppSpacing.md),
            decoration: BoxDecoration(
              color: AppColors.surface,
              borderRadius: BorderRadius.circular(AppRadius.md),
              border: Border.all(
                color: isEmpty
                    ? AppColors.border
                    : AppColors.primary.withAlpha(128),
              ),
            ),
            child: Row(
              children: [
                Icon(
                  Icons.location_on,
                  color: isEmpty ? AppColors.textSecondary : AppColors.primary,
                ),
                const SizedBox(width: AppSpacing.md),
                Expanded(
                  child: Text(
                    isEmpty
                        ? 'Ketuk untuk pilih lokasi di peta'
                        : selectedAddress,
                    style: AppTextStyles.input.copyWith(
                      color: isEmpty
                          ? AppColors.textSecondary
                          : AppColors.textPrimary,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
                const Icon(Icons.chevron_right, color: AppColors.textSecondary),
              ],
            ),
          ),
        ),
      ],
    );
  }
}
