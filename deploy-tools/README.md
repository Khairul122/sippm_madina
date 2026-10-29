# Deployment pada shared hosting

Workflow `.github/workflows/deploy.yml` membangun aplikasi, menjalankan tes,
lalu mengunggah kode dan aset publik lewat FTPS. Semua koneksi
FTP wajib memakai TLS yang valid. Pastikan server mendukung FTPS.

Secret `ENV_PRODUCTION` berisi seluruh konfigurasi `.env` produksi. Workflow
membuat `.env` dari secret tersebut dan mengunggahnya ke `/laravel_app/.env`.
Jangan simpan `.env`, dump SQL, atau token pemeliharaan dalam repositori.

Folder `vendor/` yang sudah terpasang di server dipertahankan saat deploy.
Jika `composer.lock` berubah, jalankan `composer install --no-dev` pada mesin
tepercaya, kemas folder `vendor/` sebagai ZIP, unggah lewat FTPS, lalu ekstrak
di `/laravel_app/` melalui file manager hosting. Hapus ZIP setelah ekstraksi.
Lakukan ini sebelum menjalankan workflow. Unggahan FTP langsung untuk ribuan
berkas vendor pernah terputus di tengah proses.

Sebelum deploy pertama, buat symlink berikut melalui panel hosting atau SSH:

```text
/public_html/storage -> /laravel_app/storage/app/public
```

Symlink tersebut tetap ada pada deploy berikutnya. Untuk migrasi, jalankan
`php artisan migrate --force` melalui SSH atau terminal panel hosting.
Endpoint HTTP untuk migrasi, seeding, cache, dan ekstraksi vendor tidak ada.

Jika skrip lama pernah disalin secara manual ke `public_html`, hapus
`sys-unzip.php`, `sys-migrate.php`, dan `artisan-run.php` di server.

## blokir ip dari log audit

Jalankan `php artisan migrate --force` setelah deploy agar tabel `blocked_ips`
tersedia. Fitur ini menolak permintaan yang masuk ke Laravel; aset statis dan
lalu lintas yang berhenti di web server tetap perlu aturan firewall/WAF.

Jika memakai proxy atau CDN, isi `TRUSTED_PROXIES` di `.env` dengan daftar IP
proxy yang benar-benar dipercaya, dipisahkan koma. Jangan gunakan `*`.
Jalankan `php artisan config:clear` setelah mengubah nilai ini. Tanpa daftar
proxy yang tepat, Laravel dapat melihat IP proxy sebagai IP semua pengunjung.
IP pada audit log lama mungkin berasal dari header yang dapat dipalsukan;
periksa ulang sebelum memblokirnya.
