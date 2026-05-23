import 'app_constants.dart';

/// Form validators for FoundIt app
class Validators {
  /// Validate required field
  static String? required(String? value, [String? fieldName]) {
    if (value == null || value.trim().isEmpty) {
      return fieldName != null
          ? '$fieldName wajib diisi'
          : 'Field ini wajib diisi';
    }
    return null;
  }

  /// Validate UISI email (student or staff)
  static String? uisiEmail(String? value) {
    if (value == null || value.trim().isEmpty) {
      return 'Email wajib diisi';
    }

    final trimmedValue = value.trim().toLowerCase();

    // Check email format first
    final emailRegex = RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$');
    if (!emailRegex.hasMatch(trimmedValue)) {
      return 'Format email tidak valid';
    }

    // Check if email ends with valid UISI domain
    bool isValidDomain = AppConstants.validEmailDomains.any(
      (domain) => trimmedValue.endsWith(domain.toLowerCase()),
    );

    if (!isValidDomain) {
      return 'Gunakan email @student.uisi.ac.id atau @uisi.ac.id';
    }

    return null;
  }

  /// Validate password
  static String? password(String? value) {
    if (value == null || value.isEmpty) {
      return 'Password wajib diisi';
    }

    if (value.length < AppConstants.minPasswordLength) {
      return 'Password minimal ${AppConstants.minPasswordLength} karakter';
    }

    return null;
  }

  /// Validate password confirmation
  static String? confirmPassword(String? value, String password) {
    if (value == null || value.isEmpty) {
      return 'Konfirmasi password wajib diisi';
    }

    if (value != password) {
      return 'Password tidak cocok';
    }

    return null;
  }

  /// Validate name
  static String? name(String? value) {
    if (value == null || value.trim().isEmpty) {
      return 'Nama wajib diisi';
    }

    if (value.trim().length < 2) {
      return 'Nama minimal 2 karakter';
    }

    if (value.trim().length > 100) {
      return 'Nama maksimal 100 karakter';
    }

    return null;
  }

  /// Validate phone number
  static String? phone(String? value) {
    if (value == null || value.trim().isEmpty) {
      return null; // Phone is optional
    }

    // Remove spaces and dashes
    final cleanPhone = value.replaceAll(RegExp(r'[\s-]'), '');

    // Check if starts with valid prefix
    if (!cleanPhone.startsWith('08') &&
        !cleanPhone.startsWith('+62') &&
        !cleanPhone.startsWith('62')) {
      return 'Nomor HP harus dimulai dengan 08, +62, atau 62';
    }

    // Check length (Indonesian phone numbers)
    if (cleanPhone.length < 10 || cleanPhone.length > 15) {
      return 'Nomor HP tidak valid';
    }

    // Check if only contains numbers (and + at start)
    final phoneRegex = RegExp(r'^\+?[0-9]+$');
    if (!phoneRegex.hasMatch(cleanPhone)) {
      return 'Nomor HP hanya boleh berisi angka';
    }

    return null;
  }
}
