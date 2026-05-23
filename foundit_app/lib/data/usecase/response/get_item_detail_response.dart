import 'dart:convert';

import '../../model/item_model.dart';

/// Response untuk GET /items/{id} (Item Detail)
class GetItemDetailResponse {
  final bool success;
  final String message;
  final ItemModel? data;

  GetItemDetailResponse({required this.success, this.message = '', this.data});

  factory GetItemDetailResponse.fromJson(String str) =>
      GetItemDetailResponse.fromMap(json.decode(str));

  factory GetItemDetailResponse.fromMap(Map<String, dynamic> json) =>
      GetItemDetailResponse(
        success: json['success'] ?? false,
        message: json['message'] ?? '',
        data: json['data'] != null ? ItemModel.fromJson(json['data']) : null,
      );
}
