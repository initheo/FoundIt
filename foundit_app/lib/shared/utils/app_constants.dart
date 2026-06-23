class AppConstants {
  // App Info
  static const String appName = 'FoundIt';
  static const String appTagline = 'Lost something? We found it!';
  static const String appVersion = '1.0.0';

  // API - Android Emulator uses 10.0.2.2 to access host machine
  static String get apiBaseUrl {
    // Always use production server
    return 'https://foundit.neoartd.my.id/api';
  }

  // Storage Base URL - for images/files stored in Laravel storage
  static String get storageBaseUrl {
    // Always use production server
    return 'https://foundit.neoartd.my.id';
  }

  /// Converts a relative storage path to a full URL
  /// Example: /storage/photos/profile.jpg -> http://10.0.2.2:8000/storage/photos/profile.jpg
  static String? getFullImageUrl(String? relativePath) {
    if (relativePath == null || relativePath.isEmpty) return null;
    // If already a full URL, return as is
    if (relativePath.startsWith('http')) return relativePath;
    // Otherwise prepend the storage base URL
    return '$storageBaseUrl$relativePath';
  }

  static const int apiTimeout = 30;

  // Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';

  // Validation
  static const int minPasswordLength = 8;
  static const int maxPhotos = 3;
  static const List<String> validEmailDomains = [
    '@student.uisi.ac.id',
    '@uisi.ac.id',
  ];
}
