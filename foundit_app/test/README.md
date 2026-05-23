# FoundIt App - Test Documentation

Repositori ini dilengkapi dengan pengujian unit untuk memastikan kebenaran logika bisnis, konversi model data (JSON parsing), dan validasi input pada aplikasi Flutter **FoundIt**.

---

## 🚀 Cara Menjalankan Test

Jalankan perintah berikut di direktori root `foundit_app`:

### 1. Menjalankan Semua Test
```bash
flutter test
```

### 2. Menjalankan File Test Spesifik
```bash
flutter test test/unit/utils/validators_test.dart
```

### 3. Menjalankan Test dengan Coverage Report
Untuk melihat statistik cakupan kode yang diuji:
```bash
flutter test --coverage
```
Laporan cakupan kode akan disimpan di dalam folder `coverage/lcov.info`.

---

## 📁 Struktur dan Daftar Pengujian

Pengujian unit dikelompokkan di bawah direktori `test/unit/`:

### 1. Model Data Tests (`test/unit/models/`)
Menguji validitas pemetaan (mapping) data dari format JSON (respons dari API) ke dalam model objek Dart serta sebaliknya.

| File Pengujian | Deskripsi |
| :--- | :--- |
| **[user_model_test.dart](file:///Users/muqoffin/Development/FoundIt/foundit_app/test/unit/models/user_model_test.dart)** | Menguji parsing model `UserModel`, validasi atribut profil user, pemeriksaan role user, dan serialisasi ke format JSON. |
| **[category_model_test.dart](file:///Users/muqoffin/Development/FoundIt/foundit_app/test/unit/models/category_model_test.dart)** | Menguji instansiasi `CategoryModel` untuk pengelompokkan jenis barang temuan/hilang. |
| **[item_model_test.dart](file:///Users/muqoffin/Development/FoundIt/foundit_app/test/unit/models/item_model_test.dart)** | Menguji `ItemModel` (termasuk konversi path foto menjadi URL penuh, validasi status barang, dan tipe laporan `lost`/`found`). |
| **[claim_model_test.dart](file:///Users/muqoffin/Development/FoundIt/foundit_app/test/unit/models/claim_model_test.dart)** | Menguji `ClaimModel` untuk memastikan data klaim barang terurai dengan benar berdasarkan status approval. |
| **[activity_entry_test.dart](file:///Users/muqoffin/Development/FoundIt/foundit_app/test/unit/models/activity_entry_test.dart)** | Menguji model riwayat aktivitas pengguna (`ActivityEntry`) dalam memetakan log aksi di aplikasi. |
| **[leaderboard_entry_test.dart](file:///Users/muqoffin/Development/FoundIt/foundit_app/test/unit/models/leaderboard_entry_test.dart)** | Menguji mapping peringkat pengembalian barang hilang pada model `LeaderboardEntry`. |

### 2. Utility Tests (`test/unit/utils/`)
Menguji fungsi-fungsi utilitas atau helper mandiri yang digunakan secara luas di seluruh aplikasi.

| File Pengujian | Deskripsi |
| :--- | :--- |
| **[validators_test.dart](file:///Users/muqoffin/Development/FoundIt/foundit_app/test/unit/utils/validators_test.dart)** | Menguji aturan validasi input pada form registrasi dan login: <br> • Validasi format email UISI (harus mengandung `@student.uisi.ac.id` atau `@uisi.ac.id`). <br> • Validasi kekuatan password (minimal 6 karakter). <br> • Validasi format nomor telepon seluler. |
| **[date_formatters_test.dart](file:///Users/muqoffin/Development/FoundIt/foundit_app/test/unit/utils/date_formatters_test.dart)** | Menguji helper pemformatan tanggal agar tampil dalam format bahasa Indonesia yang mudah dipahami oleh pengguna. |
