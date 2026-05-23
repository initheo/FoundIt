
import 'dart:convert';
import '../model/activity_entry.dart';
import '../services/services.dart';

class ActivityRepository {
  final HttpService _httpService = HttpService();

  /// Mengambil riwayat aktivitas user dari API
  Future<List<ActivityEntry>> getActivities({int limit = 20}) async {
    final response = await _httpService.get('/activities?limit=$limit');

    if (response.statusCode == 200) {
      final body = jsonDecode(response.body);
      final List<dynamic> data = body['data']['activities'] ?? [];
      return data.map((json) => ActivityEntry.fromJson(json)).toList();
    } else {
      final body = jsonDecode(response.body);
      throw Exception(body['message'] ?? 'Gagal mengambil riwayat aktivitas');
    }
  }
}