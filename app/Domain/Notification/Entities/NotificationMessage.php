<?php

declare(strict_types=1);

namespace App\Domain\Notification\Entities;

use DateTimeImmutable;

/**
 * Plain PHP representation of an in-app notification, mirroring the
 * custom `notifications` table (id, user_id, title, message, type,
 * is_read, data, timestamps).
 */
final class NotificationMessage
{
    /**
     * @param array<string, mixed>|null $data
     */
    public function __construct(
        public readonly ?int $id,
        public readonly int $userId,
        public readonly string $title,
        public readonly string $message,
        public readonly string $type,
        public readonly bool $isRead = false,
        public readonly ?array $data = null,
        public readonly ?DateTimeImmutable $createdAt = null,
        public readonly ?DateTimeImmutable $updatedAt = null,
    ) {
    }

    public function markRead(): self
    {
        return new self(
            id: $this->id,
            userId: $this->userId,
            title: $this->title,
            message: $this->message,
            type: $this->type,
            isRead: true,
            data: $this->data,
            createdAt: $this->createdAt,
            updatedAt: $this->updatedAt,
        );
    }
}
