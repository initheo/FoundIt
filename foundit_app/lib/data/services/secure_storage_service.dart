import 'dart:convert';

import 'package:flutter_secure_storage/flutter_secure_storage.dart';

import '../../shared/utils/utils.dart';
import '../model/user_model.dart';

/// Secure storage service for sensitive data (tokens, user info)
/// Replaces plain-text SharedPreferences for auth data
class SecureStorageService {
  static const FlutterSecureStorage _storage = FlutterSecureStorage(
    aOptions: AndroidOptions(encryptedSharedPreferences: true),
  );

  // ============ TOKEN ============

  Future<void> saveToken(String token) async {
    await _storage.write(key: AppConstants.tokenKey, value: token);
  }

  Future<String?> getToken() async {
    return await _storage.read(key: AppConstants.tokenKey);
  }

  Future<void> deleteToken() async {
    await _storage.delete(key: AppConstants.tokenKey);
  }

  // ============ USER DATA ============

  Future<void> saveUser(UserModel user) async {
    final jsonString = jsonEncode(user.toJson());
    await _storage.write(key: AppConstants.userKey, value: jsonString);
  }

  Future<UserModel?> getUser() async {
    final jsonString = await _storage.read(key: AppConstants.userKey);
    if (jsonString == null) return null;
    return UserModel.fromJson(jsonDecode(jsonString));
  }

  Future<void> deleteUser() async {
    await _storage.delete(key: AppConstants.userKey);
  }

  // ============ CHECK LOGIN ============

  Future<bool> isLoggedIn() async {
    final token = await getToken();
    return token != null && token.isNotEmpty;
  }

  // ============ CLEAR ALL ============

  Future<void> clearAll() async {
    await _storage.deleteAll();
  }
}