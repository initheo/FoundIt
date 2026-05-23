import 'dart:convert';
import '../../model/user_model.dart';

class AuthResponse {
  final bool success;
  final String message;
  final AuthData? data;

  AuthResponse({
    required this.success,
    required this.message,
    this.data,
  });

  factory AuthResponse.fromJson(String jsonString) =>
      AuthResponse.fromMap(json.decode(jsonString));

  factory AuthResponse.fromMap(Map<String, dynamic> json) => AuthResponse(
        success: json['success'] ?? false,
        message: json['message'] ?? '',
        data: json['data'] != null ? AuthData.fromMap(json['data']) : null,
      );

  Map<String, dynamic> toMap() => {
        'success': success,
        'message': message,
        'data': data?.toMap(),
      };
}

/// Data yang dikembalikan dari Auth API (user + token)
class AuthData {
  final UserModel user;
  final String token;
  final String tokenType;

  AuthData({
    required this.user,
    required this.token,
    required this.tokenType,
  });

  factory AuthData.fromMap(Map<String, dynamic> json) => AuthData(
        user: UserModel.fromJson(json['user']),
        token: json['token'] ?? '',
        tokenType: json['token_type'] ?? 'Bearer',
      );

  Map<String, dynamic> toMap() => {
        'user': user.toJson(),
        'token': token,
        'token_type': tokenType,
      };
}
