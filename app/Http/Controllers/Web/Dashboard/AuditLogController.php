<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
use Illuminate\Contracts\View\View;

class AuditLogController extends Controller
{
    public function index(): View
    {
        $logs = AuditLog::query()->with('user')->orderByDesc('created_at')->paginate(20);

        return view('dashboard.audit-log.index', [
            'title' => 'Audit Log',
            'logs' => $logs,
        ]);
    }
}
