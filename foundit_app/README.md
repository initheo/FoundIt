# FoundIt App

Aplikasi mobile Flutter untuk platform pelaporan dan pengembalian barang hilang di lingkungan UISI.

## 🛠️ Tech Stack

- **Framework**: Flutter 3.10+
- **State Management**: StatefulWidget
- **HTTP Client**: http package
- **Local Storage**: shared_preferences
- **Maps**: Google Maps Flutter
- **Location**: Geolocator + Geocoding

## 📱 Fitur Aplikasi

### Authentication
- ✅ Login dengan email UISI
- ✅ Registrasi akun baru
- ✅ Logout

### Home
- ✅ Daftar barang hilang/temuan
- ✅ Tab filter (Lost/Found)
- ✅ Search by title
- ✅ Filter by category
- ✅ Pull to refresh
- ✅ Statistik personal

### Laporan Barang
- ✅ Report Lost (Lapor Kehilangan)
- ✅ Report Found (Lapor Temuan)
- ✅ Upload multiple foto
- ✅ Pilih lokasi di peta
- ✅ Detail lokasi kustom

### Detail & Claim
- ✅ Detail barang lengkap
- ✅ Photo viewer fullscreen
- ✅ Claim barang ("Ini Barang Saya!")
- ✅ Laporkan temuan ("Saya Menemukan!")
- ✅ Review claims (untuk pemilik)
- ✅ Approve/Reject claim
- ✅ Mark as returned

### My Items
- ✅ Daftar barang yang dilaporkan
- ✅ Edit item
- ✅ Delete item
- ✅ Manage foto

### Profile
- ✅ View profile
- ✅ Edit profile
- ✅ Upload foto profil
- ✅ Statistik personal
- ✅ Riwayat aktivitas

### Leaderboard
- ✅ Ranking pengguna
- ✅ Berdasarkan barang dikembalikan

## 🚀 Instalasi

1. **Prerequisites**
   - Flutter SDK 3.10+
   - Android Studio / Xcode
   - Google Maps API Key

2. **Clone repository**
   ```bash
   git clone https://github.com/initheo/FoundItUas.git
   cd foundit_app
   ```

3. **Install dependencies**
   ```bash
   flutter pub get
   ```

4. **Setup Google Maps API Key**

   **Android** (`android/app/src/main/AndroidManifest.xml`):
   ```xml
   <meta-data
       android:name="com.google.android.geo.API_KEY"
       android:value="YOUR_API_KEY"/>
   ```

   **iOS** (`ios/Runner/AppDelegate.swift`):
   ```swift
   GMSServices.provideAPIKey("YOUR_API_KEY")
   ```

5. **Konfigurasi API URL**

   Edit `lib/shared/utils/app_constants.dart`:
   ```dart
   static const String baseUrl = 'http://YOUR_API_IP:8000/api';
   ```

6. **Jalankan aplikasi**
   ```bash
   flutter run
   ```

## 📁 Struktur Folder

```
lib/
├── data/
│   ├── model/           # Data models
│   ├── repository/      # API repositories
│   └── usecase/         # Request/Response models
├── presentation/
│   └── screens/         # UI screens
│       ├── auth/        # Login, Register
│       ├── home/        # Home screen
│       ├── item/        # Detail, Edit item
│       ├── report/      # Report lost/found
│       ├── claim/       # Claim screens
│       ├── myitems/     # My items
│       ├── profile/     # Profile screens
│       ├── leaderboard/ # Leaderboard
│       └── activity/    # Activity history
└── shared/
    ├── utils/           # Helpers, constants
    └── widget/          # Reusable widgets
```

## 🔧 Build APK

```bash
# Debug APK
flutter build apk --debug

# Release APK
flutter build apk --release
```
