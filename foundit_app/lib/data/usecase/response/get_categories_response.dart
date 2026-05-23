import 'dart:convert';

import '../../model/category_model.dart';

/// Response untuk GET /categories
class GetCategoriesResponse {
  final bool success;
  final String message;
  final List<CategoryModel> data;

  GetCategoriesResponse({
    required this.success,
    required this.message,
    required this.data,
  });

  factory GetCategoriesResponse.fromJson(String str) =>
      GetCategoriesResponse.fromMap(json.decode(str));

  factory GetCategoriesResponse.fromMap(Map<String, dynamic> json) =>
      GetCategoriesResponse(
        success: json['success'] ?? false,
        message: json['message'] ?? '',
        data: json['data'] != null
            ? List<CategoryModel>.from(
                json['data'].map((x) => CategoryModel.fromJson(x)),
              )
            : [],
      );
}
