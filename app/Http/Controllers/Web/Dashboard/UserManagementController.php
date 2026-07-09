<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Domain\User\ValueObjects\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
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

        $this->recordUserAudit('user_created', $user->id, null, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $data['role'],
            'opd_id' => $user->opd_id,
            'kecamatan_id' => $user->kecamatan_id,
        ]);

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

        $auditFields = ['name', 'email', 'is_active', 'opd_id', 'kecamatan_id'];
        $oldData = $model->only($auditFields);

        $model->update($request->validated());

        $this->recordUserAudit('user_updated', $model->id, $oldData, $model->only($auditFields));

        return redirect('/dashboard/users')->with('status', 'Pengguna berhasil diperbarui.');
    }

    public function toggleActive(int $user): RedirectResponse
    {
        $model = User::query()->findOrFail($user);
        $this->authorize('deactivate', $model);

        $wasActive = $model->is_active;
        $model->update(['is_active' => ! $wasActive]);

        $this->recordUserAudit(
            $model->is_active ? 'user_activated' : 'user_deactivated',
            $model->id,
            ['is_active' => $wasActive],
            ['is_active' => $model->is_active],
        );

        return back()->with('status', $model->is_active ? 'Pengguna diaktifkan kembali.' : 'Pengguna dinonaktifkan.');
    }

    /**
     * FR-36: "kelola pengguna" wajib tercatat di audit log. Ditulis
     * langsung di sini (bukan lewat listener event terpusat seperti
     * modul Complaint/Activity) karena controller ini sudah sengaja
     * memakai Eloquent langsung tanpa UseCase — sama seperti
     * RecordLoginAuditLog menulis langsung untuk event auth bawaan
     * Laravel yang juga di luar alur UseCase.
     */
    private function recordUserAudit(string $action, int $modelId, ?array $oldData, array $newData): void
    {
        AuditLog::query()->create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => 'user',
            'model_id' => $modelId,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => request()?->ip(),
        ]);
    }
}
