class UserModel {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final String? prodiUnit;
  final String? photoUrl;

  UserModel({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    this.prodiUnit,
    this.photoUrl,
  });

  // Buat UserModel dari JSON response API
  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      phone: json['phone'],
      prodiUnit: json['prodi_unit'],
      photoUrl: json['photo_url'],
    );
  }

  // Convert ke JSON untuk local storage
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone': phone,
      'prodi_unit': prodiUnit,
      'photo_url': photoUrl,
    };
  }
}
