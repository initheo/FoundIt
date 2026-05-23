# FoundIt API - Test Documentation

Repositori ini dilengkapi dengan unit dan integration (feature) testing untuk memastikan keandalan, keamanan, dan fungsionalitas dari backend API **FoundIt**. Pengujian dilakukan menggunakan **PHPUnit** (framework pengujian bawaan Laravel).

---

## 🚀 Cara Menjalankan Test

Pastikan dependensi development sudah terinstal (`composer install`). Jalankan salah satu perintah berikut di dalam direktori `foundit_api`:

### 1. Menjalankan Semua Test
```bash
php artisan test
```
atau menggunakan PHPUnit secara langsung:
```bash
./vendor/bin/phpunit
```

### 2. Menjalankan Kelompok Test Tertentu
*   **Hanya Unit Test**:
    ```bash
    php artisan test --testsuite=Unit
    ```
*   **Hanya Integration/Feature Test**:
    ```bash
    php artisan test --testsuite=Feature
    ```

### 3. Menjalankan File Test Spesifik
```bash
php artisan test tests/Feature/AuthIntegrationTest.php
```

---

## 📁 Struktur dan Daftar Pengujian

Pengujian dibagi menjadi dua jenis utama di bawah folder `tests/`:

### 1. Unit Tests (`tests/Unit/`)
Unit test menguji bagian terkecil dari aplikasi secara terisolasi (seperti Model, Request Validation, dll) tanpa melakukan interaksi ke database eksternal sesungguhnya.

| File Pengujian | Deskripsi |
| :--- | :--- |
| **[CategoryModelTest.php](file:///Users/muqoffin/Development/FoundIt/foundit_api/tests/Unit/CategoryModelTest.php)** | Menguji instansiasi model `Category` dan relasinya terhadap model `Item`. |
| **[ClaimModelTest.php](file:///Users/muqoffin/Development/FoundIt/foundit_api/tests/Unit/ClaimModelTest.php)** | Menguji status klaim (`pending`, `approved`, `rejected`), relasi terhadap `User` (claimer) dan `Item`, serta scope query. |
| **[ItemModelTest.php](file:///Users/muqoffin/Development/FoundIt/foundit_api/tests/Unit/ItemModelTest.php)** | Menguji tipe item (`lost` atau `found`), status aktif, relasi ke `User` (pelapor), `Category`, `ItemPhoto`, dan `Claim`. |
| **[ItemPhotoModelTest.php](file:///Users/muqoffin/Development/FoundIt/foundit_api/tests/Unit/ItemPhotoModelTest.php)** | Menguji penyimpanan nama file foto, relasinya ke model `Item`, serta helper untuk menghasilkan URL foto lengkap. |
| **[LoginRequestTest.php](file:///Users/muqoffin/Development/FoundIt/foundit_api/tests/Unit/LoginRequestTest.php)** | Menguji validasi input login (email UISI valid, format email, password wajib diisi, dll). |
| **[RegisterRequestTest.php](file:///Users/muqoffin/Development/FoundIt/foundit_api/tests/Unit/RegisterRequestTest.php)** | Menguji aturan validasi registrasi (email harus menggunakan domain `@student.uisi.ac.id` atau `@uisi.ac.id`, panjang password minimal, keunikan email). |
| **[UserModelTest.php](file:///Users/muqoffin/Development/FoundIt/foundit_api/tests/Unit/UserModelTest.php)** | Menguji enkripsi password (hashing), penentuan peran (*role* seperti `admin` atau `user`), dan relasi kepemilikan item/klaim. |

### 2. Integration / Feature Tests (`tests/Feature/`)
Integration/Feature test menguji fungsionalitas yang lebih luas dengan menyimulasikan request HTTP asli, memeriksa respons JSON, status HTTP, serta konsistensi data yang tersimpan di database.

| File Pengujian | Deskripsi |
| :--- | :--- |
| **[AuthIntegrationTest.php](file:///Users/muqoffin/Development/FoundIt/foundit_api/tests/Feature/AuthIntegrationTest.php)** | Menguji alur registrasi akun baru, login untuk mendapatkan token API (Laravel Sanctum), pengaksesan rute terproteksi, dan proses logout. |
| **[CategoryIntegrationTest.php](file:///Users/muqoffin/Development/FoundIt/foundit_api/tests/Feature/CategoryIntegrationTest.php)** | Menguji pengambilan data daftar kategori barang melalui rute API. |
| **[ProfileIntegrationTest.php](file:///Users/muqoffin/Development/FoundIt/foundit_api/tests/Feature/ProfileIntegrationTest.php)** | Menguji pembacaan data profil user yang login, pembaruan data profil, unggah foto profil, serta pengambilan data peringkat (*leaderboard*). |
| **[ItemIntegrationTest.php](file:///Users/muqoffin/Development/FoundIt/foundit_api/tests/Feature/ItemIntegrationTest.php)** | Menguji endpoint list item umum dan detail spesifik dari laporan barang yang hilang atau ditemukan. |
| **[ItemCrudIntegrationTest.php](file:///Users/muqoffin/Development/FoundIt/foundit_api/tests/Feature/ItemCrudIntegrationTest.php)** | Menguji pembuatan laporan barang baru, pengunggahan foto barang pendukung, pengeditan info laporan, serta penghapusan laporan. |
| **[ItemFilterIntegrationTest.php](file:///Users/muqoffin/Development/FoundIt/foundit_api/tests/Feature/ItemFilterIntegrationTest.php)** | Menguji fitur pencarian barang, filter berdasarkan kategori, penyaringan tipe laporan (`lost` / `found`), serta pengurutan data (*sorting*). |
| **[ClaimIntegrationTest.php](file:///Users/muqoffin/Development/FoundIt/foundit_api/tests/Feature/ClaimIntegrationTest.php)** | Menguji pengajuan klaim atas barang temuan, validasi klaim ganda, daftar klaim masuk, dan penolakan klaim oleh penemu barang. |
| **[ReturnFlowIntegrationTest.php](file:///Users/muqoffin/Development/FoundIt/foundit_api/tests/Feature/ReturnFlowIntegrationTest.php)** | Menguji alur lengkap pengembalian barang dari pengajuan klaim, persetujuan klaim (*approval*), perubahan status barang menjadi selesai/kembali (`returned`), hingga pencatatan poin ke leaderboard. |
| **[AdminPanelTest.php](file:///Users/muqoffin/Development/FoundIt/foundit_api/tests/Feature/AdminPanelTest.php)** | Menguji fungsionalitas panel admin (Web), otorisasi rute khusus admin, dan manajemen CRUD data pengguna/laporan dari sisi administrator. |
| **[DataConsistencyTest.php](file:///Users/muqoffin/Development/FoundIt/foundit_api/tests/Feature/DataConsistencyTest.php)** | Menguji integritas data, konsistensi skema database antara SQLite (lokal/test) dengan PostgreSQL (produksi), serta validasi constraints. |

---

## 🛠️ Konfigurasi Lingkungan Pengujian

Secara default, Laravel menggunakan SQLite in-memory database untuk menjalankan pengujian agar proses berjalan sangat cepat dan tidak mengotori database utama. Pengaturan ini dikonfigurasi pada berkas `phpunit.xml`.
