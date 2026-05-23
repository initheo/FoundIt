import 'dart:convert';

import '../model/claim_model.dart';
import '../services/http_service.dart';

class ClaimRepository {
  final HttpService _httpService = HttpService();

  /// Get claims for an item (only owner can see)
  /// GET /items/{id}/claims
  Future<List<ClaimModel>> getItemClaims(int itemId) async {
    try {
      final response = await _httpService.get('/items/$itemId/claims');
      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        final List<dynamic> claimsJson = data['data'] ?? [];
        return claimsJson.map((json) => ClaimModel.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Gagal memuat klaim');
      }
    } catch (e) {
      rethrow;
    }
  }

  /// Submit a claim for an item
  /// POST /claims
  Future<ClaimModel> submitClaim({
    required int itemId,
    required String reason,
  }) async {
    try {
      final response = await _httpService.post(
        '/claims',
        body: {'item_id': itemId, 'reason': reason},
      );
      final data = jsonDecode(response.body);

      if (response.statusCode == 201 && data['success'] == true) {
        return ClaimModel.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Gagal mengirim klaim');
      }
    } catch (e) {
      rethrow;
    }
  }

  /// Get my claims (claims I submitted)
  /// GET /claims/my
  Future<List<ClaimModel>> myClaims() async {
    try {
      final response = await _httpService.get('/claims/my');
      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        final List<dynamic> claimsJson = data['data'] ?? [];
        return claimsJson.map((json) => ClaimModel.fromJson(json)).toList();
      } else {
        throw Exception(data['message'] ?? 'Gagal memuat klaim');
      }
    } catch (e) {
      rethrow;
    }
  }

  /// Approve a claim (only item owner can do this)
  /// PUT /claims/{id}/approve
  Future<ClaimModel> approveClaim(int claimId) async {
    try {
      final response = await _httpService.put('/claims/$claimId/approve');
      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        return ClaimModel.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Gagal menyetujui klaim');
      }
    } catch (e) {
      rethrow;
    }
  }

  /// Reject a claim (only item owner can do this)
  /// PUT /claims/{id}/reject
  Future<ClaimModel> rejectClaim(int claimId, String reason) async {
    try {
      final response = await _httpService.put(
        '/claims/$claimId/reject',
        body: {'reason': reason},
      );
      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        return ClaimModel.fromJson(data['data']);
      } else {
        throw Exception(data['message'] ?? 'Gagal menolak klaim');
      }
    } catch (e) {
      rethrow;
    }
  }
}
