import '../model/category_model.dart';
import '../services/services.dart';
import '../usecase/response/get_categories_response.dart';

class CategoryRepository {
  final HttpService _httpService = HttpService();

  Future<List<CategoryModel>> getCategories() async {
    final response = await _httpService.get('/categories');

    if (response.statusCode == 200) {
      final parsed = GetCategoriesResponse.fromJson(response.body);
      return parsed.data;
    } else {
      throw Exception('Gagal mengambil data kategori');
    }
  }
}
