# Progress — SIPPM Madina

## Status Proyek

Seluruh fase inti (0-8) selesai. Fondasi (environment, Domain layer,
Database/Model/RBAC, Repository bindings), Application layer (UseCases),
event/broadcasting architecture, Presentation layer (API + Web penuh
dengan Blade + Bootstrap 5 + "Bright Skeuomorphism" styling), dan Laravel
Reverb (WebSocket real-time aktif) semua sudah berjalan dan diverifikasi
end-to-end secara manual. Sisa pekerjaan bersifat pengerasan produksi
(hardening) — lihat "Known Issues" dan "Next Steps".

## Update Terakhir

**2026-07-09 (lanjutan 6)** — Bug dilaporkan user: `SQLSTATE[22001]...
Data too long for column 'nip'` saat submit form TTD. Penyebab: asumsi di
entri sebelumnya ("migration `ttd_signatures` belum pernah dijalankan
manapun jadi aman diedit langsung") **TERBUKTI SALAH** — user memang
sempat menjalankan `php artisan migrate` di mesin sendiri (di antara
perbaikan "hapus instansi" dan "hapus batasan 18 digit"), jadi tabel nyata
sudah terlanjur dibuat dengan `nip VARCHAR(18)` dari versi migration saat
itu. Edit lanjutan ke file migration create (`32`, lalu `50`) tidak
berpengaruh apa-apa ke database yang sudah pernah di-migrate — migration
di Laravel hanya menjalankan `up()` sekali, tercatat di tabel `migrations`.
**Pelajaran**: jangan lagi asumsikan "migration belum pernah jalan" tanpa
verifikasi eksplisit (mis. minta user jalankan `php artisan migrate:status`)
ketika sesi kerja sendiri tidak punya akses PHP untuk mengecek — asumsi
itu cuma valid untuk migration yang benar-benar ditulis & dijalankan
dalam sesi yang sama.
Perbaikan: migration ALTER baru
`2026_07_09_210000_widen_nip_column_in_ttd_signatures_table.php` — pakai
raw SQL `ALTER TABLE ... MODIFY nip VARCHAR(50) NOT NULL` (bukan
`Schema::table()->change()`, karena `doctrine/dbal` tidak terpasang di
proyek ini), idempotent via `Schema::hasColumn()` guard. Migration create
awal juga ikut disamakan ke `50` (`down()` `ALTER TABLE ...->change()` versi lama)
supaya instalasi baru (`migrate:fresh`) langsung konsisten tanpa perlu dua
migration terpisah. `UpdateTtdRequest`: `nip` max disesuaikan `32` → `50`.
**User WAJIB jalankan `php artisan migrate` lagi** supaya migration ALTER
baru ini benar-benar diterapkan sebelum submit form TTD dicoba lagi.

**2026-07-09 (lanjutan 5)** — Dua perbaikan atas permintaan user:
1. Batasan "NIP wajib 18 digit" (`digits:18`) di `UpdateTtdRequest`
   dihapus — sekarang cuma `required|string|max:32` (bebas format/panjang,
   masih wajib diisi). Kolom `nip` di migration `ttd_signatures` ikut
   dilebarkan dari `string(18)` ke `string(32)` supaya nilai lebih panjang
   dari 18 karakter tidak silently truncated oleh DB (migration masih
   belum pernah dijalankan di environment manapun, jadi aman diedit
   langsung). `maxlength="18"` di input NIP (`dashboard/laporan/index.blade.php`)
   ikut dihapus. Preview TTD (`formattedNip` di Alpine) tidak diubah —
   tetap otomatis mem-format jadi kelompok "8 6 1 3" HANYA kalau NIP yang
   diketik kebetulan genap 18 digit, dan menampilkan apa adanya kalau
   tidak (sudah begitu dari awal, jadi tidak perlu logic baru).
2. Semua pesan error validasi di dashboard (bukan cuma form TTD — ini
   perbaikan global karena block `@if($errors->any())` sebelumnya ada di
   `layouts/dashboard.blade.php`, dipakai oleh SEMUA halaman dashboard)
   sekarang tampil sebagai dialog SweetAlert2 (icon error + daftar semua
   pesan sebagai `<ul>`), bukan lagi kotak `alert-danger` inline statis di
   atas konten — konsisten dengan pola SweetAlert2 yang sudah dipakai
   project ini untuk toast sukses (`session('status')`). Pesan
   di-escape (`e()`) lalu di-encode lewat `@json(...)` (pola sama seperti
   `@json(session('status'))` yang sudah ada) supaya aman dari XSS/karakter
   kutip.
`tests/Feature/Web/LaporanTest.php`: test validasi NIP 18-digit diganti
jadi `test_ttd_nip_no_longer_requires_exactly_18_digits` (assert NIP bebas
panjang tetap tersimpan, `assertSessionDoesntHaveErrors('nip')`), test
"missing required fields" disesuaikan (tidak lagi mengirim NIP invalid,
cukup NIP kosong untuk trigger `required`). Status verifikasi masih sama
seperti sebelumnya — lihat "Known Issues" (PHP CLI belum tersedia di
environment sesi ini, migration & test belum pernah benar-benar
dieksekusi).

**2026-07-09 (lanjutan 4)** — Field "Instansi" dihapus total dari fitur
TTD atas permintaan user (bukan cuma disembunyikan di form — dihilangkan
dari skema juga, karena migration `ttd_signatures` belum pernah dijalankan
di environment manapun sehingga aman diedit langsung tanpa migration
drop-column terpisah, sama seperti pola "migration dibuat & dijalankan
dalam sesi yang sama" di riwayat proyek ini sebelumnya). Perubahan:
kolom `instansi` dihapus dari migration `create_ttd_signatures_table`,
`TtdSignature::class` (`#[Fillable]`), `UpdateTtdRequest::rules()`, form
input di `dashboard/laporan/index.blade.php` (termasuk `x-data` Alpine),
baris instansi di blok tanda tangan `dashboard/laporan/export-pdf.blade.php`,
dan test terkait di `LaporanTest.php`. Preview TTD (lanjutan 3 di bawah)
sudah dari awal tidak menampilkan instansi jadi tidak perlu diubah. Status
verifikasi masih sama — lihat "Known Issues".

**2026-07-09 (lanjutan 3)** — Preview TTD live di sub-tab "Pengaturan TTD"
(`dashboard/laporan/index.blade.php`), atas permintaan user dengan mockup
gambar acuan. Card baru "Preview Tanda Tangan" di bawah form, meniru
persis tata letak blok tanda tangan di PDF (tempat+tanggal italic, jabatan
bold-uppercase, spasi kosong ruang ttd fisik, nama bold-uppercase, pangkat
italic, NIP format berkelompok "8 6 1 3 digit" berwarna biru) — TAPI
sengaja tidak menampilkan baris instansi terpisah, sesuai mockup yang
diberikan (jabatan sudah mewakili instansi). Reaktif langsung saat mengetik
di form (Alpine.js `x-data`/`x-model` di elemen pembungkus form+preview,
field input tetap punya `name` asli jadi submit form tidak berubah) —
tidak perlu tombol "refresh preview" terpisah. NIP di-format live di sisi
klien pakai getter `formattedNip` (regex strip non-digit, cuma diformat
kalau genap 18 digit, kalau belum lengkap tampil apa adanya). Nilai awal
di-inject aman ke Alpine pakai direktif Blade `@js(...)` (bukan interpolasi
string manual) supaya nama/jabatan yang mengandung tanda kutip tidak
merusak JS. Bagian ini murni tambahan UI di atas fitur Laporan+TTD yang
sudah ditulis sebelumnya — status verifikasi masih sama seperti sebelumnya
(lihat "Known Issues": migration `ttd_signatures` & test terkait masih
belum pernah dijalankan di environment ini karena PHP CLI tidak tersedia).

**2026-07-09 (lanjutan 2)** — Fitur baru atas permintaan user: halaman
"Laporan" (Kominfo-only) untuk mencetak laporan pengaduan + sub-tab
pengaturan TTD (tanda tangan). **BELUM diverifikasi jalan** — lihat catatan
penting di bawah.
- Tabel baru `ttd_signatures` (migration
  `2026_07_09_200000_create_ttd_signatures_table.php`): `nama_penandatangan`,
  `jabatan_penandatangan`, `pangkat` (nullable), `instansi`, `nip` (18
  digit). Pola "satu profil aktif": selalu diakses/diupdate lewat
  `updateOrCreate(['id' => 1], ...)` di controller — tidak ada tabel/flag
  tambahan untuk "aktif", tidak ada riwayat versi lama.
  Model: `App\Infrastructure\Persistence\Eloquent\Models\TtdSignature`.
- `App\Http\Controllers\Web\Dashboard\LaporanController` (baru): `index()`
  (filter+tabel), `exportPdf()`, `exportExcel()`, `updateTtd()`. Filter
  pengaduan: status, tujuan (`opd:{id}`/`camat:{id}`, pola sama persis
  dengan filter Pengaduan FR-19), dan **hari/bulan/tahun** — ketiganya
  independen satu sama lain (bukan date-range). Poin penting yang beda dari
  filter lain di project ini: **"Hari" = hari dalam seminggu
  (Senin–Minggu), BUKAN tanggal 1-31 dalam bulan** (dikonfirmasi eksplisit
  oleh user) — diimplementasikan `whereRaw('WEEKDAY(created_at) = ?', ...)`
  dengan dropdown value 0-6 (Senin=0..Minggu=6, cocok persis urutan
  `WEEKDAY()` MySQL). Logic filter di-extract ke method privat
  `filteredComplaints()` supaya tabel di layar dan hasil export PDF/Excel
  selalu memakai filter yang identik.
  Tidak ada Policy baru — kominfo-only cukup lewat middleware `role:kominfo`
  di `routes/web.php`, konsisten dengan `StatisticsController` dan
  `ComplaintDashboardController::index` yang juga tidak punya Policy untuk
  aksi index/export.
- `App\Exports\LaporanPengaduanExport` (baru, `FromCollection`+
  `WithHeadings`+`WithTitle`) — constructor menerima Collection pengaduan
  yang sudah difilter plus peta nama Opd/Kecamatan (`pluck('name','id')`)
  supaya resolusi nama "Tujuan" tidak N+1 query per baris.
- `resources/views/dashboard/laporan/export-pdf.blade.php` (baru): laporan
  PDF landscape (`setPaper('a4','landscape')`) dengan kop surat (logo
  `public_path('images/logo-madina.png')` — WAJIB `public_path()` bukan
  `asset()` karena DomPDF render server-side tanpa konteks HTTP), ringkasan
  filter aktif, tabel data, dan blok tanda tangan resmi (tempat+tanggal,
  jabatan+instansi, spasi kosong untuk ttd fisik+cap, nama digarisbawahi,
  pangkat, NIP) diambil dari `TtdSignature::query()->find(1)` — kalau belum
  pernah diisi, field ditampilkan `-` (tidak error).
- `resources/views/dashboard/laporan/index.blade.php` (baru): dua
  Bootstrap 5 native tab (`nav-tabs`/`tab-content` — tab pertama di
  project ini, sebelumnya tidak ada) — "Data Laporan" (filter bar persis
  urutan mockup dari user: search, Status, Tujuan, Hari, Bulan, Tahun,
  lalu tombol Cari/Reset/TTD/PDF/Excel; tombol "TTD" `type="button"` +
  `data-bs-toggle="tab"`, BUKAN submit, cuma pindah ke tab satunya) dan
  "Pengaturan TTD" (form 5 field, pre-filled dari `$ttd` kalau ada).
  Link PDF/Excel membawa query string filter aktif (`http_build_query(request()->query())`)
  supaya hasil export selalu sama dengan yang tampil di layar.
- Route baru di grup `role:kominfo` (`routes/web.php`): `GET
  /dashboard/laporan`, `GET /dashboard/laporan/export-pdf`, `GET
  /dashboard/laporan/export-excel`, `POST /dashboard/laporan/ttd`. Link
  sidebar baru "Laporan" ditambahkan di blok "Administrasi"
  (`dashboard/partials/sidebar-nav.blade.php`, setelah "Audit Log").
- Test baru `tests/Feature/Web/LaporanTest.php` (7 method: akses
  kominfo/non-kominfo, filter status+tujuan, filter hari independen dari
  bulan/tahun, simpan+update TTD single-row, validasi NIP 18 digit,
  export PDF/Excel).

**CATATAN PENTING — belum diverifikasi jalan**: environment kerja sesi ini
**tidak punya PHP CLI terpasang/di-PATH** (berbeda dari sesi-sesi
sebelumnya yang memakai Laragon — `C:\laragon` tidak ditemukan sama sekali
di environment ini), jadi `php artisan migrate` (migration `ttd_signatures`
belum benar-benar dijalankan ke database) dan `php artisan test` **belum
bisa dieksekusi**. Sebelum dipakai, sesi berikutnya (di environment yang
punya PHP/Laragon aktif) WAJIB: (1) `php artisan migrate` untuk membuat
tabel `ttd_signatures`, (2) `php artisan test --filter=LaporanTest` untuk
verifikasi otomatis, (3) manual QA: login `kominfo@demo.test`, cek filter
kombinasi, isi form TTD lalu reload untuk pastikan data tersimpan, cek PDF
(kop surat + blok ttd) dan Excel, dan pastikan `opd@demo.test`/role lain
ditolak 403 di `/dashboard/laporan`.

**2026-07-09 (lanjutan)** — Polishing visual halaman publik + auth, murni
UI (tidak ada perubahan business rule/route/migration), atas permintaan
user. Belum di-commit — lihat "Known Issues".
- **Beranda publik** (`public/home.blade.php`): section baru "Kenapa SIPPM
  Madina" (3 value-prop card) di bawah hero, section baru "Kegiatan &amp;
  Dokumentasi Terbaru" (3 kegiatan terpublikasi terbaru, card sama dengan
  `/kegiatan`) sebelum footer, dan CTA band penutup baru
  (`.btn-sippm-gov-primary`/`.btn-sippm-gov-secondary`). Ditopang query baru
  `recentActivities` di `HomeController::index()`
  (`Activity::where(status=DIPUBLIKASIKAN)->with(['documentations','actor'])->latest('date')->take(3)`).
- Style `.activity-card*` yang sebelumnya inline di `@push('styles')`
  `public/activities.blade.php` dipindah ke `resources/css/app.css` supaya
  section "Kegiatan Terbaru" di beranda bisa reuse tanpa duplikasi CSS.
- Animasi scroll-reveal baru: class `.reveal` (opacity+translateY, transisi
  di-trigger `.is-visible`) dipasang di kartu-kartu beranda, kartu
  aktivitas, dan kartu form auth. Digerakkan `IntersectionObserver` baru di
  `resources/js/app.js` — fallback aman: elemen tetap ada di DOM (bukan
  `display:none`), langsung `.is-visible` kalau `prefers-reduced-motion`
  atau browser tidak dukung `IntersectionObserver`, dan force-reveal semua
  elemen setelah 2 detik sebagai safety net kalau script lain error.
- Toggle show/hide password baru (`data-toggle-password="#id"`, listener
  generik di `app.js`) dipasang di `auth/{login,register}.blade.php`.
  Kartu auth juga direstyle (`.sippm-auth-card`, heading+subheading baru,
  tombol lebih besar).
- Type-scale sitewide dinaikkan (root `17px`, heading `h1-h6` dapat skala
  eksplisit lewat `clamp()`) di `resources/css/app.css` karena banyak
  halaman publik sebelumnya sengaja pakai `.h4`/`.h5`/`.h6`/`.small` yang
  terasa terlalu kecil untuk portal pemerintah — beberapa Blade
  (`public/{activities,track}.blade.php`, `layouts/app.blade.php`) sekalian
  dinaikkan kelas heading-nya (mis. `.h4`→`.h3`) dan `font-size` inline kecil
  yang sekarang mubazir dihapus.
- Bug kecil ditemukan & diperbaiki sekalian: `dashboard/statistics/index.blade.php`
  memanggil `$complaintsByCategory->keys()`/`->values()` (method
  `Collection`), tapi `StatisticsController::index()` dan
  `Api\DashboardController::statistics()/performance()` sekarang eksplisit
  `->toArray()` hasil `pluck()` sebelum masuk payload — Blade diikutkan
  memakai `array_keys()`/`array_values()` native. `min-width:260px` +
  `flex-shrink:0` ditambah ke `.sippm-sidebar` (`layouts/dashboard.blade.php`)
  supaya sidebar tidak collapse saat konten tabel lebar (DataTables).
- `php artisan test`: 25 test tetap **passed** (tidak ada test baru — murni
  perubahan visual/CSS/JS tanpa business rule baru).

**2026-07-09** — Audit PRD menyeluruh (bukan cuma percaya progress.md,
tapi verifikasi langsung ke kode via subagent Explore) menemukan 3 gap
nyata, semua diperbaiki dan diverifikasi lewat test otomatis baru:
1. **FR-23/FR-24**: `ActivityDashboardController::index()` sebelumnya
   cuma auto-scope ke OPD/Kecamatan milik user login, tanpa filter
   pilih-bebas. Ditambah filter `status`, `target` (encoded
   `opd:{id}`/`kecamatan:{id}`, hanya untuk role yang melihat semua
   kegiatan — Kominfo/Bupati/Wabup/Sekda, karena OPD/Camat sudah
   auto-scoped), dan `date_from`/`date_to` (kolom `date`, bukan
   `created_at`) — pola yang sama persis dengan filter Pengaduan FR-19,
   plus filter bar baru di `dashboard/activities/index.blade.php`.
   Catatan: `activities.actor_type` pakai `'opd'`/`'kecamatan'`, BEDA
   dari `complaints.target_type` yang pakai `'camat'`.
2. **FR-36/FR-37**: `RecordAuditLog` sebelumnya hardcode `old_data =
   null` di semua baris — tidak pernah merekam state sebelum perubahan,
   padahal FR-37 eksplisit minta "data yang berubah". Diperbaiki dengan
   menambah param `previousStatus` ke 5 domain event
   (`ComplaintVerified/Disposed/Handled/Resolved`, `ActivityPublished`)
   yang diisi dari entity pre-mutation yang sudah ada di scope UseCase
   (entity immutable, `withStatus()` return instance baru — status lama
   tidak pernah hilang, cuma belum pernah diteruskan ke event). Selain
   itu, aksi "kelola pengguna" (create/edit/nonaktifkan akun) di
   `UserManagementController` tidak pernah tercatat ke `audit_logs` sama
   sekali meski disebut eksplisit di FR-36 — ditulis langsung di
   controller (`recordUserAudit()`, action `user_created`/
   `user_updated`/`user_activated`/`user_deactivated`) karena controller
   ini sudah menyimpang dari pola UseCase (Eloquent langsung), sama
   seperti `RecordLoginAuditLog` menulis langsung untuk event auth
   bawaan Laravel.
3. **FR-34**: badge unread notifikasi di `layouts/dashboard.blade.php`
   sebelumnya dihitung client-side dari daftar 20 notifikasi terbaru
   saja (`per_page=20`), jadi under-count kalau user punya >20 notifikasi
   belum dibaca. `NotificationRepositoryInterface::countUnreadForUser()`
   sudah ada tapi tidak pernah dipanggil — sekarang dipanggil di
   `NotificationWebController::index()` dan diteruskan sebagai
   `unread_count` di response JSON; Alpine `notificationBell()` di
   `layouts/dashboard.blade.php` dipakai sebagai sumber kebenaran
   (bukan lagi `items.filter(...)` yang cuma menghitung 20 item
   terpaginasi).

4 test baru ditambahkan (`ActivityWorkflowTest::test_activity_index_filters_by_status_and_target`,
audit-log assertions di `ActivityWorkflowTest`/`ComplaintWorkflowTest`/
`UserManagementTest`, dan `NotificationWebControllerTest` baru).
`php artisan test`: 25 test (4 baru) tetap **passed**.

**2026-07-06 (lanjutan 3)** — Atas permintaan eksplisit: fitur "Laporan
Kejadian" khusus role camat (entri sebelumnya di bawah) **dicopot penuh**
— tidak jadi dipakai. Dikembalikan bersih ke kondisi sebelum fitur itu
ada, bukan sekadar dinonaktifkan:
- Migration `add_reporting_fields_to_activities_table` di-rollback lalu
  file migration-nya dihapus (bukan ditambah migration baru untuk
  drop-column, karena migration itu sendiri dibuat & dijalankan dalam
  sesi yang sama sehingga aman dihapus langsung tanpa jejak).
  Kolom `reporter_name`/`desa_id`/`target_type`/`target_id` di
  `activities` sudah tidak ada lagi.
- Dihapus: enum `App\Domain\Activity\ValueObjects\ActivityTargetType`.
- Dikembalikan ke versi sebelum fitur: `Domain\Activity\Entities\Activity`,
  `Infrastructure\...\Models\Activity` (fillable/casts/relasi `desa()`/
  method `targetLabel()` dicopot), `EloquentActivityRepository`,
  `SubmitActivityDTO`, `SubmitActivityUseCase`, `SubmitActivityRequest`
  (validasi kembali satu set aturan untuk OPD & Camat, tanpa percabangan
  role), `Web\Dashboard\ActivityDashboardController`,
  `Api\ActivityController`, `ActivityResource`,
  `dashboard/activities/{create,index}.blade.php`.
- Form "Input Kegiatan" kembali sama persis untuk OPD maupun Camat:
  judul, deskripsi, tanggal, lokasi, dokumentasi foto (JPG/PNG maks
  5 MB) — tidak ada lagi nama pelapor/desa/tujuan laporan.
- `ActivityWorkflowTest` dikembalikan ke versi semula. Test regresi
  lokalisasi pesan validasi (`test_oversized_documentation_upload_...`,
  entri sebelumnya) TETAP dipertahankan karena itu perbaikan terpisah
  yang tidak terkait fitur ini.
- Model/CRUD Desa (dari fitur Data Wilayah sebelumnya) tidak disentuh —
  hanya relasi `Activity::desa()` yang ditambahkan khusus untuk fitur ini
  yang dicopot.
`php artisan test`: 21 test tetap **passed**.

**2026-07-06 (lanjutan 2)** — Bug dilaporkan user: submit form "Input
Kegiatan" role OPD dengan lampiran melebihi batas ukuran menampilkan
pesan error mentah `validation.max.filevalidation.max.file` alih-alih
kalimat yang bisa dibaca. Akar masalah ternyata jauh lebih luas dari satu
pesan itu saja: proyek ini memakai `APP_LOCALE=id` (dan
`APP_FALLBACK_LOCALE=id` — tanpa fallback Inggris), tapi **tidak pernah
punya file `lang/id/validation.php` sama sekali** (folder `lang/` cuma
berisi terjemahan vendor `spatie/laravel-backup`) — jadi SETIAP pesan
error validasi bawaan Laravel di seluruh aplikasi (login, registrasi,
pengaduan, kegiatan/laporan kejadian, kelola pengguna, dst.) sebenarnya
selalu menampilkan key mentah, bukan cuma kasus upload ini.
Diperbaiki dengan `php artisan lang:publish` (menerbitkan
`lang/en/{validation,auth,pagination}.php` bawaan Laravel sebagai
acuan struktur), lalu ditulis penuh terjemahan Indonesia di
`lang/id/validation.php` (seluruh rule Laravel + `attributes` untuk
nama field yang dipakai di seluruh form aplikasi supaya pesan
menyebut "nama pelapor"/"desa" dst, bukan `reporter_name`/`desa_id` mentah)
serta `lang/id/{auth,pagination}.php` (yang terakhir dipakai
`Paginator::useBootstrapFive()` di setiap tabel berpaginasi — sama-sama
akan menampilkan key mentah "pagination.previous"/"pagination.next" tanpa
file ini). `lang/en/passwords.php` hasil publish dihapus lagi karena
fitur reset password via email sudah dicopot total (lihat entri
sebelumnya). Regresi ditambahkan di `ActivityWorkflowTest`
(`test_oversized_documentation_upload_shows_localized_error_message`):
submit lampiran 6000 KB (batas OPD 5120 KB) dan pastikan pesan error
tidak mengandung `"validation."` mentah. `php artisan test`: 21 test
(1 baru) tetap **passed**.

**2026-07-06 (lanjutan)** — Form "Input Kegiatan" milik role camat diganti
jadi "Laporan Kejadian" sesuai spesifikasi field baru: Nama Pelapor, Nama
Kecamatan (readonly, dari akun camat sendiri), Nama Desa (select, hanya
desa milik kecamatan camat tsb), Waktu Kejadian, Tujuan Laporan
(Bupati/Wakil Bupati/Sekda/OPD), Uraian Laporan (rich text, sudah ada
sejak sebelumnya), dan Lampiran (gambar/video/dokumen, maks 15 MB).
Form OPD ("Input Kegiatan" biasa: judul/deskripsi/tanggal/lokasi, gambar
maks 5 MB) **tidak berubah** — keduanya tetap berbagi satu form request
(`SubmitActivityRequest`), yang sekarang bercabang aturan validasi
berdasarkan role (`hasRole('camat')`).
- Migration baru menambah 4 kolom nullable ke `activities`:
  `reporter_name`, `desa_id` (FK ke `desas`, nullOnDelete), `target_type`,
  `target_id` — nullable karena OPD tidak pernah mengisinya.
- Enum baru `App\Domain\Activity\ValueObjects\ActivityTargetType`
  (bupati/wakil_bupati/sekda/opd) — sengaja dibuat terpisah dari
  `Complaint\ValueObjects\TargetType` yang sudah ada (meski isinya mirip)
  supaya modul Activity tidak bergantung ke domain Complaint.
- "Tujuan Laporan" dikirim sebagai satu field `target` dari `<select>`
  tunggal, di-encode `"opd:{id}"` untuk OPD atau `"bupati"`/
  `"wakil_bupati"`/`"sekda"` untuk pejabat — pola yang sama persis dengan
  filter "tujuan" di `ComplaintDashboardController` (bukan pasangan
  dropdown type+id terpisah). Parsing `target` jadi `target_type`/
  `target_id` dilakukan lewat 2 method baru di `SubmitActivityRequest`
  (`targetType()`/`targetId()`), dipakai bersama oleh web dashboard
  maupun API controller supaya tidak duplikasi logic parsing.
- Field "Judul Kegiatan" TIDAK ada di form camat (sesuai spesifikasi) —
  `title` tetap wajib diisi di kolom DB (dipakai listing/feed publik) jadi
  di-generate otomatis oleh controller: `"Laporan Kejadian - {desa},
  Kec. {kecamatan}"`. `location` juga di-generate otomatis dari
  desa+kecamatan yang sama supaya kolom "Lokasi" di tabel Kegiatan tetap
  terisi wajar tanpa perlu field lokasi terpisah di form.
- `Activity::targetLabel()` (accessor baru): resolve nama OPD dari
  `target_id` kalau `target_type=opd`, atau label tetap (Bupati/dst) untuk
  ketiganya. Dipakai di kolom baru "Tujuan" pada
  `dashboard/activities/index.blade.php` (juga ditambah kolom "Pelapor").
- `ActivityResource` (API) diperluas dengan `reporter_name`, `desa_id`,
  `target_type`, `target_label`, `target_id`.
- Bug tes ditemukan sekalian saat verifikasi: `ActivityWorkflowTest`
  memakai `Activity::query()->firstOrFail()` untuk mengambil record yang
  baru dibuat — ternyata `DummyReportSeeder` sudah menyeed 2 activity
  lain duluan (OPD & Camat), jadi `firstOrFail()` diam-diam mengambil
  record seeder, bukan punya test (kebetulan tidak ketahuan sebelumnya
  karena assertion lama tidak memeriksa field yang dibedakan). Diperbaiki
  dengan filter eksplisit `where('reporter_name', ...)`.
  Diverifikasi end-to-end via curl sebagai `camat@demo.test`: form
  menampilkan field & opsi (desa milik kecamatan sendiri, opsi tujuan
  Bupati/Wabup/Sekda + optgroup semua OPD) dengan benar; submit dengan
  tujuan `opd:1` tersimpan benar (title/location ter-generate, target_type
  `opd`, target_label resolve ke nama OPD asli). `php artisan test`: 20
  test tetap **passed**.

**2026-07-06** — Atas permintaan eksplisit: fitur berbasis pengiriman
email (verifikasi email saat registrasi/FR-03, lupa password via
email/FR-07) **dicopot total** — proyek ini tidak jadi memakai mailer apa
pun. Field `email` tetap ada di form registrasi/login (tetap dipakai
sebagai identitas login), hanya fungsinya yang dihapus:
- Dihapus: `ForgotPasswordController`, `ResetPasswordController`,
  `EmailVerificationController` beserta view-nya
  (`auth/{forgot-password,reset-password,verify-email}.blade.php`) dan
  rute terkait di `routes/web.php` (`/forgot-password`,
  `/reset-password/{token}`, `/email/verify*`).
- `User` model tidak lagi `implements MustVerifyEmail`
  (trait+contract dicopot); middleware `verified` dicopot dari rute
  `/pengaduan/*` — masyarakat bisa langsung mengajukan pengaduan begitu
  registrasi tanpa syarat verifikasi email.
- `RegisterController` tidak lagi memicu event `Registered` (yang
  sebelumnya memicu `SendEmailVerificationNotification`).
  `AppServiceProvider::boot()` tidak lagi mendaftarkan listener tsb.
- Link "Lupa kata sandi?" dicopot dari `auth/login.blade.php`.
- Rate limiting login (NFR-07) dan audit log
  login/logout/login_failed (FR-08) **tetap dipertahankan** — keduanya
  tidak bergantung pada pengiriman email.
`php artisan test`: 20 test tetap **passed**.

**2026-07-05 (lanjutan 6)** — Audit kepatuhan penuh terhadap PRD (bukan
sekadar percaya `progress.md`, tapi pengecekan kode langsung) menemukan 8
celah nyata, semua diperbaiki dan diverifikasi end-to-end (curl
cookie-session + query DB langsung):
1. **RBAC**: Bupati/Wakil Bupati/Sekda sebelumnya tidak punya akses web ke
   "Lihat Laporan Kegiatan" (pelanggaran matriks PRD §4.2) — rute
   `GET /dashboard/activities` dipisah dari rute create-nya, middleware
   `role:` diperluas mencakup ketiga role tsb; sidebar nav dipecah supaya
   mereka lihat menu "Kegiatan" tanpa "Pengaduan". Diverifikasi:
   `bupati@demo.test` → `/dashboard/activities` = 200, `/dashboard/complaints`
   tetap 403 (benar).
2. **FR-19**: filter pengaduan diperluas dari status+search saja menjadi
   +kategori, +rentang tanggal, +tujuan disposisi (OPD/Kecamatan, encoded
   `opd:{id}`/`camat:{id}` di satu `<select>`). Bug ditemukan saat
   verifikasi manual: `$request->string('target')` mengembalikan
   `Illuminate\Support\Stringable`, bukan `string` — `str_contains()`/
   `explode()` native PHP melempar `TypeError`. Diperbaiki dengan cast
   `(string)` eksplisit di `ComplaintDashboardController`.
3. **NFR-07**: `RateLimiter::for('login', ...)` (5x/menit per
   email+IP) + middleware `throttle:login` di rute web `/login` maupun API
   `/api/v1/login`. Callback custom: request API/JSON dapat 429 JSON,
   request web dapat redirect-back dengan pesan "Terlalu banyak percobaan
   masuk...". Diverifikasi: percobaan ke-6 dengan kredensial salah
   langsung diblokir dengan pesan tsb tampil di halaman login.
4. **FR-08**: listener baru `RecordLoginAuditLog` (sync, bukan queued)
   didaftarkan untuk event native Laravel `Login`/`Logout`/`Failed`,
   menulis ke `audit_logs` (action `login`/`logout`/`login_failed`).
   Diverifikasi via query DB: baris `login` (dengan `model_id` user yang
   benar) dan `login_failed` (saat percobaan gagal) benar-benar tercatat.
5. **FR-07**: alur lupa password lengkap pakai password broker bawaan
   Laravel (`Password::sendResetLink`/`Password::reset`) — halaman
   `/forgot-password` dan `/reset-password/{token}`, rute `password.reset`
   dinamai sesuai konvensi notifikasi default Laravel. Diverifikasi
   end-to-end: submit email → link reset diekstrak dari
   `storage/logs/laravel.log` (`MAIL_MAILER=log`) → set password baru →
   berhasil login dengan password baru → password didemo dikembalikan ke
   semula setelahnya.
6. **FR-03**: verifikasi email saat registrasi pakai kontrak native
   Laravel (`MustVerifyEmail` di model `User`, listener bawaan
   `SendEmailVerificationNotification` pada event `Registered`). Rute
   `/pengaduan/*` sekarang juga di-guard middleware `verified` (akun demo
   seeder aman karena `DemoUserSeeder` sudah set `email_verified_at`).
   Diverifikasi end-to-end: registrasi akun baru → `/pengaduan` diblokir
   (302) → link verifikasi diekstrak dari log → klik → `/pengaduan` lolos
   (200). Akun uji dihapus lagi setelah verifikasi selesai.
7. **NFR-13/FR-39**: `spatie/laravel-backup` sudah ter-install tapi
   `config/backup.php` belum pernah dipublish dan tidak pernah dijadwalkan
   — dipublish, dijadwalkan (`backup:run` 01:00 + `backup:clean` 01:30
   harian, `Schedule::command()` di `routes/console.php`). Ditemukan &
   diperbaiki masalah lingkungan Windows/Laragon: `mysqldump` tidak ada di
   PATH → ditambahkan `dump.dump_binary_path` di `config/database.php`
   (dari env `MYSQLDUMP_PATH`, default kosong supaya tidak berdampak di
   Linux produksi yang biasanya sudah punya `mysqldump` di PATH).
   Diverifikasi: `php artisan backup:run --only-db` benar-benar
   menghasilkan file zip nyata di `storage/app/private/SIPPM Madina/`.
8. **NFR-16**: `Cache::remember()` TTL 60 detik dipasang di kedua endpoint
   statistik (`StatisticsController::index/performance` web,
   `DashboardController::statistics/performance` API) — TTL pendek dipilih
   ketimbang cache tags/invalidasi manual karena `CACHE_STORE=database`
   tidak mendukung cache tags. Diverifikasi: baris `dashboard.statistik`/
   `dashboard.kinerja` benar-benar muncul di tabel `cache` setelah
   endpoint diakses.

`php artisan test`: 20 test tetap **passed** (tidak ada test baru — semua
perbaikan di atas adalah RBAC/middleware/config/listener native Laravel
tanpa business rule baru yang butuh unit test, coverage lewat verifikasi
manual end-to-end di atas).

**2026-07-05 (lanjutan 5)** — CRUD Data Wilayah (kominfo-only): Data OPD,
Data Kecamatan, Data Desa (`/dashboard/{opd,kecamatan,desa}`). Desa adalah
entitas baru (migration `desas`, FK `kecamatan_id` cascade-delete, model
`Desa belongsTo Kecamatan`) — setiap desa wajib terikat ke satu kecamatan,
index-nya bisa difilter per kecamatan. OPD/Kecamatan pakai model yang
sudah ada (sebelumnya cuma diisi lewat seeder, sekarang punya UI CRUD
penuh). Delete OPD/Kecamatan dijaga guard anti-orphan: ditolak dengan
pesan jelas kalau masih punya user/kegiatan/pengaduan/disposisi terkait
(polymorphic reference tanpa FK asli — lihat penjelasan sebelumnya soal
`model_has_roles`), supaya tidak meninggalkan data yatim. Nav sidebar
dapat section baru "Data Wilayah". Diverifikasi end-to-end via curl
sebagai kominfo: create/edit/delete OPD & Desa berhasil, filter Desa per
kecamatan berhasil, dan guard anti-hapus terbukti menolak penghapusan
Kecamatan Panyabungan (masih dipakai `camat@demo.test`) dengan pesan
error yang benar. `php artisan test`: 20 test tetap **passed** (tidak ada
test baru ditambahkan untuk modul ini — murni CRUD referensi tanpa business
rule, coverage cukup lewat verifikasi manual di atas).

**2026-07-05 (lanjutan 4)** — Atas permintaan eksplisit: notifikasi
in-app (`PersistComplaintNotification`, `PersistActivityNotification`)
diubah dari queued job (`ShouldQueue`) menjadi listener sync biasa —
notifikasi sekarang langsung tersimpan ke tabel `notifications` saat
event terjadi, TIDAK menunggu `php artisan queue:work`. Audit log
(`RecordAuditLog`) dan broadcast WebSocket (event `ShouldBroadcast`)
tetap queued (non-blocking, boleh retry di background). Diverifikasi
langsung via tinker: submit pengaduan baru tanpa queue worker aktif sama
sekali → notifikasi kominfo langsung muncul di tabel (2 baris baru),
hanya job broadcast WebSocket yang tetap masuk antrian. `AGENTS.md`
diperbarui supaya dokumentasi konsisten dengan keputusan baru ini.
`php artisan test`: 20 test tetap **passed**.

**2026-07-05 (lanjutan 3)** — Ukuran grafik Chart.js di
`dashboard/statistics/{index,performance}.blade.php` dibatasi konsisten
(wrapper `.sippm-chart-box`, tinggi 240px, `maintainAspectRatio:false`)
— sebelumnya bisa membesar tanpa batas tergantung lebar kolom. Seluruh
tampilan tanggal/waktu diseragamkan ke format Indonesia: `APP_LOCALE`/
`APP_FALLBACK_LOCALE` diganti `en`→`id`, `Carbon::setLocale('id')`
dipanggil di `AppServiceProvider::boot()`, semua `->format(...)` di view
diganti `->translatedFormat(...)` (nama bulan Indonesia: "05 Agt 2026",
"05 Desember 2026, 14:30" — dikonfirmasi via tinker, bukan fallback
Inggris). Waktu sudah 24 jam sejak awal (`H`, bukan `h`/`g` 12-jam) jadi
tidak perlu diubah, hanya diseragamkan formatnya. `php artisan test`: 20
test tetap **passed**.

**2026-07-05 (lanjutan 2)** — SweetAlert2 dialog box dipasang untuk semua
proses tambah/login/logout/konfirmasi/edit/nonaktifkan (padanan "hapus" —
tidak ada tombol hapus literal di UI, hanya nonaktifkan akun pengguna):
toast sukses otomatis dari `session('status')` (dipakai ulang di SEMUA
controller yang sudah flash pesan itu, tanpa ubah controller satu-persatu)
di kedua layout; dialog konfirmasi (`data-confirm` + listener global di
`resources/js/app.js`) di form logout, verifikasi/tolak pengaduan,
disposisi, penanganan, jawaban resmi, verifikasi/publikasi kegiatan, dan
nonaktifkan/aktifkan pengguna. Login sukses & logout kini flash pesan
sambutan/perpisahan. Dua bug nyata ditemukan & diperbaiki saat verifikasi:
(1) flash `session('status')` hilang saat redirect setelah login untuk
role internal karena `/dashboard` sendiri melakukan redirect tambahan ke
`/dashboard/complaints` dst — diperbaiki dengan re-flash di
`DashboardHomeController`; (2) **bug disposisi nyata dilaporkan user**:
checkbox OPD/Kecamatan yang tidak dicentang tetap mengirim hidden input
`type` kosong (validasi gagal "field is required"), dan setelah itu
diperbaiki, `DisposeComplaintUseCase` ternyata membangun array
`$targetTypes` dengan index sekuensial dari 0 lalu membacanya kembali
pakai index asli dari form (bisa bolong/tidak mulai dari 0) — error
`Undefined array key`. Diperbaiki di kedua lapisan (Blade: hidden input
`disabled` saat unchecked; UseCase: `$targetTypes` diindeks sama persis
dengan `$dto->targets`). Test regresi baru ditambahkan
(`test_disposition_with_sparse_non_zero_target_index_succeeds`).
`php artisan test`: 20 test **passed** (sebelumnya 19).

**2026-07-05 (lanjutan)** — Refactor visual total halaman publik + layout
dashboard, mengambil struktur (bukan warna) dari situs dinas sejenis
(dinkes.madina.go.id): navbar publik 2-lapis (identity bar + page-header
berlogo+pencarian tiket) + nav sticky dengan shadow-on-scroll, footer 3
kolom (brand/sosial media, tautan cepat, kontak), section statistik nyata
di beranda (total pengaduan/selesai/kegiatan dipublikasikan, animasi
count-up vanilla JS), thumbnail dokumentasi + badge OPD/Kecamatan di feed
kegiatan publik (`/kegiatan`, sebelumnya polos tanpa gambar), sidebar
dashboard kini punya versi mobile (Bootstrap 5 offcanvas — sebelumnya
`d-none d-lg-flex` tanpa fallback sama sekali, bug murni). Lambang resmi
Kabupaten Mandailing Natal (`public/images/logo-madina.png`, dipindah
dari folder `foto/` yang diberikan user lalu dihapus) dipasang di semua
header/sidebar/footer/halaman auth. Dua bug lama ikut diperbaiki:
`Paginator::useBootstrapFive()` belum pernah dipanggil (pagination
sebelumnya diam-diam bermarkup Tailwind di proyek yang sudah menghapus
Tailwind) dan symlink `public/storage` belum pernah dibuat
(`php artisan storage:link`, lampiran pengaduan sebelumnya broken image).
CSS font ganda (`--font-sans` Instrument Sans tak terpakai vs override
inline Inter) dibersihkan; `bunny()` font plugin dicopot dari
`vite.config.js`. `php artisan test`: 19 test tetap **passed** (satu test
scaffold lama, `ExampleTest`, diperbaiki sekalian — hilang
`RefreshDatabase` sehingga gagal begitu `/` mulai query DB untuk
statistik).

**2026-07-05** — Fase 7-8 selesai (Presentation layer web penuh:
AdminLTE-style Bootstrap 5 dashboard, seluruh form aksi pengaduan/
kegiatan/pengguna, dashboard statistik Chart.js + export PDF/Excel,
notification bell, Laravel Reverb + Echo real-time). Seluruh alur
end-to-end (pengaduan: ajukan → verifikasi → disposisi → tangani → jawab
resmi; kegiatan: input → verifikasi → publikasi; kelola pengguna; live
notification via WebSocket) diverifikasi manual dengan `php artisan
serve` + curl (cookie-based session) memakai akun demo seeder, DAN via
19 automated feature/unit test (`php artisan test`, semua passed). Lihat
detail di bagian "Riwayat" di bawah.
Sebelumnya: Fase 4-6 selesai (Application layer UseCases+DTOs,
Broadcasting/Event architecture, Presentation layer API+Web tipis).
Fase 0-3 selesai (environment & fondasi, Domain layer, database/model/
RBAC, repository implementations + binding).

## Modul Autentikasi dan RBAC

- [x] Migrasi tabel users (kolom tambahan: nik, phone, is_active,
      consent_at, opd_id, kecamatan_id)
- [x] Migrasi tabel roles/permissions/model_has_roles/model_has_permissions/
      role_has_permissions (via Spatie Laravel Permission)
- [x] Setup Spatie Laravel Permission (package, migration, middleware
      alias `role`/`permission`)
- [x] Seed 7 role x 14 permission matrix (`RolePermissionSeeder`)
- [x] Seed data referensi Opd (`OpdSeeder`) dan Kecamatan (`KecamatanSeeder`)
- [x] Seed 1 akun demo per peran (`DemoUserSeeder`)
- [x] Middleware `EnsureAccountIsActive` (alias `active`)
- [x] Registrasi & login masyarakat (form Bootstrap + controller + view,
      `resources/views/auth/{login,register}.blade.php`)
- [x] Login internal (Kominfo/OPD/Camat/Bupati/Wabup/Sekda) — satu form
      login untuk semua role, redirect berbeda tidak diperlukan karena
      sidebar dashboard menyesuaikan role
- [x] Middleware RBAC diterapkan per rute/grup rute sesuai matriks role
      (`routes/web.php`, `routes/api.php`)
- [x] Policy diisi penuh (ComplaintPolicy, ActivityPolicy, UserPolicy,
      AuditLogPolicy) dan dipanggil di controller Web via
      `$this->authorize()` (`AuthorizesRequests` trait ditambahkan ke
      `App\Http\Controllers\Controller`)
- [x] Halaman kelola pengguna (create/edit/aktivasi/nonaktivasi akun) —
      `UserManagementController` + `resources/views/dashboard/users/*`
- [x] Consent UU PDP di form registrasi (checkbox wajib di
      `auth/register.blade.php`, divalidasi `RegisterRequest`)

## Modul Pengaduan

- [x] Migrasi tabel complaints, complaint_attachments,
      complaint_status_histories, dispositions, complaint_handlings,
      complaint_responses
- [x] Domain layer: entity, value objects (ComplaintStatus, TargetType,
      TicketNumber), repository interfaces, business rules
      (TicketNumberGeneratorRule, DispositionMustTargetOpdOrCamatRule,
      StatusTransitionGuard)
- [x] Repository implementation (EloquentComplaintRepository dkk) + binding
- [x] Form pengajuan pengaduan (multi-step Alpine.js 4 langkah, Leaflet
      pin lokasi) — `resources/views/complaints/create.blade.php`,
      `App\Http\Controllers\Web\MyComplaintController`, rute `/pengaduan/*`
      (khusus role masyarakat, sengaja di luar prefix `/dashboard`)
- [x] Generate nomor tiket saat submit (UseCase, pakai
      TicketNumberGeneratorRule) — sudah sejak Fase 4, dipakai ulang oleh
      controller web
- [x] Verifikasi pengaduan (Kominfo) — form di
      `dashboard/complaints/show.blade.php` + `ComplaintDashboardController@verify`
- [x] Disposisi ke OPD/Camat (Kominfo, enforce
      DispositionMustTargetOpdOrCamatRule) — checkbox multi-target OPD/
      Kecamatan di halaman yang sama + `@dispose`
- [x] Penanganan pengaduan (OPD/Camat) — `@handle`, discope ke disposisi
      milik unit yang login (`ComplaintPolicy::handle`)
- [x] Jawaban resmi ke masyarakat (Kominfo) — `@respond`
- [x] Tracking publik via nomor tiket (tanpa expose ID internal) —
      `public/track.blade.php` (sudah ada sejak Fase 6, kini bergaya
      Bootstrap)

## Modul Pelaporan Kegiatan

- [x] Migrasi tabel activities, activity_documentations
- [x] Domain layer: entity, ActivityStatus enum, repository interface
- [x] Repository implementation (EloquentActivityRepository) + binding
- [x] Input kegiatan OPD/Kecamatan —
      `dashboard/activities/create.blade.php` + `ActivityDashboardController@store`
- [x] Verifikasi kegiatan (Kominfo) — tombol aksi di
      `dashboard/activities/index.blade.php` + `@verify`
- [x] Publikasi kegiatan (Kominfo) + feed publik — `@publish`,
      tampil di `public/activities.blade.php` (`/kegiatan`)
- [x] Riwayat kegiatan per OPD/Kecamatan + filter status/tujuan/periode
      (FR-23/FR-24) — `ActivityDashboardController@index`,
      `dashboard/activities/index.blade.php` (2026-07-09)

## Modul Monitoring dan Dashboard

- [x] Dashboard statistik pengaduan — Chart.js doughnut per status +
      bar per kategori (`dashboard/statistics/index.blade.php`)
- [x] Dashboard statistik kegiatan — Chart.js doughnut per status
- [x] Dashboard monitoring kinerja (Kominfo/Bupati/Wabup/Sekda) —
      `dashboard/statistics/performance.blade.php` (`/dashboard/kinerja`):
      total, selesai, tingkat penyelesaian, breakdown per tujuan
- [x] Export laporan PDF (barryvdh/laravel-dompdf) —
      `StatisticsController@exportPdf`, `/dashboard/statistik/export/pdf`
- [x] Export laporan Excel (maatwebsite/excel) —
      `App\Exports\ComplaintStatisticsExport`,
      `/dashboard/statistik/export/excel`
- [~] Halaman "Laporan" pengaduan (Kominfo-only) dengan filter
      status/tujuan/hari(hari-dalam-minggu)/bulan/tahun + export PDF
      (kop surat + blok TTD)/Excel, plus sub-tab pengaturan TTD (nama,
      jabatan, pangkat, instansi, NIP — tabel `ttd_signatures`, satu baris
      aktif) — `LaporanController`, `dashboard/laporan/*.blade.php`
      (2026-07-09). **Kode sudah ditulis lengkap tapi BELUM diverifikasi
      jalan** (migration belum dijalankan, test belum dieksekusi — lihat
      "Known Issues" dan entri "Update Terakhir" 2026-07-09 lanjutan 2).

## Modul Notifikasi Real Time

- [x] Migrasi tabel notifications (custom, bukan tabel notifications
      bawaan Laravel)
- [x] Domain layer: NotificationMessage entity, repository interface
- [x] Repository implementation (EloquentNotificationRepository) + binding
- [x] Setup Laravel Reverb (`composer require laravel/reverb`,
      `reverb:install` — config `config/reverb.php` + env `REVERB_*`
      digenerate). `BROADCAST_CONNECTION` diganti dari `log` ke `reverb`
      di `.env`/`.env.example` (lihat "Keputusan Arsitektur" di AGENTS.md)
- [x] Setup Laravel Echo di `resources/js/app.js` (npm `laravel-echo` +
      `pusher-js`, broadcaster `reverb`) — subscribe ke channel privat
      sesuai role/id user (`window.SIPPM_USER` di-inject dari
      `layouts/dashboard.blade.php`) dan channel publik `public-activities`
- [x] Event/broadcast tiap perubahan status pengaduan
      (ComplaintSubmitted/Verified/Disposed/Handled/Resolved) — sudah ada
      sejak Fase 5, kini benar-benar tersiar live lewat Reverb (diverifikasi
      manual: `php artisan reverb:start` + `queue:work` + trigger event via
      tinker, broadcast terkirim tanpa exception)
- [x] Event/broadcast ActivityPublished (channel publik, satu-satunya
      pengecualian dari private channel)
- [x] routes/channels.php authorization — sudah ada sejak Fase 5, kini
      diverifikasi jalan dengan Reverb aktif
- [x] Indikator jumlah notifikasi belum dibaca (FR-34) dihitung
      server-side (`countUnreadForUser`, sudah ada di repository sejak
      awal tapi baru dipanggil sekarang) — sebelumnya client-side dari
      20 notifikasi terbaru saja, under-count untuk >20 unread
      (2026-07-09)

## Modul Audit Log

- [x] Migrasi tabel audit_logs (immutable: tidak ada updated_at/soft
      delete)
- [x] Model AuditLog: `update()`/`delete()` sengaja throw RuntimeException
      agar benar-benar tidak bisa diubah/dihapus
- [x] Pencatatan audit log otomatis via event listener terpusat (Fase 4-5,
      `App\Infrastructure\Notification\Listeners\RecordAuditLog`)
- [x] Halaman audit log (Kominfo-only) — `dashboard/audit-log/index.blade.php`
      bergaya Bootstrap, `/dashboard/audit-log`
- [x] `old_data` (state sebelum perubahan) direkam di setiap baris audit
      log transisi status (FR-37) — sebelumnya selalu `null`; sekarang
      diisi dari `previousStatus` yang diteruskan tiap domain event
      (2026-07-09)
- [x] Aksi "kelola pengguna" (create/edit/nonaktifkan akun) tercatat ke
      audit log (FR-36) — sebelumnya tidak tercatat sama sekali;
      `UserManagementController::recordUserAudit()` (2026-07-09)

## Known Issues

- **Fitur Laporan+TTD (2026-07-09 lanjutan 2) belum diverifikasi jalan.**
  Sesi yang menulis kodenya (migration `ttd_signatures`, `LaporanController`,
  views, `tests/Feature/Web/LaporanTest.php`) berjalan di environment tanpa
  PHP CLI/Laragon terpasang sama sekali (`C:\laragon` tidak ada, `php` tidak
  ada di PATH) — beda dari environment sesi-sesi sebelumnya. Migration
  belum pernah dijalankan ke database manapun, test belum pernah
  dieksekusi sama sekali. Sesi berikutnya (dengan PHP aktif) WAJIB
  menjalankan `php artisan migrate` lalu `php artisan test
  --filter=LaporanTest` sebelum fitur ini dianggap selesai/dipakai —
  jangan asumsikan kode ini bebas bug hanya karena ditulis mengikuti pola
  yang sudah terbukti di fitur lain.
- Redis belum terpasang di environment ini — `QUEUE_CONNECTION=database`
  dipakai sebagai default pragmatis (reversible, semua job pakai
  `ShouldQueue`). Ini juga berarti **queue worker (`php artisan
  queue:work`) harus berjalan terus-menerus** (di production: dikelola
  Supervisor per PRD 13.4) — tanpa worker, event broadcast/notifikasi
  tertahan di tabel `jobs` dan tidak akan pernah terkirim.
- Laravel Reverb sudah terpasang & `BROADCAST_CONNECTION=reverb` aktif,
  tapi **server Reverb (`php artisan reverb:start`) juga harus berjalan
  terus-menerus** bersamaan dengan queue worker — kalau salah satu mati,
  notifikasi real-time berhenti (fallback: notification bell tetap
  memuat riwayat via `GET /dashboard/notifications` saat halaman
  di-refresh, jadi tidak ada data yang hilang, hanya tidak real-time).
- MySQL Laragon (`mysqld.exe`) tidak berjalan sebagai Windows Service di
  environment ini — perlu dijalankan manual (lihat catatan di Riwayat di
  bawah) setiap kali environment/agent baru mulai bekerja, kecuali sudah
  dikonfigurasi sebagai service oleh user.
- Sidebar dashboard adalah Bootstrap 5 custom (bukan library `admin-lte`
  asli dari npm/CDN) — AdminLTE 3.x resmi dibangun di atas Bootstrap 4
  dan akan bentrok dengan Bootstrap 5 yang dipakai di seluruh proyek ini
  (PRD 13.2 mensyaratkan Bootstrap 5). Tampilan akhir tetap mengikuti
  bahasa desain AdminLTE (sidebar gelap + topbar + card) dan palet
  "Bright Skeuomorphism", hanya tanpa dependensi CSS/JS AdminLTE literal.
- ext-zip pada PHP CLI Laragon sebelumnya nonaktif (dibutuhkan
  spatie/laravel-backup) — sudah diaktifkan dengan menghapus `;` di
  depan `extension=zip` pada
  `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.ini`.
- `php artisan schedule:run` (backup harian, lihat FR-39 di Riwayat)
  hanya benar-benar terpicu kalau cron/Task Scheduler OS memanggilnya
  tiap menit, atau `php artisan schedule:work` dibiarkan berjalan — di
  environment dev ini tidak ada keduanya yang otomatis aktif.
- Working tree saat ini (per 2026-07-09 lanjutan) berisi 13 file uncommitted
  — polishing visual beranda/auth/layout dari entri "Update Terakhir" di
  atas (`app/Http/Controllers/{Api/DashboardController,
  Web/Dashboard/StatisticsController,Web/Public/HomeController}.php`,
  `resources/{css/app.css,js/app.js}`,
  `resources/views/{auth/{login,register},dashboard/statistics/index,
  layouts/{app,dashboard},public/{activities,home,track}}.blade.php`).
  Catatan sebelumnya di sini soal `lang/id/*`/`config/backup.php`/
  `hero-illustration.png` sudah usang (item itu sudah ter-commit di
  `dcb3a02 update`) — dikoreksi. Sesi berikutnya perlu `git add`/commit
  perubahan visual di atas dulu sebelum menambah pekerjaan baru, per PRD
  13.4 ("commit wajib per fitur").

## Next Steps

Fase 0-8 (fondasi sampai Presentation layer web penuh + Reverb) sudah
selesai. Seluruh item pengerasan produksi (hardening) dari audit PRD
("2026-07-05 lanjutan 6" di atas) **sudah diimplementasikan** — NFR-07
(`throttle:login`), NFR-16 (`Cache::remember` di `StatisticsController`
dan `Api\DashboardController`), FR-08 (`RecordLoginAuditLog`), FR-19
(filter kategori/tanggal/tujuan), FR-39 (backup terjadwal), dan gap RBAC
Bupati/Wabup/Sekda pada "Lihat Laporan Kegiatan" — hanya belum di-commit
(lihat "Known Issues"). Sisa pekerjaan:

- **Commit dulu** seluruh perubahan working tree di atas sebelum
  menambah fitur baru, per-fitur sesuai PRD 13.4 (bukan satu commit
  besar gabungan).
- Tambahkan HTTPS/TLS di reverse proxy (Nginx) saat deployment — NFR-05
  tidak relevan di localhost dev.
- Jalankan `php artisan queue:work`, `php artisan reverb:start`, dan
  `php artisan schedule:work` (atau cron `schedule:run` per menit) di
  bawah Supervisor sebelum UAT/production (lihat "Known Issues").
- Pertimbangkan memindahkan sidebar dashboard ke library AdminLTE resmi
  jika proyek suatu saat turun versi ke Bootstrap 4, atau tunggu AdminLTE
  versi stabil berbasis Bootstrap 5.
- Tambah test feature untuk endpoint export PDF/Excel dan skenario
  reject/tolak pengaduan (saat ini hanya alur happy-path + satu skenario
  BR-01 yang dites di `ComplaintWorkflowTest`).

Lihat `AGENTS.md` bagian "Repository Interfaces (Domain)" untuk daftar
method signature semua repository (masih akurat, tidak berubah sejak
Fase 4).

## Riwayat

### 2026-07-05 — Fase 0-3: Environment, Domain Layer, Database/Model/RBAC, Repository Bindings

**Fase 0 (Environment & Fondasi)**
- MySQL 8.4 Laragon dinyalakan manual (`mysqld.exe --defaults-file=...my.ini`,
  karena tidak terdaftar sebagai Windows Service) dan database
  `sippm_madina` (utf8mb4_unicode_ci) dibuat.
- `.env` dan `.env.example`: `DB_CONNECTION=mysql`,
  `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=sippm_madina`,
  `DB_USERNAME=root`, `DB_PASSWORD=` (kosong). `APP_NAME` diganti ke
  "SIPPM Madina". `QUEUE_CONNECTION=database` &
  `BROADCAST_CONNECTION=log` sudah default dari skeleton, dikonfirmasi
  tidak perlu diubah.
- Frontend: Tailwind dihapus dari `package.json` & `vite.config.js`.
  `resources/css/app.css` diganti berisi CSS custom properties palet
  "Bright Skeuomorphism" + utility classes (`.sippm-card`,
  `.sippm-badge-*`, dst). `resources/js/app.js` jadi placeholder minimal.
- `progress.md`, `AGENTS.md`, `README.md` dibuat/diperbarui.
- Composer packages terpasang: `laravel/sanctum`,
  `spatie/laravel-permission`, `barryvdh/laravel-dompdf`,
  `maatwebsite/excel`, `spatie/laravel-backup` (versi `^10.2`, karena PHP
  di environment ini 8.3 — versi 10.0-10.1 butuh PHP ^8.4). Ekstensi
  `zip` di php.ini Laragon diaktifkan agar laravel-backup bisa terpasang.

**Fase 1 (Domain Layer)** — semua PHP murni di `app/Domain/`, tanpa
dependensi Illuminate:
- `Complaint/Entities/Complaint.php`,
  `Complaint/ValueObjects/{TicketNumber,ComplaintStatus,TargetType}.php`,
  `Complaint/Repositories/{ComplaintRepositoryInterface,
  ComplaintStatusHistoryRepositoryInterface,
  DispositionRepositoryInterface}.php`,
  `Complaint/Rules/{InvalidDispositionTargetException,
  DispositionMustTargetOpdOrCamatRule, TicketNumberGeneratorRule,
  StatusTransitionGuard}.php`
- `Activity/Entities/Activity.php`,
  `Activity/ValueObjects/ActivityStatus.php`,
  `Activity/Repositories/ActivityRepositoryInterface.php`
- `User/Entities/UserAccount.php`, `User/ValueObjects/Role.php`,
  `User/Repositories/UserRepositoryInterface.php`
- `Notification/Entities/NotificationMessage.php`,
  `Notification/Repositories/NotificationRepositoryInterface.php`

**Fase 2 (Database, Model, RBAC)**
- 17 migration files (`database/migrations/2026_07_05_*`), urutan:
  Spatie permission tables → opds → kecamatans → alter users (nik,
  phone, is_active, consent_at, opd_id FK, kecamatan_id FK) → complaints
  → complaint_attachments → complaint_status_histories → dispositions →
  complaint_handlings → complaint_responses → activities →
  activity_documentations → notifications (custom) → audit_logs
  (immutable, tanpa updated_at/soft delete).
- Eloquent models direlokasi/dibuat di
  `app/Infrastructure/Persistence/Eloquent/Models/`: `User` (dipindah
  dari `app/Models/User.php`, tambah `HasRoles`, `HasApiTokens`, relasi
  complaints/notifications/opd/kecamatan), `Opd`, `Kecamatan`,
  `Complaint` (cast status ke enum, SoftDeletes, semua relasi),
  `ComplaintAttachment`, `ComplaintStatusHistory`, `Disposition`,
  `ComplaintHandling`, `ComplaintResponse`, `Activity` (morphTo actor,
  SoftDeletes), `ActivityDocumentation`, `Notification`, `AuditLog`
  (blok update/delete via event `updating`/`deleting` → throw
  RuntimeException).
- `app/Models/` dihapus. `config/auth.php` provider User diarahkan ke
  namespace baru. `database/factories/UserFactory.php` diperbarui
  (namespace + kolom nik/phone/is_active).
- Morph map (`opd`, `kecamatan`, `user`) didaftarkan di
  `AppServiceProvider::boot()`.
- `database/seeders/{RolePermissionSeeder,OpdSeeder,KecamatanSeeder,
  DemoUserSeeder}.php` dibuat & didaftarkan di `DatabaseSeeder`.
- Middleware alias `role`/`permission` (Spatie) dan `active`
  (`EnsureAccountIsActive`, cek `is_active`) didaftarkan di
  `bootstrap/app.php`.
- Policy stub (deny-by-default) dibuat di `app/Http/Policies/`:
  `ComplaintPolicy`, `ActivityPolicy`, `UserPolicy`, `AuditLogPolicy`.

**Fase 3 (Repository Implementations + Binding)**
- `app/Infrastructure/Persistence/Eloquent/Repositories/`:
  `EloquentComplaintRepository`, `EloquentComplaintStatusHistoryRepository`,
  `EloquentDispositionRepository`, `EloquentActivityRepository`,
  `EloquentUserRepository`, `EloquentNotificationRepository` — semua
  method dari interface Domain diimplementasikan penuh (bukan stub).
- `app/Providers/RepositoryServiceProvider.php` dibuat & didaftarkan di
  `bootstrap/providers.php`, bind semua 6 interface ke implementasinya.

**Verifikasi**
- `php artisan migrate:fresh --seed` berhasil penuh terhadap MySQL nyata
  (`sippm_madina`), termasuk semua 17 migration dan 4 seeder.
- `php artisan config:clear` dan `composer dump-autoload` berhasil tanpa
  error (8215 class ter-autoload).
- Sanity check via `php artisan tinker`: resolusi binding
  `ComplaintRepositoryInterface`/`UserRepositoryInterface` dari container
  berhasil ke implementasi Eloquent yang benar;
  `DispositionMustTargetOpdOrCamatRule::assert()` menolak target BUPATI
  dan menerima target OPD sesuai BR-01/BR-02;
  `TicketNumberGeneratorRule::generate(2026, 0)` menghasilkan
  `PGD-2026-000001`; user demo `kominfo@demo.test` memiliki role
  `kominfo` via Spatie.

### 2026-07-05 — Fase 7-8: Presentation Layer Web Penuh + Laravel Reverb

**Fase 7 (Blade + Bootstrap 5 + "Bright Skeuomorphism")**
- `resources/views/layouts/{app,dashboard}.blade.php` dibuat: layout
  publik (navbar navy + footer) dan layout dashboard (sidebar gelap
  per-role + topbar + notification bell), semua CDN (Bootstrap 5,
  Bootstrap Icons, DataTables, SweetAlert2, Alpine.js) sesuai PRD 13.3.
  Base class `App\Http\Controllers\Controller` ditambah trait
  `AuthorizesRequests` (sebelumnya kosong — `$this->authorize()` di
  controller manapun akan fatal error tanpa ini).
- Semua view placeholder polos direstyle total: `auth/{login,register}`,
  `public/{home,track,activities}`, `dashboard/{complaints,activities,
  statistics,users,audit-log}/index`, ditambah view baru:
  `complaints/{index,create,show}` (masyarakat), `dashboard/complaints/show`
  (aksi verify/dispose/handle/respond), `dashboard/activities/create`,
  `dashboard/users/{create,edit}`, `dashboard/statistics/performance`,
  `dashboard/statistics/export-pdf`.
- `App\Http\Controllers\Web\MyComplaintController` (baru): pengaduan milik
  masyarakat sendiri, rute `/pengaduan/*` — sengaja di luar prefix
  `/dashboard` (AGENTS.md Don'ts: masyarakat tidak boleh akses dashboard
  internal). Form pengajuan 4 langkah (Alpine.js) + Leaflet pin lokasi.
- `ComplaintDashboardController`, `ActivityDashboardController`,
  `UserManagementController`, `StatisticsController` diperluas dari
  read-only (`index`/`show`) menjadi full write actions (verify/dispose/
  handle/respond, create/verify/publish kegiatan, create/edit/toggle-active
  pengguna, export pdf/excel) — semua reuse Form Request + UseCase yang
  sama persis dengan `Api\*Controller` (tidak ada duplikasi validasi/logic).
- `App\Http\Controllers\Web\Dashboard\NotificationWebController` (baru):
  endpoint JSON session-based untuk notification bell
  (`/dashboard/notifications`) — sengaja bukan `/api/v1/notifications`
  karena Sanctum di proyek ini token-based, bukan stateful-session, jadi
  fetch dari Blade tidak akan lolos `auth:sanctum` tanpa konfigurasi
  tambahan yang tidak diperlukan.
- `App\Exports\ComplaintStatisticsExport` (baru, `maatwebsite/excel`) +
  `dashboard/statistics/export-pdf.blade.php` (`barryvdh/laravel-dompdf`)
  untuk FR-29.
- Bug ditemukan & diperbaiki saat verifikasi manual:
  `SubmitComplaintDTO`/`Api\ComplaintController` & `MyComplaintController`
  meneruskan `target_id`/`latitude`/`longitude` mentah dari
  `$request->validated()` (string) ke parameter `?int`/`?float` bertipe
  ketat (`declare(strict_types=1)`) — `TypeError` di kedua controller,
  diperbaiki dengan cast eksplisit di kedua tempat.
  `dashboard/complaints/show.blade.php` mengakses
  `$complaint->target_type->value` seolah enum, padahal
  `Complaint::casts()` hanya meng-cast `status` (bukan `target_type`) —
  diperbaiki jadi akses string langsung.

**Fase 8 (Laravel Reverb)**
- `composer require laravel/reverb`, lalu env `REVERB_*`/`VITE_REVERB_*`
  digenerate ke `.env` (dan ditambahkan sebagai placeholder kosong ke
  `.env.example`). `BROADCAST_CONNECTION` diganti `log` → `reverb`.
- `npm install laravel-echo pusher-js`; `resources/js/app.js` diisi
  bootstrap Echo (`broadcaster: 'reverb'`) + listener per channel
  (`App.Models.User.{id}`, `channel-kominfo`, `channel-opd.{id}`,
  `channel-camat.{id}`, `public-activities`), memicu `CustomEvent`
  browser `sippm:notification` yang didengar oleh Alpine component
  notification bell di `layouts/dashboard.blade.php`
  (`window.SIPPM_USER` di-inject inline sebelum `@vite` load app.js).
- Sidebar dashboard dibangun manual dengan Bootstrap 5 (bukan library
  `admin-lte` literal) karena AdminLTE 3.x resmi berbasis Bootstrap 4 dan
  akan bentrok — lihat "Known Issues".

**Verifikasi (manual, end-to-end via `php artisan serve` + curl
cookie-session, akun demo seeder)**
- Alur pengaduan penuh: masyarakat ajukan (dapat nomor tiket
  `PGD-2026-000001`) → kominfo verifikasi → kominfo disposisi ke OPD →
  OPD tangani → kominfo jawab resmi → masyarakat lihat jawaban di halaman
  sendiri. Status berpindah benar di setiap langkah
  (diajukan→diverifikasi→diproses→ditindaklanjuti→selesai).
- Alur kegiatan penuh: camat input (draft) → kominfo verifikasi →
  kominfo publikasikan → tampil di feed publik `/kegiatan`.
- Kelola pengguna: kominfo membuat akun OPD baru, nonaktifkan akun.
- RBAC: kominfo dapat akses semua menu; OPD ditolak (403) saat
  input kegiatan Kominfo-only dan saat mencoba menangani pengaduan yang
  bukan didisposisikan ke unitnya.
- Reverb: `php artisan reverb:start` boot bersih di port 8080;
  `php artisan queue:work` memproses job broadcast tanpa exception
  (driver `reverb` berhasil terhubung & mem-publish ke server Reverb
  yang berjalan) untuk event `ComplaintSubmitted/Verified/Disposed/
  Handled/Resolved` dan `ActivityPublished`; notification bell terisi
  setelah worker memproses `PersistComplaintNotification`/
  `PersistActivityNotification`.
- `php artisan test`: 19 test (7 baru: `Feature/Web/{ComplaintWorkflowTest,
  UserManagementTest,ActivityWorkflowTest}`, ditambah test API/Unit yang
  sudah ada), 62 assertion, semua **passed**.
