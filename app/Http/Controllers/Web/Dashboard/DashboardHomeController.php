<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Landing target for bare "/dashboard" (e.g. after login, or a bookmarked
 * URL) — redirects to the first page each role actually has access to,
 * per the RBAC matrix in routes/web.php. Prevents the 404 that happened
 * when LoginController redirected here but no literal "/dashboard" route
 * existed.
 */
class DashboardHomeController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        $user = $request->user();

        $redirect = match (true) {
            $user->hasRole('masyarakat') => redirect('/pengaduan'),
            $user->hasAnyRole(['kominfo', 'opd', 'camat']) => redirect('/dashboard/complaints'),
            $user->hasAnyRole(['bupati', 'wakil_bupati', 'sekda']) => redirect('/dashboard/statistik'),
            default => redirect('/'),
        };

        // This route is itself an extra redirect hop (e.g. straight after
        // login) — a flashed session('status') from the previous request
        // would otherwise be consumed/aged out here before ever reaching
        // a rendered page. Re-flash it forward one more hop.
        if ($request->session()->has('status')) {
            $redirect->with('status', $request->session()->get('status'));
        }

        return $redirect;
    }
}
