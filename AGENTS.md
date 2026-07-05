# AGENTS.md — SIPPM Madina

Panduan wajib untuk siapa pun (manusia atau AI coding agent) yang
mengerjakan proyek ini. Berdasarkan PRD SIPPM Madina bagian 14
(Do's/Don'ts) dan bagian 9 (struktur folder wajib).

## Struktur Folder Wajib (Clean Architecture, 4 lapis)

```
app/
├── Domain/                     # Lapis paling dalam. PHP murni, TIDAK BOLEH
│   │                            # ada `use Illuminate\...` atau extends Eloquent.
│   ├── Complaint/
│   │   ├── Entities/            # Plain PHP object (Complaint)
│   │   ├── ValueObjects/        # Enum & VO immutable (ComplaintStatus, TargetType, TicketNumber)
│   │   ├── Repositories/        # Interface saja (ComplaintRepositoryInterface, dst)
│   │   └── Rules/                # Business rules murni (StatusTransitionGuard, dst)
│   ├── Activity/
│   ├── User/
│   └── Notification/
│
├── Application/                 # UseCases + DTOs (Fase 4). Orkestrasi:
│                                 # panggil Domain Rules, panggil Repository
│                                 # interface, buka DB transaction, dispatch event.
│
├── Infrastructure/               # Implementasi konkret dari interface Domain.
│   └── Persistence/Eloquent/
│       ├── Models/               # Semua Eloquent model (BUKAN app/Models!)
│       └── Repositories/         # Eloquent*Repository implements *RepositoryInterface
│
└── Http/                         # Presentation layer (Fase 6-7)
    ├── Controllers/              # TIPIS — hanya panggil UseCase, tidak ada business logic
    ├── Requests/                 # Form Request untuk semua validasi input
    ├── Resources/                # API Resource untuk response konsisten
    ├── Middleware/                # EnsureAccountIsActive, dst
    └── Policies/                  # Object-level authorization di atas middleware role:
```

Binding interface → implementasi didaftarkan di
`app/Providers/RepositoryServiceProvider.php` (`$this->app->bind(...)`),
bukan di tempat lain.

## Do's

- Ikuti PSR-12.
- Business logic HANYA di UseCase (Application layer) atau Domain Rules —
  bukan di Controller.
- Validasi input SELALU via Form Request, bukan validasi manual di
  Controller.
- Object-level authorization SELALU via Policy, di atas middleware RBAC
  (`role:`/`permission:`) yang sudah menyaring di level route.
- Eager loading (`with()`) untuk mencegah N+1 query di semua listing.
- Notifikasi in-app (`PersistComplaintNotification`,
  `PersistActivityNotification`) dijalankan **sync, bukan queued**
  (keputusan diubah dari draf awal) — supaya notifikasi langsung masuk ke
  tabel `notifications` tanpa bergantung pada `queue:work` yang mungkin
  tidak berjalan. Audit log (`RecordAuditLog`) dan broadcast WebSocket
  (event `ShouldBroadcast`) TETAP queued, karena keduanya bukan
  data yang harus langsung terlihat pengguna dan boleh gagal/retry di
  latar belakang tanpa mengganggu request.
- Commit kecil per fitur.
- Secret HANYA di `.env`, tidak pernah di-commit di kode.
- Setiap perubahan skema database HARUS lewat migration.
- Response API konsisten via API Resource.
- Audit log dicatat untuk SEMUA aksi sensitif dan TIDAK PERNAH dihapus
  (lihat `AuditLog` model — `delete()`/`update()` sengaja throw).
- Format nomor tiket pengaduan: `PGD-{tahun}-{6 digit}` (lihat
  `TicketNumberGeneratorRule`).
- Unit test untuk tiap UseCase + feature test untuk endpoint kritis.
- Validasi tipe & ukuran file upload di semua Form Request upload.
- `complaints` & `activities` pakai soft delete.
- Single responsibility per class.

## Don'ts

- JANGAN taruh business logic di Controller — panggil UseCase.
- JANGAN disposisi pengaduan langsung ke Bupati/Wakil Bupati/Sekda —
  disposisi hanya boleh ke OPD atau Camat (BR-01/BR-02, satu-satunya
  enforcement ada di
  `App\Domain\Complaint\Rules\DispositionMustTargetOpdOrCamatRule`).
  Ini berlaku walau target ASLI pengaduan adalah Bupati/Wabup/Sekda —
  Kominfo tetap harus mendisposisikannya ke OPD/Camat yang relevan.
- JANGAN expose ID internal di endpoint tracking publik — hanya nomor
  tiket (lihat rencana `ComplaintPublicResource` di Fase 6).
- JANGAN pakai raw SQL kalau Eloquent query builder sudah cukup.
- JANGAN broadcast data sensitif di channel WebSocket publik — selalu
  private channel, KECUALI event `ActivityPublished` yang memang publik
  by design.
- JANGAN lewati DB transaction untuk write yang menyentuh >1 tabel.
- Role `masyarakat` TIDAK BOLEH mengakses rute dashboard internal.
- JANGAN lompati whitelist transisi status
  (`App\Domain\Complaint\Rules\StatusTransitionGuard`, BR-04) — semua
  perubahan status pengaduan wajib lewat guard ini.

## Keputusan Arsitektur yang Sudah Diambil

- **`QUEUE_CONNECTION=database`**: dipakai karena Redis belum terpasang
  di environment ini. Reversible — semua job pakai `ShouldQueue`, jadi
  pindah ke Redis nanti hanya ganti `.env`, tanpa ubah kode.
- **`BROADCAST_CONNECTION=log`**: sementara, sampai Laravel Reverb
  dipasang di fase akhir pengembangan (Fase 8 pada rencana implementasi).
  Arsitektur event (Fase 5: `ComplaintSubmitted`, `ComplaintVerified`,
  dst, semua `implements ShouldBroadcast`) sudah driver-agnostic — pindah
  ke `BROADCAST_CONNECTION=reverb` murni perubahan config, tidak ada
  redesign class Event.
- **RBAC via Spatie Laravel Permission**: tabel `roles`/`permissions`/
  `model_has_roles`/`model_has_permissions`/`role_has_permissions` adalah
  skema standar Spatie — ini SATU-SATUNYA sumber kebenaran RBAC, tidak
  ada tabel roles/permissions custom terpisah.
- **Eloquent Model di `app/Infrastructure/Persistence/Eloquent/Models/`**,
  bukan `app/Models/` — `app/Models/` sudah dihapus dari proyek ini.
- **Morph map** (`app/Providers/AppServiceProvider.php`) memetakan
  `'opd' => Opd::class`, `'kecamatan' => Kecamatan::class`,
  `'user' => User::class` untuk kolom polymorphic (`activities.actor_type`,
  dst) agar menyimpan slug pendek, bukan FQCN penuh.
- **Frontend**: Tailwind DIHAPUS dari proyek ini. Stack final adalah
  Blade + Bootstrap 5 + Alpine.js + Chart.js + DataTables + SweetAlert2 +
  Leaflet, semua via CDN (bukan npm bundle). Vite dipertahankan tipis
  untuk compile `resources/css/app.css` (palet warna "Bright
  Skeuomorphism") dan `resources/js/app.js` (bootstrap Laravel Echo,
  Fase 8, via npm `laravel-echo`+`pusher-js` — satu-satunya bagian yang
  memang di-bundle npm, karena Echo/Pusher client tidak praktis dipakai
  murni via CDN dengan ESM `import.meta.env`).
- **Sidebar dashboard Bootstrap 5 custom, bukan library AdminLTE
  literal**: AdminLTE 3.x (versi stabil) dibangun di atas Bootstrap 4 dan
  akan bentrok dengan Bootstrap 5 yang dipakai di seluruh proyek ini
  (PRD 13.2). Sidebar gelap + topbar + card di
  `resources/views/layouts/dashboard.blade.php` meniru bahasa desain
  AdminLTE secara visual dengan Bootstrap 5 murni. Reversible: bila
  AdminLTE merilis versi stabil berbasis Bootstrap 5, sidebar ini bisa
  diganti tanpa mengubah controller/route.
- **Reverb + queue worker WAJIB berjalan bersamaan** (`php artisan
  reverb:start` dan `php artisan queue:work`, dikelola Supervisor di
  production per PRD 13.4). Tanpa `queue:work`, event broadcast/
  notifikasi tertahan di tabel `jobs` (semua listener notifikasi
  `ShouldQueue`, `QUEUE_CONNECTION=database`) dan tidak pernah terkirim.

## Palet Warna "Bright Skeuomorphism"

Didefinisikan sebagai CSS custom properties di `resources/css/app.css`:

```
--sippm-navy:   #16345C
--sippm-gold:   #C9A227
--sippm-green:  #2E7D4F
--sippm-amber:  #D98E04
--sippm-red:    #B23A3A
--sippm-cream:  #F7F5EF
--sippm-text:   #2B2B2B
```

Shadow lembut 2-lapis (`--sippm-shadow-soft`, `--sippm-shadow-raised`) dan
border-radius 12-16px (`--sippm-radius-sm`, `--sippm-radius-lg`) sudah
disiapkan sebagai utility class (`.sippm-card`, `.sippm-card-raised`,
`.sippm-badge-*`, `.sippm-btn-*`) untuk dipakai Blade views di fase
berikutnya.

## Repository Interfaces (Domain) — Referensi Cepat untuk Fase 4

Semua sudah diimplementasikan penuh di
`app/Infrastructure/Persistence/Eloquent/Repositories/` dan di-bind di
`app/Providers/RepositoryServiceProvider.php`:

- `ComplaintRepositoryInterface`: `save`, `findById`, `findByTicketNumber`,
  `countForYear`, `updateStatus`, `paginate(filters, page, perPage)`,
  `delete`.
- `ComplaintStatusHistoryRepositoryInterface`: `recordChange`,
  `listForComplaint`.
- `DispositionRepositoryInterface`: `create`, `listForComplaint`,
  `findById`.
- `ActivityRepositoryInterface`: `save`, `findById`, `updateStatus`,
  `paginate`, `paginatePublished`, `delete`.
- `UserRepositoryInterface`: `findById`, `findByEmail`, `findByNik`,
  `save`, `setActive`, `paginate`.
- `NotificationRepositoryInterface`: `create`, `findById`, `markAsRead`,
  `countUnreadForUser`, `paginateForUser`.

Business rules Domain siap pakai:

- `TicketNumberGeneratorRule::generate(int $year, int $currentMaxSequenceForYear): TicketNumber`
- `DispositionMustTargetOpdOrCamatRule::assert(TargetType $targetType): void`
- `StatusTransitionGuard::assertCanTransition(ComplaintStatus $from, ComplaintStatus $to, string $actingRoleSlug): void`
