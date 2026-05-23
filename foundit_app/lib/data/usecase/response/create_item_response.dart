import 'dart:convert';

import '../../model/item_model.dart';

/// Response untuk POST /items (Create Item)
class CreateItemResponse {
  final bool success;
  final String message;
  final ItemModel? data;

  CreateItemResponse({required this.success, this.message = '', this.data});

  factory CreateItemResponse.fromJson(String str) =>
      CreateItemResponse.fromMap(json.decode(str));

  factory CreateItemResponse.fromMap(Map<String, dynamic> json) =>
      CreateItemResponse(
        success: json['success'] ?? false,
        message: json['message'] ?? '',
        data: json['data'] != null ? ItemModel.fromJson(json['data']) : null,
      );
}
