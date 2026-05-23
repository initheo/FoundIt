import 'package:flutter_test/flutter_test.dart';
import 'package:foundit_app/data/model/user_model.dart';

/// Unit Test Suite: UserModelTest
/// Skenario Utama: Validasi parsing JSON dan serialisasi model UserModel
void main() {
  group('UserModel', () {
    late Map<String, dynamic> validJson;

    setUp(() {
      validJson = {
        'id': 1,
        'name': 'Ahmad Fauzi',
        'email': 'ahmad@student.uisi.ac.id',
        'phone': '081234567890',
        'prodi_unit': 'Teknik Informatika',
        'photo_url': '/storage/photos/ahmad.jpg',
      };
    });

    /// TC-FU01: fromJson parsing data lengkap berhasil
    test('fromJson parses complete JSON correctly', () {
      final user = UserModel.fromJson(validJson);

      expect(user.id, 1);
      expect(user.name, 'Ahmad Fauzi');
      expect(user.email, 'ahmad@student.uisi.ac.id');
      expect(user.phone, '081234567890');
      expect(user.prodiUnit, 'Teknik Informatika');
      expect(user.photoUrl, '/storage/photos/ahmad.jpg');
    });

    /// TC-FU02: fromJson dengan field nullable bernilai null
    test('fromJson handles null optional fields', () {
      validJson['phone'] = null;
      validJson['prodi_unit'] = null;
      validJson['photo_url'] = null;
      final user = UserModel.fromJson(validJson);

      expect(user.phone, isNull);
      expect(user.prodiUnit, isNull);
      expect(user.photoUrl, isNull);
    });

    /// TC-FU03: toJson menghasilkan map yang benar
    test('toJson produces correct map', () {
      final user = UserModel.fromJson(validJson);
      final json = user.toJson();

      expect(json['id'], 1);
      expect(json['name'], 'Ahmad Fauzi');
      expect(json['email'], 'ahmad@student.uisi.ac.id');
      expect(json['phone'], '081234567890');
      expect(json['prodi_unit'], 'Teknik Informatika');
      expect(json['photo_url'], '/storage/photos/ahmad.jpg');
    });

    /// TC-FU04: toJson roundtrip (fromJson -> toJson -> fromJson)
    test('toJson roundtrip preserves data', () {
      final user1 = UserModel.fromJson(validJson);
      final json = user1.toJson();
      final user2 = UserModel.fromJson(json);

      expect(user2.id, user1.id);
      expect(user2.name, user1.name);
      expect(user2.email, user1.email);
    });

    /// TC-FU05: toJson dengan field null tetap menghasilkan key
    test('toJson includes null fields as keys', () {
      validJson['phone'] = null;
      final user = UserModel.fromJson(validJson);
      final json = user.toJson();

      expect(json.containsKey('phone'), true);
      expect(json['phone'], isNull);
    });
  });
}
