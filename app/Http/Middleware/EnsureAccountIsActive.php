<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks deactivated (is_active = false) accounts from proceeding past
 * authentication. Registered as the `active` middleware alias in
 * bootstrap/app.php.
 */
class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->is_active) {
            auth()->guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                abort(403, 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.');
            }

            throw new AuthenticationException('Akun Anda telah dinonaktifkan.');
        }

        return $next($request);
    }
}
