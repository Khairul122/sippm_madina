<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Infrastructure\Persistence\Eloquent\Models\BlockedIp;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class RejectBlockedIp
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (BlockedIp::query()->where('ip_address', $request->ip())->exists()) {
                abort(403, 'Akses dari alamat IP ini diblokir.');
            }
        } catch (QueryException $e) {
            // FTP can publish code before migration; other database failures must still surface.
            if (Schema::hasTable('blocked_ips')) {
                throw $e;
            }
        }

        return $next($request);
    }
}
