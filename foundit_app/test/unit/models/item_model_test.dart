import 'package:flutter_test/flutter_test.dart';
import 'package:foundit_app/data/model/item_model.dart';

/// Unit Test Suite: ItemModelTest
/// Skenario Utama: Validasi parsing JSON, getter, dan logika model ItemModel
void main() {
  group('ItemModel', () {
    late Map<String, dynamic> validJson;

    setUp(() {
      validJson = {
        'id': 1,
        'user_id': 10,
        'category_id': 2,
        'type': 'lost',
        'title': 'Dompet Hitam',
        'description': 'Dompet kulit warna hitam',
        'location': 'Gedung A',
        'location_detail': 'Ruang CM201',
        'latitude': -7.3225,
        'longitude': 112.7275,
        'date_time': '2025-06-10 14:30:00',
        'storage_info': 'Satpam Gedung A',
        'status': 'active',
        'photo_url': '/storage/photos/dompet.jpg',
        'photo_urls': [
          {'id': 1, 'url': '/storage/photos/dompet1.jpg'},
          {'id': 2, 'url': '/storage/photos/dompet2.jpg'},
        ],
        'created_at': '2025-06-10 14:30:00',
        'user': {'name': 'Budi', 'phone': '08123456789', 'photo_url': null},
        'category': {'name': 'Dompet'},
        'claims_count': 3,
      };
    });

    /// TC-FI01: fromJson parsing data lengkap berhasil
    test('fromJson parses complete JSON correctly', () {
      final item = ItemModel.fromJson(validJson);

      expect(item.id, 1);
      expect(item.userId, 10);
      expect(item.categoryId, 2);
      expect(item.type, 'lost');
      expect(item.title, 'Dompet Hitam');
      expect(item.description, 'Dompet kulit warna hitam');
      expect(item.location, 'Gedung A');
      expect(item.locationDetail, 'Ruang CM201');
      expect(item.status, 'active');
    });

    /// TC-FI02: fromJson parsing latitude dan longitude
    test('fromJson parses coordinates correctly', () {
      final item = ItemModel.fromJson(validJson);

      expect(item.latitude, -7.3225);
      expect(item.longitude, 112.7275);
    });

    /// TC-FI03: fromJson parsing photo_urls sebagai object list
    test('fromJson parses photo objects correctly', () {
      final item = ItemModel.fromJson(validJson);

      expect(item.photoUrls.length, 2);
      expect(item.photoUrls[0], '/storage/photos/dompet1.jpg');
      expect(item.photoObjects.length, 2);
      expect(item.photoObjects[0]['id'], 1);
    });

    /// TC-FI04: fromJson parsing user info nested
    test('fromJson parses nested user info', () {
      final item = ItemModel.fromJson(validJson);

      expect(item.userName, 'Budi');
      expect(item.userPhone, '08123456789');
      expect(item.categoryName, 'Dompet');
    });

    /// TC-FI05: isLost getter mengembalikan true untuk type lost
    test('isLost returns true when type is lost', () {
      final item = ItemModel.fromJson(validJson);
      expect(item.isLost, true);
      expect(item.isFound, false);
    });

    /// TC-FI06: isFound getter mengembalikan true untuk type found
    test('isFound returns true when type is found', () {
      validJson['type'] = 'found';
      final item = ItemModel.fromJson(validJson);
      expect(item.isFound, true);
      expect(item.isLost, false);
    });

    /// TC-FI07: isActive getter mengembalikan true untuk status active
    test('isActive returns true when status is active', () {
      final item = ItemModel.fromJson(validJson);
      expect(item.isActive, true);
      expect(item.isReturned, false);
    });

    /// TC-FI08: isReturned getter mengembalikan true untuk status returned
    test('isReturned returns true when status is returned', () {
      validJson['status'] = 'returned';
      final item = ItemModel.fromJson(validJson);
      expect(item.isReturned, true);
      expect(item.isActive, false);
    });

    /// TC-FI09: toJson menghasilkan map yang benar
    test('toJson produces correct map', () {
      final item = ItemModel.fromJson(validJson);
      final json = item.toJson();

      expect(json['id'], 1);
      expect(json['title'], 'Dompet Hitam');
      expect(json['type'], 'lost');
      expect(json['user_id'], 10);
    });

    /// TC-FI10: copyWith menghasilkan instance baru dengan perubahan
    test('copyWith creates new instance with changes', () {
      final item = ItemModel.fromJson(validJson);
      final updated = item.copyWith(title: 'Dompet Baru', status: 'returned');

      expect(updated.title, 'Dompet Baru');
      expect(updated.status, 'returned');
      expect(updated.id, item.id); // unchanged
      expect(item.title, 'Dompet Hitam'); // original unchanged
    });

    /// TC-FI11: formattedDate menghasilkan format Indonesia
    test('formattedDate returns Indonesian formatted date', () {
      final item = ItemModel.fromJson(validJson);
      expect(item.formattedDate, '10 Juni 2025');
    });

    /// TC-FI12: allPhotos menggabungkan photoUrl dan photoUrls tanpa duplikat
    test('allPhotos combines photoUrl and photoUrls without duplicates', () {
      final item = ItemModel.fromJson(validJson);
      final photos = item.allPhotos;

      expect(photos.contains('/storage/photos/dompet.jpg'), true);
      expect(photos.contains('/storage/photos/dompet1.jpg'), true);
      expect(photos.length, 3); // 1 photoUrl + 2 photoUrls
    });

    /// TC-FI13: fromJson dengan id sebagai String (bukan int)
    test('fromJson handles string id correctly', () {
      validJson['id'] = '99';
      validJson['user_id'] = '5';
      final item = ItemModel.fromJson(validJson);

      expect(item.id, 99);
      expect(item.userId, 5);
    });

    /// TC-FI14: fromJson dengan latitude/longitude null
    test('fromJson handles null coordinates', () {
      validJson['latitude'] = null;
      validJson['longitude'] = null;
      final item = ItemModel.fromJson(validJson);

      expect(item.latitude, isNull);
      expect(item.longitude, isNull);
    });

    /// TC-FI15: fromJson dengan photo_urls sebagai string list
    test('fromJson handles string photo_urls list', () {
      validJson['photo_urls'] = ['/photo1.jpg', '/photo2.jpg'];
      final item = ItemModel.fromJson(validJson);

      expect(item.photoUrls.length, 2);
      expect(item.photoUrls[0], '/photo1.jpg');
    });
  });
}
