/// Request untuk GET /items dengan filter (Home Screen)
class GetItemsRequest {
  final String? type; // 'lost' atau 'found'
  final int? categoryId;
  final String? search;

  GetItemsRequest({this.type, this.categoryId, this.search});

  /// Convert to query parameters
  Map<String, String> toQueryParams() {
    final params = <String, String>{};

    if (type != null) {
      params['type'] = type!;
    }
    if (categoryId != null) {
      params['category_id'] = categoryId.toString();
    }
    if (search != null && search!.isNotEmpty) {
      params['search'] = search!;
    }

    return params;
  }

  /// Build query string
  String toQueryString() {
    final params = toQueryParams();
    if (params.isEmpty) return '';
    final queryParams = params.entries
        .map((e) => '${e.key}=${Uri.encodeComponent(e.value)}')
        .join('&');
    return '?$queryParams';
  }
}
