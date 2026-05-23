import 'package:flutter/material.dart';
import '../../../data/model/leaderboard_entry.dart';
import '../../../data/repository/leaderboard_repository.dart';
import '../../../shared/utils/utils.dart';
import '../../../shared/widget/widgets.dart';

class LeaderboardScreen extends StatefulWidget {
  const LeaderboardScreen({super.key});

  @override
  State<LeaderboardScreen> createState() => _LeaderboardScreenState();
}

class _LeaderboardScreenState extends State<LeaderboardScreen> {
  final LeaderboardRepository _repository = LeaderboardRepository();

  bool _isLoading = true;
  String? _errorMessage;
  List<LeaderboardEntry> _leaderboard = [];

  @override
  void initState() {
    super.initState();
    _loadLeaderboard();
  }

  Future<void> _loadLeaderboard() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final data = await _repository.getLeaderboard(limit: 20);
      if (mounted) {
        setState(() {
          _leaderboard = data;
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
        title: Text('Leaderboard', style: AppTextStyles.h3),
        centerTitle: true,
        backgroundColor: AppColors.surface,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: _buildBody(),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_errorMessage != null) {
      return ErrorState(message: _errorMessage, onRetry: _loadLeaderboard);
    }

    if (_leaderboard.isEmpty) {
      return const EmptyState(
        icon: Icons.leaderboard_outlined,
        title: 'Belum Ada Data',
        subtitle: 'Leaderboard akan muncul ketika ada kontributor',
      );
    }

    return RefreshIndicator(
      onRefresh: _loadLeaderboard,
      child: ListView(
        padding: const EdgeInsets.all(AppSpacing.md),
        children: [
          // Header info
          _buildHeaderInfo(),
          const SizedBox(height: AppSpacing.lg),

          // Top 3 Podium
          if (_leaderboard.length >= 3) ...[
            _buildPodium(),
            const SizedBox(height: AppSpacing.lg),
          ],

          // Ranking List
          SectionHeader(
            title: 'Peringkat',
            count: _leaderboard.length,
            color: AppColors.primary,
          ),
          const SizedBox(height: AppSpacing.md),
          ..._leaderboard.asMap().entries.map((entry) {
            // Skip top 3 for list if podium is shown
            if (_leaderboard.length >= 3 && entry.key < 3) {
              return const SizedBox.shrink();
            }
            return _buildRankingCard(entry.value);
          }),
        ],
      ),
    );
  }

  Widget _buildHeaderInfo() {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [AppColors.primary, AppColors.primary.withAlpha(200)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(AppRadius.lg),
      ),
      child: Row(
        children: [
          Container(
            width: 50,
            height: 50,
            decoration: BoxDecoration(
              color: Colors.white.withAlpha(51),
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.emoji_events,
              color: Colors.white,
              size: 28,
            ),
          ),
          const SizedBox(width: AppSpacing.md),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Kontributor Terbaik',
                  style: AppTextStyles.h3.copyWith(color: Colors.white),
                ),
                const SizedBox(height: 4),
                Text(
                  'Pengguna yang paling banyak mengembalikan barang temuan',
                  style: AppTextStyles.caption.copyWith(
                    color: Colors.white.withAlpha(204),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPodium() {
    final first = _leaderboard[0];
    final second = _leaderboard[1];
    final third = _leaderboard[2];

    return Row(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        // 2nd place
        Expanded(child: _buildPodiumItem(second, 2, 100)),
        const SizedBox(width: AppSpacing.sm),
        // 1st place
        Expanded(child: _buildPodiumItem(first, 1, 130)),
        const SizedBox(width: AppSpacing.sm),
        // 3rd place
        Expanded(child: _buildPodiumItem(third, 3, 80)),
      ],
    );
  }

  Widget _buildPodiumItem(LeaderboardEntry entry, int rank, double height) {
    final colors = {
      1: const Color(0xFFFFD700), // Gold
      2: const Color(0xFFC0C0C0), // Silver
      3: const Color(0xFFCD7F32), // Bronze
    };

    return Column(
      children: [
        // Avatar
        Container(
          width: rank == 1 ? 70 : 55,
          height: rank == 1 ? 70 : 55,
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            border: Border.all(color: colors[rank]!, width: 3),
            boxShadow: [
              BoxShadow(
                color: colors[rank]!.withAlpha(77),
                blurRadius: 10,
                spreadRadius: 2,
              ),
            ],
          ),
          child: ClipOval(
            child: entry.userPhotoUrl != null
                ? Image.network(
                    AppConstants.getFullImageUrl(entry.userPhotoUrl)!,
                    fit: BoxFit.cover,
                    errorBuilder: (c, e, s) => _buildAvatarPlaceholder(entry),
                  )
                : _buildAvatarPlaceholder(entry),
          ),
        ),
        const SizedBox(height: AppSpacing.sm),

        // Name
        Text(
          entry.userName.split(' ').first, // First name only
          style: AppTextStyles.body.copyWith(
            fontWeight: FontWeight.w600,
            fontSize: rank == 1 ? 14 : 12,
          ),
          textAlign: TextAlign.center,
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
        ),

        // Count
        Text(
          '${entry.returnedCount} dikembalikan',
          style: AppTextStyles.caption.copyWith(
            color: AppColors.textSecondary,
            fontSize: 10,
          ),
          textAlign: TextAlign.center,
        ),
        const SizedBox(height: AppSpacing.sm),

        // Pedestal
        Container(
          height: height,
          decoration: BoxDecoration(
            color: colors[rank],
            borderRadius: const BorderRadius.vertical(
              top: Radius.circular(AppRadius.md),
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withAlpha(26),
                blurRadius: 4,
                offset: const Offset(0, -2),
              ),
            ],
          ),
          child: Center(
            child: Text(
              '$rank',
              style: AppTextStyles.h2.copyWith(
                color: Colors.white,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildAvatarPlaceholder(LeaderboardEntry entry) {
    return Container(
      color: AppColors.primary.withAlpha(51),
      child: Center(
        child: Text(
          entry.userName.isNotEmpty ? entry.userName[0].toUpperCase() : '?',
          style: AppTextStyles.h3.copyWith(color: AppColors.primary),
        ),
      ),
    );
  }

  Widget _buildRankingCard(LeaderboardEntry entry) {
    return Container(
      margin: const EdgeInsets.only(bottom: AppSpacing.sm),
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadius.md),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withAlpha(13),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        children: [
          // Rank badge
          Container(
            width: 36,
            height: 36,
            decoration: BoxDecoration(
              color: AppColors.surfaceAlt,
              borderRadius: BorderRadius.circular(AppRadius.sm),
            ),
            child: Center(
              child: Text(
                '${entry.rank}',
                style: AppTextStyles.body.copyWith(
                  fontWeight: FontWeight.bold,
                  color: AppColors.textSecondary,
                ),
              ),
            ),
          ),
          const SizedBox(width: AppSpacing.md),

          // Avatar
          Container(
            width: 44,
            height: 44,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              border: Border.all(color: AppColors.border),
            ),
            child: ClipOval(
              child: entry.userPhotoUrl != null
                  ? Image.network(
                      AppConstants.getFullImageUrl(entry.userPhotoUrl)!,
                      fit: BoxFit.cover,
                      errorBuilder: (c, e, s) => _buildAvatarPlaceholder(entry),
                    )
                  : _buildAvatarPlaceholder(entry),
            ),
          ),
          const SizedBox(width: AppSpacing.md),

          // Info
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  entry.userName,
                  style: AppTextStyles.body.copyWith(
                    fontWeight: FontWeight.w600,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                if (entry.userProdiUnit != null)
                  Text(
                    entry.userProdiUnit!,
                    style: AppTextStyles.caption.copyWith(
                      color: AppColors.textSecondary,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
              ],
            ),
          ),

          // Stats
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(Icons.check_circle, size: 16, color: AppColors.success),
                  const SizedBox(width: 4),
                  Text(
                    '${entry.returnedCount}',
                    style: AppTextStyles.body.copyWith(
                      fontWeight: FontWeight.bold,
                      color: AppColors.success,
                    ),
                  ),
                ],
              ),
              Text(
                'dikembalikan',
                style: AppTextStyles.caption.copyWith(
                  color: AppColors.textTertiary,
                  fontSize: 10,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}