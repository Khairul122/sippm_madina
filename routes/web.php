<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\RegisterController;
use App\Http\Controllers\Web\Dashboard\ActivityDashboardController;
use App\Http\Controllers\Web\Dashboard\AuditLogController;
use App\Http\Controllers\Web\Dashboard\ComplaintDashboardController;
use App\Http\Controllers\Web\Dashboard\DashboardHomeController;
use App\Http\Controllers\Web\Dashboard\DesaManagementController;
use App\Http\Controllers\Web\Dashboard\KecamatanManagementController;
use App\Http\Controllers\Web\Dashboard\LaporanController;
use App\Http\Controllers\Web\Dashboard\NotificationWebController;
use App\Http\Controllers\Web\Dashboard\OpdManagementController;
use App\Http\Controllers\Web\Dashboard\StatisticsController;
use App\Http\Controllers\Web\Dashboard\UserManagementController;
use App\Http\Controllers\Web\Dashboard\ComplaintCategoryManagementController;
use App\Http\Controllers\Web\ManualBookController;
use App\Http\Controllers\Web\MyComplaintController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\Public\ActivityFeedController;
use App\Http\Controllers\Web\Public\HomeController;
use App\Http\Controllers\Web\Public\TrackComplaintController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Session-based dashboard for internal roles + a thin public site, plus
| masyarakat's own complaint pages under /pengaduan (deliberately NOT under
| /dashboard — that prefix is internal-only per AGENTS.md Don'ts). RBAC
| below follows the role matrix from the PRD (section 4.2) enforced by
| Spatie's `role:` middleware, layered under `active`
| (EnsureAccountIsActive). Object-level checks beyond the role matrix are
| enforced by Policies inside each controller action ($this->authorize()).
|
*/

Route::get('/', [HomeController::class, 'index']);
Route::get('/lacak', [TrackComplaintController::class, 'index']);
Route::get('/kegiatan', [ActivityFeedController::class, 'index']);

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:login');
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth');

// Masyarakat: pengaduan milik sendiri.
Route::prefix('pengaduan')->middleware(['auth', 'active', 'role:masyarakat'])->group(function () {
    Route::get('/', [MyComplaintController::class, 'index']);
    Route::get('/ajukan', [MyComplaintController::class, 'create']);
    Route::post('/', [MyComplaintController::class, 'store']);
    Route::get('/{complaint}', [MyComplaintController::class, 'show']);
});

// Profil diri sendiri: berlaku untuk SEMUA role yang login (termasuk
// masyarakat), sengaja di prefix netral, sejajar /pengaduan — BUKAN
// /dashboard, karena masyarakat tidak boleh masuk /dashboard/*
// (AGENTS.md Don'ts). Tidak ada parameter {user}, semua action operasi
// ke $request->user() sendiri.
Route::prefix('profil')->middleware(['auth', 'active'])->group(function () {
    Route::get('/', [ProfileController::class, 'show']);
    Route::put('/', [ProfileController::class, 'updateInfo']);
    Route::post('/avatar', [ProfileController::class, 'updateAvatar']);
    Route::put('/password', [ProfileController::class, 'updatePassword']);
});

// Manual book: bisa dilihat/diunduh SEMUA role yang login (termasuk
// masyarakat), sengaja di prefix netral sejajar /pengaduan dan /profil
// — BUKAN /dashboard. Upload/ganti file dibatasi role:kominfo lewat
// middleware pada rute POST-nya saja (rute GET tetap terbuka untuk
// semua auth+active).
Route::prefix('manual-book')->middleware(['auth', 'active'])->group(function () {
    Route::get('/', [ManualBookController::class, 'show']);
    Route::get('/download', [ManualBookController::class, 'download']);
    Route::get('/preview', [ManualBookController::class, 'preview']);
    Route::post('/', [ManualBookController::class, 'upload'])->middleware('role:kominfo');
});

Route::prefix('dashboard')->middleware(['auth', 'active'])->group(function () {
    // Bare "/dashboard" (login redirect target, bookmarks) — bounces to
    // the first page the user's role actually has access to.
    Route::get('/', [DashboardHomeController::class, 'index']);

    // Notification bell (all internal roles).
    Route::get('/notifications', [NotificationWebController::class, 'index']);
    Route::post('/notifications/read-all', [NotificationWebController::class, 'markAllRead']);
    Route::post('/notifications/{notification}/read', [NotificationWebController::class, 'markRead']);

    // All internal roles (masyarakat is excluded — masyarakat never gets a
    // dashboard account per AGENTS.md Don'ts).
    Route::middleware('role:kominfo|opd|camat|bupati|wakil_bupati|sekda')->group(function () {
        Route::get('/statistik', [StatisticsController::class, 'index']);
        Route::get('/statistik/export/pdf', [StatisticsController::class, 'exportPdf']);
        Route::get('/statistik/export/excel', [StatisticsController::class, 'exportExcel']);
    });

    // Complaint dashboard: Kominfo sees/manages everything; OPD/Camat see
    // only their own scope (filtered in the controller). Per PRD 4.2
    // there is no "Lihat Pengaduan" row for Bupati/Wabup/Sekda — their
    // visibility is limited to aggregate stats (/statistik, /kinerja).
    Route::middleware('role:kominfo|opd|camat')->group(function () {
        Route::get('/complaints', [ComplaintDashboardController::class, 'index']);
        Route::get('/complaints/{complaint}', [ComplaintDashboardController::class, 'show']);
        Route::get('/activities/{activity}/edit', [ActivityDashboardController::class, 'edit']);
        Route::put('/activities/{activity}', [ActivityDashboardController::class, 'update']);
        Route::delete('/activities/{activity}', [ActivityDashboardController::class, 'destroy']);
    });

    // Kegiatan: "Lihat Laporan Kegiatan" (PRD 4.2) is granted to EVERY
    // internal role, not just kominfo/opd/camat — Bupati/Wabup/Sekda must
    // be able to view this list too (previously missing, a real RBAC gap
    // vs the PRD matrix). "Input Kegiatan" stays OPD/Camat-only.
    Route::middleware('role:kominfo|opd|camat|bupati|wakil_bupati|sekda')->group(function () {
        Route::get('/activities', [ActivityDashboardController::class, 'index']);
        Route::get('/activities/{activity}', [ActivityDashboardController::class, 'show']);
    });

    Route::middleware('role:opd|camat')->group(function () {
        Route::get('/activities/create', [ActivityDashboardController::class, 'create']);
        Route::post('/activities', [ActivityDashboardController::class, 'store']);
    });

    Route::middleware('role:kominfo')->group(function () {
        Route::post('/complaints/{complaint}/verify', [ComplaintDashboardController::class, 'verify']);
        Route::post('/complaints/{complaint}/dispose', [ComplaintDashboardController::class, 'dispose']);
        Route::post('/complaints/{complaint}/respond', [ComplaintDashboardController::class, 'respond']);
        Route::post('/activities/{activity}/verify', [ActivityDashboardController::class, 'verify']);
        Route::post('/activities/{activity}/publish', [ActivityDashboardController::class, 'publish']);
        Route::post('/activities/{activity}/unpublish', [ActivityDashboardController::class, 'unpublish']);

        Route::get('/users', [UserManagementController::class, 'index']);
        Route::get('/users/create', [UserManagementController::class, 'create']);
        Route::post('/users', [UserManagementController::class, 'store']);
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit']);
        Route::put('/users/{user}', [UserManagementController::class, 'update']);
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy']);
        Route::post('/users/{user}/toggle-active', [UserManagementController::class, 'toggleActive']);

        Route::get('/audit-log', [AuditLogController::class, 'index']);

        Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf']);
        Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel']);
        Route::post('/laporan/ttd', [LaporanController::class, 'updateTtd']);
        Route::get('/laporan/activities/export-pdf', [LaporanController::class, 'exportActivitiesPdf']);
        Route::get('/laporan/activities/export-excel', [LaporanController::class, 'exportActivitiesExcel']);

        // Data referensi wilayah (OPD/Kecamatan/Desa).
        Route::get('/opd', [OpdManagementController::class, 'index']);
        Route::get('/opd/create', [OpdManagementController::class, 'create']);
        Route::post('/opd', [OpdManagementController::class, 'store']);
        Route::get('/opd/{opd}/edit', [OpdManagementController::class, 'edit']);
        Route::put('/opd/{opd}', [OpdManagementController::class, 'update']);
        Route::delete('/opd/{opd}', [OpdManagementController::class, 'destroy']);

        Route::get('/kecamatan', [KecamatanManagementController::class, 'index']);
        Route::get('/kecamatan/create', [KecamatanManagementController::class, 'create']);
        Route::post('/kecamatan', [KecamatanManagementController::class, 'store']);
        Route::get('/kecamatan/{kecamatan}/edit', [KecamatanManagementController::class, 'edit']);
        Route::put('/kecamatan/{kecamatan}', [KecamatanManagementController::class, 'update']);
        Route::delete('/kecamatan/{kecamatan}', [KecamatanManagementController::class, 'destroy']);

        Route::get('/desa', [DesaManagementController::class, 'index']);
        Route::get('/desa/create', [DesaManagementController::class, 'create']);
        Route::post('/desa', [DesaManagementController::class, 'store']);
        Route::get('/desa/{desa}/edit', [DesaManagementController::class, 'edit']);
        Route::put('/desa/{desa}', [DesaManagementController::class, 'update']);
        Route::delete('/desa/{desa}', [DesaManagementController::class, 'destroy']);

        // Kelola Kategori Pengaduan.
        Route::get('/categories', [ComplaintCategoryManagementController::class, 'index']);
        Route::get('/categories/create', [ComplaintCategoryManagementController::class, 'create']);
        Route::post('/categories', [ComplaintCategoryManagementController::class, 'store']);
        Route::get('/categories/{category}/edit', [ComplaintCategoryManagementController::class, 'edit']);
        Route::put('/categories/{category}', [ComplaintCategoryManagementController::class, 'update']);
        Route::delete('/categories/{category}', [ComplaintCategoryManagementController::class, 'destroy']);
    });

    Route::middleware('role:opd|camat')->group(function () {
        Route::post('/complaints/{complaint}/handle', [ComplaintDashboardController::class, 'handle']);
    });

    Route::middleware('role:kominfo|bupati|wakil_bupati|sekda')->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index']);
        Route::get('/laporan/activities', [LaporanController::class, 'activitiesIndex']);
        Route::get('/kinerja', [StatisticsController::class, 'performance']);
    });
});

// Helper routes for hosting environments (CWP) without SSH access
Route::get('/sys/clear', function () {
    if (request('token') !== 'uwVW5Kx3Xfmv') {
        abort(403);
    }
    Illuminate\Support\Facades\Artisan::call('config:clear');
    Illuminate\Support\Facades\Artisan::call('cache:clear');
    Illuminate\Support\Facades\Artisan::call('view:clear');
    Illuminate\Support\Facades\Artisan::call('route:clear');
    return '<pre>Cache cleared successfully!' . "\n\n" . Illuminate\Support\Facades\Artisan::output() . '</pre>';
});

Route::get('/sys/link', function () {
    if (request('token') !== 'uwVW5Kx3Xfmv') {
        abort(403);
    }
    
    // Determine public_html directory path
    $publicPath = realpath(base_path('../public_html'));
    if (!$publicPath || !file_exists($publicPath)) {
        $publicPath = public_path();
    }
    
    app()->usePublicPath($publicPath);

    // Delete or rename existing link/directory if exists
    $link = $publicPath . '/storage';
    if (file_exists($link) || is_link($link)) {
        if (is_link($link)) {
            @unlink($link);
        } elseif (is_dir($link)) {
            @rename($link, $link . '_old_' . time());
        } else {
            @unlink($link);
        }
    }

    try {
        Illuminate\Support\Facades\Artisan::call('storage:link');
        return '<pre>Symlink created at: ' . $link . "\n\n" . Illuminate\Support\Facades\Artisan::output() . '</pre>';
    } catch (\Throwable $e) {
        return '<pre>Failed to create symlink: ' . $e->getMessage() . '</pre>';
    }
});

Route::get('/sys/migrate', function () {
    if (request('token') !== 'uwVW5Kx3Xfmv') {
        abort(403);
    }
    try {
        Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return '<pre>Migration successful!' . "\n\n" . Illuminate\Support\Facades\Artisan::output() . '</pre>';
    } catch (\Throwable $e) {
        return '<pre>Migration failed: ' . $e->getMessage() . '</pre>';
    }
});

Route::get('/sys/seed', function () {
    if (request('token') !== 'uwVW5Kx3Xfmv') {
        abort(403);
    }
    try {
        Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        return '<pre>Seeding successful!' . "\n\n" . Illuminate\Support\Facades\Artisan::output() . '</pre>';
    } catch (\Throwable $e) {
        return '<pre>Seeding failed: ' . $e->getMessage() . '</pre>';
    }
});
