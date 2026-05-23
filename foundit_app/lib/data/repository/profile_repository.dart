import 'dart:convert';
import 'dart:io';
import '../model/user_model.dart';
import '../services/services.dart';

class ProfileRepository {
  final HttpService _httpService = HttpService();

  Future<UserModel> getProfile() async {
    final response = await _httpService.get('/profile');

    if (response.statusCode == 200) {
      final body = jsonDecode(response.body);
      return UserModel.fromJson(body['data']['user']);
    } else {
      final body = jsonDecode(response.body);
      throw Exception(body['message'] ?? 'Gagal mengambil profil');
    }
  }

  Future<UserModel> updateProfile({
    String? name,
    String? email,
    String? phone,
    String? prodiUnit,
  }) async {
    final data = <String, dynamic>{};
    if (name != null) data['name'] = name;
    if (email != null) data['email'] = email;
    if (phone != null) data['phone'] = phone;
    if (prodiUnit != null) data['prodi_unit'] = prodiUnit;

    final response = await _httpService.put('/profile', body: data);

    if (response.statusCode == 200) {
      final body = jsonDecode(response.body);
      return UserModel.fromJson(body['data']['user']);
    } else {
      final body = jsonDecode(response.body);
      throw Exception(body['message'] ?? 'Gagal mengupdate profil');
    }
  }

  Future<String> uploadPhoto(File photo) async {
    final response = await _httpService.postMultipart(
      '/profile/photo',
      {},
      image: photo,
      imageFieldName: 'photo',
    );

    if (response.statusCode == 200) {
      final responseBody = await response.stream.bytesToString();
      final body = jsonDecode(responseBody);
      return body['data']['photo_url'];
    } else {
      final responseBody = await response.stream.bytesToString();
      final body = jsonDecode(responseBody);
      throw Exception(body['message'] ?? 'Gagal mengupload foto');
    }
  }

  Future<void> deletePhoto() async {
    final response = await _httpService.delete('/profile/photo');

    if (response.statusCode != 200) {
      final body = jsonDecode(response.body);
      throw Exception(body['message'] ?? 'Gagal menghapus foto');
    }
  }
}