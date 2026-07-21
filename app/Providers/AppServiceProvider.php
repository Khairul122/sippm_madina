<?php

namespace App\Providers;

use App\Http\Policies\ActivityPolicy;
use App\Http\Policies\AuditLogPolicy;
use App\Http\Policies\ComplaintPolicy;
use App\Http\Policies\UserPolicy;
use App\Infrastructure\Broadcasting\Events\ActivityPublished;
use App\Infrastructure\Broadcasting\Events\ComplaintDisposed;
use App\Infrastructure\Broadcasting\Events\ComplaintHandled;
use App\Infrastructure\Broadcasting\Events\ComplaintResolved;
use App\Infrastructure\Broadcasting\Events\ComplaintSubmitted;
use App\Infrastructure\Broadcasting\Events\ComplaintVerified;
use App\Infrastructure\Notification\Listeners\PersistActivityNotification;
use App\Infrastructure\Notification\Listeners\PersistComplaintNotification;
use App\Infrastructure\Notification\Listeners\RecordAuditLog;
use App\Infrastructure\Notification\Listeners\RecordLoginAuditLog;
use App\Infrastructure\Persistence\Eloquent\Models\Activity;
use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use App\Infrastructure\Persistence\Eloquent\Models\Kecamatan;
use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (str_starts_with(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Tailwind was removed from this project (Bootstrap 5 only), but
        // Laravel's default pagination views are Tailwind-based unless
        // told otherwise — every `->links()` call needs Bootstrap markup.
        Paginator::useBootstrapFive();

        // NFR-10: seluruh tanggal/waktu tampil dalam Bahasa Indonesia
        // (nama bulan, dst) via Carbon::translatedFormat(). Waktu sendiri
        // sudah 24-jam secara default (format 'H', bukan 'h'/'g' 12-jam).
        Carbon::setLocale('id');

        // Keep the `activities.actor_type` / `complaints.target_type` /
        // `dispositions.disposed_to_type` polymorphic columns as short,
        // stable slugs instead of fully-qualified class names. Not
        // enforced (rather than enforceMorphMap) because Spatie
        // Permission's model_has_roles/model_has_permissions pivots also
        // morph against User and we don't want to force every morph
        // relation in vendor code onto this map.
        Relation::morphMap([
            'opd' => Opd::class,
            'kecamatan' => Kecamatan::class,
            'user' => User::class,
        ]);

        Gate::policy(Complaint::class, ComplaintPolicy::class);
        Gate::policy(Activity::class, ActivityPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(AuditLog::class, AuditLogPolicy::class);

        // Fase 4/5: one centralized audit-log listener + one persisted
        // in-app notification listener per domain, subscribed to every
        // Complaint/Activity event — instead of duplicating
        // AuditLog::create()/Notification::create() calls inside each
        // UseCase. Laravel 13 has no app/Console/Kernel.php or
        // app/Providers/EventServiceProvider.php by default, so listeners
        // are registered here via Event::listen().
        foreach ([
            ComplaintSubmitted::class,
            ComplaintVerified::class,
            ComplaintDisposed::class,
            ComplaintHandled::class,
            ComplaintResolved::class,
            ActivityPublished::class,
        ] as $event) {
            Event::listen($event, RecordAuditLog::class);
        }

        foreach ([
            ComplaintSubmitted::class,
            ComplaintVerified::class,
            ComplaintDisposed::class,
            ComplaintHandled::class,
            ComplaintResolved::class,
        ] as $event) {
            Event::listen($event, PersistComplaintNotification::class);
        }

        Event::listen(ActivityPublished::class, PersistActivityNotification::class);

        // FR-08: catat setiap aktivitas login (berhasil/gagal) dan logout
        // ke audit log — pakai Auth event bawaan Laravel yang sudah
        // otomatis terpicu dari Auth::attempt()/login()/logout() di
        // Web\Auth\LoginController dan Api\AuthController, tanpa perlu
        // ubah controller itu sendiri. Sengaja SYNC (bukan ShouldQueue)
        // sama seperti keputusan notifikasi — supaya baris audit log
        // langsung ada begitu login/logout terjadi.
        Event::listen(Login::class, [RecordLoginAuditLog::class, 'handleLogin']);
        Event::listen(Logout::class, [RecordLoginAuditLog::class, 'handleLogout']);
        Event::listen(Failed::class, [RecordLoginAuditLog::class, 'handleFailed']);

        // NFR-07: batasi percobaan login per kombinasi IP+email supaya
        // brute force pada satu akun ataupun dari satu sumber sama-sama
        // dibatasi. Diterapkan via `throttle:login` di routes/web.php
        // (session login) dan routes/api.php (Sanctum token login).
        RateLimiter::for('login', function (Request $request) {
            $key = strtolower((string) $request->input('email')).'|'.$request->ip();
            $message = 'Terlalu banyak percobaan masuk. Coba lagi dalam beberapa menit.';

            return Limit::perMinute(5)->by($key)->response(function () use ($request, $message) {
                if ($request->is('api/*') || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 429);
                }

                return back()->withErrors(['email' => $message])->onlyInput('email');
            });
        });
    }
}
