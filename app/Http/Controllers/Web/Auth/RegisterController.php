<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register', ['title' => 'Daftar']);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'name' => $data['name'],
            'nik' => $data['nik'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
            'consent_at' => now(),
        ]);

        $user->assignRole('masyarakat');

        Auth::login($user);

        return redirect('/dashboard')->with('status', 'Pendaftaran berhasil! Selamat datang di SIPPM Madina.');
    }
}
