import 'dart:io';

import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';

import '../../../data/model/category_model.dart';
import '../../../data/repository/category_repository.dart';
import '../../../data/repository/item_repository.dart';
import '../../../data/usecase/request/create_item_request.dart';
import '../../../shared/utils/utils.dart';
import '../../../shared/widget/widgets.dart';
import '../location/location_picker_screen.dart';

class ReportFoundScreen extends StatefulWidget {
  const ReportFoundScreen({super.key});

  @override
  State<ReportFoundScreen> createState() => _ReportFoundScreenState();
}

class _ReportFoundScreenState extends State<ReportFoundScreen> {
  final _formKey = GlobalKey<FormState>();
  final CategoryRepository _categoryRepository = CategoryRepository();
  final ItemRepository _itemRepository = ItemRepository();

  // Controllers
  final _titleController = TextEditingController();
  final _descriptionController = TextEditingController();
  final _locationController = TextEditingController();
  final _locationDetailController = TextEditingController();
  final _storageInfoController = TextEditingController();

  // State
  List<CategoryModel> _categories = [];
  CategoryModel? _selectedCategory;
  DateTime _selectedDate = DateTime.now();
  TimeOfDay _selectedTime = TimeOfDay.now();
  final List<File> _selectedImages = [];
  bool _isLoading = false;
  bool _isCategoriesLoading = true;
  SelectedLocation? _selectedLocation;

  final ImagePicker _imagePicker = ImagePicker();

  @override
  void initState() {
    super.initState();
    _loadCategories();
  }

  @override
  void dispose() {
    _titleController.dispose();
    _descriptionController.dispose();
    _locationController.dispose();
    _locationDetailController.dispose();
    _storageInfoController.dispose();
    super.dispose();
  }

  Future<void> _loadCategories() async {
    try {
      final categories = await _categoryRepository.getCategories();
      setState(() {
        _categories = categories;
        _isCategoriesLoading = false;
      });
    } catch (e) {
      setState(() {
        _isCategoriesLoading = false;
      });
    }
  }

  Future<void> _pickDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _selectedDate,
      firstDate: DateTime(2020),
      lastDate: DateTime.now(),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(primary: AppColors.foundBadge),
          ),
          child: child!,
        );
      },
    );
    if (picked != null) {
      setState(() => _selectedDate = picked);
    }
  }

  Future<void> _pickTime() async {
    final picked = await showTimePicker(
      context: context,
      initialTime: _selectedTime,
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(primary: AppColors.foundBadge),
          ),
          child: child!,
        );
      },
    );
    if (picked != null) {
      setState(() => _selectedTime = picked);
    }
  }

  Future<void> _pickImage(ImageSource source) async {
    if (_selectedImages.length >= 3) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Maksimal 3 foto'),
          backgroundColor: AppColors.warning,
        ),
      );
      return;
    }

    try {
      final XFile? image = await _imagePicker.pickImage(
        source: source,
      );

      if (image != null) {
        final file = File(image.path);
        final sizeInBytes = await file.length();
        if (sizeInBytes > 2 * 1024 * 1024) {
          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Ukuran foto melebihi 2MB'),
              backgroundColor: AppColors.error,
            ),
          );
          return;
        }
        setState(() {
          _selectedImages.add(file);
        });
      }
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Gagal mengambil gambar: $e'),
          backgroundColor: AppColors.error,
        ),
      );
    }
  }

  Future<void> _pickImageFromSource() async {
    final source = await showImageSourcePicker(context);
    if (source != null) {
      _pickImage(source);
    }
  }

  void _removeImage(int index) {
    setState(() {
      _selectedImages.removeAt(index);
    });
  }

  Future<void> _handleSubmit() async {
    if (!_formKey.currentState!.validate()) return;

    if (_selectedCategory == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Pilih kategori barang'),
          backgroundColor: AppColors.error,
        ),
      );
      return;
    }

    if (_selectedImages.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Tambahkan minimal 1 foto barang'),
          backgroundColor: AppColors.error,
        ),
      );
      return;
    }

    setState(() => _isLoading = true);

    try {
      // Combine date and time
      final dateTime = DateTime(
        _selectedDate.year,
        _selectedDate.month,
        _selectedDate.day,
        _selectedTime.hour,
        _selectedTime.minute,
      );

      final request = CreateItemRequest(
        type: 'found',
        categoryId: _selectedCategory!.id,
        title: _titleController.text.trim(),
        description: _descriptionController.text.trim(),
        location: _locationController.text.trim(),
        locationDetail: _locationDetailController.text.trim(),
        latitude: _selectedLocation?.latitude,
        longitude: _selectedLocation?.longitude,
        dateTime: dateTime,
        storageInfo: _storageInfoController.text.trim(),
        photos: _selectedImages,
      );

      await _itemRepository.createItem(request);

      if (!mounted) return;
      setState(() => _isLoading = false);

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Laporan berhasil dikirim!'),
          backgroundColor: AppColors.success,
        ),
      );

      Navigator.pop(context, true);
    } catch (e) {
      if (!mounted) return;
      setState(() => _isLoading = false);

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Gagal mengirim laporan: $e'),
          backgroundColor: AppColors.error,
        ),
      );
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
        title: const Text(
          'Lapor Barang Temuan',
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
                // Type Badge
                _buildTypeBadge(),
                const SizedBox(height: AppSpacing.lg),

                // Title Field
                CustomTextField(
                  controller: _titleController,
                  label: 'Judul Barang',
                  hint: 'Contoh: iPhone 15 Pro Max Hitam',
                  icon: Icons.title,
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Judul barang wajib diisi';
                    }
                    if (value.trim().length < 5) {
                      return 'Judul barang minimal 5 karakter';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: AppSpacing.lg),

                // Category Dropdown
                _buildSectionTitle('Kategori'),
                const SizedBox(height: AppSpacing.sm),
                _buildCategoryDropdown(),
                const SizedBox(height: AppSpacing.lg),

                // Description Field
                _buildDescriptionField(),
                const SizedBox(height: AppSpacing.lg),

                // Location Field
                _buildLocationPicker(),
                const SizedBox(height: AppSpacing.lg),

                // Storage Info Field
                CustomTextField(
                  controller: _storageInfoController,
                  label: 'Lokasi Penyimpanan',
                  hint: 'Contoh: Satpam Gedung A',
                  icon: Icons.inventory_2,
                  helperText: 'Di mana barang ini disimpan saat ini?',
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Lokasi penyimpanan wajib diisi';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: AppSpacing.lg),

                // Date & Time Pickers
                _buildSectionTitle('Tanggal & Waktu Ditemukan'),
                const SizedBox(height: AppSpacing.sm),
                _buildDateTimePickers(),
                const SizedBox(height: AppSpacing.lg),

                // Photo Upload
                _buildSectionTitle('Foto Barang (min 1, maks 3)'),
                const SizedBox(height: AppSpacing.sm),
                _buildPhotoUpload(),
                const SizedBox(height: AppSpacing.xl),

                // Submit Button
                PrimaryButton(
                  text: 'Kirim Laporan',
                  onPressed: _handleSubmit,
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

  Widget _buildTypeBadge() {
    return Container(
      padding: const EdgeInsets.symmetric(
        horizontal: AppSpacing.md,
        vertical: AppSpacing.sm,
      ),
      decoration: BoxDecoration(
        color: AppColors.foundBadge.withAlpha(25),
        borderRadius: BorderRadius.circular(AppRadius.md),
        border: Border.all(color: AppColors.foundBadge.withAlpha(76)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          const Icon(Icons.search, color: AppColors.foundBadge, size: 20),
          const SizedBox(width: AppSpacing.sm),
          Text(
            'Barang Temuan',
            style: AppTextStyles.bodySmall.copyWith(
              color: AppColors.foundBadge,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(
      title,
      style: AppTextStyles.bodySmall.copyWith(
        color: AppColors.textSecondary,
        fontWeight: FontWeight.w500,
      ),
    );
  }

  Widget _buildDescriptionField() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text('Deskripsi', style: AppTextStyles.inputLabel),
        const SizedBox(height: AppSpacing.sm),
        TextFormField(
          controller: _descriptionController,
          maxLines: 4,
          style: AppTextStyles.input,
          decoration: InputDecoration(
            hintText: 'Jelaskan ciri-ciri barang secara detail...',
            prefixIcon: const Icon(
              Icons.description,
              color: AppColors.textSecondary,
            ),
          ),
          validator: (value) {
            if (value == null || value.isEmpty) {
              return 'Deskripsi wajib diisi';
            }
            return null;
          },
        ),
      ],
    );
  }

  Widget _buildCategoryDropdown() {
    if (_isCategoriesLoading) {
      return Container(
        padding: const EdgeInsets.all(AppSpacing.md),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(AppRadius.md),
          border: Border.all(color: AppColors.border),
        ),
        child: const Row(
          children: [
            SizedBox(
              width: 20,
              height: 20,
              child: CircularProgressIndicator(strokeWidth: 2),
            ),
            SizedBox(width: AppSpacing.sm),
            Text('Memuat kategori...'),
          ],
        ),
      );
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadius.md),
        border: Border.all(color: AppColors.border),
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<CategoryModel>(
          value: _selectedCategory,
          isExpanded: true,
          hint: const Text('Pilih Kategori'),
          icon: const Icon(Icons.keyboard_arrow_down),
          items: _categories.map((category) {
            return DropdownMenuItem<CategoryModel>(
              value: category,
              child: Row(
                children: [
                  Icon(
                    _getCategoryIcon(category.name),
                    color: AppColors.primary,
                    size: 20,
                  ),
                  const SizedBox(width: AppSpacing.sm),
                  Text(category.name),
                ],
              ),
            );
          }).toList(),
          onChanged: (value) {
            setState(() {
              _selectedCategory = value;
            });
          },
        ),
      ),
    );
  }

  Widget _buildLocationPicker() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text('Lokasi Ditemukan', style: AppTextStyles.inputLabel),
        const SizedBox(height: AppSpacing.sm),
        GestureDetector(
          onTap: () async {
            final result = await Navigator.push<SelectedLocation>(
              context,
              MaterialPageRoute(
                builder: (context) => const LocationPickerScreen(),
              ),
            );
            if (result != null && mounted) {
              setState(() {
                _selectedLocation = result;
                _locationController.text = result.address;
              });
            }
          },
          child: Container(
            padding: const EdgeInsets.all(AppSpacing.md),
            decoration: BoxDecoration(
              color: AppColors.surface,
              borderRadius: BorderRadius.circular(AppRadius.md),
              border: Border.all(
                color: _locationController.text.isEmpty
                    ? AppColors.border
                    : AppColors.foundBadge.withAlpha(128),
              ),
            ),
            child: Row(
              children: [
                Icon(
                  Icons.location_on,
                  color: _locationController.text.isEmpty
                      ? AppColors.textSecondary
                      : AppColors.foundBadge,
                ),
                const SizedBox(width: AppSpacing.md),
                Expanded(
                  child: Text(
                    _locationController.text.isEmpty
                        ? 'Ketuk untuk pilih lokasi di peta'
                        : _locationController.text,
                    style: AppTextStyles.input.copyWith(
                      color: _locationController.text.isEmpty
                          ? AppColors.textSecondary
                          : AppColors.textPrimary,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
                const Icon(Icons.chevron_right, color: AppColors.textSecondary),
              ],
            ),
          ),
        ),
        // Location detail text field
        const SizedBox(height: AppSpacing.md),
        Text('Detail Lokasi (Opsional)', style: AppTextStyles.inputLabel),
        const SizedBox(height: AppSpacing.sm),
        TextFormField(
          controller: _locationDetailController,
          style: AppTextStyles.input,
          decoration: InputDecoration(
            hintText: 'Contoh: Ruang CM201, Lantai 2',
            prefixIcon: const Icon(
              Icons.edit_location_alt_outlined,
              color: AppColors.textSecondary,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildDateTimePickers() {
    return Row(
      children: [
        Expanded(
          child: GestureDetector(
            onTap: _pickDate,
            child: Container(
              padding: const EdgeInsets.all(AppSpacing.md),
              decoration: BoxDecoration(
                color: AppColors.surface,
                borderRadius: BorderRadius.circular(AppRadius.md),
                border: Border.all(color: AppColors.border),
              ),
              child: Row(
                children: [
                  const Icon(
                    Icons.calendar_today,
                    size: 20,
                    color: AppColors.textSecondary,
                  ),
                  const SizedBox(width: AppSpacing.sm),
                  Text(
                    DateFormat('dd MMM yyyy').format(_selectedDate),
                    style: AppTextStyles.body,
                  ),
                ],
              ),
            ),
          ),
        ),
        const SizedBox(width: AppSpacing.sm),
        Expanded(
          child: GestureDetector(
            onTap: _pickTime,
            child: Container(
              padding: const EdgeInsets.all(AppSpacing.md),
              decoration: BoxDecoration(
                color: AppColors.surface,
                borderRadius: BorderRadius.circular(AppRadius.md),
                border: Border.all(color: AppColors.border),
              ),
              child: Row(
                children: [
                  const Icon(
                    Icons.access_time,
                    size: 20,
                    color: AppColors.textSecondary,
                  ),
                  const SizedBox(width: AppSpacing.sm),
                  Text(
                    _selectedTime.format(context),
                    style: AppTextStyles.body,
                  ),
                ],
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildPhotoUpload() {
    return SizedBox(
      height: 100,
      child: ListView(
        scrollDirection: Axis.horizontal,
        children: [
          // Add Photo Button
          if (_selectedImages.length < 3)
            GestureDetector(
              onTap: _pickImageFromSource,
              child: Container(
                width: 100,
                height: 100,
                margin: const EdgeInsets.only(right: AppSpacing.sm),
                decoration: BoxDecoration(
                  color: AppColors.surface,
                  borderRadius: BorderRadius.circular(AppRadius.md),
                  border: Border.all(color: AppColors.border),
                ),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(
                      Icons.add_a_photo,
                      size: 32,
                      color: AppColors.textSecondary,
                    ),
                    const SizedBox(height: AppSpacing.xs),
                    Text(
                      'Tambah',
                      style: AppTextStyles.caption.copyWith(
                        color: AppColors.textSecondary,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          // Selected Images
          ..._selectedImages.asMap().entries.map((entry) {
            final index = entry.key;
            final image = entry.value;
            return Stack(
              children: [
                Container(
                  width: 100,
                  height: 100,
                  margin: const EdgeInsets.only(right: AppSpacing.sm),
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(AppRadius.md),
                    image: DecorationImage(
                      image: FileImage(image),
                      fit: BoxFit.cover,
                    ),
                  ),
                ),
                Positioned(
                  top: 4,
                  right: AppSpacing.sm + 4,
                  child: GestureDetector(
                    onTap: () => _removeImage(index),
                    child: Container(
                      padding: const EdgeInsets.all(4),
                      decoration: const BoxDecoration(
                        color: AppColors.error,
                        shape: BoxShape.circle,
                      ),
                      child: const Icon(
                        Icons.close,
                        size: 16,
                        color: Colors.white,
                      ),
                    ),
                  ),
                ),
              ],
            );
          }),
        ],
      ),
    );
  }

  IconData _getCategoryIcon(String categoryName) {
    switch (categoryName.toLowerCase()) {
      case 'elektronik':
        return Icons.devices;
      case 'dokumen':
        return Icons.description;
      case 'aksesoris':
        return Icons.watch;
      case 'pakaian':
        return Icons.checkroom;
      case 'tas':
        return Icons.backpack;
      default:
        return Icons.category;
    }
  }
}
