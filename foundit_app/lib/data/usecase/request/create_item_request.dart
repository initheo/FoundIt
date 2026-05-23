import 'dart:io';

/// Request untuk membuat laporan barang baru (POST /items)
class CreateItemRequest {
  final String type; // 'lost' atau 'found'
  final int categoryId;
  final String title;
  final String description;
  final String location;
  final String? locationDetail; // Custom label like "Ruang CM201"
  final double? latitude;
  final double? longitude;
  final DateTime dateTime;
  final String? storageInfo; // Hanya untuk type='found'
  final List<File> photos;

  CreateItemRequest({
    required this.type,
    required this.categoryId,
    required this.title,
    required this.description,
    required this.location,
    this.locationDetail,
    this.latitude,
    this.longitude,
    required this.dateTime,
    this.storageInfo,
    required this.photos,
  });

  /// Convert to Map for multipart request
  Map<String, String> toFields() {
    final fields = {
      'type': type,
      'category_id': categoryId.toString(),
      'title': title,
      'description': description,
      'location': location,
      'date_time': dateTime.toIso8601String(),
    };

    if (locationDetail != null && locationDetail!.isNotEmpty) {
      fields['location_detail'] = locationDetail!;
    }

    if (latitude != null) {
      fields['latitude'] = latitude.toString();
    }

    if (longitude != null) {
      fields['longitude'] = longitude.toString();
    }

    if (storageInfo != null && storageInfo!.isNotEmpty) {
      fields['storage_info'] = storageInfo!;
    }

    return fields;
  }
}
