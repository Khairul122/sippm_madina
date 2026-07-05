<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Domain\User\ValueObjects\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Infrastructure\Persistence\Eloquent\Models\Kecamatan;
use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

/**
 * Kominfo-only account management for internal roles (OPD/Camat/Bupati/
 * Wakil Bupati/Sekda). Masyarakat self-registers via Web\Auth\RegisterController
 * — never created here (FR-04).
 */
class UserManagementController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()->with('roles')->orderBy('name')->paginate(15);

        return view('dashboard.users.index', [
            'title' => 'Kelola Pengguna',
            'users' => $users,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('dashboard.users.create', [
            'title' => 'Tambah Pengguna',
            'roles' => array_filter(Role::cases(), fn (Role $r) => $r !== Role::MASYARAKAT),
            'opds' => Opd::query()->orderBy('name')->get(),
            'kecamatans' => Kecamatan::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

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

        return redirect('/dashboard/users')->with('status', 'Pengguna berhasil dibuat.');
    }

    public function edit(int $user): View
    {
        $model = User::query()->with('roles')->findOrFail($user);
        $this->authorize('update', $model);

        return view('dashboard.users.edit', [
            'title' => 'Ubah Pengguna',
            'targetUser' => $model,
            'opds' => Opd::query()->orderBy('name')->get(),
            'kecamatans' => Kecamatan::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, int $user): RedirectResponse
    {
        $model = User::query()->findOrFail($user);
        $this->authorize('update', $model);

        $model->update($request->validated());

        return redirect('/dashboard/users')->with('status', 'Pengguna berhasil diperbarui.');
    }

    public function toggleActive(int $user): RedirectResponse
    {
        $model = User::query()->findOrFail($user);
        $this->authorize('deactivate', $model);

        $model->update(['is_active' => ! $model->is_active]);

        return back()->with('status', $model->is_active ? 'Pengguna diaktifkan kembali.' : 'Pengguna dinonaktifkan.');
    }
}
