/// Model untuk Claim dari API
class ClaimModel {
  final int id;
  final int itemId;
  final int claimerId;
  final String reason;
  final String status; // pending, approved, rejected
  final String? rejectionReason;
  final DateTime? reviewedAt;
  final DateTime createdAt;
  final DateTime updatedAt;

  // Optional: claimer info
  final String? claimerName;
  final String? claimerEmail;
  final String? claimerProdiUnit;
  final String? claimerPhotoUrl;
  final String? claimerPhone;

  // Optional: item info (for my claims list)
  final String? itemTitle;
  final String? itemCategory;
  final String? itemPhotoUrl;

  ClaimModel({
    required this.id,
    required this.itemId,
    required this.claimerId,
    required this.reason,
    required this.status,
    this.rejectionReason,
    this.reviewedAt,
    required this.createdAt,
    required this.updatedAt,
    this.claimerName,
    this.claimerEmail,
    this.claimerProdiUnit,
    this.claimerPhotoUrl,
    this.claimerPhone,
    this.itemTitle,
    this.itemCategory,
    this.itemPhotoUrl,
  });

  factory ClaimModel.fromJson(Map<String, dynamic> json) {
    // Parse claimer info if available
    final claimer = json['claimer'] as Map<String, dynamic>?;
    // Parse item info if available
    final item = json['item'] as Map<String, dynamic>?;

    return ClaimModel(
      id: json['id'] is int ? json['id'] : int.parse(json['id'].toString()),
      itemId: json['item_id'] is int
          ? json['item_id']
          : int.parse(json['item_id'].toString()),
      claimerId: json['claimer_id'] is int
          ? json['claimer_id']
          : int.parse(json['claimer_id'].toString()),
      reason: json['reason'] ?? '',
      status: json['status'] ?? 'pending',
      rejectionReason: json['rejection_reason'],
      reviewedAt: json['reviewed_at'] != null
          ? DateTime.parse(json['reviewed_at'])
          : null,
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
      claimerName: claimer?['name'],
      claimerEmail: claimer?['email'],
      claimerProdiUnit: claimer?['prodi_unit'],
      claimerPhotoUrl: claimer?['photo_url'],
      claimerPhone: claimer?['phone'],
      itemTitle: item?['title'],
      itemCategory: item?['category']?['name'],
      itemPhotoUrl: item?['photo_url'],
    );
  }

  Map<String, dynamic> toJson() => {
    'id': id,
    'item_id': itemId,
    'claimer_id': claimerId,
    'reason': reason,
    'status': status,
    'reviewed_at': reviewedAt?.toIso8601String(),
    'created_at': createdAt.toIso8601String(),
    'updated_at': updatedAt.toIso8601String(),
  };

  bool get isPending => status == 'pending';
  bool get isApproved => status == 'approved';
  bool get isRejected => status == 'rejected';

  /// Inisial dari nama claimer
  String get claimerInitial {
    if (claimerName != null && claimerName!.isNotEmpty) {
      return claimerName![0].toUpperCase();
    }
    return '?';
  }
}
