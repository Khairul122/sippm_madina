<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kecamatan\StoreKecamatanRequest;
use App\Http\Requests\Kecamatan\UpdateKecamatanRequest;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use App\Infrastructure\Persistence\Eloquent\Models\Disposition;
use App\Infrastructure\Persistence\Eloquent\Models\Kecamatan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Kominfo-only: manage the Kecamatan reference list used to scope
 * Camat-role user accounts, Desa (village) records, and as a
 * disposition/complaint target (BR-01/BR-02: only via Camat, never
 * directly to Bupati/Wakil Bupati/Sekda).
 */
class KecamatanManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Kecamatan::query()->withCount(['users', 'activities', 'desas']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"));
        }

        $kecamatans = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('dashboard.kecamatan.index', [
            'title' => 'Data Kecamatan',
            'kecamatans' => $kecamatans,
        ]);
    }

    public function create(): View
    {
        return view('dashboard.kecamatan.create', ['title' => 'Tambah Kecamatan']);
    }

    public function store(StoreKecamatanRequest $request): RedirectResponse
    {
        Kecamatan::query()->create($request->validated());

        return redirect('/dashboard/kecamatan')->with('status', 'Data kecamatan berhasil ditambahkan.');
    }

    public function edit(int $kecamatan): View
    {
        $model = Kecamatan::query()->findOrFail($kecamatan);

        return view('dashboard.kecamatan.edit', ['title' => 'Ubah Kecamatan', 'kecamatan' => $model]);
    }

    public function update(UpdateKecamatanRequest $request, int $kecamatan): RedirectResponse
    {
        $model = Kecamatan::query()->findOrFail($kecamatan);
        $model->update($request->validated());

        return redirect('/dashboard/kecamatan')->with('status', 'Data kecamatan berhasil diperbarui.');
    }

    public function destroy(int $kecamatan): RedirectResponse
    {
        $model = Kecamatan::query()->withCount(['users', 'desas'])->findOrFail($kecamatan);

        if ($model->users_count > 0) {
            return back()->withErrors('Kecamatan ini tidak dapat dihapus karena masih memiliki akun pengguna terkait.');
        }

        if ($model->desas_count > 0) {
            return back()->withErrors('Kecamatan ini tidak dapat dihapus karena masih memiliki data desa terkait. Hapus data desanya terlebih dahulu.');
        }

        $inUse = Complaint::query()->where('target_type', 'camat')->where('target_id', $model->id)->exists()
            || Disposition::query()->where('disposed_to_type', 'camat')->where('disposed_to_id', $model->id)->exists()
            || $model->activities()->exists();

        if ($inUse) {
            return back()->withErrors('Kecamatan ini tidak dapat dihapus karena masih memiliki riwayat pengaduan/kegiatan terkait.');
        }

        $model->delete();

        return redirect('/dashboard/kecamatan')->with('status', 'Data kecamatan berhasil dihapus.');
    }
}
