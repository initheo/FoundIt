import 'package:flutter_test/flutter_test.dart';
import 'package:foundit_app/data/model/claim_model.dart';

/// Unit Test Suite: ClaimModelTest
/// Skenario Utama: Validasi parsing JSON, getter status, dan logika model ClaimModel
void main() {
  group('ClaimModel', () {
    late Map<String, dynamic> validJson;

    setUp(() {
      validJson = {
        'id': 1,
        'item_id': 10,
        'claimer_id': 5,
        'reason': 'Ini dompet saya yang hilang kemarin',
        'status': 'pending',
        'rejection_reason': null,
        'reviewed_at': null,
        'created_at': '2025-06-10 10:00:00',
        'updated_at': '2025-06-10 10:00:00',
        'claimer': {
          'name': 'Budi Santoso',
          'email': 'budi@student.uisi.ac.id',
          'prodi_unit': 'Sistem Informasi',
          'photo_url': null,
          'phone': '08123456789',
        },
        'item': {
          'title': 'Dompet Hitam',
          'category': {'name': 'Dompet'},
          'photo_url': '/storage/photos/dompet.jpg',
        },
      };
    });

    /// TC-FC01: fromJson parsing data lengkap berhasil
    test('fromJson parses complete JSON correctly', () {
      final claim = ClaimModel.fromJson(validJson);

      expect(claim.id, 1);
      expect(claim.itemId, 10);
      expect(claim.claimerId, 5);
      expect(claim.reason, 'Ini dompet saya yang hilang kemarin');
      expect(claim.status, 'pending');
    });

    /// TC-FC02: fromJson parsing claimer info nested
    test('fromJson parses nested claimer info', () {
      final claim = ClaimModel.fromJson(validJson);

      expect(claim.claimerName, 'Budi Santoso');
      expect(claim.claimerEmail, 'budi@student.uisi.ac.id');
      expect(claim.claimerProdiUnit, 'Sistem Informasi');
      expect(claim.claimerPhone, '08123456789');
    });

    /// TC-FC03: fromJson parsing item info nested
    test('fromJson parses nested item info', () {
      final claim = ClaimModel.fromJson(validJson);

      expect(claim.itemTitle, 'Dompet Hitam');
      expect(claim.itemCategory, 'Dompet');
      expect(claim.itemPhotoUrl, '/storage/photos/dompet.jpg');
    });

    /// TC-FC04: isPending getter benar untuk status pending
    test('isPending returns true for pending status', () {
      final claim = ClaimModel.fromJson(validJson);
      expect(claim.isPending, true);
      expect(claim.isApproved, false);
      expect(claim.isRejected, false);
    });

    /// TC-FC05: isApproved getter benar untuk status approved
    test('isApproved returns true for approved status', () {
      validJson['status'] = 'approved';
      validJson['reviewed_at'] = '2025-06-11 09:00:00';
      final claim = ClaimModel.fromJson(validJson);

      expect(claim.isApproved, true);
      expect(claim.isPending, false);
      expect(claim.reviewedAt, isNotNull);
    });

    /// TC-FC06: isRejected getter benar untuk status rejected
    test('isRejected returns true for rejected status', () {
      validJson['status'] = 'rejected';
      validJson['rejection_reason'] = 'Bukti tidak cukup';
      final claim = ClaimModel.fromJson(validJson);

      expect(claim.isRejected, true);
      expect(claim.rejectionReason, 'Bukti tidak cukup');
    });

    /// TC-FC07: claimerInitial mengambil huruf pertama nama
    test('claimerInitial returns first letter uppercase', () {
      final claim = ClaimModel.fromJson(validJson);
      expect(claim.claimerInitial, 'B');
    });

    /// TC-FC08: claimerInitial mengembalikan ? jika nama null
    test('claimerInitial returns ? when name is null', () {
      validJson['claimer'] = null;
      final claim = ClaimModel.fromJson(validJson);
      expect(claim.claimerInitial, '?');
    });

    /// TC-FC09: fromJson dengan id sebagai String
    test('fromJson handles string ids correctly', () {
      validJson['id'] = '42';
      validJson['item_id'] = '15';
      validJson['claimer_id'] = '7';
      final claim = ClaimModel.fromJson(validJson);

      expect(claim.id, 42);
      expect(claim.itemId, 15);
      expect(claim.claimerId, 7);
    });

    /// TC-FC10: toJson menghasilkan map yang benar
    test('toJson produces correct map', () {
      final claim = ClaimModel.fromJson(validJson);
      final json = claim.toJson();

      expect(json['id'], 1);
      expect(json['item_id'], 10);
      expect(json['claimer_id'], 5);
      expect(json['reason'], 'Ini dompet saya yang hilang kemarin');
      expect(json['status'], 'pending');
    });
  });
}
