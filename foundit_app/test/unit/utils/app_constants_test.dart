import 'package:flutter_test/flutter_test.dart';
import 'package:foundit_app/shared/utils/app_constants.dart';

void main() {
  group('AppConstants Tests', () {
    test('apiBaseUrl returns correct production url', () {
      expect(AppConstants.apiBaseUrl, 'https://foundit.neoartd.my.id/api');
    });

    test('storageBaseUrl returns correct production url', () {
      expect(AppConstants.storageBaseUrl, 'https://foundit.neoartd.my.id');
    });

    test('getFullImageUrl returns correct values', () {
      expect(AppConstants.getFullImageUrl(null), null);
      expect(AppConstants.getFullImageUrl(''), null);
      expect(AppConstants.getFullImageUrl('http://example.com/image.jpg'), 'http://example.com/image.jpg');
      expect(AppConstants.getFullImageUrl('/storage/image.jpg'), 'https://foundit.neoartd.my.id/storage/image.jpg');
    });
  });
}
