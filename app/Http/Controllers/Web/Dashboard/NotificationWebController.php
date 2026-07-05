<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON endpoints backing the notification bell in layouts/dashboard.blade.php.
 * Session-authenticated (web guard) — deliberately not under /api/v1, which
 * is Sanctum token-based and not configured for stateful session requests.
 */
class NotificationWebController extends Controller
{
    public function __construct(
        private readonly NotificationRepositoryInterface $notifications,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $result = $this->notifications->paginateForUser($request->user()->id, 1, 20);

        return response()->json([
            'data' => array_map(fn ($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'is_read' => $n->isRead,
                'type' => $n->type,
                'created_at' => $n->createdAt?->format(\DateTimeInterface::ATOM),
            ], $result['data']),
        ]);
    }

    public function markRead(int $notification): JsonResponse
    {
        $this->notifications->markAsRead($notification);

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $result = $this->notifications->paginateForUser($request->user()->id, 1, 1000);

        foreach ($result['data'] as $notification) {
            if (! $notification->isRead) {
                $this->notifications->markAsRead($notification->id);
            }
        }

        return response()->json(['success' => true]);
    }
}
