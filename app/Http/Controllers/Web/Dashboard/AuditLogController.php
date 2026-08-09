<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
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

        return view('dashboard.audit-log.index', [
            'title' => 'Audit Log Sistem',
            'logs' => $logs,
            'search' => $search,
            'modelType' => $modelType,
        ]);
    }
}
