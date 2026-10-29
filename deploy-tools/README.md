# Deployment pada shared hosting

Workflow `.github/workflows/deploy.yml` membangun aplikasi, menjalankan tes,
lalu mengunggah kode, `vendor/`, dan aset publik lewat FTPS. Semua koneksi
FTP wajib memakai TLS yang valid. Pastikan server mendukung FTPS.

Secret `ENV_PRODUCTION` berisi seluruh konfigurasi `.env` produksi. Workflow
membuat `.env` dari secret tersebut dan mengunggahnya ke `/laravel_app/.env`.
Jangan simpan `.env`, dump SQL, atau token pemeliharaan dalam repositori.

Sebelum deploy pertama, buat symlink berikut melalui panel hosting atau SSH:

```text
/public_html/storage -> /laravel_app/storage/app/public
```

Symlink tersebut tetap ada pada deploy berikutnya. Untuk migrasi, jalankan
`php artisan migrate --force` melalui SSH atau terminal panel hosting.
Endpoint HTTP untuk migrasi, seeding, cache, dan ekstraksi vendor tidak ada.

Jika skrip lama pernah disalin secara manual ke `public_html`, hapus
`sys-unzip.php`, `sys-migrate.php`, dan `artisan-run.php` di server.
