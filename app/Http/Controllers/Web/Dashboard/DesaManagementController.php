<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Desa\StoreDesaRequest;
use App\Http\Requests\Desa\UpdateDesaRequest;
use App\Infrastructure\Persistence\Eloquent\Models\Desa;
use App\Infrastructure\Persistence\Eloquent\Models\Kecamatan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Kominfo-only: manage the Desa (village) reference list. Every Desa
 * belongs to exactly one Kecamatan — the index can be filtered by
 * kecamatan_id, and create/edit always require picking one.
 */
class DesaManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Desa::query()->with('kecamatan');

        if ($request->filled('kecamatan_id')) {
            $query->where('kecamatan_id', $request->integer('kecamatan_id'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"));
        }

        $desas = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('dashboard.desa.index', [
            'title' => 'Data Desa',
            'desas' => $desas,
            'kecamatans' => Kecamatan::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.desa.create', [
            'title' => 'Tambah Desa',
            'kecamatans' => Kecamatan::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreDesaRequest $request): RedirectResponse
    {
        Desa::query()->create($request->validated());

        return redirect('/dashboard/desa')->with('status', 'Data desa berhasil ditambahkan.');
    }

    public function edit(int $desa): View
    {
        $model = Desa::query()->findOrFail($desa);

        return view('dashboard.desa.edit', [
            'title' => 'Ubah Desa',
            'desa' => $model,
            'kecamatans' => Kecamatan::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateDesaRequest $request, int $desa): RedirectResponse
    {
        $model = Desa::query()->findOrFail($desa);
        $model->update($request->validated());

        return redirect('/dashboard/desa')->with('status', 'Data desa berhasil diperbarui.');
    }

    public function destroy(int $desa): RedirectResponse
    {
        Desa::query()->findOrFail($desa)->delete();

        return redirect('/dashboard/desa')->with('status', 'Data desa berhasil dihapus.');
    }
}
