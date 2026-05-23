import 'package:flutter/material.dart';
import 'package:geocoding/geocoding.dart';
import 'package:geolocator/geolocator.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';

import '../../../shared/utils/utils.dart';
import '../../../shared/widget/widgets.dart';

/// Model untuk hasil pemilihan lokasi
class SelectedLocation {
  final double latitude;
  final double longitude;
  final String address;

  SelectedLocation({
    required this.latitude,
    required this.longitude,
    required this.address,
  });
}

/// Screen untuk memilih lokasi menggunakan Google Maps
class LocationPickerScreen extends StatefulWidget {
  final LatLng? initialLocation;
  final String? initialAddress;

  const LocationPickerScreen({
    super.key,
    this.initialLocation,
    this.initialAddress,
  });

  @override
  State<LocationPickerScreen> createState() => _LocationPickerScreenState();
}

class _LocationPickerScreenState extends State<LocationPickerScreen> {
  GoogleMapController? _mapController;
  LatLng? _selectedLocation;
  String _address = '';
  bool _isLoading = true;
  bool _isGettingAddress = false;
  final TextEditingController _searchController = TextEditingController();

  // Default: UISI Surabaya
  static const LatLng _defaultLocation = LatLng(-7.1756, 112.6492);

  @override
  void initState() {
    super.initState();
    _initializeLocation();
  }

  @override
  void dispose() {
    _mapController?.dispose();
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _initializeLocation() async {
    if (widget.initialLocation != null) {
      _selectedLocation = widget.initialLocation;
      _address = widget.initialAddress ?? '';
      setState(() => _isLoading = false);
      return;
    }

    // Try to get current location
    try {
      final permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        await Geolocator.requestPermission();
      }

      final currentPermission = await Geolocator.checkPermission();
      if (currentPermission == LocationPermission.whileInUse ||
          currentPermission == LocationPermission.always) {
        final position = await Geolocator.getCurrentPosition(
          desiredAccuracy: LocationAccuracy.high,
        );
        _selectedLocation = LatLng(position.latitude, position.longitude);
        await _getAddressFromLatLng(_selectedLocation!);
      } else {
        _selectedLocation = _defaultLocation;
      }
    } catch (e) {
      _selectedLocation = _defaultLocation;
    }

    if (mounted) {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _getAddressFromLatLng(LatLng location) async {
    setState(() => _isGettingAddress = true);
    try {
      final placemarks = await placemarkFromCoordinates(
        location.latitude,
        location.longitude,
      );
      if (placemarks.isNotEmpty) {
        final place = placemarks.first;
        // Build address smartly to avoid duplication
        final parts = <String>[];

        // Use name if it exists and is specific (not just city name)
        if (place.name != null &&
            place.name!.isNotEmpty &&
            place.name != place.locality &&
            place.name != place.administrativeArea) {
          parts.add(place.name!);
        } else {
          // Otherwise combine street + number
          if (place.thoroughfare != null && place.thoroughfare!.isNotEmpty) {
            var street = place.thoroughfare!;
            if (place.subThoroughfare != null &&
                place.subThoroughfare!.isNotEmpty) {
              street = '$street No. ${place.subThoroughfare}';
            }
            parts.add(street);
          }
        }

        // Add location hierarchy (avoid duplicates)
        if (place.subLocality != null &&
            place.subLocality!.isNotEmpty &&
            !parts.any((p) => p.contains(place.subLocality!))) {
          parts.add(place.subLocality!);
        }
        if (place.locality != null &&
            place.locality!.isNotEmpty &&
            !parts.any((p) => p.contains(place.locality!))) {
          parts.add(place.locality!);
        }
        if (place.administrativeArea != null &&
            place.administrativeArea!.isNotEmpty &&
            !parts.any((p) => p.contains(place.administrativeArea!))) {
          parts.add(place.administrativeArea!);
        }

        _address = parts.join(', ');
      }
    } catch (e) {
      _address =
          'Lat: ${location.latitude.toStringAsFixed(6)}, '
          'Lng: ${location.longitude.toStringAsFixed(6)}';
    }
    if (mounted) {
      setState(() => _isGettingAddress = false);
    }
  }

  void _onMapTap(LatLng location) async {
    setState(() {
      _selectedLocation = location;
    });
    _mapController?.animateCamera(CameraUpdate.newLatLng(location));
    await _getAddressFromLatLng(location);
  }

  Future<void> _searchLocation() async {
    final query = _searchController.text.trim();
    if (query.isEmpty) return;

    setState(() => _isGettingAddress = true);
    try {
      final locations = await locationFromAddress(query);
      if (locations.isNotEmpty) {
        final loc = locations.first;
        final newLocation = LatLng(loc.latitude, loc.longitude);
        setState(() {
          _selectedLocation = newLocation;
        });
        _mapController?.animateCamera(
          CameraUpdate.newLatLngZoom(newLocation, 16),
        );
        await _getAddressFromLatLng(newLocation);
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Lokasi tidak ditemukan')),
          );
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(
          context,
        ).showSnackBar(SnackBar(content: Text('Gagal mencari lokasi: $e')));
      }
    }
    if (mounted) {
      setState(() => _isGettingAddress = false);
    }
  }

  Future<void> _goToCurrentLocation() async {
    try {
      final position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      );
      final newLocation = LatLng(position.latitude, position.longitude);
      setState(() {
        _selectedLocation = newLocation;
      });
      _mapController?.animateCamera(
        CameraUpdate.newLatLngZoom(newLocation, 16),
      );
      await _getAddressFromLatLng(newLocation);
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Gagal mendapatkan lokasi saat ini')),
        );
      }
    }
  }

  void _confirmLocation() {
    if (_selectedLocation == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pilih lokasi terlebih dahulu')),
      );
      return;
    }

    Navigator.pop(
      context,
      SelectedLocation(
        latitude: _selectedLocation!.latitude,
        longitude: _selectedLocation!.longitude,
        address: _address.isEmpty ? 'Lokasi dipilih' : _address,
      ),
    );
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
          'Pilih Lokasi',
          style: AppTextStyles.h3.copyWith(color: AppColors.textPrimary),
        ),
        centerTitle: true,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : Column(
              children: [
                // Search Bar
                Padding(
                  padding: const EdgeInsets.all(AppSpacing.md),
                  child: Row(
                    children: [
                      Expanded(
                        child: TextField(
                          controller: _searchController,
                          decoration: InputDecoration(
                            hintText: 'Cari lokasi...',
                            prefixIcon: const Icon(Icons.search),
                            filled: true,
                            fillColor: AppColors.surface,
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(AppRadius.md),
                              borderSide: BorderSide(color: AppColors.border),
                            ),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(AppRadius.md),
                              borderSide: BorderSide(color: AppColors.border),
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(AppRadius.md),
                              borderSide: const BorderSide(
                                color: AppColors.primary,
                              ),
                            ),
                            contentPadding: const EdgeInsets.symmetric(
                              horizontal: AppSpacing.md,
                              vertical: AppSpacing.sm,
                            ),
                          ),
                          onSubmitted: (_) => _searchLocation(),
                        ),
                      ),
                      const SizedBox(width: AppSpacing.sm),
                      Material(
                        color: AppColors.primary,
                        borderRadius: BorderRadius.circular(AppRadius.md),
                        child: InkWell(
                          onTap: _searchLocation,
                          borderRadius: BorderRadius.circular(AppRadius.md),
                          child: Container(
                            padding: const EdgeInsets.all(12),
                            child: const Icon(
                              Icons.search,
                              color: Colors.white,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),

                // Map
                Expanded(
                  child: Stack(
                    children: [
                      GoogleMap(
                        initialCameraPosition: CameraPosition(
                          target: _selectedLocation ?? _defaultLocation,
                          zoom: 15,
                        ),
                        onMapCreated: (controller) =>
                            _mapController = controller,
                        onTap: _onMapTap,
                        markers: _selectedLocation != null
                            ? {
                                Marker(
                                  markerId: const MarkerId('selected'),
                                  position: _selectedLocation!,
                                  infoWindow: InfoWindow(title: _address),
                                ),
                              }
                            : {},
                        myLocationEnabled: true,
                        myLocationButtonEnabled: false,
                        zoomControlsEnabled: false,
                      ),

                      // Current Location Button
                      Positioned(
                        right: AppSpacing.md,
                        bottom: AppSpacing.md,
                        child: FloatingActionButton.small(
                          onPressed: _goToCurrentLocation,
                          backgroundColor: AppColors.surface,
                          child: const Icon(
                            Icons.my_location,
                            color: AppColors.primary,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),

                // Bottom Info Card
                Container(
                  padding: const EdgeInsets.all(AppSpacing.md),
                  decoration: BoxDecoration(
                    color: AppColors.surface,
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withAlpha(26),
                        blurRadius: 10,
                        offset: const Offset(0, -2),
                      ),
                    ],
                  ),
                  child: SafeArea(
                    top: false,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Row(
                          children: [
                            Icon(
                              Icons.location_on,
                              color: AppColors.primary,
                              size: 20,
                            ),
                            const SizedBox(width: AppSpacing.sm),
                            Expanded(
                              child: Text(
                                'Lokasi Terpilih',
                                style: AppTextStyles.bodySmall.copyWith(
                                  color: AppColors.textSecondary,
                                ),
                              ),
                            ),
                            if (_isGettingAddress)
                              const SizedBox(
                                width: 16,
                                height: 16,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2,
                                ),
                              ),
                          ],
                        ),
                        const SizedBox(height: AppSpacing.xs),
                        Text(
                          _address.isEmpty
                              ? 'Ketuk peta untuk memilih lokasi'
                              : _address,
                          style: AppTextStyles.body,
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: AppSpacing.md),
                        PrimaryButton(
                          text: 'Konfirmasi Lokasi',
                          onPressed: _confirmLocation,
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
    );
  }
}
