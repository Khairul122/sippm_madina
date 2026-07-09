<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateAvatarRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileInfoRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Self-service profile untuk SEMUA role yang login (termasuk masyarakat).
 * Sengaja di prefix netral /profil, bukan /dashboard — masyarakat tidak
 * boleh masuk /dashboard/* (AGENTS.md Don'ts). Setiap action operasi
 * murni ke $request->user() sendiri (tidak ada parameter {user} di URL
 * manapun) jadi tidak ada celah IDOR dan tidak perlu Policy.
 */
class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('profile.show', [
            'title' => 'Profil Saya',
            'user' => $request->user()->load('opd', 'kecamatan'),
        ]);
    }

    public function updateInfo(UpdateProfileInfoRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return back()->with('status', 'Profil berhasil diperbarui.');
    }

    public function updateAvatar(UpdateAvatarRequest $request): RedirectResponse
    {
        $user = $request->user();
        $path = $request->file('avatar')->store('avatars', 'public');

        $this->deleteOldAvatar($user->avatar_path);
        $user->update(['avatar_path' => $path]);

        return back()->with('status', 'Foto profil berhasil diperbarui.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update(['password' => $request->validated('password')]);

        return back()->with('status', 'Kata sandi berhasil diubah.');
    }

    private function deleteOldAvatar(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
