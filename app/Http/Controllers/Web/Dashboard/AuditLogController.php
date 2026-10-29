<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Application\UseCases\ManageBlockedIpUseCase;
use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
use App\Infrastructure\Persistence\Eloquent\Models\BlockedIp;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AuditLogController extends Controller
{
    public function __construct(private readonly ManageBlockedIpUseCase $manageBlockedIp)
    {
    }

    public function index(Request $request): View
    {
        $query = AuditLog::query()->with('user');

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('model_type', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($modelType = $request->query('model_type')) {
            $query->where('model_type', $modelType);
        }

        $logs = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $ips = collect($logs->items())->pluck('ip_address')->filter()->map(
            fn (string $ip) => filter_var($ip, FILTER_VALIDATE_IP) ? inet_ntop(inet_pton($ip)) : null
        )->filter()->all();
        $blockingReady = Schema::hasTable('blocked_ips');

        return view('dashboard.audit-log.index', [
            'title' => 'Audit Log Sistem',
            'logs' => $logs,
            'search' => $search,
            'modelType' => $modelType,
            'blockedIps' => $blockingReady
                ? BlockedIp::query()->whereIn('ip_address', $ips)->pluck('id', 'ip_address')
                : collect(),
            'blockingReady' => $blockingReady,
        ]);
    }

    public function blockIp(Request $request, AuditLog $auditLog): RedirectResponse
    {
        $this->authorize('manageIpBlocks', AuditLog::class);

        if (! Schema::hasTable('blocked_ips')) {
            return back()->withErrors('Jalankan migrasi database sebelum memblokir IP.');
        }

        try {
            $created = $this->manageBlockedIp->block($auditLog, $request->user()->id, $request->ip());
        } catch (DomainException $e) {
            return back()->withErrors($e->getMessage());
        }

        return back()->with('status', $created ? 'Alamat IP berhasil diblokir.' : 'Alamat IP sudah diblokir.');
    }

    public function unblockIp(Request $request, BlockedIp $blockedIp): RedirectResponse
    {
        $this->authorize('manageIpBlocks', AuditLog::class);

        $this->manageBlockedIp->unblock($blockedIp, $request->user()->id);

        return back()->with('status', 'Blokir alamat IP berhasil dicabut.');
    }
}
