import 'dart:io';

import 'package:flutter/material.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';

import '../../../data/model/category_model.dart';
import '../../../data/model/item_model.dart';
import '../../../data/repository/category_repository.dart';
import '../../../data/repository/item_repository.dart';
import '../../../shared/utils/utils.dart';
import '../../../shared/widget/widgets.dart';
import '../location/location_picker_screen.dart';

/// Screen untuk mengedit item yang sudah dilaporkan
class EditItemScreen extends StatefulWidget {
  final ItemModel item;

  const EditItemScreen({super.key, required this.item});

  @override
  State<EditItemScreen> createState() => _EditItemScreenState();
}

class _EditItemScreenState extends State<EditItemScreen> {
  final _formKey = GlobalKey<FormState>();
  final CategoryRepository _categoryRepository = CategoryRepository();
  final ItemRepository _itemRepository = ItemRepository();

  // Controllers
  late TextEditingController _titleController;
  late TextEditingController _descriptionController;
  late TextEditingController _locationController;
  late TextEditingController _locationDetailController;
  late TextEditingController _storageInfoController;

  // State
  List<CategoryModel> _categories = [];
  CategoryModel? _selectedCategory;
  late DateTime _selectedDate;
  late TimeOfDay _selectedTime;
  bool _isLoading = false;
  bool _isCategoriesLoading = true;
  SelectedLocation? _selectedLocation;

  // Photo state
  List<Map<String, dynamic>> _existingPhotos = [];
  bool _isUploadingPhoto = false;

  @override
  void initState() {
    super.initState();
    _titleController = TextEditingController(text: widget.item.title);
    _descriptionController = TextEditingController(
      text: widget.item.description,
    );
    _locationController = TextEditingController(text: widget.item.location);
    _locationDetailController = TextEditingController(
      text: widget.item.locationDetail ?? '',
    );
    _storageInfoController = TextEditingController(
      text: widget.item.storageInfo ?? '',
    );
    _selectedDate = widget.item.dateTime;
    _selectedTime = TimeOfDay.fromDateTime(widget.item.dateTime);

    // Initialize selected location from existing item data
    if (widget.item.latitude != null && widget.item.longitude != null) {
      _selectedLocation = SelectedLocation(
        latitude: widget.item.latitude!,
        longitude: widget.item.longitude!,
        address: widget.item.location,
      );
    }

    _loadCategories();
    _loadExistingPhotos();
  }

  void _loadExistingPhotos() {
    // Load existing photos from item.photoObjects (contains id and url)
    final photoObjects = widget.item.photoObjects;
    _existingPhotos = [];

    if (photoObjects.isNotEmpty) {
      // Use photoObjects if available (from myItems API with IDs)
      for (var photo in photoObjects) {
        _existingPhotos.add({'url': photo['url'], 'id': photo['id']});
      }
    } else {
      // Fallback to photoUrls (no IDs available)
      final photoUrls = widget.item.photoUrls;
      for (var url in photoUrls) {
        _existingPhotos.add({'url': url, 'id': null});
      }
    }
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
        _selectedCategory = categories.firstWhere(
          (c) => c.id == widget.item.categoryId,
          orElse: () => categories.first,
        );
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
            colorScheme: const ColorScheme.light(primary: AppColors.primary),
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
            colorScheme: const ColorScheme.light(primary: AppColors.primary),
          ),
          child: child!,
        );
      },
    );
    if (picked != null) {
      setState(() => _selectedTime = picked);
    }
  }

  Future<void> _pickAndUploadPhoto() async {
    if (_existingPhotos.length >= 3) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Maksimal 3 foto per item'),
          backgroundColor: AppColors.warning,
        ),
      );
      return;
    }

    final source = await showImageSourcePicker(context);
    if (source != null) {
      _pickImage(source);
    }
  }

  Future<void> _pickImage(ImageSource source) async {
    final picker = ImagePicker();
    final pickedFile = await picker.pickImage(
      source: source,
      maxWidth: 1024,
      maxHeight: 1024,
      imageQuality: 80,
    );

    if (pickedFile == null) return;

    setState(() => _isUploadingPhoto = true);

    try {
      final result = await _itemRepository.addItemPhoto(
        widget.item.id,
        File(pickedFile.path),
      );

      setState(() {
        _existingPhotos.add({'url': result['photo_url'], 'id': result['id']});
        _isUploadingPhoto = false;
      });

      _showSuccess('Foto berhasil ditambahkan');
    } catch (e) {
      setState(() => _isUploadingPhoto = false);
      _showError(e.toString().replaceFirst('Exception: ', ''));
    }
  }

  Future<void> _deletePhoto(int index) async {
    final photo = _existingPhotos[index];
    final photoId = photo['id'];

    if (photoId == null) {
      // Photo ID not available, just remove from UI
      setState(() => _existingPhotos.removeAt(index));
      return;
    }

    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus Foto'),
        content: const Text('Apakah kamu yakin ingin menghapus foto ini?'),
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

    try {
      await _itemRepository.deleteItemPhoto(widget.item.id, photoId);
      setState(() => _existingPhotos.removeAt(index));
      _showSuccess('Foto berhasil dihapus');
    } catch (e) {
      _showError(e.toString().replaceFirst('Exception: ', ''));
    }
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

      final data = <String, dynamic>{
        'category_id': _selectedCategory!.id,
        'title': _titleController.text.trim(),
        'description': _descriptionController.text.trim(),
        'location': _locationController.text.trim(),
        'location_detail': _locationDetailController.text.trim(),
        'latitude': _selectedLocation?.latitude,
        'longitude': _selectedLocation?.longitude,
        'date_time': dateTime.toIso8601String(),
      };

      // Only include storage_info for found items
      if (widget.item.isFound && _storageInfoController.text.isNotEmpty) {
        data['storage_info'] = _storageInfoController.text.trim();
      }

      final updatedItem = await _itemRepository.updateItem(
        widget.item.id,
        data,
      );

      if (!mounted) return;
      setState(() => _isLoading = false);

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Barang berhasil diupdate!'),
          backgroundColor: AppColors.success,
        ),
      );

      Navigator.pop(context, updatedItem); // Return updated item
    } catch (e) {
      if (!mounted) return;
      setState(() => _isLoading = false);

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            'Gagal mengupdate: ${e.toString().replaceFirst("Exception: ", "")}',
          ),
          backgroundColor: AppColors.error,
        ),
      );
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
          'Edit Barang',
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

                // Photos Section
                _buildPhotosSection(),
                const SizedBox(height: AppSpacing.lg),

                // Title Field
                CustomTextField(
                  controller: _titleController,
                  label: 'Judul Barang',
                  hint: 'Contoh: Dompet Hitam Kulit',
                  icon: Icons.title,
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Judul barang wajib diisi';
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

                // Storage Info (only for found items)
                if (widget.item.isFound) ...[
                  CustomTextField(
                    controller: _storageInfoController,
                    label: 'Info Penyimpanan',
                    hint: 'Contoh: Disimpan di Satpam Gedung A',
                    icon: Icons.inventory_2,
                  ),
                  const SizedBox(height: AppSpacing.lg),
                ],

                // Date & Time Pickers
                _buildSectionTitle(
                  widget.item.isLost
                      ? 'Tanggal & Waktu Hilang'
                      : 'Tanggal & Waktu Ditemukan',
                ),
                const SizedBox(height: AppSpacing.sm),
                _buildDateTimePickers(),
                const SizedBox(height: AppSpacing.xl),

                // Submit Button
                PrimaryButton(
                  text: 'Simpan Perubahan',
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

  Widget _buildPhotosSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildSectionTitle('Foto Barang (${_existingPhotos.length}/3)'),
        const SizedBox(height: AppSpacing.sm),
        SizedBox(
          height: 100,
          child: ListView(
            scrollDirection: Axis.horizontal,
            children: [
              // Existing photos
              ..._existingPhotos.asMap().entries.map((entry) {
                final index = entry.key;
                final photo = entry.value;
                return Padding(
                  padding: const EdgeInsets.only(right: AppSpacing.sm),
                  child: _buildPhotoItem(photo['url'], index),
                );
              }),
              // Add photo button
              if (_existingPhotos.length < 3) _buildAddPhotoButton(),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildPhotoItem(String url, int index) {
    final fullUrl = AppConstants.getFullImageUrl(url);
    return Stack(
      children: [
        ClipRRect(
          borderRadius: BorderRadius.circular(AppRadius.md),
          child: Container(
            width: 100,
            height: 100,
            color: AppColors.surfaceAlt,
            child: fullUrl != null
                ? Image.network(
                    fullUrl,
                    fit: BoxFit.cover,
                    errorBuilder: (context, error, stackTrace) => const Center(
                      child: Icon(
                        Icons.broken_image,
                        color: AppColors.textTertiary,
                      ),
                    ),
                  )
                : const Center(
                    child: Icon(Icons.image, color: AppColors.textTertiary),
                  ),
          ),
        ),
        Positioned(
          top: 4,
          right: 4,
          child: GestureDetector(
            onTap: () => _deletePhoto(index),
            child: Container(
              padding: const EdgeInsets.all(4),
              decoration: BoxDecoration(
                color: AppColors.error,
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.2),
                    blurRadius: 4,
                  ),
                ],
              ),
              child: const Icon(Icons.close, color: Colors.white, size: 14),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildAddPhotoButton() {
    return GestureDetector(
      onTap: _isUploadingPhoto ? null : _pickAndUploadPhoto,
      child: Container(
        width: 100,
        height: 100,
        decoration: BoxDecoration(
          color: AppColors.surfaceAlt,
          borderRadius: BorderRadius.circular(AppRadius.md),
          border: Border.all(color: AppColors.border, style: BorderStyle.solid),
        ),
        child: _isUploadingPhoto
            ? const Center(
                child: SizedBox(
                  width: 24,
                  height: 24,
                  child: CircularProgressIndicator(strokeWidth: 2),
                ),
              )
            : const Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.add_photo_alternate_outlined,
                    color: AppColors.primary,
                    size: 32,
                  ),
                  SizedBox(height: 4),
                  Text(
                    'Tambah',
                    style: TextStyle(
                      color: AppColors.textSecondary,
                      fontSize: 12,
                    ),
                  ),
                ],
              ),
      ),
    );
  }

  Widget _buildTypeBadge() {
    final isLost = widget.item.isLost;
    return Container(
      padding: const EdgeInsets.symmetric(
        horizontal: AppSpacing.md,
        vertical: AppSpacing.sm,
      ),
      decoration: BoxDecoration(
        color: (isLost ? AppColors.lostBadge : AppColors.foundBadge).withAlpha(
          26,
        ),
        borderRadius: BorderRadius.circular(AppRadius.full),
        border: Border.all(
          color: isLost ? AppColors.lostBadge : AppColors.foundBadge,
        ),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(
            isLost ? Icons.search_off : Icons.search,
            color: isLost ? AppColors.lostBadge : AppColors.foundBadge,
            size: 18,
          ),
          const SizedBox(width: AppSpacing.xs),
          Text(
            isLost ? 'Barang Hilang' : 'Barang Temuan',
            style: AppTextStyles.body.copyWith(
              color: isLost ? AppColors.lostBadge : AppColors.foundBadge,
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
      style: AppTextStyles.body.copyWith(
        fontWeight: FontWeight.w600,
        color: AppColors.textPrimary,
      ),
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
            SizedBox(width: AppSpacing.md),
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
          icon: const Icon(Icons.keyboard_arrow_down),
          hint: const Text('Pilih kategori'),
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
            setState(() => _selectedCategory = value);
          },
        ),
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

  Widget _buildDescriptionField() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildSectionTitle('Deskripsi'),
        const SizedBox(height: AppSpacing.sm),
        TextFormField(
          controller: _descriptionController,
          maxLines: 4,
          decoration: InputDecoration(
            hintText: 'Jelaskan ciri-ciri barang secara detail...',
            filled: true,
            fillColor: AppColors.surface,
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(AppRadius.md),
              borderSide: const BorderSide(color: AppColors.border),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(AppRadius.md),
              borderSide: const BorderSide(color: AppColors.border),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(AppRadius.md),
              borderSide: const BorderSide(color: AppColors.primary, width: 2),
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

  Widget _buildLocationPicker() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildSectionTitle(
          widget.item.isLost ? 'Lokasi Terakhir Terlihat' : 'Lokasi Ditemukan',
        ),
        const SizedBox(height: AppSpacing.sm),
        GestureDetector(
          onTap: () async {
            final result = await Navigator.push<SelectedLocation>(
              context,
              MaterialPageRoute(
                builder: (context) => LocationPickerScreen(
                  initialLocation: _selectedLocation != null
                      ? LatLng(
                          _selectedLocation!.latitude,
                          _selectedLocation!.longitude,
                        )
                      : null,
                  initialAddress: _locationController.text,
                ),
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
                    : AppColors.primary.withAlpha(128),
              ),
            ),
            child: Row(
              children: [
                Icon(
                  Icons.location_on,
                  color: _locationController.text.isEmpty
                      ? AppColors.textSecondary
                      : AppColors.primary,
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
        _buildSectionTitle('Detail Lokasi (Opsional)'),
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
        // Date Picker
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
                    color: AppColors.primary,
                    size: 20,
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
        const SizedBox(width: AppSpacing.md),
        // Time Picker
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
                    color: AppColors.primary,
                    size: 20,
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
}
