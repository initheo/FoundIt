# FoundIt API

Backend REST API untuk aplikasi FoundIt - platform pelaporan dan pengembalian barang hilang di lingkungan UISI.

## 🛠️ Tech Stack

- **Framework**: Laravel 12
- **PHP**: 8.2+
- **Database**: MySQL / SQLite
- **Authentication**: Laravel Sanctum (Token-based)
- **API Documentation**: Scramble (auto-generated)

## 📋 Fitur API

### Authentication
- `POST /api/register` - Registrasi user (email UISI)
- `POST /api/login` - Login & mendapatkan token
- `POST /api/logout` - Logout (revoke token)

### Items
- `GET /api/items` - List semua item (filter: type, category, search)
- `GET /api/items/{id}` - Detail item
- `POST /api/items` - Buat laporan baru (lost/found)
- `PUT /api/items/{id}` - Update item
- `DELETE /api/items/{id}` - Hapus item
- `GET /api/my-items` - Item milik user yang login
- `POST /api/items/{id}/photos` - Upload foto item
- `DELETE /api/items/{id}/photos/{photoId}` - Hapus foto item
- `PUT /api/items/{id}/mark-returned` - Tandai sebagai dikembalikan

### Claims
- `GET /api/items/{id}/claims` - List claims untuk item
- `POST /api/items/{id}/claims` - Submit claim
- `PUT /api/claims/{id}/approve` - Approve claim
- `PUT /api/claims/{id}/reject` - Reject claim
- `GET /api/my-claims` - Riwayat claim user

### Profile
- `GET /api/profile` - Get profile user
- `PUT /api/profile` - Update profile
- `POST /api/profile/photo` - Upload foto profil
- `GET /api/statistics` - Statistik user

### Other
- `GET /api/categories` - List kategori barang
- `GET /api/leaderboard` - Leaderboard pengembalian
- `GET /api/activity-history` - Riwayat aktivitas user

## 🚀 Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/initheo/FoundItUas.git
   cd foundit_api
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi database** di `.env`
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=foundit
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Migrasi & seeding**
   ```bash
   php artisan migrate --seed
   ```

6. **Jalankan server**
   ```bash
   php artisan serve
   ```

## 📁 Struktur Folder

```
app/
├── Http/Controllers/Api/   # Controller API
├── Models/                 # Eloquent Models
database/
├── migrations/             # Database migrations
├── seeders/               # Data seeders
routes/
└── api.php                # API routes
storage/
└── app/public/            # Uploaded files
```

## 📝 API Documentation

API documentation tersedia di `/docs/api` (powered by Scramble).

## 👥 Default Users (Seeder)

| Email | Password | Nama |
|-------|----------|------|
| muhammad.nuha23@student.uisi.ac.id | password | Muhammad Muqoffin Nuha |
| ari.hinanda23@student.uisi.ac.id | password | Ari Setia Hinanda |
