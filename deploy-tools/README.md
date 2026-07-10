# artisan-run.php — jalankan artisan command tanpa terminal (CWP)

Alat sementara untuk shared hosting (CWP) yang tidak menyediakan akses terminal/SSH.
File ini TIDAK ikut ter-deploy ke `public_html` oleh CI — hanya ikut ke `laravel_app/deploy-tools/`
(folder itu tidak diakses lewat browser, jadi aman berada di sana secara permanen sebagai cadangan).

## Cara pakai

1. Buka file ini di File Manager CWP: `laravel_app/deploy-tools/artisan-run.php`.
2. Edit baris `$token = '...'` — ganti dengan string random panjang (minimal 32 karakter, bebas kombinasi huruf/angka).
   Jangan pakai contoh apa pun yang pernah dibagikan di chat/dokumentasi manapun.
3. Salin file yang sudah diedit ke `public_html/artisan-run.php` (lewat File Manager, copy-paste atau upload manual).
4. Jalankan lewat browser, satu command per URL, berurutan sesuai kebutuhan:

   ```
   https://domainkamu.go.id/artisan-run.php?token=TOKEN_KAMU&cmd=key
   https://domainkamu.go.id/artisan-run.php?token=TOKEN_KAMU&cmd=migrate
   https://domainkamu.go.id/artisan-run.php?token=TOKEN_KAMU&cmd=link
   https://domainkamu.go.id/artisan-run.php?token=TOKEN_KAMU&cmd=config
   ```

   Command yang tersedia: `key`, `migrate`, `link`, `config`, `route`, `view`, `clear`.

5. **Setelah selesai, HAPUS `public_html/artisan-run.php` dari server.** Jangan biarkan file ini
   menetap di `public_html` — siapa pun yang tahu URL dan token bisa menjalankan artisan command
   di server produksi selama file itu ada.

## Urutan command yang biasanya dibutuhkan saat setup pertama kali

1. `key` — generate `APP_KEY` di `.env` (jalankan sekali saja, lewati kalau `APP_KEY` sudah terisi)
2. `migrate` — jalankan migration database
3. `link` — buat symlink `public/storage` → `storage/app/public`
4. `config` — cache konfigurasi untuk performa (jalankan ulang tiap kali `.env` berubah, karena
   selama config di-cache, perubahan `.env` tidak akan terbaca sampai kamu jalankan `clear` atau `config` lagi)
