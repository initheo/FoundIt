import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../../../data/model/user_model.dart';
import '../../../data/repository/profile_repository.dart';
import '../../../shared/utils/utils.dart';
import '../../../shared/widget/widgets.dart';

class EditProfileScreen extends StatefulWidget {
  final UserModel currentUser;

  const EditProfileScreen({super.key, required this.currentUser});

  @override
  State<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends State<EditProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  final ProfileRepository _profileRepository = ProfileRepository();

  late TextEditingController _nameController;
  late TextEditingController _emailController;
  late TextEditingController _phoneController;
  late TextEditingController _prodiUnitController;

  bool _isLoading = false;
  bool _isUploadingPhoto = false;
  File? _selectedPhoto;
  String? _currentPhotoUrl;

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.currentUser.name);
    _emailController = TextEditingController(text: widget.currentUser.email);
    _phoneController = TextEditingController(
      text: widget.currentUser.phone ?? '',
    );
    _prodiUnitController = TextEditingController(
      text: widget.currentUser.prodiUnit ?? '',
    );
    _currentPhotoUrl = widget.currentUser.photoUrl;
  }

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _prodiUnitController.dispose();
    super.dispose();
  }

  Future<void> _pickImage() async {
    final source = await showImageSourcePicker(context);
    if (source == null) return;

    final picker = ImagePicker();
    _pickImageFromSource(picker, source);
  }

  Future<void> _pickImageFromSource(
    ImagePicker picker,
    ImageSource source,
  ) async {
    final pickedFile = await picker.pickImage(
      source: source,
      maxWidth: 800,
      maxHeight: 800,
      imageQuality: 80,
    );

    if (pickedFile != null) {
      setState(() => _selectedPhoto = File(pickedFile.path));
      await _uploadPhoto();
    }
  }

  Future<void> _uploadPhoto() async {
    if (_selectedPhoto == null) return;

    setState(() => _isUploadingPhoto = true);

    try {
      final photoUrl = await _profileRepository.uploadPhoto(_selectedPhoto!);
      setState(() {
        _currentPhotoUrl = photoUrl;
        _isUploadingPhoto = false;
      });
      _showSuccess('Foto profil berhasil diupload');
    } catch (e) {
      setState(() => _isUploadingPhoto = false);
      _showError(e.toString().replaceFirst('Exception: ', ''));
    }
  }

  Future<void> _deletePhoto() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus Foto'),
        content: const Text('Apakah kamu yakin ingin menghapus foto profil?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          FilledButton(
            onPressed: () => Navigator.pop(context, true),
            style: FilledButton.styleFrom(backgroundColor: AppColors.error),
            child: const Text('Hapus'),
          ),
        ],
      ),
    );

    if (confirmed != true) return;

    setState(() => _isUploadingPhoto = true);

    try {
      await _profileRepository.deletePhoto();
      setState(() {
        _currentPhotoUrl = null;
        _selectedPhoto = null;
        _isUploadingPhoto = false;
      });
      _showSuccess('Foto profil berhasil dihapus');
    } catch (e) {
      setState(() => _isUploadingPhoto = false);
      _showError(e.toString().replaceFirst('Exception: ', ''));
    }
  }

  Future<void> _saveProfile() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isLoading = true);

    try {
      final updatedUser = await _profileRepository.updateProfile(
        name: _nameController.text.trim(),
        email: _emailController.text.trim(),
        phone: _phoneController.text.trim(),
        prodiUnit: _prodiUnitController.text.trim().isEmpty
            ? null
            : _prodiUnitController.text.trim(),
      );

      setState(() => _isLoading = false);
      _showSuccess('Profil berhasil diupdate');

      if (mounted) {
        Navigator.pop(context, updatedUser);
      }
    } catch (e) {
      setState(() => _isLoading = false);
      _showError(e.toString().replaceFirst('Exception: ', ''));
    }
  }

  void _showSuccess(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: AppColors.success,
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  void _showError(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: AppColors.error,
        behavior: SnackBarBehavior.floating,
      ),
    );
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
        title: const Text(
          'Edit Profil',
          style: TextStyle(
            color: AppColors.textPrimary,
            fontSize: 18,
            fontWeight: FontWeight.w600,
          ),
        ),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.md),
          child: Form(
            key: _formKey,
            autovalidateMode: AutovalidateMode.onUserInteraction,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Photo Section
                _buildPhotoSection(),
                const SizedBox(height: AppSpacing.lg),

                // Name Field
                CustomTextField(
                  controller: _nameController,
                  label: 'Nama Lengkap',
                  hint: 'Masukkan nama lengkap',
                  icon: Icons.person_outline,
                  validator: (value) {
                    if (value == null || value.trim().isEmpty) {
                      return 'Nama tidak boleh kosong';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: AppSpacing.lg),

                // Email Field
                CustomTextField(
                  controller: _emailController,
                  label: 'Email',
                  hint: 'Masukkan email UISI',
                  icon: Icons.email_outlined,
                  keyboardType: TextInputType.emailAddress,
                  validator: (value) {
                    if (value == null || value.trim().isEmpty) {
                      return 'Email tidak boleh kosong';
                    }
                    if (!value.contains('@')) {
                      return 'Email tidak valid';
                    }
                    if (!value.endsWith('@student.uisi.ac.id') &&
                        !value.endsWith('@uisi.ac.id')) {
                      return 'Gunakan email UISI';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: AppSpacing.lg),

                // Phone Field
                CustomTextField(
                  controller: _phoneController,
                  label: 'Nomor Telepon',
                  hint: 'Contoh: 081234567890',
                  icon: Icons.phone_outlined,
                  keyboardType: TextInputType.phone,
                  validator: (value) {
                    if (value == null || value.trim().isEmpty) {
                      return 'Nomor telepon wajib diisi';
                    }
                    if (value.trim().length < 10) {
                      return 'Nomor telepon minimal 10 digit';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: AppSpacing.lg),

                // Prodi/Unit Field
                CustomTextField(
                  controller: _prodiUnitController,
                  label: 'Prodi / Unit (opsional)',
                  hint: 'Contoh: Teknik Informatika',
                  icon: Icons.school_outlined,
                ),
                const SizedBox(height: AppSpacing.xl),

                // Submit Button
                PrimaryButton(
                  text: 'Simpan Perubahan',
                  onPressed: _saveProfile,
                  isLoading: _isLoading,
                ),
                const SizedBox(height: AppSpacing.lg),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildPhotoSection() {
    return Center(
      child: Column(
        children: [
          Stack(
            children: [
              // Avatar
              Container(
                width: 100,
                height: 100,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: AppColors.surface,
                  border: Border.all(color: AppColors.border),
                ),
                child: ClipOval(
                  child: _isUploadingPhoto
                      ? Container(
                          color: AppColors.surface,
                          child: const Center(
                            child: CircularProgressIndicator(strokeWidth: 2),
                          ),
                        )
                      : _selectedPhoto != null
                      ? Image.file(_selectedPhoto!, fit: BoxFit.cover)
                      : _currentPhotoUrl != null
                      ? Image.network(
                          AppConstants.getFullImageUrl(_currentPhotoUrl)!,
                          fit: BoxFit.cover,
                          errorBuilder: (context, error, stackTrace) =>
                              _buildAvatarPlaceholder(),
                        )
                      : _buildAvatarPlaceholder(),
                ),
              ),
              // Camera button
              Positioned(
                bottom: 0,
                right: 0,
                child: GestureDetector(
                  onTap: _pickImage,
                  child: Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: AppColors.primary,
                      shape: BoxShape.circle,
                      border: Border.all(color: Colors.white, width: 2),
                    ),
                    child: const Icon(
                      Icons.camera_alt,
                      color: Colors.white,
                      size: 16,
                    ),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: AppSpacing.sm),
          Text(
            'Tap ikon kamera untuk mengubah foto',
            style: AppTextStyles.caption.copyWith(
              color: AppColors.textSecondary,
            ),
          ),
          if (_currentPhotoUrl != null) ...[
            const SizedBox(height: AppSpacing.xs),
            TextButton.icon(
              onPressed: _isUploadingPhoto ? null : _deletePhoto,
              icon: const Icon(Icons.delete_outline, size: 18),
              label: const Text('Hapus Foto'),
              style: TextButton.styleFrom(foregroundColor: AppColors.error),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildAvatarPlaceholder() {
    return Container(
      color: AppColors.primary.withAlpha(25),
      child: Center(
        child: Text(
          widget.currentUser.name.isNotEmpty
              ? widget.currentUser.name[0].toUpperCase()
              : 'U',
          style: AppTextStyles.h1.copyWith(
            color: AppColors.primary,
            fontSize: 40,
          ),
        ),
      ),
    );
  }
}
