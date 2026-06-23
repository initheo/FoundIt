# LAPORAN PENGUJIAN PERANGKAT LUNAK INDEPENDEN
## CROSS-TESTING & USER ACCEPTANCE TESTING (UAT)
## APK FOUNDIT - LOST & FOUND MANAGEMENT SYSTEM

**DOSEN PENGAMPU:**
Taufiqotul Bariyah, S.Kom., M.IM., MCE

**Disusun oleh:**
- Ari Setia Hinanda (3012310005)
- Bayu Nurcahyo (3012310007)
- Rahmansyah Ragil Cahyadi (3012310034)

**DEPARTEMEN INFORMATIKA**
**UNIVERSITAS INTERNASIONAL SEMEN INDONESIA**
**GRESIK — 2026**

---

# BAB I — PROFIL PERANGKAT LUNAK YANG DIUJI

- **Nama Aplikasi:** FoundIt - Lost & Found Management System
- **Kelompok Pemilik Aplikasi:** Kelompok 1
- **Kelompok Penguji:** Kelompok 4
- **Tanggal Pengujian:** 10–11 Juni 2026
- **Platform:** Mobile Application (Android/iOS) dengan Backend API Laravel
- **Versi:** 1.0.0
- **URL Backend API:** `http://localhost:8000/api`

**Deskripsi Aplikasi:**
FoundIt adalah aplikasi mobile berbasis Flutter yang membantu civitas akademika UISI untuk melaporkan barang hilang, melaporkan barang temuan, serta memfasilitasi proses klaim dan pengembalian barang secara terkoordinasi di lingkungan kampus.

**Lingkup Modul Fitur yang Diuji:**

1. **Sistem Autentikasi (Login/Register)**
   - Registrasi pengguna baru dengan email domain UISI
   - Login dan manajemen token autentikasi
   - Logout dan invalidasi token
2. **Manajemen Item (Pelaporan barang hilang/ditemukan)**
   - Pembuatan laporan barang hilang (Lost Items)
   - Pembuatan laporan barang ditemukan (Found Items)
   - Upload foto barang (maksimal 3 foto)
   - Edit dan hapus laporan item
   - Pencarian dan filter item berdasarkan kategori, tipe, dan kata kunci
   - Update status item (active/returned)
3. **Sistem Klaim Barang**
   - Pengajuan klaim kepemilikan barang
   - Review dan approval klaim oleh pemilik laporan
   - Penolakan klaim dengan alasan
   - Tracking status klaim (pending/approved/rejected)
4. **Manajemen Profil Pengguna**
   - Update informasi profil
   - Upload foto profil
   - Lihat riwayat item dan klaim
5. **Sistem Kategori Barang**
   - Browse kategori barang
   - Filter item berdasarkan kategori

**Teknologi yang Digunakan:**
- Backend: Laravel 12, PHP 8.2+
- Database: SQLite
- Authentication: Laravel Sanctum (Token-based)
- Storage: Laravel File Storage (Public Disk)
- Dokumentasi API: Scramble
- Maps & Lokasi: Google Maps Flutter 2.6
- Mobile: Flutter 3.10+

---

# BAB II — METODOLOGI & DESAIN PENGUJIAN

## 2.1 Tabel Test Case Input Domain (EP & BVA)

### 2.1.1 Form Registrasi Pengguna

**Kelas Equivalence:**
- **Email UISI valid:** Format surel benar (mengandung simbol @), menggunakan domain kampus (`@student.uisi.ac.id` atau `@uisi.ac.id`), dan belum terdaftar (unik).
- **Email UISI invalid:** Format salah (tanpa @), domain eksternal (`@gmail.com`), atau sudah terdaftar.
- **Password & Confirm Password valid:** Panjang minimal 8 karakter dan isian sama persis.
- **Password & Confirm Password invalid:** Panjang kurang dari 8 karakter (termasuk mismatch UI "Minimal 6 karakter") atau Confirm Password tidak cocok.
- **Nama Lengkap valid:** String 1–100 karakter.
- **Nama Lengkap invalid:** Kosong atau melebihi 100 karakter.
- **No. HP valid:** Format awalan 08, 62, atau +62, panjang digit ≤15 karakter, dan unik.
- **No. HP invalid:** Melebihi 15 digit, mengandung huruf/simbol selain angka dan +, atau sudah digunakan akun lain.

| ID | Skenario | Input Data | Ekspektasi Hasil | Hasil Aktual | Status |
|---|---|---|---|---|---|
| EP–REG–01 | Mahasiswa mendaftar dengan email kampus `@student.uisi.ac.id` | Nama: Rahmansyah Ragil Cahyadi; Email: rahmansyah.cahyadi23@student.uisi.ac.id; Prodi: Informatika; No. HP: 085156963404; Password/Confirm: kelompok4 | Sistem menyimpan data pengguna baru dan menampilkan alert "Registrasi Berhasil". | Sesuai ekspektasi. Setelah tombol 'Daftar' diklik, layar berpindah ke halaman Login tanpa kendala dan menampilkan alert "Registrasi Berhasil". | **Pass** |
| EP–REG–02 | Staff mendaftar dengan email kampus `@uisi.ac.id` | Nama: Andi Pratama; Email: staff.magang@student.uisi.ac.id; Prodi/Unit: Staff Magang; No. HP: 0812345678; Password/Confirm: kelompok4 | Sistem menyimpan data dan menampilkan alert "Registrasi Berhasil". | Sesuai ekspektasi. Layar berpindah ke halaman Login dan menampilkan alert "Registrasi Berhasil". | **Pass** |
| EP–REG–03 | Mahasiswa mendaftar dengan domain tidak sesuai (`@gmail.com`) | Nama: Ari Setia Hinanda; Email: ari.hinanda23@gmail.com; Prodi: Informatika; No. HP: 0812345678; Password/Confirm: kelompok4 | Sistem menolak pendaftaran dan memunculkan alert "Gunakan email @student.uisi.ac.id atau @uisi.ac.id". | Sesuai ekspektasi. Muncul teks merah sesuai pesan di bawah input email. | **Pass** |
| EP–REG–04 | Mahasiswa lupa mengetik simbol '@' saat mengisi email | Nama: Bayu Nurcahyo; Email: bayu.nurcahyo23student.uisi.ac.id; Prodi: Informatika; No. HP: 082273952703; Password/Confirm: kelompok4 | Sistem menolak pendaftaran dan memunculkan alert "Format email tidak valid". | Sesuai ekspektasi. Muncul teks merah "Format email tidak valid" di bawah input email. | **Pass** |
| EP–REG–05 | Mahasiswa lupa sudah punya akun dan mendaftar ulang dengan email yang sama | Nama: Rahmansyah Ragil Cahyadi; Email: rahmansyah.cahyadi23@student.uisi.ac.id; Prodi: Informatika; No. HP: 085156963404; Password/Confirm: kelompok4 | Sistem menemukan kesamaan data, membatalkan pendaftaran baru, dan memunculkan alert "Email sudah terdaftar". | Sesuai ekspektasi. Muncul alert "Email sudah terdaftar" dan registrasi tidak berhasil. | **Pass** |
| EP–REG–06 | Staff mendaftar dengan password valid dan Confirm Password sesuai | Nama: Budi Harianto; Email: budi.harianto@uisi.ac.id; Prodi: Staff; No. HP: 0812345679; Password/Confirm: kelompok4 | Sistem memvalidasi password, memproses pendaftaran, dan memunculkan alert "Registrasi Berhasil". | Sesuai ekspektasi. Pendaftaran sukses dengan alert "Registrasi Berhasil". | **Pass** |
| EP–REG–07 | Staff mendaftar dengan password valid namun Confirm Password tidak sesuai | Nama: Zabrina Arnyta; Email: zabrina.arnyta@uisi.ac.id; Prodi: Staff; No. HP: 0812345679; Password: kelompok4; Confirm: kelompok1 | Sistem mendeteksi ketidakcocokan sandi, pendaftaran gagal, dan muncul alert "Password tidak cocok". | Sesuai ekspektasi. Layar tidak berpindah dan muncul alert "Password tidak cocok". | **Pass** |
| EP–REG–08 | Staff mendaftar dengan No. HP yang telah terdaftar user sebelumnya | Nama: Zabrina Arnyta; Email: zabrina.arnyta@uisi.ac.id; Prodi: Staff; No. HP: 0812345679; Password/Confirm: kelompok4 | Sistem mendeteksi No. HP telah digunakan dan memunculkan alert "No. HP sudah terdaftar". | **Tidak sesuai ekspektasi.** Sistem gagal mencegah pendaftaran dengan No. HP yang sama dengan user terdahulu. | **Fail (bug)** |
| EP–REG–09 | Mahasiswa memasukkan No. HP format kode negara internasional (+62) | Nama: Muhammad Fakhrudin; Email: muhammad.fakhrudin23@student.uisi.ac.id; Prodi: Informatika; No. HP: +628123456; Password/Confirm: kelompok4 | Sistem mengenali format kode negara, menormalkan nomor, atau menolak jika digit kurang panjang. | **Tidak sesuai ekspektasi.** Sistem menerima kode negara tapi panjang nomor dianggap cukup, menyebabkan registrasi berhasil. | **Fail (bug)** |
| BVA–REG–01 | Staff membuat password pendek pas memenuhi batas aman minimal | Nama: Muhammad Tegar; Email: muhammad.tegar@student.uisi.ac.id; Prodi: Manajemen; No. HP: +628987654321; Password/Confirm: tes123 | Sistem memvalidasi panjang batas minimal, menyimpan data akun, dan registrasi berhasil. | **Tidak sesuai ekspektasi.** Pendaftaran gagal diproses dan ada ketidaksinkronan alert: teks "Minimal 6 karakter" padahal validasi sebenarnya 8 karakter. | **Fail** |
| BVA–REG–02 | Mahasiswa menginputkan nama dengan panjang 100 karakter | Nama: Muhammad Raffialda Syahputra Kusuma Wardhana Notonegoro Suryahadi Pratama Sastrawidjaja Kusumaningty; Email: muhammad.raffialda@student.uisi.ac.id; Prodi: Akutansi; No. HP: +628368291231; Password/Confirm: kelompok4 | Sistem menyimpan nama secara utuh dan menyelesaikan pendaftaran tanpa memotong teks. | Sesuai ekspektasi. Isian 100 karakter berhasil diproses dan tersimpan sempurna di Beranda. | **Pass** |
| BVA–REG–03 | Mahasiswa menginputkan nama melewati batas validasi (101 karakter) | Nama: ...Kusumaningtys (101 karakter); Email: muhammad.raffialdas@student.uisi.ac.id; Prodi: Akutansi; No. HP: +628368291232; Password/Confirm: kelompok4 | Sistem mendeteksi kelebihan karakter dan memunculkan alert "Nama maksimal 100 karakter". | Sesuai ekspektasi. Sistem menahan input setelah karakter ke-100, mencegah huruf tambahan. | **Pass** |
| BVA–REG–04 | Mahasiswa mengetik No. HP berawalan 08 namun kelebihan satu angka (16 digit) | Nama: Rafka Ardiantara; Email: rafka.ardiantara@student.uisi.ac.id; Prodi: Teknik Logistik; No. HP: 0812345678901234; Password/Confirm: kelompok4 | Sistem mendeteksi kelebihan angka, menghentikan proses, dan memunculkan alert "Nomor HP tidak valid". | Sesuai ekspektasi. UI otomatis menahan ketikan pada karakter ke-15 dan memunculkan alert "Nomor HP tidak valid". | **Pass** |

### 2.1.2 Form Pelaporan Item Hilang/Ditemukan

**Kelas Equivalence:**
- **Judul Barang valid:** String 5–255 karakter.
- **Judul Barang invalid:** Kosong, kurang dari 5 karakter, atau melebihi 255 karakter.
- **Deskripsi valid:** String berisi deskripsi lengkap ciri-ciri barang.
- **Deskripsi invalid:** Kosong.
- **Foto Barang valid:** 1–3 foto format JPEG/PNG/JPG, maksimal 2MB per file.
- **Foto Barang invalid:** Kosong, lebih dari 3 file, format tidak didukung, atau melebihi 2MB.
- **Tanggal valid:** Hari ini atau sebelumnya.
- **Tanggal invalid:** Tanggal di masa depan.
- **Waktu/Menit valid:** 00–59.
- **Waktu/Menit invalid:** 60 atau lebih.

| ID | Skenario | Input Data (ringkas) | Ekspektasi Hasil | Hasil Aktual | Status |
|---|---|---|---|---|---|
| EP–ITEM-01 | Melaporkan barang hilang dengan informasi lengkap dan jelas | Dompet Hitam Kulit; Kategori: Aksesoris; lokasi & foto lengkap | Sistem memproses laporan, menyimpan titik lokasi peta, dan menampilkan di beranda barang hilang. | Sesuai ekspektasi. Laporan sukses, foto terunggah, muncul di tab "Barang Hilang". | **Pass** |
| EP–ITEM–02 | Melaporkan barang temuan dengan instruksi penyimpanan | iPhone 15 Pro Max Hitam; Kategori: Elektronik; lokasi penyimpanan Pos Satpam Kampus A | Sistem membedakan tipe laporan sebagai barang temuan dan menampilkan lokasi penyimpanan. | Sesuai ekspektasi. Laporan temuan berhasil dipublikasikan beserta info lokasi penyimpanan. | **Pass** |
| EP–ITEM-03 | Judul Barang dibiarkan kosong saat kirim laporan | Judul Barang: (kosong); Kunci Motor Honda Vario | Sistem mencegah laporan terkirim dan memunculkan peringatan judul wajib diisi. | Sesuai ekspektasi. Muncul alert "Gagal mengirim laporan. Exception. The title field is required". | **Pass** |
| EP–ITEM–04 | Mengirim laporan tanpa unggah foto sama sekali | Kunci Motor Vario; tanpa foto | Sistem menahan pengiriman dan memunculkan peringatan minimal 1 foto wajib. | Sesuai ekspektasi. Aplikasi memunculkan error validasi wajib unggah foto. | **Pass** |
| EP–ITEM-05 | Memaksimalkan kuota lampiran dengan 3 foto sekaligus | Kunci Motor Vario; 3 foto diunggah | Sistem menerima ketiga gambar dan melampirkan ke galeri detail laporan. | Sesuai ekspektasi. Ketiga gambar sukses diunggah. | **Pass** |
| BVA-ITEM-01 | Judul Barang sangat singkat (tepat di bawah 5 karakter, "Topi") | Topi; Kategori: Aksesoris | Sistem memvalidasi panjang minimal dan mencegah submit, muncul alert peringatan. | Sesuai ekspektasi. Muncul alert "Gagal mengirim laporan. Exception, The title must be at least 5 characters." | **Pass** |
| BVA-ITEM-02 | Judul Barang dengan deskripsi panjang hingga pas 255 karakter | Judul Barang panjang mendekati 255 karakter | Sistem mendeteksi kelebihan karakter, menghentikan publikasi, dan memunculkan peringatan batas maksimal. | Sesuai ekspektasi. Laporan tertahan, UI memunculkan peringatan maksimal 255 karakter terpenuhi. | **Pass** |
| BVA-ITEM-03 | Unggah foto kualitas tinggi persis di batas atas maksimal (2049 KB / 2MB) | Tumbler; 1 foto ~2049KB | Sistem memvalidasi batas kapasitas, tidak mengizinkan unggah, dan menampilkan alert peringatan. | **Tidak sesuai ekspektasi.** Foto berhasil terunggah dan diproses tanpa pesan error terkait ukuran. | **Fail** |
| BVA-ITEM-04 | Memilih tanggal di batas atas maksimal hari ini (14 Juni 2026) | Enter Date: 06/15/2026 | Sistem menolak input dan memberikan alert peringatan. | Sesuai ekspektasi. Sistem menolak dan memberi peringatan "Out of range". | **Pass** |
| BVA-ITEM-05 | Memilih menit di batas atas maksimal | Enter Time: 12:61 PM | Sistem menolak input dan memberikan alert peringatan. | Sesuai ekspektasi. Sistem menolak dan memberi peringatan "Enter a valid time". | **Pass** |

### 2.1.3 Form Pengajuan Klaim Barang

**Kelas Equivalence:**
- **Alasan Klaim valid:** String panjang ≥20 karakter.
- **Alasan Klaim invalid:** Kosong atau kurang dari 20 karakter.
- **Format Alasan invalid:** Manipulasi dengan dominasi spasi kosong dan sedikit abjad (tidak dihitung sebagai kalimat sah).
- **Pemilihan Item valid:** Memilih barang yang tersedia di sistem.

| ID | Skenario | Input Data | Ekspektasi Hasil | Hasil Aktual | Status |
|---|---|---|---|---|---|
| EP-CLAIM-01 | Mengklaim barang temuan dengan alasan detail dan rinci | Item: minuman matcha; Alasan: "Saya menemukan matcha ini di perpustakaan sedang di minum si ari." | Sistem memproses pengajuan, mengirim notifikasi ke penemu, status klaim menjadi pending. | Sesuai ekspektasi. Klaim berhasil diajukan, muncul status pending di riwayat. | **Pass** |
| EP-CLAIM-02 | Mengajukan klaim dengan alasan sangat singkat, mengabaikan batas minimal | Item: Kotak Makan Tupperware; Alasan: "Ini loh barang saya" | Sistem menahan pengajuan dan memunculkan peringatan minimal 20 karakter. | Sesuai ekspektasi. Muncul error validasi UI "Alasan klaim minimal 20 karakter". | **Pass** |
| EP-CLAIM-03 | Menekan tombol klaim tanpa mengisi alasan kepemilikan | Item: Kotak Makan Tupperware; Alasan: null | Sistem memblokir pengajuan dan mengingatkan alasan klaim wajib diisi. | Sesuai ekspektasi. Form ditolak dan menampilkan error validasi "Alasan klaim wajib diisi.". | **Pass** |
| EP-CLAIM-04 | Mengisi alasan dengan spasi 19 karakter dan 1 abjad sebagai karakter ke-20 | Item: Karemt Matras Yoga; Alasan: "a" (dominan spasi) | Sistem menolak alasan dan mengingatkan harus jelas dan panjangnya 20 karakter. | Sesuai ekspektasi. Sistem menolak, alert "The reason field must be at least 20 characters". | **Pass** |
| BVA-CLAIM-01 | Alasan sangat mepet, persis menyentuh batas minimal 20 karakter | Item: Jacket Hoodie H&M; Alasan: "Ini hoodie saya, broo" | Sistem memvalidasi batas bawah dengan benar dan meloloskan pengajuan klaim. | Sesuai ekspektasi. Reason 20 karakter diterima tanpa error, klaim berhasil diajukan untuk direview. | **Pass** |
| BVA-CLAIM-02 | Alasan kurang satu spasi/huruf (19 karakter) dari batas minimum | Item: Kunci Mobil Toyota; Alasan: "wih mobil saya nih!" | Sistem mendeteksi kekurangan karakter dan menggagalkan klaim. | Sesuai ekspektasi. UI langsung menolak dengan error "The reason field must be at least 20 characters". | **Pass** |
| BVA-CLAIM-03 | Alasan sedikit lebih panjang dari batas minimal (20 karakter) | Item: Kunci Mobil Toyota; Alasan: "wih kunci saya disini" | Sistem menerima input teks sebagai alasan sah dan memproses pembuatan klaim. | Sesuai ekspektasi. Alasan klaim dengan 21 karakter diterima tanpa kendala validasi panjang. | **Pass** |

### 2.1.4 Ringkasan Hasil Blackbox Testing Fase I

- **Total Test Case:** 30 Test Case (100%)
- **Passed:** 26 Test Case (86,67%)
- **Failed:** 4 Test Case (13,33%)

**Kesimpulan:** Pengujian berbasis input domain secara Blackbox Testing telah diselesaikan dengan mengeksekusi 30 skenario, menghasilkan 26 Pass (86,67%) dan 4 Fail. Berdasarkan evaluasi Equivalence Partitioning (EP), aplikasi FoundIt menunjukkan pertahanan solid dalam memblokir input kosong dan format invalid, terutama pada form Klaim Barang yang lulus 100%, namun masih ada celah integritas data karena gagal mencegah pendaftaran akun dengan nomor HP yang sudah terdaftar. Evaluasi Boundary Value Analysis (BVA) membuktikan sistem sangat baik mencegah input teks melebihi kapasitas maksimal, namun masih lemah dalam sinkronisasi presisi batas ekstrem — terbukti dari ketidakcocokan instruksi batas minimal password antara antarmuka dan backend, serta kelonggaran sistem yang meloloskan unggahan foto melebihi batas maksimal keamanan 2MB.

## 2.2 Model Transisi Status & Skenario End-to-End

Pada Fase 2, dilakukan pengujian berbasis model (Model-Based Testing) untuk memvalidasi alur logika aplikasi, dengan fokus pada fitur **Sistem Klaim Barang** karena siklus perubahan statusnya (state) cukup dinamis dan melibatkan interaksi langsung antara beberapa pengguna sekaligus.

### 2.2.1 State Transition Diagram: Proses Klaim Barang

```
Item Dilaporkan (Status: active)
        │
        ▼
Klaim Dibuat (status: pending)
   ├── Owner Approve ──▶ Klaim Disetujui (status: approved, Item: claimed)
   │                          │
   │                          ▼ (Claimer mengambil barang & status item diupdate)
   │                     Barang Dikembalikan (status: returned)
   │
   └── Owner Reject ───▶ Klaim Ditolak (status: rejected, Item: tetap active)
```

### 2.2.2 Tabel Transisi dan Penjelasan Status

| State Awal | Pemicu Event | Kondisi | State Akhir | Item Status |
|---|---|---|---|---|
| active | Pengguna lain mengajukan klaim | Item harus berstatus active & bukan milik sendiri | pending | active |
| Pending | Pemilik laporan menyetujui klaim | Aksi hanya bisa dilakukan oleh pemilik laporan | approved | claimed |
| Pending | Pemilik laporan menolak klaim | Aksi hanya bisa dilakukan oleh pemilik laporan | rejected | active |
| approved | Pemilik laporan menandai barang sudah dikembalikan | Pemilik menyetujui claimer | returned | returned |

**Penjelasan Status:**
- **Active:** Laporan barang masih tayang secara publik dan tersedia untuk diklaim.
- **Pending:** Klaim kepemilikan baru dibuat, sistem menunggu peninjauan owner.
- **Approved:** Owner menyetujui klaim; item berubah menjadi `claimed`, pengklaim memiliki hak sah mengambil barang.
- **Rejected:** Owner menolak pengajuan; barang kembali berstatus bebas untuk diklaim pengguna lain.
- **Returned:** Garis akhir siklus — barang telah dikembalikan ke pemilik aslinya, kasus selesai.

### 2.2.3 Skenario End-to-End Testing

#### 2.2.3.1 Skenario 1: Alur Sukses Klaim Barang (Happy Path)

Seorang mahasiswa kehilangan jaket kulitnya, mahasiswa lain menemukannya, dan sistem berhasil menjembatani pengembalian barang tersebut tanpa kendala.

| Step | Aktor | Aksi | Expected Result | Actual Result | Status |
|---|---|---|---|---|---|
| 1 | Ragil (A) | Login menggunakan akun mahasiswa (rahmansyah.cahyadi23@student.uisi.ac.id), password kelompok4 | Sistem memberikan akses masuk, diarahkan ke beranda. | Sesuai ekspektasi. | **Pass** |
| 2 | Ragil (A) | Membuat laporan kehilangan Jaket Kulit (deskripsi, lokasi, foto lengkap) | Pesan berhasil, laporan muncul di daftar barang hilang. | Sesuai ekspektasi. Laporan tayang di beranda. | **Pass** |
| 3 | Ari (B) | Login (ari.hinanda23@student.uisi.ac.id, password123) dan membuka halaman utama | Daftar laporan barang pengguna lain ditampilkan lengkap. | Sesuai ekspektasi. | **Pass** |
| 4 | Ari (B) | Membuat laporan barang temuan Jaket Kulit dengan lokasi penyimpanan Pos Satpam Gedung B | Laporan temuan dipublikasikan beserta detail lokasi penyimpanan. | Sesuai ekspektasi. | **Pass** |
| 5 | Ragil (A) | Mencari barang dengan kata kunci "Jaket Kulit" | Sistem memfilter dan menampilkan laporan relevan. | Sesuai ekspektasi, laporan jaket dari Ari muncul. | **Pass** |
| 6 | Ragil (A) | Mengajukan klaim dengan alasan dan bukti kepemilikan detail | Pengajuan diproses, notifikasi ke penemu, status pending. | Sesuai ekspektasi. | **Pass** |
| 7 | Ari (B) | Mengecek notifikasi dan membaca alasan klaim | Kotak dialog menampilkan teks alasan klaim secara utuh. | Sesuai ekspektasi. | **Pass** |
| 8 | Ari (B) | Menekan tombol "Setujui" | Klaim disetujui, barang dikunci untuk orang lain, notifikasi terkirim. | Sesuai ekspektasi. | **Pass** |
| 9 | Ragil (A) | Membuka notifikasi persetujuan untuk melihat kontak Ari, menemui satpam Gedung B, mengambil jaket | Akses nomor WhatsApp penemu terbuka untuk koordinasi. | Sesuai ekspektasi, nomor HP & tombol WhatsApp berfungsi. | **Pass** |
| 10 | Ari (B) | Mengonfirmasi item sudah diambil, menekan tombol konfirmasi di aplikasi | Status barang menjadi returned dan disembunyikan dari beranda publik. | Sesuai ekspektasi. | **Pass** |

#### 2.2.3.2 Skenario 2: Alur Pembatalan di Tengah Jalan (Exception Path)

Menguji fleksibilitas aplikasi ketika mahasiswa telah melaporkan barang hilang, namun ternyata menemukan barangnya sendiri sebelum ada orang lain yang merespons.

| Step | Aktor | Aksi | Expected Result | Actual Result | Status |
|---|---|---|---|---|---|
| 1 | Bayu (A) | Melaporkan kehilangan Kunci Motor Mio di area parkir kampus | Laporan kehilangan berhasil dibuat dan ditayangkan publik. | Sesuai ekspektasi, terlihat oleh pengguna lain. | **Pass** |
| 2 | Bayu (A) | Mengecek aplikasi setelah 2 jam untuk melihat progres | Belum ada klaim/temuan dari orang lain. | Sesuai ekspektasi, riwayat klaim masih kosong. | **Pass** |
| 3 | Bayu (A) | Membuka menu "Item Saya" dan menekan "Hapus" karena kunci ternyata terselip di tasnya sendiri | Sistem meminta konfirmasi lalu menghapus laporan & foto secara permanen. | Sesuai ekspektasi, laporan sukses terhapus. | **Pass** |
| 4 | Ari (B) | Login dan mencoba mencari "Kunci Motor Mio" sesaat setelah laporan dihapus | Laporan tidak muncul di hasil pencarian. | Sesuai ekspektasi, barang tidak tersedia di beranda. | **Pass** |

#### 2.2.3.3 Ringkasan Skenario End-to-End Testing

- **Total Skenario:** 2 skenario
- **Total Steps:** 14 steps
- **Passed Steps:** 14 (100%)
- **Failed Steps:** 0 (0%)
- **State Transition Coverage:** Semua transisi status ter-cover
- **Kesimpulan:** Alur bisnis end-to-end berfungsi sempurna

---

# BAB III — JURNAL EXPLORATORY TESTING

## 3.1 Fokus Pengujian Eksplorasi

1. **Keamanan:** Mencari kerentanan autentikasi, SQL injection, XSS
2. **Performa:** Menguji dengan data ekstrem, multiple requests
3. **Race Condition:** Double-click, concurrent requests
4. **Error Handling:** Network error, timeout, malformed data
5. **Edge Cases:** Karakter spesial, unicode di input field

## 3.2 Tabel Pengujian Eksplorasi

### Keamanan

| ID | Aksi | Expected Result | Actual Result | Status |
|---|---|---|---|---|
| EXP–SEC–01 | Logout untuk menghanguskan token, lalu mencoba akses endpoint `/api/items` dengan header Authorization bearer token lama | Sistem memblokir akses dan mengembalikan HTTP 401 Unauthorized. | Sistem berhasil memblokir dan merespons HTTP 401 Unauthorized - "Unauthenticated.". Sistem aman dari kebocoran data. | **Pass** |
| EXP–SEC–02 | Memasukkan script SQL (`ari.hinanda23@student.uisi.ac.id' OR '1'='1`) pada input email form login | Sistem kebal terhadap injeksi dan menetralisir sql query menjadi teks biasa. | Sesuai ekspektasi. Form email langsung divalidasi, alert "Format email tidak valid". | **Pass** |
| EXP–SEC–03 | Memasukkan script HTML/JavaScript (`<script>alert('XSS')</script>`) pada kolom 'Judul Barang' | Sistem kebal terhadap injeksi XSS dan menetralisir tag script menjadi teks biasa. | Sesuai ekspektasi. Tag script tersimpan namun ditampilkan sebagai plain text tanpa eksekusi alert berkat auto-escape. | **Pass** |
| EXP–SEC–04 | Memasukkan password salah secara brutal dan cepat (10 kali beruntun dalam 30 detik) | Sistem membatasi percobaan (Rate Limiting) dan memunculkan peringatan terlalu banyak percobaan. | Sistem menerima dan memproses seluruh ke-10 percobaan secara terus-menerus tanpa penahanan keamanan. | **Fail (bug)** |

### Performa

| ID | Aksi | Expected Result | Actual Result | Status |
|---|---|---|---|---|
| EXP–PER–01 | Mengunggah 3 foto sekaligus masing-masing berukuran batas atas (total 6MB) | Request berhasil diproses server dalam waktu wajar. | Server merespons dan memproses unggahan berat dalam waktu sekitar 10 detik. | **Pass** |

### Race Condition

| ID | Aksi | Expected Result | Actual Result | Status |
|---|---|---|---|---|
| EXP–RC–01 | Menekan tombol "Submit" form laporan 5 kali dalam 1 detik sebelum layar memuat | Sistem hanya memproses klik pertama dan langsung mengunci (disable) tombol. | Sesuai ekspektasi, button langsung disable saat diklik submit. | **Pass** |

### Error Handling

| ID | Aksi | Expected Result | Actual Result | Status |
|---|---|---|---|---|
| EXP–EH–01 | Submit form laporan, lalu mematikan koneksi WiFi/Data Seluler secara paksa di tengah progress | Aplikasi memberi peringatan gangguan jaringan, proses dibatalkan bersih, data/file tidak disimpan server. | Aplikasi berhasil menangkap error dan memunculkan alert merah ("ClientException: Software caused connection abort"). | **Pass** |
| EXP–EH–02 | Mengirim request API dengan data form valid namun menyabotase header Content-Type menjadi `text/plain` | Sistem API yang ketat mendeteksi ketidakcocokan format dan menolak dengan HTTP 415 Unsupported Media Type. | Sistem mengabaikan manipulasi header, tetap memproses data, mengembalikan HTTP 201 Created. Tidak crash, namun validasi tipe media terlalu longgar. | **Fail (bug)** |

### Edge Cases

| ID | Aksi | Expected Result | Actual Result | Status |
|---|---|---|---|---|
| EXP–EC–01 | Menginput karakter Unicode (mis. "Laptop 龍") lalu mencarinya dengan kata kunci "龍" | Karakter Unicode tersimpan sempurna dan muncul saat dicari. | Karakter tersimpan dengan baik, fitur pencarian berfungsi dengan baik. | **Pass** |

## 3.3 Kesimpulan Pengujian Eksplorasi

- **Total Test Case:** 9 Test Case
- **Passed Steps:** 7 (77,78%)
- **Failed Steps:** 2 (22,22%)

**Kesimpulan:** Dari 9 skenario eksplorasi yang dieksekusi, 7 lulus dan 2 gagal. Kegagalan pertama (EXP–SEC–04) mengidentifikasi tidak adanya mekanisme rate limiting pada endpoint login, yang berpotensi membuka celah serangan brute force. Kegagalan kedua (EXP–EH–02) menunjukkan validasi header Content-Type pada backend API terlalu longgar sehingga tidak sesuai standar REST API yang ketat.

---

# BAB IV — USER ACCEPTANCE TESTING (UAT) MATRIX

Pengujian dilakukan dari sudut pandang Persona Pengguna: Civitas UISI yang sedang kehilangan atau menemukan barang di kampus dengan menggunakan aplikasi FoundIt.

## 4.1 Persona I — Mahasiswa yang Kehilangan Barang di Kampus

**Profile Persona:**
- Nama: Ari Setia Hinanda
- Role: Mahasiswa Semester 6, Informatika
- Usia: 22 tahun
- Kebutuhan: Menemukan TWS merek Redmi Buds 6 miliknya yang hilang di area kampus dengan cepat dan mudah.

| ID UAT | Skenario Bisnis | Langkah Eksekusi (ringkas) | Hasil yang Diharapkan | Hasil Aktual | Keputusan | Catatan Pengguna |
|---|---|---|---|---|---|---|
| UAT-PEMILIK-01 | Masuk ke aplikasi untuk mencari TWS hilang (tanpa registrasi ulang) | Buka FoundIt, login, tekan Login | Akses ke beranda, alert "Login Berhasil" | Sesuai harapan. Login berhasil, beranda termuat dengan baik. | **Pass** | "Bagian login sudah baik dan responsif, tidak ada keluhan." |
| UAT-PEMILIK-02 | Melaporkan TWS hilang secepat mungkin dalam kondisi panik | Isi form Lapor Barang Hilang lengkap (judul, kategori, deskripsi, lokasi, tanggal, foto), kirim laporan | Sistem memproses laporan melalui alur ringkas dan tahan gangguan | Sesuai ekspektasi secara fungsi, namun kolom wajib terlalu banyak dan tidak ada fitur draft otomatis; jika aplikasi tertutup, isian harus diulang dari awal. | **Accept with Notes** | "Saat panik kehilangan barang, mengisi form sepanjang ini melelahkan. Mohon disediakan penyimpanan draft otomatis dan kurangi kolom yang wajib diisi." |
| UAT-PEMILIK-03 | Mengetahui secara proaktif apabila barangnya sudah ditemukan orang lain | Cari "TWS" di tab Barang Temuan, telusuri secara berkala | Sistem memberi tahu pengguna saat ada laporan temuan yang cocok | Tidak sepenuhnya sesuai. Pencarian hanya pasif berdasarkan kata kunci/riwayat aktivitas; tidak ada pencocokan otomatis. | **Accept with Notes** | "Saya harus mengecek sendiri terus-menerus. Idealnya ada notifikasi otomatis 'barang Anda mungkin ditemukan' ketika ada laporan temuan yang mirip." |
| UAT-PEMILIK-04 | Mengajukan klaim kepemilikan disertai bukti ciri barang | Buka detail laporan temuan, ajukan klaim dengan alasan ≥20 karakter | Klaim terkirim dengan status pending dan tercatat di riwayat | Sesuai ekspektasi. Klaim berhasil diajukan, validasi minimal 20 karakter berjalan benar. | **Pass** | "Mekanisme klaim berbasis bukti tertulis sudah tepat dan meyakinkan." |
| UAT-PEMILIK-05 | Mengetahui hasil persetujuan klaim secara langsung tanpa harus menunggu | Tunggu keputusan penemu, buka aplikasi periksa status, lihat lokasi & kontak WhatsApp | Pengguna menerima push notification begitu klaim disetujui | Sesuai ekspektasi secara fungsi (lokasi & WhatsApp berfungsi), namun tidak ada push notification; persetujuan baru diketahui setelah membuka aplikasi sendiri. | **Accept with Notes** | "Untungnya kontak WhatsApp berfungsi. Tapi saya tidak tahu klaim sudah disetujui sampai iseng membuka aplikasi. Notifikasi langsung ke HP sangat dibutuhkan." |

## 4.2 Persona II — Mahasiswa yang Menemukan Barang

**Profile Persona:**
- Nama: Rahmansyah Ragil Cahyadi
- Role: Mahasiswa Semester 6, Informatika
- Usia: 22 tahun
- Kebutuhan: Ingin membantu orang yang kehilangan barang

| ID UAT | Skenario Bisnis | Langkah Eksekusi (ringkas) | Hasil yang Diharapkan | Hasil Aktual | Keputusan | Catatan Pengguna |
|---|---|---|---|---|---|---|
| UAT-PENEMU-01 | Masuk ke aplikasi untuk melaporkan barang yang ditemukan | Login dengan akun terdaftar | Akses ke beranda, alert "Login Berhasil" | Sesuai harapan. Login berhasil, beranda termuat baik. | **Pass** | Tidak ada kendala pada proses Login. |
| UAT-PENEMU-02 | Melaporkan barang temuan dan mendokumentasikan dengan foto kamera | Isi form Barang Temuan (Redmi Buds 6, lokasi, foto langsung dari kamera 2999KB), kirim laporan | Sistem menerima laporan dan menerima foto sesuai batas (maks 2MB) | Sesuai ekspektasi secara fungsi (laporan terbit), namun sistem menerima foto melebihi batas 2MB tanpa peringatan/kompresi (konsisten dengan BVA–ITEM–03). | **Accept with Notes** | "Foto langsung dari kamera HP saya berukuran besar, tapi diterima begitu saja tanpa peringatan. Sebaiknya ada kompresi otomatis agar aplikasi tetap ringan." |
| UAT-PENEMU-03 | Memverifikasi keaslian pengklaim sebelum menyetujui | Buka MyItem, lihat klaim masuk, baca alasan, hubungi pengeklaim | Sistem menyediakan cukup informasi (riwayat, identitas, bukti foto) untuk menilai keabsahan klaim | Tidak sepenuhnya sesuai. Penemu hanya bisa menilai dari teks alasan klaim; tidak ada profil ringkas, riwayat, atau bukti foto pendukung dari pengklaim. | **Accept with Notes** | "Saya hanya berpegang pada teks alasannya dan nomor pengeklaim. Kalau ada dua orang yang sama-sama meyakinkan, saya bingung menentukan. Tolong tambahkan bukti foto dari pengklaim." |
| UAT-PENEMU-04 | Menolak klaim yang tidak meyakinkan dari pihak lain | Lihat klaim masuk, tolak dengan alasan "Bukti nya kurang jelas lebih ke bercanda" | Klaim ditolak (rejected) dan barang tetap berstatus active | Sesuai ekspektasi. Klaim ditolak dengan benar, barang tetap tersedia untuk diklaim pemilik sah. | **Pass** | "Fungsi tolak bekerja baik dan penting untuk mencegah salah menyerahkan barang ke orang yang bukan pemilik barang." |
| UAT-PENEMU-05 | Menyetujui pemilik sah dan menutup kasus setelah barang diambil | Setujui klaim Ari Setia Hinanda, hubungi via WhatsApp, tandai "Sudah Dikembalikan" | Klaim disetujui, status menjadi returned, laporan diarsipkan dari beranda publik | Sesuai ekspektasi. Klaim disetujui dan setelah konfirmasi laporan berubah status "Dikembalikan" serta hilang dari beranda. | **Pass** | "Penutupan kasus sudah jelas, walau konfirmasi hanya bergantung pada kejujuran satu pihak (penemu) saja." |

## 4.3 Persona III — Petugas Keamanan/Satpam Kampus

**Profile Persona:**
- Nama: Pak Hendra
- Role: Satpam Gedung B
- Usia: 45 tahun
- Kebutuhan: Mudah membantu mahasiswa cari barang yang hilang dengan titipan dari penemu barang.

| ID UAT | Skenario Bisnis | Langkah Eksekusi (ringkas) | Hasil yang Diharapkan | Hasil Aktual | Keputusan | Catatan Pengguna |
|---|---|---|---|---|---|---|
| UAT–SATPAM–01 | Mendaftarkan akun petugas mengikuti petunjuk di layar | Daftar dengan email @uisi.ac.id dan password 6 karakter sesuai petunjuk layar | Sistem menerima password sesuai petunjuk dan menyelesaikan registrasi | Register berhasil, email @uisi.ac.id diterima (bukan hanya @student.uisi.ac.id) | **Pass** | Domain staff sudah disupport. |
| UAT–SATPAM–02 | Mendata barang titipan beserta jejak siapa yang menitipkan | Tambah laporan Barang Temuan mewakili mahasiswa, isi lokasi penyimpanan, kirim | Sistem mencatat laporan beserta identitas penyerah barang (chain of custody) | Sesuai sebagian. Laporan berhasil dibuat, namun tidak ada kolom identitas penitip; seluruh barang tercatat atas nama akun Hendra sehingga jejak penyerah barang hilang. | **Accept with Notes** | "Catatannya jadi rancu, seolah semua barang saya sendiri yang menemukan. Perlu kolom 'nama penitip' agar pertanggungjawaban barang jelas." |
| UAT–SATPAM–03 | Memantau seluruh barang tersimpan di Pos Satpam Kampus B secara terpusat | Cari menu/filter berdasarkan lokasi penyimpanan, telusuri daftar barang | Sistem menyediakan filter/dasbor berdasarkan lokasi penyimpanan | **Tidak sesuai (gagal).** Aplikasi hanya menyediakan filter kategori, tipe, dan kata kunci; tidak ada filter/dasbor berdasarkan lokasi penyimpanan. | **Fail (Ditolak)** | "Fitur ini krusial bagi petugas tetapi tidak tersedia. Saya tidak bisa tahu ada berapa barang di pos saya tanpa mengecek manual. Mohon dibuatkan dasbor khusus petugas." |
| UAT–SATPAM–04 | Memastikan barang hanya diserahkan kepada pengklaim yang sah | Pengklaim datang, tunjukkan status klaim di layar HP, cocokkan nama dengan KTM | Sistem menyediakan bukti verifikasi yang sulit dipalsukan (kode pengambilan unik/QR) | Tidak sesuai harapan keamanan. Verifikasi hanya mengandalkan tampilan layar HP (dapat direkayasa); tidak ada kode/QR pengambilan resmi. | **Accept with Notes** | "Saya hanya bisa percaya pada layar HP yang ditunjukkan. Untuk barang berharga, ini berisiko. Tolong adakan kode/QR pengambilan resmi." |
| UAT–SATPAM–05 | Berkoordinasi langsung dengan pemilik/penemu terkait barang titipan di posnya | Cari kanal kontak dalam sistem untuk konfirmasi penyerahan | Sistem memberi petugas akses kontak pihak terkait dalam satu transaksi barang titipan | Tidak sesuai. Kontak WhatsApp hanya terbuka antara pemilik dan penemu; satpam sebagai pihak ketiga penyimpan barang tidak memperoleh akses koordinasi apa pun. | **Accept with Notes** | "Saya yang menyimpan barangnya, tetapi justru tidak punya jalur koordinasi di aplikasi. Peran satpam seperti dilupakan dalam alur ini." |

## 4.4 Kesimpulan dan Ringkasan Acceptance Testing (UAT)

| Persona | Total | Pass | Accept With Notes | Fail |
|---|---|---|---|---|
| Ari Setia Hinanda (Pelapor) | 5 | 2 | 3 | 0 |
| Rahmansyah Ragil Cahyadi (Penemu) | 5 | 3 | 2 | 0 |
| Pak Hendra (Satpam) | 5 | 1 | 3 | 1 |
| **Total** | **15 (100%)** | **5 (33,33%)** | **9 (60,00%)** | **1 (6,67%)** |

**Tingkat Penerimaan:** Jika Pass dan Accept with Notes digabungkan sebagai "diterima", maka 14 dari 15 skenario (93,33%) diterima dan 1 skenario (6,67%) ditolak. Namun, angka penerimaan ini perlu dibaca secara kritis: hanya 5 skenario (33,33%) yang lolos tanpa catatan, sementara mayoritas (60%) diterima dengan catatan perbaikan. Artinya, aplikasi FoundIt sudah fungsional namun belum matang dari sisi pengalaman pengguna dan kelengkapan peran.

**Kesimpulan:** Inti bisnis lost & found terbukti berjalan — siklus dari pelaporan, klaim, persetujuan/penolakan, koordinasi via WhatsApp, hingga penutupan kasus dapat diselesaikan oleh ketiga persona.

---

# BAB V — DEFECT REPORT (LAPORAN BUG)

## 5.1 Pendahuluan dan Klasifikasi Keparahan

Bab ini mengonsolidasikan seluruh cacat (defect) teknis maupun fungsional yang ditemukan selama empat fase pengujian — Blackbox EP/BVA (Fase 1), Model-Based/End-to-End (Fase 2), Exploratory (Fase 3), dan User Acceptance Testing (Fase 4). Setiap defect ditelusuri kembali (traceability) ke ID skenario uji asalnya agar dapat diverifikasi ulang oleh tim pengembang.

**Kriteria Tingkat Keparahan (Severity):**

| Tingkat | Definisi |
|---|---|
| Critical | Membuat aplikasi crash, data hilang, atau transaksi gagal total (Blocker). |
| Major | Fungsi berjalan tetapi menghasilkan kalkulasi/keluaran yang salah atau melanggar hak akses/aturan keamanan. |
| Minor | Kesalahan kosmetik, tulisan (typo), tata letak UI rusak, atau eror minor yang tidak mengganggu fungsi utama. |

## 5.2 Tabel Laporan Defect (Bug)

| ID Bug | Nama Bug & Fitur | Tingkat Keparahan | Langkah Mereproduksi Bug |
|---|---|---|---|
| BUG–01 | Nomor HP Duplikat Lolos Validasi Keunikan: Modul Registrasi (Ref: EP–REG–08) | **Major** | 1. Buka halaman Registrasi. 2. Daftarkan akun pertama dengan No. HP 0812345679 hingga berhasil. 3. Mulai registrasi akun kedua memakai email berbeda namun No. HP sama persis. 4. Tekan "Daftar". 5. Hasilnya sistem tetap membuat akun kedua — seharusnya muncul alert "No. HP sudah terdaftar" dan pendaftaran ditolak. |
| BUG–02 | Validasi Panjang Nomor HP Format +62 tidak memenuhi batas: Modul Registrasi (Ref: EP–REG–09) | **Minor** | 1. Buka halaman Registrasi. 2. Isi seluruh data valid. 3. Isi No. HP format internasional terlalu pendek "+628123456". 4. Lengkapi password valid. 5. Tekan "Daftar". 6. Hasilnya registrasi berhasil — seharusnya sistem menormalkan nomor atau menolaknya karena panjang digit kurang. |
| BUG–03 | Inkonsistensi Instruksi Panjang Password (UI "Min. 6" vs Backend "Min. 8"): Modul Registrasi (Ref: BVA–REG–01) | **Minor** | 1. Buka halaman Registrasi. 2. Isi seluruh data valid. 3. Buat password 6 karakter mengikuti petunjuk layar "Minimal 6 karakter" (mis. "tes123"). 4. Tekan "Daftar". 5. Hasilnya pendaftaran ditolak karena backend mensyaratkan minimal 8 karakter — teks petunjuk di UI menyesatkan pengguna. |
| BUG–04 | Batas Ukuran Foto 2MB Tidak Diterapkan (Upload Bypass): Modul Pelaporan Item/Unggah Foto (Ref: BVA–ITEM–03) | **Major** | 1. Login ke aplikasi. 2. Buat laporan barang baru. 3. Pada unggah foto, pilih gambar melebihi 2MB (mis. 2999 KB). 4. Tekan "Kirim Laporan". 5. Hasilnya foto berhasil terunggah dan laporan tersimpan tanpa peringatan ukuran — berisiko membebani penyimpanan server. |
| BUG–05 | Tidak Ada Pembatasan Percobaan Login/Rate Limiting (Celah Brute Force): Modul Autentikasi (Ref: EXP–SEC–04) | **Major** | 1. Buka halaman Login. 2. Masukkan email terdaftar dengan password salah. 3. Ulangi percobaan login gagal 10 kali beruntun dalam waktu <30 detik. 4. Hasilnya seluruh percobaan tetap diproses tanpa penguncian sementara maupun peringatan "terlalu banyak percobaan" — endpoint login rentan serangan brute force. |
| BUG–06 | Validasi Header Content-Type API tidak mempunyai validasi: Backend API (Ref: EXP–EH–02) | **Minor** | 1. Susun request API pembuatan item dengan body valid (mis. via Postman). 2. Ubah header Content-Type menjadi text/plain. 3. Kirim request. 4. Hasilnya server tetap memproses data dan membalas HTTP 201 Created — seharusnya mengembalikan HTTP 415 Unsupported Media Type sesuai standar REST API. |

## 5.3 Rekapitulasi Tingkat Keparahan

| Tingkat Keparahan | Jumlah | Persentase | ID Bug |
|---|---|---|---|
| Critical | 0 | 0% | - |
| Major | 3 | 50% | BUG–01, BUG–04, BUG–05 |
| Minor | 3 | 50% | BUG–02, BUG–03, BUG–06 |
| **Total Defect** | **6** | **100%** | |

**Analisis:** Sepanjang empat fase pengujian, tidak ditemukan satu pun defect berstatus Critical. Artinya, aplikasi FoundIt tergolong stabil — tidak ada crash, kehilangan data, maupun kegagalan transaksi total yang bersifat blocker. Meski demikian, terdapat 3 defect Major yang menuntut perbaikan prioritas tinggi karena menyentuh ranah keamanan dan integritas data: kerentanan brute force akibat ketiadaan rate limiting (BUG–05), pelolosan unggahan foto melebihi batas 2MB (BUG–04), dan duplikasi nomor HP yang merusak keunikan identitas akun (BUG–01). Tiga defect Minor sisanya bersifat inkonsistensi validasi dan instruksi yang berdampak pada kualitas data serta kenyamanan pengguna.

## 5.4 Kesimpulan dan Prioritas Perbaikan

Secara keseluruhan, FoundIt adalah aplikasi yang fungsional dan stabil pada jalur normal (tanpa defect Critical), namun belum siap dirilis ke produksi sebelum defect Major di ranah keamanan dan integritas data diperbaiki. Berikut urutan prioritas penanganan yang direkomendasikan:

1. **Prioritas P1 — Wajib sebelum rilis produksi (Keamanan & Integritas):**
   - BUG–05 (terapkan rate limiting pada login)
   - BUG–04 (tegakkan validasi ukuran foto 2MB di sisi server)
   - BUG–01 (tegakkan constraint keunikan nomor HP)
2. **Prioritas P2 — Perbaikan validasi & konsistensi:**
   - BUG–03 (selaraskan teks petunjuk password menjadi "Minimal 8 karakter")
   - BUG–02 (normalisasi/validasi nomor format +62)
   - BUG–06 (perketat validasi header Content-Type menjadi HTTP 415)

---

# BAB VI — BERITA ACARA UAT SIGN-OFF

Pada hari ini, Senin tanggal 15 Juni 2026, bertempat di Universitas Internasional Semen Indonesia (UISI) Gresik, kami yang bertanda tangan di bawah ini selaku Kelompok Penguji (Kelompok 4) yang bertindak sebagai perwakilan pengguna (user representative), telah menyelesaikan rangkaian pengujian User Acceptance Testing beserta seluruh fase pengujian pendukungnya terhadap aplikasi FoundIt — Lost & Found Management System. Berita acara ini disusun sebagai pernyataan resmi atas hasil dan keputusan kelayakan rilis aplikasi.

## 6.1 Ringkasan Hasil Pengujian

| Fase | Jenis Pengujian | Hasil Utama |
|---|---|---|
| Fase 1 | Blackbox Testing (EP & BVA) | 30 test case: 26 Pass (86,67%), 4 Fail |
| Fase 2 | Model-Based / End-to-End Testing | 14 langkah: 14 Pass (100%), seluruh transisi status ter-cover |
| Fase 3 | Exploratory Testing | 9 test case: 7 Pass (77,78%), 2 Fail |
| Fase 4 | User Acceptance Testing | 15 skenario: 14 diterima (93,33%), 1 ditolak (6,67%) |
| Defect | Defect Report | 6 defect: 0 Critical, 3 Major, 3 Minor |

## 6.2 Pernyataan Kelayakan (Sign-Off Decision)

Berdasarkan seluruh pertimbangan di atas, Kelompok Penguji selaku perwakilan pengguna dengan ini menyatakan bahwa aplikasi FoundIt v1.0.0:

| Status | Keputusan |
|---|---|
| Layak Rilis (Approved For Release) | |
| **Layak Rilis Dengan Perbaikan (Approved With Conditions)** | **✓** |
| Tidak Layak Rilis (Rejected) | |

Demikian berita acara ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.

**Gresik, 15 Juni 2026**

**Mengetahui**

| Dosen Pengampu | Penguji 1 | Penguji 2 | Penguji 3 |
|---|---|---|---|
| Taufiqotul Bariyah, S.Kom., M.IM., MCE | Ari Setia Hinanda | Bayu Nurcahyo | Rahmansyah Ragil Cahyadi |
