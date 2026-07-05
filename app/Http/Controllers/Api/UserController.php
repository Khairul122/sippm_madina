<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ApiResponds;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Kominfo-only (see routes/api.php: `role:kominfo` middleware group).
 */
class UserController extends Controller
{
    use ApiResponds;

    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $request->string('role')));
        }

        $users = $query->orderBy('name')->paginate((int) $request->integer('per_page', 15));

        return $this->success(UserResource::collection($users));
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
            'opd_id' => $data['opd_id'] ?? null,
            'kecamatan_id' => $data['kecamatan_id'] ?? null,
        ]);

        $user->assignRole($data['role']);

        return $this->success(new UserResource($user), 'Pengguna berhasil dibuat.', 201);
    }

    public function update(UpdateUserRequest $request, int $user): JsonResponse
    {
        $model = User::query()->findOrFail($user);
        $model->update($request->validated());

        return $this->success(new UserResource($model), 'Pengguna berhasil diperbarui.');
    }

    public function destroy(int $user): JsonResponse
    {
        $model = User::query()->findOrFail($user);
        $model->update(['is_active' => false]);

        return $this->success(null, 'Pengguna berhasil dinonaktifkan.');
    }
}
