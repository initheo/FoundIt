import 'dart:convert';

import '../../model/item_model.dart';

/// Response untuk GET /items
class GetItemsResponse {
  final bool success;
  final String message;
  final List<ItemModel> data;

  GetItemsResponse({
    required this.success,
    required this.message,
    required this.data,
  });

  factory GetItemsResponse.fromJson(String str) =>
      GetItemsResponse.fromMap(json.decode(str));

  factory GetItemsResponse.fromMap(Map<String, dynamic> json) =>
      GetItemsResponse(
        success: json['success'] ?? false,
        message: json['message'] ?? '',
        data: json['data'] != null
            ? List<ItemModel>.from(
                json['data'].map((x) => ItemModel.fromJson(x)),
              )
            : [],
      );
}
