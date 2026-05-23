import 'dart:convert';

/// Response untuk error dari API
class ErrorResponse {
  final bool success;
  final String message;
  final Map<String, List<String>>? errors; // Validation errors

  ErrorResponse({this.success = false, required this.message, this.errors});

  factory ErrorResponse.fromJson(String str) =>
      ErrorResponse.fromMap(json.decode(str));

  factory ErrorResponse.fromMap(Map<String, dynamic> json) {
    Map<String, List<String>>? parseErrors(dynamic value) {
      if (value == null) return null;
      if (value is Map) {
        return value.map((key, val) {
          if (val is List) {
            return MapEntry(
              key.toString(),
              val.map((e) => e.toString()).toList(),
            );
          }
          return MapEntry(key.toString(), [val.toString()]);
        });
      }
      return null;
    }

    return ErrorResponse(
      success: json['success'] ?? false,
      message: json['message'] ?? 'Terjadi kesalahan',
      errors: parseErrors(json['errors']),
    );
  }

  /// Get first error message from validation errors
  String get firstError {
    if (errors != null && errors!.isNotEmpty) {
      final firstKey = errors!.keys.first;
      if (errors![firstKey]!.isNotEmpty) {
        return errors![firstKey]!.first;
      }
    }
    return message;
  }

  /// Get all error messages as a single string
  String get allErrors {
    if (errors == null || errors!.isEmpty) return message;
    return errors!.values.expand((e) => e).join('\n');
  }
}
