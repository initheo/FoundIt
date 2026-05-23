import 'package:flutter_test/flutter_test.dart';
import 'package:foundit_app/data/model/activity_entry.dart';

/// Unit Test Suite: ActivityEntryTest
/// Skenario Utama: Validasi parsing JSON dan default values model ActivityEntry
void main() {
  group('ActivityEntry', () {
    late Map<String, dynamic> validJson;

    setUp(() {
      validJson = {
        'id': 'act-001',
        'type': 'item_found',
        'title': 'Barang Ditemukan',
        'description': 'Anda melaporkan dompet hitam ditemukan',
        'icon': 'search',
        'color': 'success',
        'reference_id': 42,
        'reference_type': 'item',
        'photo_url': '/storage/photos/dompet.jpg',
        'created_at': '2025-06-15 10:30:00',
      };
    });

    /// TC-FA01: fromJson parsing data lengkap berhasil
    test('fromJson parses complete JSON correctly', () {
      final entry = ActivityEntry.fromJson(validJson);

      expect(entry.id, 'act-001');
      expect(entry.type, 'item_found');
      expect(entry.title, 'Barang Ditemukan');
      expect(entry.description, 'Anda melaporkan dompet hitam ditemukan');
      expect(entry.icon, 'search');
      expect(entry.color, 'success');
    });

    /// TC-FA02: fromJson parsing optional fields
    test('fromJson parses optional fields correctly', () {
      final entry = ActivityEntry.fromJson(validJson);

      expect(entry.referenceId, 42);
      expect(entry.referenceType, 'item');
      expect(entry.photoUrl, '/storage/photos/dompet.jpg');
    });

    /// TC-FA03: fromJson parsing createdAt sebagai DateTime
    test('fromJson parses createdAt as DateTime', () {
      final entry = ActivityEntry.fromJson(validJson);

      expect(entry.createdAt, isA<DateTime>());
      expect(entry.createdAt.year, 2025);
      expect(entry.createdAt.month, 6);
      expect(entry.createdAt.day, 15);
      expect(entry.createdAt.hour, 10);
      expect(entry.createdAt.minute, 30);
    });

    /// TC-FA04: fromJson dengan optional fields null
    test('fromJson handles null optional fields', () {
      validJson.remove('reference_id');
      validJson.remove('reference_type');
      validJson.remove('photo_url');
      final entry = ActivityEntry.fromJson(validJson);

      expect(entry.referenceId, isNull);
      expect(entry.referenceType, isNull);
      expect(entry.photoUrl, isNull);
    });

    /// TC-FA05: fromJson menggunakan default 'info' untuk icon jika null
    test('fromJson uses default icon when null', () {
      validJson['icon'] = null;
      final entry = ActivityEntry.fromJson(validJson);

      expect(entry.icon, 'info');
    });

    /// TC-FA06: fromJson menggunakan default 'primary' untuk color jika null
    test('fromJson uses default color when null', () {
      validJson['color'] = null;
      final entry = ActivityEntry.fromJson(validJson);

      expect(entry.color, 'primary');
    });

    /// TC-FA07: fromJson menggunakan string kosong untuk id jika null
    test('fromJson uses empty string for id when null', () {
      validJson['id'] = null;
      final entry = ActivityEntry.fromJson(validJson);

      expect(entry.id, '');
    });

    /// TC-FA08: fromJson menggunakan string kosong untuk type jika null
    test('fromJson uses empty string for type when null', () {
      validJson['type'] = null;
      final entry = ActivityEntry.fromJson(validJson);

      expect(entry.type, '');
    });

    /// TC-FA09: fromJson menggunakan string kosong untuk title jika null
    test('fromJson uses empty string for title when null', () {
      validJson['title'] = null;
      final entry = ActivityEntry.fromJson(validJson);

      expect(entry.title, '');
    });

    /// TC-FA10: Constructor membuat instance dengan benar
    test('constructor creates instance correctly', () {
      final entry = ActivityEntry(
        id: 'test-id',
        type: 'claim_approved',
        title: 'Klaim Disetujui',
        description: 'Klaim Anda telah disetujui',
        icon: 'check',
        color: 'success',
        createdAt: DateTime(2025, 7, 1, 12, 0, 0),
      );

      expect(entry.id, 'test-id');
      expect(entry.type, 'claim_approved');
      expect(entry.title, 'Klaim Disetujui');
      expect(entry.referenceId, isNull);
      expect(entry.createdAt.year, 2025);
    });
  });
}
