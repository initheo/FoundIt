# 📦 Paket Serah Terima Perangkat Lunak
## *Software Handover Package - Cross-Testing & UAT*

---

> **Dokumen ini bersifat resmi dan dipersiapkan untuk keperluan pengujian lintas kelompok.**
> Mohon baca seluruh bagian sebelum memulai proses pengujian.

---

## 1. Identitas Kelompok Pemilik

| Keterangan | Detail |
|---|---|
| **Nama / No. Kelompok** | Kelompok 1 |
| **Mata Kuliah** | Pengujian Perangkat Lunak |
| **Program Studi** | Informatika - Universitas Internasional Semen Indonesia (UISI) |
| **Tanggal Serah Terima** | 9 Juni 2026 |

### Anggota Kelompok

| No. | Nama | NIM |
|-----|------|-----|
| 1 | Muhammad Rafli Afriansyah Ikhsan | 3012310702 |
| 2 | Muhammad Muqoffin Nuha | 3012310023 |
| 3 | Sustri Elina Simamora | 3012310040 |

---

## 2. Informasi Aplikasi

| Keterangan | Detail |
|---|---|
| **Nama Aplikasi** | FoundIt - Platform Lost & Found UISI |
| **Versi** | v2.0.0 |
| **Tipe Aplikasi** | Mobile Application (Android & iOS) |
| **Tanggal Rilis (Build)** | Juli 2026 |

### Tech Stack

| Layer | Teknologi | Versi |
|-------|-----------|-------|
| **Mobile Frontend** | Flutter | 3.10+ |
| **Backend API** | Laravel | 12.x |
| **Runtime Backend** | PHP | 8.2+ |
| **Database** | SQLite (lokal) / MySQL (produksi) | 8.0 |
| **Autentikasi** | Laravel Sanctum | 4.0 |
| **Dokumentasi API** | Scramble (auto-generated) | 0.13 |
| **Maps & Lokasi** | Google Maps Flutter | 2.6 |

### Deskripsi Singkat

> **FoundIt** adalah aplikasi mobile berbasis Flutter yang membantu civitas akademika UISI untuk melaporkan barang hilang, melaporkan barang temuan, serta memfasilitasi proses klaim dan pengembalian barang secara terkoordinasi di lingkungan kampus.

---

## 3. Akses Aplikasi

### 3.1 URL & Deployment

| Lingkungan | Keterangan |
|---|---|
| **Download APK (Android)** | [app-release.apk - OneDrive](https://uisiacid-my.sharepoint.com/:u:/g/personal/muhammad_nuha23_student_uisi_ac_id/IQDI4l6xcQLJTo0tZxXRMnsZAfy-d2W1fx8Eg7KtCJ4_z54?e=TBbEB7) |
| **Deployment Publik** | Backend sudah di-deploy - lihat Admin Panel di atas |
| **Backend API (lokal)** | `http://localhost:8000/api` |
| **Dokumentasi API** | `http://localhost:8000/docs` *(auto-generated oleh Scramble)* |
| **Admin Panel (Web)** | [https://foundit.neoartd.my.id/admin](https://foundit.neoartd.my.id/admin) |

---

### 3.2 Akun Demo

> ⚠️ **Semua akun demo menggunakan password yang sama:** `password123`
> Email wajib domain UISI (`@student.uisi.ac.id` atau `@uisi.ac.id`).

#### Role: Admin (Akses penuh + Panel Admin Web)

| # | Nama | Email | Password |
|---|------|-------|----------|
| 1 | muqoffin | `muqoffin@student.uisi.ac.id` | `password123` |
| 2 | Muhammad Muqoffin Nuha | `muhammad.nuha23@student.uisi.ac.id` | `password123` |
| 3 | Ari Setia Hinanda | `ari.hinanda23@student.uisi.ac.id` | `password123` |

#### Role: User (Akses fitur aplikasi standar)

| # | Nama | Email | Password |
|---|------|-------|----------|
| 1 | Sustri | `sustri.simamora23@student.uisi.ac.id` | `password123` |
| 2 | Muhammad Muqoffin Nuha | `muqoffinn@student.uisi.ac.id` | `password123` |
| 3 | Ari Setia Hinanda | `ari.hinanda233@student.uisi.ac.id` | `password123` |
| 4 | Andi Pratama | `andi@student.uisi.ac.id` | `password123` |
| 5 | anriu | `theo@student.uisi.ac.id` | `password123` |

---

### 3.3 Panduan Instalasi Lokal

> Ikuti langkah berikut secara berurutan. Pastikan sudah terinstal: **PHP 8.2+**, **Composer**, **Flutter SDK 3.10+**, dan **Git**.

#### Backend (Laravel API)

```bash
# 1. Clone repository
git clone https://github.com/initheo/FoundIt.git
cd FoundIt/foundit_api

# 2. Install dependency PHP
composer install

# 3. Siapkan environment
cp .env.example .env
php artisan key:generate

# 4. Jalankan migrasi database & seeder (termasuk data demo)
php artisan migrate --seed

# 5. Buat symlink storage untuk foto
php artisan storage:link

# 6. Jalankan server
php artisan serve --host=0.0.0.0 --port=8000
```

> **Catatan:** Database default menggunakan SQLite. File database tersedia di `database/database.sqlite`.

#### Frontend (Flutter App)

```bash
# 1. Masuk ke direktori aplikasi Flutter
cd FoundIt/foundit_app

# 2. Install dependency Flutter
flutter pub get

# 3. Konfigurasi URL API
# Edit file: lib/shared/utils/app_constants.dart
# Ganti baseUrl sesuai lingkungan:
# - Android Emulator : http://10.0.2.2:8000/api
# - iOS Simulator : http://127.0.0.1:8000/api
# - Device Fisik : http://<IP_LOKAL>:8000/api

# 4. Jalankan aplikasi
flutter run
```

#### One-Command Setup (Backend)

```bash
# Jalankan semua setup backend dalam satu perintah
cd foundit_api && composer run setup
```

---

## 4. Modul / Fitur yang Diserahkan untuk Diuji

Berikut adalah seluruh modul yang **wajib** diuji oleh kelompok penguji:

### Modul 1 - Autentikasi

| # | Sub-Fitur | Deskripsi |
|---|-----------|-----------|
| 1.1 | Registrasi | Daftar akun baru; email **wajib** domain `@student.uisi.ac.id` atau `@uisi.ac.id` |
| 1.2 | Login | Login dengan email & password; menerima Bearer token |
| 1.3 | Logout | Invalidasi token aktif |

---

### Modul 2 - Laporan Barang

| # | Sub-Fitur | Deskripsi |
|---|-----------|-----------|
| 2.1 | Lapor Barang Hilang | Form input lengkap: judul, deskripsi, lokasi, tanggal, kategori |
| 2.2 | Lapor Barang Temuan | Sama dengan 2.1 + field `storage_info` (info penyimpanan barang) |
| 2.3 | Upload Foto | Upload hingga **3 foto** per laporan (JPEG/PNG, maks. 2 MB/foto) |
| 2.4 | Pilih Lokasi via Maps | Pin lokasi di Google Maps; menyimpan `latitude` & `longitude` |
| 2.5 | Edit Laporan | Pemilik laporan dapat mengedit detail dan menambah/hapus foto |
| 2.6 | Hapus Laporan | Pemilik laporan dapat menghapus laporan beserta fotonya |
| 2.7 | Update Status | **"Tandai Sudah Dikembalikan"** oleh pemilik (found) atau approved claimer (lost) |

---

### Modul 3 - Pencarian & Filter

| # | Sub-Fitur | Deskripsi |
|---|-----------|-----------|
| 3.1 | Daftar Semua Item | Tampil semua barang aktif (belum returned), diurutkan terbaru |
| 3.2 | Filter Tipe | Filter berdasarkan `lost` atau `found` |
| 3.3 | Filter Kategori | Filter berdasarkan kategori: Elektronik, Dokumen, Aksesoris, Tas & Dompet, Kunci, Pakaian, Lainnya |
| 3.4 | Pencarian Teks | Cari berdasarkan judul, deskripsi, atau lokasi (case-insensitive) |
| 3.5 | Detail Item | Lihat detail lengkap barang + foto + info pelapor |

---

### Modul 4 - Sistem Klaim

| # | Sub-Fitur | Deskripsi |
|---|-----------|-----------|
| 4.1 | Ajukan Klaim | User mengklaim suatu item dengan alasan (min. 20 karakter) |
| 4.2 | Lihat Daftar Klaim | Pemilik item melihat semua klaim masuk via tombol **"Lihat Klaim Masuk"** |
| 4.3 | Setujui Klaim | Pemilik menyetujui satu klaim; klaim lain otomatis ditolak |
| 4.4 | Tolak Klaim | Pemilik menolak klaim dengan alasan penolakan |
| 4.5 | Riwayat Klaim Saya | User melihat semua klaim yang pernah diajukan beserta statusnya |

**Aturan bisnis klaim yang perlu divalidasi:**
- User tidak bisa mengklaim barangnya sendiri
- Item yang sudah `claimed` / `returned` tidak bisa diklaim ulang
- Satu user hanya boleh punya satu klaim `pending` atau `approved` per item

---

### Modul 5 - Profil Pengguna

| # | Sub-Fitur | Deskripsi |
|---|-----------|-----------|
| 5.1 | Lihat Profil | Nama, NIM/email, prodi/unit, nomor telepon, foto profil |
| 5.2 | Edit Profil | Update nama, nomor telepon, prodi/unit |
| 5.3 | Upload Foto Profil | Ganti foto profil (JPEG/PNG, maks. 2 MB) |
| 5.4 | Statistik Personal | Jumlah barang dilaporkan, diklaim, dan dikembalikan |

---

### Modul 6 - Riwayat Aktivitas

| # | Sub-Fitur | Deskripsi |
|---|-----------|-----------|
| 6.1 | Log Aktivitas | Menampilkan timeline semua aktivitas user: melapor, mengklaim, menerima klaim, barang dikembalikan |

---

### Modul 7 - Leaderboard

| # | Sub-Fitur | Deskripsi |
|---|-----------|-----------|
| 7.1 | Ranking Kontributor | Menampilkan peringkat pengguna berdasarkan jumlah barang yang berhasil dikembalikan |

---

---

> ⚠️ **Catatan:** Admin Panel **tidak termasuk** dalam scope pengujian lintas kelompok ini. Pengujian difokuskan pada **aplikasi mobile (Flutter)** dan **Backend API (Laravel)** saja.

---

## 5. Dokumen Pendukung yang Disertakan

| Dokumen | Status | Lokasi |
|---------|--------|--------|
| ✅ Dokumentasi API (Auto-generated) | Tersedia | `http://localhost:8000/docs` |
| ✅ Panduan Instalasi Backend | Tersedia | `foundit_api/README.md` |
| ✅ Panduan Pengujian Backend | Tersedia | `foundit_api/tests/README.md` |
| ✅ Panduan Pengujian Frontend | Tersedia | `foundit_app/test/README.md` |
| ✅ Skema Database (via Migrations) | Tersedia | `foundit_api/database/migrations/` |
| ✅ ERD (implisit dari model & migrasi) | Tersedia | Lihat Bagian 6 dokumen ini |
| ✅ User Stories / Spesifikasi Fitur | Tersedia | `USER_STORIES.md` |
| ⬜ Wireframe / UI Mockup | Belum disertakan | - |

---

## 6. Skema Database (Ringkasan ERD)

```
users
├── id, name, email, password, phone, prodi_unit, photo_url, role
│
categories
├── id, name, icon
│
items
├── id, user_id (FK→users), category_id (FK→categories)
├── type [lost|found], title, description
├── location, location_detail, latitude, longitude
├── date_time, storage_info
└── status [active|claimed|returned]
│
item_photos
├── id, item_id (FK→items), photo_url
│
claims
├── id, item_id (FK→items), claimer_id (FK→users)
├── reason, status [pending|approved|rejected]
├── rejection_reason, reviewed_at
```

**Relasi:**
- `users` ←(1:N)→ `items` *(satu user bisa lapor banyak item)*
- `items` ←(1:N)→ `item_photos` *(satu item bisa punya maks. 3 foto)*
- `items` ←(1:N)→ `claims` *(satu item bisa menerima banyak klaim)*
- `users` ←(1:N)→ `claims` *(satu user bisa mengajukan klaim ke banyak item)*

---

## 7. Skenario Pengujian yang Disarankan (Happy Path)

Berikut adalah alur utama yang **disarankan** untuk diuji secara end-to-end:

### Skenario A - Penemu Melaporkan & Pemilik Mengklaim
```
1. Login sebagai User A (penemu)
2. Buat laporan barang TEMUAN dengan foto & lokasi
3. Logout → Login sebagai User B (pemilik)
4. Cari barang di daftar, buka detail
5. Ajukan klaim dengan alasan lengkap
6. Logout → Login kembali sebagai User A
7. Buka daftar klaim masuk → Setujui klaim User B
8. Login sebagai User B → Mark as returned
9. Verifikasi status barang berubah menjadi "returned"
10. Verifikasi leaderboard User A bertambah
```

### Skenario B - Pemilik Melaporkan Kehilangan & Penemu Melapor
```
1. Login sebagai User A (pemilik)
2. Buat laporan barang HILANG
3. Login sebagai User B (penemu)
4. Temukan laporan → Ajukan klaim "Saya Menemukannya"
5. Login sebagai User A → Approve klaim
6. Login sebagai User B → Mark as returned
```

### Skenario C - Klaim Ditolak & Pengajuan Ulang
```
1. Login sebagai User B → Ajukan klaim pada item milik User A
2. Login sebagai User A → Tolak klaim dengan alasan
3. Login sebagai User B → Verifikasi notifikasi penolakan dan alasan tampil
4. User B → Ajukan klaim baru (ulang) pada item yang sama
5. Verifikasi sistem menerima klaim ulang setelah ditolak
```

---

## 8. Catatan Khusus untuk Tim Penguji

> Baca bagian ini sebelum memulai pengujian.

### ⚠️ Keterbatasan & Hal yang Perlu Diperhatikan

1. **Validasi Email Domain**: Registrasi **hanya menerima** email dengan domain `@student.uisi.ac.id` atau `@uisi.ac.id`. Email dengan domain lain (Gmail, Yahoo, dll.) akan ditolak. Gunakan akun demo yang sudah disediakan.

2. **Upload Foto**: Foto hanya bisa diupload melalui aplikasi Flutter (kamera atau galeri). Endpoint API menerima `multipart/form-data`.

3. **Google Maps**: Fitur pilih lokasi via peta memerlukan koneksi internet dan konfigurasi Google Maps API Key yang valid di build Flutter.

4. **Storage Foto**: Setelah `php artisan serve` dijalankan, pastikan `php artisan storage:link` sudah dieksekusi agar foto yang diupload dapat diakses.

5. **Koordinasi via WhatsApp**: Fitur "Hubungi via WhatsApp" pada detail item hanya berfungsi jika nomor telepon pelapor sudah diisi dan WhatsApp terinstal di perangkat.

6. **Modul Leaderboard**: Ranking baru muncul setelah ada item yang berstatus `returned`. Jika database bersih, leaderboard akan kosong.

7. **Admin Panel**: Admin panel berbasis web **tidak termasuk dalam scope pengujian ini**. Panel tersedia di `https://foundit.neoartd.my.id/admin` namun tidak perlu diuji.

8. **Database Default (SQLite)**: Aplikasi menggunakan SQLite secara default untuk kemudahan instalasi lokal. Untuk lingkungan produksi, konfigurasi MySQL tersedia di `.env`.

### 🐛 Bug Diketahui (Known Issues dari Pengujian Sebelumnya)

Bug berikut **sudah diidentifikasi** pada siklus pengujian UTS. Tim penguji diharapkan **memverifikasi apakah bug ini sudah diperbaiki atau belum**, bukan melaporkan sebagai temuan baru.

| ID | Modul | Deskripsi | Severity | Status Terakhir |
|----|-------|-----------|----------|-----------------|
| BUG-001 | Home | Hanya 4 item card yang terlihat dalam satu layar (ekspektasi: min. 5) | Low | Belum diperbaiki |
| BUG-002 | Klaim | Daftar klaim masuk tidak menampilkan tanggal pengajuan, hanya waktu relatif ("2 jam yang lalu") | Medium | Belum diperbaiki |
| BUG-003 | Profil | Circle foto profil tidak dapat diklik langsung untuk ganti foto; gunakan menu **Edit Profil** | Low | Belum diperbaiki |
| BUG-004 | Profil | Statistik profil tidak menampilkan metrik "Total Poin" (hanya 3 metrik: Laporan, Klaim, Dikembalikan) | Medium | Belum diperbaiki |

### 🔒 Aturan Otorisasi Penting

| Aksi | Siapa yang Bisa |
|------|-----------------|
| Edit / hapus laporan | Hanya pemilik laporan |
| Lihat daftar klaim masuk | Hanya pemilik item |
| Approve / reject klaim | Hanya pemilik item |
| Mark as returned (found) | Hanya pemilik item |
| Mark as returned (lost) | Hanya claimer yang sudah approved |
| Akses Admin Panel | Hanya user dengan `role = admin` - *tidak dalam scope pengujian* |

---

## 9. Kontak Tim Pengembang

Jika ditemukan kendala teknis selama pengujian, hubungi tim pengembang:

| Nama | Kontak |
|------|--------|
| Muhammad Muqoffin Nuha | `muqoffin@student.uisi.ac.id` |
| Ari Setia Hinanda | `ari.hinanda23@student.uisi.ac.id` |

---

## 10. Catatan Perbaikan Bug (Handover v2.0.0)

Berikut adalah ringkasan perbaikan bug yang dilaporkan oleh penguji independen (Kelompok 4) dalam dokumen Laporan Bug Kelompok 1 dan telah diselesaikan pada versi ini:

| ID Bug | Deskripsi Temuan Bug | Status Perbaikan | Keterangan Teknis / Solusi |
|---|---|---|---|
| **BUG-01** | Nomor HP duplikat lolos validasi keunikan pada form registrasi. | **Fixed** | Menambahkan validasi `'unique:users,phone'` di `RegisterRequest` pada Laravel backend untuk menjamin keunikan nomor handphone. |
| **BUG-02** | Validasi panjang nomor HP format `+62` atau `62` terlalu pendek lolos. | **Fixed** | Memperbaiki parser normalisasi nomor HP di `prepareForValidation()` dan menambahkan regex `^08[0-9]{8,11}$` yang mendeteksi panjang akhir 10-13 digit angka setelah normalisasi. |
| **BUG-03** | Inkonsistensi instruksi minimal password (UI menyebut 6 karakter, backend wajib 8 karakter). | **Fixed** | Menyelaraskan teks instruksi di UI/halaman Register Flutter menjadi "Minimal 8 karakter" dan memperbarui validator di frontend agar sinkron dengan aturan backend (minimal 8 karakter). |
| **BUG-04** | Upload foto berukuran di atas 2MB lolos tanpa peringatan (Upload Bypass). | **Fixed** | Menerapkan validasi ukuran maksimal file gambar (maks 2MB / 2048 KB) secara ketat pada sisi backend API serta integrasi penanganan error ukuran di sisi frontend. |
| **BUG-05** | Tidak ada pembatasan percobaan login (Rate Limiting) pada API login. | **Fixed** | Menerapkan middleware rate limiting `throttle:login` (maksimal 10 kali percobaan per menit) di rute `/api/login` Laravel backend untuk mencegah serangan brute force. |
| **BUG-06** | Validasi header `Content-Type` API terlalu longgar (menerima `text/plain` untuk body JSON). | **Fixed** | Menambahkan middleware `ValidateContentType` secara global pada routing API backend untuk memastikan request POST/PUT/PATCH wajib menyertakan Content-Type yang valid (`application/json`, `multipart/form-data`, dll) atau mengembalikan HTTP 415. |
| **BUG-07** | URL search parameter yang berisi karakter khusus (`&` atau `=`) memutus struktur URL (URL Breaking) pada saat mencari item. | **Fixed** | Menambahkan `Uri.encodeComponent()` pada nilai parameter pencarian di Frontend (`ItemRepository.dart`) untuk melakukan URL encoding secara aman. |
| **BUG-08** | Sistem menerima input tanggal kejadian (`date_time`) di masa depan (contoh: tahun 2099) untuk item yang hilang/ditemukan. | **Fixed** | Menambahkan validasi waktu maksimal `before_or_equal:now` pada `ItemController.php` beserta custom message error untuk memastikan tanggal logis (masa lalu/saat ini). |

---

<p align="center">
 <i>Dokumen ini dibuat untuk keperluan UAS Mata Kuliah Pengujian Perangkat Lunak<br>
 Universitas Internasional Semen Indonesia (UISI) - 2026</i><br><br>
 <b>FoundIt</b> 🔍 - Kehilangan? Tenang, kami bantu temukan!
</p>
