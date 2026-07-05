<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\User\Entities\UserAccount;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\User as UserModel;
use DateTimeImmutable;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?UserAccount
    {
        $model = UserModel::query()->find($id);

        return $model ? $this->toEntity($model) : null;
    }

    public function findByEmail(string $email): ?UserAccount
    {
        $model = UserModel::query()->where('email', $email)->first();

        return $model ? $this->toEntity($model) : null;
    }

    public function findByNik(string $nik): ?UserAccount
    {
        $model = UserModel::query()->where('nik', $nik)->first();

        return $model ? $this->toEntity($model) : null;
    }

    public function save(UserAccount $user): UserAccount
    {
        $model = $user->id
            ? UserModel::query()->findOrFail($user->id)
            : new UserModel();

        $model->fill([
            'name' => $user->name,
            'email' => $user->email,
            'nik' => $user->nik,
            'phone' => $user->phone,
            'is_active' => $user->isActive,
            'consent_at' => $user->consentAt,
            'opd_id' => $user->opdId,
            'kecamatan_id' => $user->kecamatanId,
        ]);

        $model->save();

        if (! empty($user->roles)) {
            $model->syncRoles($user->roles);
        }

        return $this->toEntity($model->fresh());
    }

    public function setActive(int $userId, bool $isActive): void
    {
        UserModel::query()->findOrFail($userId)->update(['is_active' => $isActive]);
    }

    public function paginate(array $filters, int $page = 1, int $perPage = 15): array
    {
        $query = UserModel::query();

        if (! empty($filters['role'])) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $filters['role']));
        }

        if (array_key_exists('is_active', $filters)) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (! empty($filters['opd_id'])) {
            $query->where('opd_id', $filters['opd_id']);
        }

        if (! empty($filters['kecamatan_id'])) {
            $query->where('kecamatan_id', $filters['kecamatan_id']);
        }

        $paginator = $query->orderBy('name')->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(fn (UserModel $m) => $this->toEntity($m), $paginator->items()),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
        ];
    }

    private function toEntity(UserModel $model): UserAccount
    {
        return new UserAccount(
            id: $model->id,
            name: $model->name,
            email: $model->email,
            nik: $model->nik,
            phone: $model->phone,
            isActive: (bool) $model->is_active,
            consentAt: $model->consent_at ? DateTimeImmutable::createFromInterface($model->consent_at) : null,
            opdId: $model->opd_id,
            kecamatanId: $model->kecamatan_id,
            roles: $model->getRoleNames()->all(),
            createdAt: $model->created_at ? DateTimeImmutable::createFromInterface($model->created_at) : null,
            updatedAt: $model->updated_at ? DateTimeImmutable::createFromInterface($model->updated_at) : null,
        );
    }
}
