<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Http\Controllers\Concerns\ApiResponds;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponds;

    public function __construct(
        private readonly NotificationRepositoryInterface $notifications,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->integer('page', 1);
        $perPage = (int) $request->integer('per_page', 15);

        $result = $this->notifications->paginateForUser($request->user()->id, $page, $perPage);

        return $this->success([
            'data' => array_map(fn ($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'type' => $n->type,
                'is_read' => $n->isRead,
                'data' => $n->data,
                'created_at' => $n->createdAt?->format(DATE_ATOM),
            ], $result['data']),
            'total' => $result['total'],
            'per_page' => $result['per_page'],
            'current_page' => $result['current_page'],
            'unread_count' => $this->notifications->countUnreadForUser($request->user()->id),
        ]);
    }

    public function markRead(int $notification): JsonResponse
    {
        $this->notifications->markAsRead($notification);

        return $this->success(null, 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $result = $this->notifications->paginateForUser($request->user()->id, 1, 1000);

        foreach ($result['data'] as $notification) {
            if (! $notification->isRead) {
                $this->notifications->markAsRead($notification->id);
            }
        }

        return $this->success(null, 'Semua notifikasi ditandai sudah dibaca.');
    }
}
