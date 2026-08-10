<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\SiteSetting\UpdateSiteSettingRequest;
use App\Infrastructure\Persistence\Eloquent\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

/**
 * Pengaturan tampilan beranda publik — saat ini hanya foto hero (mis.
 * foto Bupati aktif) + keterangan singkatnya. Kominfo-only (dijaga
 * middleware role:kominfo di routes/web.php). Satu baris aktif, pola
 * sama persis dengan ManualBookController — selalu diakses/diupdate
 * lewat updateOrCreate(['id' => 1], ...).
 */
class SiteSettingController extends Controller
{
    public function edit(): View
    {
        return view('dashboard.settings.edit', [
            'title' => 'Pengaturan Beranda',
            'siteSetting' => SiteSetting::query()->find(1),
        ]);
    }

    public function update(UpdateSiteSettingRequest $request): RedirectResponse
    {
        $existing = SiteSetting::query()->find(1);
        $data = [
            'hero_caption' => $request->validated('hero_caption'),
            'updated_by' => $request->user()->id,
        ];

        if ($request->hasFile('hero_image')) {
            $path = $request->file('hero_image')->store('site', 'public');

            if ($existing?->hero_image_path) {
                Storage::disk('public')->delete($existing->hero_image_path);
            }

            $data['hero_image_path'] = $path;
        }

        SiteSetting::singleton($data);

        return back()->with('status', 'Pengaturan beranda berhasil diperbarui.');
    }

    public function destroyHeroImage(): RedirectResponse
    {
        $existing = SiteSetting::query()->find(1);

        if ($existing?->hero_image_path) {
            Storage::disk('public')->delete($existing->hero_image_path);
            $existing->update(['hero_image_path' => null]);
        }

        return back()->with('status', 'Foto hero dikembalikan ke ilustrasi bawaan.');
    }
}
