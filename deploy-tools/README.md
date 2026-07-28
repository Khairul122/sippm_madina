# Tools Deployment (Shared Hosting / CWP tanpa SSH)

Folder ini berisi kumpulan script bantu untuk mengelola aplikasi Laravel di hosting tanpa akses SSH terminal.

---

## 1. Fast Vendor Unzipper (`vendor-unzip.php` / `public_html/sys-unzip.php`)

Digunakan untuk melakukan **Up Vendor Super Cepat (< 30 detik)** tanpa mengunggah puluhan ribu file `vendor/` secara manual satu per satu via FTP.

### ⚡ Cara Kerja Otomatis (GitHub Actions CI/CD)
1. Workflow `.github/workflows/deploy.yml` akan menjalankan `composer install --no-dev --optimize-autoloader` di runner.
2. Seluruh folder `vendor/` dikompres menjadi **1 file tunggal `vendor.zip`**.
3. `vendor.zip` diunggah via FTP ke `/laravel_app/vendor.zip` (proses upload hanya butuh ~10 detik).
4. GitHub Actions memanggil URL `https://domainanda.com/sys-unzip.php?token=uwVW5Kx3Xfmv`.
5. Script di server mengekstrak `vendor.zip` secara otomatis dalam 1–2 detik dan menghapus file zip setelah selesai.

### 🛠️ Cara Kerja Manual (Jika Tanpa CI/CD)
1. Di komputer lokal, jalankan:
   ```bash
   composer install --no-dev --prefer-dist --optimize-autoloader
   ```
2. Kompres folder `vendor` di lokal menjadi `vendor.zip`.
3. Unggah file `vendor.zip` tersebut ke folder `laravel_app/` di server via CWP File Manager atau FileZilla.
4. Akses URL berikut di browser:
   ```
   https://domainanda.com/sys-unzip.php?token=uwVW5Kx3Xfmv
   ```
5. Vendor berhasil diekstrak dan `vendor.zip` akan terhapus otomatis!

---

## 2. Artisan Command Runner (`artisan-run.php`)

Alat sementara untuk mengeksekusi artisan command tanpa terminal (seperti `migrate`, `storage:link`, `config:cache`).

### Cara Pakai:
1. Buka file di CWP File Manager: `laravel_app/deploy-tools/artisan-run.php`.
2. Ganti nilai `$token = '...'` dengan token rahasia Anda.
3. Salin file tersebut ke `public_html/artisan-run.php`.
4. Jalankan lewat browser:
   ```
   https://domainkamu.go.id/artisan-run.php?token=TOKEN_KAMU&cmd=migrate
   https://domainkamu.go.id/artisan-run.php?token=TOKEN_KAMU&cmd=link
   https://domainkamu.go.id/artisan-run.php?token=TOKEN_KAMU&cmd=config
   ```
5. **Setelah selesai, HAPUS `public_html/artisan-run.php` dari server.**
