/// Utility class for date formatting functions used across the app
class DateFormatters {
  /// Format date as relative time (e.g., "5 menit lalu", "Kemarin")
  static String formatTimeAgo(DateTime dateTime) {
    final now = DateTime.now();
    final difference = now.difference(dateTime);

    // Handle future dates or very small differences
    if (difference.isNegative || difference.inMinutes < 1) {
      return 'Baru saja';
    }

    if (difference.inDays == 0) {
      if (difference.inHours == 0) {
        return '${difference.inMinutes} menit lalu';
      }
      return '${difference.inHours} jam lalu';
    } else if (difference.inDays == 1) {
      return 'Kemarin';
    } else if (difference.inDays < 7) {
      return '${difference.inDays} hari lalu';
    }

    // Show full date for older dates
    return formatShortDate(dateTime);
  }

  /// Format date as short date (e.g., "13 Jan 2026" or "13/1/2026")
  static String formatShortDate(DateTime dateTime, {bool useSlash = false}) {
    if (useSlash) {
      return '${dateTime.day}/${dateTime.month}/${dateTime.year}';
    }

    const months = [
      'Jan',
      'Feb',
      'Mar',
      'Apr',
      'Mei',
      'Jun',
      'Jul',
      'Agu',
      'Sep',
      'Okt',
      'Nov',
      'Des',
    ];
    return '${dateTime.day} ${months[dateTime.month - 1]} ${dateTime.year}';
  }
}
