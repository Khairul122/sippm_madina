<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\Listeners;

use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

/**
 * FR-08: "Sistem mencatat setiap aktivitas login ke dalam audit log."
 */
class RecordLoginAuditLog
{
    public function handleLogin(Login $event): void
    {
        AuditLog::record(
            action: 'Login Berhasil',
            modelType: 'user',
            modelId: $event->user->id,
            newData: ['guard' => $event->guard, 'name' => $event->user->name, 'email' => $event->user->email],
            userId: $event->user->id
        );
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            AuditLog::record(
                action: 'Logout',
                modelType: 'user',
                modelId: $event->user->id,
                newData: ['guard' => $event->guard],
                userId: $event->user->id
            );
        }
    }

    public function handleFailed(Failed $event): void
    {
        AuditLog::record(
            action: 'Gagal Login',
            modelType: 'user',
            modelId: $event->user?->id,
            newData: ['attempted_email' => $event->credentials['email'] ?? null],
            userId: $event->user?->id
        );
    }
}
