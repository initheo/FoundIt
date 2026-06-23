import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:foundit_app/presentation/screens/auth/login_screen.dart';
import 'package:foundit_app/presentation/screens/main_navigation_screen.dart';
import 'package:foundit_app/data/services/secure_storage_service.dart';

import 'shared/utils/utils.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();

  // Set system UI overlay style
  SystemChrome.setSystemUIOverlayStyle(
    const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.dark,
      systemNavigationBarColor: AppColors.surface,
      systemNavigationBarIconBrightness: Brightness.dark,
    ),
  );

  // Set preferred orientations
  SystemChrome.setPreferredOrientations([
    DeviceOrientation.portraitUp,
    DeviceOrientation.portraitDown,
  ]);

  runApp(const FoundItApp());
}

class FoundItApp extends StatelessWidget {
  const FoundItApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: AppConstants.appName,
      debugShowCheckedModeBanner: false,
      theme: AppTheme.lightTheme,
      home: FutureBuilder<Map<String, dynamic>>(
        future: _checkLoginStatus(),
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Scaffold(
              body: Center(
                child: CircularProgressIndicator(),
              ),
            );
          }
          if (snapshot.hasData && snapshot.data!['isLoggedIn'] == true) {
            return MainNavigationScreen(currentUser: snapshot.data!['user']);
          }
          return const LoginScreen();
        },
      ),
    );
  }

  Future<Map<String, dynamic>> _checkLoginStatus() async {
    final storage = SecureStorageService();
    final isLoggedIn = await storage.isLoggedIn();
    if (isLoggedIn) {
      final user = await storage.getUser();
      if (user != null) {
        return {'isLoggedIn': true, 'user': user};
      }
    }
    return {'isLoggedIn': false};
  }
}

/// Placeholder home screen until actual screens are implemented
class PlaceholderHomeScreen extends StatelessWidget {
  const PlaceholderHomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Center(
          child: Padding(
            padding: const EdgeInsets.all(AppSpacing.lg),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                // App Icon Placeholder
                Container(
                  width: 120,
                  height: 120,
                  decoration: BoxDecoration(
                    color: AppColors.primary,
                    borderRadius: BorderRadius.circular(AppRadius.xl),
                  ),
                  child: const Icon(
                    Icons.search,
                    size: 64,
                    color: AppColors.white,
                  ),
                ),
                const SizedBox(height: AppSpacing.lg),

                // App Name
                Text(
                  AppConstants.appName,
                  style: AppTextStyles.h1.copyWith(color: AppColors.primary),
                ),
                const SizedBox(height: AppSpacing.sm),

                // Tagline
                Text(
                  AppConstants.appTagline,
                  style: AppTextStyles.bodySecondary,
                  textAlign: TextAlign.center,
                ),
                const SizedBox(height: AppSpacing.xl),

                // Setup Status
                Container(
                  padding: const EdgeInsets.all(AppSpacing.md),
                  decoration: BoxDecoration(
                    color: AppColors.success.withValues(alpha: 0.1),
                    borderRadius: BorderRadius.circular(AppRadius.md),
                    border: Border.all(
                      color: AppColors.success.withValues(alpha: 0.3),
                    ),
                  ),
                  child: Column(
                    children: [
                      const Icon(
                        Icons.check_circle,
                        color: AppColors.success,
                        size: 32,
                      ),
                      const SizedBox(height: AppSpacing.sm),
                      Text(
                        'Core Setup Complete',
                        style: AppTextStyles.h3.copyWith(
                          color: AppColors.success,
                        ),
                      ),
                      const SizedBox(height: AppSpacing.xs),
                      Text(
                        'Colors, Typography, Spacing, Theme, API Service ready',
                        style: AppTextStyles.caption,
                        textAlign: TextAlign.center,
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: AppSpacing.lg),

                // Version
                Text(
                  'v${AppConstants.appVersion}',
                  style: AppTextStyles.caption,
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
