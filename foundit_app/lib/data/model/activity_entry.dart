/// Model untuk entry aktivitas
class ActivityEntry {
  final String id;
  final String type;
  final String title;
  final String description;
  final String icon;
  final String color;
  final int? referenceId;
  final String? referenceType;
  final String? photoUrl;
  final DateTime createdAt;

  ActivityEntry({
    required this.id,
    required this.type,
    required this.title,
    required this.description,
    required this.icon,
    required this.color,
    this.referenceId,
    this.referenceType,
    this.photoUrl,
    required this.createdAt,
  });

  factory ActivityEntry.fromJson(Map<String, dynamic> json) {
    return ActivityEntry(
      id: json['id'] ?? '',
      type: json['type'] ?? '',
      title: json['title'] ?? '',
      description: json['description'] ?? '',
      icon: json['icon'] ?? 'info',
      color: json['color'] ?? 'primary',
      referenceId: json['reference_id'],
      referenceType: json['reference_type'],
      photoUrl: json['photo_url'],
      createdAt: DateTime.parse(
        json['created_at'] ?? DateTime.now().toIso8601String(),
      ),
    );
  }
}