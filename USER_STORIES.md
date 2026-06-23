# 📖 User Stories — FoundIt
## Platform Lost & Found UISI

**Proyek:** FoundIt — Platform Pelaporan dan Pengembalian Barang Hilang  
**Versi:** v1.0.0  
**Tim:** Kelompok 1 — Muhammad Rafli Afriansyah Ikhsan, Muhammad Muqoffin Nuha, Sustri Elina Simamora  

---

## Daftar Isi

1. [Autentikasi](#1-autentikasi)
2. [Laporan Barang](#2-laporan-barang)
3. [Pencarian & Filter](#3-pencarian--filter)
4. [Sistem Klaim](#4-sistem-klaim)
5. [Profil Pengguna](#5-profil-pengguna)
6. [Riwayat Aktivitas](#6-riwayat-aktivitas)
7. [Leaderboard](#7-leaderboard)

---

## Konvensi Penulisan

```
US-[nomor]
Sebagai [aktor],
Saya ingin [aksi/fitur],
Agar [manfaat/tujuan].

Kriteria Penerimaan:
- [kondisi yang harus terpenuhi]

Catatan Teknis:
- [detail implementasi relevan]
```

**Aktor yang terlibat:**
- **Pengguna (User)** — civitas akademika UISI yang sudah login
- **Pelapor** — user yang membuat laporan barang hilang atau temuan
- **Pengklaim** — user yang mengajukan klaim atas suatu barang
- **Sistem** — backend Laravel API

---

## 1. Autentikasi

### US-01 — Registrasi Akun

```
Sebagai civitas akademika UISI yang belum memiliki akun,
Saya ingin mendaftarkan diri dengan email institusi UISI,
Agar saya dapat mengakses fitur pelaporan dan pencarian barang.
```

**Kriteria Penerimaan:**
- [ ] Form registrasi meminta: nama lengkap, email, password, konfirmasi password, nomor HP, prodi/unit
- [ ] Email hanya boleh menggunakan domain `@student.uisi.ac.id` atau `@uisi.ac.id`; domain lain ditolak dengan pesan error yang jelas
- [ ] Password minimal 8 karakter
- [ ] Konfirmasi password harus cocok dengan password
- [ ] Jika email sudah terdaftar, sistem menampilkan pesan "Email sudah terdaftar"
- [ ] Setelah berhasil, sistem mengembalikan token autentikasi (Bearer token) dan data user
- [ ] User langsung masuk ke aplikasi tanpa perlu login ulang

**Catatan Teknis:**
- `POST /api/register`
- Validasi regex email: `^[a-zA-Z0-9._%+-]+@(student\.uisi\.ac\.id|uisi\.ac\.id)$`

---

### US-02 — Login

```
Sebagai pengguna terdaftar,
Saya ingin masuk ke aplikasi dengan email dan password,
Agar saya dapat menggunakan seluruh fitur FoundIt.
```

**Kriteria Penerimaan:**
- [ ] Form login meminta email dan password
- [ ] Jika email atau password salah, sistem menampilkan pesan "Email atau password salah"
- [ ] Setelah berhasil, sistem mengembalikan Bearer token untuk autentikasi request selanjutnya
- [ ] Token tersimpan di perangkat untuk sesi berikutnya

**Catatan Teknis:**
- `POST /api/login`
- Semua endpoint selain register & login memerlukan header `Authorization: Bearer {token}`

---

### US-03 — Logout

```
Sebagai pengguna yang sedang login,
Saya ingin keluar dari aplikasi,
Agar sesi saya berakhir dengan aman dan token tidak dapat disalahgunakan.
```

**Kriteria Penerimaan:**
- [ ] Tersedia tombol logout yang mudah ditemukan
- [ ] Setelah logout, token yang digunakan dihapus dari sistem (tidak bisa dipakai ulang)
- [ ] User diarahkan kembali ke halaman login
- [ ] Data sesi lokal di perangkat dibersihkan

**Catatan Teknis:**
- `POST /api/logout` (requires auth)

---

## 2. Laporan Barang

### US-04 — Melaporkan Barang Hilang

```
Sebagai pengguna yang kehilangan barang di lingkungan kampus UISI,
Saya ingin membuat laporan barang hilang dengan detail lengkap,
Agar orang yang menemukannya dapat menghubungi saya untuk pengembalian.
```

**Kriteria Penerimaan:**
- [ ] Form laporan meminta: judul barang (min. 5 karakter), kategori, deskripsi, lokasi terakhir terlihat, tanggal & waktu hilang
- [ ] User dapat memilih lokasi secara presisi menggunakan Google Maps (pin lokasi)
- [ ] User dapat menambahkan detail lokasi kustom (contoh: "Ruang CM201")
- [ ] User dapat mengupload 1–3 foto barang (format JPEG/PNG, maks. 2MB per foto)
- [ ] Foto wajib ada minimal 1 — laporan tanpa foto tidak dapat disimpan; muncul pesan **"Tambahkan minimal 1 foto barang"**
- [ ] Setelah berhasil, laporan muncul di halaman Home dengan badge "Hilang"
- [ ] Status awal laporan adalah `active`

**Catatan Teknis:**
- `POST /api/items` dengan `type: lost`
- Field `latitude` & `longitude` diisi dari Google Maps picker
- Upload foto via `multipart/form-data`, field `photos[]`

---

### US-05 — Melaporkan Barang Temuan

```
Sebagai pengguna yang menemukan barang milik orang lain di kampus UISI,
Saya ingin membuat laporan barang temuan,
Agar pemilik barang dapat menemukannya dan mengajukan klaim.
```

**Kriteria Penerimaan:**
- [ ] Form laporan meminta: judul barang, kategori, deskripsi, lokasi ditemukan, tanggal & waktu ditemukan
- [ ] User dapat mengisi lokasi penyimpanan barang (contoh: "Satpam Gedung A", "Di saya")
- [ ] User dapat memilih lokasi via Google Maps dan menambahkan detail lokasi kustom
- [ ] User dapat mengupload 1–3 foto barang
- [ ] Foto wajib ada minimal 1 — laporan tanpa foto tidak dapat disimpan; muncul pesan **"Tambahkan minimal 1 foto barang"**
- [ ] Setelah berhasil, laporan muncul di Home dengan badge "Temuan"

**Catatan Teknis:**
- `POST /api/items` dengan `type: found`
- Field `storage_info` untuk lokasi penyimpanan

---

### US-06 — Melihat Detail Barang

```
Sebagai pengguna,
Saya ingin melihat detail lengkap suatu laporan barang,
Agar saya dapat memutuskan apakah barang tersebut milik saya atau cocok dengan yang saya cari.
```

**Kriteria Penerimaan:**
- [ ] Halaman detail menampilkan: judul, kategori, deskripsi, lokasi, tanggal & waktu, info penyimpanan (jika ada)
- [ ] Semua foto barang dapat dilihat; ketuk foto untuk tampilan fullscreen dengan fitur zoom (pinch-to-zoom)
- [ ] Informasi pelapor ditampilkan: nama, prodi/unit, foto profil
- [ ] Tersedia tombol untuk menghubungi pelapor via WhatsApp (jika nomor HP tersedia)
- [ ] Pemilik laporan melihat tombol Edit dan Hapus
- [ ] User lain melihat tombol **"Saya Menemukan Barang Ini!"** (untuk item `lost`) atau **"Ini Barang Saya!"** (untuk item `found`)

**Catatan Teknis:**
- `GET /api/items/{id}`

---

### US-07 — Mengedit Laporan Sendiri

```
Sebagai pelapor,
Saya ingin mengedit laporan yang sudah saya buat,
Agar informasi yang tersedia selalu akurat dan terkini.
```

**Kriteria Penerimaan:**
- [ ] Hanya pemilik laporan yang dapat mengakses menu edit; user lain tidak dapat mengedit
- [ ] Semua field yang tersedia saat pembuatan dapat diubah
- [ ] User dapat menambah foto baru (selama total foto belum mencapai 3)
- [ ] User dapat menghapus foto yang sudah ada
- [ ] Setelah simpan, perubahan langsung tampil di halaman detail

**Catatan Teknis:**
- `PUT /api/items/{id}` — hanya bisa dilakukan oleh `user_id` yang sama
- `POST /api/items/{id}/photos` — tambah foto
- `DELETE /api/items/{id}/photos/{photoId}` — hapus foto

---

### US-08 — Menghapus Laporan Sendiri

```
Sebagai pelapor,
Saya ingin menghapus laporan barang yang saya buat,
Agar laporan yang sudah tidak relevan tidak membingungkan pengguna lain.
```

**Kriteria Penerimaan:**
- [ ] Hanya pemilik laporan yang dapat menghapus; user lain tidak bisa
- [ ] Sistem menampilkan dialog konfirmasi sebelum menghapus
- [ ] Setelah dihapus, semua foto terkait juga terhapus dari storage
- [ ] Laporan tidak muncul lagi di daftar Home setelah dihapus
- [ ] User diarahkan kembali ke halaman Home atau MyItem

**Catatan Teknis:**
- `DELETE /api/items/{id}`

---

### US-09 — Menandai Barang Dikembalikan

```
Sebagai pelapor atau pengklaim yang sudah melakukan serah terima barang,
Saya ingin menandai barang sebagai "Dikembalikan",
Agar status laporan terupdate dan sistem mencatat pengembalian barang tersebut.
```

**Kriteria Penerimaan:**
- [ ] Untuk barang **temuan (found)**: hanya pemilik laporan yang dapat menandai sebagai dikembalikan
- [ ] Untuk barang **hilang (lost)**: hanya pengklaim yang klaimnya sudah disetujui (approved) yang dapat menandai
- [ ] User yang tidak memiliki hak mendapat pesan error 403
- [ ] Setelah ditandai, status item berubah menjadi `returned` dan tidak muncul di daftar aktif
- [ ] Poin kontribusi pelapor/pengklaim bertambah di leaderboard

**Catatan Teknis:**
- `PUT /api/items/{id}/status` dengan body `status: returned`

---

### US-10 — Melihat Laporan Milik Sendiri

```
Sebagai pelapor,
Saya ingin melihat semua laporan yang pernah saya buat,
Agar saya dapat memantau status setiap laporan dan klaim yang masuk.
```

**Kriteria Penerimaan:**
- [ ] Daftar menampilkan semua item milik user (aktif, diklaim, maupun dikembalikan)
- [ ] Setiap item menampilkan jumlah klaim yang masuk
- [ ] User dapat mengakses detail dan melakukan tindakan (edit, hapus, review klaim) dari halaman ini

**Catatan Teknis:**
- `GET /api/items/my`

---

## 3. Pencarian & Filter

### US-11 — Melihat Daftar Semua Laporan

```
Sebagai pengguna,
Saya ingin melihat daftar semua laporan barang yang aktif,
Agar saya dapat menemukan barang yang hilang atau menemukan pemilik barang temuan.
```

**Kriteria Penerimaan:**
- [ ] Daftar menampilkan semua item dengan status selain `returned`
- [ ] Setiap card menampilkan: foto, judul, kategori, lokasi, tanggal, badge tipe (Hilang/Temuan)
- [ ] Daftar diurutkan dari yang terbaru (created_at descending)
- [ ] Fitur pull-to-refresh tersedia untuk memperbarui data

**Catatan Teknis:**
- `GET /api/items`

---

### US-12 — Filter Berdasarkan Tipe

```
Sebagai pengguna,
Saya ingin memfilter daftar berdasarkan tipe laporan (Hilang atau Temuan),
Agar saya hanya melihat informasi yang relevan dengan kebutuhan saya.
```

**Kriteria Penerimaan:**
- [ ] Tersedia tab atau tombol filter untuk "Semua", "Hilang", dan "Temuan"
- [ ] Setelah memilih filter "Hilang", hanya item bertipe `lost` yang tampil; tidak ada item `found`
- [ ] Setelah memilih filter "Temuan", hanya item bertipe `found` yang tampil; tidak ada item `lost`
- [ ] Perubahan filter langsung memperbarui daftar tanpa reload halaman penuh

**Catatan Teknis:**
- `GET /api/items?type=lost` atau `GET /api/items?type=found`

---

### US-13 — Filter Berdasarkan Kategori

```
Sebagai pengguna,
Saya ingin memfilter daftar barang berdasarkan kategori,
Agar pencarian lebih spesifik dan efisien.
```

**Kriteria Penerimaan:**
- [ ] Tersedia daftar kategori yang bisa dipilih: Elektronik, Dokumen, Aksesoris, Tas & Dompet, Kunci, Pakaian, Lainnya
- [ ] Setelah memilih kategori, hanya item dengan kategori tersebut yang tampil
- [ ] Filter kategori dapat dikombinasikan dengan filter tipe (lost/found)

**Catatan Teknis:**
- `GET /api/items?category_id={id}`
- `GET /api/categories` untuk mengambil daftar kategori

---

### US-14 — Pencarian Berdasarkan Kata Kunci

```
Sebagai pengguna,
Saya ingin mencari barang menggunakan kata kunci,
Agar saya dapat menemukan laporan yang spesifik tanpa harus scroll seluruh daftar.
```

**Kriteria Penerimaan:**
- [ ] Tersedia field pencarian (search bar) di halaman Home
- [ ] Pencarian dilakukan terhadap judul, deskripsi, dan lokasi barang
- [ ] Pencarian bersifat case-insensitive (huruf besar/kecil tidak berpengaruh)
- [ ] Hasil pencarian diperbarui secara langsung atau setelah user menekan enter/tombol cari
- [ ] Jika tidak ada hasil, tampil pesan kosong yang informatif

**Catatan Teknis:**
- `GET /api/items?search={keyword}`
- Query menggunakan `LOWER(field) LIKE '%keyword%'`

---

## 4. Sistem Klaim

### US-15 — Mengajukan Klaim Barang

```
Sebagai pengguna yang merasa barang temuan yang dilaporkan adalah miliknya,
Saya ingin mengajukan klaim dengan menyertakan alasan,
Agar pemilik laporan dapat memverifikasi dan mengembalikan barang tersebut.
```

**Kriteria Penerimaan:**
- [ ] Tombol klaim tersedia di halaman detail item; tidak tersedia untuk laporan milik sendiri
- [ ] Form klaim meminta alasan klaim dengan minimal 20 karakter
- [ ] Jika alasan kurang dari 20 karakter, sistem menampilkan pesan validasi dan klaim tidak terkirim
- [ ] User hanya dapat memiliki satu klaim `pending` atau `approved` per item
- [ ] Item yang sudah berstatus `claimed` atau `returned` tidak dapat diklaim ulang
- [ ] Setelah berhasil, status tombol berubah menjadi **"Menunggu Review"** yang dapat diklik untuk navigasi ke riwayat klaim
- [ ] Jika klaim sebelumnya ditolak (`rejected`), user **dapat mengajukan klaim baru lagi** pada item yang sama

**Catatan Teknis:**
- `POST /api/claims` dengan body `item_id` dan `reason`

---

### US-16 — Melihat Daftar Klaim Masuk

```
Sebagai pelapor,
Saya ingin melihat semua klaim yang masuk untuk laporan saya,
Agar saya dapat meninjau dan memutuskan klaim mana yang sah.
```

**Kriteria Penerimaan:**
- [ ] Hanya pemilik laporan yang dapat melihat daftar klaim masuk
- [ ] Setiap klaim menampilkan: nama pengklaim, prodi/unit, nomor HP, alasan klaim, waktu pengajuan, status
- [ ] Tersedia tombol "Setujui" dan "Tolak" untuk setiap klaim berstatus `pending`

**Catatan Teknis:**
- `GET /api/items/{id}/claims` — hanya bisa diakses oleh pemilik item

---

### US-17 — Menyetujui Klaim

```
Sebagai pelapor yang sudah memverifikasi pengklaim,
Saya ingin menyetujui klaim yang sah,
Agar pengklaim dapat mengambil barang dan proses pengembalian dapat dilanjutkan.
```

**Kriteria Penerimaan:**
- [ ] Hanya pemilik laporan yang dapat menyetujui klaim
- [ ] Hanya klaim berstatus `pending` yang dapat disetujui
- [ ] Setelah disetujui: status klaim berubah menjadi `approved`, status item berubah menjadi `claimed`
- [ ] Semua klaim `pending` lainnya pada item yang sama otomatis ditolak
- [ ] Tombol/ikon WhatsApp muncul untuk menghubungi pengklaim

**Catatan Teknis:**
- `PUT /api/claims/{id}/approve`

---

### US-18 — Menolak Klaim

```
Sebagai pelapor yang menilai klaim tidak sah,
Saya ingin menolak klaim dengan menyertakan alasan penolakan,
Agar pengklaim mengetahui mengapa klaimnya tidak diterima.
```

**Kriteria Penerimaan:**
- [ ] Hanya pemilik laporan yang dapat menolak klaim
- [ ] Hanya klaim berstatus `pending` yang dapat ditolak
- [ ] Form penolakan meminta alasan penolakan (min. 5 karakter)
- [ ] Setelah ditolak: status klaim berubah menjadi `rejected`; status item tetap `active`
- [ ] Alasan penolakan tersimpan dan dapat dilihat oleh pengklaim

**Catatan Teknis:**
- `PUT /api/claims/{id}/reject` dengan body `reason`

---

### US-19 — Melihat Riwayat Klaim Saya

```
Sebagai pengklaim,
Saya ingin melihat semua klaim yang pernah saya ajukan beserta statusnya,
Agar saya dapat memantau perkembangan setiap klaim yang sedang berjalan.
```

**Kriteria Penerimaan:**
- [ ] Daftar menampilkan semua klaim yang diajukan oleh user yang sedang login
- [ ] Setiap klaim menampilkan: info item yang diklaim (judul, foto, kategori), status klaim, tanggal pengajuan
- [ ] Untuk klaim `rejected`, alasan penolakan ikut ditampilkan
- [ ] Daftar diurutkan dari yang terbaru

**Catatan Teknis:**
- `GET /api/claims/my`

---

## 5. Profil Pengguna

### US-20 — Melihat Profil

```
Sebagai pengguna,
Saya ingin melihat informasi profil akun saya,
Agar saya dapat memverifikasi data diri yang terdaftar di sistem.
```

**Kriteria Penerimaan:**
- [ ] Halaman profil menampilkan: nama, email, nomor HP, prodi/unit, foto profil
- [ ] Tersedia ringkasan statistik personal: jumlah laporan dibuat, klaim diterima, barang dikembalikan

**Catatan Teknis:**
- `GET /api/profile`

---

### US-21 — Mengedit Profil

```
Sebagai pengguna,
Saya ingin memperbarui data profil saya,
Agar informasi yang ditampilkan ke pengguna lain selalu akurat.
```

**Kriteria Penerimaan:**
- [ ] Field yang dapat diubah: nama, nomor HP, prodi/unit
- [ ] Field nomor HP hanya menerima angka; input huruf tidak diizinkan
- [ ] Setelah simpan, perubahan langsung tampil di halaman profil
- [ ] Semua field bersifat opsional — hanya field yang diisi yang akan diupdate

**Catatan Teknis:**
- `PUT /api/profile`

---

### US-22 — Mengganti Foto Profil

```
Sebagai pengguna,
Saya ingin mengganti foto profil saya,
Agar pengguna lain dapat lebih mudah mengenali saya.
```

**Kriteria Penerimaan:**
- [ ] User dapat memilih foto dari galeri atau mengambil foto baru dengan kamera
- [ ] Foto yang diterima: format JPEG atau PNG, maksimal 2MB
- [ ] Foto lama otomatis terhapus dari storage setelah diganti
- [ ] Foto profil baru langsung tampil setelah upload berhasil

**Catatan Teknis:**
- `POST /api/profile/photo` via `multipart/form-data`, field `photo`
- `DELETE /api/profile/photo` untuk menghapus foto profil

---

## 6. Riwayat Aktivitas

### US-23 — Melihat Riwayat Aktivitas

```
Sebagai pengguna,
Saya ingin melihat riwayat semua aktivitas yang pernah saya lakukan di aplikasi,
Agar saya dapat memantau rekam jejak interaksi saya dengan sistem.
```

**Kriteria Penerimaan:**
- [ ] Riwayat menampilkan semua jenis aktivitas: membuat laporan, mengajukan klaim, menerima klaim, barang dikembalikan
- [ ] Setiap entri aktivitas menampilkan: ikon aksi, judul aktivitas, deskripsi, dan timestamp
- [ ] Daftar diurutkan dari aktivitas terbaru ke terlama
- [ ] Jumlah aktivitas yang ditampilkan dapat dibatasi (default: 20 entri terbaru)

**Catatan Teknis:**
- `GET /api/activities?limit={n}`
- Menggabungkan data dari tabel `items` dan `claims` secara bersamaan

---

## 7. Leaderboard

### US-24 — Melihat Leaderboard Kontributor

```
Sebagai pengguna,
Saya ingin melihat daftar peringkat pengguna berdasarkan kontribusi pengembalian barang,
Agar saya termotivasi untuk aktif membantu pengembalian barang di kampus.
```

**Kriteria Penerimaan:**
- [ ] Leaderboard menampilkan peringkat pengguna diurutkan berdasarkan jumlah barang yang berhasil dikembalikan (tertinggi di atas)
- [ ] Setiap entri menampilkan: nomor peringkat, nama user, foto avatar, prodi/unit, jumlah barang dikembalikan, jumlah barang ditemukan
- [ ] Leaderboard hanya menampilkan pengguna yang memiliki minimal 1 kontribusi (returned atau found)
- [ ] Default menampilkan 10 pengguna teratas
- [ ] Fitur pull-to-refresh tersedia
- [ ] Posisi user sendiri terlihat dalam daftar

**Catatan Teknis:**
- `GET /api/leaderboard?limit={n}`
- Poin dihitung dari: (item `found` yang `returned`) + (klaim `approved` pada item `lost` yang `returned`)

---

## Ringkasan User Stories

| ID | Modul | Judul | Prioritas |
|----|-------|-------|-----------|
| US-01 | Autentikasi | Registrasi akun | 🔴 High |
| US-02 | Autentikasi | Login | 🔴 High |
| US-03 | Autentikasi | Logout | 🔴 High |
| US-04 | Laporan | Lapor barang hilang | 🔴 High |
| US-05 | Laporan | Lapor barang temuan | 🔴 High |
| US-06 | Laporan | Lihat detail barang | 🔴 High |
| US-07 | Laporan | Edit laporan sendiri | 🟡 Medium |
| US-08 | Laporan | Hapus laporan sendiri | 🟡 Medium |
| US-09 | Laporan | Tandai barang dikembalikan | 🔴 High |
| US-10 | Laporan | Lihat laporan milik sendiri | 🟡 Medium |
| US-11 | Pencarian | Lihat semua laporan | 🔴 High |
| US-12 | Pencarian | Filter tipe (Hilang/Temuan) | 🟡 Medium |
| US-13 | Pencarian | Filter kategori | 🟡 Medium |
| US-14 | Pencarian | Pencarian kata kunci | 🟡 Medium |
| US-15 | Klaim | Ajukan klaim | 🔴 High |
| US-16 | Klaim | Lihat daftar klaim masuk | 🔴 High |
| US-17 | Klaim | Setujui klaim | 🔴 High |
| US-18 | Klaim | Tolak klaim | 🔴 High |
| US-19 | Klaim | Lihat riwayat klaim saya | 🟡 Medium |
| US-20 | Profil | Lihat profil | 🟡 Medium |
| US-21 | Profil | Edit profil | 🟡 Medium |
| US-22 | Profil | Ganti foto profil | 🟢 Low |
| US-23 | Aktivitas | Lihat riwayat aktivitas | 🟢 Low |
| US-24 | Leaderboard | Lihat leaderboard | 🟢 Low |

**Total: 24 User Stories** | 🔴 High: 11 | 🟡 Medium: 9 | 🟢 Low: 4

---

<p align="center">
  <i>Dokumen User Stories — FoundIt v1.0.0<br>
  Kelompok 1 — Universitas Internasional Semen Indonesia (UISI) 2026</i>
</p>
