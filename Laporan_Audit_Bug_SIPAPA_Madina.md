# Laporan Audit & Analisis Bug — Sistem SIPAPA Madina

**Investigasi kode atas temuan pengujian pada lingkungan hosting CWP**
Disusun: 11 Agustus 2026

---

## 1. Ringkasan Eksekutif

Audit ini menelusuri source code proyek SIPAPA Madina (Laravel 13, Clean Architecture) untuk mencari akar masalah dari 12 temuan bug yang dilaporkan pada dokumen pengujian (SIPAPA_MENU.docx). Investigasi dilakukan langsung pada kode, konfigurasi deployment, dan riwayat perbaikan sebelumnya (progress.md, PERBAIKAN.docx).

Sembilan dari sebelas temuan berhasil dilacak sampai ke baris kode penyebabnya. Delapan di antaranya berasal dari satu akar masalah yang sama: symlink storage publik yang salah sasaran akibat arsitektur deployment terpisah (app core di `/laravel_app/`, folder public di `/public_html/`) pada hosting CWP tanpa akses SSH. Tiga temuan lain adalah bug independen di kode aplikasi: kesalahan scope RBAC pada tombol detail pengaduan, tombol Konfig TTD yang tidak terhubung ke Alpine.js, dan file logo aplikasi yang ternyata identik dengan logo pemda.

Semua temuan disertai lokasi file dan baris kode sebagai bukti, beserta rekomendasi perbaikan konkret di bagian 5.

## 2. Metodologi

- Membaca dokumen bug SIPAPA_MENU.docx (11 catatan hasil uji dari beberapa akun demo) sebagai daftar temuan yang harus ditelusuri.
- Menelusuri source code di `app/`, `routes/web.php`, `resources/views/`, `config/`, dan `deploy-tools/` untuk setiap temuan.
- Membandingkan konfigurasi `.env` lokal dan `deploy-tools/.env.production.example` dengan alur deploy di `.github/workflows/deploy.yml`.
- Memeriksa riwayat perbaikan sebelumnya di `progress.md` dan `PERBAIKAN.docx` untuk konteks dan pola bug yang berulang.

Catatan: audit ini murni analisis kode (static analysis). Tidak ada akses langsung ke server produksi CWP, jadi beberapa temuan (bagian 4.8 dan 4.9) memerlukan verifikasi langsung di server untuk konfirmasi akhir.

## 3. Ringkasan Prioritas Temuan

| No | Temuan | Tingkat | Akar Masalah |
|---|---|---|---|
| 1 | Foto/gambar 404 di hampir semua halaman (foto pimpinan, dokumentasi kegiatan, lampiran pengaduan, bukti dukung) | **KRITIS** | Symlink `public/storage` dibuat di lokasi yang salah — lihat 4.1 |
| 2 | Deploy otomatis tidak menyertakan file `index.php` produksi yang sudah diperbaiki | **KRITIS** | Celah pada pipeline CI/CD — lihat 4.2 |
| 3 | Tombol "Detail" laporan pengaduan error untuk akun Bupati & Sekda | **TINGGI** | Middleware role tidak mencakup role tsb — lihat 4.3 |
| 4 | Tombol "Konfig TTD" tidak berfungsi | **SEDANG** | Directive Alpine.js di luar scope `x-data` — lihat 4.6 |
| 5 | Logo pada cetak laporan adalah logo pemda, bukan logo aplikasi | **RENDAH** | File `logo-sipapa.png` identik dengan `logo-madina.png` — lihat 4.7 |
| 6 | Manual book gagal diunduh | **SEDANG** | Kemungkinan file fisik tidak ada di server — lihat 4.8, perlu verifikasi |
| 7 | Filter "Dari Tanggal" bermasalah di dashboard Kominfo | **RENDAH** | Tidak ditemukan bug kode — lihat 4.9, perlu reproduksi |

## 4. Detail Temuan

### 4.1 Akar masalah utama — symlink storage salah sasaran

**Dampak:** menjelaskan hampir seluruh laporan "foto 404 / error", termasuk foto pimpinan tidak muncul, foto dokumentasi kegiatan, foto lampiran bukti dukung pengaduan, dan laporan akun `wakil_bupati@gmail.com` — "saat melihat poto semua nya 404 atau error".

Proyek ini di-deploy ke shared hosting CWP tanpa SSH, dengan struktur folder terpisah: kode aplikasi diunggah ke `/laravel_app/`, sedangkan isi folder `public/` diunggah terpisah ke `/public_html/` (lihat `.github/workflows/deploy.yml` baris 126-180). Karena Laravel secara default menganggap folder `public/` berada persis di dalam folder aplikasi, `public_path()` akan salah menunjuk ke `/laravel_app/public` — folder yang sengaja dikosongkan karena isinya sudah dipindah ke `/public_html/`.

Untuk mengatasi ini, proyek sudah memiliki mekanisme perbaikan di dua tempat:

- `deploy-tools/index.php` baris 31 — memanggil `$app->usePublicPath(__DIR__)` SEBELUM request diproses. Ini benar dan berfungsi bila file ini yang aktif di server sebagai `public_html/index.php`.
- `routes/web.php` baris 235-266 — route `/sys/link`, dipanggil otomatis oleh pipeline deploy (`.github/workflows/deploy.yml` baris 194-199) setiap kali deploy sukses.

Route `/sys/link` inilah yang bermasalah. Kodenya memanggil `app()->usePublicPath($publicPath)` di dalam closure route, lalu menjalankan `Artisan::call('storage:link')`. Masalahnya, `config/filesystems.php` baris 76-78 mendefinisikan lokasi symlink lewat `public_path('storage')` yang dievaluasi sekali saat konfigurasi Laravel di-load — proses ini terjadi jauh sebelum route/closure dijalankan (config selalu dimuat lebih dulu saat bootstrap, lalu baru routing). Artinya, saat closure `/sys/link` memanggil `usePublicPath()`, array config `filesystems.links` sudah telanjur berisi path lama (`/laravel_app/public/storage`), dan panggilan `usePublicPath()` di baris 246 tidak berpengaruh apa pun terhadap config yang sudah dimuat.

```php
// config/filesystems.php:76-78
'links' => [
    public_path('storage') => storage_path('app/public'),
],
```

```php
// vendor/laravel/framework/.../StorageLinkCommand.php:61-65
protected function links() {
    return $this->laravel['config']['filesystems.links'] ?? [...];
}
```

Akibatnya, setiap kali pipeline deploy memicu `/sys/link`, symlink storage justru dibuat ulang di `/laravel_app/public/storage` — folder yang tidak pernah diakses browser — bukan di `/public_html/storage` yang sebenarnya menjadi webroot. Jika sebelumnya sempat ada symlink manual yang benar di `/public_html/storage`, kode di baris 249-258 pada route yang sama justru menghapus symlink lama tersebut sebelum menjalankan `storage:link` versi yang salah sasaran, sehingga bisa memperparah kondisi dari yang tadinya berfungsi menjadi rusak total.

**Lokasi kode:** `routes/web.php` baris 235-266, `config/filesystems.php` baris 76-78.

### 4.2 Celah pipeline deploy — index.php produksi tidak terkelola git

Akar masalah 4.1 sebenarnya sudah punya solusi yang benar dan tersedia di repository: `deploy-tools/index.php`. File ini didesain untuk disalin manual ke `public_html/index.php` di server. Tapi file `public/index.php` pada repo (yang notabene ikut ter-deploy melalui workflow) tidak memiliki baris `usePublicPath()` sama sekali — dan workflow `deploy.yml` baris 181-185 secara sengaja mengecualikan `index.php` dari proses upload ke `public_html/`.

Ini berarti perbaikan kritis untuk seluruh masalah foto/gambar bergantung sepenuhnya pada satu file yang disalin manual sekali oleh pengguna via CWP File Manager, tidak pernah diverifikasi otomatis, dan tidak ikut ter-deploy ulang setiap ada perubahan. Catatan di `progress.md` (entri 2026-07-11 dan 2026-07-12) mengonfirmasi celah ini pernah menyebabkan HTTP 500 di situs live dan perbaikannya "belum dikonfirmasi berhasil oleh user" — polanya konsisten dengan laporan foto 404 yang masih berulang di SIPAPA_MENU.docx.

**Lokasi kode:** `deploy-tools/index.php` (versi benar, tidak otomatis ter-deploy), `public/index.php` (versi default, ikut ter-deploy tapi tidak dipakai server), `.github/workflows/deploy.yml` baris 181-185.

### 4.3 Tombol "Detail" laporan pengaduan error untuk akun Bupati & Sekda

**Dampak:** sesuai laporan — "Di akun bupati demo dan akun sekda demo di menu laporan pengaduan ketika tombol detail tekan" muncul halaman error.

Halaman `/dashboard/laporan` (tab Laporan Pengaduan) dapat diakses oleh role `kominfo`, `bupati`, `wakil_bupati`, dan `sekda` (`routes/web.php` baris 209-210). Di halaman ini, setiap baris data pengaduan menampilkan tombol "Detail" yang mengarah ke `/dashboard/complaints/{id}`:

```php
// resources/views/dashboard/laporan/index.blade.php:342-344
<a href="{{ url('/dashboard/complaints/'.$complaint->id) }}" ...>
    <i class="bi bi-eye"></i> Detail
</a>
```

Namun route `/dashboard/complaints/{complaint}` hanya diizinkan untuk role `kominfo`, `opd`, dan `camat` (`routes/web.php` baris 109-111). Role `bupati`, `wakil_bupati`, dan `sekda` tidak termasuk dalam middleware ini, sehingga saat mereka menekan tombol Detail, Laravel mengembalikan 403 Forbidden. Komentar di kode (baris 105-108) memang menyatakan by design "tidak ada baris Lihat Pengaduan untuk Bupati/Wabup/Sekda", tapi tampilan UI tetap menyediakan tombol tersebut untuk ketiga role itu — inkonsistensi antara desain akses dan tampilan inilah yang menjadi bug-nya.

**Lokasi kode:** `routes/web.php` baris 109-111 vs baris 209-210, `resources/views/dashboard/laporan/index.blade.php` baris 342-344.

### 4.4 Detail Kegiatan — dokumentasi foto error (akun Sekda)

Berbeda dari 4.3, route `/dashboard/activities/{activity}` sudah mengizinkan semua role termasuk sekda (`routes/web.php` baris 133-136), jadi halaman detail kegiatan itu sendiri bisa diakses. Masalahnya ada pada foto dokumentasi di dalam halaman tersebut, yang dirender lewat `asset('storage/...')`:

```php
// resources/views/dashboard/activities/show.blade.php:43-44
<a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank">
<img src="{{ asset('storage/'.$doc->file_path) }}" ...>
```

URL ini bergantung sepenuhnya pada symlink `public/storage` yang benar. Ini adalah konsekuensi langsung dari akar masalah 4.1, bukan bug terpisah.

**Lokasi kode:** `resources/views/dashboard/activities/show.blade.php` baris 43-44.

### 4.5 Dashboard Kominfo — foto lampiran & bukti dukung tindak lanjut OPD

Sama seperti 4.4, halaman detail pengaduan menampilkan lampiran pengaduan dan bukti dukung penanganan dari OPD lewat pola URL yang sama:

```php
// resources/views/dashboard/complaints/show.blade.php:28-29 (lampiran pengaduan)
// resources/views/dashboard/complaints/show.blade.php:55-56 (bukti dukung/tindak lanjut OPD)
<a href="{{ asset('storage/'.$attachment->file_path) }}" ...>
```

Konsekuensi langsung dari akar masalah 4.1. Setelah symlink diperbaiki dengan benar, kedua temuan ini (dan laporan `kominfo@gmail.com` poin "POTO LAMPIRAN" serta "detail kegiatan setiap melihat dokumentasi/poto") akan otomatis pulih tanpa perubahan kode tambahan.

### 4.6 Tombol "Konfig TTD" tidak berfungsi

Tombol ini berada di halaman `/dashboard/laporan`, khusus role kominfo, dimaksudkan untuk memindahkan pengguna ke tab "Pengaturan TTD" lewat Bootstrap tab API:

```php
// resources/views/dashboard/laporan/index.blade.php:274-277
<button type="button" @click="document.getElementById('ttd-tab').click()">
    <i class="bi bi-pencil-square"></i>
    <span>Konfig TTD</span>
</button>
```

Atribut `@click` adalah sintaks directive Alpine.js, bukan atribut HTML native. Alpine hanya akan memproses directive semacam ini jika elemen tersebut berada di dalam elemen lain yang memiliki atribut `x-data`. Satu-satunya `x-data` di file ini berada di baris 508 — jauh setelah tombol Konfig TTD di baris 274, dan berada di bagian DOM yang berbeda (bukan leluhur dari tombol ini). Karena tidak ada `x-data` yang menaunginya, Alpine tidak pernah menginisialisasi directive `@click` pada tombol ini, sehingga secara fungsional atribut itu diabaikan browser dan tombol tidak melakukan apa pun saat ditekan.

**Lokasi kode:** `resources/views/dashboard/laporan/index.blade.php` baris 274 (tombol bermasalah) vs baris 508 (satu-satunya `x-data` di file ini).

### 4.7 Logo pada cetak laporan adalah logo pemda, bukan logo aplikasi

File yang dipakai sebagai logo aplikasi di seluruh sistem, termasuk kop surat cetak laporan PDF, adalah `public/images/logo-sipapa.png`:

```php
// resources/views/dashboard/laporan/export-pdf.blade.php:139
$logoPath = public_path('images/logo-sipapa.png');
```

Pengecekan checksum (MD5) membuktikan file ini identik byte-per-byte dengan `public/images/logo-madina.png` (logo resmi Pemkab Mandailing Natal) dan dengan file upload asli "SIPAPA (3).png":

```
MD5: 8b85623b40dc4bbe93d82674857983f7
public/images/logo-sipapa.png
public/images/logo-madina.png
public/images/SIPAPA (3).png
```

Dengan kata lain, tidak pernah ada aset logo aplikasi SIPAPA yang berdiri sendiri — file yang dinamai `logo-sipapa.png` sebenarnya berisi gambar logo pemda yang sama. Ini sesuai persis dengan laporan "Logo Laporan dibuat logo pemda bukan logo aplikasi", dan juga tercatat sebagai isu berulang di PERBAIKAN.docx sebelumnya ("Logo pemda di cetak laporan").

**Lokasi kode:** `public/images/logo-sipapa.png`, `resources/views/dashboard/laporan/export-pdf.blade.php` baris 139.

### 4.8 Manual book gagal diunduh (perlu verifikasi di server)

Kode `ManualBookController` sudah benar secara struktur — upload, download, dan preview semuanya eksplisit memakai `Storage::disk('public')` yang membaca file langsung dari `storage/app/public/` tanpa melalui symlink, sehingga tidak terpengaruh akar masalah 4.1:

```php
// app/Http/Controllers/Web/ManualBookController.php:39
return Storage::disk('public')->download($manualBook->file_path, $manualBook->original_name);
```

Kemungkinan penyebab paling besar: baris `file_path` di tabel `manual_books` pada database produksi menunjuk ke file yang secara fisik tidak pernah ada di server — misalnya karena data diimpor dari dump SQL lokal (`silapga_web.sql` di root proyek) tanpa file fisiknya ikut diunggah, atau karena upload sebelumnya gagal sebagian. Symfony akan melempar `FileNotFoundException` saat file fisik tidak ada, dan karena `APP_DEBUG=false` di produksi, pengguna hanya melihat halaman error generik tanpa detail — konsisten dengan laporan "masih eror dan tidak bisa di download". Ini juga tercatat sebagai isu lama di PERBAIKAN.docx ("Manual Book belum bisa di download").

**Rekomendasi verifikasi:** cek langsung di CWP File Manager apakah file di `storage/app/public/manual-books/` benar-benar ada, dan cocokkan dengan kolom `file_path` di tabel `manual_books`.

### 4.9 Filter "Dari Tanggal" di dashboard Kominfo

Logika filter `date_from` di `LaporanController` (baris 141-147 dan 282-288) menggunakan `whereDate()` dengan Carbon date object dari `$request->date('date_from')` — pola ini standar dan tidak ditemukan bug logika di baliknya. Input filter tanggal di halaman `/dashboard/laporan` (baris 251, 406) memakai `<input type="date">` native tanpa auto-submit `onchange`, sehingga pengguna harus menekan tombol "Cari Data" secara terpisah setelah mengisi tanggal — kemungkinan ini yang dirasakan pengguna sebagai "eror" karena filter tidak langsung berefek begitu tanggal dipilih (beda dari halaman lain seperti `dashboard/activities/index.blade.php` baris 39 yang sudah memakai `onchange="this.form.submit()"`).

**Rekomendasi:** perlu reproduksi lebih detail dari pengguna (screenshot/video) untuk memastikan apakah ini murni ekspektasi UX atau ada skenario spesifik yang benar-benar gagal.

## 5. Rencana Perbaikan (Prioritas)

### Prioritas 1 — Perbaiki akar masalah symlink storage (dampak terbesar, ke 4.1, 4.2, 4.4, 4.5)

- Pastikan file yang aktif di server sebagai `public_html/index.php` adalah persis salinan dari `deploy-tools/index.php` (punya baris `usePublicPath(__DIR__)` sebelum `handleRequest`). Verifikasi langsung via CWP File Manager.
- Perbaiki route `/sys/link` di `routes/web.php` agar tidak bergantung pada config `filesystems.links` yang sudah telanjur ke-cache. Cara paling andal: jalankan `storage:link` lewat proses PHP terpisah yang `usePublicPath()`-nya dipanggil sebelum Laravel bootstrap penuh (pola yang sama seperti `deploy-tools/artisan-run.php` baris 32), bukan di dalam closure route yang sudah berjalan setelah bootstrap.
- Tambahkan step di `.github/workflows/deploy.yml` yang otomatis mengunggah `deploy-tools/index.php` sebagai `public_html/index.php` di setiap deploy, supaya perbaikan ini permanen dan tidak lagi bergantung langkah manual yang mudah hilang.
- Setelah symlink diperbaiki, verifikasi dengan membuka salah satu URL foto (misalnya foto hero beranda) langsung di browser dan pastikan tidak lagi 404.

### Prioritas 2 — Perbaiki inkonsistensi RBAC tombol Detail Pengaduan (4.3)

- **Opsi A** (sesuai desain awal PRD): sembunyikan tombol "Detail" di `resources/views/dashboard/laporan/index.blade.php` baris 342-344 untuk role `bupati`, `wakil_bupati`, dan `sekda`, karena role tersebut memang tidak dirancang untuk melihat detail pengaduan individual.
- **Opsi B** (perluas akses baca-saja): tambahkan `bupati|wakil_bupati|sekda` ke middleware role pada route GET `/dashboard/complaints/{complaint}` di `routes/web.php` baris 109-111, dan pastikan blade `dashboard/complaints/show.blade.php` tidak menampilkan tombol aksi (verifikasi, disposisi, respon) untuk role tersebut — hanya mode baca.

Catatan: dokumen PERBAIKAN.docx sebelumnya sudah mencatat permintaan serupa ("Akun Bupati, wakil bupati sekda ditambah menu laporan kegiatan dan laporan pengaduan ... tetapi hanya bisa membaca"), jadi Opsi B kemungkinan lebih sesuai dengan kebutuhan aktual pengguna dibanding Opsi A.

### Prioritas 3 — Perbaikan cepat (low effort, high clarity)

- Ganti `@click` Alpine.js di `resources/views/dashboard/laporan/index.blade.php` baris 274 menjadi `onclick="document.getElementById('ttd-tab').click()"` (atribut HTML native) — perbaikan satu baris karena aksi ini murni DOM manipulation tanpa perlu reaktivitas Alpine.
- Buat/unggah file logo aplikasi SIPAPA yang sesungguhnya (bukan salinan logo pemda) sebagai `public/images/logo-sipapa.png`, atau jika kop surat laporan memang seharusnya memakai logo pemda sebagai identitas resmi pemerintah daerah, ganti nama file/variabel supaya tidak menyesatkan (dan siapkan aset logo aplikasi terpisah untuk keperluan lain seperti favicon/navbar).
- Verifikasi keberadaan file fisik manual book di server (lihat 4.8); jika hilang, minta Kominfo re-upload lewat menu yang sudah tersedia.
- Tambahkan `onchange="this.form.submit()"` pada input `date_from`/`date_to` di `dashboard/laporan/index.blade.php` baris 251 dan 406 supaya konsisten dengan halaman lain dan mengurangi kebingungan pengguna soal filter tanggal.

## 6. Catatan Keamanan Tambahan

Di luar cakupan bug yang dilaporkan, ditemukan catatan di `progress.md` yang masih relevan untuk ditindaklanjuti bila belum dilakukan:

- Token akses `deploy-tools/artisan-run.php` dan route `/sys/*` (`uwVW5Kx3Xfmv`) bersifat hardcoded dan tercatat pernah sama dengan `DB_PASSWORD` produksi. Jika belum pernah dirotasi, sangat disarankan mengganti `DB_PASSWORD` dan token ini dengan nilai berbeda, karena token URL mudah tercatat di access log server maupun riwayat browser.
- Route `/sys/clear`, `/sys/link`, `/sys/migrate`, `/sys/seed` di `routes/web.php` (baris 217-291) adalah endpoint publik yang hanya dilindungi token statis di query string, bukan autentikasi Laravel. Setelah kondisi hosting stabil dan mendukung akses via SSH atau file manager yang lebih baik, endpoint ini sebaiknya dinonaktifkan atau dipindah ke luar routing HTTP.

---

*Laporan ini disusun berdasarkan static analysis terhadap source code proyek. Beberapa temuan (4.8, 4.9) memerlukan verifikasi langsung di server produksi untuk konfirmasi akhir.*
