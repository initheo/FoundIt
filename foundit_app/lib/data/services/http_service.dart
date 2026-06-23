import 'dart:convert';
import 'dart:io';

import 'package:http/http.dart' as http;

import '../../shared/utils/utils.dart';
import 'services.dart';

class HttpService {
  final String baseUrl = AppConstants.apiBaseUrl;
  final SecureStorageService _secureStorage = SecureStorageService();

  Future<String?> _getToken() async {
    return await _secureStorage.getToken();
  }

  Future<Map<String, String>> _headers({bool isMultipart = false}) async {
    final token = await _getToken();
    final headers = <String, String>{'Accept': 'application/json'};
    if (!isMultipart) {
      headers['Content-Type'] = 'application/json';
    }
    if (token != null) {
      headers['Authorization'] = 'Bearer $token';
    }
    return headers;
  }

  Future<http.Response> get(String endpoint) async {
    final url = Uri.parse('$baseUrl$endpoint');
    final headers = await _headers();
    return await http
        .get(url, headers: headers)
        .timeout(Duration(seconds: AppConstants.apiTimeout));
  }

  Future<http.Response> post(
    String endpoint, {
    Map<String, dynamic>? body,
  }) async {
    final url = Uri.parse('$baseUrl$endpoint');
    final headers = await _headers();
    return await http
        .post(
          url,
          headers: headers,
          body: body != null ? jsonEncode(body) : null,
        )
        .timeout(Duration(seconds: AppConstants.apiTimeout));
  }

  Future<http.Response> put(
    String endpoint, {
    Map<String, dynamic>? body,
  }) async {
    final url = Uri.parse('$baseUrl$endpoint');
    final headers = await _headers();
    return await http
        .put(
          url,
          headers: headers,
          body: body != null ? jsonEncode(body) : null,
        )
        .timeout(Duration(seconds: AppConstants.apiTimeout));
  }

  Future<http.Response> delete(String endpoint) async {
    final url = Uri.parse('$baseUrl$endpoint');
    final headers = await _headers();
    return await http
        .delete(url, headers: headers)
        .timeout(Duration(seconds: AppConstants.apiTimeout));
  }

  Future<http.StreamedResponse> postMultipart(
    String endpoint,
    Map<String, String> fields, {
    File? image,
    String imageFieldName = 'image',
  }) async {
    final url = Uri.parse('$baseUrl$endpoint');
    final headers = await _headers(isMultipart: true);
    final request = http.MultipartRequest('POST', url);
    request.headers.addAll(headers);
    request.fields.addAll(fields);

    if (image != null) {
      request.files.add(
        await http.MultipartFile.fromPath(imageFieldName, image.path),
      );
    }

    return await request.send().timeout(
      Duration(seconds: AppConstants.apiTimeout),
    );
  }

  Future<http.StreamedResponse> putMultipart(
    String endpoint,
    Map<String, String> fields, {
    File? image,
    String imageFieldName = 'image',
  }) async {
    final url = Uri.parse('$baseUrl$endpoint');
    final headers = await _headers(isMultipart: true);
    final request = http.MultipartRequest('POST', url);
    request.headers.addAll(headers);
    request.fields.addAll(fields);
    request.fields['_method'] = 'PUT';

    if (image != null) {
      request.files.add(
        await http.MultipartFile.fromPath(imageFieldName, image.path),
      );
    }

    return await request.send().timeout(
      Duration(seconds: AppConstants.apiTimeout),
    );
  }

  /// POST multipart with multiple files support
  Future<http.StreamedResponse> postMultipartWithFiles(
    String endpoint,
    Map<String, String> fields, {
    List<File>? files,
    String filesFieldName = 'photos',
  }) async {
    final url = Uri.parse('$baseUrl$endpoint');
    final headers = await _headers(isMultipart: true);
    final request = http.MultipartRequest('POST', url);
    request.headers.addAll(headers);
    request.fields.addAll(fields);

    if (files != null && files.isNotEmpty) {
      for (int i = 0; i < files.length; i++) {
        request.files.add(
          await http.MultipartFile.fromPath(
            '$filesFieldName[$i]',
            files[i].path,
          ),
        );
      }
    }

    return await request.send().timeout(
      Duration(seconds: AppConstants.apiTimeout),
    );
  }
}
