import 'dart:convert';
import '../model/leaderboard_entry.dart';
import '../services/services.dart';

class LeaderboardRepository {
  final HttpService _httpService = HttpService();

  /// Mengambil data leaderboard dari API
  Future<List<LeaderboardEntry>> getLeaderboard({int limit = 10}) async {
    final response = await _httpService.get('/leaderboard?limit=$limit');

    if (response.statusCode == 200) {
      final body = jsonDecode(response.body);
      final List<dynamic> data = body['data']['leaderboard'] ?? [];
      return data.map((json) => LeaderboardEntry.fromJson(json)).toList();
    } else {
      final body = jsonDecode(response.body);
      throw Exception(body['message'] ?? 'Gagal mengambil leaderboard');
    }
  }
}