import 'package:flutter_test/flutter_test.dart';
import 'package:foundit_app/data/model/leaderboard_entry.dart';

/// Unit Test Suite: LeaderboardEntryTest
/// Skenario Utama: Validasi parsing JSON dan logika kalkulasi LeaderboardEntry
void main() {
  group('LeaderboardEntry', () {
    late Map<String, dynamic> validJson;

    setUp(() {
      validJson = {
        'rank': 1,
        'user': {
          'id': 10,
          'name': 'Siti Aminah',
          'email': 'siti@student.uisi.ac.id',
          'prodi_unit': 'Teknik Informatika',
          'photo_url': '/storage/photos/siti.jpg',
        },
        'stats': {
          'returned_count': 5,
          'found_count': 8,
        },
      };
    });

    /// TC-FL01: fromJson parsing data lengkap berhasil
    test('fromJson parses complete JSON correctly', () {
      final entry = LeaderboardEntry.fromJson(validJson);

      expect(entry.rank, 1);
      expect(entry.userId, 10);
      expect(entry.userName, 'Siti Aminah');
      expect(entry.userEmail, 'siti@student.uisi.ac.id');
      expect(entry.returnedCount, 5);
      expect(entry.foundCount, 8);
    });

    /// TC-FL02: totalContribution menghitung returned + found
    test('totalContribution calculates sum correctly', () {
      final entry = LeaderboardEntry.fromJson(validJson);
      expect(entry.totalContribution, 13); // 5 + 8
    });

    /// TC-FL03: fromJson dengan optional fields null
    test('fromJson handles null optional fields', () {
      final jsonWithNulls = {
        'rank': 1,
        'user': {
          'id': 10,
          'name': 'Siti Aminah',
          'email': 'siti@student.uisi.ac.id',
          'prodi_unit': null,
          'photo_url': null,
        },
        'stats': {'returned_count': 5, 'found_count': 8},
      };
      final entry = LeaderboardEntry.fromJson(jsonWithNulls);

      expect(entry.userProdiUnit, isNull);
      expect(entry.userPhotoUrl, isNull);
    });

    /// TC-FL04: totalContribution dengan count nol
    test('totalContribution returns zero when both counts are zero', () {
      validJson['stats'] = {'returned_count': 0, 'found_count': 0};
      final entry = LeaderboardEntry.fromJson(validJson);

      expect(entry.totalContribution, 0);
    });

    /// TC-FL05: fromJson dengan default values saat field null
    test('fromJson uses defaults when fields are null', () {
      final jsonWithNulls = {
        'rank': null,
        'user': {
          'id': null,
          'name': '',
          'email': '',
          'prodi_unit': null,
          'photo_url': null,
        },
        'stats': {'returned_count': null, 'found_count': null},
      };
      final entry = LeaderboardEntry.fromJson(jsonWithNulls);

      expect(entry.rank, 0);
      expect(entry.userId, 0);
      expect(entry.returnedCount, 0);
    });
  });
}
