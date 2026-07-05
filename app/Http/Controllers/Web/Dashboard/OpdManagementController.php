<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Opd\StoreOpdRequest;
use App\Http\Requests\Opd\UpdateOpdRequest;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use App\Infrastructure\Persistence\Eloquent\Models\Disposition;
use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Kominfo-only: manage the OPD (Organisasi Perangkat Daerah) reference
 * list used to scope OPD-role user accounts and as a disposition/
 * complaint target. Route-level RBAC (`role:kominfo` in routes/web.php)
 * is the only access guard needed — this is global reference data with
 * no per-user object-level nuance, unlike UserPolicy's self-protection
 * rules.
 */
class OpdManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Opd::query()->withCount(['users', 'activities']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"));
        }

        $opds = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('dashboard.opd.index', [
            'title' => 'Data OPD',
            'opds' => $opds,
        ]);
    }

    public function create(): View
    {
        return view('dashboard.opd.create', ['title' => 'Tambah OPD']);
    }

    public function store(StoreOpdRequest $request): RedirectResponse
    {
        Opd::query()->create($request->validated());

        return redirect('/dashboard/opd')->with('status', 'Data OPD berhasil ditambahkan.');
    }

    public function edit(int $opd): View
    {
        $model = Opd::query()->findOrFail($opd);

        return view('dashboard.opd.edit', ['title' => 'Ubah OPD', 'opd' => $model]);
    }

    public function update(UpdateOpdRequest $request, int $opd): RedirectResponse
    {
        $model = Opd::query()->findOrFail($opd);
        $model->update($request->validated());

        return redirect('/dashboard/opd')->with('status', 'Data OPD berhasil diperbarui.');
    }

    public function destroy(int $opd): RedirectResponse
    {
        $model = Opd::query()->withCount('users')->findOrFail($opd);

        if ($model->users_count > 0) {
            return back()->withErrors('OPD ini tidak dapat dihapus karena masih memiliki akun pengguna terkait.');
        }

        $inUse = Complaint::query()->where('target_type', 'opd')->where('target_id', $model->id)->exists()
            || Disposition::query()->where('disposed_to_type', 'opd')->where('disposed_to_id', $model->id)->exists()
            || $model->activities()->exists();

        if ($inUse) {
            return back()->withErrors('OPD ini tidak dapat dihapus karena masih memiliki riwayat pengaduan/kegiatan terkait.');
        }

        $model->delete();

        return redirect('/dashboard/opd')->with('status', 'Data OPD berhasil dihapus.');
    }
}
