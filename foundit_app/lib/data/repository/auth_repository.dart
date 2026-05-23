import 'dart:convert';

import 'package:shared_preferences/shared_preferences.dart';

import '../../shared/utils/utils.dart';
import '../model/user_model.dart';
import '../services/services.dart';
import '../usecase/request/login_request.dart';
import '../usecase/request/register_request.dart';
import '../usecase/response/auth_response.dart';

class AuthRepository {
  final HttpService _httpService = HttpService();

  // ============ LOGIN ============
  Future<UserModel> login(LoginRequest request) async {
    final response = await _httpService.post('/login', body: request.toMap());

    if (response.statusCode == 200) {
      final authResponse = AuthResponse.fromJson(response.body);

      if (!authResponse.success || authResponse.data == null) {
        throw Exception(authResponse.message);
      }

      await _saveToken(authResponse.data!.token);
      await saveUser(authResponse.data!.user);

      return authResponse.data!.user;
    } else if (response.statusCode == 401) {
      throw Exception('Email atau password salah.');
    } else {
      final body = jsonDecode(response.body);
      throw Exception(body['message'] ?? 'Login gagal.');
    }
  }

  // ============ REGISTER ============
  Future<UserModel> register(RegisterRequest request) async {
    final response = await _httpService.post(
      '/register',
      body: request.toMap(),
    );

    if (response.statusCode == 200 || response.statusCode == 201) {
      final authResponse = AuthResponse.fromJson(response.body);

      if (!authResponse.success || authResponse.data == null) {
        throw Exception(authResponse.message);
      }

      await _saveToken(authResponse.data!.token);
      await saveUser(authResponse.data!.user);

      return authResponse.data!.user;
    } else if (response.statusCode == 422) {
      final body = jsonDecode(response.body);
      if (body['errors'] != null) {
        final errors = body['errors'] as Map;
        final firstError = errors.values.first;
        if (firstError is List && firstError.isNotEmpty) {
          throw Exception(firstError.first);
        }
      }
      throw Exception(body['message'] ?? 'Validasi gagal.');
    } else {
      final body = jsonDecode(response.body);
      throw Exception(body['message'] ?? 'Registrasi gagal.');
    }
  }

  // ============ LOGOUT ============
  Future<void> logout() async {
    try {
      await _httpService.post('/logout');
    } catch (error) {
      // Abaikan error, tetap hapus token lokal
    }
    await _clearStorage();
  }

  // ============ CHECK LOGIN STATUS ============
  Future<bool> isLoggedIn() async {
    final token = await _getToken();
    return token != null && token.isNotEmpty;
  }

  // ============ GET CURRENT USER ============
  Future<UserModel?> getCurrentUser() async {
    final preferences = await SharedPreferences.getInstance();
    final userJsonString = preferences.getString(AppConstants.userKey);
    if (userJsonString == null) return null;

    return UserModel.fromJson(jsonDecode(userJsonString));
  }

  // ============ PRIVATE METHODS ============

  Future<void> _saveToken(String token) async {
    final preferences = await SharedPreferences.getInstance();
    await preferences.setString(AppConstants.tokenKey, token);
  }

  Future<String?> _getToken() async {
    final preferences = await SharedPreferences.getInstance();
    return preferences.getString(AppConstants.tokenKey);
  }

  Future<void> saveUser(UserModel user) async {
    final preferences = await SharedPreferences.getInstance();
    await preferences.setString(
      AppConstants.userKey,
      jsonEncode(user.toJson()),
    );
  }

  Future<void> _clearStorage() async {
    final preferences = await SharedPreferences.getInstance();
    await preferences.remove(AppConstants.tokenKey);
    await preferences.remove(AppConstants.userKey);
  }
}
