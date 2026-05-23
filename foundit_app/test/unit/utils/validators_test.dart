import 'package:flutter_test/flutter_test.dart';
import 'package:foundit_app/shared/utils/validators.dart';

/// Unit Test Suite: ValidatorsTest
/// Skenario Utama: Validasi logika form validators
void main() {
  group('Validators', () {
    group('required', () {
      /// TC-FV01: required mengembalikan error jika null
      test('returns error when value is null', () {
        expect(Validators.required(null), isNotNull);
      });

      /// TC-FV02: required mengembalikan error jika string kosong
      test('returns error when value is empty string', () {
        expect(Validators.required(''), isNotNull);
        expect(Validators.required('   '), isNotNull);
      });

      /// TC-FV03: required mengembalikan null jika valid
      test('returns null when value is valid', () {
        expect(Validators.required('hello'), isNull);
      });

      /// TC-FV04: required menyertakan nama field di pesan error
      test('includes field name in error message', () {
        final result = Validators.required(null, 'Nama');
        expect(result, contains('Nama'));
      });
    });

    group('uisiEmail', () {
      /// TC-FV05: uisiEmail valid untuk domain student.uisi.ac.id
      test('returns null for valid student email', () {
        expect(Validators.uisiEmail('budi@student.uisi.ac.id'), isNull);
      });

      /// TC-FV06: uisiEmail valid untuk domain uisi.ac.id
      test('returns null for valid staff email', () {
        expect(Validators.uisiEmail('dosen@uisi.ac.id'), isNull);
      });

      /// TC-FV07: uisiEmail error untuk domain non-UISI
      test('returns error for non-UISI domain', () {
        expect(Validators.uisiEmail('user@gmail.com'), isNotNull);
      });

      /// TC-FV08: uisiEmail error untuk format email invalid
      test('returns error for invalid email format', () {
        expect(Validators.uisiEmail('bukan-email'), isNotNull);
      });

      /// TC-FV09: uisiEmail error jika kosong
      test('returns error when empty', () {
        expect(Validators.uisiEmail(''), isNotNull);
        expect(Validators.uisiEmail(null), isNotNull);
      });
    });

    group('password', () {
      /// TC-FV10: password error jika kosong
      test('returns error when empty', () {
        expect(Validators.password(''), isNotNull);
        expect(Validators.password(null), isNotNull);
      });

      /// TC-FV11: password error jika kurang dari minimum
      test('returns error when too short', () {
        expect(Validators.password('12345'), isNotNull);
      });

      /// TC-FV12: password valid jika memenuhi minimum
      test('returns null when meets minimum length', () {
        expect(Validators.password('123456'), isNull);
      });
    });

    group('confirmPassword', () {
      /// TC-FV13: confirmPassword error jika tidak cocok
      test('returns error when passwords do not match', () {
        expect(Validators.confirmPassword('abc', '123'), isNotNull);
      });

      /// TC-FV14: confirmPassword valid jika cocok
      test('returns null when passwords match', () {
        expect(Validators.confirmPassword('secret123', 'secret123'), isNull);
      });
    });

    group('phone', () {
      /// TC-FV15: phone valid untuk nomor 08xxx
      test('returns null for valid 08 phone number', () {
        expect(Validators.phone('081234567890'), isNull);
      });

      /// TC-FV16: phone valid untuk nomor +62xxx
      test('returns null for valid +62 phone number', () {
        expect(Validators.phone('+6281234567890'), isNull);
      });

      /// TC-FV17: phone error untuk prefix invalid
      test('returns error for invalid prefix', () {
        expect(Validators.phone('091234567890'), isNotNull);
      });

      /// TC-FV18: phone null (optional) jika kosong
      test('returns null when empty (optional field)', () {
        expect(Validators.phone(''), isNull);
        expect(Validators.phone(null), isNull);
      });

      /// TC-FV19: phone error jika terlalu pendek
      test('returns error when too short', () {
        expect(Validators.phone('0812345'), isNotNull);
      });
    });

    group('name', () {
      /// TC-FV20: name error jika kosong
      test('returns error when empty', () {
        expect(Validators.name(''), isNotNull);
        expect(Validators.name(null), isNotNull);
      });

      /// TC-FV21: name error jika kurang dari 2 karakter
      test('returns error when less than 2 characters', () {
        expect(Validators.name('A'), isNotNull);
      });

      /// TC-FV22: name valid jika memenuhi syarat
      test('returns null when valid', () {
        expect(Validators.name('Budi'), isNull);
      });
    });
  });
}
