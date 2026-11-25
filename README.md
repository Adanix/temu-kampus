# TemuKampus — Campus Lost & Found

**TemuKampus** adalah aplikasi web sistem pelaporan dan pengembalian barang hilang & temuan di lingkungan kampus (*Campus Lost & Found*) berbasis **Laravel 13**, **Tailwind CSS 4**, dan **Alpine.js**. Proyek ini dirancang dengan alur kerja operasional kampus yang realistis: pelaporan barang hilang/temuan terstruktur per zona kampus, pencocokan otomatis (*Smart Matching*), perlindungan privasi dokumen sensitif (*Sensitive ID Auto-Blur*), verifikasi klaim berbasis *Blind Challenge*, serta serah terima barang fisik menggunakan PIN 6 digit melalui Meja Petugas Pos.

---

## Fitur Utama

- **Beragam Peran Kampus & Demo Quick Login (`/demo/login`)**
  Dukungan empat peran (*Mahasiswa*, *Dosen/Staf*, *Petugas Pos Penitipan*, dan *Admin Kemahasiswaan*) dengan halaman masuk cepat 1-klik saat `APP_DEMO_MODE=true`.
- **Katalog & Pelaporan Barang (`/items`, `/items/create`, `/items/{item}`)**
  - Penomoran tiket otomatis dengan format `TK-YYYY-NNNN`.
  - Filter multi-parameter: tipe laporan (`Hilang` / `Ditemukan`), kategori (`electronics`, `id_document`, `wallet_bag`, `keys`, `books_stationery`, `accessories`, `other`), zona kampus, status, urutan, dan pencarian kata kunci.
  - Pengunggahan foto bersifat opsional (`nullable`); laporan tanpa unggahan foto tetap menampilkan ilustrasi kategori yang relevan (`Ilustrasi kategori` / `Category illustration`) tanpa mengubah `photo_path` di basis data.
  - 22 laporan awal (`TK-2026-0001`–`TK-2026-0022`) dilengkapi aset gambar lokal `800×600` WebP di `public/images/demo/items/`.
- **Perlindungan Privasi & Ciri Rahasia (*Blind Challenge*)**
  - Laporan berkategori `id_document` otomatis ditandai sensitif (`is_sensitive = true`): foto disamarkan (`blur-md`) untuk publik, dan hanya pelapor, petugas pos, atau admin yang memiliki tombol *"Tampilkan Foto"*.
  - Kolom `hidden_traits` pada barang temuan dirahasiakan dari halaman publik agar hanya pemilik asli yang dapat membuktikannya saat mengajukan klaim.
- **Pencocokan Otomatis (*Smart Matching* — `/matches`)**
  Menghitung skor kecocokan deterministik (`0–100`) antara laporan *Hilang* dan *Temuan* berstatus terbuka pada kategori yang sama (disimpan jika skor `>= 60`), berdasarkan:
  - Kategori sama (`35` poin)
  - Zona kampus (`25` poin untuk zona identik, `12` poin untuk grup zona sama)
  - Kesamaan kata kunci judul/merek/warna/deskripsi (`maks 25` poin)
  - Kedekatan tanggal kejadian (`15` poin jika `<= 2` hari, `10` poin jika `<= 7` hari, `5` poin jika `<= 14` hari)
- **Verifikasi Klaim & PIN Serah Terima 6 Digit (`/claims`, `/custodian/desk`)**
  - Pemohon mengisi jawaban verifikasi *Blind Challenge* (serta bukti foto opsional).
  - Persetujuan klaim oleh Petugas Pos berjalan atomik dalam transaksi DB, menerbitkan PIN serah terima 6 digit (`XXX-XXX`), serta menutup klaim pesaing lain pada item yang sama secara otomatis.
  - Penyerahan fisik memvalidasi PIN sebelum status klaim ditandai selesai dan status barang diperbarui menjadi *Dikembalikan* (`returned`).
- **Analitik Admin, Master Data & Notifikasi Simulasi (`/admin/analytics`, `/admin/master`, `/notifications`)**
  - Dasbor admin menampilkan KPI (`total_reports`, `recovery_rate`, `avg_recovery_hours`, `pending_claims`), 5 zona hotspot kehilangan teratas, distribusi status, serta ekspor data CSV (`/admin/export/items`).
  - Pengelolaan data master zona kampus & pos penitipan, serta pusat log notifikasi dwibahasa simulasi (`SimulatedNotificationDispatcher`).
- **Antarmuka Dwibahasa (`ID` / `EN`)**
  Seluruh navigasi, label, pesan validasi, enum, dan halaman panduan (`/docs`) tersedia dalam Bahasa Indonesia dan Bahasa Inggris (`POST /locale`).

---

## Akun Persona Demo

Saat dijalankan dengan `APP_DEMO_MODE=true` dan seeder demo (`php artisan demo:reset`), keempat akun berikut tersedia di `/demo/login` (kata sandi seeder: `TemuKampusDemo!2026`):

| Peran | Nama | Email | Kode / Unit Kampus |
|---|---|---|---|
| Mahasiswa (`student`) | Raka Pratama | `raka.pratama@kampustemu.ac.id` | `NIM-2023010142` — Fakultas Teknik — Informatika |
| Dosen/Staf (`staff`) | Dr. Ratna Wijaya | `ratna.wijaya@kampustemu.ac.id` | `NIDN-0412088501` — Fakultas Ekonomi & Bisnis |
| Petugas Pos (`custodian`) | Bapak Hendra Kusuma | `hendra.kusuma@kampustemu.ac.id` | `STF-POS-001` — Pos Satpam Gerbang Utama |
| Admin (`admin`) | Nadia Larasati | `nadia.larasati@kampustemu.ac.id` | `ADM-SARPRAS-01` — Biro Kemahasiswaan & Sarpras |

---

## Teknologi yang Digunakan

- **Backend:** PHP `^8.3` (diuji pada PHP 8.5), Laravel Framework `^13.17`
- **Frontend:** Blade Templates, Tailwind CSS `^4.0.0` (`@tailwindcss/vite`), Alpine.js `^3.17.5`, Vite `^8.0.0`
- **Testing & Kualitas Kode:** PHPUnit `^12.5`, Laravel Pint `^1.27`
- **Basis Data:** SQLite (bawaan `.env.example`) atau MySQL / MariaDB

---

## Struktur Direktori Inti

```text
app/
├── Console/Commands/       # Perintah artisan demo:reset
├── Enums/                  # ItemType, ItemCategory, ItemStatus, ClaimStatus, UserRole, dll.
├── Http/
│   ├── Controllers/        # ItemReport, ItemClaim, ItemMatch, CustodianDesk, AdminAnalytics, dll.
│   ├── Middleware/         # SetAppLocale, EnsureDemoModeEnabled
│   └── Requests/           # Validasi form terstruktur (FormRequest)
├── Models/                 # ItemReport, ItemClaim, ItemMatch, CampusZone, CustodyPost, NotificationLog, User
├── Policies/               # Otorisasi berbasis peran & kepemilikan laporan
└── Services/               # SmartMatchingService, ClaimWorkflowService, AdminAnalyticsService, Notifications
database/
├── migrations/             # Skema users, campus_zones, custody_posts, item_reports, item_matches, claims, logs
└── seeders/                # DatabaseSeeder (22 item demo, 6 zona, 3 pos penitipan, klaim & kecocokan awal)
lang/                       # Kamus terjemahan simetris ID & EN
public/images/demo/items/   # 22 aset gambar barang .webp (800x600)
resources/views/            # Komponen UI & halaman Blade (layouts, items, matches, claims, custodian, admin)
tests/                      # Feature & Unit test suite PHPUnit
```

---

## Instalasi & Menjalankan Proyek Secara Lokal

1. **Kloning repositori & pasang dependensi**
   ```bash
   git clone https://github.com/Adanix/temu-kampus.git
   cd temu-kampus
   composer install
   npm install
   ```

2. **Konfigurasi lingkungan (`.env`)**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Sesuaikan konfigurasi basis data (`DB_CONNECTION`, `DB_DATABASE`, dll.) pada berkas `.env` apabila menggunakan MySQL/MariaDB. Jika menggunakan SQLite, pastikan berkas `database/database.sqlite` tersedia.

3. **Tautkan penyimpanan & isi data demo (`demo:reset`)**
   ```bash
   php artisan storage:link
   php artisan demo:reset
   ```
   > Perintah `php artisan demo:reset` menjalankan `migrate:fresh --seed` dan membersihkan folder unggahan uji coba (`items`, `claim-proofs`) pada disk `public`, tanpa menghapus aset gambar demo bawaan di `public/images/demo/items/`.

4. **Build aset frontend & jalankan server**
   ```bash
   npm run build
   php artisan serve
   ```
   Atau jalankan mode pengembangan penuh (`composer run dev`). Akses aplikasi pada `http://localhost:8000` (atau virtual host lokal Anda seperti Laragon).

---

## Pengujian & Pemformatan

- Jalankan seluruh rangkaian uji fitur & unit (`42` tes PHPUnit):
  ```bash
  php artisan test --compact
  ```
- Format kode PHP dengan Laravel Pint:
  ```bash
  vendor/bin/pint --format agent
  ```

---

## Lisensi

Proyek ini menggunakan lisensi sumber terbuka [MIT License](https://opensource.org/licenses/MIT).
