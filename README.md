# FoundIt 🔍

**Platform Pelaporan dan Pengembalian Barang Hilang**

Aplikasi mobile untuk membantu civitas akademika UISI dalam melaporkan dan menemukan barang hilang di lingkungan kampus.

## 📦 Repository Structure

```
FoundItUas/
├── foundit_api/    # Backend (Laravel 12 + PHP 8.2)
├── foundit_app/    # Frontend (Flutter 3.10+)
└── README.md       # Dokumentasi ini
```

## 🎯 Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| **Lapor Kehilangan** | User bisa melaporkan barang yang hilang dengan foto dan lokasi |
| **Lapor Temuan** | User bisa melaporkan barang yang ditemukan |
| **Claim Barang** | Pemilik bisa claim barang temuan ("Ini Barang Saya!") |
| **Laporkan Temuan** | Penemu bisa melapor pada barang hilang ("Saya Menemukan!") |
| **Review Claims** | Pemilik/penemu bisa approve/reject claim |
| **Koordinasi** | Komunikasi via WhatsApp untuk pengembalian |
| **Leaderboard** | Ranking kontributor pengembalian barang |
| **Riwayat Aktivitas** | Log semua aktivitas user |

## 📱 Fitur Detail per Screen

### Authentication
- ✅ Login dengan email UISI (@student.uisi.ac.id / @uisi.ac.id)
- ✅ Registrasi akun baru dengan validasi
- ✅ Logout dengan konfirmasi

### Home Screen
- ✅ Daftar barang hilang/temuan
- ✅ Tab filter (Lost/Found)
- ✅ Search by title
- ✅ Filter by category
- ✅ Pull to refresh
- ✅ Statistik personal (dilaporkan, diklaim, dikembalikan)

### Report Screen
- ✅ Form lapor kehilangan
- ✅ Form lapor temuan
- ✅ Upload multiple foto (kamera/galeri)
- ✅ Pilih lokasi di Google Maps
- ✅ Detail lokasi kustom
- ✅ Pilih kategori barang

### Item Detail Screen
- ✅ Detail barang lengkap
- ✅ Photo viewer fullscreen dengan zoom
- ✅ Info pelapor + kontak WhatsApp
- ✅ Tombol claim/laporkan temuan
- ✅ Edit & delete (owner only)

### Claim System
- ✅ Submit claim dengan alasan
- ✅ Review claims list
- ✅ Approve/Reject claim
- ✅ Riwayat claim user
- ✅ Mark as returned

### Profile Screen
- ✅ View profile
- ✅ Edit profile (nama, NIM, phone)
- ✅ Upload foto profil
- ✅ Statistik personal
- ✅ Riwayat aktivitas

### Leaderboard
- ✅ Ranking pengguna berdasarkan barang dikembalikan
- ✅ Pull to refresh

## 🚀 Quick Start

### 1. Backend (Laravel API)

```bash
cd foundit_api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve --host=0.0.0.0
```

### 2. Frontend (Flutter App)

```bash
cd foundit_app
flutter pub get
flutter run
```

### 3. Konfigurasi API URL

Edit `lib/shared/utils/app_constants.dart`:
```dart
// Android Emulator
return 'http://10.0.2.2:8000/api';

// iOS Simulator
return 'http://127.0.0.1:8000/api';

// Device fisik (ganti dengan IP lokal)
return 'http://192.168.x.x:8000/api';
```

## 🛠️ Tech Stack

### Backend
| Teknologi | Versi | Fungsi |
|-----------|-------|--------|
| Laravel | 12 | Framework |
| PHP | 8.2+ | Runtime |
| MySQL | 8.0 | Database |
| Laravel Sanctum | 4.0 | Authentication |
| Scramble | 0.13 | API Documentation |

### Frontend
| Teknologi | Versi | Fungsi |
|-----------|-------|--------|
| Flutter | 3.10+ | Framework |
| Google Maps | 2.6 | Maps & Location |
| Image Picker | 1.2 | Kamera & Galeri |
| Geolocator | 12.0 | GPS Location |
| HTTP | 1.6 | API Client |
| SharedPreferences | 2.5 | Local Storage |

## 📖 Dokumentasi Lengkap

Dokumentasi proyek telah disusun untuk membantu pengembangan dan pengujian:

- [**Backend API Test Guide**](foundit_api/tests/README.md) - Panduan pengujian unit dan integration test (Feature) di sisi backend Laravel.
- [**Mobile App Test Guide**](foundit_app/test/README.md) - Panduan pengujian unit test di sisi frontend Flutter.

---

## 👥 Tim Pengembang

| Nama | NIM |
|------|-----|
| Muhammad Muqoffin Nuha | 3012310023 | 
| Ari Setia Hinanda | 3012310005 | 

## 📝 Lisensi

Proyek ini dibuat untuk keperluan UAS Mata Kuliah Pemrograman Mobile - Universitas Internasional Semen Indonesia (UISI) Tahun 2026.

---

<p align="center">
  <b>FoundIt</b> - Kehilangan? Tenang, kami bantu temukan! 🔍
</p>
