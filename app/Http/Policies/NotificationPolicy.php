<?php

namespace App\Http\Policies;

use App\Infrastructure\Persistence\Eloquent\Models\Notification;
use App\Infrastructure\Persistence\Eloquent\Models\User;

/**
 * Object-level authorization untuk notifikasi in-app.
 *
 * Sebelumnya NotificationWebController/Api\NotificationController::markRead()
 * langsung memanggil NotificationRepositoryInterface::markAsRead($id) dari
 * ID mentah di URL tanpa pernah mengecek kepemilikan — celah IDOR: user
 * manapun yang login bisa menandai "read" notifikasi milik user lain
 * dengan menebak/mengurutkan ID. Policy ini menutup celah itu, dipanggil
 * lewat $this->authorize('view', $notification) di kedua controller
 * sebelum markAsRead() dieksekusi.
 */
class NotificationPolicy
{
    public function view(User $user, Notification $notification): bool
    {
        return $user->id === $notification->user_id;
    }
}
