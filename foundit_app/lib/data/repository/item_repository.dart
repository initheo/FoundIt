import 'dart:convert';
import 'dart:io';
import '../model/item_model.dart';
import '../services/services.dart';
import '../usecase/request/create_item_request.dart';
import '../usecase/response/get_items_response.dart';

class ItemRepository {
  final HttpService _httpService = HttpService();

  // ============ GET ALL ITEMS ============
  Future<List<ItemModel>> getItems({String? type, String? search}) async {
    String endpoint = '/items';
    List<String> params = [];

    if (type != null) params.add('type=$type');
    if (search != null) params.add('search=${Uri.encodeComponent(search)}');

    if (params.isNotEmpty) {
      endpoint += '?${params.join('&')}';
    }

    final response = await _httpService.get(endpoint);

    if (response.statusCode == 200) {
      final parsed = GetItemsResponse.fromJson(response.body);
      return parsed.data;
    } else {
      final body = jsonDecode(response.body);
      throw Exception(body['message'] ?? 'Gagal mengambil data items');
    }
  }

  // ============ GET STATISTICS ============
  Future<Map<String, int>> getStatistics() async {
    final response = await _httpService.get('/items/stats');

    if (response.statusCode == 200) {
      final body = jsonDecode(response.body);
      final data = body['data'];
      return {
        'lost': data['lost'] ?? 0,
        'found': data['found'] ?? 0,
        'returned': data['returned'] ?? 0,
      };
    } else {
      final body = jsonDecode(response.body);
      throw Exception(body['message'] ?? 'Gagal mengambil statistik');
    }
  }

  // ============ GET SINGLE ITEM ============
  Future<ItemModel> getItem(int id) async {
    final response = await _httpService.get('/items/$id');

    if (response.statusCode == 200) {
      final body = jsonDecode(response.body);
      return ItemModel.fromJson(body['data']);
    } else if (response.statusCode == 404) {
      throw Exception('Item tidak ditemukan');
    } else {
      final body = jsonDecode(response.body);
      throw Exception(body['message'] ?? 'Gagal mengambil detail item');
    }
  }

  // ============ GET MY ITEMS ============
  Future<List<ItemModel>> getMyItems() async {
    final response = await _httpService.get('/items/my');

    if (response.statusCode == 200) {
      final body = jsonDecode(response.body);
      final List<dynamic> data = body['data'];
      return data.map((json) => ItemModel.fromJson(json)).toList();
    } else {
      final body = jsonDecode(response.body);
      throw Exception(body['message'] ?? 'Gagal mengambil data items');
    }
  }

  // ============ CREATE ITEM ============
  Future<ItemModel> createItem(CreateItemRequest request) async {
    final response = await _httpService.postMultipartWithFiles(
      '/items',
      request.toFields(),
      files: request.photos,
      filesFieldName: 'photos',
    );

    final responseBody = await response.stream.bytesToString();

    if (response.statusCode == 201) {
      final body = jsonDecode(responseBody);
      return ItemModel.fromJson(body['data']);
    } else {
      final body = jsonDecode(responseBody);
      throw Exception(body['message'] ?? 'Gagal membuat laporan');
    }
  }

  // ============ UPDATE ITEM ============
  Future<ItemModel> updateItem(int id, Map<String, dynamic> data) async {
    final response = await _httpService.put('/items/$id', body: data);

    if (response.statusCode == 200) {
      final body = jsonDecode(response.body);
      return ItemModel.fromJson(body['data']);
    } else if (response.statusCode == 404) {
      throw Exception('Item tidak ditemukan atau bukan milik Anda');
    } else {
      final body = jsonDecode(response.body);
      throw Exception(body['message'] ?? 'Gagal mengupdate item');
    }
  }

  Future<void> updateStatus(int id, String status, {String? verificationCode}) async {
    final response = await _httpService.put(
      '/items/$id/status',
      body: {
        'status': status,
        if (verificationCode != null) 'verification_code': verificationCode,
      },
    );

    if (response.statusCode != 200) {
      final body = jsonDecode(response.body);
      throw Exception(body['message'] ?? 'Gagal mengupdate status');
    }
  }

  // ============ DELETE ITEM ============
  Future<void> deleteItem(int id) async {
    final response = await _httpService.delete('/items/$id');

    if (response.statusCode != 200) {
      final body = jsonDecode(response.body);
      throw Exception(body['message'] ?? 'Gagal menghapus item');
    }
  }

  // ============ ADD PHOTO TO ITEM ============
  Future<Map<String, dynamic>> addItemPhoto(int itemId, File photo) async {
    final response = await _httpService.postMultipart(
      '/items/$itemId/photos',
      {},
      image: photo,
      imageFieldName: 'photo',
    );

    final responseBody = await response.stream.bytesToString();

    if (response.statusCode == 200) {
      final body = jsonDecode(responseBody);
      return body['data'];
    } else {
      final body = jsonDecode(responseBody);
      throw Exception(body['message'] ?? 'Gagal menambah foto');
    }
  }

  // ============ DELETE PHOTO FROM ITEM ============
  Future<void> deleteItemPhoto(int itemId, int photoId) async {
    final response = await _httpService.delete(
      '/items/$itemId/photos/$photoId',
    );

    if (response.statusCode != 200) {
      final body = jsonDecode(response.body);
      throw Exception(body['message'] ?? 'Gagal menghapus foto');
    }
  }
}
