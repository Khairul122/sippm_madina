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

## Modul Audit Log

- [x] Migrasi tabel audit_logs (immutable: tidak ada updated_at/soft
      delete)
- [x] Model AuditLog: `update()`/`delete()` sengaja throw RuntimeException
      agar benar-benar tidak bisa diubah/dihapus
- [x] Pencatatan audit log otomatis via event listener terpusat (Fase 4-5,
      `App\Infrastructure\Notification\Listeners\RecordAuditLog`)
- [x] Halaman audit log (Kominfo-only) — `dashboard/audit-log/index.blade.php`
      bergaya Bootstrap, `/dashboard/audit-log`

## Known Issues

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
- `resources/views/dashboard/statistics/index.blade.php` dan
  `performance.blade.php` mengambil data query on-demand tanpa caching —
  NFR-16 (caching statistik/dashboard) belum diimplementasikan, cocok
  untuk fase hardening berikutnya.
- Belum ada rate limiting eksplisit pada endpoint login (NFR-07) di luar
  default Laravel; perlu ditambahkan `throttle` middleware pada rute
  `/login` dan `/api/v1/auth/login` sebelum production.

## Next Steps

Fase 0-8 (fondasi sampai Presentation layer web penuh + Reverb) sudah
selesai. Pekerjaan yang tersisa bersifat pengerasan produksi (hardening)
dan sudah tidak mengubah arsitektur:

- Tambahkan `throttle:login` pada rute login (web & API) untuk NFR-07.
- Terapkan caching (`Cache::remember`) pada `StatisticsController` dan
  `Api\DashboardController` untuk NFR-16.
- Tambahkan HTTPS/TLS di reverse proxy (Nginx) saat deployment — NFR-05
  tidak relevan di localhost dev.
- Jalankan `php artisan queue:work` dan `php artisan reverb:start` di
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
