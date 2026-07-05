<?php

declare(strict_types=1);

namespace App\Domain\User\Repositories;

use App\Domain\User\Entities\UserAccount;

interface UserRepositoryInterface
{
    public function findById(int $id): ?UserAccount;

    public function findByEmail(string $email): ?UserAccount;

    public function findByNik(string $nik): ?UserAccount;

    public function save(UserAccount $user): UserAccount;

    public function setActive(int $userId, bool $isActive): void;

    /**
     * @param array{role?: string, is_active?: bool, opd_id?: int, kecamatan_id?: int} $filters
     * @return array{data: UserAccount[], total: int, per_page: int, current_page: int}
     */
    public function paginate(array $filters, int $page = 1, int $perPage = 15): array;
}
