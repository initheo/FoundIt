import 'package:flutter_test/flutter_test.dart';
import 'package:foundit_app/data/model/category_model.dart';

/// Unit Test Suite: CategoryModelTest
/// Skenario Utama: Validasi parsing JSON dan serialisasi model CategoryModel
void main() {
  group('CategoryModel', () {
    /// TC-FCAT01: fromJson parsing data lengkap berhasil
    test('fromJson parses complete JSON correctly', () {
      final json = {'id': 1, 'name': 'Elektronik'};
      final category = CategoryModel.fromJson(json);

      expect(category.id, 1);
      expect(category.name, 'Elektronik');
    });

    /// TC-FCAT02: fromJson dengan id sebagai String
    test('fromJson handles string id correctly', () {
      final json = {'id': '5', 'name': 'Dokumen'};
      final category = CategoryModel.fromJson(json);

      expect(category.id, 5);
    });

    /// TC-FCAT03: toJson menghasilkan map yang benar
    test('toJson produces correct map', () {
      final category = CategoryModel(id: 3, name: 'Pakaian');
      final json = category.toJson();

      expect(json['id'], 3);
      expect(json['name'], 'Pakaian');
    });

    /// TC-FCAT04: toJson roundtrip (fromJson -> toJson -> fromJson)
    test('toJson roundtrip preserves data', () {
      final original = {'id': 2, 'name': 'Kunci'};
      final cat1 = CategoryModel.fromJson(original);
      final json = cat1.toJson();
      final cat2 = CategoryModel.fromJson(json);

      expect(cat2.id, cat1.id);
      expect(cat2.name, cat1.name);
    });

    /// TC-FCAT05: Constructor membuat instance dengan benar
    test('constructor creates instance correctly', () {
      final category = CategoryModel(id: 10, name: 'Tas');

      expect(category.id, 10);
      expect(category.name, 'Tas');
    });
  });
}
