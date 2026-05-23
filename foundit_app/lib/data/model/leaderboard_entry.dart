
/// Model untuk entry leaderboard
class LeaderboardEntry {
  final int rank;
  final int userId;
  final String userName;
  final String userEmail;
  final String? userProdiUnit;
  final String? userPhotoUrl;
  final int returnedCount;
  final int foundCount;

  LeaderboardEntry({
    required this.rank,
    required this.userId,
    required this.userName,
    required this.userEmail,
    this.userProdiUnit,
    this.userPhotoUrl,
    required this.returnedCount,
    required this.foundCount,
  });

  factory LeaderboardEntry.fromJson(Map<String, dynamic> json) {
    final user = json['user'] as Map<String, dynamic>;
    final stats = json['stats'] as Map<String, dynamic>;

    return LeaderboardEntry(
      rank: json['rank'] ?? 0,
      userId: user['id'] ?? 0,
      userName: user['name'] ?? '',
      userEmail: user['email'] ?? '',
      userProdiUnit: user['prodi_unit'],
      userPhotoUrl: user['photo_url'],
      returnedCount: stats['returned_count'] ?? 0,
      foundCount: stats['found_count'] ?? 0,
    );
  }

  /// Total kontribusi (returned + found)
  int get totalContribution => returnedCount + foundCount;
}