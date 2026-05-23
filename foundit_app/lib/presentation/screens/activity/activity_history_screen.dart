import 'package:flutter/material.dart';
import '../../../data/model/activity_entry.dart';
import '../../../data/repository/activity_repository.dart';
import '../../../shared/utils/utils.dart';
import '../../../shared/widget/widgets.dart';

class ActivityHistoryScreen extends StatefulWidget {
  const ActivityHistoryScreen({super.key});

  @override
  State<ActivityHistoryScreen> createState() => _ActivityHistoryScreenState();
}

class _ActivityHistoryScreenState extends State<ActivityHistoryScreen> {
  final ActivityRepository _repository = ActivityRepository();

  bool _isLoading = true;
  String? _errorMessage;
  List<ActivityEntry> _activities = [];

  @override
  void initState() {
    super.initState();
    _loadActivities();
  }

  Future<void> _loadActivities() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final data = await _repository.getActivities(limit: 50);
      if (mounted) {
        setState(() {
          _activities = data;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _errorMessage = e.toString().replaceFirst('Exception: ', '');
          _isLoading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: AppColors.textPrimary),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Riwayat Aktivitas',
          style: AppTextStyles.h3.copyWith(color: AppColors.textPrimary),
        ),
        centerTitle: true,
      ),
      body: _buildBody(),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_errorMessage != null) {
      return ErrorState(message: _errorMessage, onRetry: _loadActivities);
    }

    if (_activities.isEmpty) {
      return const EmptyState(
        icon: Icons.history,
        title: 'Belum Ada Aktivitas',
        subtitle:
            'Riwayat aktivitas akan muncul setelah kamu mulai menggunakan aplikasi',
      );
    }

    return RefreshIndicator(
      onRefresh: _loadActivities,
      child: ListView.builder(
        padding: const EdgeInsets.all(AppSpacing.md),
        itemCount: _activities.length,
        itemBuilder: (context, index) {
          final activity = _activities[index];
          final isLast = index == _activities.length - 1;
          final isFirst = index == 0;
          return _buildActivityItem(activity, isLast, isFirst);
        },
      ),
    );
  }

  Widget _buildActivityItem(ActivityEntry activity, bool isLast, bool isFirst) {
    final color = _getActivityColor(activity.color);

    return IntrinsicHeight(
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 24,
            child: Column(
              children: [
                Container(
                  width: 12,
                  height: 12,
                  decoration: BoxDecoration(
                    color: color,
                    shape: BoxShape.circle,
                  ),
                ),
                if (!isLast)
                  Expanded(child: Container(width: 2, color: AppColors.border)),
              ],
            ),
          ),
          const SizedBox(width: AppSpacing.sm),

          Expanded(
            child: Container(
              margin: EdgeInsets.only(bottom: isLast ? 0 : AppSpacing.md),
              padding: const EdgeInsets.all(AppSpacing.md),
              decoration: BoxDecoration(
                color: isFirst ? color.withAlpha(13) : AppColors.surface,
                borderRadius: BorderRadius.circular(AppRadius.md),
                border: Border.all(
                  color: isFirst ? color.withAlpha(77) : AppColors.border,
                ),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withAlpha(13),
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Status badge and "Terbaru" label
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      // Activity type badge
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: AppSpacing.sm,
                          vertical: 2,
                        ),
                        decoration: BoxDecoration(
                          color: color.withAlpha(26),
                          borderRadius: BorderRadius.circular(AppRadius.full),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(
                              _getActivityIcon(activity.icon),
                              size: 12,
                              color: color,
                            ),
                            const SizedBox(width: 4),
                            Text(
                              activity.title,
                              style: AppTextStyles.caption.copyWith(
                                color: color,
                                fontWeight: FontWeight.w600,
                                fontSize: 11,
                              ),
                            ),
                          ],
                        ),
                      ),
                      // "Terbaru" label for first item
                      if (isFirst)
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: AppSpacing.xs,
                            vertical: 2,
                          ),
                          decoration: BoxDecoration(
                            color: AppColors.primary.withAlpha(26),
                            borderRadius: BorderRadius.circular(AppRadius.sm),
                          ),
                          child: Text(
                            'Terbaru',
                            style: AppTextStyles.caption.copyWith(
                              color: AppColors.primary,
                              fontWeight: FontWeight.w600,
                              fontSize: 10,
                            ),
                          ),
                        ),
                    ],
                  ),
                  const SizedBox(height: AppSpacing.sm),

                  // Photo and description row
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Photo thumbnail
                      ClipRRect(
                        borderRadius: BorderRadius.circular(AppRadius.sm),
                        child: activity.photoUrl != null
                            ? Image.network(
                                AppConstants.getFullImageUrl(
                                  activity.photoUrl,
                                )!,
                                width: 50,
                                height: 50,
                                fit: BoxFit.cover,
                                errorBuilder: (c, e, s) =>
                                    const ImagePlaceholder(size: 50),
                              )
                            : const ImagePlaceholder(size: 50),
                      ),
                      const SizedBox(width: AppSpacing.md),

                      // Description
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              activity.description,
                              style: AppTextStyles.body.copyWith(fontSize: 13),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                            ),
                            const SizedBox(height: AppSpacing.sm),
                            Text(
                              DateFormatters.formatTimeAgo(activity.createdAt),
                              style: AppTextStyles.caption.copyWith(
                                color: AppColors.textTertiary,
                                fontSize: 11,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Color _getActivityColor(String colorName) {
    switch (colorName) {
      case 'success':
        return AppColors.success;
      case 'warning':
        return AppColors.warning;
      case 'error':
        return AppColors.error;
      case 'info':
        return AppColors.primary;
      case 'primary':
      default:
        return AppColors.primary;
    }
  }

  IconData _getActivityIcon(String iconName) {
    switch (iconName) {
      case 'search':
        return Icons.search;
      case 'inventory_2':
        return Icons.inventory_2_outlined;
      case 'how_to_reg':
        return Icons.how_to_reg_outlined;
      case 'person_add':
        return Icons.person_add_outlined;
      case 'check_circle':
        return Icons.check_circle_outline;
      case 'cancel':
        return Icons.cancel_outlined;
      default:
        return Icons.info_outline;
    }
  }
}