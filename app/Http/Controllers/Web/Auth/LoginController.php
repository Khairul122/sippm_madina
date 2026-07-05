<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Session-based web login for the internal dashboard (distinct from the
 * Sanctum token login in Api\AuthController used by the citizen-facing
 * public app / future mobile client).
 */
class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login', ['title' => 'Masuk']);
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau kata sandi salah.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = $request->user();
        $default = $user->hasRole('masyarakat') ? '/pengaduan' : '/dashboard';

        return redirect()->intended($default)->with('status', 'Selamat datang, '.$user->name.'!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Anda berhasil keluar.');
    }
}
