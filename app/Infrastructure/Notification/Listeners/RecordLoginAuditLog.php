<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\Listeners;

use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

/**
 * FR-08: "Sistem mencatat setiap aktivitas login ke dalam audit log."
 * Subscribed to Laravel's built-in Auth events (fired automatically by
 * Auth::attempt()/Auth::login()/Auth::logout(), already used in
 * Web\Auth\LoginController and Api\AuthController — no controller
 * changes needed).
 */
class RecordLoginAuditLog
{
    public function handleLogin(Login $event): void
    {
        AuditLog::query()->create([
            'user_id' => $event->user->id,
            'action' => 'login',
            'model_type' => 'user',
            'model_id' => $event->user->id,
            'old_data' => null,
            'new_data' => ['guard' => $event->guard],
            'ip_address' => request()?->ip(),
        ]);
    }

    public function handleLogout(Logout $event): void
    {
        AuditLog::query()->create([
            'user_id' => $event->user?->id,
            'action' => 'logout',
            'model_type' => 'user',
            'model_id' => $event->user?->id,
            'old_data' => null,
            'new_data' => ['guard' => $event->guard],
            'ip_address' => request()?->ip(),
        ]);
    }

    public function handleFailed(Failed $event): void
    {
        AuditLog::query()->create([
            'user_id' => $event->user?->id,
            'action' => 'login_failed',
            'model_type' => 'user',
            'model_id' => $event->user?->id,
            'old_data' => null,
            'new_data' => ['email' => $event->credentials['email'] ?? null],
            'ip_address' => request()?->ip(),
        ]);
    }
}
