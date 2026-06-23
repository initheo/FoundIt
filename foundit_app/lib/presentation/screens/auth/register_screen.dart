import 'package:flutter/material.dart';

import '../../../data/repository/auth_repository.dart';
import '../../../data/usecase/request/register_request.dart';
import '../../../shared/utils/utils.dart';
import '../../../shared/widget/widgets.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _authRepository = AuthRepository();

  // Controllers untuk setiap input field
  final _namaController = TextEditingController();
  final _emailController = TextEditingController();
  final _prodiController = TextEditingController();
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();

  // Toggle untuk show/hide password
  bool _obscurePassword = true;
  bool _obscureConfirmPassword = true;

  // Loading state
  bool _isLoading = false;

  @override
  void dispose() {
    _namaController.dispose();
    _emailController.dispose();
    _prodiController.dispose();
    _phoneController.dispose();
    _passwordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

  Future<void> _handleRegister() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isLoading = true);

    try {
      final registerRequest = RegisterRequest(
        name: _namaController.text.trim(),
        email: _emailController.text.trim(),
        password: _passwordController.text,
        passwordConfirmation: _confirmPasswordController.text,
        phone: _phoneController.text.trim(),
        prodiUnit: _prodiController.text.trim(),
      );

      await _authRepository.register(registerRequest);

      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Registrasi berhasil!'),
          backgroundColor: AppColors.success,
        ),
      );

      Navigator.pop(context);
    } catch (e) {
      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(e.toString().replaceAll('Exception: ', '')),
          backgroundColor: AppColors.error,
        ),
      );
    } finally {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.background,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text('Daftar Akun', style: AppTextStyles.h2),
        centerTitle: false,
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(AppSpacing.screenPadding),
          child: Form(
            key: _formKey,
            autovalidateMode: AutovalidateMode.onUserInteraction,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Nama Lengkap
                CustomTextField(
                  controller: _namaController,
                  label: 'Nama Lengkap',
                  hint: 'Masukkan nama lengkap',
                  icon: Icons.person_outline,
                  validator: Validators.name,
                ),
                const SizedBox(height: AppSpacing.md),

                // Email UISI
                CustomTextField(
                  controller: _emailController,
                  label: 'Email UISI',
                  hint: 'contoh@student.uisi.ac.id',
                  icon: Icons.email_outlined,
                  keyboardType: TextInputType.emailAddress,
                  validator: Validators.uisiEmail,
                  helperText:
                      'Gunakan email @student.uisi.ac.id atau @uisi.ac.id',
                ),
                const SizedBox(height: AppSpacing.md),

                // Prodi/Unit
                CustomTextField(
                  controller: _prodiController,
                  label: 'Prodi/Unit',
                  hint: 'Contoh: Informatika',
                  icon: Icons.school_outlined,
                  validator: (value) =>
                      Validators.required(value, 'Prodi/Unit'),
                ),
                const SizedBox(height: AppSpacing.md),

                // No. HP (Wajib untuk koordinasi)
                CustomTextField(
                  controller: _phoneController,
                  label: 'No. HP',
                  hint: '08xxxxxxxxxx',
                  icon: Icons.phone_outlined,
                  keyboardType: TextInputType.phone,
                  validator: (value) {
                    if (value == null || value.trim().isEmpty) {
                      return 'No. HP wajib diisi untuk koordinasi pengembalian';
                    }
                    return Validators.phone(value);
                  },
                  helperText: 'Digunakan saat koordinasi pengembalian barang',
                ),
                const SizedBox(height: AppSpacing.md),

                // Password
                CustomTextField(
                  controller: _passwordController,
                  label: 'Password',
                  hint: 'Minimal 8 karakter',
                  icon: Icons.lock_outline,
                  obscureText: _obscurePassword,
                  validator: Validators.password,
                  suffixIcon: IconButton(
                    icon: Icon(
                      _obscurePassword
                          ? Icons.visibility_off
                          : Icons.visibility,
                      color: AppColors.textSecondary,
                    ),
                    onPressed: () {
                      setState(() => _obscurePassword = !_obscurePassword);
                    },
                  ),
                ),
                const SizedBox(height: AppSpacing.md),

                // Konfirmasi Password
                CustomTextField(
                  controller: _confirmPasswordController,
                  label: 'Konfirmasi Password',
                  hint: 'Ulangi password',
                  icon: Icons.lock_outline,
                  obscureText: _obscureConfirmPassword,
                  validator: (value) => Validators.confirmPassword(
                    value,
                    _passwordController.text,
                  ),
                  suffixIcon: IconButton(
                    icon: Icon(
                      _obscureConfirmPassword
                          ? Icons.visibility_off
                          : Icons.visibility,
                      color: AppColors.textSecondary,
                    ),
                    onPressed: () {
                      setState(
                        () =>
                            _obscureConfirmPassword = !_obscureConfirmPassword,
                      );
                    },
                  ),
                ),
                const SizedBox(height: AppSpacing.lg),

                // Tombol Daftar
                PrimaryButton(
                  text: 'DAFTAR',
                  isLoading: _isLoading,
                  onPressed: _handleRegister,
                ),
                const SizedBox(height: AppSpacing.lg),

                // Link ke Login
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text('Sudah punya akun? ', style: AppTextStyles.body),
                    GestureDetector(
                      onTap: () => Navigator.pop(context),
                      child: Text(
                        'Masuk',
                        style: AppTextStyles.body.copyWith(
                          color: AppColors.primary,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
