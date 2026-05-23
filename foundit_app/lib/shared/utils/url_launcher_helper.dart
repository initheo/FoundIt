import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

/// Helper class untuk meluncurkan URL eksternal (WhatsApp, telepon)
class UrlLauncherHelper {
  /// Buka WhatsApp dengan nomor telepon dan pesan opsional
  static Future<void> launchWhatsApp({
    required BuildContext context,
    required String phone,
    String? message,
  }) async {
    // Format phone: remove leading 0 and add 62 for Indonesia
    String formattedPhone = phone;
    if (phone.startsWith('0')) {
      formattedPhone = '62${phone.substring(1)}';
    } else if (!phone.startsWith('62')) {
      formattedPhone = '62$phone';
    }

    final encodedMessage = message != null ? Uri.encodeComponent(message) : '';
    final uri = Uri.parse(
      'https://wa.me/$formattedPhone${encodedMessage.isNotEmpty ? '?text=$encodedMessage' : ''}',
    );

    try {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    } catch (e) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('WhatsApp tidak tersedia di perangkat ini'),
          ),
        );
      }
    }
  }

  /// Buka aplikasi telepon dengan nomor
  static Future<void> launchPhone({
    required BuildContext context,
    required String phone,
  }) async {
    final uri = Uri.parse('tel:$phone');

    try {
      await launchUrl(uri);
    } catch (e) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Telepon tidak tersedia di perangkat ini'),
          ),
        );
      }
    }
  }
}
