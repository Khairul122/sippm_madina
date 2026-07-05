# SIPPM Madina

Sistem Informasi Pengaduan Masyarakat & Pelaporan Kegiatan untuk Pemerintah
Kabupaten Mandailing Natal. Dibangun di atas Laravel 13 dengan pendekatan
Clean Architecture (Domain → Application → Infrastructure → Presentation).

Fitur utama:

- Pengaduan masyarakat (single-gate-service melalui Kominfo): pengajuan,
  verifikasi, disposisi ke OPD/Camat, penanganan, jawaban resmi, tracking
  publik via nomor tiket (`PGD-{tahun}-{6 digit}`).
- Pelaporan kegiatan OPD/Kecamatan: input, verifikasi, publikasi ke feed
  publik.
- Dashboard monitoring & statistik per peran (RBAC 7 peran: Masyarakat,
  Kominfo, OPD, Camat, Bupati, Wakil Bupati, Sekda).
- Notifikasi real-time (arsitektur broadcasting driver-agnostic; Reverb
  akan dipasang di fase akhir pengembangan).
- Audit log immutable untuk semua aksi sensitif.

Lihat `AGENTS.md` untuk aturan pengembangan (Do's/Don'ts, struktur folder
wajib) dan `progress.md` untuk status implementasi modul per modul.

## Environment

- PHP 8.3+, Composer
- MySQL 8.x (development dijalankan via Laragon: `C:\laragon\bin\mysql\mysql-8.4.3-winx64`)
- Node.js + npm (Vite hanya untuk compile CSS palet warna kustom & JS
  kecil — Bootstrap 5, AdminLTE, Alpine.js, Chart.js, DataTables,
  SweetAlert2, Leaflet dipakai via CDN di Blade layouts)

## Setup

1. Pastikan MySQL Laragon jalan. Jika belum, start service-nya (Laragon
   UI, atau jalankan `mysqld.exe` langsung dengan config
   `my.ini` bawaan Laragon).
2. Salin `.env.example` ke `.env` bila belum ada, sesuaikan kredensial DB
   bila berbeda dari default (`sippm_madina` / `root` / password kosong).
3. Install dependencies:
   ```bash
   composer install
   npm install
   ```
4. Generate app key (jika belum ada):
   ```bash
   php artisan key:generate
   ```
5. Migrate & seed database (membuat RBAC roles/permissions, data OPD,
   kecamatan, dan 1 akun demo per peran — lihat
   `database/seeders/DemoUserSeeder.php` untuk kredensial):
   ```bash
   php artisan migrate --seed
   ```
6. Jalankan semua proses development (server, queue worker, log viewer,
   Vite) sekaligus:
   ```bash
   composer dev
   ```

## Struktur Kode

Lihat bagian "Struktur Folder" di `AGENTS.md` untuk penjelasan lengkap
lapisan Domain/Application/Infrastructure/Presentation.
