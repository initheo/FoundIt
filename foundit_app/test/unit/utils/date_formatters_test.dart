import 'package:flutter_test/flutter_test.dart';
import 'package:foundit_app/shared/utils/date_formatters.dart';

/// Unit Test Suite: DateFormattersTest
/// Skenario Utama: Validasi logika format tanggal
void main() {
  group('DateFormatters', () {
    group('formatTimeAgo', () {
      /// TC-FD01: Baru saja (kurang dari 1 menit)
      test('returns Baru saja for less than 1 minute', () {
        final now = DateTime.now();
        expect(DateFormatters.formatTimeAgo(now), 'Baru saja');
      });

      /// TC-FD02: X menit lalu
      test('returns minutes ago format', () {
        final date = DateTime.now().subtract(const Duration(minutes: 5));
        expect(DateFormatters.formatTimeAgo(date), '5 menit lalu');
      });

      /// TC-FD03: X jam lalu
      test('returns hours ago format', () {
        final date = DateTime.now().subtract(const Duration(hours: 3));
        expect(DateFormatters.formatTimeAgo(date), '3 jam lalu');
      });

      /// TC-FD04: Kemarin
      test('returns Kemarin for 1 day ago', () {
        final date = DateTime.now().subtract(const Duration(days: 1));
        expect(DateFormatters.formatTimeAgo(date), 'Kemarin');
      });

      /// TC-FD05: X hari lalu (2-6 hari)
      test('returns days ago for 2-6 days', () {
        final date = DateTime.now().subtract(const Duration(days: 4));
        expect(DateFormatters.formatTimeAgo(date), '4 hari lalu');
      });
    });

    group('formatShortDate', () {
      /// TC-FD06: Format default (13 Jan 2026)
      test('returns short date with month name', () {
        final date = DateTime(2026, 1, 13);
        expect(DateFormatters.formatShortDate(date), '13 Jan 2026');
      });

      /// TC-FD07: Format slash (13/1/2026)
      test('returns slash format when useSlash is true', () {
        final date = DateTime(2026, 1, 13);
        final result = DateFormatters.formatShortDate(date, useSlash: true);
        expect(result, '13/1/2026');
      });
    });
  });
}
