class ItemModel {
  final int id;
  final int userId;
  final int categoryId;
  final String type; // 'lost' or 'found'
  final String title;
  final String description;
  final String location;
  final String? locationDetail; // Custom label like "Ruang CM201"
  final double? latitude;
  final double? longitude;
  final DateTime dateTime;
  final String? storageInfo;
  final String status; // 'active', 'claimed', 'returned'
  final String? photoUrl; // Single photo for backward compatibility
  final List<String>? _photoUrls; // Multiple photo URLs (for display)
  final List<Map<String, dynamic>>?
  _photoObjects; // Photos with IDs (for editing)
  final DateTime createdAt;

  // Optional: user info untuk display
  final String? userName;
  final String? userPhone;
  final String? userPhotoUrl;
  final String? categoryName;
  final int? claimsCount; // Number of claims for this item

  ItemModel({
    required this.id,
    required this.userId,
    required this.categoryId,
    required this.type,
    required this.title,
    required this.description,
    required this.location,
    this.locationDetail,
    this.latitude,
    this.longitude,
    required this.dateTime,
    this.storageInfo,
    required this.status,
    this.photoUrl,
    List<String>? photoUrls,
    List<Map<String, dynamic>>? photoObjects,
    required this.createdAt,
    this.userName,
    this.userPhone,
    this.userPhotoUrl,
    this.categoryName,
    this.claimsCount,
  }) : _photoUrls = photoUrls,
       _photoObjects = photoObjects;

  factory ItemModel.fromJson(Map<String, dynamic> json) {
    // Helper to parse int from String or int
    int parseId(dynamic value) {
      if (value is int) return value;
      return int.parse(value.toString());
    }

    // Parse photo_urls - can be array of strings or array of objects
    List<String> photoUrls = [];
    List<Map<String, dynamic>> photoObjects = [];

    final rawPhotoUrls = json['photo_urls'];
    if (rawPhotoUrls != null && rawPhotoUrls is List) {
      for (var item in rawPhotoUrls) {
        if (item is String) {
          // Simple string URL
          photoUrls.add(item);
        } else if (item is Map) {
          // Object with id and url
          final url = item['url']?.toString() ?? '';
          final id = item['id'];
          photoUrls.add(url);
          photoObjects.add({'id': id, 'url': url});
        }
      }
    }

    return ItemModel(
      id: parseId(json['id']),
      userId: parseId(json['user_id']),
      categoryId: parseId(json['category_id']),
      type: json['type'],
      title: json['title'],
      description: json['description'],
      location: json['location'],
      locationDetail: json['location_detail'],
      latitude: json['latitude'] != null
          ? double.tryParse(json['latitude'].toString())
          : null,
      longitude: json['longitude'] != null
          ? double.tryParse(json['longitude'].toString())
          : null,
      // Just parse - Laravel now uses Asia/Jakarta timezone
      dateTime: DateTime.parse(json['date_time']),
      storageInfo: json['storage_info'],
      status: json['status'],
      photoUrl: json['photo_url'],
      photoUrls: photoUrls,
      photoObjects: photoObjects,
      createdAt: DateTime.parse(json['created_at']),
      userName: json['user']?['name'],
      userPhone: json['user']?['phone'],
      userPhotoUrl: json['user']?['photo_url'],
      categoryName: json['category']?['name'],
      claimsCount: json['claims_count'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'category_id': categoryId,
      'type': type,
      'title': title,
      'description': description,
      'location': location,
      'location_detail': locationDetail,
      'latitude': latitude,
      'longitude': longitude,
      'date_time': dateTime.toIso8601String(),
      'storage_info': storageInfo,
      'status': status,
      'photo_url': photoUrl,
      'photo_urls': _photoUrls ?? [],
      'created_at': createdAt.toIso8601String(),
    };
  }

  bool get isLost => type == 'lost';
  bool get isFound => type == 'found';
  bool get isActive => status == 'active';
  bool get isReturned => status == 'returned';

  // Getter for photo URLs (display purposes)
  List<String> get photoUrls => _photoUrls ?? [];

  // Getter for photo objects with IDs (edit purposes)
  List<Map<String, dynamic>> get photoObjects => _photoObjects ?? [];

  // Helper to get time ago style string
  String get timeAgo {
    final now = DateTime.now();
    final difference = now.difference(createdAt);

    if (difference.inDays > 0) {
      if (difference.inDays == 1) return '1 hari lalu';
      return '${difference.inDays} hari lalu';
    } else if (difference.inHours > 0) {
      if (difference.inHours == 1) return '1 jam lalu';
      return '${difference.inHours} jam lalu';
    } else if (difference.inMinutes > 0) {
      if (difference.inMinutes == 1) return '1 menit lalu';
      return '${difference.inMinutes} menit lalu';
    }
    return 'Baru saja';
  }

  // Helper to format date in Indonesian
  String get formattedDate {
    final months = [
      'Januari',
      'Februari',
      'Maret',
      'April',
      'Mei',
      'Juni',
      'Juli',
      'Agustus',
      'September',
      'Oktober',
      'November',
      'Desember',
    ];
    return '${dateTime.day} ${months[dateTime.month - 1]} ${dateTime.year}';
  }

  // Get all photos (combines photoUrl and photoUrls)
  List<String> get allPhotos {
    final photos = <String>[];
    if (photoUrl != null && photoUrl!.isNotEmpty) photos.add(photoUrl!);
    for (final url in photoUrls) {
      if (!photos.contains(url)) photos.add(url);
    }
    return photos;
  }

  // CopyWith method for immutable updates
  ItemModel copyWith({
    int? id,
    int? userId,
    int? categoryId,
    String? type,
    String? title,
    String? description,
    String? location,
    String? locationDetail,
    double? latitude,
    double? longitude,
    DateTime? dateTime,
    String? storageInfo,
    String? status,
    String? photoUrl,
    List<String>? photoUrls,
    List<Map<String, dynamic>>? photoObjects,
    DateTime? createdAt,
    String? userName,
    String? userPhotoUrl,
    String? categoryName,
    int? claimsCount,
  }) {
    return ItemModel(
      id: id ?? this.id,
      userId: userId ?? this.userId,
      categoryId: categoryId ?? this.categoryId,
      type: type ?? this.type,
      title: title ?? this.title,
      description: description ?? this.description,
      location: location ?? this.location,
      locationDetail: locationDetail ?? this.locationDetail,
      latitude: latitude ?? this.latitude,
      longitude: longitude ?? this.longitude,
      dateTime: dateTime ?? this.dateTime,
      storageInfo: storageInfo ?? this.storageInfo,
      status: status ?? this.status,
      photoUrl: photoUrl ?? this.photoUrl,
      photoUrls: photoUrls ?? this.photoUrls,
      photoObjects: photoObjects ?? this.photoObjects,
      createdAt: createdAt ?? this.createdAt,
      userName: userName ?? this.userName,
      userPhotoUrl: userPhotoUrl ?? this.userPhotoUrl,
      categoryName: categoryName ?? this.categoryName,
      claimsCount: claimsCount ?? this.claimsCount,
    );
  }
}
