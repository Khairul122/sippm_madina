<?php

declare(strict_types=1);

namespace App\Domain\User\Entities;

use DateTimeImmutable;

/**
 * Plain PHP representation of a user account, mirroring the `users` table
 * plus SIPPM-specific columns (nik, phone, is_active, consent_at, opd_id,
 * kecamatan_id). Role assignment itself lives in Spatie Permission's
 * model_has_roles pivot (Infrastructure concern), not here — the `roles`
 * property is a read projection populated by the repository for
 * convenience.
 */
final class UserAccount
{
    /**
     * @param string[] $roles role slugs assigned to this user
     */
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $nik,
        public readonly ?string $phone,
        public readonly bool $isActive,
        public readonly ?DateTimeImmutable $consentAt,
        public readonly ?int $opdId,
        public readonly ?int $kecamatanId,
        public readonly array $roles = [],
        public readonly ?DateTimeImmutable $createdAt = null,
        public readonly ?DateTimeImmutable $updatedAt = null,
    ) {
    }

    public function hasRole(string $roleSlug): bool
    {
        return in_array($roleSlug, $this->roles, true);
    }
}
